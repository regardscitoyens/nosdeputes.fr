#!/bin/bash

dir=$1
yml=$2

if [ ! -d json ] ; then mkdir json; fi
if [ -z $dir ]; then
  dir="html"
fi

for file in `ls $dir`; do
  perl parse_question.pl $dir/$file $yml > json/$file
done

