#!/bin/bash

rm -rf out
mkdir -p out

yml=$1;
for url in `ls html`; do
	perl parse_amdmt.pl html/$url $yml > out/$url
done

