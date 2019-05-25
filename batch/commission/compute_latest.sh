#!/bin/bash

source ../../bin/db.inc

mkdir -p html out presents loaded

for file in $(perl download_commission.pl $LEGISLATURE); do
    if grep "compte rendu .* sera .*é ultérieurement\|Document en attente de mise en ligne.\|La page à laquelle vous souhaitez accéder n'existe pas.\|HTTP Error 503" "html/$file" > /dev/null; then
        if echo $file | grep -P "_\d+_cr[^_]*_(\d+)-(\d+)_c\d+" > /dev/null; then
            echo "...removing empty file $file" | grep -v "http:__www.assemblee-nationale.fr_15_europe_c-rendus_c0" | grep -P "_\d+_cr[^_]*_(\d+)-(\d+)_c\1\2"
        else
            echo "...removing empty file $file" | grep -v "http:__www.assemblee-nationale.fr_15_europe_c-rendus_c0"
        fi
        rm "html/$file"
        continue
    elif grep "Ce compte rendu sera disponible dès que possible\." "html/$file" > /dev/null; then
        echo "...only parsing presents from CR dispo dès que possible $file"
        perl parse_presents.pl html/$file > presents/$file
        rm "html/$file"
        continue
    fi
	echo try ...
	perl parse_commission.pl html/$file > out/$file
	perl parse_presents.pl html/$file > presents/$file
	echo out/$file done
done

bash compute_special_orgs.sh
