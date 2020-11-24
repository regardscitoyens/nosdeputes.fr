#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sys
import datetime
import requests
import bs4

legislature = sys.argv[1] if len(sys.argv) > 1 else 15
count = 0

datefin = datetime.datetime.now()
datedebut = datetime.datetime.now() - datetime.timedelta(days=7)

while datedebut <= datefin:
    url = "http://www.assemblee-nationale.fr/dyn/opendata/list-publication/publication_" + datedebut.strftime('%Y-%m-%d')
    print(url)
    resp = requests.get(url)
    for line in resp.text.split('\n'):
        if line:
            _, file_url = line.split(';')
            if 'opendata/AMAN' in file_url and file_url.endswith('.xml'):
                print(file_url)
                resp = requests.get(file_url)
                soup = bs4.BeautifulSoup(resp.text, 'lxml')
                code = soup.select_one('code').text
                num = soup.select_one('numerolong').text \
                    .replace(' (Rect)', '') \
                    .replace('I-', '') \
                    .replace('II-', '') \
                    .replace('III-', '')
                organe = soup.select_one('prefixeorganeexamen').text
                texte = soup.select_one('urldivisiontextevise').text.split('/textes/')[1].split('.asp')[0]
                url_amdt = f"http://www.assemblee-nationale.fr/dyn/{legislature}/amendements/{texte}/{organe}/{num}"

                print(url_amdt)
                resp = requests.get(url_amdt, cookies={'website_version': 'old'})

                if resp.status_code != 200:
                    print('invalid response')
                    continue

                with open(f'html/{texte}-{organe}-{num}.html', 'w') as f:
                    f.write(resp.text)
                    count += 1
    datedebut += datetime.timedelta(days=1)

print(count, 'amendements téléchargés')