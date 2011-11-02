#!/bin/bash

if ! test -d html || ! test -d  out; then
mkdir -p html out
fi
rm -rf out.sv

cp modified/* html/
ls html/ | while read file; do
echo $file;
perl parse_hemicycle.pl html/$file > out/$file;
done
cp -r out out.sv

