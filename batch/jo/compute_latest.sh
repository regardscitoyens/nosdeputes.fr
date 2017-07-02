#!/bin/bash
# Usage bash compute_latest.sh chamber
# "chamber" peut prendre pour valeur : "an" ou "senat"

source ../../bin/db.inc

mkdir -p html

lastpdf=$(find ./html -name "coms_$1_*" | sed -e 's/[^0-9-]//g' | sort | tail -1)
if [ -z "$lastpdf" ]; then
  startyear=$((LEGISLATURE * 5 + 1942))
  lastpdf="$startyear-06-23"
fi
for date in $(python ../common/date_gen.py $lastpdf --skip-first) ; do python parse_jo.py "$1" "$date" ; done
