#!/usr/bin/env python
# -*- coding: utf-8 -*-

from __future__ import print_function
import os
import sys
import datetime
import requests
import bs4

legislature = sys.argv[1] if len(sys.argv) > 1 else 15
count = 0

datefin = datetime.datetime.now()
datedebut = datetime.datetime.now() - datetime.timedelta(days=45)

while datedebut <= datefin:
    url = "https://www.assemblee-nationale.fr/dyn/opendata/list-publication/publication_" + datedebut.strftime('%Y-%m-%d')
    print(url)
    resp = requests.get(url)
    if resp.status_code != 404:
        for line in resp.text.split('\n'):
            if line:
                _, file_url = line.split(';')
                if 'opendata/AMAN' in file_url and file_url.endswith('.xml'):
                    #print(file_url)
                    resp = requests.get(file_url)
                    soup = bs4.BeautifulSoup(resp.text, 'lxml')
                    code = soup.select_one('code').text
                    num = soup.select_one('numerolong').text.split(" ")[0].split("-")[-1]
                    organe = soup.select_one('prefixeorganeexamen').text
                    texte = soup.select_one('urldivisiontextevise').text.split('/textes/')[1].split('.asp')[0]
                    url_amdt = "http://www.assemblee-nationale.fr/dyn/%s/amendements/%s/%s/%s" % (legislature, texte, organe, num)

                    print(url_amdt)
                    resp = requests.get(url_amdt.replace("http://", "https://"), cookies={'website_version': 'old'})

                    if resp.status_code != 200:
                        print('invalid response')
                        continue

                    slug = url_amdt.replace('/', '_-_')
                    with open(os.path.join('html', slug), 'w') as f:
                        f.write(resp.text.encode("utf-8"))
                        count += 1
    datedebut += datetime.timedelta(days=1)

print(count, 'amendements téléchargés')
