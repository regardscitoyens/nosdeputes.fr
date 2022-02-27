#!/usr/bin/env python
# -*- coding: utf8 -*-
import sys
import bs4
import json
import re

def xml2json(s):
    global timestamp
    timestamp = 0
    s = s.replace(u'\xa0', u' ')
    soup = bs4.BeautifulSoup(s, features="lxml")
    intervention_vierge = {"intervenant": "", "contexte": ""}
    intervention_vierge["source"] =  "https://www.assemblee-nationale.fr/dyn/15/comptes-rendus/seance/"+soup.uid.string
    m = soup.metadonnees
    dateseance = str(m.dateseance.string)
    intervention_vierge["date"] = "%04d-%02d-%02d" % (int(dateseance[0:4]), int(dateseance[4:6]), int(dateseance[6:8]))
    intervention_vierge["heure"] = "%02d:%02d" % (int(dateseance[8:10]), int(dateseance[10:12]))
    intervention_vierge["session"] = str(m.session.string)[-9:].replace('-', '')
    contextes = ['']
    numeros_lois = None
    intervenant2fonction = {}
    last_titre = ''
    for p in soup.find_all(['paragraphe', 'point']):
        intervention = intervention_vierge.copy()
        #Gestion des titres/contextes et numéros de loi
        if p.name == "point" and p.texte and p.texte.get_text() and int(p['nivpoint']) < 4:
            contextes = contextes[:int(p['nivpoint']) -1 ]
            if not contextes:
                contextes = []
            contextes.append(p.texte.get_text().replace('\n', ''))
        if p['valeur'] and p['valeur'][0:9] == ' (n[[o]] ':
            numeros_lois = p['valeur'][9:-1].replace(' ', '')
        if len(contextes) > 1:
            intervention["contexte"] = contextes[0] + " > " + contextes[-1]
        elif len(contextes) == 1:
            intervention["contexte"] = contextes[0]
        if p.name == "point":
            intervention['intervention'] = "<p>"+contextes[-1]+"</p>"
            if (last_titre != contextes[-1]):
                printintervention(intervention)
            last_titre = contextes[-1]
            continue
        #Gestion des interventions
        if numeros_lois:
            intervention['numeros_loi'] = numeros_lois
        intervention["source"] += "#"+p['id_syceron']
        if len(p.orateurs):
            intervention["intervenant"] = p.orateurs.orateur.nom.get_text()
            if p['id_mandat'] and p['id_mandat'] != "-1":
                intervention["intervenant_url"] = "http://www2.assemblee-nationale.fr/deputes/fiche/OMC_"+p['id_acteur']
                intervention["intervenant"] = p['id_acteur']
            if p.orateurs.orateur.qualite and p.orateurs.orateur.qualite.string:
                intervention['fonction'] = p.orateurs.orateur.qualite.get_text()
                if not intervenant2fonction.get(intervention["intervenant"]) and intervention['fonction']:
                    intervenant2fonction[intervention["intervenant"]] = intervention['fonction']
            elif intervention["intervenant"] == "Mme la présidente":
                intervention['fonction'] = "présidente"
                intervention["intervenant"] = '';
            elif intervention["intervenant"] == "M le président":
                intervention['fonction'] = "président"
                intervention["intervenant"] = '';
            else:
                intervention['fonction'] = intervenant2fonction.get(intervention["intervenant"], "")

        texte = "<p>"
        isdidascalie = False
        texte_didascalie = ""
        t_string = str(p.texte)
        t_string = t_string.replace('>\n', '> ')
        t_string = re.sub(r' ?<\/?texte> ?', '', t_string)
        t_string = t_string.replace('<italique>', '<i>')
        t_string = t_string.replace('</italique>', '</i>')
        t_string = t_string.replace('n<exposant>o</exposant>', 'n°')
        t_string = t_string.replace('n<exposant>os</exposant>', 'n°')
        t_string = t_string.replace('</i> <i>', ' ')
        t_string = t_string.replace('<br/>', '</p><p>')
        texte += t_string
        texte += "</p>"
        i = 0;
        for i in re.split(' ?(<i>\([^<]*\)</i> ?)', texte):
            if i[0] == ' ':
                i = i[1:]
            if i[-1] == ' ':
                i = i[:-1]
            if (i[0:3] !=  '<p>'):
                i = '<p>' + i
            if (i[-4:] !=  '</p>'):
                i = i + '</p>'
            if i.find('<p><i>') == 0:
                didasc = intervention_vierge
                didasc["intervention"] = i
                didasc["contexte"] = intervention["contexte"]
                printintervention(didasc)
            else:
                intervention["intervention"] = i
                printintervention(intervention)

def printintervention(i):
    global timestamp
    if i['intervention'] == '<p></p>' or i['intervention'] == '<p> </p>':
        return
    intervenants = i['intervenant'].split(' et ')
    timestamp += 10
    for intervenant in intervenants:
        i['timestamp'] = str(timestamp)
        i['intervenant'] = intervenant
        print(json.dumps(i))

content_file = sys.argv[1]
with open(content_file, encoding='utf-8') as f:
    xml2json(f.read())
