# Widget de l'activité d'un parlementaire

Ce widget permet d'embarquer des morceaux de NosDéputés.fr sur un autre site en renvoyant une zone html présentant au choix pour un député :
- son titre
- sa photo
- son graphique d'activité
- sa barre d'activité
- ses mots-clés

## Accès

L'accès est proposé via les URLs de la forme http://www.nosdeputes.fr/widget/#NOM avec #NOM idéalement de la forme "slug" (fourni dans les listes et informations individuelles de députés), "Prénom Nom" ou encore (sous réserve d'homonymes) "Nom", par exemple : http://www.nosdeputes.fr/widget/ayrault

L'url doit être légèrement ajustée en fonctions de la législature souhaitée :
- pour la 13ème législature : nosdeputes.fr/widget
- pour la 14ème législature : nosdeputes.fr/widget14
- pour la 15ème législature : nosdeputes.fr/widget15

Pour un usage simplifié, nous proposons une interface web permettant de réaliser des essais et de disposer d'une simple ligne html à insérer : http://www.nosdeputes.fr/widget14

## Configuration

Plusieurs options peuvent également être passées en paramètres :
- Les options `notitre`, `nophoto`, `nographe`, `noactivite` et `notags` peuvent être utilisées de façon complémentaire pour n'afficher qu'une partie des éléments (activer avec valeur 1 ou true).
- L'option `maxtags` peut être précisée pour limiter le nombre de mots-clés affichés. Par défaut la valeur maximale affichée est de 40 mots-clés.
- L'option `width` permet de définir la largeur totale de la zone produite. Par défaut la taille normale est de 935 pixels.
- L'option `iframe` est à employer lors de l'usage d'un iframe html pour intégrer le widget, afin d'assurer le bon fonctionnement des liens (activer avec valeur 1 ou true).

## Exemples

- Les 100 premiers mots-clés de Bernard Accoyer, son titre, et son graphe : http://www.nosdeputes.fr/widget/accoyer?nophoto=1&noactivite=1&maxtags=100
- Une vignette de 150px de large avec le nom, la photo et les 20 principaux mots-clés de Jean-François Copé : http://www.nosdeputes.fr/widget/cope?nographe=1&noactivite=1&width=150&maxtags=20
- La photo, le graphe d'activité et la barre d'activité de François Hollande sur 600 pixels de large : http://www.nosdeputes.fr/widget/hollande?notags=1&notitre=1&width=600

Il est relativement aisé d'inclure ces éléments sur un autre site à l'aide d'iframe en html, de file_get_contents en php, d'ajax en javascript...

Par exemple la page http://www.regardscitoyens.org/gouvernement2012/ est écrite tout simplement ainsi :

```php
<?php
  $anciens_deputes = array("HOLLANDE", "Ayrault", "FABIUS", "TAUBIRA", "MOSCOVICI", "TOURAINE", "VALLS", "MONTEBOURG", "SAPIN", "FILIPPETTI", "FIORASO", "LEBRANCHU", "LUREL", "FOURNEYRON", "CAHUZAC", "PAU-LANGEVIN", "VIDALIES", "BATHO", "LAMY", "CAZENEUVE", "DELAUNAY", "PINEL", "CUVILLIER");
  foreach ($anciens_deputes as $d)
    echo file_get_contents('http://www.nosdeputes.fr/widget/'.$d.'?maxtags=15');
?>
```
