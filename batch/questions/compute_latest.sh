#!/bin/bash

if [ ! -d html ] ; then mkdir html; fi
if [ ! -d json ] ; then mkdir json; fi

if [[ $1 != "all" && $1 != "recent" ]]; then
  echo "usage: compute_latest.sh all/recent"
  exit 1
fi

rm -f html/*

source ../../bin/db.inc

if [[ $1 -eq "all" ]]; then
  sql_string='SELECT source FROM question WHERE (reponse = "" OR reponse IS NULL) AND motif_retrait IS NULL'
else
  sql_string='SELECT source FROM question WHERE question IS NULL OR ((reponse = "" OR reponse IS NULL) AND motif_retrait IS NULL AND date > DATE_SUB(CURDATE(), INTERVAL 75 DAY))'
fi
echo $sql_string | mysql $MYSQLID $DBNAME | grep -v source > liste_sans_reponse.txt

date_from=`echo "SELECT date FROM question ORDER BY date DESC limit 1" | mysql $MYSQLID $DBNAME | grep -v date`
perl download_questions_from_recherche.pl $date_from

for file in `grep -L "The page cannot be found" html/*`; do
  fileout=$(echo $file | sed 's/html/json/')
  perl parse_question.pl $file > $fileout
done;

