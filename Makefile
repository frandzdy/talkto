# Docker Compose
DOCKER_COMPOSE = docker-compose

# Docker containers
DOCK_PHP = $(DOCKER_COMPOSE) exec php

# Executables (Docker)
COMPOSER = $(DOCK_PHP) composer
SYMFONY  = $(DOCK_PHP) php bin/console

# Executables (Local)
YARN = yarn

# Config
.DEFAULT_GOAL = help
.PHONY        =

## —— Help 💡 ———————————————————————————————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
docker-build: ## Pull and build images
	@$(DOCKER_COMPOSE) build --pull

docker-up: ## Start docker in detached mode (no logs)
	@$(DOCKER_COMPOSE) up --detach

docker-start: docker-build docker-up ## Build and start the containers

docker-stop: ## Stop docker containers
	@$(DOCKER_COMPOSE) stop

docker-down: ## Remove docker containers
	@$(DOCKER_COMPOSE) down --remove-orphans

docker-logs: ## Show live logs
	@$(DOCKER_COMPOSE) logs --tail=0 --follow

## —— Development 🧙 ———————————————————————————————————————————————————————————
install: vendor database migration fixtures asset ## Install a fresh application (database will be reset)

build: vendor migration asset ## Update dependencies, database and asset

vendor: ## Install dependencies with composer (no options)
	@$(COMPOSER) install

database: ## Create a new database
	@$(SYMFONY) doctrine:database:drop --if-exists --force
	@$(SYMFONY) doctrine:database:create

migration: ## Run database migrations
	@$(SYMFONY) doctrine:migrations:migrate --allow-no-migration --no-interaction

fixtures: ## Load database fixtures
	@$(SYMFONY) doctrine:fixtures:load --no-interaction

asset: ## Build assets (css, js, fonts, images)
	@cd app && \
	$(YARN) install && \
	$(YARN) dev

bash: ## Launch bash in PHP container
	@$(DOCK_PHP) bash

checkstyle: ## Check Coding Standard with PHPCodeSniffer
	@$(DOCK_PHP) phpcs --ignore=src/Enum --standard=PSR12 --exclude=Generic.Files.LineLength src/

composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

symfony: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make symfony c='about'
	@$(eval c ?=)
	@$(SYMFONY) $(c)

## —— Application 🚀 ———————————————————————————————————————————————————————————
#domain-command: ## Description de la commande
#	@$(SYMFONY) app:domain:command
