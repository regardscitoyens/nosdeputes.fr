#!/bin/bash
MYSQLID="-u cpc -pM_O_T__D_E__P_A_S_S_E"
DBNAME="cpc"
cat liste_sort_indefini.sql | mysql $MYSQLID $DBNAME | grep -v source > liste_sort_indefini.txt
perl download_amendements.pl

