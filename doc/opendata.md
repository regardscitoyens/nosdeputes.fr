# Données parlementaires en OpenData

Cette page présente une liste non exhaustive des contenus et données réutilisables de [NosDéputés.fr](https://NosDéputés.fr) et [NosSénateurs.fr](https://NosSénateurs.fr) ainsi que diverses sources de données parlementaires utiles.

## Données directement accessibles par l'[API](api.md)

- [Liste des parlementaires](api.md#) (infos biographique, responsabilités parlementaire et extra-parlementaire...)
- [Photos des parlementaires](api.md#)
- [Indicateurs de l'activité parlementaire](api.md#) (par parlementaire et par mois)
- [Recherche et métadonnées des activités parlementaire publiées](api.md#) :
 + Interventions
 + Questions écrites
 + Amendements
 + Documents parlementaires (rapports, projets et propositions de loi)
 + Organismes (commissions, délégations, missions...)

Voir la [documentation de l'API](api.md) pour plus de détails.

## Données complètes SQL téléchargeables

Des [dumps SQL complets des bases](http://www.regardscitoyens.org/telechargement/donnees/) (sans les données personnelles de nos utilisateurs) sont mis à disposition à l'adresse suivante : http://www.regardscitoyens.org/telechargement/donnees/

Ils contiennent en plus de ce qui est déjà disponible via l'API :

- Présences repérées des parlementaires
- Séances de commissions et plénières
- Dossiers parlementaires

Vous pouvez consulter [le modèle et le schéma SQL de ces bases ici](data_model.md).

## Données complémentaires

Diverses données parlementaires également mises en œuvre sur nos sites sont disponibles :

- [Carte SVG de l'hémicycle de l'Assemblée](https://github.com/regardscitoyens/mapHemicycle/blob/gh-pages/img/hemicycle-an.svg)
- [Carte SVG des départements](https://github.com/regardscitoyens/nosdeputes.fr/blob/master/web/france_deptmts.svg)
- [Carte SVG des circonscriptions](http://www.data.gouv.fr/fr/datasets/carte-des-circonscriptions-lgislatives-2012/)
- [Appartenance des communes aux circonscriptions législatives](https://github.com/regardscitoyens/nosdeputes.fr/raw/master/batch/depute/circo_insee_2012.csv)
- [Catalogue OpenData officiel de l'Assemblée nationale](http://data.assemblee-nationale.fr)
- [Catalogue OpenData officiel du Sénat](http://data.senat.fr)
- [ParlAPI reprenant les données des catalogues officiels OpenData](http://parlapi.fr/)
- [API de LaFabriqueDeLaLoi.fr proposant les détails de procédure parlementaire pour 300 dossiers](http://api.lafabriquedelaloi.fr)
- [Comptes Twitter des parlementaires](https://github.com/regardscitoyens/twitter-parlementaires)
- [Rattachement financier annuel des parlementaires aux partis politiques](https://github.com/regardscitoyens/rattachement-financier-parlementaires)
- [Réserve parlementaire annuelle des députés](https://github.com/regardscitoyens/reserveparlementaire_parser)
- [Déclarations d'intérêts 2014 manuscrites numérisées des parlementaires](http://www.data.gouv.fr/fr/datasets/declarations-d-interets-des-parlementaires-publiees-par-la-haute-autorite-pour-la-transparence/)
- [Liste des collaborateurs parlementaires](https://github.com/regardscitoyens/Collaborateurs-Parlement)
- [Liste des représentants d'intérets enregistrés](https://github.com/regardscitoyens/registre-lobbying-AN)
- [Personnes auditionnées à l'Assemblée entre 2010 et 2012](http://www.nosdonnees.fr/package/influence-auditions-deputes-lobbying)
- [Top mensuel par groupe des amendements adoptés réalisé pour L'Hémicycle](https://github.com/regardscitoyens/top-amendements-adoptes)
- [Statistiques de visite quotidiennes de NosDéputés.fr et NosSénateurs.fr](https://github.com/regardscitoyens/stats-analytics)
- [RSS du flux de dépèches AFP de l'Assemblée](https://github.com/regardscitoyens/AFP-AN-RSS)
- [Membres du gouvernement](https://github.com/regardscitoyens/direct-parlement/blob/gh-pages/resources/gouvernement.csv)

## Conditions d'utilisation

Sauf mention contraire, les contenus sont diffusés sous licence CC-BY-SA et les données sous [licence ODbL](http://www.vvlibri.org/fr/licence/odbl/10/fr/legalcode). Cela signifie que vous êtes libre de les réutiliser, modifier et recouper dans la mesure où vous indiquez leur source (NosDéputés.fr (ou NosSénateurs.fr par Regards Citoyens à partir de l'Assemblée nationale (ou du Sénat) et du Journal Officiel) et que vous republiez de votre côté les éventuelles données modifiées servant à une réutilisation publiée.

N'hésitez pas à nous contacter pour tout besoin spécifique à partir de ces données.
