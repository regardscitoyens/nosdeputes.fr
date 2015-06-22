#!/bin/bash

source ../../bin/db.inc

if [ ! -d html ] ; then mkdir html; fi
if [ ! -d out ] ; then mkdir out; fi

for file in $(perl download_commission.pl $LEGISLATURE); do
    if grep "La page à laquelle vous souhaitez accéder n'existe pas." "html/$file"; then
        echo "...removing empty file."
        rm "html/$file"
        continue
    fi
	echo try ... ;
	perl parse_commission.pl html/$file > out/$file ;
	perl parse_presents.pl html/$file > presents/$file ;
	echo out/$file done;
done

