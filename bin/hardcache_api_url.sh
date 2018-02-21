#!/bin/bash

URL=$(echo $1 | sed -r 's#/(xml|json|csv)$##')
DIR=$(echo $URL | sed 's#^.*nossenateurs.fr#web#')
TMPFILE=/tmp/hard_cache_api.$$

mkdir -p $DIR

for f in xml json csv; do
  rm $DIR/$f.html 2> /dev/null
  curl -qL "$URL/$f?_sf_ignore_cache=$$" > $TMPFILE
  mv $TMPFILE $DIR/$f.html
done

echo $URL $DIR
