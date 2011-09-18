#!/bin/bash

reparse=shift
mkdir -p test
outdir="out.yml"

if $reparse; then
  rm -rf $outdir.sv
  mv $outdir $outdir.sv
  mkdir -p $outdir
  for dir in pjl ppl ppr tas rap rga; do
    for file in `ls $dir`; do
      perl parse_doc.pl $dir/$file 1 > $outdir/$file
    done;
  done;
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
  if [ $total -ne $uniqs ] && [ $uniqs -le 500 ]; then
    while read line; do if [[ ! -z $line ]]; then
      echo $line | sed 's/^/'`grep -r "$field: $line$" out.yml/ | wc -l | awk '{print $1}'`' fois\t\t/' >> test/$field.stats
    fi; done < test/$field.uniq
  else cat test/$field.uniq >> test/$field.stats
  fi
  rm test/$field.uniq
done;

echo "Vérifier les champs dans test :"
ls -lrth test

