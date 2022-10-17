#!/bin/bash

mkdir -p html json loaded

AMDTFILE=$(perl download_one.pl $1 | awk '{print $1}')
python parse_amendement.py html/$AMDTFILE > json/$AMDTFILE
#if test -e loaded/$AMDTFILE && ! diff {json,loaded}/$AMDTFILE | grep . > /dev/null; then
#  rm -f json/$AMDTFILE
#fi

