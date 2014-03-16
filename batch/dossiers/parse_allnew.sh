#!/bin/bash
mkdir -p out
bash list_dossiers_promulgues.sh | 
bash download_dossiers.sh | while read file; do
    output=$(echo $file | sed 's/html/out/')
    cat $file | perl parse_dossier.pl | uniq > $output
done