#!/bin/bash
for file in html/*; do 
fileout=$(echo $file | sed 's/html/out/')
perl parse_hemicycle.pl $file > $fileout
done; 
