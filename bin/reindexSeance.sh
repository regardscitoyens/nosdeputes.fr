#!/bin/bash

seance=$1
source bin/db.inc

for id in `echo "SELECT id from intervention where seance_id = $seance" | mysql $MYSQLID $DBNAME | grep -v id`; do
  php symfony reindex:SolrObject Intervention $id
done

