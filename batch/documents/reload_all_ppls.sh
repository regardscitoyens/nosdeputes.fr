#!/bin/bash

curl -sL "http://www2.assemblee-nationale.fr/documents/liste/(ajax)/1/(limit)/10000/(type)/propositions-loi" | sed 's|14/ta/ta0080/|14/propositions/pion0733/|' | grep "documents/notice/14/pro" | sed 's/^.*href="//' | sed 's/"><i.*$//' | sed 's|/documents/notice/14/propositions/pion\([0-9]\+\)/(index)/propositions-loi|http://www.assemblee-nationale.fr/14/propositions/pion\1.asp|' > all-ppls

cat all-ppls | while read url; do
  file=`echo $url | sed 's/\//_/g'`
  perl download_one.pl $url
  perl parse_metas.pl html/$file > out/$file
  mv html/$file ppl/$file
done

curl -sL "http://www.assemblee-nationale.fr/14/documents/index-resolutions.asp" > /tmp/all-pprs
curl -sL "http://www.assemblee-nationale.fr/14/documents/index-enquete-resolution.asp" >> /tmp/all-pprs
curl -sL "http://www.assemblee-nationale.fr/14/documents/index-resol-reglement.asp" >> /tmp/all-pprs
curl -sL "http://www.assemblee-nationale.fr/14/documents/index-resol-art34-1.asp" >> /tmp/all-pprs

iconv -f iso-8859-15 -t utf-8 /tmp/all-pprs | grep 'href="/14/\(propositions\|europe\)' | sed 's|^.*href="|http://www.assemblee-nationale.fr|' | sed 's/">[<n].*$//' > all-pprs

cat all-pprs | while read url; do
  file=`echo $url | sed 's/\//_/g'`
  perl download_one.pl $url
  perl parse_metas.pl html/$file > out/$file
  mv html/$file ppr/$file
done

