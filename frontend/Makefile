build-prod-image:
	docker build -t registry.heroku.com/ls-stock-ui/web -f Dockerfile .

push-prod-image:
	docker push registry.heroku.com/ls-stock-ui/web

release-prod-image:
	heroku container:release web --app ls-stock-ui

deploy: build-prod-image push-prod-image release-prod-image
