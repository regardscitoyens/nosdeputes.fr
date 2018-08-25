#!/usr/bin/env python
# -*- coding: utf8 -*-
# Ce script extrait les présences en commission du Journal Officiel
# Usage : parse_jo.py chamber [day] [stdout]
# "chamber" peut prendre pour valeur : "an" ou "senat" ; "day" doit être une date de la forme "2016-04-15", si omis, la date du jour sera utilisée ; "stdout" permet d'afficher la sortie du script plutôt que de créer un fichier dans ./json
import re, os, sys, json
import requests
from datetime import date, time, datetime
from bs4 import BeautifulSoup

def date_iso(datestr):
  month = {
    u"janvier": "01",
    u"février": "02",
    u"mars": "03",
    u"avril": "04",
    u"mai": "05",
    u"juin": "06",
    u"juillet": "07",
    u"août": "08",
    u"septembre": "09",
    u"octobre": "10",
    u"novembre": "11",
    u"décembre": "12",
  }
  d = datestr.split(' ')
  if d[1] == '1er':
    d[1] = '01'
  if len(d[1]) == 1:
    d[1] = '0'+d[1]
  dateiso = d[3][0:4]+'-'+month[d[2]]+'-'+d[1]
  if re.search(reg['date'], dateiso) is not None:
    return dateiso
  else:
    return datestr

reg = {}
reg['date'] = '^([0-9]{4})-([0-9]{2})-([0-9]{2})$'
reg['com'] = '^Commissions'
reg['start_an'] = u'^[0-9]{0,2}\. Membres présents ou excusés'
reg['start_senat'] = u'^Membres'
reg['commission'] = u'(.*) :$'
reg['reunion_an'] = u'^Réunion du (.*) ?à (.*) :'
reg['reunion_senat'] = u'^(.{1,6}éance) du (.*) :'
reg['reunion_senat_bis'] = u'^(.*), séance du (.*)$'
reg['presents'] = u'^Présents.* ?(-|:) (.*)'
reg['excuses'] = u'^Excusé.*(-|:) (.*)'
reg['assistent'] = u'^Assistai.* (-|:) (.*)'
reg['civilite'] = u' ?(Mme|M\.) '
reg['fonction_senat'] = u' \([^)]*\)'

# Paramètres
try:
  chamber = sys.argv[1]
except IndexError:
  sys.exit('Le 1er argument est obligatoire, il doit être "an ou "senat"')
try:
  day = sys.argv[2]
except IndexError:
  day = str(datetime.now().date())
try:
  stdout = sys.argv[3]
  stdout = True
except IndexError:
  stdout = False

prefix = 'https://www.legifrance.gouv.fr'

if chamber == 'an':
  text_link = u'Commissions et organes de contrôle'
elif chamber == 'senat':
  text_link = u'Commissions'
else:
  sys.exit('Le 1er argument doit être "an" ou "senat"')

if re.search(reg['date'], day) is not None:
  m = re.search(reg['date'], day)
  date_fr = m.group(3)+'/'+m.group(2)+'/'+m.group(1)
  jo_eli = prefix+'/eli/jo/'+m.group(1)+'/'+str(int(m.group(2)))+'/'+str(int(m.group(3)))
else:
  sys.exit('Le 2ème argument doit être une date de la forme "2016-04-15"')

# Repertoires
if not os.path.exists('./html'):
  os.makedirs('./html')
if not os.path.exists('./json'):
  os.makedirs('./json')

# Sommaire JO
print jo_eli
#summary = requests.get(jo_eli, verify=False).text
summary = requests.api.request('get', jo_eli, json=None, verify=False)
soup = BeautifulSoup(summary, "lxml")

sys.stderr.write(chamber.upper()+' '+date_fr+' '+jo_eli+'\n')
if soup.title.string.strip().startswith(u'Recherche'):
  sys.exit(' no JO')

else:
  # Sauvegarde sommaire
  with open("html/sommaire_"+day+".html", "wb") as file:
    file.write(soup.prettify("utf-8"))

  data = {}
  commission_link = False

  for link in soup.find_all('a'):
    link_string = unicode(link.string).strip()
    if re.search(reg['com'], link_string, re.IGNORECASE) is not None:

      data['source'] = 'Journal officiel du '+date_fr

      # Commission
      if link_string == text_link:
        commission_link = True
        n_presences = 0
        com_link = prefix+link.get('href')
        coms_doc = requests.get(com_link, verify=False).text
        soup = BeautifulSoup(coms_doc, "lxml")

        # Sauvegarde commissions
        with open("html/coms_"+chamber+"_"+day+".html", "wb") as file:
          file.write(soup.prettify("utf-8"))

        t = soup.find_all("div", "article")

        for br in t[0].findAll('br'):
          br.replace_with(os.linesep)

        on = False

        com_text = ''

        for line in t[0].get_text().split(os.linesep):
          line = line.strip()

          # Détecter début
          if re.search(reg['start_'+chamber], line, re.IGNORECASE) is not None:
            on = True

          # Pre-process
          if on and line:
            if line.startswith(u'Présent') is False and line.startswith(u'Excusé') is False and line.startswith(u'Assistai') is False and line.startswith(u'Ont') is False and line.startswith(u'Les') is False and line.startswith(u'ERRATUM') is False and line.endswith(u' :') is False:
              line = line+u' :'

            if line.startswith(u'Les') and line.endswith(u' :'):
              line = line[0:-2]

            com_text += line+os.linesep

        com_text = com_text.replace(u"’", u"'")

        json_file = ''

        for line in com_text.split(os.linesep):

          if re.search(reg['commission'], line) is not None:

            if re.search(reg['reunion_an'], line, re.IGNORECASE) is not None:
              m = re.search(reg['reunion_an'], line, re.IGNORECASE)
              data['reunion'] = date_iso(m.group(1))
              data['session'] = m.group(2).replace(' :', '').replace(' h ', ':').replace(' heures', ':00')[0:5]
            elif re.search(reg['reunion_senat'], line, re.IGNORECASE) is not None:
              m = re.search(reg['reunion_senat'], line, re.IGNORECASE)
              data['date'] = date_iso(m.group(2))
              data['heure'] = m.group(1).replace(u'Séance', '')
            else:
              m = re.search(reg['commission'], line)
              data['commission'] = re.sub(':', '', m.group(1)).strip()

              if chamber == "senat" and re.search(reg['reunion_senat_bis'], data['commission'], re.IGNORECASE):
                m = re.search(reg['reunion_senat_bis'], data['commission'], re.IGNORECASE)
                data['date'] = date_iso(m.group(2))
                data['heure'] = ''
                data['commission'] = m.group(1)

          if re.search(reg['presents'], line, re.IGNORECASE) is not None:
            m = re.search(reg['presents'], line, re.IGNORECASE)
            presents = re.sub(reg['civilite'], "", m.group(2))

            for present in presents.split(','):
              if chamber == "senat":
                data['senateur'] = present.strip().strip('.')
              else:
                data['depute'] = present.strip().strip('.')
              json_file += json.dumps(data, separators=(',',':'), ensure_ascii=False, sort_keys=True)+os.linesep
              n_presences += 1

          if re.search(reg['assistent'], line, re.IGNORECASE) is not None:
            m = re.search(reg['assistent'], line, re.IGNORECASE)
            presents = re.sub(reg['civilite'], "", m.group(2))
            if chamber == "senat":
              presents = re.sub(reg['fonction_senat'], "", presents)

            for present in presents.split(','):
              if chamber == "senat":
                data['senateur'] = present.strip().strip('.')
              else:
                data['depute'] = present.strip().strip('.')
              json_file += json.dumps(data, separators=(',',':'), ensure_ascii=False, sort_keys=True)+os.linesep
              n_presences += 1

        if not json_file:
          sys.exit(' no attendance '+com_link)
        else:
          sys.stderr.write(str(n_presences)+' présences '+com_link+'\n')
          if stdout:
            print(json_file.strip().encode('utf-8'))
          else:
            with open("json/"+chamber+"_"+day+".json", "wb") as file:
              file.write(json_file.strip().encode('utf-8'))

if not commission_link:
  sys.exit(' no commission ')


