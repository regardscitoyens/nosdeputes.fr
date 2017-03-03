#!/bin/bash

cd $(dirname $0)
source ../../bin/db.inc

function download {
  echo "- $1" 1>&2
  curl -sL "$1"
}

echo "Downloading list of Compte-rendus from AN search engine..."
rm -f all_crs_searchAN.tmp
CRURL="http://www2.assemblee-nationale.fr/recherche/resultats_recherche/%28tri%29/date/%28query%29/YTo3OntzOjE6InEiO3M6NDY6InR5cGVEb2N1bWVudDoiY29tcHRlIHJlbmR1IiBhbmQgY29udGVudTpjb21wdGUiO3M6NDoicm93cyI7aToxMDA7czo1OiJzdGFydCI7aTowO3M6Mjoid3QiO3M6MzoicGhwIjtzOjI6ImhsIjtzOjU6ImZhbHNlIjtzOjI6ImZsIjtzOjE5NzoidXJsLHRpdHJlLHVybERvc3NpZXJMZWdpc2xhdGlmLHRpdHJlRG9zc2llckxlZ2lzbGF0aWYsdGV4dGVRdWVzdGlvbix0eXBlRG9jdW1lbnQsc3NUeXBlRG9jdW1lbnQscnVicmlxdWUsdGV0ZUFuYWx5c2UsbW90c0NsZXMsYXV0ZXVyLGRhdGVEZXBvdCxzaWduYXRhaXJlc0FtZW5kZW1lbnQsZGVzaWduYXRpb25BcnRpY2xlLHNvbW1haXJlLHNvcnQiO3M6NDoic29ydCI7czowOiIiO30="
while [ ! -z "$CRURL" ]; do
  download "$CRURL" > all_crs_searchAN_list.tmp
  grep '"Accédez au document" href' all_crs_searchAN_list.tmp   |
    sed -r 's/^.*href="([^"]+)".*$/\1/'                         |
    sed -r 's|\.fr//|.fr/|'                                     |
    grep -v '/cri/20' >> all_crs_searchAN.tmp
  CRURL=$(grep '"text">Suivant' all_crs_searchAN_list.tmp |
            head -1                                       |
            sed -r 's|^.*href="([^"]+)".*$|http://www2.assemblee-nationale.fr\1|')
done
sort -u all_crs_searchAN.tmp > all_crs_searchAN
rm -f all_crs_searchAN*.tmp

rm -f all_crs_listAN.tmp
function process {
  download "$1"                                                 |
    iconv -f "iso-8859-15" -t "utf-8"                           |
    grep 'href="[^"]*c[0-9\-]\+\.asp'                             |
    sed -r 's/^.*href="([^"#]*?c[0-9\-]+\.asp)(#[^"]*)?".*$/\1/' |
    sort -u                                                     |
    while read id; do
      if echo $id | grep '://' > /dev/null; then
        echo "$id" >> all_crs_listAN.tmp
      else
        echo "$1$id" >> all_crs_listAN.tmp
      fi
    done
}

echo "Downloading list of Compte-rendus from AN lists..."
STARTYEAR=$(($LEGISLATURE * 5 + 1941))
LASTYEAR=$(($STARTYEAR + 6))
for YEAR in $(seq $STARTYEAR $LASTYEAR); do
  SESSION=$(printf '%02d-%02d' $(($YEAR - 2001)) $(($YEAR - 2000)))
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-cedu/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-eco/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-cafe/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-soc/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-cdef/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-dvp/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-cfiab/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-cloi/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/budget/plf$YEAR/commissions_elargies/cr/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-mec/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-mecss/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-delf/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-dom/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-oecst/$SESSION/"
  process "http://www.assemblee-nationale.fr/$LEGISLATURE/cr-cec/$SESSION/"
done
process "http://www.assemblee-nationale.fr/$LEGISLATURE/europe/c-rendus/"
if [ $LEGISLATURE -eq 13 ]; then
  process "http://www.assemblee-nationale.fr/13/cr-micompetitivite/10-11/"
  process "http://www.assemblee-nationale.fr/13/cr-micompetitivite/11-12/"
  process "http://www.assemblee-nationale.fr/13/cr-mitoxicomanie/10-11/"
  process "http://www.assemblee-nationale.fr/13/cr-cegrippea/09-10/"
elif [ $LEGISLATURE -eq 14 ]; then
  process "http://www.assemblee-nationale.fr/14/cr-miazerb/16-17/"
  process "http://www.assemblee-nationale.fr/14/cr-mibrexit/16-17/"
  process "http://www.assemblee-nationale.fr/14/cr-mibrexit/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-micmacron/16-17/"
  process "http://www.assemblee-nationale.fr/14/cr-micmacron/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-miautofr/16-17/"
  process "http://www.assemblee-nationale.fr/14/cr-miautofr/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-mimdaesh/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-mitransen/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-miparitarisme/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-minouvcal/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-mibpifrance/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-mi-expo-univ/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-mi-expo-univ/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-misimplileg/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-misimplileg/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-micice/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-miecotaxe/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-micoutsprod/12-13/"
  process "http://www.assemblee-nationale.fr/14/cr-micoutsprod/11-12/"
  process "http://www.assemblee-nationale.fr/14/cr-mimage/12-13/"
  process "http://www.assemblee-nationale.fr/14/cr-mrengagmt/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-comnum/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-gtinstit/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-csegalite/16-17/"
  process "http://www.assemblee-nationale.fr/14/cr-csegalite/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-cscroissact/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-cstransenerg/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-cstransenerg/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-csprostit/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-csprostit/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-csprostit/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-cssimplif/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-csprogfinances/12-13/"
  process "http://www.assemblee-nationale.fr/14/cr-csprogfinances/11-12/"
  process "http://www.assemblee-nationale.fr/14/cr-cenum23/16-17/"
  process "http://www.assemblee-nationale.fr/14/cr-cenum23/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-cefibromyalgie/16-17/"
  process "http://www.assemblee-nationale.fr/14/cr-cefibromyalgie/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-ceabattage/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-cemoyter/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-cecip/15-16/"
  process "http://www.assemblee-nationale.fr/14/cr-cecip/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-cesurvfil/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-ceordrerep/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-ceelectricite/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-cediffmonass/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-cediffmonass/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-ceredutemtra/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-ceredutemtra/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-ceexilfvives/14-15/"
  process "http://www.assemblee-nationale.fr/14/cr-ceexilfvives/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-cefugy/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-cefugy/12-13/"
  process "http://www.assemblee-nationale.fr/14/cr-cenucleaire/13-14/"
  process "http://www.assemblee-nationale.fr/14/cr-cesncm/12-13/"
  process "http://www.assemblee-nationale.fr/14/cr-ceaffcahuzac/12-13/"
  process "http://www.assemblee-nationale.fr/14/cr-cesidmet/12-13/"                                                                                                                                                        
fi
sort -u all_crs_listAN.tmp > all_crs_listAN 
rm -f all_crs_listAN.tmp

cat all_crs_{list,search}AN |
  sort -u > all_crs_AN

echo "Extracting list of Compte-rendus from NosDéputés..."
echo 'SELECT source
      FROM intervention
      WHERE type = "commission"
      ORDER BY source'          |
  mysql $MYSQLID $DBNAME        |
  grep -v "^source"             |
  sed 's/#.*$//'                |
  sed -r 's|\.fr//|.fr/|'       |
  sed -r 's/^(.*)$/\L\1/'       |
  sort -u > all_crs_ND

echo "Analysing diff..."
extra=$(diff all_crs_AN all_crs_ND | grep "^>" | wc -l)
if [ $extra -gt 0 ]; then
  echo "- NosDéputés has $extra Compte-rendus not found in AN's lists (?):"
  diff all_crs_AN all_crs_ND    |
    grep "^>"                   |
    sed 's/^> //'
  echo
fi

missing=$(diff all_crs_AN all_crs_ND | grep "^<" | wc -l)
if [ $missing -gt 0 ]; then
  echo "There are $missing Compte-rendus missing, reloading them:"
  diff all_crs_AN all_crs_ND    |
    grep "^<"                   |
    sed 's/^< //'               |
    while read CRurl; do
      CRfile=$(echo $CRurl | sed 's|/|_|g')
      echo $CRurl
      #perl download_one.pl "$CRurl"
      #perl parse_presents.pl html/$CRfile > presents/$CRfile
      #perl parse_commission.pl html/$CRfile > out/$CRfile
    done
  echo 'All missing Compte-rendus reloaded and parsed, run to complete:'
  echo 'while find batch/commission/out -type f > /dev/null; do php symfony load:Commission; done'
  echo 'while find batch/commission/presents -type f > /dev/null; do php symfony load:JO --source=cri; done'
fi

#rm -f all_crs_*

