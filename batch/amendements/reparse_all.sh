#!/bin/bash

. ../../bin/db.inc

mkdir -p html json loaded
rm -f html/*

echo 'SELECT distinct(source) FROM amendement ORDER BY source' | mysql $MYSQLID $DBNAME | grep -v source | while read url; do
  perl download_one.pl $url
done

for file in `ls html`; do
  fileout=$(echo $file | sed 's/html/json/' | sed 's/\.asp/\.xml/')
  perl cut_amdmt.pl html/$file | python clean_subjects_amdmts.py > json/$fileout
  if test -e loaded/$fileout && ! diff {json,loaded}/$fileout | grep . > /dev/null; then
    rm -f json/$fileout
  fi
done

