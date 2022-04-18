#!/bin/bash

source ../../bin/db.inc

mkdir -p html out loaded raw

perl download_via_recherche.pl $LEGISLATURE | while read url; do
	bash compute_one.sh $url
done
