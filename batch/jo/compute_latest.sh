#!/bin/bash
# Usage bash compute_latest.sh chamber
# "chamber" peut prendre pour valeur : "an" ou "senat"
for date in $(python ../common/date_gen.py $(find ./html -name "coms_$1_*" | sed -e 's/[^0-9-]//g' | sort | tail -1) --skip-first) ; do python parse_jo.py "$1" "$date" ; done
