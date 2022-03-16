#!/bin/bash

mkdir -p html out presents loaded raw
OLDURL=$1
CACHE=
DEBUG=
if [ ! -z "$3" ] && [ ! -z "$2" ]; then
  CACHE="--use-cache"
  DEBUG=1
elif [ "$2" = "--use-cache" ]; then
  CACHE="--use-cache"
elif [ ! -z "$2" ]; then
  DEBUG=1
fi

perl download_one.pl $OLDURL $CACHE | while read line; do
  contentfile=$(echo $line | awk '{print $1}')
  file=$(echo $line | awk '{print $2}')
  url=$(echo $line | awk '{print $3}')
  outfile=$(echo $file | sed 's|^html/|out/|')
  presentsfile=$(echo $file | sed 's|^html/|presents/|')
  perl parse_presents.pl $contentfile $url > $presentsfile
  python3 parse_commission.py $contentfile $url $CACHE > $outfile
  if [ ! -z "$DEBUG" ]; then
    ./check_parsed_cr.sh $outfile
    cat $presentsfile
  else
    echo $url
    echo "-> $contentfile + $outfile"
  fi
  ./reserve_cr_to_check.sh $outfile
done

