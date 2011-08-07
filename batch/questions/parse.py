#!/usr/bin/python

import urllib2, sys, os
import re
from BeautifulSoup import BeautifulSoup, BeautifulStoneSoup, NavigableString, Tag

# todo: erratum question / reponse
#start_url = 'http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&C1=QE&C2=QG&C3=QOSD&C4=RET&C5=AR&C6=SR&C7=NR'
# SortField=NAT&SortOrder=ASC&ResultCount=25

def convertdate(s):
    d, m, y = s.split('/')
    return '-'.join((y, m, d))

fn = sys.argv[1]
url = 'http://questions.assemblee-nationale.fr/q13/13-%sQE.htm'

fieldorder = (
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

d = dict.fromkeys(fieldorder, '')
d['legislature'] = '13'
d['type'] = 'QE'

_clean_html_re = re.compile("<.*?>")
def clean_html(s):
    return _clean_html_re.sub('', s)

lastbr_re = re.compile('\s*<br\s*/?>$', re.U|re.M)
def extracttext(t):
    div = t.parent.findNextSibling('div', attrs={'class': 'contenutexte'})
    text = div.decodeContents().strip()
    return lastbr_re.sub('', text)

def extractspan(t):
    span = t.findNextSibling('span', attrs={'class': 'contenu'})
    return span.decodeContents()

s = BeautifulSoup(open(fn).read(), convertEntities=BeautifulStoneSoup.ALL_ENTITIES)

spandict = {
    'ministere_interroge': u'Minist\xe8re interrog\xe9',
    'ministere_attribue': u'Minist\xe8re attributaire',
    'rubrique': u'Rubrique',
    'tete_analyse': u"T\xeate d'analyse",
    'analyse': u'Analyse',
}



for k, v in spandict.iteritems():
    d[k] = extractspan(s.find(text=re.compile(u'^%s >' % v)))

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
dates = dates.decodeContents().split('<br />')

# integre le deuxieme emplacement de retrait
retrait = s.find(text=re.compile(u'^Question retir\xe9e le'))
if retrait:
    try:
        span = extractspan(retrait)
    except:
        pass
    else:
        dates.append(u'%s\xa0page\xa0:\xa0%s' % (retrait, span))

xre = re.compile(u'(?P<type>[A-Z].*?)\s*:\s*(?P<date>\d+/\d+/\d+)((\s*page\s*:\s*(?P<page>\d*))?(\s*\(\s*(?P<motif>.*?)\s*\)\s*)?)?\s*', re.U|re.M)
for l in dates:
    if not l:
        continue
    for m in xre.finditer(clean_html(l)):
        for k, v in pubdict[m.group('type')]:
            value = m.group(k)
            if value is None:
                value = ''
            if k == 'date':
                value = convertdate(value)
            d[v] = value

# pour les questions au gvt, avec dates identiques
if 'date_reponse' in d and not 'date_question' in d:
    d['date_question'] = d['date_reponse']

num = s.find(text=re.compile(u'^Question N\xb0 : '))
d['numero'] = num.findNextSibling('b').decodeContents()
auteur = num.parent.findNextSibling('td').findNext('b').decodeContents()

preauteur = (u'M.\xa0', u'Mme\xa0', u'Mlle\xa0')
for p in preauteur:
    if auteur.startswith(p):
        auteur = auteur[len(p):]
        break
d['auteur'] = auteur.replace(u'\xa0', u' ')

q = s.find(text=re.compile(u'^\s*Texte de la question\s*$'))
d['question'] = extracttext(q)

if d.get('date_reponse'):
    r = s.find(text=re.compile(u'^\s*Texte de la r\xe9ponse\s*$'))
    d['reponse'] = extracttext(r)

d['source'] = url % d['numero']
d['motif_retrait'] = d['motif_retrait'].lower()

for k, v in d.iteritems():
    d[k] = v.encode('utf8').replace('\\', '\\\\').replace('"', '\\"')
print "{%s}" % ", ".join('"%s": "%s"' % (k, d[k]) for k in fieldorder)
