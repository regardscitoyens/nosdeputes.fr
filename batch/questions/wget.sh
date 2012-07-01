#!/bin/bash
echo "ce script est obsolète il permet de télécharger toutes les questions de la 13ème législature\n";
exit;

ct=1
while [ $ct -le 132810 ] ; do
  wget http://questions.assemblee-nationale.fr/q13/13-${ct}QE.htm -O wget/${ct}.htm
  ct=$(( ct + 1 ))
done;

