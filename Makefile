dev: down up logs

prod: down prod-up permissions

prod-up:
	docker-compose -f docker-compose.yml -f docker-compose-production.yml up -d --build
	docker-compose exec -T app composer install --no-dev --optimize-autoloader
	docker-compose exec -T app bin/console cache:clear
	docker-compose exec -T app bin/console doctrine:schema:update --force

up: docker-up permissions

down: docker-down

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down

permissions:
	docker-compose exec -T app chown -R www-data var

logs:
	docker-compose logs -f

composer:
	docker-compose exec -T app composer install

db:
	docker-compose run app sqlite3 var/data.db

shell:
	docker-compose exec app sh

