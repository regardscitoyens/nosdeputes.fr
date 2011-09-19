#!/bin/bash

reparse=$1
mkdir -p test

if [ ! -z $reparse ]; then
  bash reparse_all.sh 1
fi

fields=`cat out.yml/* | grep ": ." | grep -v "^    - " | sed 's/: .*$//' | sed 's/^ *//' | sort | uniq`
total=`ls out.yml/ | wc -l | awk '{print $1}'`
echo "$total documents parsés analysés"

for field in $fields; do
  grep -r "  $field:" out.yml/ | sed 's/^.*\(.\)\.html:  '$field': //' | sort > test/$field.tmp
  stotal=`wc -l test/$field.tmp | awk '{print $1}'`
  echo "Champ $field présent dans $stotal documents (manque dans $(($total-$stotal)) documents)" > test/$field.stats
  echo  >> test/$field.stats
  uniq test/$field.tmp > test/$field.uniq
  rm test/$field.tmp
  uniqs=`wc -l test/$field.uniq | awk '{print $1}'`
  echo "$uniqs valeurs uniques :" >> test/$field.stats
  echo  >> test/$field.stats
  if [ $total -ne $uniqs ] && [ $uniqs -le 500 ] || [[ $field == "id" ]]; then
    while read line; do if [[ ! -z $line ]]; then
      echo $line | sed 's/^/'`grep -r "$field: $line$" out.yml/ | wc -l | awk '{print $1}'`' fois\t\t/' >> test/$field.stats
    fi; done < test/$field.uniq
  else cat test/$field.uniq >> test/$field.stats
  fi
  rm test/$field.uniq
done;

echo "Vérifier les champs dans test :"
ls -lrth test

