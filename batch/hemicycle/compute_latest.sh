#!/bin/bash

if [ ! -d html ] ; then mkdir html; fi
if [ ! -d out ] ; then mkdir out; fi

#SCRIPT=download_via_recherche.pl
SCRIPT=download_hemicycle.pl

for file in $(perl $SCRIPT); do
	perl parse_hemicycle.pl html/$file > out/$file ;
	echo out/$file done;
done
