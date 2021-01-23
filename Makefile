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
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:diff

migrate:
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:migrate

behat:
	docker exec -it homecomb_php_1 php bin/console cache:clear --env=test
	make copy-test-env-to-local
	docker exec -it homecomb_php_1 vendor/bin/behat --format=progress
	make clear-env-local

e2e:
	make copy-e2e-env-to-local load-fixtures e2e-all clear-env-local load-fixtures

e2e-all:
	make e2e-public e2e-agency-admin

e2e-agency-admin:
	make e2e-solicit-review e2e-update-agency

e2e-public:
	make e2e-review-solicitation-response e2e-flag-review e2e-register e2e-tenancy-review e2e-search-for-property-and-review e2e-complete-survey

e2e-complete-survey:
	cd symfony && PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/CompleteSurvey.php

e2e-flag-review:
	cd symfony && PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/FlagReview.php

e2e-register:
	cd symfony && PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/Register.php

e2e-review-solicitation-response:
	cd symfony && PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/ReviewSolicitationResponse.php

e2e-search-for-property-and-review:
	cd symfony && PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/SearchForPropertyAndReview.php

e2e-solicit-review:
	cd symfony && PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/SolicitReview.php

e2e-tenancy-review:
	cd symfony && PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/TenancyReview.php

e2e-update-agency:
	cd symfony && PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/UpdateAgency.php

php-analyse:
	make php-cs-fixer phpstan

php-check:
	make php-cs-fixer php-test phpstan

php-cs-fixer:
	docker exec -it homecomb_php_1 vendor/bin/php-cs-fixer fix --verbose

php-test:
	make php-test-unit php-test-functional php-test-end

php-test-functional:
	make copy-test-env-to-local load-fixtures
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage tests/Functional
	make clear-env-local

php-test-unit:
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage tests/Unit

php-test-unit-coverage:
	docker exec -it homecomb_php_1 vendor/bin/phpunit --coverage-html var/tests/coverage tests/Unit

php-test-end:
	make clear-env-local load-fixtures

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

yarn-watch:
	docker exec -it homecomb_php_1 yarn encore dev --watch

clear-env-local:
	docker exec -it homecomb_php_1 bash -c "rm -f /var/www/symfony/.env.local"

copy-test-env-to-local:
	docker exec -it homecomb_php_1 bash -c "cp /var/www/symfony/.env.test /var/www/symfony/.env.local"

copy-e2e-env-to-local:
	docker exec -it homecomb_php_1 bash -c "cp /var/www/symfony/.env.e2e /var/www/symfony/.env.local"
