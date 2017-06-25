all: lib/vendor/autoload.php cache log apps/frontend/config/routing.yml apps/frontend/config/factories.yml lib/model/doctrine/base/BaseParlementaire.class.php bin/db.inc web/images/tmp/xspchart

lib/vendor/autoload.php: composer.json composer.lock
	composer install

cache:
	mkdir cache ; sudo chown www-data cache  ; sudo chmod g+ws cache

log:
	mkdir log; sudo chown www-data log ; sudo chmod g+ws log

apps/frontend/config/routing.yml: bin/generate_routing.sh apps/frontend/config/routing.yml.example bin/db.inc
	bash bin/generate_routing.sh

apps/frontend/config/factories.yml: apps/frontend/config/factories.yml.example
	cp apps/frontend/config/factories.yml.example apps/frontend/config/factories.yml

lib/model/doctrine/base/BaseParlementaire.class.php:
	php symfony doctrine:build --model

bin/db.inc: config/databases.yml bin/db.inc.example config/app.yml bin/generate_dbinc.sh
	bash bin/generate_dbinc.sh

web/images/tmp/xspchart:
	mkdir -p web/images/tmp/xspchart ; sudo chown www-data web/images/tmp/xspchart ; sudo chmod g+ws web/images/tmp/xspchart
