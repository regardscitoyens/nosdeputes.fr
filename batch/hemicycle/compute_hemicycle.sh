#!/bin/bash
if ! test -d html ; then
mkdir html out 
fi

perl download_hemicycle.pl | while read file; do
echo $file;
perl parse_hemicycle.pl html/$file > out/$file;
done
