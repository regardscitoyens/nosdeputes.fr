#!/bin/bash

cd $(dirname $0)
source ../../bin/db.inc
ANroot="http://www.assemblee-nationale.fr"

function download {
  echo "- $1" 1>&2
  curl -sL "$1"
}

echo "Downloading Débats from OpenData AN..."
rm -f SyceronBrut.xml*
wget -q http://data.assemblee-nationale.fr/static/openData/repository/VP/syceronbrut/SyceronBrut.xml.zip -O SyceronBrut.xml.zip
unzip SyceronBrut.xml.zip > /dev/null
echo "Extracting list of Séances from OpenData AN..."
less SyceronBrut.xml                                                |
  sed -r "s/(<DateSeance>)/\n\1/g"                                  |
  grep '<DateSeance>'                                               |
  sed -r 's/<DateSeance>(....)(..)(..)(..)(..).*$/\1-\2-\3 \4:\5/'  |
  sort -n > all_seances_dates_opendataAN

rm -f SyceronBrut.xml*

echo "Downloading list of Séances from AN website..."
download "http://www.assemblee-nationale.fr/$LEGISLATURE/debats/" > all_seances_lists_AN.tmp
grep '/'$LEGISLATURE'/cri/[^\"]\+\.asp\"' all_seances_lists_AN.tmp    |
  sed 's|^.*href="|'"$ANroot"'|'                                        |
  sed 's/\.asp".*$/.asp/' > all_seances_urls_AN.tmp
grep '/'$LEGISLATURE'/cri/[^\"\.]\+\"' all_seances_lists_AN.tmp |
  sed 's|^.*href="|'"$ANroot"'|'                                |
  sed 's|/".*$|/|'                                              |
  while read listUrl; do
    download "$listUrl" | grep 'class="seance".*href="[^"]\+\.asp"' |
      iconv -f iso-8859-15 -t utf-8                                 |
      sed 's|^.*href="|'"$listUrl"'|'                               |
      sed 's/\.asp".*$/.asp/' >> all_seances_urls_AN.tmp
  done
sort -u all_seances_urls_AN.tmp > all_seances_urls_AN

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
  sort > all_seances_urls_ND

echo "Analysing diff..."
extra=$(diff all_seances_urls_AN all_seances_urls_ND | grep "^>" | wc -l)
if [ $extra -gt 0 ]; then
  echo "- NosDéputés has $extra Séances not found on AN's website (how is that possible?):"
  diff all_seances_urls_AN all_seances_urls_ND  |
    grep "^>"                                   |
    sed 's/^> //'
  echo
fi

missing=$(diff all_seances_urls_AN all_seances_urls_ND | grep "^<" | wc -l)
if [ $missing -gt 0 ]; then
  echo "There are $missing Séances missing, reloading them:"
  diff all_seances_urls_AN all_seances_urls_ND  |
    grep "^<"                                   |
    sed 's/^< //'                               |
    while read SEurl; do
      SEfile=$(echo "$SEurl" | sed 's|/|_|g')
      perl download_one.pl "$SEurl" 2>/dev/null && perl parse_hemicycle.pl "html/$SEfile" > "out/$SEfile"
    done
  echo 'All missing Séances reloaded and parsed, run "php symfony load:Hemicycle" to complete'
fi

echo 'You should run "vimdiff all_seances_dates_ND all_seances_dates_opendataAN" to investigate possible more fixes'

rm -f all_seances_*
