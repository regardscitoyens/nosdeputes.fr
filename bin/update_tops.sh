#!/bin/bash

php symfony top:Deputes
for month in 06 07 08 09 10 11 12; do
  php symfony top:Deputes 2007-$month-01
done
for year in 2008 2009 2010; do
  for month in 01 02 03 04 05 06 07 08 09 10 11 12; do
    php symfony top:Deputes $year-$month-01
  done
done


