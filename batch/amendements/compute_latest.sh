#!/bin/bash

MYSQLID="-u cpc -pM_O_T__D_E__P_A_S_S_E"
DBNAME="cpc"
cat liste_sort_indefini.sql | mysql $MYSQLID $DBNAME | grep -v source > liste_sort_indefini.txt
rm -f html/*
perl download_amendements.pl > /tmp/download_amendements.log
rm -f json/*
for file in html/*; do 
	fileout=$(echo $file | sed 's/html/json/' | sed 's/\.asp/\.xml/')
	perl cut_amdmt.pl $file > $fileout
done;

