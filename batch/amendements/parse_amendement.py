#!/usr/bin/env python
# -*- coding: utf-8 -*-

import re
import sys
import json
import requests
from HTMLParser import HTMLParser

from clean_subjects_amdmts import clean_subject
from parseAmdtsFromANOpenData import extractNumRectif, cleanAuteurs, extractSort, fixHTML

htmlfile = sys.argv[1]
htmlurl = htmlfile.replace("html/", "").replace('_-_', '/')

with open(htmlfile) as f:
    htmlcontent = f.read()

re_opendata_json = re.compile(r'<a [^>]*href="(/dyn/opendata/.*\.json)"', re.I)

jsonurl = re_opendata_json.search(htmlcontent)
if not jsonurl:
    print >> sys.stderr, "ERROR: could not find link to JSON opendata for amendement %s" % htmlurl
    sys.exit(1)
jsonurl = "https://www.assemblee-nationale.fr" + jsonurl.group(1)

try:
    data = requests.get(jsonurl).json()
except:
    print >> sys.stderr, "ERROR: could not download JSON opendata for amendement %s at %s" % (htmlurl, jsonurl)
    sys.exit(1)

extract_numero = lambda data: data['identification']['numeroLong'].split(" ")[0].split("-")[-1]
simplify_url = lambda u: u.replace('https://', 'http://').replace('/dyn/', '/').replace('.asp', '')

h = HTMLParser()
try:
    amd = {}
    amd['legislature'] = data['legislature']
    amd['numero'] = extract_numero(data)
    amd['loi'] = data['pointeurFragmentTexte']['division']['urlDivisionTexteVise'].split('/textes/')[1].split('.asp')[0]
    amd['sort'] = extractSort(data['cycleDeVie']['sort'] or data['cycleDeVie']['etatDesTraitements']['sousEtat'].get('libelle', None) or data['cycleDeVie']['etatDesTraitements']['etat'].get('libelle', ''))
    amd['date'] = data['cycleDeVie']['dateDepot']

    amd['source'] = htmlurl
    organe = data['identification']['prefixeOrganeExamen']
    amdurl = "http://www.assemblee-nationale.fr/dyn/%s/amendements/%s/%s/%s" % (amd['legislature'], amd['loi'], organe, amd['numero'])
    if simplify_url(amd['source']) != simplify_url(htmlurl):
        print >> sys.stderr, "WARNING: source URL parsed (%s) different than original URL for amendement %s" % (amdurl, htmlurl)

    amd['rectif'], amd['serie'] = extractNumRectif(data['identification']['numeroLong'])
    if amd['rectif'] != data['identification']['numeroRect']:
        print >> sys.stderr, "WARNING: rectification number parsed (%s) different than OpenData's (%s) for amendement %s" % (amd['rectif'], data['identification']['numeroRect'], htmlurl)

    amd['parent'] = ""
    if data['amendementParentRef']:
        try:
            parentjson = "https://www.assemblee-nationale.fr/dyn/opendata/%s.json" % data['amendementParentRef']
            parentdata = requests.get(parentjson).json()
            amd['parent'] = extract_numero(parentdata)
        except:
            print >> sys.stderr, "WARNING: could not retrieve parent numero from parent amendement's json (%s %s) for amendement %s" % (parentjson, data['amendementParentRef'], htmlurl)

    amd['sujet'] = h.unescape(data['pointeurFragmentTexte']['division']['articleDesignation'])
    amd['sujet'] = clean_subject(amd['sujet'], source=htmlurl)

    amd['auteurs'] = data['signataires']['libelle']
    if data['signataires']['suffixe']:
        amd['auteurs'] += " %s" % data['signataires']['suffixe']
    amd['auteurs'] = cleanAuteurs(amd['auteurs'], h)
    amd['auteur_reel'] = data['signataires']['auteur']['acteurRef']

    amd['expose'] = fixHTML(data['corps']['contenuAuteur']['exposeSommaire'], h)
    # TODO: check tableaux ok
    if 'dispositif' in data['corps']['contenuAuteur']:
        amd['texte'] = fixHTML(data['corps']['contenuAuteur']['dispositif'], h)
    else:
        try:
            dispurl = "https://www.assemblee-nationale.fr/dyn/%s/amendements/dispositif/%s.fragmenthtml" % (amd['legislature'], data['uid'])
            disphtml = requests.get(dispurl).text
            amd['texte'] = fixHTML(disphtml, h)
        except:
            print >> sys.stderr, "ERROR: could not get missing dispositif for amendement %s: %s" % (htmlurl, dispurl)
            sys.exit(1)

except Exception as e:
    print >> sys.stderr, "ERROR: could not parse JSON opendata for amendement %s at %s" % (htmlurl, jsonurl)
    print >> sys.stderr, "%s: %s" % (type(e), e)
    from pprint import pprint
    pprint(data)
    sys.exit(1)

print json.dumps(amd, ensure_ascii=False).encode('utf-8')
