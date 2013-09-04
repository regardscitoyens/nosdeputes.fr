#!/bin/bash

curl -s http://www.senat.fr/dossiers-legislatifs/lois-promulguees.html | 
    tr '\n' ' ' | iconv -f iso88591 -t utf8 | sed 's|</a>|\n|g' | 
    sed "s/.*href=[\"']//" | sed "s/[\"'].*//"  | grep dossier-leg | sed 's|^|http://www.senat.fr|' | sort | uniq