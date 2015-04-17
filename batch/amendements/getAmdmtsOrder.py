#!/usr/bin/env python

import requests, json, re, sys

if len(sys.argv) != 3:
    print "USAGE: python getAmdmtsOrder.py <ID_DOSSIER> <ID_EXAMEN>"
    exit(1)

id_dossier = sys.argv[1]
id_examen = sys.argv[2]

url = "http://www2.assemblee-nationale.fr/recherche/query_amendements?typeDocument=amendement&idExamen=%s&idDossierLegislatif=%s&numAmend=&idAuteur=&idArticle=&idAlinea=&sort=&dateDebut=&dateFin=&periodeParlementaire=&texteRecherche=&rows=2500&format=html&tri=ordreTexteasc&typeRes=liste&start=" % (id_examen, id_dossier)

re_clean_num = re.compile(r"\D")
count = 0
with open ("liasse_order.tmp", "w") as f:
    for line in requests.get(url).json()["data_table"]:
        print >> f, re_clean_num.sub("", line.split("|")[5])
        count += 1

print "%s amendements" % count

