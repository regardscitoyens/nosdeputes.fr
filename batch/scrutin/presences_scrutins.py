#!/usr/bin/env python
# -*- coding: utf8 -*-
from __future__ import print_function, unicode_literals, absolute_import

import os
import sys

# Faux module dans .. (batch/)
sys.path.append(os.path.join(os.path.dirname(__file__), ".."))

from common.opendata import (
    fetch_an_json,
    ref_deputes,
    ref_groupes,
    ref_seances,
)  # noqa


def parse_scrutins(legislature, json):
    deputes = ref_deputes(legislature)
    groupes = ref_groupes(legislature)
    seances = ref_seances(legislature)

    for scrutin in json["scrutins"]["scrutin"]:
        parse_scrutin(scrutin, seances, deputes, groupes)


def parse_scrutin(scrutin, seances, deputes, groupes):
    num_scrutin = scrutin["numero"]
    vote_groupes = scrutin["ventilationVotes"]["organe"]["groupes"]["groupe"]

    seance_ref = seances[scrutin["seanceRef"]]
    titre = scrutin["titre"]

    for vote_groupe in vote_groupes:
        nom_groupe = groupes[vote_groupe["organeRef"]]
        dn = vote_groupe["vote"]["decompteNominatif"]
        for position in ("pour", "contre", "abstention", "nonVotant"):
            votants_position = dn.get("%ss" % position, dn.get(position))
            if not votants_position:
                continue
            votants = votants_position["votant"]

            if not isinstance(votants, list):
                votants = [votants]

            for votant in votants:
                try:
                    nom_depute = deputes[votant["acteurRef"]]
                except KeyError:
                    continue

                print(
                    "%s,%s,%s,%s,%s,%s"
                    % (
                        num_scrutin,
                        titre,
                        seance_ref,
                        nom_depute,
                        nom_groupe,
                        position,
                    )
                )


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Utilisation: %s <legislature>" % sys.argv[0])
        sys.exit(1)

    legislature = sys.argv[1]
    json, updated = fetch_an_json(legislature, "scrutins")
    parse_scrutins(legislature, json)
