#!/bin/bash

cd $(dirname $0)/QE-tableaux

touch numeros
bash query_QE_tableaux.sh > numeros.new
if [ `diff -uw numeros numeros.new | grep "^+h" | wc -l` -gt 0 ] ; then
  diff -uw numeros numeros.new
  diff -uw numeros numeros.new | grep "^+h" | sed 's/^+//' | mail -a "From: contact@regardscitoyens.org" -s "Nouvelles réponses à des QE AN contenant un tableau" $1 $2 $3 $4 $5 $6
  mv numeros.new numeros
fi
