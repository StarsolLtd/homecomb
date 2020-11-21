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
	docker exec -it homecomb_php_1 vendor/bin/behat --format=progress

phpunit:
	docker exec -it homecomb_php_1 vendor/bin/simple-phpunit --no-coverage tests/Unit/*

test:
	make behat phpunit

analyse:
	make php-cs-fixer phpstan

php-cs-fixer:
	docker exec -it homecomb_php_1 vendor/bin/php-cs-fixer fix --verbose

phpstan:
	docker exec -it homecomb_php_1 vendor/bin/phpstan analyse src --level max

follow-logs:
	docker-compose --file=docker-compose.yml logs -f
