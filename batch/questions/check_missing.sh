#!/bin/bash

cd $(dirname $0)
source ../../bin/db.inc

echo "Downloading Questions from OpenData AN..."
rm -f Questions_ecrites_XIV.json*
wget -q http://data.assemblee-nationale.fr/static/openData/repository/QUESTIONS/questions_ecrites/Questions_ecrites_XIV.json.zip -O Questions_ecrites_XIV.json.zip
unzip Questions_ecrites_XIV.json.zip > /dev/null
echo "Extracting list of Questions from OpenData AN..."
cat Questions_ecrites_XIV.json              |
  sed -r 's/("numero": ")/\n\1/g'           |
  grep '^"numero": .*'"$LEGISLATURE"        |
  sed -r 's/^"numero": "([0-9]+)".*$/\1/'   |
  sort -n > all_questions_opendataAN.tmp

rm -f Questions_ecrites_XIV.json*

echo "Extracting list of Questions from NosDéputés..."
echo "SELECT numero FROM question_ecrite ORDER BY numero"   |
  mysql $MYSQLID $DBNAME                                    |
  grep -v numero > all_questions_nosdeputes.tmp

echo "Analysing diff..."
extra=$(diff all_questions_opendataAN.tmp all_questions_nosdeputes.tmp | grep "^>" | wc -l)
if [ $extra -gt 0 ]; then
  echo "- NosDéputés has $extra questions not in AN's OpenData yet(?):"
  diff all_questions_opendataAN.tmp all_questions_nosdeputes.tmp    |
    grep "^>"                                                       |
    sed 's/[^0-9]//g'                                               |
    while read num; do
      echo "http://questions.assemblee-nationale.fr/q$LEGISLATURE/$LEGISLATURE-${num}QE.htm"
    done
  echo
fi

missing=$(diff all_questions_opendataAN.tmp all_questions_nosdeputes.tmp | grep "^<" | wc -l)
if [ $missing -gt 0 ]; then
  echo "There are $missing questions missing, reloading them:"
  diff all_questions_opendataAN.tmp all_questions_nosdeputes.tmp    |
    grep "^<"                                                       |
    sed 's/[^0-9]//g'                                               |
    while read num; do
      QEurl="http://questions.assemblee-nationale.fr/q$LEGISLATURE/$LEGISLATURE-${num}QE.htm"
      QEfile="http:__questions.assemblee-nationale.fr_q${LEGISLATURE}_${LEGISLATURE}-${num}QE.htm"
      perl download_one.pl "$QEurl"
      python parse.py "html/$QEfile" > "json/$QEfile"
    done
  echo 'All missing questions reloaded and parsed, run "php symfony load:Questions" to complete'
fi

rm -f all_questions_*.tmp
