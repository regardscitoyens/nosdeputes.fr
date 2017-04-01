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

def parseUrl(urlAN):
    elements = urlAN[:-4].split("/")
    loi = elements[3]
    try:
        lettre = re.search(ur"([A-Z])$", loi, re.I).group(1)
        loi = loi[:-1]
    except:
        lettre = ""
    if not loi.startswith(u"TA"):
        loi = str(int(loi))
    numero = elements[5]
    if elements[4] != u"AN" and not re.search(ur"[A-Z]", numero, re.I):
        numero = re.sub(ur"[^A-Z]", "", elements[4], re.I) + numero
    numero += lettre.upper()
    return loi, numero

re_rmNum = re.compile(ur"\d+")
def extractNumRectif(num):
    # Extract rectif & serie from numéro long strings such as
    # - "456"                       -> "0", ""
    # - "456 (Rect)"                -> "1", ""
    # - "456 (4ème Rect)"           -> "4", ""
    # - "456 à 460"                 -> "0", "456-460"
    # - "456 (Rect) à 460 (Rect)"   -> "1", "456-460"
    # - "DC456 à 460"               -> "0", "DC456-DC460"
    # - "DC456 à DC460"             -> "0", "DC456-DC460"
    serie = ""
    if u" à " in num:
        num, serie = num.split(u" à ")
        serie = serie.split(u" ")[0]
    pieces = num.split(u' (')
    num = pieces[0]
    if serie:
        try:
            numint = int(num)
            com = u""
        except:
            com = re_rmNum.sub(u"", num)
        serie = "%s-%s%s" % (num, com, serie.replace(com, u""))
    if len(pieces) > 1:
        if pieces[1].startswith(u"Rect)"):
            return "1", serie
        return pieces[1][0], serie
    return "0", serie

re_et = re.compile(ur"\s*?(,| et)\s*M", re.I)
def cleanAuteurs(auteurs, h):
    auteurs = h.unescape(auteurs)
    auteurs = re_et.sub(u", M", auteurs)
    return auteurs

def extractSort(sort):
    if sort.startswith("Irrecevable"):
        return u"Irrecevable"
    if sort in [u"A discuter", u"En traitement"]:
        return u"Indéfini"
    if sort == u"Tombé":
        return u"Tombe"
    return sort

reRmAttributes = re.compile(ur"<([pbiu]|t([drh]|able)|span|em|div)\s+[^>]*>", re.I)
reRmMarkup = re.compile(ur"<\/?(span|div)>", re.I)
reCleanDoubleBR = re.compile(ur"(</?br */*>\s*)+", re.I)
reCleanEmpty = re.compile(ur"\s*<p>(</?[bi][r /]*>|\s)*</p>\s*", re.I)
reCleanDouble = re.compile(ur"\s*((</?p>)(</?[bi][r /]*>|\s)*|(</?[bi][r /]*>|\s)*(</?p>))\s*", re.I)
def fixHTML(text, h):
    text = h.unescape(text)
    text = text.replace(u"\n", u" ")
    text = reRmAttributes.sub(ur"<\1>", text)
    text = reRmMarkup.sub(u"", text)
    text = reCleanDoubleBR.sub(u"<br/>", text)
    text = reCleanEmpty.sub(u" ", text)
    text = reCleanDouble.sub(lambda x: x.group(2) or x.group(5), text)
    return text

def convertToNDFormat(amdtOD):
    h = HTMLParser()
    formatND = {}
    formatND['source'] = u"http://www.assemblee-nationale.fr%s.asp" % amdtOD['representation.contenu.documentURI'][:-4]

    try:
        formatND['legislature'] = amdtOD['identifiant.legislature']
        #formatND['loi'] = amdtOD['refTexteLegislatif']
        #formatND['numero'] = amdtOD['identifiant.numero']
        #formatND['numero'] = amdtOD['numeroLong']
        formatND['loi'], formatND['numero'] = parseUrl(amdtOD['representation.contenu.documentURI'])
        #formatND['rectif']  = amdtOD['identifiant.numRect']
        formatND['rectif'], formatND['serie'] = extractNumRectif(amdtOD['numeroLong'])
        formatND['parent'] = amdtOD['amendementParent']
        formatND['date'] = amdtOD['dateDepot']
        formatND['auteurs'] = cleanAuteurs(amdtOD['signataires.texteAffichable'], h)
        formatND['sort'] = extractSort(amdtOD.get('sort.sortEnSeance', amdtOD['etat']))
        formatND['sujet'] = h.unescape(amdtOD['pointeurFragmentTexte.division.articleDesignationCourte'])
        formatND['texte'] = fixHTML(amdtOD['corps.dispositif'], h)
        formatND['expose'] = fixHTML(amdtOD['corps.exposeSommaire'], h)
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
                    result['corps.dispositif'] = json.dumps(amdt['corps'], ensure_ascii=False)
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

