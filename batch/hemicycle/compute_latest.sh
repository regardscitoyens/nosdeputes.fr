#!/bin/bash

source ../../bin/db.inc

mkdir -p html out loaded

#SCRIPT=download_via_recherche.pl
SCRIPT=download_hemicycle.pl

for file in $(perl $SCRIPT $LEGISLATURE); do
	perl parse_hemicycle.pl html/$file > out/$file ;
	echo out/$file done;
done
