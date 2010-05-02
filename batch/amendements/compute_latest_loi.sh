#!/bin/bash

loi=$1

rm -f html/*

perl download_amendements_loi.pl $loi > /tmp/download_amendements.log

for file in html/*; do 
	fileout=$(echo $file | sed 's/html/json/' | sed 's/\.asp/\.xml/')
	perl cut_amdmt.pl $file > $fileout
done;

