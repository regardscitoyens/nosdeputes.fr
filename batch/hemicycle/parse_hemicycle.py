#!/usr/bin/env python
# -*- coding: utf8 -*-
import sys
import bs4
import json
import re

intervenant2fonction = {}
intervenant2url = {}
output = []

def clean_all(text):
    text = text.replace("’", "'")
    text = text.replace("&amp;", "&")
    text = text.replace("<br/>", " ")
    text = text.replace('\n', ' ')
    text = re.sub(r'\s+', ' ', text)
    return text.strip()

def clean_intervenant(interv):
    interv = clean_all(interv)
    interv = re.sub(r'^(Mmes|MM\.) (.*? et )([A-LN-Z])', r'\1 \2\1 \3', interv)
    interv = re.sub(r'^MM?(\.|mes?)\s+', '', interv)
    # cleanup parenthesis from intervenant (groupe)
    interv = re.sub(r'\s+\(\s*[A-Z][\w\s\-]+\)$', '', interv)
    interv = re.sub(r'([pP])lu[ise]{2,}urs', r'Plusieurs', interv)
    interv = interv.strip(", ")
    interv = re.sub(r"^et | et$", "", interv)
    return interv

def clean_num_lois(s):
    res = re.sub(r'[^TA\d]+', ',', s).strip(",")
    res = re.sub(r'TA[,0]+', 'TA', res)
    res = re.sub(r'(^|,)0,', r'\1', res)
    return res

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
    numeros_loi = None

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
            point_texte = p.texte
            if point_texte.parent.name == "paragraphe":
                continue
            contexte = clean_all(str(point_texte))
            contexte = re.sub(r'<\/?[a-z][^>]*>', '', contexte)
            contexte = re.sub(r'\s*\(suite\)[\s.]*$', '', contexte)
            contexte = re.sub(r'\s*-\s*suite\)[\s.]*$', ')', contexte)
            contexte = clean_all(contexte)

            if contexte and int(p['nivpoint']) < 4:
                contextes = contextes[:int(p['nivpoint'])-1]
                if not contextes:
                    contextes = []

                if not re.match(r"Suite\s*de\s*la\s*discussion|Rappels?\s*au\s*règlement|Suspension|Reprise\s*de\s*la\s*séance|Faits? personnel|Demandes? de vérification du quorum", contexte):
                    contextes.append(contexte)

                # Clean rapporteurs and numlois when changing dossier
                if len(contextes) == 1:
                    for (orateur, fonction) in intervenant2fonction.copy().items():
                        if "rapporteur" in fonction or "commission" in fonction:
                            del intervenant2fonction[orateur]
                    numeros_loi = None

        # Handle crazy variant formats of num_lois in OpenData AN...
        if p.get('bibard', '').strip() and not p.get("code_grammaire", "").startswith("DISC_GENERALE_"):
            numeros_loi = clean_num_lois(p['bibard'])
        elif p['valeur'] and re.match(r'\s*(\(?T\.?A\.?\s*|\(?n\[\[[o°]s?\]\]?\s*)+(0,\s*)?(T\.?A\.?\s*)?\d+', p['valeur']):
            numeros_loi = clean_num_lois(p['valeur'])
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
                printintervention(intervention)
            continue

        # Gestion des interventions
        if len(p.orateurs):
            nom = ""
            qualite = ""
            orateurs = p.orateurs.find_all('orateur')
            if len(orateurs) > 1:
                for orateur in orateurs:
                    nom += ", " + orateur.nom.get_text().strip(", ")
                    if orateur.qualite and orateur.qualite.string:
                        nom += ", " + orateur.qualite.get_text().strip(", ")
                    nom = nom.strip(", ")
                print("WARNING: merged multiple orateurs into one:", nom, file=sys.stderr)
            else:
                nom = p.orateurs.orateur.nom.get_text().strip()
                if p.orateurs.orateur.qualite and p.orateurs.orateur.qualite.string:
                    qualite = p.orateurs.orateur.qualite.get_text().strip()
            intervention["intervenant"] = clean_intervenant(nom)

            if p['id_mandat'] and p['id_mandat'] != "-1":
                intervention["intervenant_url"] = "http://www2.assemblee-nationale.fr/deputes/fiche/OMC_"+p['id_acteur']
                intervenant2url[intervention["intervenant"]] = intervention['intervenant_url']

            existingfonction = intervenant2fonction.get(intervention["intervenant"])
            if qualite:
                intervention['fonction'] = clean_all(qualite)
                if not existingfonction and intervention['fonction']:
                    intervenant2fonction[intervention["intervenant"]] = intervention['fonction']
                elif existingfonction:
                    if existingfonction.startswith(intervention['fonction']):
                        intervention["fonction"] = existingfonction
                    elif intervention['fonction'].startswith(existingfonction):
                        intervenant2fonction[intervention["intervenant"]] = intervention['fonction']
            elif intervention["intervenant"] == "la présidente":
                intervention['fonction'] = "présidente"
            elif intervention["intervenant"] == "le président":
                intervention['fonction'] = "président"
            elif existingfonction:
                intervention['fonction'] = existingfonction

        t_string = str(p.texte)
        t_string = re.sub(r' ?<\/?texte[^>]*> ?', '', t_string)
        t_string = re.sub(r'n[° ]*(<exposant>[os]+</exposant>\s*)+', 'n° ', t_string)
        t_string = re.sub(r'\s*<exposant>([eè][rme]+)</exposant>\s*', r'\1 ', t_string)
        t_string = re.sub(r"(la mission|compte d(’affectation spéciale|e concours financier)s?)\s*</?italique>\s*([^<()]*)\s*</?italique>\s*", r'\1 "\3" ', t_string)
        t_string = re.sub(r'\s*</?italique>\s*((bis|ter|qua|quinqu|sex|sept|oct|non|ies)+)\s*</?italique>\s*', r' \1 ', t_string)
        t_string = t_string.replace('<italique></italique>', ' ')
        t_string = t_string.replace('<italique> </italique>', ' ')
        t_string = t_string.replace('<italique>', '<i>')
        t_string = t_string.replace('</italique>', '</i>')
        # Cleanup <i> markups when we can
        t_string = re.sub(r'\s*<i>\s*([,.])\s*\(\s*', r'\1 <i>(', t_string)
        t_string = re.sub(r'\s*\(\s*<i>\s*', ' <i>(', t_string)
        t_string = re.sub(r'\s*(</i>\s*\.|\.\s*</i>)\s*\)\s*', ')</i>. ', t_string)
        t_string = re.sub(r'\s*</i>\)\s*', ')</i> ', t_string)
        t_string = re.sub(r'</i>([A-Za-z\s–.»]{0,7})<i>\s*', r'\1', t_string)
        t_string = re.sub(r'\)(\s*[.,:;?!…–]+)\s*</i>', r')</i>\1 ', t_string)
        t_string = re.sub(r'(<i>\([^>)]*\))(<br/>|\s)+(\([^>)]*\)\s*</i>)', r'\1</i> <i>\2', t_string)
        t_string = re.sub(r'(<i>\([^<)]*)</i>$', r'\1)</i>', t_string)
        t_string = re.sub(r'(<i>\w+\W*)(\([^)<]*\)</i>)', r'\1</i> <i>\2', t_string)
        t_string = re.sub(r'(<i>\([^)<]*)\)([\s.]*)<br/>\s*(\([^)<]*\).?</i>)', r'\1\2)</i> <i>\3', t_string)
        t_string = re.sub(r'\s*<br/>\s*', '</p><p>', t_string)
        t_string = re.sub(r'\)\s*</p>\s*<p>\s*</i>\s*', ')</i></p><p>', t_string)
        t_string = re.sub(r'<i>\s*(\([^)<]*\))\s*</i>(\s*[.,:;?!…–]+)\s*', r'\2 <i>(\1)</i> ', t_string)
        t_string = re.sub(r'(<i>[^(<]*)\.\s*(\([^)<]*\)\s*</i>)', r'\1</i>. <i>\2', t_string)
        t_string = re.sub(r'(\([^)<]{0,13}\s*)<i>([^()<]*\)</i>)', r'<i>\1\2', t_string)
        t_string = t_string.replace('<p></p>', '')
        t_string = clean_all(t_string)
        if not t_string:
            continue
        texte = "<p>%s</p>" % t_string

        i = 0
        # Extract didascalies from within discussions
        curinterv = intervention.copy()
        for i in re.split('\s*(<i>\s*\([^<]*\)\s*</i>\s*)', texte):
            if not i:
                continue
            if i[0] == ' ':
                i = i[1:]
            if i[-1] == ' ':
                i = i[:-1]
            if (i[0:3] != '<p>'):
                i = '<p>' + i
            if (i[-4:] != '</p>'):
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

    for line in output:
        print(json.dumps(line, ensure_ascii=False))

def record_line(i):
    global timestamp
    # Assemble divided successive interventions from same intervenant within same contexte
    last_i = output[-1] if output else {"intervenant": "", "contexte": ""}
    if i["intervenant"] and i["intervenant"] == last_i["intervenant"] and i["contexte"] == last_i["contexte"]:
        timestamp = int(last_i["timestamp"])
        if i["intervention"] == last_i["intervention"]:
            return
        print("WARNING, merging successive interventions from same intervenant", i["intervenant"], last_i["source"], file=sys.stderr)
        if i.get("intervenant_url") and not last_i.get("intervenant_url"):
            last_i["intervenant_url"] = i["intervenant_url"]
        if i.get("fonction") and (not last_i.get("fonction") or i["fonction"].startswith(last_i["fonction"])):
            last_i["fonction"] = i["fonction"]
        last_i["intervention"] += i["intervention"]
    else:
        output.append(i)

def printintervention(i):
    global timestamp

    # No empty interv
    i["intervention"] = re.sub(r'(<p>\s*</p>\s*)+', '', i["intervention"])
    if not i["intervention"]:
        return

    # Split multiple intervenants
    if i["intervenant"].endswith(" et Plusieurs députés"):
        intervenants = i["intervenant"].split(" et ")
    elif "lusieurs députés" in i["intervenant"] or (len(i["intervenant"].split(" ")) <= 6 and " et " in i["intervenant"] and "," not in i["intervenant"]):
        intervenants = i["intervenant"].replace(", ", " et ").split(" et ")
    else:
        intervenants = re.split(r"(?:\s+et|,)+\s+MM?(?:\.|mes?)\s+", i['intervenant'])
    if len(intervenants) > 1:
        #print("WARNING, multiple interv: %s" % i, intervenants, file=sys.stderr)
        if intervenants[0].startswith("Plusieurs députés"):
            intervenants[0] = intervenants[0].replace("des groupes", "du groupe")
            radical = re.sub(r"^(.*?\s)[A-Z].*$", r"\1", intervenants[0])
            for idx in range(1, len(intervenants)):
                intervenants[idx] = radical + intervenants[idx]

    timestamp += 10
    curtimestamp = timestamp
    for intervenant in intervenants:
        i['timestamp'] = str(curtimestamp)
        curtimestamp += 1

        # Extract function from split intervenants
        if ', ' in intervenant:
            intervenantfonction = intervenant.split(', ', 1)
            intervenant = intervenantfonction[0]
            i['fonction'] = clean_all(intervenantfonction[1]).strip(", ")
        i['intervenant'] = clean_intervenant(intervenant)
        existingfonction = intervenant2fonction.get(i['intervenant'])
        if not existingfonction and i.get('fonction'):
            intervenant2fonction[i["intervenant"]] = i['fonction']
        elif existingfonction:
            if not i.get('fonction') or existingfonction.startswith(i['fonction']):
                i['fonction'] = existingfonction
            elif i['fonction'].startswith(existingfonction):
                intervenant2fonction[i["intervenant"]] = i['fonction']

        if intervenant2url.get(i['intervenant']):
            i['intervenant_url'] = intervenant2url[i['intervenant']]

        record_line(i.copy())

        if i.get('fonction'):
            del i['fonction']
        if i.get('intervenant_url'):
            del i['intervenant_url']


content_file = sys.argv[1]
source_url = ''
if (len(sys.argv) > 2):
    source_url = sys.argv[2]
with open(content_file, encoding='utf-8') as f:
    xml2json(f.read())
