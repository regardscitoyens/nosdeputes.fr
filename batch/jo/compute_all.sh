#/bin/bash

for date in $(python ../common/date_gen.py "2015-12-31") ; do python parse_jo.py "an" "$date" ; done
