#!/bin/bash

source bin/db.inc

php symfony cc
#php symfony doctrine:build --all-classes
#cat bin/updateDB3.model.sql | mysql $MYSQLID $DBNAME
php symfony doctrine:build --all --no-confirmation

echo "ALTER TABLE parlementaire ADD photo LONGBLOB NULL AFTER profession" | mysql $MYSQLID $DBNAME

zcat data/sql/dumps/nosdeputes_prod.100925.sql.gz | mysql --default-character-set=utf8 $MYSQLID $DBNAME

echo "ALTER TABLE parlementaire DROP photo" | mysql $MYSQLID $DBNAME

cat bin/updateDB3.1.sql | mysql $MYSQLID $DBNAME

bash bin/load_deputes

cd batch/hemicycle
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2007-2008_20080120.asp > out/out1
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2008-2009_20090021.asp > out/out2
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2007-2008_20080086.asp > out/out3
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2008-2009-extra2_20092014.asp > out/out4
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2009-2010_20100162.asp > out/out5
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2007-2008_20080193.asp > out/out6
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2008-2009_20090221.asp > out/out7
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2009-2010-extra_20101013.asp > out/out8
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2006-2007-extra2_20072008.asp > out/out9
perl parse_hemicycle.pl html/http\:__www.assemblee-nationale.fr_13_cri_2008-2009_20090242.asp > out/out10
cd -

while ls batch/hemicycle/out | grep [a-z] > /dev/null ; do 
	php symfony cc --env=test  --app=frontend > /dev/null
	php symfony load:Hemicycle 
done;

php symfony move:Seance 304 13 31
php symfony move:Seance 278 1043 31
php symfony move:Seance 84 1043 31
php symfony move:Seance 2756 1043 7573
php symfony move:Seance 2769 1043 7573
php symfony move:Seance 59 785 254
php symfony move:Seance 311 785 181
php symfony move:Seance 59 784 254
php symfony move:Seance 311 784 181
php symfony move:Seance 311 7004 181
php symfony move:Seance 521 5039 357
php symfony move:Seance 3194 8634 2
php symfony move:Seance 108 1356 1348
php symfony move:Seance 281 3362 2765
php symfony move:Seance 48 621 1380
php symfony move:Seance 348 4185 2
php symfony move:Seance 2903 7751 7573
php symfony move:Seance 3639 10334 2
php symfony move:Seance 3473 1213 2
php symfony move:Seance 3264 8874 2

cat bin/updateDB3.2.sql | mysql $MYSQLID $DBNAME

php symfony fuse:Dossiers 5091 9804
php symfony fuse:Dossiers 3511 952
php symfony fuse:Dossiers 2867 952
php symfony fuse:Dossiers 547 357
php symfony fuse:Dossiers 6576 1993
php symfony fuse:Dossiers 46 1993
php symfony fuse:Dossiers 8897 8900
php symfony fuse:Dossiers 7558 7573
php symfony fuse:Dossiers 7591 7573
php symfony fuse:Dossiers 927 1926
php symfony fuse:Dossiers 733 4450
php symfony fuse:Dossiers 4825 4450
php symfony fuse:Dossiers 5911 4450
php symfony fuse:Dossiers 1421 31
php symfony fuse:Dossiers 1011 4321
php symfony fuse:Dossiers 2783 195
php symfony fuse:Dossiers 1443 2363
php symfony fuse:Dossiers 6879 5549
php symfony fuse:Dossiers 8837 1384
php symfony fuse:Dossiers 7551 7521
php symfony fuse:Dossiers 8740 8663
php symfony fuse:Dossiers 9424 9427
php symfony fuse:Dossiers 10869 10790
php symfony fuse:Dossiers 9833 9718
php symfony fuse:Dossiers 9747 9837
php symfony fuse:Dossiers 7005 7038
php symfony fuse:Dossiers 3201 4773
php symfony fuse:Dossiers 5034 5470
php symfony fuse:Dossiers 9842 9804
php symfony fuse:Dossiers 3790 181
php symfony fuse:Dossiers 5828 2675
php symfony fuse:Dossiers 515 2811
php symfony fuse:Dossiers 813 2814
php symfony fuse:Dossiers 3759 5050
php symfony fuse:Dossiers 3904 964
php symfony fuse:Dossiers 10999 9328
php symfony fuse:Dossiers 10772 9485
php symfony fuse:Dossiers 10331 10354
php symfony fuse:Dossiers 7983 8003
php symfony fuse:Dossiers 8611 4974
php symfony fuse:Dossiers 914 2370
php symfony fuse:Dossiers 265 16
php symfony fuse:Dossiers 2348 6376
php symfony fuse:Dossiers 9485 10874
php symfony fuse:Dossiers 3955 3953

cat bin/updateDB3.3.sql | mysql $MYSQLID $DBNAME

echo > /tmp/res_maxdate
while grep -v "^0 sections trouvées" /tmp/res_maxdate > /dev/null ; do 
	php symfony cc --env=test  --app=frontend > /dev/null
	php symfony set:MaxDateDossiers > /tmp/res_maxdate
done;
rm -f /tmp/res_maxdate

cd batch/documents
  mkdir ppr
  mkdir pjl
  mkdir ppl
  mkdir rap
  mkdir ta
  mkdir out
  perl download_docs.pl
  for dir in rap ppl pjl ppr ta; do
    for file in `ls $dir`; do
      perl parse_metas.pl $dir/$file > out/$file
    done
  done
cd -


while ls batch/documents/out | grep [a-z] > /dev/null ; do 
	php symfony cc --env=test  --app=frontend > /dev/null
	php symfony load:Documents
done;

php symfony top:Deputes
bash bin/update_tops.sh

#bash bin/update_hardcache_all



