#!/bin/bash

bash download_from_opendata.sh

curl -s https://pad.regardscitoyens.org/p/documents-manquants/export/txt | sed 's/_/\//g' | grep assemblee-nationale | sed 's/.*http/http/' | sed 's/asp.*/asp/' | while read url ; do
    bash parse_url_from_opendata.sh $url outtest/$(echo $url | base64 -w 0 )".json"
done
