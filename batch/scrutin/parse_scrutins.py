#!/usr/bin/env python
# -*- coding: utf8 -*-

"""
parse_scrutins.py - Analyse des scrutins depuis l'open data AN

Utilisation:
    parse_scrutins.py <legislature>

Répertoire de travail:
    /data/opendata/scrutins

Extrait pour chaque scrutin une représentation JSON conforme au modèle de
données, et un hash SHA1 de cette représentation.

Si le fichier scrutin_<legislature>_<numero>.sha1 existe dans le répertoire de
travail et contient un SHA1 différent, ou s'il n'existe pas, la réprésentation
JSON est stockée dans le fichier scrutin_<legislature>_<numero>.json et le SHA1
est mis à jour.
"""

from __future__ import print_function, unicode_literals, absolute_import

import hashlib
import json
import os
import sys

# Faux module dans .. (batch/)
sys.path.append(os.path.join(os.path.dirname(__file__), ".."))

from common import BATCH_DIR  # noqa
from common.opendata import (
    fetch_an_json,
    ref_groupes,
    ref_seances,
    log,
)  # noqa


POSITIONS = ("pour", "contre", "abstention", "nonVotant")
POS_MAP = {
    "pour": "pours",
    "nonVotant": "nonVotants",
    "nonVotantVolontaire": "nonVotantsVolontaires",
    "contre": "contres",
    "abstention": "abstentions",
}


SCRUTINS_DIR = os.path.join(BATCH_DIR, "scrutin", "scrutins")

TYPES = {"SPS": "solennel", "SPO": "ordinaire"}


def parse_scrutins(legislature, data):
    groupes = ref_groupes(legislature, ND_names=True)
    seances = ref_seances(legislature)

    if not os.path.exists(SCRUTINS_DIR):
        os.makedirs(SCRUTINS_DIR)

    for item in data["scrutins"]["scrutin"]:
        scrutin = parse_scrutin(item, seances, groupes)

        numero = ("00000%s" % scrutin["numero"])[-5:]
        basename = "scrutin_%s_%s" % (legislature, numero)
        hash_file = os.path.join(SCRUTINS_DIR, "%s.sha1" % basename)
        json_file = os.path.join(SCRUTINS_DIR, "%s.json" % basename)

        json_data = json.dumps(scrutin, sort_keys=True)

        hash = hashlib.sha1()
        hash.update(json_data)
        sha1 = hash.hexdigest()

        updated = True
        if os.path.exists(hash_file):
            with open(hash_file) as f:
                previous_sha1 = f.read()
            if previous_sha1 == sha1:
                updated = False

        if updated:
            with open(hash_file, "w") as f:
                f.write(sha1)
            with open(json_file, "w") as f:
                f.write(json_data)
            log("Scrutin %s mis à jour" % scrutin["numero"])


def parse_scrutin(data, seances, groupes):
    synthese = data["syntheseVote"]
    decompte = synthese["decompte"]
    scrutin = {
        "numero": int(data["numero"]),
        "seance": seances[data["seanceRef"]],
        "titre": data["titre"],
        "type": TYPES[data["typeVote"]["codeTypeVote"]],
        "nombre_votants": int(synthese["nombreVotants"]),
        "nombre_pours": int(decompte["pour"]),
        "nombre_contres": int(decompte["contre"]),
        "nombre_abstentions": int(decompte["abstentions"]),
        "sort": data["sort"]["code"],
        "demandeur": data["demandeur"]["texte"],
        "parlementaires": {},
    }

    delegations = 0

    vote_groupes = data["ventilationVotes"]["organe"]["groupes"]["groupe"]
    for vote_groupe in vote_groupes:
        acro_groupe = groupes[vote_groupe["organeRef"]]
        dn = vote_groupe["vote"]["decompteNominatif"]
        position_groupe = vote_groupe["vote"]["positionMajoritaire"]

        for position in POSITIONS:
            votants_position = dn.get("%ss" % position, dn.get(position))
            if not votants_position:
                continue
            votants = votants_position["votant"]

            if not isinstance(votants, list):
                votants = [votants]

            for votant in votants:
                par_delegation = None
                if "parDelegation" in votant:
                    par_delegation = votant["parDelegation"] == "true"
                    if par_delegation:
                        delegations += 1

                scrutin["parlementaires"][votant["acteurRef"]] = {
                    "position": position,
                    "groupe": acro_groupe,
                    "position_groupe": position_groupe,
                    "par_delegation": par_delegation,
                }

    if 2 * delegations > int(synthese["nombreVotants"]):
        log("Scrutin %s: trop de délégations" % scrutin["numero"])

    vote_map = data["miseAuPoint"]
    if vote_map:
        for position, pluriel in POS_MAP.items():
            map_position = vote_map[pluriel]
            if not isinstance(map_position, list):
                map_position = [map_position]
            for part in map_position:
                if not part:
                    continue
                votants = part["votant"]
                if not isinstance(votants, list):
                    votants = [votants]
                for votant in votants:
                    if votant["acteurRef"] not in scrutin["parlementaires"]:
                        scrutin["parlementaires"][votant["acteurRef"]] = {}
                    parl = scrutin["parlementaires"][votant["acteurRef"]]
                    parl["mise_au_point_position"] = position

    return scrutin


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Utilisation: %s <legislature>" % sys.argv[0])
        sys.exit(1)

    legislature = sys.argv[1]
    data, updated = fetch_an_json(legislature, "scrutins")
    parse_scrutins(legislature, data)
