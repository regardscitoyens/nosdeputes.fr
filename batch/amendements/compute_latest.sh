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

python download_amendements.py $LEGISLATURE > /tmp/download_amendements.log
python download_amendements_indefinis.py >> /tmp/download_amendements.log

for file in `ls html`; do
  fileout=$(echo $file | sed 's/html/json/' | sed 's/\.asp/\.xml/')
  perl cut_amdmt.pl html/$file | python clean_subjects_amdmts.py > json/$fileout
  if test -e loaded/$fileout && ! diff {json,loaded}/$fileout | grep . > /dev/null; then
    rm -f json/$fileout
  fi
done

