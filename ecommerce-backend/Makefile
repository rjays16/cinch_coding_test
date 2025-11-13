.PHONY: help build up down restart logs shell composer artisan migrate fresh seed test

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker containers
	docker-compose build

up: ## Start all containers
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

restart: ## Restart all containers
	docker-compose restart

logs: ## Show container logs
	docker-compose logs -f

shell: ## Access PHP container shell
	docker-compose exec php bash

composer: ## Run composer install
	docker-compose exec php composer install

artisan: ## Run artisan command (use: make artisan CMD="migrate")
	docker-compose exec php php artisan $(CMD)

migrate: ## Run database migrations
	docker-compose exec php php artisan migrate

fresh: ## Fresh migration with seed
	docker-compose exec php php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec php php artisan db:seed

test: ## Run tests
	docker-compose exec php php artisan test

key: ## Generate application key
	docker-compose exec php php artisan key:generate

cache-clear: ## Clear all caches
	docker-compose exec php php artisan optimize:clear

install: build up composer key ## Complete installation
	@echo "Installation complete! Visit http://localhost:8000"

init: install migrate ## Initialize project with migrations
	@echo "Project initialized! Visit http://localhost:8000"