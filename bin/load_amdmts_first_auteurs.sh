#!/bin/bash

cd batch/amendements
bash runAmdtOD.sh
cd OpenDataAN
cat amdts_*  > amdts_all.json
split -l 100 -a 10 --additional-suffix=.json amdts_all.json "amendements_"
rm -f amdts_*.json
cd ../../..
while ls batch/amendements/OpenDataAN/amendements_* | grep [a-z] > /dev/null ; do
    php symfony cc --env=test  --app=frontend > /dev/null
    php symfony update:Amdmts
    php symfony load:Amdmts
done
