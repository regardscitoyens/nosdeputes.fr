#!/bin/bash

source ../../bin/init_pyenv38.sh
url=$1
output=$2

if ! test "$output"; then
    output="out/"$(echo $url | base64 -w 0 )".json"
fi

docid=$(echo $url | sed 's|.*/||' | sed 's/.asp//' | awk -F '-' '{print $1}' | sed 's/[^0-9]//g')

find opendata/document/ -name '*'$docid'*' | while read file ; do
    python3 parse_opendata_documents.py $file > "/tmp/documents_$$.json"
    uri=$(echo $url | sed 's|https://www.assemblee-nationale.fr/[^0-9]*/||')
    if grep "$uri" "/tmp/documents_$$.json"  > "$output" ; then
        break;
    fi
done
rm -f /tmp/documents_$$.json

if ! test -s "$output"; then
    if ! [ -z "$2" ]; then
      echo "$0: $url not found" 1>&2 ;
    fi
    rm -f "$output"
elif ! jq . < "$output" | grep '"contenu": "[^"]' > /dev/null; then
    echo "$0: erreur de contenu avec $url" 1>&2 ;
fi
