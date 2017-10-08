#!/bin/bash

. ../../bin/db.inc
mkdir -p html

for url in `echo "SELECT source FROM texteloi WHERE date > DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND type LIKE 'Proposition%' ORDER BY numero"  | mysql $MYSQLID $DBNAME | grep -v source`; do
  file=`echo $url | sed 's/\//_/g'`
  perl download_one.pl $url
  if ! test -e ppl/$file || diff html/$file ppl/$file | grep . > /dev/null; then
    perl parse_metas.pl html/$file > out/$file
    mv html/$file ppl/$file
  else
    rm -f html/$file
  fi
done;

