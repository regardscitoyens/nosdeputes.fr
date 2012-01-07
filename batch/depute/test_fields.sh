#!/bin/bash

mkdir -p out.yml test
if [ -z $1 ]; then
  rm -rf out.sv out.yml.sv
  cp -r out.yml out.yml.sv
  rm -f out.yml/*
  for url in `ls html`; do
    perl parse_depute_new.pl html/$url 1 > out.yml/$url
  done
fi

fields=`cat out.yml/* | grep ": ." | grep -v "^      - " | sed 's/: .*$//' | sed 's/^ *//' | sort | uniq`
total=`ls out.yml/ | wc -l | awk '{print $1}'`

for field in $fields; do
  grep -r "    $field:" out.yml/ | sed 's/^.*\(.\)\.asp:    '$field': //' | sort > test/$field.tmp
  stotal=`wc -l test/$field.tmp | awk '{print $1}'`
  echo "Champ $field présent dans $stotal fichiers (manque dans $(($total-$stotal)) fichiers)" > test/$field.stats
  echo  >> test/$field.stats
  uniq test/$field.tmp > test/$field.uniq
  rm test/$field.tmp
  uniqs=`wc -l test/$field.uniq | awk '{print $1}'`
  echo "$uniqs valeurs uniques :" >> test/$field.stats
  echo  >> test/$field.stats
  if [ $total -ne $uniqs ]; then
    while read line; do
      echo $line | sed 's/^/'`grep -r "$line$" out.yml/ | wc -l | awk '{print $1}'`'\t\t/' >> test/$field.stats
    done < test/$field.uniq
  else cat test/$field.uniq >> test/$field.stats
  fi
  rm test/$field.uniq
done;

grep -r "^      - " out.yml/ | sed 's/^.*\(.\)\.asp:      - //' | sort | uniq > test/arrays
grep "@" test/arrays > test/emails.uniq
grep "http" test/arrays > test/sites.uniq
grep "Téléphone" test/arrays > test/adresses.uniq
grep "- [0-9]\+\/[0-9]\+\/[0-9]\+ \/ " test/arrays | sort | uniq > test/premiersmandats.uniq
grep -v "\(Téléphone|@\|http\|\- [0-9]\+\/[0-9]\+\/[0-9]\+ \/ \)" test/arrays > test/organismes
cat test/organismes | awk -F " / " '{print $1}' | sort | uniq > test/organismes.uniq
cat test/organismes | awk -F " / " '{print $2}' | sort | uniq > test/fonctions.uniq
cat test/organismes | awk -F " / " '{print $3}' | sort | uniq > test/postes.uniq
cat test/organismes | awk -F " / " '{print $4" / "$5}' | sort | uniq > test/dates.uniq
#rm test/arrays test/organismes

echo "Vérifier les champs dans test :"
ls -lrth test


