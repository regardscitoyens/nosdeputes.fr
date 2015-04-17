#!/bin/bash

loi=$1

id_dossier=$(curl -sL "http://www2.assemblee-nationale.fr/recherche/amendements" |
  grep "<option.*Texte.* $loi[,)]" |
  sed -r 's/^.*value="([0-9]+)".*$/\1/')

if ! echo "$id_dossier" | grep -P "^\d+$" > /dev/null; then
  echo "Cannot find a dossier id for loi $loi in http://www2.assemblee-nationale.fr/recherche/amendements"
  exit 1
fi

curl -sL "http://www2.assemblee-nationale.fr/recherche/query_amendements?typeDocument=amendement&idDossierLegislatif=$id_dossier&typeRes=facettes" |
  grep "examen" |
  sed 's/{/\n/g' |
  grep "[lL]ect.* - $loi -" |
  sed -r 's/^.*val":"([0-9]+)".*$/\1/' |
  while read id_examen; do
    echo "$id_dossier;$id_examen"
  done 
