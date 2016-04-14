#/bin/bash

for date in $(python date_gen.py "2015-12-31") ; do python parse.py "an" "$date" ; done
