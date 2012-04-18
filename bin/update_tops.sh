#!/bin/bash

cd /home/nossenateurs/prod

php symfony top:Senateurs
for month in 10 11 12; do
  php symfony top:Senateurs 2004-$month-01
done
for year in `seq 2005 $(date +%Y)`; do
  for month in 01 02 03 04 05 06 07 08 09 10 11 12; do
    if test $year -eq `date +%Y`; then
     if test $month -gt `date +%m`; then
      break
     fi
    fi
    php symfony top:Senateurs $year-$month-01
  done
done


