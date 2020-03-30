rebuild-run:
	docker-compose up --build --force-recreate -d

run:
	docker-compose up -d

stop:
	docker-compose down
