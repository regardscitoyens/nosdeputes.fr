#!/bin/bash

DIR=$(echo $0 | sed 's/[^\/]*$//');

if echo $DIR | grep -i [a-z]
then
    cd $DIR
fi

if [ ! -d out ] || [ ! -d html ]
then
    tar zxvf last_data.tgz
    exit;
fi

for d in html/* ; 
do
    ID=$(echo $d | sed 's/html\///' | sed 's/.asp//')
    if test -n "$1"; then
	    echo $ID;
    fi
    perl parse_depute.pl html/$ID.asp > out/$ID.xml
done
