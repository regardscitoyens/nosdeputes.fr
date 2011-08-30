#!/bin/bash

yml=$1

for dir in 20*; do
  echo "working on $dir"
  bash parse_dir.sh $dir $yml
done
if [ ! -z $yml ]; then
  bash test_fields.sh
fi
