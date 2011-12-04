#!/bin/bash

if [ ! -d html ] ; then mkdir html; fi
if [ ! -d out ] ; then mkdir out; fi

perl predownload_commission.pl > log_predownload
for file in $(perl download_commission.pl $1); do
	echo try ... ;
	perl parse_commission.pl html/$file > out/$file ;
	echo out/$file done;
done

