#!/bin/bash
# Usage bash compute_latest.sh chamber
# "chamber" peut prendre pour valeur : "an" ou "senat"
for date in $(python ../common/date_gen.py $(find ./html -name "coms_$1_*" | sed -e 's/.*_//' | sed -e 's/\..*//' | sort | tail -1)) ; do python parse_jo.py "$1" "$date" ; done
