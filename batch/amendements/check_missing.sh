#!/bin/bash

cd $(dirname $0)
source ../../bin/db-external.inc || source ../../bin/db.inc
ANroot="http://www.assemblee-nationale.fr"

echo "Downloading Amendements from OpenData AN..."
rm -f Amendements_XIV.json*
wget -q http://data.assemblee-nationale.fr/static/openData/repository/LOI/amendements_legis/Amendements_XIV.json.zip -O Amendements_XIV.json.zip
unzip Amendements_XIV.json.zip > /dev/null
echo "Extracting list of Amendements from OpenData AN..."
cat Amendements_XIV.json                                    |
  sed -r 's/("documentURI": ")/\n\1/g'                      |
  grep '^"documentURI": "/'"$LEGISLATURE"'/'                |
  sed -r 's|^"documentURI": "([^"]*)".*$|'"$ANroot"'\1|'    |
  sed 's/\.pdf/\.asp/'                                      |
  sort -u > all_amdts_opendataAN.tmp

rm -f Amendements_XIV.json*

echo "Extracting list of Amendements from search engine AN..."
searchurl="http://www2.assemblee-nationale.fr/recherche/query_amendements?typeDocument=amendement&leg=$LEGISLATURE&idExamen=&idDossierLegislatif=&missionVisee=&numAmend=&idAuteur=&idArticle=&idAlinea=&sort=&dateDebut=&dateFin=&periodeParlementaire=&texteRecherche=&format=html&tri=ordreTexteasc&typeRes=liste&rows="
start=1
total=$(curl -sL "${searchurl}5"|
  grep '"nb_resultats"'         |
  sed 's/^.*:\s*//'             |
  sed 's/,\s*$//')
rm -f all_amdts_searchAN.tmp
while [ $start -lt $total ]; do
  curl -sL "${searchurl}1000&start=$start"      |
    grep '^\['                                  |
    sed 's/","/\n/g'                            |
    sed 's/^.*|http:/http:/'                    |
    sed 's/|.*$//'                              |
    sed 's|\\/|/|g' >> all_amdts_searchAN.tmp
  start=$(($start + 1000))
done

cat all_amdts_opendataAN.tmp all_amdts_searchAN.tmp | sort -u > all_amdts_AN.tmp

echo "Extracting list of Amendements from NosDéputés..."
echo 'SELECT source FROM amendement WHERE sort NOT LIKE "Rect%" ORDER BY source'    |
  mysql $MYSQLID $DBNAME                                                            |
  sed -r 's|(/T?A?[0-9]{4}[A-Z]?/)([0-9]+\.asp)|\1AN/\2|'                           |
  grep -v "cr-cfiab/12-13/c1213068"                                                 |
  grep -v "source"                                                                  |
  sort > all_amdts_nosdeputes.tmp

echo "Analysing diff..."
extra=$(diff all_amdts_AN.tmp all_amdts_nosdeputes.tmp | grep "^>" | wc -l)
if [ $extra -gt 0 ]; then
  echo "- NosDéputés has $extra Amendements not in AN's OpenData yet(?):"
  diff all_amdts_AN.tmp all_amdts_nosdeputes.tmp    |
    grep "^>"                                               |
    sed 's/^> //' > extra_amdmts_ND
    echo 'Full list available in "extra_amdmts_ND"'
  echo
fi

missing=$(diff all_amdts_AN.tmp all_amdts_nosdeputes.tmp | grep "^<" | wc -l)
if [ $missing -gt 0 ]; then
  echo "There are $missing Amendements missing, reloading them:"
  diff all_amdts_AN.tmp all_amdts_nosdeputes.tmp    |
    grep "^<"                                               |
    sed 's/^< //'                                           |
    while read AMurl; do
      AMfile=$(echo "$AMurl" | sed 's|/|_-_|g')
      perl download_one.pl "$AMurl" 2>/dev/null && ( perl cut_amdmt.pl "html/$AMfile" | python clean_subjects_amdmts.py > "json/$AMfile" ) || echo "ERROR: $AMurl missing from AN web"
      if grep '"sort": "", "auteurs": "", "parent": "", "serie": "", "expose": "", .*"date": "1970-01-01", "auteur_reel": "", "sujet": "", "texte": ""}' "json/$AMfile" > /dev/null; then
        echo "ERROR: $AMurl missing from AN web"
        rm -f "json/$AMfile"
      fi
    done
  AMdone=$(ls json | wc -l)
  echo
  echo "$(($missing - $AMdone)) missing amendements from AN's OpenData could not be found on AN's website"
  echo 'All '"$AMdone"' missing found Amendements reloaded and parsed into batch/amendements/json'
fi

rm -f all_amdts_*.tmp
