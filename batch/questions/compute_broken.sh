#!/bin/bash

rm -f html/*

echo "DOWNLOADING"
ct=0
for url in `cat /tmp/reload-questions.urls`; do
  ct=$(($ct + 1))
  perl download_one.pl $url
  echo "$ct done"
done

echo "PARSING"
ct=0
for file in `grep -L "The page cannot be found" html/*`; do
  ct=$(($ct + 1))
  fileout=$(echo $file | sed 's/html/json/')
  perl parse_question.pl $file > $fileout
  echo "$ct done"
done;

