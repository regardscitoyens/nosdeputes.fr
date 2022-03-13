#!/bin/bash

JSON=$1

if grep '"source": "https\?://videos.assemblee-nationale.fr/' $JSON > /dev/null; then
  INTERVS=$(grep -v '"intervenant": ""' $JSON       |
              grep -v '"source": "https\?://videos.'|
              sed 's/^.*"intervenant": "//'         |
              sed 's/",.*"fonction": "/\t\t|  /'    |
              sed 's/".*$//'                        |
              sort | uniq -c                        |
              grep -iv "pr[eé]sident"               |
              wc -l)
  VIDEOSOURCES=$(cat $JSON               |
    sed 's/^.*"source": "/SOURCE:     /' |
    sed 's/[#"].*$/\n/'                  |
    sed 's/\?timecode=.*$//'             |
    grep .                               |
    uniq                                 |
    grep 'https\?://videos.'             |
    wc -l)
  if [ "$INTERVS" -gt 0 ] || [ "$VIDEOSOURCES" -gt 1 ]; then
    OUT=$(echo $JSON | sed 's|out/|checkvideos/|')
    echo "WARNING: Reserving CR with both video and interventions for further checks in $OUT"
    mkdir -p checkvideos
    mv $JSON checkvideos/
  fi
fi
