#!/bin/bash

. $(echo $0 | sed 's/[^\/]*$//')db.inc
cd $PATH_APP
start=$(($LEGISLATURE * 5 + 1942))

php symfony top:Deputes
for month in 06 07 08 09 10 11 12; do
  if [ $start -eq `date +%Y` ] && [ $month -gt `date +%m` ]; then
    break
  fi
  php symfony top:Deputes $start-$month-01
done
for year in `seq $(($start + 1)) $(($start + 5))`; do
  if [ $year -gt `date +%Y` ]; then
    break
  fi
  for month in 01 02 03 04 05 06 07 08 09 10 11 12; do
    if [ $year -eq `date +%Y` ] && [ $month -gt `date +%m` ]; then
      break
    fi
    php symfony top:Deputes $year-$month-01
  done
done

