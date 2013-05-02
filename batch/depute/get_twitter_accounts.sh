#!/bin/bash

mkdir -p twitter
rm -f twitter/liste_assnat*
cursor="-1"
touch twitter/liste_assnat.json
while [ "$cursor" != "0" ]; do
  url="https://api.twitter.com/1/lists/members.json?slug=les-d%C3%A9put%C3%A9s&owner_screen_name=AssembleeNat&skip_status=true&cursor=$cursor"
  echo "$url"
  curl -sL "$url" > twitter/liste_assnat_$cursor.json
  cat twitter/liste_assnat_$cursor.json | sed 's/},{"id/}\n{"id/g' | sed 's/{"users":\[//' | sed 's/],"next_cursor".*$/\n/' >> twitter/liste_assnat.json
  wc -l twitter/liste_assnat.json
  cursor=$(grep "next_cursor_str" twitter/liste_assnat_$cursor.json | sed 's/^.*"next_cursor_str":"\([0-9]*\)".*$/\1/')
  sleep 2
done

