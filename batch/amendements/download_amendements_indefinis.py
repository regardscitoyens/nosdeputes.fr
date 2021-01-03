#!/usr/bin/env python
# -*- coding: utf-8 -*-

from __future__ import print_function
import os
import requests

count = 0

for url_amdt in open('liste_sort_indefini.txt'):
    url_amdt = url_amdt.strip()
    print(url_amdt)
    resp = requests.get(url_amdt, cookies={'website_version': 'old'})
    if resp.status_code != 200:
        print('invalid response')
        continue
    slug = url_amdt.replace('/', '_-_')
    with open(os.path.join('html', slug), 'w') as f:
        f.write(resp.text.encode("utf-8"))
        count += 1

print(count, 'amendements indefinis téléchargés')
