#!/bin/bash

MYSQLID="-u cpc -pM_O_T__D_E__P_A_S_S_E"
DBNAME="cpc"
cat dernier_numero.sql | mysql $MYSQLID $DBNAME | grep -v numero > dernier_numero.txt
cat liste_sans_reponse.sql | mysql $MYSQLID $DBNAME | grep -v source > liste_sans_reponse.txt
rm -f html/*
perl download_questions.pl
rm -f json/*
for for file in `grep -L "The page cannot be found" html/*`; do
	fileout=$(echo $file | sed 's/html/json/' | sed 's/\.htm/\.xml/')
	perl cut_quest.pl $file > $fileout
done;

