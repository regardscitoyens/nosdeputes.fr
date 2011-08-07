#!/bin/bash

. ../../bin/db.inc

for url in `echo "SELECT source FROM texteloi WHERE date > DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND type = 'Proposition de loi' ORDER BY numero"  | mysql $MYSQLID $DBNAME | grep -v source`; do
  file=`echo $url | sed 's/\//_/g'`
  perl download_one.pl $url
  perl parse_metas.pl html/$file > out/$file
  mv html/$file ppl/$file
done;


