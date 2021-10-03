#!/usr/bin/env python
# -*- coding: utf8 -*-
import sys
import bs4
import json

def xml2json(s):
    global timestamp
    timestamp = 0
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
            printintervention(intervention)
            continue
        #Gestion des interventions
        if numeros_lois:
            intervention['numeros_loi'] = numeros_lois
        intervention["source"] += "#"+p['id_syceron']
        if len(p.orateurs):
            intervention["intervenant"] = p.orateurs.orateur.nom.get_text()
            if p['id_mandat'] and p['id_mandat'] != "-1":
                intervention["intervenant_url"] = "https://www2.assemblee-nationale.fr/deputes/fiche/OMC_"+p['id_acteur']
            if p.orateurs.orateur.qualite and p.orateurs.orateur.qualite.string:
                intervention['fonction'] = p.orateurs.orateur.qualite.get_text()
                if not intervenant2fonction.get(intervention["intervenant"]) and intervention['fonction']:
                    intervenant2fonction[intervention["intervenant"]] = intervention['fonction']
            elif intervention["intervenant"] == "Mme la présidente":
                intervention['fonction'] = "présidente"
            elif intervention["intervenant"] == "M le président":
                intervention['fonction'] = "président"
            else:
                intervention['fonction'] = intervenant2fonction.get(intervention["intervenant"], "")
                    
        texte = "<p>"
        isdidascalie = False
        texte_didascalie = ""
        for t in p.texte.childGenerator():
            t_string = str(t.string)
            if isinstance(t,  bs4.element.NavigableString):
                texte += t.string
            elif t.name == 'br':
                texte += "</p><p>"
            #Cas des didascalies
            elif t.name == 'italique' and (t_string[0] == '(' or isdidascalie):
                if not isdidascalie:
                    intervention["intervention"] = texte+"</p>"
                    printintervention(intervention)
                isdidascalie = True
                texte_didascalie += t.get_text();
                texte_didascalie = texte_didascalie.replace('(', '').replace(')', '')
                if t_string[-1] == ')':
                    didascalie = intervention_vierge.copy()
                    didascalie['intervention'] = "<p>"+texte_didascalie+"</p>"
                    printintervention(didascalie)
                    isdidascalie = False
                texte = "<p>"
            elif t.string and t_string != '\n':
                texte += t.string
            texte = texte.replace('\n', '')
        texte += "</p>"
        intervention["intervention"] = texte            
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
