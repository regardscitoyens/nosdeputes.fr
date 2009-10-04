#!/bin/bash

for file in $(perl download_commission.pl); do
	echo try ... ;
	perl parse_commission.pl html/$file > out/$file ;
	echo out/$file done;
done

