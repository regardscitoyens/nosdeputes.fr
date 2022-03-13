#!/bin/bash

source=$1

url=$source
while test "$url" ; do
    curl -s -D /tmp/source.headers "$url" > /tmp/source.html
    url=''
    if grep location /tmp/source.headers > /dev/null ; then
        if grep location /tmp/source.headers | grep assemblee-nationale > /dev/null ; then 
        url=$(grep location /tmp/source.headers | awk '{print $2}' | tr -d '\n' | tr -d "\r")
        else
        url="https://www.assemblee-nationale.fr"$(grep location /tmp/source.headers | awk '{print $2}' | tr -d '\n' | tr -d "\r")
        fi
    fi
done

opendataid=$(grep opendata /tmp/source.html  | grep .html | sed 's/"/\//g' | awk -F '/' '{print $5}' )

python3 parse_commission.py "raw/"$opendataid $source
