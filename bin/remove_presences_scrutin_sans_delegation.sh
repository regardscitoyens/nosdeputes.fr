#!/bin/bash

ID=$1

source bin/db.inc

# Remove preuves presence from scrutin ID :

presences=$(echo "SELECT p.id FROM preuve_presence pp LEFT JOIN presence p ON p.id = pp.presence_id WHERE pp.source = 'http://www2.assemblee-nationale.fr/scrutins/detail/(legislature)/15/(num)/$ID'" | mysql $MYSQLID $DBNAME | grep -v id | tr '\n' ',' | sed 's/,$//')
preuves=$(echo "SELECT pp.id FROM preuve_presence pp LEFT JOIN presence p ON p.id = pp.presence_id WHERE pp.source = 'http://www2.assemblee-nationale.fr/scrutins/detail/(legislature)/15/(num)/$ID'" | mysql $MYSQLID $DBNAME | grep -v id | tr '\n' ',' | sed 's/,$//')
echo "DELETE FROM preuve_presence WHERE id in ($preuves)" | mysql $MYSQLID $DBNAME -vv

# Remove présences résultantes with no preuves :

todelete=$(echo "SELECT t.id FROM (SELECT COUNT( pp.id ) AS preuves, p.* FROM presence p LEFT JOIN preuve_presence pp ON pp.presence_id = p.id GROUP BY p.id) t WHERE t.preuves = 0" | mysql $MYSQLID $DBNAME | grep -v id | tr '\n' ',' | sed 's/,$//')
echo "DELETE FROM presence WHERE id IN ($todelete)" | mysql $MYSQLID $DBNAME -vv

# Fix nb_preuves of presences hemicycle restantes from interventions :

echo "UPDATE presence SET nb_preuves = 1 WHERE id in ($presences)" | mysql $MYSQLID $DBNAME -vv
