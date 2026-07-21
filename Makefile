PHP = docker compose run --rm php

.PHONY: install test phpstan cs-fix cs-check audit shell build

## Build the Docker image
build:
	docker compose build

## Install Composer dependencies
install:
	$(PHP) composer install

## Run unit tests
test:
	$(PHP) vendor/bin/phpunit --testsuite Unit --colors=always

## Run PHPStan static analysis
phpstan:
	$(PHP) vendor/bin/phpstan analyse --no-progress --ansi --memory-limit=512M

## Auto-fix code style
cs-fix:
	$(PHP) vendor/bin/php-cs-fixer fix --ansi

## Check code style (dry-run)
cs-check:
	$(PHP) vendor/bin/php-cs-fixer fix --dry-run --diff --ansi

## Check for known security vulnerabilities in dependencies
audit:
	$(PHP) composer audit

## Run all checks (same as CI)
ci: audit phpstan cs-check test

## Open a shell in the PHP container
shell:
	docker compose run --rm php sh
