#!/bin/bash

cd $(dirname $0)
source ../../bin/db.inc
ANroot="http://www.assemblee-nationale.fr"

function download {
  echo "- $1" 1>&2
  curl -sL "$1"
}

echo "Downloading Débats from OpenData AN..."
rm -f xml
wget -q https://data.assemblee-nationale.fr/static/openData/repository/$LEGISLATURE/vp/syceronbrut/syseron.xml.zip -O SyceronBrut.xml.zip
unzip SyceronBrut.xml.zip > /dev/null
echo "Extracting list of Séances from OpenData AN..."
cat xml/compteRendu/*                                               |
  grep '<dateSeance>'                                               |
  sed -r 's/^.*<dateSeance>(....)(..)(..)(..)(..).*$/\1-\2-\3 \4:\5/'  |
  sort -n > all_seances_dates_opendataAN
rm -f SyceronBrut.xml*


echo "Downloading list of Séances from AN website..."
rm -f all_seances_searchAN.tmp
# Recherche de "de" filtrée sur les Compte-rendus depuis https://www2.assemblee-nationale.fr/recherche/resultats_recherche
CRURL="https://www2.assemblee-nationale.fr/recherche/resultats_recherche/(tri)/date/(legislature)/15/(query)/eyJxIjoidHlwZURvY3VtZW50OlwiY29tcHRlIHJlbmR1XCIgYW5kIGNvbnRlbnU6ZGUiLCJyb3dzIjoxMCwic3RhcnQiOjAsInd0IjoicGhwIiwiaGwiOiJmYWxzZSIsImZsIjoic2NvcmUsdXJsLHRpdHJlLHVybERvc3NpZXJMZWdpc2xhdGlmLHRpdHJlRG9zc2llckxlZ2lzbGF0aWYsdGV4dGVRdWVzdGlvbix0eXBlRG9jdW1lbnQsc3NUeXBlRG9jdW1lbnQscnVicmlxdWUsdGV0ZUFuYWx5c2UsbW90c0NsZXMsYXV0ZXVyLGRhdGVEZXBvdCxzaWduYXRhaXJlc0FtZW5kZW1lbnQsZGVzaWduYXRpb25BcnRpY2xlLHNvbW1haXJlLHNvcnQifQ=="
while [ ! -z "$CRURL" ]; do
  download "$CRURL" > all_seances_searchAN_list.tmp
  cat all_seances_searchAN_list.tmp |
    tr "\t\n" " "               |
    sed 's/<d/\n<d/g'           |
    grep 'Accédez au document'  |
    sed 's/^.*https\?/http/'    |
    sed 's/\s*">.*$//'          |
    grep '/cri/\(20\|NaN\)'     |
    sed 's|dyn/comptes-rendus/seance/redirect?url=/||'    |
    grep -v '/NaN' >> all_seances_searchAN.tmp
  CRURL=$(grep '"text">Suivant' all_seances_searchAN_list.tmp |
            head -1                                       |
            sed -r 's|^.*href="([^"]+)".*$|https://www2.assemblee-nationale.fr\1|')
done
sort -u all_seances_searchAN.tmp > all_seances_searchAN
rm -f all_seances_searchAN*.tmp


echo "Extracting list of Séances from NosDéputés..."
echo 'SELECT s.date, s.moment, i.source
      FROM seance s
      LEFT JOIN intervention i ON i.seance_id = s.id
      WHERE s.type = "hemicycle"
      GROUP BY s.date, s.moment
      ORDER BY s.date, s.moment'    |
  mysql $MYSQLID $DBNAME            |
  grep -v "^date" > all_seances_ND.tmp
awk -F "\t" '{print $1" "$2}' all_seances_ND.tmp > all_seances_dates_ND
awk -F "\t" '{print $3}' all_seances_ND.tmp |
  sed 's/#.*$//'                            |
  sort > all_seances_ND

echo "Analysing diff..."
extra=$(diff all_seances_searchAN all_seances_ND | grep "^>" | wc -l)
if [ $extra -gt 0 ]; then
  echo "- NosDéputés has $extra Séances not found on AN's website (how is that possible?):"
  diff all_seances_searchAN all_seances_ND  |
    grep "^>"                               |
    sed 's/^> //'
  echo
fi

missing=$(diff all_seances_searchAN all_seances_ND | grep "^<" | wc -l)
if [ $missing -gt 0 ]; then
  echo "There are $missing Séances missing, reloading them:"
  diff all_seances_searchAN all_seances_ND      |
    grep "^<"                                   |
    sed 's/^< //'                               |
    while read SEurl; do
      SEfile=$(echo "$SEurl" | sed 's|/|_|g')
      echo $SEurl
      #perl download_one.pl "$SEurl" 2>/dev/null && perl parse_hemicycle.pl "html/$SEfile" > "out/$SEfile"
    done
  echo 'All missing Séances reloaded and parsed, run "php symfony load:Hemicycle" to complete'
fi

echo 'You should run "vimdiff all_seances_dates_ND all_seances_dates_opendataAN" to investigate possible more fixes'

rm -f all_seances_*
