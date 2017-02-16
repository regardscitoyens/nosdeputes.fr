#!/bin/bash

source /home/nosdeputes/prod/bin/db.inc

echo "SELECT pa.id as id, pa.nom as nom, pa.nom_de_famille as nom_de_famille, pa.groupe_acronyme as groupe, pa.nom_circo, pa.debut_mandat as debut_mandat, pa.fin_mandat as fin_mandat, o.slug as commission FROM parlementaire pa LEFT JOIN parlementaire_organisme po ON ( po.parlementaire_id = pa.id ) JOIN organisme o ON ( o.id = po.organisme_id ) WHERE o.id IN ( 2, 11, 13, 22, 204, 211, 212, 237 ) ORDER BY id" | mysql $MYSQLID $DBNAME | sed 's/\t/;/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > csv/deputes.txt

year=2009

echo 'SELECT distinct(date), id FROM seance WHERE DAYOFWEEK(date) = 4 AND YEAR(date) IN ( 2007, 2008, 2009, 2010 ) AND type = "hemicycle" AND ( moment LIKE "1ère%" OR moment LIKE "08:%" OR moment LIKE "09:%" OR moment LIKE "10:%" OR moment LIKE "11:%" OR moment LIKE "12:%" OR moment LIKE "13:%" ) order by date desc' | mysql $MYSQLID $DBNAME | sed 's/\t/;/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > csv/seances_hemicycle_merc_matin.txt

for month in 10 11 12 01 02 03 04 05 06; do

  if [ $month -eq 01 ]; then
    year=$(($year + 1))
  fi
  if [ $month -eq 12 ]; then
    month2=01
    year2=$(($year + 1))
  else
    month2=$(($month + 1))
    if [ $month2 -le 9 ]; then
      month2="0$month2"
    fi
    year2=$year
  fi

  echo $year'-'$month'-01->'$year2'-'$month2'-01 : csv/'$year'-'$month'.txt'

  echo 'SELECT pa.id as id, count(DISTINCT(p.date)) AS n_'$year'_'$month' FROM parlementaire pa LEFT JOIN presence p ON ( pa.id = p.parlementaire_id ) LEFT JOIN seance s ON ( s.id = p.seance_id ) WHERE DAYOFWEEK(p.date) = 4 AND MONTH(p.date) = '$month' AND YEAR(p.date) = '$year' AND s.type = "commission" AND s.organisme_id IN ( 2, 11, 13, 22, 204, 211, 212, 237, 239 ) AND ( s.moment LIKE "1ère%" OR s.moment LIKE "08:%" OR s.moment LIKE "09:%" OR s.moment LIKE "10:%" OR s.moment LIKE "11:%" OR s.moment LIKE "12:%" OR s.moment LIKE "13:%" ) AND pa.debut_mandat <= CAST("'$year'-'$month'-01" as DATE) AND ( pa.fin_mandat IS NULL OR pa.fin_mandat >= CAST("'$year2'-'$month2'-01" as DATE) ) GROUP BY pa.id ORDER BY pa.id' | mysql $MYSQLID $DBNAME | sed 's/\t/;/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > csv/$year-$month.txt

  echo 'SELECT o.slug as commission, count(DISTINCT(s.date)) AS n_'$year'_'$month'_com FROM organisme o LEFT JOIN seance s ON ( s.organisme_id = o.id ) WHERE s.type = "commission" AND DAYOFWEEK(s.date) = 4 AND MONTH(s.date) = '$month' AND YEAR(s.date) = '$year' AND ( s.moment LIKE "1ère%" OR s.moment LIKE "08:%" OR s.moment LIKE "09:%" OR s.moment LIKE "10:%" OR s.moment LIKE "11:%" OR s.moment LIKE "12:%" OR s.moment LIKE "13:%" ) AND o.id IN ( 2, 11, 13, 22, 204, 211, 212, 237 ) GROUP BY o.id' | mysql $MYSQLID $DBNAME | sed 's/\t/;/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > csv/$year-$month-com.txt

  n_hemi=`grep "$year-$month" csv/seances_hemicycle_merc_matin.txt | wc -l`
  if [ $n_hemi -gt 0 ]; then
    echo "Attention : il y a une ou plusieurs séances d'hémicycle le mercredi matin pour ce mois, correction en cours de csv/$year-$month-com.txt"
    cat csv/$year-$month-com.txt | awk 'BEGIN {FS=";"; OFS=";"} {value = $2; f (NR > 1) value -= "'$n_hemi'"; print $1,value}' > /tmp/corcom
    mv /tmp/corcom csv/$year-$month-com.txt
  fi

done

echo 'SELECT distinct(p.id) as id FROM parlementaire p JOIN parlementaire_organisme po ON ( po.parlementaire_id = p.id ) WHERE ( po.organisme_id = 32 AND po.fonction NOT LIKE "secretaire" ) OR ( po.organisme_id IN ( 3, 8, 35, 42, 59 ) AND po.fonction like "president%" ) OR ( p.nom_circo IN ( "Guadeloupe", "Martinique", "Guyane", "Saint-Pierre-et-Miquelon", "Mayotte", "Wallis-et-Futuna" ) ) OR p.nom_circo LIKE "Reunion" OR p.nom_circo LIKE "Polynesie%" OR p.nom_circo LIKE "Nouvelle-Caledonie" order by p.id' | mysql $MYSQLID $DBNAME  | sed 's/$/;1/' | sed 's/id;1/id;exonere/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > csv/exoneres.txt

php parse-all.php | sed 's/;$//' > confiseurs.csv

