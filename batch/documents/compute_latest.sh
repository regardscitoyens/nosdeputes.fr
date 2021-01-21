#!/bin/bash

source ../../bin/db.inc

mkdir -p pjl ppl ppr rap ta out

bash download_from_opendata.sh

perl download_docs.pl $LEGISLATURE | while read url ; do
  echo $url
  bash parse_url_from_opendata.sh "$url"
done;
