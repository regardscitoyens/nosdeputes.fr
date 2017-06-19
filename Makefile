all: lib/vendor/autoload.php cache log

lib/vendor/autoload.php: composer.json composer.lock
	composer install

cache:
	mkdir cache ; sudo chown www-data cache  ; sudo chmod g+ws cache

log:
	mkdir log; sudo chown www-data log ; sudo chmod g+ws cache
