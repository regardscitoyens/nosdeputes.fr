#!/usr/bin/env python
# -*- coding: utf8 -*-
# Ce script extrait les présences en commission du Journal Officiel
# Usage : parse_jo.py chamber [day] [stdout]
# "chamber" peut prendre pour valeur : "an" ou "senat" ; "day" doit être une date de la forme "2016-04-15", si omis, la date du jour sera utilisée ; "stdout" permet d'afficher la sortie du script plutôt que de créer un fichier dans ./json
import re, os, sys, urllib2, json, io
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

  njour_index = 1
  if d[0][0].isdigit() > 0:
      njour_index = 0

  if d[njour_index] == '1er':
    d[njour_index] = '01'
  if len(d[njour_index]) == 1:
    d[njour_index] = '0'+d[njour_index]
  dateiso = d[njour_index+2][0:4]+'-'+month[d[njour_index+1]]+'-'+d[njour_index]
  if re.search(reg['date'], dateiso) is not None:
    return dateiso
  else:
    return datestr

reg = {}
reg['date'] = '^([0-9]{4})-([0-9]{2})-([0-9]{2})$'
reg['check_an'] = u'>Assemblée nationale<'
reg['check_senat'] = u'>Sénat<'
reg['check_office'] = u'>Offices et délégations<'
reg['start_an'] = u'^[0-9]{0,2}\.? *Membres présents ou excusés'
reg['start_senat'] = u'^Membres'
reg['commission'] = u'^([^0-9].* .*) :$'
reg['reunion_an'] = u'^(?:Réunion|Séance) du (.*) ?à (.*) :'
reg['reunion_an_bis'] = u'^(?:Réunion|Séance) du (.*) :'
reg['reunion_senat'] = u'^(.{1,5}éance) du (.*) :'
reg['reunion_senat_bis'] = u'^(.*), séance du (.*)$'
reg['presents'] = u'^Présents?.*?[\-:]+\s*(.*)'
reg['presents_an'] = u'^Députés? [pP]résents?.*?[\-:]+\s*(.*)'
reg['presents_senat'] = u'^Sénateurs? [pP]résents?.*?[\-:]+\s*(.*)'
reg['excuses'] = u'^(?:Député|Sénateur)s?\s*[eE]xcusé.*?[\-:]+\s*(.*)'
reg['assistent'] = u'^Assistai.*?[\-:]+\s*(.*)'
reg['civilite'] = u' ?(Mme|M\.) '
reg['fonction_senat'] = u' \([^)]*\)'

# Paramètres
try:
  chamber = sys.argv[1]
except IndexError:
  sys.exit('Le 1er argument est obligatoire, il doit être "an ou "senat"')
try:
  file = sys.argv[2]
  joid = re.findall(r'(JORFA[^\.\/]*)', file, re.IGNORECASE)[0]
except IndexError:
  sys.exit('Le 2d argument est obligatoire, il doit être un fichier du JO')
try:
  stdout = sys.argv[3]
  stdout = True
except IndexError:
  stdout = False

prefix = 'https://www.legifrance.gouv.fr'
json_file = ''

with io.open(file, encoding="utf-8", mode='r') as xmlfile:

      xmlstring = xmlfile.read()

      if (re.search(reg['check_'+chamber], xmlstring, re.IGNORECASE)) is None and (re.search(reg['check_office'], xmlstring, re.IGNORECASE)) is None  :
          sys.exit('Ce XML concerne pas la chambre '+chamber)

      soup = BeautifulSoup(xmlstring, "lxml")

      d = soup.find_all("titre_txt")
      commission = ''
      if re.search(u'^Office parlementaire|^Délégation', d[0].get_text(), re.IGNORECASE):
          commission = d[0].get_text()

      d = soup.find_all("texte")
      day = d[0]['date_publi']
      if re.search(reg['date'], day) is not None:
          m = re.search(reg['date'], day)
          date_fr = m.group(3)+'/'+m.group(2)+'/'+m.group(1)
      else:
          sys.exit('Date de publication non trouvée dans le XML"')

      t = soup.find_all("bloc_textuel")

      for br in t[0].findAll('br'):
        br.replace_with(os.linesep)

      on = False

      com_text = ''

      data = {'source': 'Journal officiel du '+date_fr, 'commission': commission}
      n_presences = 0
      com_link = ''


      for line in t[0].get_text().split(os.linesep):
        line = line.strip().replace(u' ', ' ')

        # Détecter début
        if (re.search(reg['start_'+chamber], line, re.IGNORECASE) is not None
            or (re.search(reg['reunion_'+chamber], line, re.I) or re.search(reg['reunion_'+chamber+'_bis'], line, re.I))):
          on = True

        # Pre-process
        if on and line:
          if line.startswith(u'Présent') is False and line.startswith(u'Excusé') is False and line.startswith(u'Assistai') is False and line.startswith(u'Ont') is False and line.startswith(u'Les') is False and line.startswith(u'ERRATUM') is False and line.endswith(u' :') is False:
            line = line+u' :'

          if line.startswith(u'Les') and line.endswith(u' :'):
            line = line[0:-2]

          com_text += line+os.linesep

      com_text = com_text.replace(u"’", u"'")

      for line in com_text.split(os.linesep):

        #print >> sys.stderr, line.encode('utf-8')
        m = re.search(reg['presents'], line, re.IGNORECASE)
        if not m:
          m = re.search(reg['presents_'+chamber], line)
        if m:
          presents = re.sub(reg['civilite'], "", m.group(1))

          for present in presents.split(','):
            if chamber == "senat":
              data['senateur'] = present.strip('. :')
            else:
              data['depute'] = present.strip('. :')
            json_file += json.dumps(data, separators=(',',':'), ensure_ascii=False, sort_keys=True)+os.linesep
            n_presences += 1
        elif re.search(reg['assistent'], line, re.IGNORECASE) is not None:
          m = re.search(reg['assistent'], line, re.IGNORECASE)
          presents = re.sub(reg['civilite'], "", m.group(1))
          if chamber == "senat":
            presents = re.sub(reg['fonction_senat'], "", presents, re.I)

          for present in presents.split(','):
            if chamber == "senat":
              data['senateur'] = present.strip('. :')
            else:
              data['depute'] = present.strip('. :')
            json_file += json.dumps(data, separators=(',',':'), ensure_ascii=False, sort_keys=True)+os.linesep
            n_presences += 1

        elif (chamber == 'an' and re.search(reg['presents_senat'], line, re.I)) or (chamber == 'senat' and re.search(reg['presents_an'], line, re.I)):
          pass
        elif re.search(reg['excuses'], line) is not None:
          pass
        elif commission:
          pass
        elif re.search(reg['commission'], line) is not None:
          if re.search(reg['start_senat'], line, re.IGNORECASE) or line == u'Députés :':
            pass
          elif re.search(reg['reunion_an'], line, re.IGNORECASE) is not None:
            m = re.search(reg['reunion_an'], line, re.IGNORECASE)
            data['reunion'] = date_iso(m.group(1))
            data['session'] = m.group(2).replace(' :', '').replace(' h ', ':').replace(' heures', ':00')[0:5]
          elif re.search(reg['reunion_an_bis'], line, re.IGNORECASE) is not None:
            m = re.search(reg['reunion_an_bis'], line, re.IGNORECASE)
            data['reunion'] = date_iso(m.group(1))
            data['session'] = u"1ère réunion"
          elif re.search(reg['reunion_senat'], line, re.IGNORECASE) is not None:
            m = re.search(reg['reunion_senat'], line, re.IGNORECASE)
            data['date'] = date_iso(m.group(2))
            data['reunion'] = data['date']
            data['heure'] = m.group(1).replace(u'Séance', '') or u"1ère réunion"
            data['session'] = data['heure']
          else:
            m = re.search(reg['commission'], line)

            data['commission'] = re.sub(':', '', m.group(1)).strip()

            if chamber == "senat" and re.search(reg['reunion_senat_bis'], data['commission'], re.IGNORECASE):
              m = re.search(reg['reunion_senat_bis'], data['commission'], re.IGNORECASE)
              data['date'] = date_iso(m.group(2))
              data['heure'] = ''
              data['commission'] = m.group(1)

      if not n_presences:
        sys.stderr.write(' no attendance '+com_link+'\n')
      else:
        sys.stderr.write(str(n_presences)+' présences '+com_link+'\n')

if json_file:
  if stdout:
    print(json_file.strip().encode('utf-8'))
  else:
    with open("json/"+joid+".json", "wb") as file:
      file.write(json_file.strip().encode('utf-8'))
