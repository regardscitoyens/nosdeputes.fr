#!/usr/bin/env python
# -*- coding: utf-8 -*-

from __future__ import print_function
import os
import sys
import time
import datetime
import requests

GLOBAL_RETRIES = 0

def download(url, json=True, retries=5):
    resp = None
    try:
        resp = requests.get(url)
        if resp.status_code != 200:
            if not retries or (retries < 5 and resp.status_code == 404):
                print("ERROR: could not download amendement at %s: (HTTP code: %s)" % (url, resp.status_code), file=sys.stderr)
                return None
            time.sleep((6-retries)*5)
            return download(url, json=json, retries=retries-1)
        if json:
            return resp.json()
        return resp.text
    except Exception as e:
        if not resp or resp.status_code != 404:
            GLOBAL_RETRIES += 1
            if GLOBAL_RETRIES > 50:
                sys.exit("ERROR: too many retries, server AN seems to error")
        if retries > 0:
            time.sleep((6-retries)*5)
            return download(url, json=json, retries=retries-1)
        print("ERROR: could not download %s at %s: (%s - %s)" % ('json' if json else 'html', url, type(e), e), file=sys.stderr)
        return None


if __name__ == "__main__":
    legislature = sys.argv[1] if len(sys.argv) > 1 else 15
    daysback = int(sys.argv[2]) if len(sys.argv) > 2 else 3
    count = 0

    datefin = datetime.datetime.now()
    datedebut = datetime.datetime.now() - datetime.timedelta(days=daysback)

    while datedebut <= datefin:
        url = "https://www.assemblee-nationale.fr/dyn/opendata/list-publication/publication_" + datedebut.strftime('%Y-%m-%d')
        print(url)
        resp = requests.get(url)
        if resp.status_code != 404:
            for line in resp.text.split('\n'):
                if line and ';' in line:
                    _, file_url = line.split(';')
                    if 'opendata/AMAN' in file_url and file_url.endswith('.xml'):
                        file_url = file_url.replace(".xml", ".json")
                        #print(file_url)
                        resp = download(file_url)
                        if not resp:
                            continue
                        num = resp['identification']['numeroLong'].split(" ")[0].split("-")[-1]
                        organe = resp['identification']['prefixeOrganeExamen']
                        try:
                            texte = resp['pointeurFragmentTexte']['division']['urlDivisionTexteVise'].split('/textes/')[1].split('.asp')[0]
                            url_amdt = "https://www.assemblee-nationale.fr/dyn/%s/amendements/%s/%s/%s" % (legislature, texte, organe, num)
                        except (KeyError, AttributeError):
                            r = requests.get("https://www.assemblee-nationale.fr/dyn/15/amendements/%s" % resp["uid"])
                            url_amdt = r.url.replace("http:", "https:")

                        print(url_amdt)
                        text = download(url_amdt, json=False)
                        if not text:
                            continue

                        slug = url_amdt.replace('/', '_-_')
                        with open(os.path.join('html', slug), 'w') as f:
                            f.write(text.encode("utf-8"))
                            count += 1
        datedebut += datetime.timedelta(days=1)

    print(count, 'amendements téléchargés')
