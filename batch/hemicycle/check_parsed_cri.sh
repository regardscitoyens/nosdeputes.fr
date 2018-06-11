#!/bin/bash

JSON=$1

source ../../bin/db-external.inc
DATE=$(head -1 $JSON            |
       sed 's/^.*"date": "//'   |
       sed 's/".*$//')
DEPUTES=$(echo "SELECT nom from parlementaire
                WHERE fin_mandat IS NULL
                   OR fin_mandat > '$DATE'"     |
          mysql $MYSQLID $DBNAME                |
          grep -v '^nom'                        |
          sed 's/\W/./g'                        |
          tr '\n' '|'                           |
          sed 's/|$//')

echo "Didascalies :"
echo "-------------"
grep '"intervenant": ""' $JSON      |
  sed 's/^.*"intervention": "/-> /' |
  sed 's/".*$//'                    |
  sort -u
echo "-------------"
echo
echo

echo "Sommaire :"
echo "-------------"
cat $JSON                   |
  sed 's/^.*"contexte": "//'|
  sed 's/".*$//'            |
  grep . | uniq
echo "-------------"
echo
echo

echo "Parenthèses :"
echo "-------------"
grep '(' $JSON                          |
  sed 's/^.*"contexte": "//'            |
  sed 's/",.*"intervention": "/  |  /'  |
  sed 's/".*$//'                        |
  grep -v '(.*  |  [^(]*$'
echo "-------------"
echo
echo

echo "Meme interv :"
interv="START"
cat $JSON | while read line; do
  newinterv=$(echo $line            |
    sed 's/^.*"intervenant": "//'   |
    sed 's/",.*"fonction": "/, /'   |
    sed 's/".*$//'
  )
  newtext=$(echo $line              |
    sed 's/^.*"intervention": "//'  |
    sed 's/".*$//'
  )
  if [ "$interv" = "$newinterv" ] && [ ! -z "$interv" ]; then
    echo "-------------"
    echo "$interv: $text"
    echo "$newinterv: $newtext"
  fi
  interv=$newinterv
  text=$newtext
done
echo "-------------"
echo
echo

echo "Intervenants:"
echo "-------------"
echo " - Députés:"
grep -v '"intervenant": ""' $JSON   |
  sed 's/^.*"intervenant": "//'     |
  sed 's/",.*"fonction": "/\t\t|  /'|
  sed 's/".*$//'                    |
  sort | uniq -c                    |
  grep -iP "$DEPUTES"
echo "-------------"
echo " - Autres:"
grep -v '"intervenant": ""' $JSON   |
  sed 's/^.*"intervenant": "//'     |
  sed 's/",.*"fonction": "/\t\t|  /'|
  sed 's/".*$//'                    |
  sort | uniq -c                    |
  grep -viP "$DEPUTES"
echo "-------------"
echo
echo

head -1 $JSON                       |
  sed 's/^.*"date": "/\nDATE:    /' |
  sed 's/", .*"heure": "/ - /'      |
  sed 's/".*$//'

head -1 $JSON                       |
  sed 's/^.*"source": "/SOURCE:  /' |
  sed 's/[#"].*$//'
