#!/bin/bash

loi=$1

if test -z "$loi"; then
  echo "Please input a proper loi"
  exit 1
fi

. ../../bin/db.inc

echo 'SELECT source FROM amendement WHERE sort NOT LIKE "Ind%" AND sort NOT LIKE "Rect%" AND texteloi_id = '$loi | mysql $MYSQLID $DBNAME | grep -v source > "$loi.done"

rm -rf "html-$loi"

mkdir -p "html-$loi"

echo > /tmp/download_amendements.log
count=0
bash get_ids_loi.sh "$loi" | while read line; do
  id_dossier=$(echo $line | awk -F ";" '{print $1}')
  id_examen=$(echo $line | awk -F ";" '{print $2}')
  curl -sL "http://www2.assemblee-nationale.fr/recherche/query_amendements?typeDocument=amendement&idExamen=$id_examen&idDossierLegislatif=$id_dossier&rows=100000&tri=ordreTexteasc&typeRes=liste" |
    python -m json.tool 2> /dev/null |
    grep -P "http://\S+/amendements/" |
    awk -F "|" '{print $7}' |
    while read url; do
      if ! grep "$url" "$loi.done" > /dev/null; then 
        count=$(($count + 1))
        perl download_one.pl "$url" "html-$loi" >> /tmp/download_amendements.log
        if [ $count -gt 20 ]; then
          break;
        fi
      fi
    done
done

for file in `ls html-$loi`; do 
	fileout=$(echo $file | sed 's|html-[^/]*|json|' | sed 's/\.asp/\.xml/')
	perl cut_amdmt.pl "html-$loi/$file" > json/$fileout
done;

