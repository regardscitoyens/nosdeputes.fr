#!/bin/bash

#Récupère le fichier de configuration pour notre environnement (qui se trouve dans le même que ce script)
. $(echo $0 | sed 's/[^\/]*$//')db.inc

cd $PATH_APP

cd cache
mv frontend frontend-old
nice -n 19 rm -rf frontend-old

