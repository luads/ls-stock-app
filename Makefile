build-ui:
	cd frontend; npm install && npm run build

rebuild-run: build-ui
	docker-compose up --build --force-recreate -d

run:
	docker-compose up -d

stop:
	docker-compose down

open:
	open http://localhost:5000
