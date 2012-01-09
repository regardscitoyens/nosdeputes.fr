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
grep "@" test/arrays > test/mails.uniq
grep "http" test/arrays > test/sites_web.uniq
grep "\([0-9]\{5\}\|[0-9][0-9] [0-9][0-9] [0-9][0-9]\)" test/arrays > test/adresses.uniq
grep "^[0-9]\+\/[0-9]\+\/[0-9]\+"  test/arrays > test/premiers_mandats.uniq
grep "^\([^0-9].*\)\? / .* / " test/arrays > test/mandats_locaux.uniq
grep "Groupe d'" test/arrays > test/groupes.uniq
grep -v "\(@\|http\|[0-9]\{5\}\|[0-9][0-9] [0-9][0-9] [0-9][0-9]\|^[0-9]\+\/[0-9]\+\/[0-9]\+\|^\([^0-9].*\)\? / .* / \|Groupe d'\)" test/arrays > test/allfonctions.uniq

cat test/premiers_mandats.uniq | awk -F " / " '{print $3}' | sort | uniq > test/premiers_mandats_motifs.uniq
cat test/mandats_locaux.uniq | awk -F " / " '{print $1}' | sort | uniq > test/mandats_locauxlieux.uniq
cat test/mandats_locaux.uniq | awk -F " / " '{print $2}' | sort | uniq > test/mandats_locauxinstances.uniq
cat test/mandats_locaux.uniq | awk -F " / " '{print $3}' | sort | uniq > test/mandats_locauxpostes.uniq
cat test/groupes.uniq | awk -F " / " '{print $1}' | sort | uniq > test/groupes_noms.uniq
cat test/groupes.uniq | awk -F " / " '{print $2}' | sort | uniq > test/groupes_fonctions.uniq
cat test/allfonctions.uniq | awk -F " / " '{print $1}' | sort | uniq > test/allfonctions_organismes.uniq
cat test/allfonctions.uniq | awk -F " / " '{print $2}' | sort | uniq > test/allfonctions_fonctions.uniq

#rm test/arrays

echo "Vérifier les champs dans test :"
ls -lrth test


