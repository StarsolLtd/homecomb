build:
	docker-compose rm -vsf
	docker-compose -v --remove-orphans
	docker-compose --file=docker-compose.yml build
	docker-compose --file=docker-compose.yml up -d
	docker exec -it homecomb_php_1 composer install --no-interaction
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
	docker exec -it homecomb_php_1 php bin/console make:migration

migrate:
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:migrate

behat:
	docker exec -it homecomb_php_1 php bin/console cache:clear --env=test
	docker exec -it homecomb_php_1 bash -c "echo 'APP_ENV=test' >> /var/www/symfony/.env.local"
	docker exec -it homecomb_php_1 vendor/bin/behat --format=progress
	docker exec -it homecomb_php_1 bash -c "rm -f /var/www/symfony/.env.local"

phpunit:
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage

test-functional:
	make load-fixtures
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage tests/Controller

test-unit:
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage tests/Unit

test:
	make test-functional test-unit

analyse:
	make php-cs-fixer phpstan

php-cs-fixer:
	docker exec -it homecomb_php_1 vendor/bin/php-cs-fixer fix --verbose

phpstan:
	docker exec -it homecomb_php_1 vendor/bin/phpstan analyse -c phpstan.neon src --level max

follow-logs:
	docker-compose --file=docker-compose.yml logs -f

reset-database:
	make empty-database migrate load-fixtures

empty-database:
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:migrate first

load-fixtures:
	docker exec -it homecomb_php_1 php bin/console doctrine:fixtures:load

dump:
	docker exec -it homecomb_php_1 php bin/console server:dump
