#!/bin/bash

cd batch/amendements
bash runAmdtOD.sh
cd -
while ls batch/amendements/OpenDataAN/amdts_* | grep [a-z] > /dev/null ; do
    php symfony cc --env=test  --app=frontend > /dev/null
    php symfony update:Amdmts
    php symfony load:Amdmts
done
