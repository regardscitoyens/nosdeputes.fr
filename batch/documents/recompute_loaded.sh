#!/bin/bash

source ../../bin/db.inc

mkdir -p pjl ppl ppr rap ta loaded

for file in `find pjl ppl ppr rap ta -type f`; do
  echo $file
  file2=`echo $file | sed 's/^\(pjl\|ppl\|ppr\|rap\|ta\)\//loaded\//'`
  perl parse_metas.pl $file > $file2
done;


