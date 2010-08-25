#!/bin/bash

echo "OBSOLETE"
exit


source ./bin/db.inc

rm lib/filter/doctrine/CommentaireParlementairesFormFilter.class.php
rm lib/filter/doctrine/base/BaseCommentaireParlementairesFormFilter.class.php
rm lib/form/doctrine/CommentaireParlementairesForm.class.php
rm lib/form/doctrine/base/BaseCommentaireParlementairesForm.class.php
rm lib/model/doctrine/base/BaseCommentaireParlementaires.class.php

php symfony cc
php symfony doctrine:build-model
php symfony doctrine:build-form
php symfony doctrine:build-filters
php symfony doctrine:build-sql

cat bin/updateDB.sql | mysql $MYSQLID $DBNAME

php symfony correct:Commentaires

php symfony cc

