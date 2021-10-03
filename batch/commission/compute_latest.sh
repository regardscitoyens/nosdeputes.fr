#!/bin/bash

source ../../bin/db.inc

mkdir -p html out presents loaded raw

perl download_commission.pl $LEGISLATURE | while read line; do
    file=$(echo $line | awk '{print $2}')
    contentfile=$(echo $line | awk '{print $1}')
    url=$(echo $line | awk '{print $3}')
    echo $url $contentfile $file
    if grep "compte rendu .* sera .*é ultérieurement\|Document en attente de mise en ligne.\|La page à laquelle vous souhaitez accéder n'existe pas.\|HTTP Error 503" "$contentfile" > /dev/null; then
        if echo $file | grep -P "_\d+_cr[^_]*_(\d+)-(\d+)_c\d+" > /dev/null; then
            echo "...removing empty file $file" | grep -v "http:__www.assemblee-nationale.fr_15_europe_c-rendus_c0" | grep -P "_\d+_cr[^_]*_(\d+)-(\d+)_c\1\2"
        else
            echo "...removing empty file $file" | grep -v "http:__www.assemblee-nationale.fr_15_europe_c-rendus_c0"
        fi
        rm "$file" "$contentfile"
        continue
    elif grep "Ce compte rendu sera disponible dès que possible\." "$contentfile" > /dev/null; then
        #echo "...only parsing presents from CR dispo dès que possible $contentfile"
        #perl parse_presents.pl html/$file > presents/$file
        rm "$file" "$contentfile"
        continue
    fi
	echo try ...
    outfile=$(echo $file | sed 's|^html/|out/|')
	python parse_commission.py $contentfile $url > $outfile
	#perl parse_commission.pl html/$file > out/$file
	#perl parse_presents.pl html/$file > presents/$file
	echo out/$file done
done

bash compute_special_orgs.sh
