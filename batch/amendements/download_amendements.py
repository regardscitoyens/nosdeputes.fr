#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sys
import datetime
import requests
import bs4

legislature = sys.argv[1] if len(sys.argv) > 1 else 15
count = 0

now = datetime.datetime.now()
day = int(now.strftime("%d"))
month = int(now.strftime("%m"))
year = int(now.strftime("%Y"))
datefin = datetime.datetime(year+1, 1, 1)
if day > 7:
    day -= 7
else:
    day += 21
    if month == 1:
        month = 12
        year -= 1
    else:
        month -= 1
datedebut = datetime.datetime(year, month, day)

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
                num = soup.select_one('numerolong').text.replace(' (Rect)', '')
                organe = soup.select_one('prefixeorganeexamen').text
                texte = soup.select_one('textelegislatifref').text.split('B')[1]
                url_amdt = f"http://www.assemblee-nationale.fr/dyn/15/amendements/{texte}/{organe}/{num}"

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