#!/bin/bash

legislature=$1
loi=$2

if test -z "$2"; then
  echo "Please input a proper loi"
  exit 1
fi

rm -rf "html-$loi"

bash download_amendements_loi.sh "$loi" > /tmp/download_amendements.log

for file in `ls html-$loi`; do 
	fileout=$(echo $file | sed 's|html-[^/]*|json|' | sed 's/\.asp/\.xml/')
	perl cut_amdmt.pl "html-$loi/$file" > json/$fileout
done;

