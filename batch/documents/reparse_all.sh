#!/bin/bash

yml=$1
outdir="out"
if [ ! -z  $yml ]; then 
  outdir="out.yml"
fi

rm -rf $outdir.sv
mv $outdir $outdir.sv
mkdir -p $outdir

for dir in pjl ppl ppr tas rap rga; do
  for file in `ls $dir`; do
    perl parse_doc.pl $dir/$file $yml > $outdir/$file
  done;
done;

