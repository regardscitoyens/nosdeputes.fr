#!/bin/bash

lists="AssembleeNat/les-d%C3%A9put%C3%A9s ADAN_Officiel/d%C3%A9put%C3%A9s samuellaurent/deputes leLab_E1/d%C3%A9put%C3%A9s ebertholomey/assembl%C3%A9e-nationale"
#possible extra mais pas seulement : VuDuPerchoir/directan Bekouz/politiques Authueil/parlement

mkdir -p twitter
rm -f twitter/liste_assnat*
touch twitter/liste_assnat.json
touch twitter/liste_assnat_tmp.json
for list in $lists; do
  id=`echo $list | sed 's#^\(.*\)/\(.*\)$#\2\&owner_screen_name=\1#'`
  name=`echo $list | sed 's/[^a-z0-9]//ig'`
  cursor="-1"
  while [ "$cursor" != "0" ]; do
    url="https://api.twitter.com/1/lists/members.json?slug=$id&skip_status=true&cursor=$cursor"
    echo "$url"
    curl -sL "$url" > twitter/liste_assnat_$name_$cursor.json
    cat twitter/liste_assnat_$name_$cursor.json | sed 's/},{"id/}\n{"id/g' | sed 's/{"users":\[//' | sed 's/],"next_cursor".*$/\n/' > twitter/liste_assnat_tmp.json
    cat twitter/liste_assnat.json twitter/liste_assnat_tmp.json | sort -u > twitter/liste_assnat_tmp2.json
    mv twitter/liste_assnat_tmp2.json twitter/liste_assnat.json
    wc -l twitter/liste_assnat.json
    cursor=$(grep "next_cursor_str" twitter/liste_assnat_$name_$cursor.json | sed 's/^.*"next_cursor_str":"\([0-9]*\)".*$/\1/')
    sleep 2
  done
done

