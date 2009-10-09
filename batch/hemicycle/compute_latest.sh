#!/bin/bash

#SCRIPT=download_via_recherche.pl
SCRIPT=download_hemicycle.pl

for file in $(perl $SCRIPT); do
	perl parse_hemicycle.pl html/$file > out/$file ;
	echo out/$file done;
done
