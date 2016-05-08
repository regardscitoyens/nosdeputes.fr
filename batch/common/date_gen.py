#!/usr/bin/env python
# -*- coding: utf8 -*-
# Usage 'date_gen.py from_date [to_date]'
# Ce script utilisera la date d'aujourd'hui si "to_date" n'est pas précisé
import re, sys
from datetime import date, timedelta

re_date = re.compile('^([0-9]{4})-([0-9]{2})-([0-9]{2})$')

if len(sys.argv) > 1 and re_date.search(sys.argv[1]):
  m = re_date.search(sys.argv[1])
  from_date = date(int(m.group(1)), int(m.group(2)), int(m.group(3)))
else:
  sys.exit('Le 1er argument doit être la date de début (de la forme 2016-04-13)')

try:
  to_date = sys.argv[2]
  if re_date.search(to_date):
    m = re_date.search(to_date)
    to_date = date(int(m.group(1)), int(m.group(2)), int(m.group(3)))
  else:
    sys.exit('Le 2nd argument (optionnel) doit être la date de fin (de la forme 2016-04-13)')
except IndexError:
  to_date = date.today()

if to_date < from_date:
  sys.exit('La date de début doit être antérieure à la date de fin')

date = from_date
while date <= to_date:
  print(date)
  date += timedelta(days=1)
