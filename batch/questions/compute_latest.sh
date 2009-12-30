#!/bin/bash

if [[ $1 != "liste_sans_reponse_recent.sql" && $1 != "liste_sans_reponse.sql" ]]; then
  echo "usage: compute_latest.sh liste_sans_reponse_recent.sql / liste_sans_reponse.sql"
  exit 1
fi

. ../../bin/db.inc

cat dernier_numero.sql | mysql $MYSQLID $DBNAME | grep -v numero > dernier_numero.txt
cat $1 | mysql $MYSQLID $DBNAME | grep -v source > liste_sans_reponse.txt

rm -f html/*

#log cette partie très verbeuse
perl download_questions.pl > /tmp/download_questions.log

for file in `grep -L "The page cannot be found" html/*`; do
	fileout=$(echo $file | sed 's/html/json/' | sed 's/\.htm/\.xml/')
	perl cut_quest.pl $file > $fileout
done;

