.PHONY dev:
dev:
	docker-compose up --build

.PHONY prod:
prod:
	docker-compose -f docker-compose.yml -f docker-compose-production.yml up --build

.PHONY cronjobs:
cronjobs:
	cp docker/app/cronjobs/evett-import docker/app/cronjobs/active/hourly/