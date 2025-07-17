# Makefile

# Define variables
DOCKER_COMPOSE = docker-compose
PHP            = $(DOCKER_COMPOSE) exec -T php
CONSOLE        = $(PHP) bin/console

# Default target: start everything
run: up migrate messenger scheduler

# Start Docker containers (detached)
up:
	$(DOCKER_COMPOSE) up -d

# Stop Docker containers
down:
	$(DOCKER_COMPOSE) down

# Run Symfony Messenger worker
messenger:
	$(CONSOLE) messenger:consume async --time-limit=3600 --memory-limit=128M --no-interaction

# Dispatch messages regularly using custom command (replace app:dispatch with your real command)
scheduler:
	while true; do \
		$(CONSOLE) app:sync-reviews; \
		sleep 60; \
	done

# Shortcut to run migrations
migrate:
	$(CONSOLE) doctrine:migrations:migrate --no-interaction

# Clear cache
cache-clear:
	$(CONSOLE) cache:clear

# Build docker images
build:
	$(DOCKER_COMPOSE) build
