build:
	make pull
	docker-compose rm -vsf
	docker-compose -v --remove-orphans
	docker-compose --file=docker-compose.yml build
	docker-compose --file=docker-compose.yml up -d
	docker exec -it homecomb_php_1 composer install --no-interaction
	docker exec -it homecomb_php_1 bash -c "mkdir /var/www/symfony/var/cache/dev/vich_uploader"
	docker exec -it homecomb_php_1 bash -c "npm install --force"
	make yarn-build
	docker-compose --file=docker-compose.yml logs -f

pull:
	docker-compose pull

up:
	make pull
	export APP_ENV=dev && docker-compose --file=docker-compose.yml up -d

down:
	docker-compose --file=docker-compose.yml down --remove-orphans

jump-in:
	docker exec -it homecomb_php_1 bash

generate-migration:
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:generate

migrate:
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:migrate

behat:
	docker exec -it homecomb_php_1 php bin/console cache:clear --env=test
	docker exec -it homecomb_php_1 bash -c "echo 'APP_ENV=test' >> /var/www/symfony/.env.local"
	docker exec -it homecomb_php_1 vendor/bin/behat --format=progress
	docker exec -it homecomb_php_1 bash -c "rm -f /var/www/symfony/.env.local"

php-analyse:
	make php-cs-fixer phpstan

php-check:
	make php-cs-fixer php-test phpstan

php-cs-fixer:
	docker exec -it homecomb_php_1 vendor/bin/php-cs-fixer fix --verbose

php-test:
	make php-test-unit php-test-functional

php-test-functional:
	docker exec -it homecomb_php_1 bash -c "echo 'APP_ENV=test' >> /var/www/symfony/.env.local"
	make load-fixtures
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage tests/Functional
	docker exec -it homecomb_php_1 bash -c "cat /dev/null > /var/www/symfony/.env.local"

php-test-unit:
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage tests/Unit

phpstan:
	docker exec -it homecomb_php_1 vendor/bin/phpstan analyse -c phpstan.neon src --level max

phpunit:
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage

follow-logs:
	docker-compose --file=docker-compose.yml logs -f

reset-database:
	make empty-database migrate load-fixtures

empty-database:
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:migrate first

load-fixtures:
	docker exec -it homecomb_php_1 php bin/console doctrine:fixtures:load -n

dump:
	docker exec -it homecomb_php_1 php bin/console server:dump

yarn-build:
	docker exec -it homecomb_php_1 yarn encore dev