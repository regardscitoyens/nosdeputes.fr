#!/bin/bash

php symfony cc
php symfony doctrine:build --all --no-confirmation
bash bin/load_senateurs

cd batch/questions
bash parse_all.sh 1
cp -r json json.test
bash parse_all.sh
cd -

while ls batch/questions/json | grep [a-z] > /dev/null ; do
	php symfony cc --env=test  --app=frontend > /dev/null
	php symfony load:Questions 
done;

php symfony top:Senateurs


