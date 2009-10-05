#!/usr/bin/python
# vim: set fileencoding=latin1 :


dptmts = dict({
 "Ain": "001",
 "Aisne": "002",
 "Allier": "003",
 "Alpes-de-Haute-Provence": "004",
 "Hautes-Alpes": "005",
 "Alpes-Maritimes": "006",
 "Ardèche": "007",
 "Ardennes": "008",
 "Ariège": "009",
 "Aube": "010",
 "Aude": "011",
 "Aveyron": "012",
 "Bouches-du-Rhône": "013",
 "Calvados": "014",
 "Cantal": "015",
 "Charente": "016",
 "Charente-Maritime": "017",
 "Cher": "018",
 "Corrèze": "019",
 "Corse-du-Sud": "02a",
 "Haute-Corse": "02b",
 "Côte-d'Or": "021",
 "Côtes-d'Armor": "022",
 "Creuse": "023",
 "Dordogne": "024",
 "Doubs": "025",
 "Drôme": "026",
 "Eure": "027",
 "Eure-et-Loir": "028",
 "Finistère": "029",
 "Gard": "030",
 "Haute-Garonne": "031",
 "Gers": "032",
 "Gironde": "033",
 "Hérault": "034",
 "Ille-et-Vilaine": "035",
 "Indre": "036",
 "Indre-et-Loire": "037",
 "Isère": "038",
 "Jura": "039",
 "Landes": "040",
 "Loir-et-Cher": "041",
 "Loire": "042",
 "Haute-Loire": "043",
 "Loire-Atlantique": "044",
 "Loiret": "045",
 "Lot": "046",
 "Lot-et-Garonne": "047",
 "Lozère": "048",
 "Maine-et-Loire": "049",
 "Manche": "050",
 "Marne": "051",
 "Haute-Marne": "052",
 "Mayenne": "053",
 "Meurthe-et-Moselle": "054",
 "Meuse": "055",
 "Morbihan": "056",
 "Moselle": "057",
 "Nièvre": "058",
 "Nord": "059",
 "Oise": "060",
 "Orne": "061",
 "Pas-de-Calais": "062",
 "Puy-de-Dôme": "063",
 "Pyrénées-Atlantiques": "064",
 "Hautes-Pyrénées": "065",
 "Pyrénées-Orientales": "066",
 "Bas-Rhin": "067",
 "Haut-Rhin": "068",
 "Rhône": "069",
 "Haute-Saône": "070",
 "Saône-et-Loire": "071",
 "Sarthe": "072",
 "Savoie": "073",
 "Haute-Savoie": "074",
 "Paris": "075",
 "Seine-Maritime": "076",
 "Seine-et-Marne": "077",
 "Yvelines": "078",
 "Deux-Sèvres": "079",
 "Somme": "080",
 "Tarn": "081",
 "Tarn-et-Garonne": "082",
 "Var": "083",
 "Vaucluse": "084",
 "Vendée": "085",
 "Vienne": "086",
 "Haute-Vienne": "087",
 "Vosges": "088",
 "Yonne": "089",
 "Territoire-de-Belfort": "090",
 "Essonne": "091",
 "Hauts-de-Seine": "092",
 "Seine-Saint-Denis": "093",
 "Val-de-Marne": "094",
 "Val-d'Oise": "095",
 "Guadeloupe": "971",
 "Martinique": "972",
 "Guyane": "973",
 "Réunion": "974",
 "Saint-Pierre-et-Miquelon": "975",
 "Mayotte": "976",
 "Saint-Barthélémy": "977",
 "Saint-Martin": "978",
 "Wallis-et-Futuna": "986",
 "Polynésie Française": "987",
 "Nouvelle-Calédonie": "988"
})

def IsInt( str ):
	""" Is the given string an integer?	"""
	ok = 1
	try:
		num = int(str)
	except ValueError:
		ok = 0
	return ok


partis = dict()

import MySQLdb

conn = MySQLdb.connect (host = "localhost",
                        user = "cpc",
                        passwd = "M_O_T__D_E__P_A_S_S_E",
                        db = "cpc")
cursor = conn.cursor ()
cursor.execute ("SELECT groupe_acronyme, nom_circo, num_circo, fin_mandat FROM parlementaire")
while (1):
  row = cursor.fetchone ()
  if row == None:
    break
  if row[3] == None:
    partis[dptmts[row[1]]+"-%.2d" % row[2]] = row[0]
cursor.close ()
conn.close ()

colors = dict({
   "GDR": "00aa00",
   "SRC": "ff14a0",
   "UMP": "0000aa",
   "NC": "00a0ff",
   "NI": "dcdcdc",
   "UNKNOWN": "000000"
})

import os
import re
import sys
import xml.dom.minidom
import string

if len(sys.argv) == 0:
    sys.exit("svn2imagemap.py FILENAME")
if not os.path.exists(sys.argv[1]+".svg"):
    sys.exit("Input file does not exist")

svg_file = xml.dom.minidom.parse(sys.argv[1]+".svg")
svg = svg_file.getElementsByTagName('svg')[0]


elements = [g for g in svg.getElementsByTagName('g')]

for e in elements:
    for path in e.getElementsByTagName('path'):
        m = re.match("...-..", path.attributes['id'].value)
        if m != None and m.group(0) in partis:
          print partis[m.group(0)]
          if partis[m.group(0)] in colors:
             color = colors[partis[m.group(0)]]
          else:
             color = colors["UNKNOWN"]
          path.setAttribute('style',"fill:#"+color+";fill-rule:evenodd;stroke:#"+color+";stroke-width:1.57843995px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1")
        else:
            print path.attributes['id'].value
    
outfile = open(sys.argv[1]+"-colors.svg", 'w')
outfile.write(svg_file.toxml().encode('utf-8'))
outfile.close()
