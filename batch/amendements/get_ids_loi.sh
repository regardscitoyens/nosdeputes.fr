#!/bin/bash

. ../../bin/db.inc

loi=$1

id_dossier=$(curl -sL "http://www.assemblee-nationale.fr/dyn/15/amendements" |
  grep "<option.*DLR.L..N.*$loi" |
  sed -r 's/^.*value="(\w*?)".*$/\1/')

if ! echo "$id_dossier" > /dev/null; then
  echo "Cannot find a dossier id for loi $loi in http://www2.assemblee-nationale.fr/recherche/amendements"
  exit 1
fi

>&2 echo "id dossier: $id_dossier"

curl -sL "http://www.assemblee-nationale.fr/dyn/api/amendements/facets?dossier_legislatif=$id_dossier" | jq . |
  grep "EXAN" |
  grep "[lL]ect.* - $loi " |
  sed -r 's/^.*?"(.*?)":.*$/\1/' |
  while read id_examen; do
    echo "$id_dossier;$id_examen"
  done 
