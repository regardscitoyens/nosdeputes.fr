#!/usr/bin/env python
# -*- coding: utf8 -*-
# Usage 'date_gen.py from_date [to_date]'
# Ce script utilisera la date d'aujourd'hui si "to_date" n'est pas précisé
import re, sys, calendar
from datetime import date, time, datetime

from_date = sys.argv[1]

reg = {}
reg['date'] = '^([0-9]{4})-([0-9]{2})-([0-9]{2})$'

if re.search(reg['date'], from_date) is not None:
  m = re.search(reg['date'], from_date)
  from_date = datetime(int(m.group(1)), int(m.group(2)), int(m.group(3))).date()
else:
  sys.exit('Le 1er argument doit être la date de début (de la forme 2016-04-13)')

try:
  to_date = sys.argv[2]
  if re.search(reg['date'], to_date) is not None:
    m = re.search(reg['date'], to_date)
    to_date = datetime(int(m.group(1)), int(m.group(2)), int(m.group(3))).date()
  else:
    sys.exit('Le 2nd argument (optionnel) doit être la date de fin (de la forme 2016-04-13)')
except IndexError:
  to_date = datetime.now().date()

cal = calendar.Calendar()

for year in range(from_date.year, to_date.year+1):

  # 1ère année
  if from_date.year == year:
    start_month = from_date.month
  else:
    start_month = 1

  # dernière année
  if to_date.year == year:
    stop_month = to_date.month
  else:
    stop_month = 12

  for month in range(start_month, stop_month+1):

    for day in cal.itermonthdates(year, month):

      if day >= from_date and day.month == month and day <= to_date:
        print(day)
