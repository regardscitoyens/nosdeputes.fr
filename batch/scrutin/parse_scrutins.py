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
import re
import sys

# Faux module dans .. (batch/)
sys.path.append(os.path.join(os.path.dirname(__file__), ".."))

from common import BATCH_DIR  # noqa
from common.opendata import (
    fetch_an_json,
    ref_groupes,
    ref_histo_groupes,
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

TYPES = {"SPS": "solennel", "SPO": "ordinaire", "MOC": "solennel"}

CLEAN_DEMANDEUR = [
    (u"Pr[eé]sident", u"Président"),
    ("Conference", u"Conférence"),
    ('"', ''),
    (r'\s+', ' '),
]

def clean_demandeur(d):
    for reg, rep in CLEAN_DEMANDEUR:
        d = re.compile(reg, re.I).sub(rep, d)
    return d.strip()

MISSING_DEMANDEURS = {
    "17": [u"Président du groupe Nouvelle Gauche"],
    "43": [u"Président du groupe La France Insoumise"],
    "153": [u"Président du groupe Les Républicains"],
    "1573": [u"Président du groupe Gauche démocrate et républicaine", u"Président du groupe Nouvelle Gauche", u"Président du groupe La France Insoumise"]
}
def clean_demandeurs(demandeurs, numero):
    if not demandeurs:
        return MISSING_DEMANDEURS.get(numero, [])
    demandeurs = re.sub(ur"[\s\n]*(Commission|Pr[eé]sident)", ur"\n\1", demandeurs)
    demandeurs = demandeurs.replace(u"des\nPr", "des Pr")
    demandeurs = [clean_demandeur(d) for d in demandeurs.split("\n")]
    return [d for d in demandeurs if d]

def parse_scrutins(legislature, data):
    groupes = ref_groupes(legislature, ND_names=True)
    histo_groupes = ref_histo_groupes(legislature, ND_names=True)
    seances = ref_seances(legislature)

    if not os.path.exists(SCRUTINS_DIR):
        os.makedirs(SCRUTINS_DIR)

    for item in sorted(data["scrutins"]["scrutin"], key=lambda s: int(s["numero"])):
        scrutin, logs = parse_scrutin(item, seances, groupes, histo_groupes)

        numero = "%05d" % scrutin["numero"]
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
            [log(l) for l in logs]
            with open(hash_file, "w") as f:
                f.write(sha1)
            with open(json_file, "w") as f:
                f.write(json_data)
            log("Scrutin %s mis à jour" % scrutin["numero"])

ERREURS_AN = {
   "120": "20172002", # instead of 20172001
   "121": "20172002", # instead of 20172001
   "334": "20180086", # instead of 20180085
   "335": "20180086", # instead of 20180085
   "336": "20180086", # instead of 20180085
   "337": "20180086", # instead of 20180085
   "361": "20180121", # instead of 20180120
   "438": "20180174", # instead of 20180173
   "1704": "20190153", # instead of 20190152
   "1705": "20190153", # instead of 20190152
   "1706": "20190153", # instead of 20190152
   "1840": "20190218", # instead of 20190217
   "2104": "20200003", # instead of null
   "2105": "20200003", # instead of null
   "2339": "20200092", # instead of null
   "2340": "20200092", # instead of null
   "2341": "20200092", # instead of null
   "2342": "20200092", # instead of null
   "2446": "20200140", # instead of 20200141
   "2447": "20200140", # instead of 20200141
}

MISSING_HISTOGPES = {
    "PA721632": "UAI",
    "PA719664": "LREM",
    "PA722090": "LREM",
    "PA342601": "UAI"
}

def parse_scrutin(data, seances, groupes, histo_groupes):
    logs = []
    synthese = data["syntheseVote"]
    decompte = synthese["decompte"]
    try:
        seance = seances[data["seanceRef"]]
    except KeyError:
        print("ERROR parsing scrutin #%s: seance Ref %s missing in AN's reunions OpenData" % (data["numero"], data["seanceRef"]))
        exit(1)
    scrutin = {
        "numero": int(data["numero"]),
        "seance": seance,
        "date": data["dateScrutin"],
        "titre": data["titre"],
        "type": TYPES[data["typeVote"]["codeTypeVote"]],
        "nombre_votants": int(synthese["nombreVotants"]),
        "nombre_pours": int(decompte["pour"]),
        "nombre_contres": int(decompte["contre"]),
        "nombre_abstentions": int(decompte["abstentions"]),
        "sort": data["sort"]["code"],
        "demandeurs": clean_demandeurs(data["demandeur"]["texte"], data["numero"]),
        "parlementaires": {},
    }
    if data["numero"] in ERREURS_AN:
        scrutin["seance"] = ERREURS_AN[data["numero"]]
    # Temp fix
    # elif scrutin["numero"] >= 2446 and scrutin["seance"] and scrutin["seance"].startswith("2020"):
    #     scrutin["seance"] = str(int(scrutin["seance"]) - 1)
    if not scrutin["seance"]:
        logs.append("WARNING: scrutin %s has no seance %s" % (data["numero"], data["seanceRef"]))
    if not scrutin["demandeurs"]:
        logs.append("WARNING: scrutin %s has no demandeurs %s" % (data["numero"], data["demandeur"]))

    delegations = 0

    positions_groupes = {}
    vote_groupes = data["ventilationVotes"]["organe"]["groupes"]["groupe"]
    for vote_groupe in vote_groupes:
        acro_groupe = groupes[vote_groupe["organeRef"]]
        dn = vote_groupe["vote"]["decompteNominatif"]
        position_groupe = vote_groupe["vote"]["positionMajoritaire"]
        positions_groupes[acro_groupe] = position_groupe

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
                    "mise_au_point_position": None
                }

    scrutin["nb_delegations"] = delegations
    if 2 * delegations > scrutin["nombre_votants"]:
        logs.append("Scrutin %s: trop de délégations" % scrutin["numero"])

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
                        groupe = None
                        histo = histo_groupes.get(votant["acteurRef"], None)
                        for h in histo or []:
                            if h["debut"] <= scrutin["date"] <= (h["fin"] or "9999-99-99"):
                                groupe = h["sigle"]
                                break
                        if not groupe:
                            logs.append("WARNING: no groupe historique found for parl %s for date %s: %s" % (votant["acteurRef"], scrutin["date"], histo))
                            if votant["acteurRef"] in MISSING_HISTOGPES:
                                groupe = MISSING_HISTOGPES[votant["acteurRef"]]
                                logs.append("  ->  hardfixing it to %s" % groupe)
                        scrutin["parlementaires"][votant["acteurRef"]] = {
                            "position": None,
                            "groupe": groupe,
                            "position_groupe": positions_groupes.get(groupe),
                            "par_delegation": None
                        }
                    parl = scrutin["parlementaires"][votant["acteurRef"]]
                    parl["mise_au_point_position"] = position
                    if position == parl["position"]:
                        logs.append("WARNING: position and mise_au_point are identical for parl %s on scrutin %s: %s" % (votant["acteurRef"], scrutin["numero"], position))

    return scrutin, logs


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Utilisation: %s <legislature>" % sys.argv[0])
        sys.exit(1)

    legislature = sys.argv[1]
    data, updated = fetch_an_json(legislature, "scrutins")
    parse_scrutins(legislature, data)
