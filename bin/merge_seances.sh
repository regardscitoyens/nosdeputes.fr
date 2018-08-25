#!/bin/bash

. bin/db.inc

OLD=$1
NEW=$2

echo "UPDATE presence SET seance_id = $2 WHERE seance_id = $1 ;" | mysql $MYSQLID $DBNAME
echo "UPDATE intervention SET seance_id = $2 WHERE seance_id = $1 ;" | mysql $MYSQLID $DBNAME
echo "DELETE FROM seance WHERE id = $1;" | mysql $MYSQLID $DBNAME
