#!/bin/bash

y=$1
for (( m=1 ; m<13  ; m=$m+1 ))
do
for (( i=1 ; i<32 ; i=$i+1 )) ; do sh compute_jo.sh $i $m $y ; done 
done