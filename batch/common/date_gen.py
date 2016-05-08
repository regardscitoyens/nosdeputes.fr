#!/usr/bin/env python
# -*- coding: utf8 -*-
# Usage 'date_gen.py from_date [to_date] [-s/--skip-first]'
# Ce script utilisera la date d'aujourd'hui si "to_date" n'est pas précisé
import re, sys
from datetime import date, timedelta

re_date = re.compile('^([0-9]{4})-([0-9]{2})-([0-9]{2})$')

if len(sys.argv) > 1 and re_date.search(sys.argv[1]):
  m = re_date.search(sys.argv[1])
  from_date = date(int(m.group(1)), int(m.group(2)), int(m.group(3)))
else:
  sys.exit('Le 1er argument doit être la date de début (de la forme 2016-04-13)')

def test_skiparg(i, fromdate):
    if len(sys.argv) > i and sys.argv[i] in ['-s', '--skip-first']:
        fromdate += timedelta(days=1)
        i += 1
    return i, fromdate

idx, from_date = test_skiparg(2, from_date)
try:
  m = re_date.search(sys.argv[idx])
  if m:
    to_date = date(int(m.group(1)), int(m.group(2)), int(m.group(3)))
  else:
    sys.exit('Le 2nd argument (optionnel) doit être la date de fin (de la forme 2016-04-13)')
except IndexError:
  to_date = date.today()
idx, from_date = test_skiparg(idx+1, from_date)

if to_date < from_date:
  sys.exit('La date de début doit être antérieure à la date de fin')

day = from_date
while day <= to_date:
  print(day)
  day += timedelta(days=1)
