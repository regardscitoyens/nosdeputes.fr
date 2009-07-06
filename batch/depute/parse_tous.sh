#!/bin/bash

if [ ! -d xml ] || [ ! -d html ]
then
    tar zxvf last_data.tgz
    exit;
fi

for d in $(ls html/*) ; 
do
    ID=$(echo $d | sed 's/html\///' | sed 's/.asp//')
    echo $ID;
    perl parse_depute.pl html/$ID.asp > xml/$ID.xml
done