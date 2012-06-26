#!/bin/bash

if [ ! -d html ] ; then mkdir html; fi
if [ ! -d out ] ; then mkdir out; fi
source ../../bin/db.inc

#SCRIPT=download_via_recherche.pl
SCRIPT=download_hemicycle.pl

for file in $(perl $SCRIPT $LEGISLATURE); do
	perl parse_hemicycle.pl html/$file > out/$file ;
	echo out/$file done;
done
