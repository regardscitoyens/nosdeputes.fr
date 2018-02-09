#!/bin/bash

ROOTURL="http://www2.assemblee-nationale.fr"

function compute_org {
  ORGID=$1
  ORGNAME=$2
  curl -sL "$ROOTURL/layout/set/ajax/content/view/embed/$ORGID" |
    tr "\n" " "                  |
    sed 's/<h3>Réunion du /\n/g' |
    grep 'href="/'               |
    grep -v '/convocation/'      |
    sed 's/^.*href="//'          |
    sed 's/".*$//'               |
    while read url; do
      outf=$(echo $ROOTURL$url | sed 's|/|_|g')
      if ! test -s "html/$outf"; then
        perl download_one.pl $ROOTURL$url
	    perl parse_presents.pl html/$outf "$ORGNAME" > presents/$outf
      fi
    done
}
  
compute_org 47173 "Conférence des présidents"
compute_org 42864 "Bureau de l'Assemblée nationale"

