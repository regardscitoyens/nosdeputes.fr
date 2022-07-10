#!/bin/bash

DIR=$(echo $0 | sed 's/[^\/]*$//');

if echo $DIR | grep -i [a-z]; then
  cd $DIR
fi

source ../../bin/db.inc

mkdir -p json html

for d in html/* ; do
  ID=$(echo $d | sed 's/html\///' | sed 's/\.asp$//')
  if test -n "$1"; then
    echo $ID;
  fi
  if [ "$LEGISLATURE" = "13" ]; then
    perl parse_depute_L13.pl html/$ID.asp > json/$ID.json
  else
    perl parse_depute.pl html/$ID > json/$ID.json
  fi
done

touch suppleants.csv
rgrep suppleant json/ | sed 's/^.*"nom": "//' | sed 's/".*"suppleant": "/;/' | sed 's/".*$//' >> suppleants.new
cat suppleants.csv suppleants.new | sort -u > suppleants.csv

