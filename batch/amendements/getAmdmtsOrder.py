#!/usr/bin/env python

import requests, json, re

loi = 2173

id_dossier = 33299
id_examen = 4073

url = "http://www2.assemblee-nationale.fr/recherche/query_amendements?typeDocument=amendement&idExamen=%s&idDossierLegislatif=%s&numAmend=&idAuteur=&idArticle=&idAlinea=&sort=&dateDebut=&dateFin=&periodeParlementaire=&texteRecherche=&rows=2500&format=html&tri=ordreTexteasc&typeRes=liste&start=" % (id_examen, id_dossier)

re_clean_num = re.compile(r"\D")
count = 0
with open ("liasse_order.tmp", "w") as f:
    for line in requests.get(url).json()["data_table"]:
        print >> f, re_clean_num.sub("", line.split("|")[5])
        count += 1

print "%s amendements pour la loi %s" % (count, loi)

