#!/bin/bash

mkdir -p html out loaded raw
OLDURL=$1
DEBUG=
if [ ! -z "$2" ]; then
  DEBUG=1
fi

perl download_one.pl $OLDURL | while read line; do
  contentfile=$(echo $line | awk '{print $1}')
  file=$(echo $line | awk '{print $2}')
  url=$(echo $line | awk '{print $3}')
  outfile=$(echo $file | sed 's|^html/|out/|')
  python3 parse_hemicycle.py $contentfile $url > $outfile
  if [ ! -z "$DEBUG" ]; then
    ./check_parsed_cri.sh $outfile
  fi
  echo "$url -> $contentfile + $outfile"
done

