#!/bin/bash

reparse=shift
mkdir -p out.yml test

if $reparse; then
  rm -f out.yml/*
  for url in `ls html`; do
    perl parse_amdmt.pl html/$url 1 > out.yml/$url
  done
fi

fields=`cat out.yml/* | grep ": ." | grep -v "^    - " | grep -v "^amendement" | sed 's/: .*$//' | sed 's/^ *//' | sort | uniq`
total=`grep -r "^amendement: " out.yml/ | wc -l | awk '{print $1}'`
echo "$total amendements parsés analysés"

for field in $fields; do
  grep -r "  $field:" out.yml/ | sed 's/^.*\(.\)\.html:  '$field': //' | sort > test/$field.tmp
  stotal=`wc -l test/$field.tmp | awk '{print $1}'`
  echo "Champ $field présent dans $stotal amendements (manque dans $(($total-$stotal)) amendements)" > test/$field.stats
  echo  >> test/$field.stats
  uniq test/$field.tmp > test/$field.uniq
  rm test/$field.tmp
  uniqs=`wc -l test/$field.uniq | awk '{print $1}'`
  echo "$uniqs valeurs uniques :" >> test/$field.stats
  echo  >> test/$field.stats
  if [ $total -ne $uniqs ] && [ $uniqs -le 500 ]; then
    while read line; do if [[ ! -z $line ]]; then
      echo $line | sed 's/^/'`grep -r ": $line$" out.yml/ | wc -l | awk '{print $1}'`' fois\t\t/' >> test/$field.stats
    fi; done < test/$field.uniq
  else cat test/$field.uniq >> test/$field.stats
  fi
  rm test/$field.uniq
done;

echo "Vérifier les champs dans test :"
ls -lrth test

