#!/bin/bash

source ../../bin/db.inc
nodownload=$1

mkdir -p opendata
cd opendata

find . -name Dossiers_Legislatifs_$LEGISLATURE.json.zip -type f -atime +1 -delete > /dev/null

if ! test -f Dossiers_Legislatifs_$LEGISLATURE.json.zip ; then

  if ! test "$nodownload"; then
    wget -q -O Dossiers_Legislatifs_$LEGISLATURE.json.zip -N http://data.assemblee-nationale.fr/static/openData/repository/$LEGISLATURE/loi/dossiers_legislatifs/Dossiers_Legislatifs.json.zip
    wget -q -O AMO20_dep_sen_min_tous_mandats_et_organes_$LEGISLATURE.json.zip -N http://data.assemblee-nationale.fr/static/openData/repository/$LEGISLATURE/amo/deputes_senateurs_ministres_legislature/AMO20_dep_sen_min_tous_mandats_et_organes.json.zip
  fi
  
  unzip -q Dossiers_Legislatifs_$LEGISLATURE.json.zip
  unzip -q AMO20_dep_sen_min_tous_mandats_et_organes_$LEGISLATURE.json.zip
  
  mkdir -p document dossierParlementaire
  rsync -a json/document/ document
  rsync -a json/dossierParlementaire/ dossierParlementaire
  rsync -a json/organe/ organe
  rsync -a json/acteur/ acteur
  
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
                    wget -q -N -O $docid".html" "https://www.assemblee-nationale.fr/dyn/opendata/"$docid".html"
                fi
            done
        fi
    done
done
find . -type f -size 0 -delete
