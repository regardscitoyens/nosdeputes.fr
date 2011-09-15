#!/bin/bash 

for i in html/http* ; do 
file=$(echo $i | sed 's/html\///') ; 
echo $file ; 
perl parse_commission.pl html/$file > out/$file ; 
done
