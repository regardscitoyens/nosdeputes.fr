#!/bin/bash

. ../../bin/db.inc

if [ ! -d html ] ; then mkdir html; fi
if [ ! -d json ] ; then mkdir json; fi

echo 'SELECT source FROM amendement WHERE sort LIKE "Ind%" AND date > DATE_SUB(CURDATE() , INTERVAL 1 YEAR)' | mysql $MYSQLID $DBNAME | grep -v source > liste_sort_indefini.txt

rm -f html/*

perl download_amendements.pl $LEGISLATURE > /tmp/download_amendements.log

for file in `ls html`; do 
	fileout=$(echo $file | sed 's/html/json/' | sed 's/\.asp/\.xml/')
	perl cut_amdmt.pl html/$file > json/$fileout
done;

