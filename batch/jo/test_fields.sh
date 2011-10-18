#!/bin/bash

mkdir -p test

total=`cat xml/* | wc -l | awk '{print $1}'`
echo "$total présences trouvées"

for field in date heure commission senateur source; do
echo $field
  grep -r "\"$field\":" xml/ | sed "s/^.*\"$field\": \"\([^\"]*\)\".*$/\1/" | sort > test/$field.tmp
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
      echo $line | sed 's/^/'`grep -r "\"$field\": \"$line\"" xml/ | wc -l | awk '{print $1}'`' fois\t\t/' >> test/$field.stats
    fi; done < test/$field.uniq
  else cat test/$field.uniq >> test/$field.stats
  fi
  rm test/$field.uniq
done;

echo "Vérifier les champs dans test :"
ls -lrth test

