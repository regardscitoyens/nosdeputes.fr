#!/bin/bash

mkdir -p out.yml test
rm -f out.yml/*

for url in `ls html`; do
  perl parse_senateur.pl html/$url 1 > out.yml/$url
done

fields=`cat out.yml/* | grep ": ." | grep -v "^    - " | sed 's/: .*$//' | sed 's/^ *//' | sort | uniq`
total=`ls out.yml/ | wc -l | awk '{print $1}'`

for field in $fields; do
  grep -r "  $field:" out.yml/ | sed 's/^.*\(.\)\.html:  //' | sort > test/$field.tmp
  stotal=`wc -l test/$field.tmp | awk '{print $1}'`
  echo "Champ $field présent dans $stotal fichiers (manque dans $(($total-$stotal)) fichiers)" > test/$field.stats
  echo  >> test/$field.stats
  uniq test/$field.tmp > test/$field.uniq
  rm test/$field.tmp
  uniqs=`wc -l test/$field.uniq | awk '{print $1}'`
  echo "$uniqs valeurs uniques :" >> test/$field.stats
  echo  >> test/$field.stats
  if [ $uniqs -le 100 ] &&  [ $total -ne $uniqs ]; then
    while read line; do
      echo $line | sed 's/^/'`grep -r "$line$" out.yml/ | wc -l | awk '{print $1}'`'\t\t/' >> test/$field.stats
    done < test/$field.uniq
  else cat test/$field.uniq >> test/$field.stats
  fi
  rm test/$field.uniq
done;
echo "Vérifier les champs dans test :"
ls -lrth test

