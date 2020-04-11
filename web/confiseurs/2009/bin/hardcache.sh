path=`pwd | sed 's/^.*\///'`

source config/db.inc

wget http://2007-2012.nosdeputes.fr/confiseurs/$path/index2.php $webuser -O index.php

