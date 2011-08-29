#!/bin/bash

fields="source legislature type numero date_publi date_reponse page_question page_reponse ministere titre question reponse motif_retrait auteur"

mkdir -p test

for field in $fields; do
  grep "  $field:" json/* | sed 's/^.*\(.\)\.html:  //' | sort > test/$field.tmp
  ntotal=`wc -l test/$field.tmp | awk '{print $1}'`
  echo "Champ $field présent dans $ntotal questions (manque dans $((23712-$ntotal)) questions)" > test/$field.stats
  echo  >> test/$field.stats
  uniq test/$field.tmp > test/$field.uniq
  rm test/$field.tmp
  uniqs=`wc -l test/$field.uniq | awk '{print $1}'`
  echo "$uniqs valeurs uniques :" >> test/$field.stats
  echo  >> test/$field.stats
  if [ $uniqs -le 50 ]; then
    while read line; do
      echo $line | sed 's/^/'`grep "$line$" json/* | wc -l | awk '{print $1}'`'\t\t/' >> test/$field.stats
    done < test/$field.uniq
  else cat test/$field.uniq >> test/$field.stats
  fi
  rm test/$field.uniq
done;
ls -lrth test

