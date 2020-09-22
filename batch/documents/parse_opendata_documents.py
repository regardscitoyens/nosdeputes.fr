#!/usr/bin/python
# -*- coding: utf-8 -*-

import json, sys, html2text, re, io, os

def convert_format(data):
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

    if data['classification']['type']['code'] == 'PION' or data['classification']['type']['code'] == 'PNRE':
        res["source"] = 'http://www.assemblee-nationale.fr/{}/propositions/pion{:04d}.asp'.format( data['legislature'], int(data['notice']['numNotice']) )
    elif data['classification']['type']['code'] == 'AVIS':
        res["source"] =  'http://www.assemblee-nationale.fr/{}/rapports/r{:04d}.asp'.format( data['legislature'], int(data['notice']['numNotice']) )
    elif data['classification']['type']['code'] == 'PRJL':
        res["source"] =  'http://www.assemblee-nationale.fr/{}/projets/pl{:04d}.asp'.format( data['legislature'], int(data['notice']['numNotice']) )
    elif data['classification']['type']['code'] == 'RAPP':
        res["source"] = 'http://www.assemblee-nationale.fr/{}/rapports/r{:04d}.asp'.format( data['legislature'], int(data['notice']['numNotice']) )
    elif data['classification']['type']['code'] == 'RINF':
        res["source"] = 'http://www.assemblee-nationale.fr/{}/rap-info/i{:04d}.asp'.format( data['legislature'], int(data['notice']['numNotice']) )


    res["date_depot"] = re.sub(r'T.*', '', data['cycleDeVie']['chrono']['dateDepot'])
    if data['cycleDeVie']['chrono'].get('datePublication'):
        res["date_publi"] = re.sub(r'T.*', '', data['cycleDeVie']['chrono']['datePublication'])

    res["type"] = data['classification']['type']['libelle']
    if data['classification']['sousType']:
        res["type_details"] = data['classification']['sousType']['libelle']
    res["deputes"] = {"auteurs": [], "coSignataires": [] }
    res["auteurs"] = ""
    if isinstance(data['auteurs']['auteur'], list):
        for auteur in data['auteurs']['auteur']:
            if auteur.get('acteur'):
#                res["deputes"]["auteurs"].append(auteur['acteur']['acteurRef'])
                res["auteurs"] += auteur['acteur']['acteurRef']+ " "+auteur['acteur']['qualite']+", "
            elif auteur.get('organe'):
#                res["organe"] = auteur['organe']['organeRef']
                res["auteurs"] += auteur['organe']['organeRef']+", "
    else:
#        res["deputes"]["auteurs"].append(data['auteurs']['auteur']['acteur']['acteurRef'])
      if data['auteurs']['auteur'].get('acteur'):
        res["auteurs"] += data['auteurs']['auteur']['acteur']['acteurRef']+ " "+data['auteurs']['auteur']['acteur']['qualite']+", "
    if data.get('coSignataires'):
      for aut in data['coSignataires']['coSignataire']:
    #    res["deputes"]["coSignataires"].append(aut['acteur']['acteurRef'])
        res["auteurs"] += data['auteurs']['auteur']['acteur']['acteurRef']+ " Cosignataire, "

    if data['dossierRef']:
        with open("opendata/dossierParlementaire/"+data['dossierRef']+".json") as dossierfile:
            datados = json.load(dossierfile)
            res["dossier"] = datados['dossierParlementaire']['titreDossier']['titreChemin']

    if os.path.isfile("opendata/html/"+data['uid']+".html"):
      with io.open("opendata/html/"+data['uid']+".html", encoding="utf-8", mode='r') as htmlfile:
        htmloutput = re.sub(r'<style[^<]*<\/style>', '', htmlfile.read().encode('utf-8').strip())
        from bs4 import BeautifulSoup
        soup = BeautifulSoup(htmloutput, features="lxml")
        res["contenu"] = u'N° '+res["numero"]+" - "+data['titres']['titrePrincipal']+" "+re.sub(r'  *', ' ', soup.get_text().replace('\n', ' ').replace('\t', ' '))

    print json.dumps(res)

    if data.get('divisions'):
        for division in data['divisions']['division']:
            convert_format(division)

with open(sys.argv[1], 'r') as documentfile:
    data = json.load(documentfile)
    convert_format(data['document'])
