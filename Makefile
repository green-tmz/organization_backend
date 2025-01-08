setup-local:
	./bin/setup-local.sh

lint:
	docker compose exec app ./vendor/bin/phpcs --standard=PSR12 app tests

lint-fix:
	docker compose exec app ./vendor/bin/phpcbf --standard=PSR12 app tests

test:
	docker compose exec app ./vendor/bin/phpunit

prepare-commit:
	docker compose exec app  sh -c "./vendor/bin/phpcbf --standard=PSR12 app tests && ./vendor/bin/phpcs --standard=PSR12 app tests && php artisan test --parallel --processes=12"

