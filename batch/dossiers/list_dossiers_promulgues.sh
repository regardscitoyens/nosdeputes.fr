#!/bin/bash
. ../../bin/db.inc

curl -s "http://www.assemblee-nationale.fr/$LEGISLATURE/documents/index-promulgations.asp" | iconv -f ISO88591 -t UTF-8 | grep "/dossiers/" | sed 's|.* href="/'$LEGISLATURE'/dossiers/|http://www.assemblee-nationale.fr/'$LEGISLATURE'/dossiers/|' | sed 's/asp["#].*/asp/'
