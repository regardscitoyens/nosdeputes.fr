#!/bin/bash

DIR=$(echo $0 | sed 's/[^\/]*$//');

if echo $DIR | grep -i [a-z]
then
    cd $DIR
fi

if [ ! -d out ] ; then mkdir out; fi
if [ ! -d html ] ; then mkdir html; fi

for d in html/* ; 
do
    ID=$(echo $d | sed 's/html\///' | sed 's/.asp//')
    if test -n "$1"; then
	    echo $ID;
    fi
#    perl parse_depute.pl html/$ID.asp > out/$ID.xml
    perl parse_depute_new.pl html/$ID.asp > out/$ID.xml
done
