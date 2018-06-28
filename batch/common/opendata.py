# -*- coding: utf8 -*-
from __future__ import print_function, unicode_literals

import json
import os
import sys
from zipfile import ZipFile

from bs4 import BeautifulSoup
import requests

from . import COMMON_DIR

CACHE_DIR = os.path.join(COMMON_DIR, "opendata")

AN_BASE_URL = "http://data.assemblee-nationale.fr"
AN_ENTRYPOINTS = {
    "14": {
        "amo": "opendata-archives-xive/deputes-senateurs-et-ministres-xive-legislature",
        "reunions": "opendata-archives-xive/agendas-xive-legislature",
        "scrutins": "opendata-archives-xive/scrutins-xive-legislature",
    },
    "15": {
        "amo": "acteurs/deputes-en-exercice",
        "reunions": "reunions/reunions",
        "scrutins": "travaux-parlementaires/votes",
    },
}


def log(str):
    print(str, file=sys.stderr)


def fetch_an_jsonzip(legislature, objet):
    """
    Télécharge le zip du JSON depuis une page de l'open data AN, s'il a été
    modifié depuis le dernier téléchargement.

    Renvoie le chemin local du fichier zip téléchargé (stocké dans le
    répertoire de cache) et un flag indiquant s'il a été modifié
    """

    if (
        str(legislature) not in AN_ENTRYPOINTS
        or objet not in AN_ENTRYPOINTS[str(legislature)]
    ):
        raise Exception(
            "Objet inconnu: %s (%s legislature)" % (objet, legislature)
        )

    if not os.path.exists(CACHE_DIR):
        os.makedirs(CACHE_DIR)

    localzip = os.path.join(CACHE_DIR, "%s_%s.zip" % (legislature, objet))
    localzip_lastmod = "%s.last_modified" % localzip

    url = "%s/%s" % (AN_BASE_URL, AN_ENTRYPOINTS[str(legislature)][objet])
    log("Téléchargement %s" % url)

    try:
        soup = BeautifulSoup(requests.get(url).content, "html5lib")
    except Exception:
        raise Exception("Téléchargement %s impossible" % url)

    def match_link(a):
        return a["href"].endswith(".json.zip") or a["href"].endswith(
            ".json.zip "
        )

    try:
        link = [a for a in soup.select("a[href]") if match_link(a)][0]
    except Exception:
        raise Exception("Lien vers dump .json.zip introuvable")

    jsonzip_url = link["href"].replace(".json.zip ", ".json.zip")
    if jsonzip_url.startswith("/"):
        jsonzip_url = "%s%s" % (AN_BASE_URL, jsonzip_url)

    log("URL JSON zippé : %s" % jsonzip_url)

    try:
        lastmod = requests.head(jsonzip_url).headers["Last-Modified"]
    except Exception:
        raise Exception("Date du dump .json.zip introuvable")

    log("Date modification dump .json.zip: %s" % lastmod)
    do_download = True

    if os.path.exists(localzip) and os.path.exists(localzip_lastmod):
        with open(localzip_lastmod, "r") as f:
            known_lastmod = f.read()

        log("Date modification dernier telechargement: %s" % known_lastmod)
        if known_lastmod == lastmod:
            do_download = False

    if do_download:
        log("Téléchargement .json.zip")

        try:
            with open(localzip, "wb") as out:
                r = requests.get(jsonzip_url, stream=True)
                for block in r.iter_content(1024):
                    out.write(block)
            with open(localzip_lastmod, "w") as f:
                f.write(lastmod)
        except Exception:
            raise Exception("Téléchargement .json.zip impossible")
    else:
        log("Téléchargement skippé, fichier non mis à jour")

    return localzip, do_download


def fetch_an_json(legislature, objet):
    """
    Télécharge le zip du JSON depuis une page de l'open data AN, s'il a été
    modifié depuis le dernier téléchargement.

    page: URL relative de la page, par exemple "travaux-parlementaires/votes"

    Renvoie les données JSON du fichier zip téléchargé et un flag indiquant si
    le fichier a été modifié.
    """

    localzip, updated = fetch_an_jsonzip(legislature, objet)
    with ZipFile(localzip, "r") as z:
        for f in [f for f in z.namelist() if f.endswith(".json")]:
            log("JSON extrait : %s" % f)
            with z.open(f) as zf:
                return json.load(zf), updated


def _cached_ref(
    legislature, objet, id_mapping, extract_list, extract_id, extract_mapped
):
    """
    Génère et renvoie un cache de mapping d'identifiants à partir d'un dump
    open data json.

    legislature, objet: définit le dump à utiliser
    id_mapping: identifiant unique du mapping, utilisé pour stocker en cache
    extract_list: fonction qui extrait la liste des items du dump json
    extract_id: fonction qui extrait l'identifiant à mapper d'un item
    extract_mapped: fonction qui extrait les données mappées d'un item
    """

    data, updated = fetch_an_json(legislature, objet)
    cached_file = os.path.join(
        CACHE_DIR, "mapping_%s_%s.json" % (legislature, id_mapping)
    )

    if updated or not os.path.exists(cached_file):
        cache = {}
        for item in extract_list(data):
            id = extract_id(item)
            cache[id] = extract_mapped(item)

        with open(cached_file, "w") as f:
            json.dump(cache, f)
        return cache
    else:
        with open(cached_file) as f:
            return json.load(f)


def ref_groupes(legislature):
    """
    Renvoie un mapping des id opendata des groupes parlementaires vers leur
    abbréviation
    """

    def _extract_list(data):
        return filter(
            lambda o: o["codeType"] == "GP",
            data["export"]["organes"]["organe"],
        )

    def _extract_id(organe):
        return organe["uid"]

    def _extract_mapped(organe):
        return organe["libelleAbrev"]

    return _cached_ref(
        legislature,
        "amo",
        "groupes",
        _extract_list,
        _extract_id,
        _extract_mapped,
    )


def ref_seances(legislature):
    """
    Renvoie un mapping des id opendata des séances vers leur ID
    """

    def _extract_list(data):
        return filter(
            lambda reunion: "IDS" in reunion["uid"],
            data["reunions"]["reunion"],
        )

    def _extract_id(reunion):
        return reunion["uid"]

    def _extract_mapped(reunion):
        return reunion["identifiants"]["idJO"]

    return _cached_ref(
        legislature,
        "reunions",
        "seances",
        _extract_list,
        _extract_id,
        _extract_mapped,
    )
