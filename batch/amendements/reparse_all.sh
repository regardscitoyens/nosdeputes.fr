#!/bin/bash

rm -rf json 
mkdir -p json

yml=$1;
ct=0
for url in `ls html`; do
    echo $ct $url
	perl parse_amdmt.pl html/$url $yml  | python clean_subjects_amdmts.py > json/$url
    ct=$((ct + 1))
done

