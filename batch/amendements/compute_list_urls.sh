#!/bin/bash

mkdir -p json

LIST=$1

cat $LIST | while read url; do
  file=$(perl download_one.pl $url | awk '{print $1}')
  echo "$url -> $file"
  perl parse_amdmt.pl html/$file > json/$file
done

