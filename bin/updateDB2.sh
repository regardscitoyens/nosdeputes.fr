#!/bin/bash

echo "OBSOLETE"
exit

source ./bin/db.inc

php symfony cc
php symfony doctrine:build-model
php symfony doctrine:build-form
php symfony doctrine:build-filters
php symfony doctrine:build-sql

cat bin/updateDB2.1.sql | mysql $MYSQLID $DBNAME

php symfony correct:LoiAmendements

cat bin/updateDB2.2.sql | mysql $MYSQLID $DBNAME

php symfony cc

