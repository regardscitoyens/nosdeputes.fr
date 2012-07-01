#!/bin/bash

. $(echo $0 | sed 's/[^\/]*$//')db.inc
cd $PATH_APP
start=$(($LEGISLATURE * 5 + 1942))

php symfony top:Deputes
for month in 06 07 08 09 10 11 12; do
  php symfony top:Deputes $start-$month-01
done
for year in `seq $(($start + 1)) $(($start + 4))`; do
  if test $year -gt `date +%Y`; then
    break
  fi
  for month in 01 02 03 04 05 06 07 08 09 10 11 12; do
    if test $year -eq `date +%Y`; then
     if test $month -gt `date +%m`; then
      break
     fi
    fi
    php symfony top:Deputes $year-$month-01
  done
done

