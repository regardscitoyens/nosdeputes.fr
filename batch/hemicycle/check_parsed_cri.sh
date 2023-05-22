#!/bin/bash

JSON=$1

if ! test -s $JSON; then
  echo "There's nothing in $JSON :("
  exit
fi

source ../../bin/db.inc
DATE=$(head -1 $JSON            |
       sed 's/^.*"date": "//'   |
       sed 's/".*$//')
DEPUTES=$(echo "SELECT nom from parlementaire
                WHERE fin_mandat IS NULL
                   OR fin_mandat >= '$DATE'"    |
          mysql $MYSQLID $DBNAME                |
          grep -v '^nom'                        |
          sed 's/[\Wàâäéèêëîïôöùûüç\/]/./ig'    |
          sed 's/[ \-]/[ \\-]/g'                |
          tr '\n' '|'                           |
          sed 's/|$//')

date=$(head -1 $JSON                |
  sed 's/^.*"date": "/\nDATE:    /' |
  sed 's/", .*"heure": "/ - /'      |
  sed 's/".*$//')
echo $date
echo "-------------"

echo "Didascalies :"
echo "-------------"
grep '"intervenant": ""' $JSON      |
  sed 's/^.*"intervention": "/-> /' |
  sed 's/".*$//'                    |
  sed 's/<\/p><p>/<\p>\n-> <p>/g'   |
  sed 's/\. – /.<\p>\n-> <p>/g'     |
  sort -u
echo "-------------"
echo
echo

echo "Sommaire :"
echo "-------------"
cat $JSON                               |
  sed 's/^.*"contexte": "//'            |
  sed 's/",.*"numeros_loi": "/\t\t| /'  |
  sed 's/".*$//'                        |
  grep . | uniq
echo "-------------"
echo
echo

echo "Parenthèses :"
echo "-------------"
cat $JSON                                          |
  sed -r "s/\([^)]*(précédemment réservés?|par priorité|seconde délibération|coordination|examen simplifiée|mixte paritaire|MODEM et indépendants|[A-Z]\w{1,6}|membre de l'intergroupe NUPES)\)/ /g"                 |
  sed 's/(\(…\|MoDem\|suite\|état [A-D]\))/ /ig'   |
  sed -r 's/\((loi )?[A-Z0-9 -\&]+(\-m)?\)/ /g'    |
  sed -r 's/\(n[os\s°]+\s*[0-9]+[^)]*\)/ /g'       |
  sed -r 's/\(pro[^)]* de loi[^)]*\)/ /g'          |
  grep '('                                         |
  sed 's/^.*"contexte": "//'                       |
  sed 's/",.*"intervention": "/  |   …/'           |
  sed -r 's/   ….*?([^")]{5,8}\()/   …\1/'    |
  sed -r 's/(\)[^"(]{0,8})[^")]*".*$/\1…/'         |
  grep -v '(.*  |  [^(]*$'
echo "-------------"
echo
echo

echo "Intervenants:"
echo "-------------"
echo " - Députés:"
grep -v '"intervenant": ""' $JSON   |
  sed 's/^.*"intervenant": "//'     |
  sed 's/",.*"fonction": "/\t\t|  /'|
  sed 's/".*$//'                    |
  sort | uniq -c                    |
  grep -iP "$DEPUTES"
echo "-------------"
echo " - Autres:"
grep -v '"intervenant": ""' $JSON   |
  sed 's/^.*"intervenant": "//'     |
  sed 's/",.*"fonction": "/\t\t|  /'|
  sed 's/".*$//'                    |
  sort | uniq -c                    |
  grep -viP "$DEPUTES"
echo "-------------"
echo
echo

echo $date

head -1 $JSON                       |
  sed 's/^.*"source": "/SOURCE:  /' |
  sed 's/[#"].*$//'
