#!/bin/bash

. ../../bin/db.inc

cat dernier_numero.sql | mysql $MYSQLID $DBNAME | grep -v numero > dernier_numero.txt
cat liste_sans_reponse.sql | mysql $MYSQLID $DBNAME | grep -v source > liste_sans_reponse.txt

rm -f html/*

#log cette partie très verbeuse
perl download_questions.pl > /tmp/download_questions.log

for file in `grep -L "The page cannot be found" html/*`; do
	fileout=$(echo $file | sed 's/html/json/' | sed 's/\.htm/\.xml/')
	perl cut_quest.pl $file > $fileout
done;

