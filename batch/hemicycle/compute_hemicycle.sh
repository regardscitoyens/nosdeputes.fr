#!/bin/bash
if ! test -d html || ! test -d  out; then
mkdir -p html out 
fi

perl download_hemicycle.pl | while read file; do
echo $file;
perl parse_hemicycle.pl html/$file > out/$file;
done
