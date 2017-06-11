#!/bin/bash

DIR=$(echo $0 | sed 's/[^\/]*$//');
LEGISLATURE=$1

if echo $DIR | grep -i [a-z]; then
  cd $DIR
fi

mkdir -p json html

for d in html/* ; do
  ID=$(echo $d | sed 's/html\///' | sed 's/\.asp$//')
  if test -n "$2"; then
    echo $ID;
  fi
  if [ "$LEGISLATURE" = "13" ]; then
    perl parse_depute_L13.pl html/$ID.asp > json/$ID.json
  else
    perl parse_depute.pl html/$ID > json/$ID.json
  fi
done
