dev development: # Install development dependencies
	@composer install --no-interaction

prod production: # Install production dependencies
	@composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

update upgrade: # Update dependencies
	@composer update

test: # Run coding standards/static analysis checks and tests
	@vendor/bin/php-cs-fixer fix --diff --dry-run \
		&& vendor/bin/phpstan analyze \
		&& vendor/bin/phpunit --coverage-text

coverage: # Generate an HTML coverage report
	@vendor/bin/phpunit --coverage-html .coverage
