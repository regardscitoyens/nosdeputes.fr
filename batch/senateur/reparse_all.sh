#!/bin/bash

yml=$1;
for url in `ls html`; do
	perl parse_senateur.pl html/$url $yml > out/$url
done 
