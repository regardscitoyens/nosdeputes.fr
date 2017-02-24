#!/bin/bash

source $(dirname $0)/../bin/db.inc

COMMISSIONS="9, 10, 11, 12, 13, 14, 15, 16, 20"
GROUPES="2, 3, 4, 5, 6, 7, 8, 427, 428, 858, 1002, 1128, 1023, 1118"

YEAR=$1
year=$YEAR
if ! test "$year"; then
	echo "ERROR: $0 <annee_debut_session>";
	exit 1;
fi

CSVDIR=$(dirname $0)/../csv
mkdir -p $CSVDIR

echo "SELECT pa.id as id, pa.nom as nom, pa.nom_de_famille as nom_de_famille, pa.groupe_acronyme as groupe, pa.nom_circo, pa.debut_mandat as debut_mandat, pa.fin_mandat as fin_mandat, o.slug as commission FROM parlementaire pa LEFT JOIN parlementaire_organisme po ON ( po.parlementaire_id = pa.id ) JOIN organisme o ON ( o.id = po.organisme_id ) WHERE o.id IN ("$COMMISSIONS") ORDER BY id" | mysql $MYSQLID $DBNAME | sed 's/\t/;/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > $CSVDIR/deputes.txt

echo 'SELECT distinct(date), id FROM seance WHERE DAYOFWEEK(date) = 4 AND YEAR(date) IN ( 2012, 2013, 2014, 2015, 2016, 2017 ) AND type = "hemicycle" AND ( moment LIKE "1ère%" OR moment LIKE "08:%" OR moment LIKE "09:%" OR moment LIKE "10:%" OR moment LIKE "11:%" OR moment LIKE "12:%" OR moment LIKE "13:%" ) order by date desc' | mysql $MYSQLID $DBNAME | sed 's/\t/;/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > $CSVDIR/seances_hemicycle_merc_matin.txt

rm -f $CSVDIR/presences_deputes_mercredimatin.csv $CSVDIR/commissions_mercredimatin.csv

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

  echo 'SELECT pa.id as id, "'$year'-'$month'", count(DISTINCT(p.date)) AS n_'$year'_'$month' FROM parlementaire pa LEFT JOIN presence p ON ( pa.id = p.parlementaire_id ) LEFT JOIN seance s ON ( s.id = p.seance_id ) WHERE DAYOFWEEK(p.date) = 4 AND MONTH(p.date) = '$month' AND YEAR(p.date) = '$year' AND s.type = "commission" AND s.organisme_id IN ('$COMMISSIONS') AND ( s.moment LIKE "1ère%" OR s.moment LIKE "08:%" OR s.moment LIKE "09:%" OR s.moment LIKE "10:%" OR s.moment LIKE "11:%" OR s.moment LIKE "12:%" OR s.moment LIKE "13:%" ) AND pa.debut_mandat <= CAST("'$year'-'$month'-01" as DATE) AND ( pa.fin_mandat IS NULL OR pa.fin_mandat >= CAST("'$year2'-'$month2'-01" as DATE) ) GROUP BY pa.id ORDER BY pa.id' | mysql $MYSQLID $DBNAME | sed 's/\t/;/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > $CSVDIR/$year-$month.txt

tail -n +2 $CSVDIR/$year-$month.txt >> $CSVDIR/presences_deputes_mercredimatin.csv

  echo 'SELECT o.slug as commission, "'$year'-'$month'", count(DISTINCT(s.date)) AS n_'$year'_'$month'_com FROM organisme o LEFT JOIN seance s ON ( s.organisme_id = o.id ) WHERE s.type = "commission" AND DAYOFWEEK(s.date) = 4 AND MONTH(s.date) = '$month' AND YEAR(s.date) = '$year' AND ( s.moment LIKE "1ère%" OR s.moment LIKE "08:%" OR s.moment LIKE "09:%" OR s.moment LIKE "10:%" OR s.moment LIKE "11:%" OR s.moment LIKE "12:%" OR s.moment LIKE "13:%" ) AND o.id IN ('$COMMISSIONS') GROUP BY o.id' | mysql $MYSQLID $DBNAME | sed 's/\t/;/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > $CSVDIR/$year-$month-com.txt

  n_hemi=`grep "$year-$month" $CSVDIR/seances_hemicycle_merc_matin.txt | wc -l`
  if [ $n_hemi -gt 0 ]; then
    echo "Attention : il y a une ou plusieurs séances d'hémicycle le mercredi matin pour ce mois, correction en cours de $CSVDIR/$year-$month-com.txt"
    cat $CSVDIR/$year-$month-com.txt | awk 'BEGIN {FS=";"; OFS=";"} {value = $2; f (NR > 1) value -= "'$n_hemi'"; print $1,value}' > /tmp/corcom
    mv /tmp/corcom $CSVDIR/$year-$month-com.txt
  fi

tail -n +2 $CSVDIR/$year-$month-com.txt >> $CSVDIR/commissions_mercredimatin.csv

done

echo 'SELECT distinct(p.id) as id FROM parlementaire p JOIN parlementaire_organisme po ON ( po.parlementaire_id = p.id ) WHERE ( po.organisme_id = 1 AND po.fonction NOT LIKE "secr%" ) OR ( po.organisme_id IN ( '$GROUPES' ) AND po.fonction like "president%" ) OR ( p.nom_circo IN ( "Guadeloupe", "Martinique", "Guyane", "Saint-Pierre-et-Miquelon", "Mayotte", "Wallis-et-Futuna" ) ) OR p.nom_circo LIKE "R%union" OR p.nom_circo LIKE "Polynesie%" OR p.nom_circo LIKE "Nouvelle-Caledonie" OR p.nom_circo LIKE "Saint-Barth%" order by p.id' | mysql $MYSQLID $DBNAME  | sed 's/$/;1/' | sed 's/id;1/id;exonere/g' | iconv --from-code=ISO-8859-1 --to-code=UTF-8 > $CSVDIR/exoneres.txt

tail -n +2 $CSVDIR/deputes.txt  | sort -t ';' -k 8,8 > $CSVDIR/deputes.sorted_by_commission.txt
cat $CSVDIR/commissions_mercredimatin.csv | sort -t ';' -k 1,1 > $CSVDIR/commissions_mercredimatin.sorted_b_commission.csv
join -t ';' -1 8 -2 1 $CSVDIR/deputes.sorted_by_commission.txt $CSVDIR/commissions_mercredimatin.sorted_b_commission.csv | awk -F ';' '{print $2";"$9";"$3";"$4";"$5";"$6";"$7";"$8";"$1";"$10}' | sed 's/;/@/' | sort -t ';' -k 1,1 > $CSVDIR/deputes_with_commissions_mercredimatin.csv
tail -n +2 $CSVDIR/presences_deputes_mercredimatin.csv | sed 's/;/@/' | sort -t ';' -k 1,1 > $CSVDIR/presences_deputes_mercredimatin.sorted.csv

echo "id;mois;prenom nom depute;nom depute;groupe;departement;date debut mandat;date fin mandat;commission;nb seances commission mercredi matin;nb presences mercredi matin;nb abscences;sanction" > $CSVDIR/presences_deputes_mercredimatin_with_commissions.sorted.csv
join -t ';' -a 1 -1 1 -2 1 $CSVDIR/deputes_with_commissions_mercredimatin.csv $CSVDIR/presences_deputes_mercredimatin.sorted.csv | sed 's/@/;/' | sed 's/\([^0-9];[0-9]\)$/\1;0/' | awk -F ';' '{SANCTION="NON"; if ($10 - $11 > 2) SANCTION = "RISQUE" ; if ($7 < $2"-01" && ($8 == 'NULL' || $2"-01" < $8)) print $0";"$10-$11";"SANCTION}' >> $CSVDIR/presences_deputes_mercredimatin_with_commissions.sorted.csv

tail -n +2 $CSVDIR/exoneres.txt | sed 's/;.*//' | while read exo ; do
	sed -i "s/^$exo;.*//" $CSVDIR/presences_deputes_mercredimatin_with_commissions.sorted.csv
done

grep -i "[a-z]" $CSVDIR/presences_deputes_mercredimatin_with_commissions.sorted.csv > $CSVDIR/$YEAR"_presences_deputes_mercredimatin_with_commissions.csv"


echo "Sanction généré dans $CSVDIR/"$YEAR"_presences_deputes_mercredimatin_with_commissions.csv";
echo
echo "Il reste à exclure les députés hors d'europe, ceux qui devaient être"
echo "présents dans des fonctions internationales ou ont d'autres excuses "
echo "reconnues valable au regard du règlement"
