#!/bin/bash

cd $(dirname $0)
source ../../bin/db.inc

function download {
  echo "- $1" 1>&2
  curl -sL "$1"
}

echo "Downloading list of Propositions from diverse AN sources..."
PPLURL="http://www2.assemblee-nationale.fr/documents/liste/(ajax)/1/(limit)/10000/(type)/propositions-loi"
download "$PPLURL"                                  |
  sed 's|14/ta/ta0080/|14/propositions/pion0733/|'  |
  grep "documents/notice/$LEGISLATURE/pro"          |
  sed 's/^.*href="//'                               |
  sed 's/"><i.*$//'                                 |
  sed 's|/documents/notice/'"$LEGISLATURE"'/propositions/pion\([0-9]\+\)/(index)/propositions-loi|http://www.assemblee-nationale.fr/'"$LEGISLATURE"'/propositions/pion\1.asp|' |
  sort > all-ppls

download "http://www.assemblee-nationale.fr/$LEGISLATURE/documents/index-resolutions.asp" > all-pprs.tmp
download "http://www.assemblee-nationale.fr/$LEGISLATURE/documents/index-enquete-resolution.asp" >> all-pprs.tmp
download "http://www.assemblee-nationale.fr/$LEGISLATURE/documents/index-resol-reglement.asp" >> all-pprs.tmp
download "http://www.assemblee-nationale.fr/$LEGISLATURE/documents/index-resol-art34-1.asp" >> all-pprs.tmp
iconv -f iso-8859-15 -t utf-8 all-pprs.tmp                  |
  grep 'href="/'"$LEGISLATURE"'/\(propositions\|europe\)'   |
  sed 's|^.*href="|http://www.assemblee-nationale.fr|'      |
  sed 's/">[<n].*$//'                                       |
  sort > all-pprs
cat all-ppls all-pprs | sort > all-ppls-pprs-AN

echo "Extracting list of Propositions from NosDéputés..."
echo 'SELECT source FROM texteloi WHERE type LIKE "Proposition de %" ORDER BY source'   |
  mysql $MYSQLID $DBNAME                                                                |
  grep -v "^source" > all-ppls-pprs-ND

echo "Analysing diff..."
extra=$(diff all-ppls-pprs-AN all-ppls-pprs-ND | grep "^>" | wc -l)
if [ $extra -gt 0 ]; then
  echo "- NosDéputés has $extra Propositions not found in AN's lists (?):"
  diff all-ppls-pprs-AN all-ppls-pprs-ND    |
    grep "^>"                               |
    sed 's/^> //'
  echo
fi

missing=$(diff all-ppls-pprs-AN all-ppls-pprs-ND | grep "^<" | wc -l)
if [ $missing -gt 0 ]; then
  echo "There are $missing Propositions missing, reloading them:"
  diff all-ppls-pprs-AN all-ppls-pprs-ND    |
    grep "^<"                               |
    sed 's/^< //'                           |
    while read PPurl; do
      PPfile=$(echo $PPurl | sed 's|/|_|g')
      perl download_one.pl "$PPurl"
      perl parse_metas.pl html/$PPfile > out/$PPfile
    done
  echo 'All missing Propositions reloaded and parsed, run "while ! php symfony load:Documents; do sleep 0; done" to complete'
fi

rm -f all-pp{l,r}s*

