# -*- coding: utf-8 -*-

import re
import sys
from bs4 import BeautifulSoup


_clean_html_re = re.compile("<.*?>")
lastbr_re = re.compile('\s*<br\s*/?>$', re.U|re.M)
linebreaks_re = re.compile(r'[\s\r\n]+')
re_clean_html = [
    (re.compile(r'<//[^>]*>'), ''),
    (re.compile(r"(<[^>]*='[^>'\"]*')['\"]([^>]*>)"), r'\1\2'),
    (re.compile(r"(<[^>]*=\"[^>'\"]*\")['\"]([^>]*>)"), r'\1\2'),
    (re.compile(r"(<[^>]*=)=(['\"][^>]*>)"), r'\1\2')
]
field_order = (
    'source',
    'legislature',
    'type',
    'numero',
    'date_question',
    'date_retrait',
    'date_reponse',
    'date_signalement',
    'date_cht_attr',
    'page_question',
    'page_reponse',
    'ministere_attribue',
    'ministere_interroge',
    'tete_analyse',
    'analyse',
    'rubrique',
    'question',
    'reponse',
    'motif_retrait',
    'auteur',
)


def convert_date(s):
    d, m, y = s.split('/')
    return '-'.join((y, m, d))


def clean_html(s):
    return _clean_html_re.sub('', s)


def extract_text(t):
    div = t.parent.findNextSibling('div', attrs={'class': 'contenutexte'})
    text = div.decode_contents().strip()
    return linebreaks_re.sub(' ', lastbr_re.sub('', text))


def extract_span(t):
    try:
        span = t.findNextSibling('span', attrs={'class': 'contenu'})
        return span.text
    except:
        raise


def parse_question(url, html):
    extracted_data = dict.fromkeys(field_order, '')
    leg_reg = re.compile(r'^.*nationale.fr/q(\d+)/.*$')
    extracted_data['legislature'] = leg_reg.sub(r'\1', url)
    extracted_data['type'] = 'QE'

    for reg, rep in re_clean_html:
        html = reg.sub(rep, html)

    s = BeautifulSoup(html)

    span_dict = {
        'ministere_interroge': u'Minist\xe8re interrog\xe9',
        'ministere_attribue': u'Minist\xe8re attributaire',
        'rubrique': u'Rubrique',
        'tete_analyse': u"T\xeate d'analyse",
        'analyse': u'Analyse',
    }

    for k, v in span_dict.iteritems():
        extracted_data[k] = extract_span(s.find(text=re.compile(u'^%s >' % v)))

    # extrait les dates / pages
    pubdict = {
        u'Question publi\xe9e au JO le': (('date', 'date_question'), ('page', 'page_question')),
        u'Question retir\xe9e le': (('date', 'date_retrait'), ('motif', 'motif_retrait')),
        u'R\xe9ponse publi\xe9e au JO le': (('date', 'date_reponse'), ('page', 'page_reponse')),
        u"Date de changement d'attribution": (('date', 'date_cht_attr'),),
        u'Date de signalement': (('date', 'date_signalement'),),
        u'Date de renouvellement': (('date', 'date_signalement'),),
        u'Erratum de la r\xe9ponse publi\xe9 au JO le': (('date', 'date_erratum_reponse'), ('page', 'page_erratum_reponse')),
        u'Erratum de la question publi\xe9 au JO le': (('date', 'date_erratum_question'), ('page', 'page_erratum_question')),
    }

    # split par lignes
    dates = s.find(text=re.compile(u'^Question publi\xe9e au JO le')).parent
    dates = dates.decode_contents().split('<br />')

    # integre le deuxieme emplacement de retrait
    retrait = s.find(text=re.compile(u'^Question retir\xe9e le'))
    if retrait:
        try:
            span = extract_span(retrait)
        except:
            pass
        else:
            dates.append(u'%s\xa0page\xa0:\xa0%s' % (retrait, span))

    xre = re.compile(u'(?P<type>[A-Z].*?)\s*:\s*(?P<date>\d+/\d+/\d+)((\s*page\s*:\s*(?P<page>\d*))?(\s*\(\s*(?P<motif>.*?)\s*\)\s*)?)?\s*', re.U | re.M)

    for l in dates:
        if not l:
            continue
        for m in xre.finditer(clean_html(l)):
            for k, v in pubdict[m.group('type')]:
                value = m.group(k)
                if value is None:
                    value = ''
                if k == 'date':
                    value = convert_date(value)
                extracted_data[v] = value

    # pour les questions au gvt, avec dates identiques
    if 'date_reponse' in extracted_data and not 'date_question' in extracted_data:
        extracted_data['date_question'] = extracted_data['date_reponse']

    num = s.find(text=re.compile(u'^Question N\xb0 : '))
    extracted_data['numero'] = num.findNextSibling('b').decode_contents()
    auteur = num.parent.findNextSibling('td').findNext('b').decode_contents()

    preauteur = (u'M.\xa0', u'Mme\xa0', u'Mlle\xa0')
    for p in preauteur:
        if auteur.startswith(p):
            auteur = auteur[len(p):]
            break
    extracted_data['auteur'] = auteur.replace(u'\xa0', u' ')

    q = s.find(text=re.compile(u'^\s*Texte de la question\s*$'))
    extracted_data['question'] = extract_text(q)

    if extracted_data.get('date_reponse'):
        r = s.find(text=re.compile(u'^\s*Texte de la r\xe9ponse\s*$'))
        extracted_data['reponse'] = extract_text(r)

    extracted_data['source'] = url
    extracted_data['motif_retrait'] = extracted_data['motif_retrait'].lower()

    for k, v in extracted_data.iteritems():
        extracted_data[k] = v.encode('utf8').replace('\\', '\\\\').replace('"', '\\"')

    return extracted_data


if __name__ == '__main__':
    filepath = sys.argv[1]
    url = filepath.replace('_', '/')
    parsed_data = parse_question(url, open(filepath, 'r').read())
    print "{%s}" % ", ".join('"%s": "%s"' % (k, parsed_data[k]) for k in field_order)