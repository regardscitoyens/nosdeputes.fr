# Parliamentary Open Data

[Voir cette documentation en Français](opendata.md).

Cette page présente une liste non exhaustive des contenus et données réutilisables de [NosDéputés.fr](https://NosDéputés.fr) et [NosSénateurs.fr](https://NosSénateurs.fr) ainsi que diverses sources de données parlementaires utiles.

## Données directement accessibles par l'API

- [Liste des parlementaires](api.md#liste-des-parlementaires) (infos biographique, responsabilités parlementaire et extra-parlementaire...)
- [Indicateurs de l'activité parlementaire](api.md#données-dactivité-des-parlementaires) (par parlementaire et par mois)
- [Recherche et métadonnées des activités parlementaire publiées](api.md#résultats-du-moteur-de-recherche) :
  + Interventions
  + Questions écrites
  + Amendements
  + Documents parlementaires (rapports, projets et propositions de loi)
  + Organismes (commissions, délégations, missions...)

Voir la [documentation de l'API](api.md) pour plus de détails.

## Contenus directement réutilisables

- [Photos des parlementaires](api.md#détails-de-chaque-parlementaire)
- Graphique, barre d'activité et mots-clés des parlementaires via le [widget d'activité](widget.md) (pour NosDéputés.fr seulement)

## Données complètes SQL téléchargeables

Des [dumps SQL complets des bases](https://www.regardscitoyens.org/telechargement/donnees/) (sans les données personnelles de nos utilisateurs) sont mis à disposition à l'adresse suivante : https://www.regardscitoyens.org/telechargement/donnees/

Ils contiennent en plus de ce qui est déjà disponible via l'API :

- Présences repérées des parlementaires
- Séances de commissions et plénières
- Dossiers parlementaires

Vous pouvez consulter [le modèle et le schéma SQL de ces bases ici](data_model.md).

## Données complémentaires

Diverses données parlementaires également mises en œuvre sur nos sites et ailleurs sont disponibles :

- Carte SVG de l'[hémicycle de l'Assemblée](https://github.com/regardscitoyens/mapHemicycle/blob/gh-pages/img/hemicycle-an.svg)
- Carte SVG des [départements](../web/departements.svg)
- Carte SVG des [circonscriptions (depuis 2012)](https://www.data.gouv.fr/fr/datasets/carte-des-circonscriptions-lgislatives-2012/)
- Carte SVG des [circonscriptions (avant 2012)](../web/circo2007.svg)
- Carte SVG des [circonscriptions des français de l'étranger (depuis 2012)](../web/circos-francais-etranger.svg)
- Appartenance des [communes aux circonscriptions législatives](https://github.com/regardscitoyens/nosdeputes.fr/raw/master/batch/depute/static/circo_insee.csv)
- Détail des pays appartenant aux [circonscriptions des français de l'étranger](https://github.com/regardscitoyens/nosdeputes.fr/raw/master/batch/depute/static/circo_etranger.csv)
- Catalogue [OpenData officiel de l'Assemblée nationale](http://data.assemblee-nationale.fr)
- Catalogue [OpenData officiel du Sénat](http://data.senat.fr)
- [ParlAPI](http://parlapi.fr/) reprenant les données des catalogues officiels OpenData
- [API de LaFabriqueDeLaLoi.fr](https://www.lafabriquedelaloi.fr/api/) proposant les détails de procédure parlementaire pour 300 dossiers
- Liste et comptes Twitter des [membres du gouvernement](https://github.com/regardscitoyens/direct-parlement/blob/gh-pages/resources/gouvernement.csv) maintenue par Accropolis
- [Comptes Twitter](https://github.com/regardscitoyens/twitter-parlementaires) des parlementaires
- [Rattachement financier annuel aux partis politiques](https://github.com/regardscitoyens/rattachement-financier-parlementaires) des parlementaires
- [Réserve parlementaire annuelle](https://github.com/regardscitoyens/reserveparlementaire_parser) des députés
- [Déclarations d'intérêts 2014 manuscrites numérisées](https://www.data.gouv.fr/fr/datasets/declarations-d-interets-des-parlementaires-publiees-par-la-haute-autorite-pour-la-transparence/) par crowdsourcing des parlementaires
- Listes des [collaborateurs parlementaires](https://github.com/regardscitoyens/Collaborateurs-Parlement) à l'Assemblée et au Sénat
- Listes des [représentants d'intérets enregistrés](https://github.com/regardscitoyens/registres-lobbying) aux registres de l'Assemblée, du Sénat et de la HATVP
- Diverses autres données sur les élus et les parlementaires compilées par le [Projet Arcadie](https://github.com/TrisA/Projet-Arcadie)
- [Personnes auditionnées à l'Assemblée](http://www.nosdonnees.fr/package/influence-auditions-deputes-lobbying) entre 2007 et 2012 extraites par crowdsourcing des rapports
- [Top mensuel par groupe des amendements adoptés](https://github.com/regardscitoyens/top-amendements-adoptes) réalisé pour L'Hémicycle
- [Statistiques quotidiennes de visite](https://github.com/regardscitoyens/stats-analytics) de NosDéputés.fr et NosSénateurs.fr
- RSS du [flux de dépèches AFP de l'Assemblée](https://github.com/regardscitoyens/AFP-AN-RSS)
- [Professions de foi](https://github.com/regardscitoyens/professions-foi-candidats) des candidats aux élections publiées par le Ministère de l'Intérieur
- [Métadonnées des décisions du Conseil Constitutionnel](https://github.com/regardscitoyens/CC-portes-etroites) et liste des « portes étroites » déclarées.

## Conditions d'utilisation

Sauf mention contraire, les contenus sont diffusés sous [licence CC-BY-SA](http://creativecommons.org/licenses/by-nc-sa/3.0/fr/) et les données sous [licence ODbL](http://www.vvlibri.org/fr/licence/odbl/10/fr/legalcode).

Cela signifie que vous êtes libre de les réutiliser, modifier et recouper dans la mesure où vous indiquez leur source (NosDéputés.fr (ou NosSénateurs.fr) par Regards Citoyens à partir de l'Assemblée nationale (ou du Sénat) et du Journal Officiel) et que vous republiez de votre côté les éventuelles données modifiées servant à une réutilisation publiée.

N'hésitez pas à nous contacter pour tout besoin spécifique à partir de ces données.
