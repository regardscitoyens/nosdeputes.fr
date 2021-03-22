#!/usr/bin/env python
# -*- coding: utf-8 -*-

import re
import sys
import json
import time
import requests
from HTMLParser import HTMLParser

from clean_subjects_amdmts import clean_subject
from parseAmdtsFromANOpenData import extractNumRectif, cleanAuteurs, extractSort, fixHTML, parseUrl

htmlfile = sys.argv[1]
htmlurl = htmlfile.replace("html/", "").replace('_-_', '/')
_, numero = parseUrl(htmlurl)

with open(htmlfile) as f:
    htmlcontent = f.read()

re_opendata_json = re.compile(r'<a [^>]*href="(/dyn/opendata/.*\.json)"', re.I)

jsonurl = re_opendata_json.search(htmlcontent)
if not jsonurl:
    print >> sys.stderr, "ERROR: could not find link to JSON opendata for amendement %s" % htmlurl
    sys.exit(1)
jsonurl = "https://www.assemblee-nationale.fr" + jsonurl.group(1)

def download(url, as_json=True, retries=5):
    try:
        req = requests.get(url)
        if as_json:
            return req.json()
        return req.text
    except Exception as e:
        if retries > 0:
            time.sleep((6-retries)*5)
            return download(url, as_json=as_json, retries=retries-1)
        raise(e)

try:
    data = download(jsonurl)
except Exception as e:
    print >> sys.stderr, "ERROR: could not download JSON opendata for amendement %s at %s (%s: %s)" % (htmlurl, jsonurl, type(e), e)
    sys.exit(1)

extract_numero = lambda data: data['identification']['numeroLong'].split(" ")[0].split("-")[-1]
simplify_url = lambda u: u.replace('https://', 'http://').replace('/dyn/', '/').replace('.asp', '')

h = HTMLParser()
try:
    amd = {}
    amd['legislature'] = data['legislature']
    loi_from_url = htmlurl.split('/')[-3]
    try:
        loi = data['pointeurFragmentTexte']['division']['urlDivisionTexteVise'].split('/textes/')[1].split('.asp')[0]
        if loi != loi_from_url:
            print >> sys.stderr, "WARNING: numero loi parsed from url (%s) is different than Open Data's (%s) for amendement %s" % (loi_from_url, loi, htmlurl)
    except:
        loi = loi_from_url
        print >> sys.stderr, "WARNING: could not find numero loi in Open Data (%s) for amendement %s : extracting it from url (%s)" % (jsonurl, htmlurl, loi)

    try:
        lettre = re.search(ur"([A-Z])$", loi, re.I).group(1).upper()
        loi = loi[:-1]
    except:
        lettre = ""
    if not loi.startswith(u"TA"):
        loi = int(loi)
        loistr = "%04d%s" % (loi, lettre)
    else:
        loistr = loi + lettre
    amd['loi'] = str(loi)

    amd['numero'] = extract_numero(data)
    organe = htmlurl.split('/')[-2]
    numstr = amd['numero']
    organestr = ""
    if organe != u"AN":
        amd['commission'] = organe
        if not re.search(ur"^[A-Z]", numstr, re.I):
            organestr = re.sub(ur"[^A-Z]", "", organe, re.I)
            amd['numero'] = organestr + amd['numero']
    amd['numero'] += lettre
    if amd['numero'] != numero:
        print >> sys.stderr, "WARNING: numero parsed from url (%s) is different than Open Data's (%s) for amendement %s" % (numero, amd['numero'], htmlurl)

    if not data['cycleDeVie']['sort'] and ("recevab" in data['cycleDeVie']['etatDesTraitements']['etat']['libelle'] or len(data['cycleDeVie']['etatDesTraitements']['sousEtat'].get('libelle', ''))) <= 3:
        sort = data['cycleDeVie']['etatDesTraitements']['etat']['libelle']
    else:
        sort = data['cycleDeVie']['sort'] or data['cycleDeVie']['etatDesTraitements']['sousEtat'].get('libelle', None) or data['cycleDeVie']['etatDesTraitements']['etat'].get('libelle', '')
    amd['sort'] = extractSort(sort)
    amd['date'] = data['cycleDeVie']['dateDepot'] or data['cycleDeVie']['datePublication'] or ""

    amd['source'] = htmlurl
    organe = data['identification']['prefixeOrganeExamen']
    amdurl = "http://www.assemblee-nationale.fr/dyn/%s/amendements/%s/%s/%s" % (amd['legislature'], loistr, organe, numstr)
    if simplify_url(amd['source']) != simplify_url(htmlurl):
        print >> sys.stderr, "WARNING: source URL parsed (%s) different than original URL for amendement %s" % (amdurl, htmlurl)

    amd['rectif'], amd['serie'] = extractNumRectif(data['identification']['numeroLong'])
    if amd['rectif'] != data['identification']['numeroRect']:
        print >> sys.stderr, "WARNING: rectification number parsed (%s) different than OpenData's (%s) for amendement %s" % (amd['rectif'], data['identification']['numeroRect'], htmlurl)

    amd['parent'] = ""
    if data['amendementParentRef']:
        try:
            parentjson = "https://www.assemblee-nationale.fr/dyn/opendata/%s.json" % data['amendementParentRef']
            parentdata = download(parentjson)
            amd['parent'] = extract_numero(parentdata)
            if organestr and not re.search(ur"^[A-Z]", amd['parent'], re.I):
                amd['parent'] = organestr + amd['parent']
        except Exception as e:
            print >> sys.stderr, "WARNING: could not retrieve parent numero from parent amendement's json (%s %s) for amendement %s (%s: %s)" % (parentjson, data['amendementParentRef'], htmlurl, type(e), e)

    amd['sujet'] = h.unescape(data['pointeurFragmentTexte']['division']['articleDesignation'])
    amd['sujet'] = clean_subject(amd['sujet'], source=htmlurl)

    amd['auteurs'] = h.unescape(data['signataires']['libelle'])
    if data['signataires']['suffixe'] and data['signataires']['suffixe'] not in amd['auteurs']:
        amd['auteurs'] += " %s" % data['signataires']['suffixe']
    amd['auteurs'] = cleanAuteurs(amd['auteurs'], h)
    amd['auteur_reel'] = (data['signataires']['auteur']['auteurRapporteurOrganeRef'] or data['signataires']['auteur']['acteurRef'] or "GVT").lstrip('PAO')

    try:
        if int(data['cardinaliteAmdtMultiples']) > 1:
            print >> sys.stderr, "WARNING: encountered an amendment with a multiple cardinality(%s): %s %s" % (data['cardinaliteAmdtMultiples'], htmlurl, jsonurl)
    except:
        pass

    if not data['corps']['contenuAuteur']:
        if amd['sort'] == u'Retiré avant séance':
            amd['texte'] = ''
            amd['texte'] = u'<p>Cet amendement a été retiré avant sa publication.</p>'
        elif amd['sort'] == u'Irrecevable' or ('cartoucheInformatif' in data['corps'] and 'recevab' in data['corps']['cartoucheInformatif']):
            amd['sort'] = 'Irrecevable'
            amd['texte'] = ''
            amd['texte'] = '<p>%s</p>' % data['corps']['cartoucheInformatif']
        else:
            print >> sys.stderr, "ERROR: contenuAuteur is missing for amendement %s: %s" % (htmlurl, jsonurl)
            sys.exit(1)
    else:
        amd['expose'] = fixHTML(data['corps']['contenuAuteur']['exposeSommaire'], h)
        if 'dispositif' in data['corps']['contenuAuteur']:
            amd['texte'] = fixHTML(data['corps']['contenuAuteur']['dispositif'], h)
        else:
            try:
                dispurl = "https://www.assemblee-nationale.fr/dyn/%s/amendements/dispositif/%s.fragmenthtml" % (amd['legislature'], data['uid'])
                disphtml = download(dispurl, as_json=False)
                amd['texte'] = fixHTML(disphtml, h)
            except Exception as e:
                print >> sys.stderr, "ERROR: could not get missing dispositif for amendement %s: %s (%s: %s)" % (htmlurl, dispurl, type(e), e)
                sys.exit(1)

except Exception as e:
    print >> sys.stderr, "ERROR: could not parse JSON opendata for amendement %s at %s" % (htmlurl, jsonurl)
    print >> sys.stderr, "%s: %s" % (type(e), e)
    from pprint import pprint
    pprint(data)
    sys.exit(1)

print json.dumps(amd, ensure_ascii=False).encode('utf-8')
