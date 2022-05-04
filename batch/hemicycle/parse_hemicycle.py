#!/usr/bin/env python
# -*- coding: utf8 -*-
import sys
import bs4
import json
import re

intervenant2fonction = {}
intervenant2url = {}

def clean_all(text):
    text = text.replace("’", "'")
    text = text.replace("<br/>", " ")
    text = text.replace('\n', ' ')
    text = re.sub(r'\s+', ' ', text)
    return text.strip()

def clean_intervenant(interv):
    interv = clean_all(interv)
    interv = re.sub(r'^M(\.|me)\s+', '', interv)
    # cleanup parenthesis from intervenant (groupe)
    interv = re.sub(r'\s+\(\s*[A-Z][\w\s\-]+\)$', '', interv)
    return interv

def xml2json(s):
    global timestamp
    timestamp = 0
    s = s.replace(u'\xa0', u' ')
    soup = bs4.BeautifulSoup(s, features="lxml")
    intervention_vierge = {"intervenant": "", "contexte": ""}
    intervention_vierge["source"] = source_url or "https://www.assemblee-nationale.fr/dyn/15/comptes-rendus/seance/"+soup.uid.string
    m = soup.metadonnees
    dateseance = str(m.dateseance.string)
    intervention_vierge["date"] = "%04d-%02d-%02d" % (int(dateseance[0:4]), int(dateseance[4:6]), int(dateseance[6:8]))
    intervention_vierge["heure"] = "%02d:%02d" % (int(dateseance[8:10]), int(dateseance[10:12]))
    session = str(m.session.string)
    session = re.sub(r"\D", "", session)
    if len(session) == 4:
        session = "%s%s" % (session, int(session) + 1)
    intervention_vierge["session"] = session
    contextes = ['']
    numeros_lois = None
    last_titre = ''

    # Preload detailed gouv fonctions from sommaire
    for p in soup.find_all(['para']):
        section = p.parent.parent.titrestruct.intitule.get_text()
        orateur = p.get_text()
        if "," in orateur and "rapporteur" not in orateur and "commission" not in orateur:
            orateur, fonction = orateur.split(', ', 1)
            orateur = clean_intervenant(orateur)
            fonction = clean_all(fonction)
            existing = intervenant2fonction.get(orateur)
            if not existing or fonction.startswith(existing):
                intervenant2fonction[orateur] = fonction

    for p in soup.find_all(['paragraphe', 'point']):
        contexte = ""
        intervention = intervention_vierge.copy()
        # Gestion des titres/contextes et numéros de loi
        if p.name == "point":
            contexte = clean_all(str(p.texte))
            contexte = re.sub(r'<\/?[a-z][^>]*>', '', contexte)
            contexte = re.sub(r'\s*\(suite\)\.?$', '', contexte)
            contexte = re.sub(r'\s*-\s*suite\)\.?$', ')', contexte)
            contexte = clean_all(contexte)

            if contexte and int(p['nivpoint']) < 4:
                contextes = contextes[:int(p['nivpoint'])-1]
                if not contextes:
                    contextes = []

                if not re.match(r"Suite\s*de\s*la\s*discussion|Rappels?\s*au\s*règlement|Suspension|Reprise\s*de\s*la\s*séance|Faits? personnel|Demandes? de vérification du quorum", contexte):
                    contextes.append(contexte)

                # Clean rapporteurs when changing section
                if len(contextes) == 1:
                    for (orateur, fonction) in intervenant2fonction.copy().items():
                        if "rapporteur" in fonction or "commission" in fonction:
                            del(intervenant2fonction[orateur])

        if p['valeur'] and p['valeur'][0:9] == ' (n[[o]] ':
            numeros_lois = p['valeur'][9:-1].replace(' ', '')
        if numeros_loi and not re.search(r"questions?\sau|ordre\sdu\sjour|bienvenue|hommage|annulation|(proclam|nomin)ation|suspension\sde\séance|rappels?\sau\srèglement", intervention["contexte"], re.I):
            intervention['numeros_loi'] = numeros_loi

        intervention["source"] += "#"+p['id_syceron']

        if len(contextes) > 1:
            intervention["contexte"] = contextes[0] + " > " + contextes[-1]
        elif len(contextes) == 1:
            intervention["contexte"] = contextes[0]

        if p.name == "point":
            if contexte:
                intervention['intervention'] = "<p>"+contexte+"</p>"
                if (last_titre != contexte):
                    printintervention(intervention)
                last_titre = contexte
            continue

        # Gestion des interventions
        # TODO rm numeros_lois from one texte to another without numeros
        if len(p.orateurs):
            # TODO handle cases with multiples orateurs (mostly to combine into one)
            # examples xml/compteRendu/CRSANR5L15S2021O1N068.xml
            # grep '</orateur>' -A 1 xml/compteRendu/* | grep -v '</orateur>' | grep '<orateur>'
            intervention["intervenant"] = clean_intervenant(p.orateurs.orateur.nom.get_text())
            if p['id_mandat'] and p['id_mandat'] != "-1":
                intervention["intervenant_url"] = "http://www2.assemblee-nationale.fr/deputes/fiche/OMC_"+p['id_acteur']
                intervenant2url[intervention["intervenant"]] = intervention['intervenant_url']
            existingfonction = intervenant2fonction.get(intervention["intervenant"])
            if p.orateurs.orateur.qualite and p.orateurs.orateur.qualite.string:
                intervention['fonction'] = clean_all(p.orateurs.orateur.qualite.get_text())
                if not existingfonction and intervention['fonction']:
                    intervenant2fonction[intervention["intervenant"]] = intervention['fonction']
                elif existingfonction:
                    if existingfonction.startswith(intervention['fonction']) and len(existingfonction) > len(intervention['fonction']):
                        intervention["fonction"] = existingfonction
                    elif intervention['fonction'].startswith(existingfonction) and len(intervention['fonction']) > len(existingfonction):
                        intervenant2fonction[intervention["intervenant"]] = intervention['fonction']
            elif intervention["intervenant"] == "la présidente":
                intervention['fonction'] = "présidente"
            elif intervention["intervenant"] == "le président":
                intervention['fonction'] = "président"
            elif existingfonction:
                intervention['fonction'] = intervenant2fonction[intervention["intervenant"]]

        isdidascalie = False
        texte_didascalie = ""
        t_string = clean_all(str(p.texte))
        t_string = re.sub(r' ?<\/?texte> ?', '', t_string)
        t_string = t_string.replace('<italique>', '<i>')
        t_string = t_string.replace('</italique>', '</i>')
        t_string = t_string.replace('</i> <i>', '')
        t_string = t_string.replace('<br/>', '</p><p>')
        t_string = t_string.replace('<p></p>', '')
        t_string = re.sub(r'\s+', ' ', t_string)
        t_string = re.sub(r'n[° ]*(<exposant>[os]+</exposant>\s*)+', 'n° ', t_string)
        if not t_string:
            continue
        texte = "<p>%s</p>" % t_string

        i = 0
        # TODO: handle more missing inside didascalies
        curinterv = intervention.copy()
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
                didasc = intervention_vierge.copy()
                i_str = re.sub(r"<i>[\s(]*", "", i)
                i_str = re.sub(r"[\s)]*</i>", "", i_str)
                didasc["intervention"] = i_str
                didasc["contexte"] = intervention["contexte"]
                didasc["source"] = intervention["source"]
                if intervention.get("numeros_loi"):
                    didasc["numeros_loi"] = intervention["numeros_loi"]
                printintervention(didasc)
            else:
                intervention = curinterv.copy()
                intervention["intervention"] = i
                printintervention(intervention)

def printintervention(i):
    global timestamp
    if re.match(r'(<p>\s*</p>\s*)+$', i['intervention']):
        return
    intervenants = i['intervenant'].split(' et ')
    timestamp += 10
    if len(intervenants) > 1:
        print("WARNING, multiple interv: %s" % i, file=sys.stderr)
        if intervenants[0].startswith("Plusieurs députés"):
            intervenants[0] = intervenants[0].replace("des groupes", "du groupe")
            radical = re.sub(r"^(.*?\s)[A-Z].*$", r"\1", intervenants[0])
            for idx in range(1, len(intervenants)):
                intervenants[idx] = radical + intervenants[idx]
    curtimestamp = timestamp
    for intervenant in intervenants:
        i['timestamp'] = str(curtimestamp)
        curtimestamp += 1
        # extract function from split intervenants
        if intervenant.find(','):
            intervenantfonction = intervenant.split(', ', 1)
            intervenant = intervenantfonction[0]
            if len(intervenantfonction) > 1:
                i['fonction'] = clean_all(intervenantfonction[1])
        elif intervenant2fonction.get(i['intervenant']):
            i['fonction'] = intervenant2fonction[i['intervenant']]
        i['intervenant'] = clean_intervenant(intervenant)
        if (intervenant2url.get(i['intervenant'])):
            i['intervenant_url'] = intervenant2url[i['intervenant']]
        print(json.dumps(i, ensure_ascii=False))
        if (i.get('fonction')):
            del i['fonction']
        if (i.get('intervenant_url')):
            del i['intervenant_url']

content_file = sys.argv[1]
source_url = ''
if (len(sys.argv) > 2):
    source_url = sys.argv[2]
with open(content_file, encoding='utf-8') as f:
    xml2json(f.read())
