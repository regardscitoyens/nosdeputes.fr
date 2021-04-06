#!/usr/bin/env python
# -*- coding: utf-8 -*-

from __future__ import print_function
import os
import sys
from download_amendements import download

count = 0

for url_amdt in open(sys.argv[1]):
    url_amdt = url_amdt.strip().replace("http://", "https://")
    slug = url_amdt.replace('/', '_-_')
    url_amdt = url_amdt.replace(".asp", "").replace("nationale.fr/1", "nationale.fr/dyn/1")
    print(url_amdt)
    text = download(url_amdt, json=False)
    if not text:
        continue

    with open(os.path.join('html', slug), 'w') as f:
        f.write(text.encode("utf-8"))
        count += 1

print(count, 'amendements indefinis téléchargés')
