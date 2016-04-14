#!/usr/bin/env python
# -*- coding: utf8 -*-
import re, os, sys, urllib2, json
from datetime import date, time, datetime
from bs4 import BeautifulSoup

def date_iso(datestr):
  month = {
    "janvier": "01",
    "février": "02",
    "mars": "03",
    "avril": "04",
    "mai": "05",
    "juin": "06",
    "juillet": "07",
    "août": "08",
    "septembre": "09",
    "octobre": "10",
    "novembre": "11",
    "décembre": "12",
  }
  d = datestr.split(' ')
  if d[1] == '1er':
    d[1] = '01'
  if len(d[1]) == 1:
    d[1] = '0'+d[1]
  dateiso = d[3]+'-'+month[d[2]]+'-'+d[1]
  if re.search(reg['date'], dateiso) is not None:
    return dateiso
  else:
    return datestr

reg = {}
reg['date'] = '^([0-9]{4})-([0-9]{2})-([0-9]{2})$'
reg['com'] = '^Commissions'
reg['start'] = u'^[0-9]{1,2}\. Membres présents ou excusés'
reg['commission'] = u'(.*) :$'
reg['reunion'] = u'^Réunion du (.*), à (.*) :'
reg['presents'] = u'^Présents. - (.*)'
reg['excuses'] = u'^Excusés?. - (.*)'
reg['assistent'] = u'^Assistai.* - (.*)'
reg['civilite'] = u' ?(Mme|M\.) '

# Paramètres

chamber = sys.argv[1]

try:
  day = sys.argv[2]
except IndexError:
  day = datetime.now().date()

prefix = 'https://www.legifrance.gouv.fr'

if chamber == 'an':
  text_link = u'Commissions et organes de contrôle'
elif chamber == 'senat':
  text_link = u'Commissions'
else:
  sys.exit(fail)

if re.search(reg['date'], day) is not None:
  m = re.search(reg['date'], day)
  date_fr = m.group(3)+'/'+m.group(2)+'/'+m.group(1)
  jo_eli = prefix+'/eli/jo/'+m.group(1)+'/'+str(int(m.group(2)))+'/'+str(int(m.group(3)))
else:
  sys.exit('fail')


summary = urllib2.urlopen(jo_eli)
soup = BeautifulSoup(summary.read(), "lxml-xml")

# Sauvegarde sommaire
with open("html/sommaire_"+day+".html", "wb") as file:
  file.write(soup.prettify("utf-8"))

data = {}
jo = False

for link in soup.find_all('a'):
  link_string = unicode(link.string).strip()
  if re.search(reg['com'], link_string, re.IGNORECASE) is not None:
    jo = True

    data['source'] = 'Journal officiel du '+date_fr

    if link_string == text_link:
      coms_doc = urllib2.urlopen(prefix+link.get('href'))
      soup = BeautifulSoup(coms_doc.read(), "lxml-xml")

      # Sauvegarde commissions
      with open("html/coms_doc_"+day+".html", "wb") as file:
        file.write(soup.prettify("utf-8"))

      t = soup.find_all("div", "article")

      for br in t[0].findAll('br'):
        br.replace_with("\n")

      on = False

      for line in t[0].get_text().split(os.linesep):
        line = line.strip()

        # Détecter début
        if re.search(reg['start'], line, re.IGNORECASE) is not None:
          on = True

        if on:
          if re.search(reg['commission'], line) is not None:

            if re.search(reg['reunion'], line, re.IGNORECASE) is not None:
              m = re.search(reg['reunion'], line, re.IGNORECASE)
              data['reunion'] = date_iso(m.group(1))
              data['session'] = m.group(2).replace(' h ', ':').replace(' heures', ':00')
            else:
              m = re.search(reg['commission'], line)
              data['commission'] = m.group(1)

          if re.search(reg['presents'], line, re.IGNORECASE) is not None:
            m = re.search(reg['presents'], line, re.IGNORECASE)
            presents = re.sub(reg['civilite'], "", m.group(1)).split(',')

            for present in presents:
              data['depute'] = present
              print(json.dumps(data, separators=(',',':')))

          if re.search(reg['assistent'], line, re.IGNORECASE) is not None:
            m = re.search(reg['assistent'], line, re.IGNORECASE)
            presents = re.sub(reg['civilite'], "", m.group(1)).split(',')

            for present in presents:
              data['depute'] = present
              print(json.dumps(data, separators=(',',':')))

if jo is False:
  print(date_fr+' '+jo_eli+' no commission link')
