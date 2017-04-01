#!/usr/bin/env python
# -*- coding: utf-8 -*-

import json, csv, os, re
try:                    # Python 2.6-2.7
    from HTMLParser import HTMLParser
except ImportError:     # Python 3
    from html.parser import HTMLParser

amdtFilePath="OpenDataAN/Amendements_XIV.json"

# TODO:
# - see how to handle complexe dispositif (tables) for PJLFs
# - fix num rect not matching text one

def parseUrl(urlAN):
    elements = urlAN[:-4].split("/")
    loi = elements[3]
    try:
        lettre = re.search(r"([A-Z])$", loi, re.I).group(1)
        loi = loi[:-1]
    except:
        lettre = ""
    if not loi.startswith("TA"):
        loi = str(int(loi))
    numero = elements[5]
    if elements[4] != "AN" and not re.search(r"[A-Z]", numero, re.I):
        numero = re.sub(r"[^A-Z]", "", elements[4], re.I) + numero
    numero += lettre.upper()
    return loi, numero

def convertToNDFormat(amdtOD):
    h = HTMLParser()
    formatND = {}
    formatND['source'] = "http://www.assemblee-nationale.fr%s.asp" % amdtOD['representation.contenu.documentURI'][:-4]

    try:
        formatND['legislature'] = amdtOD['identifiant.legislature']
        #formatND['loi'] = amdtOD['refTexteLegislatif']
        #formatND['numero'] = amdtOD['identifiant.numero']
        #formatND['numero'] = amdtOD['numeroLong']
        formatND['loi'], formatND['numero'] = parseUrl(amdtOD['representation.contenu.documentURI'])
        formatND['serie'] = ""
        formatND['rectif']  = amdtOD['identifiant.numRect']
        formatND['parent'] = amdtOD['amendementParent']
        formatND['date'] = amdtOD['dateDepot']
        formatND['auteurs'] = h.unescape(amdtOD['signataires.texteAffichable'])
        formatND['sort'] = amdtOD.get('sort.sortEnSeance', amdtOD['etat'])
        formatND['sujet'] = h.unescape(amdtOD['pointeurFragmentTexte.division.articleDesignationCourte'])
        formatND['texte'] = h.unescape(amdtOD['corps.dispositif'])
        formatND['expose'] = h.unescape(amdtOD['corps.exposeSommaire'])
        formatND['auteur_reel'] = amdtOD['signataires.auteur.acteurRef']
    except Exception as e:

        print ">%s" % amdtOD['refTexteLegislatif']
        #print "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
        #print formatND['source']
        #print "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
        print type(e)
        print e
        print "Error on %s\n" % json.dumps(amdtOD)


    return formatND





with open(amdtFilePath, 'r') as f:
    json_data = json.load(f)

print "Json loaded !"

#List of all the normalized fieldnames available in the json from the AN OpenData
fieldnames = ['refTexteLegislatif', 'amendementParent',
            'article99', 'cardinaliteAmdtMultiples',
            'corps.annexeExposeSommaire','corps.dispositif', 'corps.exposeSommaire',
            'dateDepot', 'dateDistribution', 'etapeTexte', 'etat',
            'identifiant.legislature', 'identifiant.numRect', 'identifiant.numero',
            'identifiant.saisine.mentionSecondeDeliberation',
            'identifiant.saisine.numeroPartiePLF', 'identifiant.saisine.organeExamen', 'identifiant.saisine.refTexteLegislatif',
            'loiReference.codeLoi', 'loiReference.divisionCodeLoi',
            'numeroLong',
            'pointeurFragmentTexte.alinea.alineaDesignation', 'pointeurFragmentTexte.alinea.avant_A_Apres',
            'pointeurFragmentTexte.alinea.numero',
            'pointeurFragmentTexte.division.articleAdditionnel',
            'pointeurFragmentTexte.division.articleDesignationCourte',
            'pointeurFragmentTexte.division.avant_A_Apres',
            'pointeurFragmentTexte.division.chapitreAdditionnel',
            'pointeurFragmentTexte.division.divisionRattachee',
            'pointeurFragmentTexte.division.titre',
            'pointeurFragmentTexte.division.type',
            'pointeurFragmentTexte.division.urlDivisionTexteVise',
            'pointeurFragmentTexte.missionVisee',
            'representation.contenu.documentURI',
            'representation.dateDispoRepresentation',
            'representation.nom', 'representation.offset', 'representation.repSource',
            'representation.statutRepresentation.canonique',
            'representation.statutRepresentation.enregistrement',
            'representation.statutRepresentation.officielle',
            'representation.statutRepresentation.transcription',
            'representation.statutRepresentation.verbatim',
            'representation.typeMime.subtype',
            'representation.typeMime.type',
            'seanceDiscussion',
            'signataires.auteur.acteurRef',
            'signataires.auteur.groupePolitiqueRef',
            'signataires.auteur.organeRef',
            'signataires.auteur.typeAuteur',
            'signataires.cosignataires.acteurRef',
            'signataires.texteAffichable',
            'sort.dateSaisie', 'sort.sortEnSeance',
            'triAmendement', 'uid' ]




counterError = 0

#with open("amendements_liste.csv", 'w') as f2:
#    spamwriter =  csv.DictWriter(f2, delimiter='|', fieldnames=fieldnames)
#    spamwriter.writeheader()

dirpath = "OpenDataAN"
if not os.path.exists(dirpath):
    os.makedirs(dirpath)


DictSourceAN = dict()
nbDuplicates = 0
for texte in json_data['textesEtAmendements']['texteleg']:
    refTexteLeg = texte['refTexteLegislatif']
    #print "Texte being treated : %s " % refTexteLeg
    texteAmdtFileName = "%s/amdts_%s.json" % (dirpath,refTexteLeg)

    DictIdAN_ND = dict()

    with open(texteAmdtFileName, 'w') as texteAmdtFile :

        if type(texte['amendements']['amendement']) != list :
            #print "==================================================="
            #print "COnverting a single element in a list"
            #print "==================================================="
            texte['amendements']['amendement'] =  [texte['amendements']['amendement']]

        #Create an index of uid and numero of amdt for ND purpose
        for amdt in texte['amendements']['amendement']:
            DictIdAN_ND[amdt['uid']] = amdt['identifiant']['numero']
#        print DictIdAN_ND

        for amdt in texte['amendements']['amendement']:
            amdtURI = amdt['representations']['representation']['contenu']['documentURI']
            if amdtURI in DictSourceAN:
                print "WARNING: duplicate Amdmt in OpenDataAN for %s\n" % amdtURI
                nbDuplicates += 1
                continue
            DictSourceAN[amdtURI] = True
            #print amdt
            try:
                result = {}
                result['refTexteLegislatif'] = refTexteLeg
#                result['amendementParent'] = amdt['amendementParent']
                result['amendementParent']= ""
                if amdt['amendementParent']:
                    try:
                        result['amendementParent'] = DictIdAN_ND[amdt['amendementParent']]
                    except:
                        counterError += 1
                        print "WARNING: could not retrieve parent amdmt ID on %s\n" % json.dumps(amdt)

                result['article99'] = amdt['article99']
                result['cardinaliteAmdtMultiples'] = amdt['cardinaliteAmdtMultiples']
                result['corps.annexeExposeSommaire'] = amdt['corps']['annexeExposeSommaire']
                if 'dispositif' in amdt['corps']:
                    result['corps.dispositif'] = amdt['corps']['dispositif']
                else:
                    #This is for PJLF amdt with tables. So we can read what is in corps as debug
                    result['corps.dispositif'] = amdt['corps']
                result['corps'] = amdt['corps']
                result['corps.exposeSommaire'] = amdt['corps']['exposeSommaire']
                result['dateDepot'] = amdt['dateDepot']
                result['dateDistribution'] = amdt['dateDistribution']
                result['etapeTexte'] = amdt['etapeTexte']
                result['etat'] = amdt['etat']
                result['identifiant.legislature'] = amdt['identifiant']['legislature']
                result['identifiant.numRect'] = amdt['identifiant']['numRect']
                result['identifiant.numero'] = amdt['identifiant']['numero']
                result['identifiant.saisine.mentionSecondeDeliberation'] = amdt['identifiant']['saisine']['mentionSecondeDeliberation']
                result['identifiant.saisine.numeroPartiePLF'] = amdt['identifiant']['saisine']['numeroPartiePLF']
                result['identifiant.saisine.organeExamen'] = amdt['identifiant']['saisine']['organeExamen']
                result['identifiant.saisine.refTexteLegislatif'] = amdt['identifiant']['saisine']['refTexteLegislatif']
                result['loiReference.codeLoi'] = amdt['loiReference']['codeLoi']
                result['loiReference.divisionCodeLoi'] = amdt['loiReference']['divisionCodeLoi']
                result['numeroLong'] = amdt['numeroLong']
                if amdt['pointeurFragmentTexte']['alinea']:
                    result['pointeurFragmentTexte.alinea.alineaDesignation'] = amdt['pointeurFragmentTexte']['alinea']['alineaDesignation']
                    result['pointeurFragmentTexte.alinea.avant_A_Apres'] = amdt['pointeurFragmentTexte']['alinea']['avant_A_Apres']
                    result['pointeurFragmentTexte.alinea.numero'] = amdt['pointeurFragmentTexte']['alinea']['numero']
                result['pointeurFragmentTexte.division.articleAdditionnel'] = amdt['pointeurFragmentTexte']['division']['articleAdditionnel']
                result['pointeurFragmentTexte.division.articleDesignationCourte'] = amdt['pointeurFragmentTexte']['division']['articleDesignationCourte']
                result['pointeurFragmentTexte.division.avant_A_Apres'] = amdt['pointeurFragmentTexte']['division']['avant_A_Apres']
                result['pointeurFragmentTexte.division.chapitreAdditionnel'] = amdt['pointeurFragmentTexte']['division']['chapitreAdditionnel']
                result['pointeurFragmentTexte.division.divisionRattachee'] = amdt['pointeurFragmentTexte']['division']['divisionRattachee']
                result['pointeurFragmentTexte.division.titre'] = amdt['pointeurFragmentTexte']['division']['titre']
                result['pointeurFragmentTexte.division.type'] = amdt['pointeurFragmentTexte']['division']['type']
                result['pointeurFragmentTexte.division.urlDivisionTexteVise'] = amdt['pointeurFragmentTexte']['division']['urlDivisionTexteVise']
                result['pointeurFragmentTexte.missionVisee'] = amdt['pointeurFragmentTexte']['missionVisee']
                result['representation.contenu.documentURI'] = amdtURI
                result['representation.dateDispoRepresentation'] = amdt['representations']['representation']['dateDispoRepresentation']
                result['representation.nom'] = amdt['representations']['representation']['nom']
                result['representation.offset'] = amdt['representations']['representation']['offset']
                result['representation.repSource'] = amdt['representations']['representation']['repSource']
                result['representation.statutRepresentation.canonique'] = amdt['representations']['representation']['statutRepresentation']['canonique']
                result['representation.statutRepresentation.enregistrement'] = amdt['representations']['representation']['statutRepresentation']['enregistrement']
                result['representation.statutRepresentation.officielle'] = amdt['representations']['representation']['statutRepresentation']['officielle']
                result['representation.statutRepresentation.transcription'] = amdt['representations']['representation']['statutRepresentation']['transcription']
                result['representation.statutRepresentation.verbatim'] = amdt['representations']['representation']['statutRepresentation']['verbatim']
                result['representation.typeMime.subtype'] = amdt['representations']['representation']['typeMime']['subType']
                result['representation.typeMime.type'] = amdt['representations']['representation']['typeMime']['type']
                result['seanceDiscussion'] = amdt['seanceDiscussion']
                if amdt['signataires']['auteur']:
                    if 'acteurRef' in amdt['signataires']['auteur']:
                        result['signataires.auteur.acteurRef'] = amdt['signataires']['auteur']['acteurRef']
                    else:
                        result['signataires.auteur.acteurRef'] = amdt['signataires']['auteur']['acteur']
                    if 'groupePolitiqueRef' in amdt['signataires']['auteur']:
                        result['signataires.auteur.groupePolitiqueRef'] = amdt['signataires']['auteur']['groupePolitiqueRef']
                    else:
                        result['signataires.auteur.groupePolitiqueRef'] = amdt['signataires']['auteur']['groupePolitique']
                    if 'organeRef' in amdt['signataires']['auteur']:
                        result['signataires.auteur.organeRef'] = amdt['signataires']['auteur']['organeRef']
                    else:
                        result['signataires.auteur.organeRef'] = amdt['signataires']['auteur']['organe']
                    result['signataires.auteur.typeAuteur'] = amdt['signataires']['auteur']['typeAuteur']
                if amdt['signataires']['cosignataires']:
                    result['signataires.cosignataires.acteurRef'] = amdt['signataires']['cosignataires']['acteurRef']
                result['signataires.texteAffichable'] = amdt['signataires']['texteAffichable']
                if amdt['sort']:
                    result['sort.dateSaisie'] = amdt['sort']['dateSaisie']
                    result['sort.sortEnSeance'] = amdt['sort']['sortEnSeance']
                result['triAmendement'] = amdt['triAmendement']
                result['uid'] = amdt['uid']

                amdtND = convertToNDFormat(result)
                texteAmdtFile.write(json.dumps(amdtND, ensure_ascii=False).encode("utf-8")+"\n")
                #spamwriter.writerow(result)


            except Exception as e:
                print type(e)
                print e
                print "Error on %s\n" % json.dumps(amdt)
                counterError += 1
#                exit()
print "\nWARNING: %s total errors" % counterError
print "       & %s total duplicates" % nbDuplicates

