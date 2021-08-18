prod-build:
	make pull clear-docker
	docker-compose -f docker-compose.yml build
	docker-compose -f docker-compose.yml up -d
	make composer-install create-directories npm-install-force yarn-build copy-prod-env-to-local

prod-up:
	export APP_ENV=prod && docker-compose -f docker-compose.yml up -d

prod-down:
	export APP_ENV=prod && docker-compose -f docker-compose.yml down --remove-orphans

prod-create-directories:
	docker exec -it homecomb_php_1 bash -c "mkdir -p /var/www/var/cache/prod/vich_uploader"
	docker exec -it homecomb_php_1 bash -c "chmod 777 /var/www/var/cache/prod -Rf"

prod-follow-logs:
	docker exec -it homecomb_php_1 bash -c "tail -f /var/www/var/log/prod.log"

build:
	make pull clear-docker
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml build
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
	make composer-install create-directories npm-install-force yarn-build
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml logs -f

create-directories:
	docker exec -it homecomb_php_1 bash -c "mkdir -p /var/www/var/cache/dev/vich_uploader"

clear-docker:
	docker-compose rm -vsf
	docker-compose -v --remove-orphans

composer-install:
	docker exec -it homecomb_php_1 composer install --no-interaction

npm-install-force:
	docker exec -it homecomb_php_1 bash -c "npm install --force"

pull:
	docker-compose pull

up:
	make pull
	export APP_ENV=dev && docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

down:
	docker-compose -f docker-compose.yml -f docker-compose.dev.yml down --remove-orphans

jump-in:
	docker exec -it homecomb_php_1 bash

db-container:
	docker exec -it homecomb_db_1 bash

generate-migration:
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:diff

migrate:
	docker exec -it homecomb_php_1 php bin/console doctrine:migrations:migrate

e2e:
	make e2e-setup e2e-all clear-env-local load-fixtures

e2e-setup:
	make copy-e2e-env-to-local load-fixtures

e2e-all:
	make e2e-public e2e-agency-admin

e2e-agency-admin:
	make e2e-solicit-review e2e-update-agency

e2e-public:
	make e2e-review-solicitation-response e2e-flag-review e2e-register e2e-tenancy-review e2e-search-for-property-and-review e2e-find-property-by-postcode e2e-complete-survey e2e-submit-contact-form e2e-submit-locale-review e2e-submit-vote

e2e-complete-survey:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/CompleteSurvey.php

e2e-find-property-by-postcode:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/FindPropertyByPostcode.php

e2e-flag-review:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/FlagReview.php

e2e-register:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/Register.php

e2e-review-solicitation-response:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/ReviewSolicitationResponse.php

e2e-search-for-property-and-review:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/SearchForPropertyAndReview.php

e2e-solicit-review:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/SolicitReview.php

e2e-submit-contact-form:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/SubmitContactForm.php

e2e-submit-locale-review:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/SubmitLocaleReview.php

e2e-submit-vote:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/SubmitVote.php

e2e-tenancy-review:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/TenancyReview.php

e2e-update-agency:
	PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --no-coverage tests/E2E/UpdateAgency.php

npm-test:
	docker exec -it homecomb_php_1 bash -c "npm test"

php-analyse:
	make php-cs-fixer phpstan

php-check:
	make php-cs-fixer php-test phpstan

php-cs-fixer:
	docker exec -it homecomb_php_1 vendor/bin/php-cs-fixer fix --verbose

php-test:
	make php-test-unit php-test-functional php-test-end

php-test-controller:
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage tests/Functional/Controller

php-test-repository:
	docker exec -it homecomb_php_1 vendor/bin/phpunit --no-coverage tests/Functional/Repository

php-test-functional:
	make copy-test-env-to-local load-fixtures php-test-controller php-test-repository clear-env-local

php-test-functional-coverage:
	make copy-test-env-to-local load-fixtures
	docker exec -it homecomb_php_1 vendor/bin/phpunit --coverage-html var/tests/coverage tests/Functional
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
	docker exec -it homecomb_php_1 bash -c "rm -f /var/www/.env.local"

copy-test-env-to-local:
	docker exec -it homecomb_php_1 bash -c "cp /var/www/.env.test /var/www/.env.local"

copy-e2e-env-to-local:
	docker exec -it homecomb_php_1 bash -c "cp /var/www/.env.e2e /var/www/.env.local"

copy-prod-env-to-local:
	docker exec -it homecomb_php_1 bash -c "cp /var/www/.env.prod /var/www/.env.local"
