#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys, re, xmltodict

def convert_date(s):
    if not s:
        return s
    d, m, y = s.split('/')
    return '-'.join((y, m, d))

clean_minis = lambda m: m.replace(u"Minsitère", u"Ministère")

def parse_question(url, xmlstring):
    data = xmltodict.parse(xmlstring)

    qe = data['QUESTION']

    if isinstance(qe['MINA']['ORDRE'], dict):
        qe['MINA']['ORDRE'] = [qe['MINA']['ORDRE']]

    if not qe['RENOUVELLEMENT']:
        qe['RENOUVELLEMENT'] = {}
    if not qe['INDEXATION_AN']['ANALYSE']['ANA']:
        qe['INDEXATION_AN']['ANALYSE']['ANA'] = []
    elif isinstance(qe['INDEXATION_AN']['ANALYSE']['ANA'], unicode):
        qe['INDEXATION_AN']['ANALYSE']['ANA'] = [qe['INDEXATION_AN']['ANALYSE']['ANA']]

    extracted_data = {
        'source': url,
        'legislature': qe['LEGISLATURE'],
        'type': qe['@TYPE'],
        'numero': qe['DEPOT'][0]['@NUMERO'],
        'date_question': convert_date(qe['DEPOT'][0]['DATE_JO']),
        'date_reponse': convert_date(qe['REPONSE']['DATE_JO_REPONSE']),
        'date_retrait': "",
        'motif_retrait': "",
        'ministere_attribue': clean_minis(qe['MINA']['ORDRE'][-1]['DEVELOPPE']),
        'ministere_interroge': clean_minis(qe['MINI']['DEVELOPPE']),
        'tete_analyse': qe['INDEXATION_AN']['TETE_ANALYSE'].replace(u"aucune tête d'analyse", ""),
        'analyse': " / ".join([a for a in qe['INDEXATION_AN']['ANALYSE']['ANA'] if a]),
        'rubrique': qe['INDEXATION_AN']['@RUBRIQUE'],
        'question': qe['DEPOT'][1]['TEXTE_DEPOT'] if qe['DEPOT'][1] else '',
        'reponse': qe['REPONSE']['TEXTE_REPONSE'],
        'auteur': qe['AUTEUR']['PRENOM'] + ' ' + qe['AUTEUR']['NOM'],
    # unused fields
        'date_signalement': max(convert_date(qe.get('RENOUVELLEMENT', {}).get('DATE_JO', '')), \
                                convert_date(qe.get('SIGNALEMENT', {}).get('DATE_JO', ''))),
        'date_cht_attr': convert_date(qe['MINA']['ORDRE'][-1]['DATE_JO']) if len(qe['MINA']['ORDRE']) > 1 else "",
        'page_question': qe['DEPOT'][0]['PAGE_JO'],
        'page_reponse': qe['REPONSE']['PAGE_JO_REPONSE']
    }
    if (not extracted_data['date_reponse']):
        extracted_data['date_retrait'] = qe['CLOTURE']['DATE_JO']
    if (extracted_data['date_retrait'] and qe['CLOTURE']['LIBELLE'] != u"Réponse publiée"):
        extracted_data['motif_retrait'] = qe['CLOTURE']['LIBELLE'].lower()

    for k, v in extracted_data.iteritems():
        if not v:
            v = ""
        extracted_data[k] = v.encode('utf-8').replace('\\', '\\\\').replace('"', '\\"').replace('\n', ' ')

    return extracted_data

if __name__ == '__main__':
    filepath = sys.argv[1]
    url = re.sub(r'^.*/([^/]+)$', r'\1', filepath).replace('_', '/').replace('/vue/xml', '')
    with open(filepath, 'r') as f:
        parsed_data = parse_question(url, f.read())
    print "{%s}" % ", ".join('"%s": "%s"' % (k, parsed_data[k]) for k in parsed_data)

