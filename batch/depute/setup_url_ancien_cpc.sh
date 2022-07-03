#!/bin/bash

source ../../bin/db.inc
curl https://$VHOST/deputes/csv | xsv select -d ";" slug,anciens_mandats | sed -r 's#\|?[0-9/]+ /  / \|?##' | xsv search -s anciens_mandats . | xsv select slug | xsv behead | sort > slug_deputes_reelus.csv

URL_ANCIEN_CPC=https://$(grep 'host_previous_legislature' ../../config/app.yml | sed 's/^.*:\s*//')

curl $URL_ANCIEN_CPC/deputes/csv | xsv select -d ";" slug | xsv behead | sort > slug_anciens_deputes.csv

echo "Cases to check :"
diff slug_deputes_reelus.csv slug_anciens_deputes.csv | grep "<" | sed -r "s#< #$URL_ANCIEN_CPC/#" | while read url; do echo "$url"; curl -sLI $url | grep "HTTP\|Location"; echo; done | grep -B 1 "^http\| 200 \| 40" | grep -v "^--"
diff slug_deputes_reelus.csv slug_anciens_deputes.csv | grep "<" | sed -r "s#< ##" | while read slug; do echo "UPDATE parlementaire SET url_nouveau_cpc = 'https://$VHOST/$slug' WHERE slug = '$slug';"; done > set_url_nouveau_cpc_old_legislatures.sql

cat slug_deputes_reelus.csv | while read slug; do echo "UPDATE parlementaire SET url_ancien_cpc = '$URL_ANCIEN_CPC/$slug' WHERE slug = '$slug';"; done | sed 's/\/christine-le-nabour/\/christine-cloarec/' > set_url_ancien_cpc.sql

xsv join "" slug_deputes_reelus.csv "" slug_anciens_deputes.csv | xsv select 1 | while read slug; do echo "UPDATE parlementaire SET url_nouveau_cpc = 'https://$VHOST/$slug' WHERE slug = '$slug';"; done > set_url_nouveau_cpc.sql
echo "UPDATE parlementaire SET url_nouveau_cpc = 'https://$VHOST/christine-le-nabour' WHERE slug = 'christine-cloarec';" >> set_url_nouveau_cpc.sql

rm -f slug_deputes_reelus.csv slug_anciens_deputes.csv
