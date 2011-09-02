#!/bin/bash

if ! test -d out; then mkdir out ; fi
if ! test -d html; then mkdir html ; fi 
rm -f out/* html/*

perl download_senateurs.pl | while read url ; do
  perl parse_senateur.pl html/$url > out/$url
done

