#!/bin/bash
echo ce script est obsolete : il parse tout le repertoire html
echo Si vous voulez le faire, appuyer sur une touche sinon CRTL+C
read
for file in html/*; do 
fileout=$(echo $file | sed 's/html/presents/')
perl parse_presents.pl $file > $fileout
if ! test -s $fileout; then
    rm $fileout;
fi
done; 
