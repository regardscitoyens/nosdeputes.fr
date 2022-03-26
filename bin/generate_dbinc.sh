#!/bin/bash

DBNAME=$(grep 'dsn:' config/databases.yml | sed 's/.*dbname=//'  | sed "s/'.*//")
USER=$(grep 'username:' config/databases.yml | sed 's/.*username: *//')
PASS=$(grep 'password:' config/databases.yml | sed 's/.*password: *//')
LEGI=$(grep ' legislature:' config/app.yml | sed 's/.*legislature: *//')
PATHAPP=$(pwd)"/"$(dirname $0)"/../"

cat bin/db.inc.example | sed "s/MYSQLID=.*/MYSQLID=\"-u $USER -p$PASS\"/" | sed "s|PATH_APP=.*|PATH_APP=$PATHAPP|" | sed "s/DBNAME=.*/DBNAME=$DBNAME/" | sed "s/.*EGISLATURE=.*/LEGISLATURE=$LEGI/" > bin/db.inc
