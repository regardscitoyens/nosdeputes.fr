#!/bin/bash

ID_OPENDATA=$1

mkdir -p opendata/html opendata/document

wget -q -N -O opendata/html/$1.html "http://www.assemblee-nationale.fr/dyn/opendata/$1.html"
wget -q -N -O opendata/document/$1.json "http://www.assemblee-nationale.fr/dyn/opendata/$1.json"

python3 parse_opendata_documents.py opendata/document/$1.json > out/$1.json
