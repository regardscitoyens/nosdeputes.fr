#!/bin/bash

y=$1;
extra=$2;

for (( m=1 ; m<13  ; m=$m+1 ))
do
for (( i=1 ; i<32 ; i=$i+1 )) ; do bash compute_jo.sh $i $m $y $extra; done 
done