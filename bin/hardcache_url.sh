#!/bin/bash

URL=$1
DIR=$(echo $URL | sed -r 's#/[^/]+$##' | sed 's#^.*nosdeputes.fr#web#')
FIL=$(echo $URL | sed -r 's#^.*/([^/]+)$#\1#')
TMPFILE=/tmp/hard_cache_url.$$

mkdir -p $DIR

rm -f $DIR/$FIL.html 2> /dev/null
curl -qL "$URL?_sf_ignore_cache=$$" > $TMPFILE
mv $TMPFILE $DIR/$FIL.html

echo $URL $DIR $FIL
