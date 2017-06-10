#!/bin/bash

exit

#exemple Migaud

source bin/db.inc

#Créer personnalité, collect ID

migaudid=3301

echo "SELECT id FROM intervention WHERE date >= '2010-03-02' AND parlementaire_id = 250" | mysql $MYSQLID $DBNAME | grep -v id | sed 's/^\(.*\)$/UPDATE intervention SET personnalite_id = "'$migaudid'", parlementaire_id = "" WHERE id = \1 LIMIT 1 ;/' | mysql $MYSQLID $DBNAME

echo 'select pr.id from presence pr left join seance s on s.id = pr.seance_id where pr.parlementaire_id = 250 and s.date > "2010-03-02"' | mysql $MYSQLID $DBNAME | grep -v id > /tmp/ids_presences_migaud

for id in `cat /tmp/ids_presences_migaud`; do
  echo "DELETE FROM presence WHERE id = $id LIMIT 1 ;" | mysql $MYSQLID $DBNAME
  echo "DELETE FROM preuve_presence WHERE presence_id = $id ;" | mysql $MYSQLID $DBNAME
done

php symfony top:Deputes


