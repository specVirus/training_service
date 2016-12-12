override NAME = 'default'

all:
	@echo 'you must enter target'

deploy-prod: git-pull-prod prod install-vendor yii-migrate-deploy

deploy-dev: git-pull-dev dev install-vendor yii-migrate-deploy

prod:
	./src/init --env=Production --overwrite=y

dev:
	./src/init --env=Development --overwrite=y

git-pull-dev:
	git reset --hard && git pull origin develop:develop && git checkout develop

git-pull-prod:
	git reset --hard && git pull origin master:master && git checkout master

install-vendor:
	composer install -d src

update-vendor:
	composer update -d src

yii-migrate-down:
	php ./src/yii migrate/down --migrationPath=./src/console/migrations --interactive=0

yii-migrate-deploy:
	php ./src/yii migrate --migrationPath=./src/console/migrations --interactive=0

yii-migrate-create:
	php ./src/yii migrate/create $(NAME) --interactive=0