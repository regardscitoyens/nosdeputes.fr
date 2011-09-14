#!/bin/bash

rm -rf json 
mkdir -p json

yml=$1;
for url in `ls html`; do
	perl parse_amdmt.pl html/$url $yml > json/$url
done

