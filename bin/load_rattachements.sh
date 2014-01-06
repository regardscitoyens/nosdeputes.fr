#!/bin/bash

source bin/db.inc

curl -sL "https://raw.github.com/regardscitoyens/rattachement-financier-parlementaires/master/data/1312-AN-rattachement-2014.csv" > rattachement-financier.csv

cat rattachement-financier.csv  |
  grep -v '^"id","'             |
  while read line; do
    slug=$(echo $line | awk -F '","' '{print $7}')
    parti=$(echo $line | awk -F '","' '{print $4}' | sed "s/'/\\'/g")
    if [ -z "$slug" ]; then
      echo "-> WARNING No slug found in line $line"
      continue
    elif [ -z "$parti" ]; then
      echo "-> WARNING No parti found in line $line"
      continue
    else
      echo "-> saving in DB parti '$parti' to '$slug'"
      echo "UPDATE parlementaire SET parti = \"$parti\" WHERE slug = '$slug'" | mysql $MYSQLID $DBNAME --default-character-set=utf8
    fi
  done

