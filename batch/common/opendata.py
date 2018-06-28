# -*- coding: utf8 -*-
from __future__ import print_function, unicode_literals

import os
import json
from zipfile import ZipFile

from bs4 import BeautifulSoup
import requests

AN_BASE_URL = 'http://data.assemblee-nationale.fr'
AN_ENTRYPOINTS = {
    '14': {
        'scrutins': 'opendata-archives-xive/scrutins-xive-legislature'
    },
    '15': {
        'scrutins': 'travaux-parlementaires/votes'
    }
}


def log(str):
    print(str)


def fetch_an_jsonzip(legislature, objet, cache_dir='./tmp'):
    """
    Télécharge le zip du JSON depuis une page de l'open data AN, s'il a été
    modifié depuis le dernier téléchargement.

    Renvoie le chemin local du fichier zip téléchargé (stocké dans le
    répertoire de cache)
    """

    if str(legislature) not in AN_ENTRYPOINTS \
            or objet not in AN_ENTRYPOINTS[str(legislature)]:
        raise Exception('Objet inconnu: %s (%s legislature)' % (objet,
                                                                legislature))

    if not os.path.exists(cache_dir):
        os.makedirs(cache_dir)

    localzip = os.path.join(cache_dir, "%s_%s.zip" % (legislature, objet))
    localzip_lastmod = '%s.last_modified' % localzip

    url = '%s/%s' % (AN_BASE_URL, AN_ENTRYPOINTS[str(legislature)][objet])
    log('Téléchargement %s' % url)

    try:
        soup = BeautifulSoup(requests.get(url).content, 'html5lib')
    except Exception:
        raise Exception('Téléchargement %s impossible' % url)

    def match_link(a):
        return a['href'].endswith('.json.zip') or \
            a['href'].endswith('.json.zip ')

    try:
        link = [a for a in soup.select('a[href]') if match_link(a)][0]
    except Exception:
        raise Exception('Lien vers dump .json.zip introuvable')

    jsonzip_url = link['href'].replace('.json.zip ', '.json.zip')
    if jsonzip_url.startswith('/'):
        jsonzip_url = '%s%s' % (AN_BASE_URL, jsonzip_url)

    log('URL JSON zippé : %s' % jsonzip_url)

    try:
        lastmod = requests.head(jsonzip_url).headers['Last-Modified']
    except Exception:
        raise Exception('Date du dump .json.zip introuvable')

    log('Date modification dump .json.zip: %s' % lastmod)
    do_download = True

    if os.path.exists(localzip) and os.path.exists(localzip_lastmod):
        with open(localzip_lastmod, 'r') as f:
            known_lastmod = f.read()

        log('Date modification dernier telechargement: %s' % known_lastmod)
        if known_lastmod == lastmod:
            do_download = False

    if do_download:
        log('Téléchargement .json.zip')

        try:
            with open(localzip, 'wb') as out:
                r = requests.get(jsonzip_url, stream=True)
                for block in r.iter_content(1024):
                    out.write(block)
            with open(localzip_lastmod, 'w') as f:
                f.write(lastmod)
        except Exception:
            raise Exception('Téléchargement .json.zip impossible')
    else:
        log('Téléchargement skippé, fichier non mis à jour')

    return localzip


def fetch_an_json(page):
    """
    Télécharge le zip du JSON depuis une page de l'open data AN, s'il a été
    modifié depuis le dernier téléchargement.

    page: URL relative de la page, par exemple "travaux-parlementaires/votes"

    Renvoie les données JSON du fichier zip téléchargé.
    """

    with ZipFile(fetch_an_jsonzip(page), 'r') as z:
        for f in [f for f in z.namelist() if f.endswith('.json')]:
            log('JSON extrait : %s' % f)
            with z.open(f) as zf:
                return json.load(zf)
