#!/bin/bash

source ../../bin/db.inc
source ../../bin/init_pyenv38.sh

mkdir -p html out loaded raw

perl download_via_recherche.pl $LEGISLATURE | sort -u | while read url; do
    bash compute_one.sh $url
done
