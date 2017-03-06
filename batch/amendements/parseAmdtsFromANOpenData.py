import json,csv,os

amdtFilePath="Amendements_XIV.json"


def convertToNDFormat(amdtOD):
    formatND = {}
    formatND['source'] = "http://www.assemblee-nationale.fr%s.asp" % amdtOD['representation.contenu.documentURI'][:-4]
    formatND['legislature'] = amdtOD['identifiant.legislature']
    formatND['loi'] = amdtOD['refTexteLegislatif']
    #formatND['numero'] = amdtOD['identifiant.numero']
    formatND['numero'] = amdtOD['numeroLong']
    formatND['serie'] = ""
    formatND['rectif']  = amdtOD['identifiant.numRect']
    formatND['parent'] = amdtOD['amendementParent']
    formatND['date'] = amdtOD['dateDepot']
    formatND['auteurs'] = amdtOD['signataires.texteAffichable']
    
    sort = ""
    if 'sort.sortEnSeance' in amdtOD:
        sort = amdtOD['sort.sortEnSeance']
    else :
        sort = amdtOD['etat']
    formatND['sort'] = sort
    formatND['sujet'] = amdtOD['pointeurFragmentTexte.division.articleDesignationCourte']
    formatND['texte'] = amdtOD['corps.dispositif']
    formatND['expose'] = amdtOD['corps.exposeSommaire']
    formatND['auteur_reel'] = amdtOD['signataires.auteur.acteurRef']

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






#with open("amendements_liste.csv", 'w') as f2:
#    spamwriter =  csv.DictWriter(f2, delimiter='|', fieldnames=fieldnames)
#    spamwriter.writeheader()

dirpath = "OpenDataAN"
if not os.path.exists(dirpath):
    os.makedirs(dirpath)


for texte in json_data['textesEtAmendements']['texteleg']:
    refTexteLeg = texte['refTexteLegislatif']
    print "Texte being treated : %s " % refTexteLeg
    texteAmdtFileName = "%s/amdts_%s.json" % (dirpath,refTexteLeg)

    with open(texteAmdtFileName, 'w') as texteAmdtFile :
        
        if type(texte['amendements']['amendement']) != list :
            print "==================================================="
            print "COnverting a single element in a list"
            print "==================================================="
            texte['amendements']['amendement'] =  [texte['amendements']['amendement']]
        for amdt in texte['amendements']['amendement']:
            #print amdt
            try:
                result = {}
                result['refTexteLegislatif'] = refTexteLeg
                result['amendementParent'] = amdt['amendementParent'] 
                result['article99'] = amdt['article99']
                result['cardinaliteAmdtMultiples'] = amdt['cardinaliteAmdtMultiples'] 
                result['corps.annexeExposeSommaire'] = amdt['corps']['annexeExposeSommaire']
                if 'dispositif' in amdt['corps']:
                    result['corps.dispositif'] = amdt['corps']['dispositif']
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
                result['representation.contenu.documentURI'] = amdt['representations']['representation']['contenu']['documentURI']
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
                for k in result:
#                    print "key is %s" % k
                    if result[k] and type(result[k]) != list and type(result[k]) != dict:
                        result[k] = result[k].encode('utf8')

                amdtND = convertToNDFormat(result)
                texteAmdtFile.write(json.dumps(amdtND)+"\n")
                #spamwriter.writerow(result)
                

            except Exception as e: 
                print type(e)
                print e
                print "Error on %s\n" % json.dumps(amdt)
            

