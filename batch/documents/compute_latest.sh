#!/bin/bash

for file in `perl download_docs.pl`; do
  echo $file
  file2=`echo $file | sed 's/\(pjl|ppl|ppr|rap|ta\)/out/'`
  perl parse_document.pl $file > $file2
done;


