# API de NosDéputés.fr & NosSénateurs.fr

Une API a été développée pour offrir un accès Open Data simplifié aux données de NosDéputés.fr et NosSénateurs.fr aux formats XML, JSON et CSV.


## Sommaire

- [Explications](#explications)
- [Liste des parlementaires](#liste-des-parlementaires)
- [Liste des organismes (commissions, groupes, etc)](#liste-des-organismes-commissions-groupes-etc)
- [Détails de chaque parlementaire](#détails-de-chaque-parlementaire)
- [Données d'activité des parlementaires](#données-dactivité-des-parlementaires)
- [Documents et débats parlementaires](#documents-et-débats-parlementaires)
- [Résultats du moteur de recherche](#résultats-du-moteur-de-recherche)
- [Librairie Python CPC-API](#librairie-python-cpc-api)
- [ParlAPI.fr : API sur l'OpenData officielle de l'AN et du Sénat](#parlapifr--api-sur-lopendata-officielle-de-lan-et-du-sénat)
- [Exemples de réutilisations basées sur l'API](#exemples-de-réutilisations-basées-sur-lapi)
- [Conditions d'utilisation](#conditions-dutilisation)


## Explications

- **Format :** La plupart des exemples fournis ci-dessous sont donnés au format XML pour permettre plus de lisibilité dans le navigateur web. Veuillez remplacer `xml` en `json` ou `csv` pour accéder aux autres formats. Pour forcer l'affichage dans le navigateur, vous pouvez utilisez l'option `?textplain=true`.

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

  *Attention : Moins de champs sont disponibles avec cette version : autres mandats, commissions, ...*

  - format tableur (CSV) : https://www.nosdeputes.fr/deputes/csv
  - XML : https://www.nosdeputes.fr/deputes/xml
  - JSON : https://www.nosdeputes.fr/deputes/json


## Liste des organismes (commissions, groupes, etc)

- **Liste des groupes politiques :**

  https://www.nosdeputes.fr/organismes/groupe/xml

- **Liste des organes parlementaires** (commissions, délégations, missions, offices) :

  https://www.nosdeputes.fr/organismes/parlementaire/xml

- **Liste des organes extra-parlementaires** (fonctions judiciaires, internationales ou autres) :

  https://www.nosdeputes.fr/organismes/extra/xml

- **Liste des groupes d'études et d'amitié :**

  https://www.nosdeputes.fr/organismes/groupes/xml

- **Liste des parlementaires membres d'un organisme :**

  - Pour les groupes politiques, il faut utiliser l'acronyme du groupe issu de la liste des groupes ou des détails d'un député.

    Par exemple pour les non inscrits (`NI`) : https://www.nosdeputes.fr/groupe/NI/xml

  - Pour les autres organes, il faut utiliser le `slug` de l'organisme souhaité issu d'une liste des organismes.

    Par exemple pour la Commission des Affaires Économiques : https://www.nosdeputes.fr/organisme/commission-des-affaires-economiques/xml

    *Remarque : les groupes politiques sont également accessibles ainsi via leur `slug`, par exemple pour les non-inscrits : https://www.nosdeputes.fr/organisme/deputes-non-inscrits/xml*

  *Note :* par défaut l'API ne renvoie que les députés actuellement membres du groupe ou de l'organisme souhaité. À partir de la 15<sup>ème</sup> législature il est possible d'inclure également les anciens membres de l'organisme en ajoutant `?includePast=true` à la fin de l'url. Les données renvoient alors également deux champs `fin_fonction` et `groupe_a_fin_fonction` indiquant la date de fin de la fonction et le groupe politique du député à cette date. Par exemple pour la commission des affaires européennes : https://www.nosdeputes.fr/organisme/commission-des-affaires-economiques/xml?includePast=true


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

- **Synthèse des 12 derniers mois ou de toute la législature :**

  *(telle qu'affichée sur la page [synthèse](https://www.nosdeputes.fr/synthese)*

  - format tableur (CSV) : https://www.nosdeputes.fr/synthese/data/csv
  - XML : https://www.nosdeputes.fr/synthese/data/xml
  - JSON : https://www.nosdeputes.fr/synthese/data/json

- **Synthèse mensuelle :**

  *Attention : seuls les parlementaires ayant manifesté une activité sur la période sont renvoyés*
  
  Exemple pour le mois d'avril 2016 : https://www.nosdeputes.fr/synthese/201604/xml


## Documents et débats parlementaires

- **Contenu des travaux :**

  Tous les contenus textuels des différents travaux parlementaires (organismes, débats, amendements, questions, rapports, propositions de loi) sont indexés dans le moteur de recherche et peuvent donc être individuellement retrouvés et filtrés à travers cette API ([voir plus bas](#résultats-du-moteur-de-recherche)).

  Les résultats de la recherche renvoient les urls des données détaillées de chaque élément accessible via l'API.

  *Attention : l'Assemblée nationale n'ayant commencé à rendre publics les amendements déposés lors de l’examen des textes en commission qu'à partir de mai 2013 (un an après le début de la 14<sup>ème</sup> législature), seuls ceux déposés en hémicycle sont disponibles pour la 13<sup>ème</sup> législature et le début de la 14<sup>ème</sup>.*

- **Liste des dossiers législatifs** (à partir de la 15<sup>ème</sup> législature uniquement pour l'Assemblée) :

  - Triés dans l'ordre alphabétique : https://www.nosdeputes.fr/15/dossiers/nom/xml
  - Triés du plus récent au plus ancien : https://www.nosdeputes.fr/15/dossiers/date/xml
  - Triés du plus débattu au moins débattu : https://www.nosdeputes.fr/15/dossiers/plus/xml

- **Détails d'un dossier législatif** (à partir de la 15<sup>ème</sup> législature uniquement pour l'Assemblée) :

  Plus de détails sont disponibles pour chaque dossier : listes des documents associés, des séances, des intervenants et des sous-sections.

  Par exemple : https://www.nosdeputes.fr/15/dossier/1/xml

  *Note :* Comme les dossiers, ces accès peuvent être filtrés par dossier législatif en utilisant l'identifiant officiel du projet de loi sur les sites de l'institution retrouvable à la fin des urls dédiées, par exemple pour le projet de loi transparence :
    - à l'Assemblée : http://www.assemblee-nationale.fr/14/dossiers/transparence_vie_publique_pjl.asp -> `transparence_vie_publique_pjl` -> https://www.nosdeputes.fr/15/dossier/transparence_vie_publique_pjl/xml
    - au Sénat : http://www.senat.fr/dossier-legislatif/pjl12-689.html -> `pjl12-689` -> https://www.nossenateurs.fr/15/dossier/pjl12-689/xml

- **Tous les amendements déposés sur un texte :**

  Les amendements sont disponibles à partir des numéros des lois correspondantes.

  - À l'Assemblée nationale, par exemple pour le projet de loi initial relatif à la transparence de la vie publique (n° 1005) sous la 14<sup>ème</sup> législature : https://www.nosdeputes.fr/14/amendements/1005/xml

    *Attention pour l'Assemblée à bien ajuster également le numéro de législature.*

  - Au Sénat, par exemple pour le texte sur la transparence présenté en commission (n° pjl12-689) après son passage à l'Assemblée : https://www.nossenateurs.fr/amendements/20122013-689/xml

    *Attention pour le Sénat a bien reformater la partie année de l'identifiant de loi sous sa forme complète : ppl15-xxx -> `20152016-xxx`, pjl12-xxx -> `20122013-yyy`.*

- **Cosignatures d'amendements sur un texte** (expérimental) :

  Un export expérimental de données de type "graphe" (uniquement au format json) indiquant les liens entre parlementaires ayant signé ou cosigné des amendements identiques sur un texte donné est accessible en remplaçant le format par `links/json` dans l'url, par exemple pour le texte transparence à l'Assemblée : https://www.nosdeputes.fr/14/amendements/1005/links/json

- **Débats parlementaires sur un texte :**

  Comme pour les amendements, les débats sont accessibles par numéro de loi du texte correspondant. L'accès se fait en deux temps : tout d'abord en obtenant la liste des séances correspondantes, puis en accédant à la liste des interventions pour chaque séance.

  Les exemples ci-dessous s'appliquent au projet de loi initial sur la transparence discuté par l'Assemblée sous la 14<sup>ème</sup> législature.

  - Remarques :

    - *pour l'Assemblée :* pensez bien à ajuster également le numéro de législature, et pour le Sénat à ne pas l'indiquer.

    - *pour le Sénat :* pensez bien à reformater la partie année de l'identifiant de loi sous sa forme complète : ppl15-xxx -> `20152016-xxx`, pjl12-xxx -> `20122013-yyy`.

    - *Note :* Comme les dossiers, ces accès peuvent être filtrés par dossier législatif en utilisant l'identifiant officiel du projet de loi sur les sites de l'institution retrouvable à la fin des urls dédiées.

  - **Liste de tous les débats d'une chambre sur un texte :**

    Par exemple pour le texte initial (n° 1005) du projet de loi transparence proposé par le gouvernement : https://www.nosdeputes.fr/14/seances/1005/xml

    *À noter :* l'API est souple et renvoie généralement toutes les séances à toutes les étapes du dossier et non seulement celles ayant porté spécifiquement sur la version du texte numéroté.

    Pour obtenir uniquement les débats en commission ou en hémicycle, ajoutez `?commission=1` ou `?hemicycle=1`. Par exemple :

    - débats en commission (texte n° 1005) : https://www.nosdeputes.fr/14/seances/1005/xml?commission=1

    - débats en hémicycle (texte n° 1109) : https://www.nosdeputes.fr/14/seances/1109/xml?hemicycle=1

    Pour l'Assemblée, seuls quelques débats de commission sont malheureusement rattachés au projets de lois correspondants à ce jour pour des raisons techniques.

    Il peut arriver que des séances soient associées par erreur à un mauvais numéro de loi. Un filtrage plus strict (mais pas plus garanti voire trop strict) peut être obtenu en ajoutant `?dossier=OFFICIAL_PROJECT_ID` à l'url, par exemple à l'Assemblée : https://www.nosdeputes.fr/14/seances/1109/xml?dossier=transparence_vie_publique_pjl ou au Sénat : http://www.nossenateurs.fr/seances/20122013-689/xml?dossier=pjl12-689

  - **Détail de toutes les interventions d'une séance :**

    Par exemple pour la réunion (n° 1461) du 4 juin 2013 à 17h30 de la Commission des lois : https://www.nosdeputes.fr/14/seance/1461/xml

  - **Détail des seules interventions d'une séance portant sur un texte** (parfois imparfait) :

    Par exemple pour le vote solennel du texte (n° 1280) précédé de Questions au gouvernement et d'autres votes lors de la séance (n° 2140) en hémicycle du 23 juillet 2013 : https://www.nosdeputes.fr/14/seance/2140/1280/xml

    Un autre moyen imparfait de filtrer sur ces seules interventions peut être employé en ajoutant `?dossier=OFFICIAL_PROJECT_ID` à l'url, par exemple pour la même séance : https://www.nosdeputes.fr/14/seance/2140/xml?dossier=transparence_vie_publique_pjl ou pour le Sénat : http://www.nossenateurs.fr/seance/9694/xml?dossier=pjl12-689


## Résultats du moteur de recherche

Tous les résultats du [moteur de recherche](https://www.nosdeputes.fr/recherche/) sont également accessibles via l'API : il suffit de rajouter le paramètre `format=[xml|json|csv]` à l'url de recherche.

**Exemple :** résultats d'une recherche sur « OpenData » https://www.nosdeputes.fr/recherche/données?format=xml

- **Pagination :**

  Les résultats indiquent un total de résultats `lastResult` et sont délivrés par lots de 50 ou `count` (maximum 500), numérotés de `start` à `end`.

  Ajoutez à l'url le paramètre `&page=N` pour accéder aux résultats de la page N (contenant les éléments n° count*(N-1)+1 à count*N).

  **Exemple :** résultats de la 2nde page d'une recherche sur « Internet » https://www.nosdeputes.fr/recherche/internet?page=2&format=xml

- **Filtrage :**

  Différents paramètres peuvent être ajoutés aux requêtes de recherche et combinés pour préciser les résultats.

  Vous pouvez restreindre les résultats à :

  - un **type d'objet** précis : ajouter `&object_name=OBJTYPE` avec `OBJTYPE` parmi : `Parlementaire`, `Organisme`, `Intervention`, `Amendement`, `QuestionEcrite`, `Section`, `Texteloi`, `Commentaire`, par exemple pour tous les TexteLoi : https://www.nosdeputes.fr/recherche/?object_name=Texteloi&format=xml&count=500

  - une **période temporelle** précise : ajouter `&date=YYYYMMDD%2CYYYYMMDD` par exemple pour les résultats sur « internet » en janvier 2016 : https://www.nosdeputes.fr/recherche/internet?format=xml&date=20160101%2C20160131

  - un **parlementaire** précis : ajouter `&tag=parlementaire=SEARCHSLUG` avec SEARCHSLUG formé ainsi : `prenom+nom`. sans accents ni cédille et en remplaçant les espaces par des + (mais en conservant le trait d'union des prénoms et noms composés, par exemple : `Francois+de+Rugy` `cedric+villani`, `carole+bureau-bonnard` ,`marie-christine+verdier-jouclas`)

  - ceux associés à des **mots-clés** spécifique : ajouter `&tag=KEYWORD1,KEYWORD2,...` par exemple pour les résultats sur « internet » effectivement taggés "internet" : https://www.nosdeputes.fr/recherche/internet?format=xml&tag=internet

- **Statistiques :**

  Vous pouvez également obtenir des statistiques agrégées sur les résultats d'une recherche (au format JSON uniquement) via les options suivantes :

  - **Répartition par députés :** `&parlfacet=1`

  - **Répartition par mots-clés** associés : `&tagsfacet=1`

  - **Répartition temporelle :** `&timefacet=1`

  *(par périodes d'un mois si la période considérée dépasse 90 jours, par jour sinon)*


## Librairie Python [CPC-API](https://pypi.python.org/pypi/cpc_api)

Certains des points d'entrée de cette API sont accessibles de manière simplifiée en langage Python à l'aide du paquet pip [`cpc-api`](https://pypi.python.org/pypi/cpc_api) dont le [code source est disponible ici](https://github.com/regardscitoyens/cpc-api).

## Module PowerShell [RegardsCitoyensPS](https://github.com/Stephanevg/RegardsCitoyenPS)

L'API est également accessible depuis PowerShell (sous Windows, MAC & Linux) à l'aide du module [`RegardsCitoyensPS`](https://github.com/Stephanevg/RegardsCitoyenPS) réalisé indépendamment par [@StephaneVG](https://github.com/Stephanevg). Plus de détails sont disponibles sur la page du projet.

## [ParlAPI.fr](http://parlapi.fr) : API sur l'OpenData officielle de l'AN et du Sénat

Complémentaires des données de NosDéputés.fr et NosSénateurs.fr, les plateformes OpenData de l'Assemblée nationale et du Sénat restent assez monolithiques.

Nous développons donc un accès simplifié par une API à ces jeux de données sur le site [ParlAPI.fr](http://parlapi.fr) dont le code source est disponible sur notre [Gitlab](https://git.regardscitoyens.org/regardscitoyens/parlapi).


## Exemples de réutilisations basées sur l'API

- [LaFabriqueDeLaLoi.fr](https://www.lafabriquedelaloi.fr)

- [DirectParlement](https://regardscitoyens.github.io/direct-parlement) : outil de génération d'incruste pour encart dans vidéo live de débat parlementaire utilisé par [Accropolis](http://accropolis.fr) ([code-source](https://github.com/regardscitoyens/direct-parlement))

- [Trombinoscope des parlementaires](https://regardscitoyens.github.io/trombi/)

- [LobbyTrack](https://github.com/regardscitoyens/LobbyTrack) : outil d'identification des travaux parlementaires qui se sont inspirés d'un document texte de lobbying

- Analyse sémantique des principaux thèmes investis par les députés dans le [Trombinoscope de Contexte](https://www.contexte.com/article/pouvoirs/les-577-deputes-francais-de-lassemblee-nationale_71506.html)

- [Suivi mensuel de l'activité des députés bretons par Le Télégramme](http://www.letelegramme.fr/dataspot/rentree-parlementaire-suivez-l-activite-des-deputes-bretons-19-09-2017-11669870.php)

- [Visualisation des votes des députés dans l'hémicycle par MédiaPart](https://www.mediapart.fr/journal/france/031017/loi-antiterroriste-qui-vote-quoi?onglet=full)

- [Synthèse globale](https://regardscitoyens.github.io/synthese-globale/) : mini-application agrégeant les données de synthèse mensuelle pour nous permettre de répondre aux sollicitations des députés nous demandant en cours de mandat leur bilan complet ([code-source](https://github.com/regardscitoyens/synthese-globale))


## Conditions d'utilisation

Les informations diffusées sur NosDéputés.fr sont disponibles sous [licence CC-BY-SA](http://creativecommons.org/licenses/by-nc-sa/3.0/fr/) pour les contenus et en OpenData sous [licence ODbL](http://www.vvlibri.org/fr/licence/odbl/10/fr/legalcode) pour les données.

Cela signifie que vous êtes libre de les réutiliser, modifier et recouper dans la mesure où vous indiquez leur source (NosDéputés.fr (ou NosSénateurs.fr) par Regards Citoyens à partir de l'Assemblée nationale (ou du Sénat) et du Journal Officiel) et que vous republiez de votre côté les éventuelles données modifiées servant à une réutilisation publiée.

N'hésitez pas à nous contacter pour tout besoin spécifique à partir de ces données.
