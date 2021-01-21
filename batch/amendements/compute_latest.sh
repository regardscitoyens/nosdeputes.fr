#!/bin/bash

. ../../bin/db.inc

mkdir -p html json loaded
if [ -d html ]; then
  find html -type f | xargs rm -f
fi

echo 'SELECT source FROM amendement WHERE sort LIKE "Ind%" AND date > DATE_SUB(CURDATE() , INTERVAL 1 YEAR)' |
 mysql $MYSQLID $DBNAME |
 grep -v "/15/amendements/2623/" |
 grep -v source > liste_sort_indefini.txt

python download_amendements.py $LEGISLATURE $1 > /tmp/download_amendements.log
python download_amendements_indefinis.py liste_sort_indefini.txt >> /tmp/download_amendements.log

for file in `ls html`; do
  fileout=$(echo $file | sed 's/html/json/' | sed 's/\.asp/\.xml/')
  python parse_amendement.py html/$file > json/$fileout
  if test -e loaded/$fileout && ! diff {json,loaded}/$fileout | grep . > /dev/null; then
    rm -f json/$fileout
  fi
done

