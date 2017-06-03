# API de NosDéputés.fr et NosSénateurs.fr

Une API a été développée pour offrir un accès aux données de NosDéputés.fr et NosSénateurs.fr aux formats XML, JSON et CSV.

## Explications

- **Format :** La plupart des exemples fournis ci-dessous sont donnés au format XML pour permettre plus de lisibilité dans le navigateur web. Veuillez remplacer `xml` en `json` ou `csv` pour accéder aux autres formats

- **Encoding :** les données sont proposées en `utf-8`. Si vous vous retrouvez face à des caractères kabbalistiques, cela signifie qu'il vous faut régler l'encodage dans les options du logiciel avec lequel vous manipulez les données. Si votre tableur ne vous permet de spécifier l'encodage, vous pouvez rajouter l'option `?withBOM=true` à la fin des adresses des fichiers CSV que vous cherchez à télécharger.

- **Anciennes législatures Assemblée :** l'accès aux données de NosDéputés.fr pour les précédentes législatures est accessible de manière permanente en remplaçant www.nosdeputes.fr par les adresses suivantes :

  - pour la 13ème législature : 2007-2012.nosdeputes.fr
  - pour la 14ème législature : 2012-2017.nosdeputes.fr

- **Sénat :** les exemples donnés ci-dessous se réfèrent à NosDéputés.fr, mais vous pouvez utiliser l'API de NosSénateurs.fr suivant les mêmes schémas en remplaçant nosdeputes.fr par nossenateurs.fr dans chaque adresse.

- **SSL :** les exemples fournis utilisent les adresses sécurisées HTTPS de nos sites, mais vous pouvez remplacer https par http lorsque vous le souhaitez.

L'ensemble des données de ces différents sites est par ailleurs mis à disposition en bloc sous la forme de dumps SQL. Retrouvez les détails ainsi que d'autres [données parlementaires ici](opendata.md).

## Liste des parlementaires

- **Tous les députés en cours de mandat :**

  - format tableur (csv) : https://www.nosdeputes.fr/deputes/enmandat/csv 
  - XML : https://www.nosdeputes.fr/deputes/enmandat/xml 
  - JSON : https://www.nosdeputes.fr/deputes/enmandat/json

- **Tous les députés :**

  *Attention : Moins de champs sont disponibles avec cette version : autres mandats, groupe, e-mails, adresses, sites webs...*

  - format tableur (CSV) : https://www.nosdeputes.fr/deputes/csv
  - XML : https://www.nosdeputes.fr/deputes/xml
  - JSON : https://www.nosdeputes.fr/deputes/json

## Détails de chaque parlementaire

Chaque parlementaire dispose d'un `slug`, identifiant unique proche du nom complet, employé dans les adresses d'accès aux données le ou la concernant.

Vous pouvez retrouver ces identifiants au sein des listes des parlementaires ou les deviner : par exemple pour Nathalie Appéré : `nathalie-appere` ou pour Pierre Morel-A-L'Huissier : `pierre-morel-a-l-huissier`.

- **Infos biographiques, contacts et mandats :**

  Exemple pour Guy Teissier : https://www.nosdeputes.fr/guy-teissier/xml

- **Photo** (hauteur configurable) :

  Exemple pour Guy Teissier avec 60 pixels de hauteur : https://www.nosdeputes.fr/depute/photo/guy-teissier/60

- **Widget HTML** (graphe, barre d'activité et mots-clés) :

  Vous pouvez embarquer sur votre site les graphiques, barres d'activité et mots-clés des députés via le [widget d'activité](http://www.nosdeputes.fr/widget14) (pour NosDéputés.fr seulement).

  Voir les détails sur la [doc du widget](widget.md).

## Données d'activité des parlementaires

- **Synthèse des 12 derniers mois ou de toute la législature** :

  *(telle qu'affichée sur la page [synthèse](https://www.nosdeputes.fr/synthese)*

  - format tableur (CSV) : https://www.nosdeputes.fr/synthese/data/csv
  - XML : https://www.nosdeputes.fr/synthese/data/xml
  - JSON : https://www.nosdeputes.fr/synthese/data/json

- **Synthèse mensuelle** :

  *Attention : seuls les parlementaires ayant manifesté une activité sur la période sont renvoyés*
  
  Exemple pour le mois d'avril 2016 : https://www.nosdeputes.fr/synthese/201604/xml

- **Contenu des travaux** :

  Tous les contenus textuels des différents travaux parlementaires (organismes, débats, amendements, questions, rapports, propositions de loi) sont indexés dans le moteur de recherche et peuvent donc être individuellement retrouvés et filtrés à travers cette API ([voir plus bas](#résultats-du-moteur-de-recherche).
  
  Les résultats de la recherche renvoient les urls des données détaillées de chaque élément accessible via l'API.

## Résultats du moteur de recherche

Tous les résultats du [moteur de recherche](https://www.nosdeputes.fr/recherche/) sont également accessibles via l'API : il suffit de rajouter le paramètre `format=[xml|json|csv]` à l'url de recherche.

**Exemple :** résultats d'une recherche sur « OpenData https://www.nosdeputes.fr/recherche/opendata?format=xml

### Pagination

Les résultats indiquent un total de résultats `lastResult` et sont délivrés par lots de 20, numérotés de `start` à `end`.

Ajoutez à l'url le paramètre `&page=N` pour accéder aux résultats de la page N (contenant les éléments n° 20*(N-1)+1 à 20*N.

**Exemple :** résultats de la 2nde page d'une recherche sur « Internet » https://www.nosdeputes.fr/recherche/internet?page=2&format=xml

### Filtrage

Différents paramètres peuvent être ajoutés aux requêtes de recherche et combinés pour préciser les résultats.

Vous pouvez restreindre les résultats à :

- un **type d'objet** précis : ajouter `&object_name=OBJTYPE` avec `OBJTYPE` parmi : Parlementaire, Organisme, Intervention, Amendement, QuestionEcrite, Section, Texteloi, Commentaire

- une **période temporelle** précise : ajouter `&date=YYYYMMDD%2CYYYYMMDD` par exemple pour les résultats sur « internet » en septembre 2015 : https://www.nosdeputes.fr/recherche/internet?format=xml&date=20150901%2C20150931

- un **parlementaire** précis : ajouter `&tag=parlementaire=SLUG`

- ceux associés à des **mots-clés** spécifique : ajouter `&tag=KEYWORD1,KEYWORD2,...` par exemple pour les résultats sur « internet » effectivement taggés "internet" : https://www.nosdeputes.fr/recherche/internet?format=xml&tag=internet

### Statistiques

Vous pouvez également obtenir des statistiques agrégées sur les résultats d'une recherche (au format JSON uniquement) via les options suivantes :

- **Répartition par députés** : `&parlfacet=1`

- **Répartition par mots-clés** associés : `&tagsfacet=1`

- **Répartition temporelle** : `&timefacet=1`

  *(par périodes d'un mois si la période considérée dépasse 90 jours, par jour sinon)*

## Librairie Python [CPC-API](https://pypi.python.org/pypi/cpc_api)

Certains des points d'entrée de cette API sont accessible de manière simplifiée en langage Python à l'aide du paquet pip [`cpc-api`](https://pypi.python.org/pypi/cpc_api) dont le [code source est disponible ici](https://github.com/regardscitoyens/cpc-api).

## [ParlAPI.fr](http://parlapi.fr) : API sur l'OpenData officielle de l'AN et du Sénat

Complémentaires des données de NosDéputés.fr et NosSénateurs.fr, les plateformes OpenData de l'Assemblée nationale et du Sénat restent assez monolithiques.

Nous développons donc un accès simplifié par une API à ces jeux de données sur le site [ParlAPI.fr](http://parlapi.fr) dont le code source est disponible sur notre [Gitlab](https://git.regardscitoyens.org/regardscitoyens/parlapi).

## Conditions d'utilisation

Les informations diffusées sur NosDéputés.fr sont disponibles sous [licence CC-BY-SA](http://creativecommons.org/licenses/by-nc-sa/3.0/fr/) pour les contenus et en OpenData sous [licence ODbL](http://www.vvlibri.org/fr/licence/odbl/10/fr/legalcode) pour les données.

Cela signifie que vous êtes libre de les réutiliser, modifier et recouper dans la mesure où vous indiquez leur source (NosDéputés.fr (ou NosSénateurs.fr) par Regards Citoyens à partir de l'Assemblée nationale (ou du Sénat) et du Journal Officiel) et que vous republiez de votre côté les éventuelles données modifiées servant à une réutilisation publiée.

N'hésitez pas à nous contacter pour tout besoin spécifique à partir de ces données.
