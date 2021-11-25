APP = abr-app-api
ARGS = $(filter-out $@,$(MAKECMDGOALS))

.PHONY: provision
provision:
	docker exec $(APP) rm -f /app/.env
	docker exec $(APP) php /app/docker/provision/consul.php i


.PHONY: install
install:
	docker exec $(APP) composer install --ignore-platform-reqs

.PHONY: consul-check
consul-check: check ./docker-compose.env
	docker exec $(APP) /app/docker/provision/consul.sh --check

.PHONY: migrate
migrate: check ./docker-compose.env
	docker exec $(APP) php artisan migrate


.PHONY: consul
consul: check ./docker-compose.env
	docker exec $(APP) rm -f .env
	docker exec $(APP) /app/docker/provision/consul.sh

.PHONY: route-cache
route-cache: check ./docker-compose.env
	docker exec $(APP) php /app/artisan route:cache

.PHONY: test-run
test-run:
	docker exec $(APP) php build/switch.php  --mode=test
	docker exec $(APP)  php artisan migrate --path=/database/migrations
	docker exec $(APP)  php artisan passport:install
	docker exec $(APP)  php ./vendor/bin/codecept build
	docker exec $(APP)  php ./vendor/bin/codecept run ${ARGS}
	docker exec $(APP)  php ./vendor/bin/codecept clean
	docker exec $(APP)  php artisan module:migrate-rollback
	docker exec $(APP)  php artisan migrate:rollback
	docker exec $(APP) php build/switch.php  --mode=dev

%:
	@:

