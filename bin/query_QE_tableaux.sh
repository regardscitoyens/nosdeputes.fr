#!/bin/bash

. bin/db.inc 
echo "SELECT CONCAT('http://www.nosdeputes.fr/$LEGISLATURE/question/QE/', numero) FROM question_ecrite WHERE reponse LIKE '%<table%' ORDER BY numero ASC" | mysql $MYSQLID $DBNAME | grep -v numero

