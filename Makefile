rebuild-run:
	docker-compose up --build --force-recreate -d

run:
	docker-compose up -d

stop:
	docker-compose down

api-ssh:
	docker exec -it stock-app-api-fpm sh

build-prod-api:
	docker build -t registry.heroku.com/ls-stock-api/web -f backend/Dockerfile backend

push-prod-api:
	docker push registry.heroku.com/ls-stock-api/web

release-prod-api:
	heroku container:release web --app ls-stock-api

deploy-api: build-prod-api push-prod-api release-prod-api
