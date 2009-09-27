#!/bin/bash
for file in html/*; do 
fileout=$(echo $file | sed 's/html/json/' | sed 's/\.asp/\.xml/')
echo $fileout
perl cut_amdmt.pl $file > $fileout
done; 
