build:
	docker-compose build --no-cache --force-rm
stop:
	docker-compose stop
up:
	docker-compose up -d
serv:
	docker exec -it laravel-driver-api bash