APP = abr-app-api
ARGS = $(filter-out $@,$(MAKECMDGOALS))

.PHONY: fill-consul
fill-consul:
	docker exec $(APP) rm -f /app/.env
	docker exec $(APP) php /app/docker/build/consul.php i

.PHONY: install
install:
	docker exec $(APP) composer install

.PHONY: migrate
migrate: check ./docker-compose.env
	docker exec $(APP) php artisan migrate

.PHONY: bash
bash:
	docker exec -it $(APP) bash

.PHONY: consul
consul: check ./docker-compose.env
	docker exec $(APP) rm -f .env
	docker exec $(APP) /app/docker/build/consul.sh

.PHONY: route-cache
route-cache: check ./docker-compose.env
	docker exec $(APP) php /app/artisan route:cache

.PHONY: composer
composer: check ./docker-compose.env
	make sync-local-to-container
	docker exec $(APP) composer ${ARGS}
	make sync-local-to-container

.PHONY: composer-install-prod
composer-install-prod: check ./docker-compose.env
	make sync-local-to-container
	docker exec $(APP) composer install

.PHONY: sync-container-to-local
sync-container-to-local: check ./docker-compose.env
	sudo docker cp  $(APP):/app/vendor/ ./vendor
	sudo docker cp  $(APP):/app/composer.json ./composer.json
	sudo docker cp  $(APP):/app/composer.lock ./composer.lock
	sudo docker cp  $(APP):/app/.env ./.env

.PHONY: sync-local-to-container
sync-local-to-container: check ./docker-compose.env
	sudo docker cp ./composer.json $(APP):/app/composer.json
	sudo docker cp ./composer.lock $(APP):/app/composer.lock
	sudo docker cp ./modules_statuses.json $(APP):/app/modules_statuses.json

.PHONY: test-run
test-run:
	docker exec $(APP) php artisan abrouter:create-database abr_test
	docker exec $(APP) php build/switch.php  --mode=test
	docker exec $(APP) php artisan migrate
	docker exec $(APP) php artisan passport:install
	docker exec $(APP) php ./vendor/bin/codecept build
	docker exec $(APP) php ./vendor/bin/codecept run ${ARGS}
	docker exec $(APP) php ./vendor/bin/codecept clean
	docker exec $(APP) php artisan abrouter:drop-database abr_test
	docker exec $(APP) php build/switch.php  --mode=dev

%:
	@:

