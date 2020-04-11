path=`pwd | sed 's/^.*\///'`

source config/db.inc

for i in 1 2 3 ; do
  wget http://2007-2012.nosdeputes.fr/confiseurs/$path/index.orig.php?sort=$i $webuser -O index$i.php
done
wget http://2007-2012.nosdeputes.fr/confiseurs/$path/index.orig.php $webuser -O index.php

