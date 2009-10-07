#!/bin/bash

. ../../bin/db.inc

cat liste_sort_indefini.sql | mysql $MYSQLID $DBNAME | grep -v source > liste_sort_indefini.txt

rm -f html/*

perl download_amendements.pl > /tmp/download_amendements.log

for file in html/*; do 
	fileout=$(echo $file | sed 's/html/json/' | sed 's/\.asp/\.xml/')
	perl cut_amdmt.pl $file > $fileout
done;

