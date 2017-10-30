#!/bin/bash

JSON=$1

echo "Didascalies :"
echo "-------------"
grep '"intervenant": ""' $JSON      |
  sed 's/^.*"intervention": "/-> /' |
  sed 's/".*$//'
echo "-------------"
echo
echo

echo "Meme interv :"
interv="START"
cat $JSON | while read line; do
  newinterv=$(echo $line            |
    sed 's/^.*"intervenant": "//'   |
    sed 's/",.*"fonction": "/, /'   |
    sed 's/".*$//'
  )
  newtext=$(echo $line              |
    sed 's/^.*"intervention": "//'  |
    sed 's/".*$//'
  )
  if [ "$interv" = "$newinterv" ] && [ ! -z "$interv" ]; then
    echo "-------------"
    echo "$interv: $text"
    echo "$newinterv: $newtext"
  fi
  interv=$newinterv
  text=$newtext
done
echo "-------------"
echo
echo

echo "Intervenants:"
echo "-------------"
grep -v '"intervenant": ""' $JSON   |
  sed 's/^.*"intervenant": "//'     |
  sed 's/",.*"fonction": "/\t\t|  /'|
  sed 's/".*$//'                    |
  sort  | uniq -c
echo "-------------"
echo
echo

head -1 $JSON                               |
  sed 's/^.*"commission": "/COMMISSION: /'  |
  sed 's/", .*"date": "/\nDATE:       /'    |
  sed 's/", .*"heure": "/ - /'          |
  sed 's/".*$//'

head -1 $JSON                           |
  sed 's/^.*"source": "/SOURCE:     /'  |
  sed 's/[#"].*$/\n/'
