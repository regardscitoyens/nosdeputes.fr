#!/bin/bash

version="2012"
if [! -z "$1" ]; then
  version="2007"
fi
curdep="";
curcirco="";
awk -F ";" '{print $2";"$4";"$5" "$3}' static/circo_insee_$legi.csv | sort | while read line; do
  dep=`echo $line | awk -F ";" '{print $1}'`;
  circo=`echo $line | awk -F ";" '{print $2}' | sed 's/^0\+//'`;
  curtext=`echo $line | awk -F ";" '{print $3}'`;
  if [ "$dep" != "$curdep" ]; then
    curdep=$dep;
    curcirco=$circo;
    echo "\"},\"$dep\":{\"$circo\": \"$curtext";
  else
    if [ "$circo" != "$curcirco" ]; then
      curcirco=$circo;
      echo "\", \"$circo\": \"$curtext";
    else
      echo ", $curtext";
    fi;
  fi;
done > /tmp/villes.json

cat /tmp/villes.json | tr '\n' ' ' | sed 's/ , /, /g' | sed 's/^"},/{/' | sed 's/$/"} }/' static/villes_$legi.json

