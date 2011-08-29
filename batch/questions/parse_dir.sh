#!/bin/bash

dir=$1
yml=$2

if [ -z $dir ]; then
  dir="html"
fi

for file in `ls $dir`; do
  perl parse_question.pl $dir/$file $yml > out/$file
done

