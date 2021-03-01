#!/bin/bash

. ../../bin/db.inc
mkdir -p html

for url in `echo "SELECT source FROM texteloi WHERE date > DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND type LIKE 'Proposition%' ORDER BY numero"  | mysql $MYSQLID $DBNAME | grep -v source`; do
    bash parse_url_from_opendata.sh "$url"
done;
