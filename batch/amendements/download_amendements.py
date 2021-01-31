#!/usr/bin/env python
# -*- coding: utf-8 -*-

from __future__ import print_function
import os
import sys
import time
import datetime
import requests

legislature = sys.argv[1] if len(sys.argv) > 1 else 15
daysback = int(sys.argv[2]) if len(sys.argv) > 2 else 7
count = 0

datefin = datetime.datetime.now()
datedebut = datetime.datetime.now() - datetime.timedelta(days=daysback)

def download_json(url, retries=5):
    try:
        return requests.get(url).json()
    except Exception as e:
        if retries < 5:
            time.sleep(5)
            return download_json(url, retries=retries-1)
        print("ERROR: could not download json at %s: (%s - %s)" % (url, type(e), e), file=sys.stderr)
        return None

while datedebut <= datefin:
    url = "https://www.assemblee-nationale.fr/dyn/opendata/list-publication/publication_" + datedebut.strftime('%Y-%m-%d')
    print(url)
    resp = requests.get(url)
    if resp.status_code != 404:
        for line in resp.text.split('\n'):
            if line:
                _, file_url = line.split(';')
                if 'opendata/AMAN' in file_url and file_url.endswith('.xml'):
                    file_url = file_url.replace(".xml", ".json")
                    #print(file_url)
                    resp = download_json(file_url)
                    if resp is None:
                        continue
                    num = resp['identification']['numeroLong'].split(" ")[0].split("-")[-1]
                    organe = resp['identification']['prefixeOrganeExamen']
                    texte = resp['pointeurFragmentTexte']['division']['urlDivisionTexteVise'].split('/textes/')[1].split('.asp')[0]
                    url_amdt = "http://www.assemblee-nationale.fr/dyn/%s/amendements/%s/%s/%s" % (legislature, texte, organe, num)

                    print(url_amdt)
                    resp = requests.get(url_amdt.replace("http://", "https://"))#, cookies={'website_version': 'old'})

                    if resp.status_code != 200:
                        print("ERROR: could not download amendement at %s: (HTTP code: %s)" % (url_amdt, resp.status_code), file=sys.stderr)
                        continue

                    slug = url_amdt.replace('/', '_-_')
                    with open(os.path.join('html', slug), 'w') as f:
                        f.write(resp.text.encode("utf-8"))
                        count += 1
    datedebut += datetime.timedelta(days=1)

print(count, 'amendements téléchargés')
