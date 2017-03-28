#!/bin/bash

tempDirOD="OpenDataAN"
urlAmdtOD="http://data.assemblee-nationale.fr/static/openData/repository/LOI/amendements_legis/Amendements_XIV.json.zip" 
AmdtODFile="Amendements_XIV.json"

rm -rf $tempDirOD

mkdir -p $tempDirOD

cd $tempDirOD

wget $urlAmdtOD
unzip $AmdtODFile.zip

cd ..
python parseAmdtsFromANOpenData.py

