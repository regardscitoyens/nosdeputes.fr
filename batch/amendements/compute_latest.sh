#!/bin/bash

mkdir -p json

if [ -e to_parse.list ]; then
  for file in `cat to_parse.list`; do
    echo $file
    output=`echo $file | sed 's/html\//json\//'`
    perl parse_amdmt.pl $file > $output
  done
fi

