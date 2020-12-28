APP = pm-app-api

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

%:
	@:

