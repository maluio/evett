dev: down up composer logs

prod: down prod-up reverse-proxy-up permissions

prod-up:
	docker-compose -f docker-compose.yml -f docker-compose-production.yml up -d --build
	docker-compose exec app composer install --no-dev --optimize-autoloader
	docker-compose exec app bin/console cache:clear

reverse-proxy-down:
	cd ../reverse-proxy/ && docker-compose down && cd -

reverse-proxy-up:
	cd ../reverse-proxy/ && docker-compose up -d && cd -

up: docker-up reverse-proxy-up permissions

down: reverse-proxy-down docker-down

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down

permissions:
	docker-compose exec app chown -R www-data var

logs:
	docker-compose logs -f

composer:
	docker-compose exec app composer install

db:
	docker-compose run app sqlite3 var/data.db

shell:
	docker-compose exec app sh

