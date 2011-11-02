#!/bin/bash 

rm -rf out.sv
mkdir -p out
cp modified/* html/
for i in html/http* ; do 
file=$(echo $i | sed 's/html\///') ; 
echo $file
perl parse_commission.pl html/$file > out/$file ; 
done

cp -r out out.sv
