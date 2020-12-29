#!/usr/bin/python
# -*- coding: utf-8 -*-

import json, sys, html2text, re, io, os

romain2num = {'I':'01','V1':'01','II':'02','V2':'02','III':'03','IV':'04','V':'05','VI':'06','VII':'07','VIII':'08','IX':'09','X':'10','XI':'11','XII':'12','XIII':'13','XIV':'14','XV':'15','XVI':'16','XVII':'17','XVIII':'18','XVIII':'19','XVIII':'20'}

def convert_format(data, extra = ''):
    res = {}
    res['motscles'] = ""
    res["categorie"] = ""
    res["annexe"] = ""
    res["type_details"] = ""
    if data['notice'].get('numNotice'):
        res["numero"] = data['notice']['numNotice']
    else:
        res["numero"] = re.sub('^.*[^0-9]+', '', data['uid'])

    res["id"] = res["numero"]
    res["legislature"] = data['legislature']

    if data['notice'].get('formule'):
        res["titre"] = data['notice']['formule']
    else:
        res["titre"] = data['titres']['titrePrincipal']

    is_plf = False
    data['plf_annee'] = ''
    if data["notice"].get("formule") and re.search('projet de loi de finances pour ', data["notice"]["formule"]): # and data["classification"]["type"]["code"] == 'AVIS':
        is_plf = True
        data['plf_annee'] = int(re.sub('[ \.].*', '', re.sub(r'.*projet de loi de finances pour ', '', data["notice"]["formule"])))

    if data['uid'].find('-') > 0:
        extra = extra + re.sub('.*-', '-', data['uid'])
        res['id'] += extra
        if extra == '-COMPA':
            extra = '-aCOMPA'
        else:
            annexes = extra.upper().split('-')
            res['annexe'] = ''
            if is_plf:
                res['annexe'] = 'B'
            for annexe in annexes[1:]:
                try:
                    res['annexe'] += re.sub('[0-9ivx]*$', '', annexe, flags=re.IGNORECASE) + str(int(re.sub('^[^ivx]', '', annexe, flags=re.IGNORECASE)))
                except:
                    res['annexe'] += re.sub('[0-9ivx]*$', '', annexe, flags=re.IGNORECASE) + romain2num[re.sub('^[^ivx]', '', annexe, flags=re.IGNORECASE)]
            res["categorie"] = data['titres']['titrePrincipal']

    if data['classification'].get('statutAdoption') == 'ADOPTCOM' and data['classification']['type']['code'] != 'RAPP':
        res["source"] = 'http://www.assemblee-nationale.fr/{}/ta-commission/r{:04d}-a0.asp'.format( data['legislature'], int(res["numero"]) )
    elif data['classification']['type']['code'] == 'PION' or data['classification']['type']['code'] == 'PNRE':
        if data['classification'].get('sousType') and data['classification']['sousType'].get('code') == 'TVXINSTITEUROP':
            res["source"] = 'http://www.assemblee-nationale.fr/{}/europe/resolutions/ppe{:04d}.asp'.format( data['legislature'], int(res["numero"]) )
        else:
            res["source"] = 'http://www.assemblee-nationale.fr/{}/propositions/pion{:04d}.asp'.format( data['legislature'], int(res["numero"]) )
    elif data['classification']['type']['code'] == 'AVIS':
        if is_plf:
            res["source"] = 'http://www.assemblee-nationale.fr/{}/budget/plf{:04d}/a{:04d}{}.asp'.format( data['legislature'], data['plf_annee'], int(res["numero"]), extra )
        else:
            res["source"] =  'http://www.assemblee-nationale.fr/{}/rapports/r{:04d}{}.asp'.format( data['legislature'], int(res["numero"]), extra )
    elif data['classification']['type']['code'] == 'PRJL':
        if data['provenance'] == 'Commission':
            res["source"] =  'http://www.assemblee-nationale.fr/{}/ta-commission/r{:04d}-a0.asp'.format( data['legislature'], int(res["numero"]) )
        else:
            res["source"] =  'http://www.assemblee-nationale.fr/{}/projets/pl{:04d}.asp'.format( data['legislature'], int(res["numero"]) )
    elif data['classification']['type']['code'] == 'RAPP':
        if data['classification'].get('sousType'):
            if data['classification']['sousType'].get('code') == 'ENQU':
                res["source"] = 'http://www.assemblee-nationale.fr/{}/rap-enq/r{:04d}{}.asp'.format( data['legislature'], int(res["numero"]), extra )
            elif data['classification']['sousType'].get('code') == "OFFPARL":
                res["source"] = 'http://www.assemblee-nationale.fr/{}/rap-off/i{:04d}{}.asp'.format( data['legislature'], int(res["numero"]), extra  )
            else:
                res["source"] = 'http://www.assemblee-nationale.fr/{}/rapports/r{:04d}{}.asp'.format( data['legislature'], int(res["numero"]), extra )
        elif is_plf:
            res["source"] = 'http://www.assemblee-nationale.fr/{}/budget/plf{:04d}/b{:04d}{}.asp'.format( data['legislature'], data['plf_annee'], int(res["numero"]), extra )
        else:
            res["source"] = 'http://www.assemblee-nationale.fr/{}/rapports/r{:04d}{}.asp'.format( data['legislature'], int(res["numero"]), extra )
    elif data['classification']['type']['code'] == 'RINF':
        if data['classification'].get('sousType') and data['classification']['sousType'].get('code') == 'AUE':
            res["source"] = 'http://www.assemblee-nationale.fr/{}/europe/rap-info/i{:04d}{}.asp'.format( data['legislature'], int(res["numero"]), extra  )
        else:
            res["source"] = 'http://www.assemblee-nationale.fr/{}/rap-info/i{:04d}{}.asp'.format( data['legislature'], int(res["numero"]), extra )

    if extra == '-aCOMPA':
        res["source"] = 'http://www.assemblee-nationale.fr/{}/pdf/rapports/r{:04d}-aCOMPA.pdf'.format(data['legislature'], int(res["numero"]))

    if data['cycleDeVie']['chrono']['dateDepot']:
        res["date_depot"] = re.sub(r'T.*', '', data['cycleDeVie']['chrono']['dateDepot'])
    if data['cycleDeVie']['chrono'].get('datePublication'):
        res["date_publi"] = re.sub(r'T.*', '', data['cycleDeVie']['chrono']['datePublication'])

    res["type"] = data['classification']['type']['libelle']
    if data['classification']['sousType'] and data['classification']['sousType'].get('libelle'):
        res["type_details"] = data['classification']['sousType']['libelle']
    res["deputes"] = {"auteurs": [], "coSignataires": [] }
    res["auteurs"] = ""

    if not isinstance(data['auteurs']['auteur'], list):
        data['auteurs']['auteur'] = [ data['auteurs']['auteur'] ]
    auteurs = []
    for auteur in data['auteurs']['auteur']:
        if auteur.get('acteur'):
            acteur_id = auteur['acteur']['acteurRef']
            acteur = acteur_id
            if os.path.isfile("opendata/acteur/"+acteur_id+".json"):
                with io.open("opendata/acteur/"+acteur_id+".json", encoding="utf-8", mode='r') as acteur_file:
                    acteur_json = json.load(acteur_file)
                    acteur = acteur_json['acteur']['etatCivil']['ident']['civ']+' '+acteur_json['acteur']['etatCivil']['ident']['prenom']+' '+acteur_json['acteur']['etatCivil']['ident']['nom']
            res["deputes"]["auteurs"].append([auteur['acteur']['acteurRef'], acteur, auteur['acteur']['qualite'].title()])
            auteurs.append(acteur+ " "+auteur['acteur']['qualite'].title())
        elif auteur.get('organe'):
            res["organe_id"] = auteur['organe']['organeRef']
            if res.get('organe_id') and os.path.isfile("opendata/organe/"+res['organe_id']+".json"):
                with io.open("opendata/organe/"+res['organe_id']+".json", encoding="utf-8", mode='r') as organe_file:
                    organe_json = json.load(organe_file)
                    res['organe_name'] = organe_json['organe']['libelle']
            if res.get('organe_name'):
                auteurs.append(res['organe_name'])
            else:
                auteurs.append(auteur['organe']['organeRef'])

    if data.get('coSignataires'):
        if not isinstance(data['coSignataires']['coSignataire'], list):
            data['coSignataires']['coSignataire'] = [ data['coSignataires']['coSignataire'] ]
        for cosign in data['coSignataires']['coSignataire']:
            if cosign.get('acteur'):
                acteur_id = cosign['acteur']['acteurRef']
                acteur = acteur_id
                if os.path.isfile("opendata/acteur/"+acteur_id+".json"):
                    with io.open("opendata/acteur/"+acteur_id+".json", encoding="utf-8", mode='r') as acteur_file:
                        acteur_json = json.load(acteur_file)
                        acteur = acteur_json['acteur']['etatCivil']['ident']['civ']+' '+acteur_json['acteur']['etatCivil']['ident']['prenom']+' '+acteur_json['acteur']['etatCivil']['ident']['nom']
                res["deputes"]["coSignataires"].append([acteur_id, acteur, "Cosignataire"])
                auteurs.append(acteur+ " Cosignataire")
            elif cosign.get('organe'):
                organe_id = cosign['organe']['organeRef']
                organe_name = organe_id
                if os.path.isfile("opendata/organe/"+organe_id+".json"):
                    with io.open("opendata/organe/"+organe_id+".json", encoding="utf-8", mode='r') as organe_file:
                        organe_json = json.load(organe_file)
                        organe_name = organe_json['organe']['libelle']
                res["deputes"]["coSignataires"].append([organe_id, organe_name, "Organe"])
                auteurs.append(organe_name)
    res["auteurs"] = ', '.join(auteurs)

    if data['dossierRef']:
        try:
            with open("opendata/dossierParlementaire/"+data['dossierRef']+".json") as dossierfile:
                datados = json.load(dossierfile)
                res["dossier"] = datados['dossierParlementaire']['titreDossier']['titreChemin']
        except:
            res["dossier"] = ''

    res["contenu"] = "Non encore publié"
    if os.path.isfile("opendata/html/"+data['uid']+".html"):
      with io.open("opendata/html/"+data['uid']+".html", encoding="utf-8", mode='r') as htmlfile:
        htmloutput = re.sub(bytes('<style[^<]*<\/style>', 'utf8'),
                            bytes('', 'utf8'),
                            htmlfile.read().encode('utf-8').strip())
        from bs4 import BeautifulSoup
        soup = BeautifulSoup(htmloutput, features="lxml")
        res["contenu"] = u'N° '+res["numero"]+" - "+data['titres']['titrePrincipal']+" "+re.sub(r'  *', ' ', soup.get_text().replace('\n', ' ').replace('\t', ' '))

    print(json.dumps(res))

    if data.get('divisions'):
        if isinstance(data['divisions']['division'], list):
            for division in data['divisions']['division']:
                convert_format(division, extra)
        else:
            convert_format(data['divisions']['division'])

with open(sys.argv[1], 'r') as documentfile:
    data = json.load(documentfile)
    convert_format(data['document'])
