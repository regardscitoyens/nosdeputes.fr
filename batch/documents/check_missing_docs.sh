#!/bin/bash

source ../../bin/db.inc

curl "https://www2.assemblee-nationale.fr/documents/liste/(ajax)/1/(limit)/1000/(type)/depots/(legis)/15/(no_margin)/false" > /tmp/alldocs_an

seq 10 | while read i; do
  i=$((i * 1000))
  curl "https://www2.assemblee-nationale.fr/documents/liste/(ajax)/1/(offset)/$i/(limit)/1000/(type)/depots/(legis)/15/(no_margin)/false" >> /tmp/alldocs_an
done

grep 'data-id=\|<h3>.*N°&\|</i> Document</a>' /tmp/alldocs_an |
 tr "\n" " "                                                  |
 sed 's/<\/a>/\n/g'                                           |
 sed 's/^.*<li data-id="OMC_//'                               |
 sed 's/">.*N°&nbsp;/ ; /'                                    |
 sed 's/<\/h3.*href="/ ; /'                                   |
 sed 's/">.*$//' > /tmp/alldocs_an.csv

cat /tmp/alldocs_an.csv | awk '{print $3}' | sort -un > /tmp/alldocs_an.nums

echo "select numero from texteloi order by numero" | mysql $MYSQLID $DBNAME | grep -v numero | sort -un > /tmp/alldocs_nd.nums
diff /tmp/alldocs_nd.nums /tmp/alldocs_an.nums | grep '>' | sed 's/> //' > /tmp/missingdocs.nums

cat /tmp/missingdocs.nums | while read i; do
  grep "; $i ;" /tmp/alldocs_an.csv
done | grep -v "^ALCNANR5" | grep -v "^MESSANR5" | while read line; do
  echo "Downloading missing doc $line"
  id=$(echo $line | awk '{print $1}')
  bash compute_one_from_id_opendata.sh $id
done


