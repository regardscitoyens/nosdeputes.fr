#!/bin/bash

mkdir -p out

if [ -e to_parse.list ]; then
  for file in `cat to_parse.list`; do
    echo $file
    output=`echo $file | sed 's/\(pjl\|ppl\|ppr\|rap\|rga\|tas\)\//out\//'`
    perl parse_doc.pl $file > $output
  done
fi

