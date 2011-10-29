#!/bin/bash

cd /home/nossenateurs/prod

php symfony top:Senateurs
for month in 10 11 12; do
  php symfony top:Senateurs 2004-$month-01
done
for year in 2005 2006 2007 2008 2009 2010 2011 2012; do
  if test $year -gt `date +%Y`; then
    break
  fi
  for month in 01 02 03 04 05 06 07 08 09 10 11 12; do
    if test $year -eq `date +%Y`; then
     if test $month -gt `date +%m`; then
      break
     fi
    fi
    php symfony top:Senateurs $year-$month-01
  done
done


