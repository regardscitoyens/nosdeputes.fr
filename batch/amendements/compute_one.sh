#!/bin/bash

mkdir -p html json loaded

AMDTFILE=$(perl download_one.pl $1 | awk '{print $1}')
perl cut_amdmt.pl html/$AMDTFILE | python clean_subjects_amdmts.py > json/$AMDTFILE
if test -e loaded/$AMDTFILE && ! diff {json,loaded}/$AMDTFILE | grep . > /dev/null; then
  rm -f json/$AMDTFILE
fi

