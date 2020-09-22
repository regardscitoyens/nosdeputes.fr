#!/bin/bash
# Usage bash compute_latest.sh chamber
# "chamber" peut prendre pour valeur : "an" ou "senat"

source ../../bin/db.inc

mkdir -p opendata tmp json

if test $1 = "all"; then
    echo > tmp/listfile.old
else
    find ./opendata -name '*tar.gz' > tmp/listfile.old
fi
cd opendata
wget -q -r --no-parent -l 1 -c --reject  'Freemium_*' --reject '*pdf' https://echanges.dila.gouv.fr/OPENDATA/JORF/
cd -
find ./opendata -name '*tar.gz' > tmp/listfile.new
cd tmp
diff listfile.old listfile.new | grep '^>' | sed 's/^. //' | while read file; do
    tar zxf '../'$file
done
cd -
touch tmp/0
rgrep -l "embres présents ou excusés" tmp/[0-9]* | while read xml; do
    python parse_jo.py an $xml
done
rm -rf tmp
