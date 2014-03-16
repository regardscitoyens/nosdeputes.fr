#!/bin/bash

curl -s "http://www.assemblee-nationale.fr/14/documents/index-promulgations.asp" | iconv -f ISO88591 -t UTF-8 | grep "/dossiers/" | sed 's|.* href="/14/dossiers/|http://www.assemblee-nationale.fr/14/dossiers/|' | sed 's/asp["#].*/asp/'
