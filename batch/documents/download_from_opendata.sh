#!/bin/bash

mkdir -p opendata
cd opendata
if find . -name Dossiers_Legislatifs_XV.json.zip -type f -atime +1 > /dev/null ; then
wget -q -O Dossiers_Legislatifs_XV.json.zip  -N http://data.assemblee-nationale.fr/static/openData/repository/15/loi/dossiers_legislatifs/Dossiers_Legislatifs_XV.json.zip
unzip -q Dossiers_Legislatifs_XV.json.zip
mkdir -p document dossierParlementaire
rsync -a json/document/  document
rsync -a json/dossierParlementaire/ dossierParlementaire
rm -rf json
fi
mkdir -p html
cd html
for rap in AVISANR5 ETDIANR5 PIONANR5 PNREANR5 PRJLANR5 RAPPANR5 RINFANR5 ; do
    ls "../document/" | grep $rap | while read doc ; do
        docid=$(echo $doc | sed 's/.json//')
        if ! test -f $docid".html"; then
            cat "../document/"$doc | jq . | grep '"uid"' | awk -F '"' '{print $4}' | sort -r | while read docid; do
                if ! test -s $docid".html"; then
                    wget -q -N -O $docid".html" "http://www.assemblee-nationale.fr/dyn/opendata/"$docid".html"
                fi
            done
        fi
    done
done
find . -type f -size 0 -delete
