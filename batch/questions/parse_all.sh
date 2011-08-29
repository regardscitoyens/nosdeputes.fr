#!/bin/bash

yml=$1

for dir in 20*; do
  bash parse_dir.sh $dir $yml
done
if [ ! -z $yml ]; then
  bash test_fields.sh
  echo "Vérifier les champs dans test :"
  ls -lrth test
fi
