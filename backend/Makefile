ssh:
	docker exec -it stock-app-api-fpm sh

test:
	docker exec -it stock-app-api-fpm bin/phpunit

build-prod-image:
	docker build -t registry.heroku.com/ls-stock-api/web -f Dockerfile .

push-prod-image:
	docker push registry.heroku.com/ls-stock-api/web

release-prod-image:
	heroku container:release web --app ls-stock-api

deploy: build-prod-image push-prod-image release-prod-image
