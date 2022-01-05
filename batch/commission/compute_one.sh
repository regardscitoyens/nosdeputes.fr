#!/bin/bash

mkdir -p html out presents loaded raw

perl download_one.pl $1 | while read line; do
  contentfile=$(echo $line | awk '{print $1}')
  file=$(echo $line | awk '{print $2}')
  url=$(echo $line | awk '{print $3}')
  outfile=$(echo $file | sed 's|^html/|out/|')
  echo $url $contentfile $file $outfile
  python parse_commission.py $contentfile $url > $outfile
done
