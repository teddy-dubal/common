ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent

list:
	sh -c "echo; $(MAKE) -p no_targets__ | awk -F':' '/^[a-zA-Z0-9][^\$$#\/\\t=]*:([^=]|$$)/ {split(\$$1,A,/ /);for(i in A)print A[i]}' | grep -v '__\$$' | grep -v 'Makefile'| sort"

#############################
# Create new project
#############################

create:
	bash bin/create-project.sh $(ARGS)

#############################
# Docker machine states
#############################

up-dev:
	docker-compose -f docker-compose.development.yml up -d --build

up-prod:
	docker-compose -f docker-compose.production.yml up -d --build

build-prod:
	docker build -t docker.aladomheure.com/lae/web . -f Dockerfile.production

start:
	docker-compose start

stop:
	docker-compose stop

state:
	docker-compose ps

rebuild:
	docker-compose stop
	docker-compose rm --force $(ARGS)
	docker-compose build --no-cache
	docker-compose up -d

#############################
# MySQL
#############################

mysql-backup:
	bash ./bin/backup.sh mysql

mysql-restore:
	bash ./bin/restore.sh mysql

#############################
# Solr
#############################

solr-backup:
	bash ./bin/backup.sh solr

solr-restore:
	bash ./bin/restore.sh solr

#############################
# General
#############################

backup:  mysql-backup  solr-backup
restore: mysql-restore solr-restore

build:
	bash bin/build.sh

bash: shell

shell:
	docker exec -it -u application $$(docker-compose ps -q $(ARGS)) /bin/bash

root:
	docker exec -it -u root $$(docker-compose ps -q $(ARGS)) /bin/bash

#############################
# Argument fix workaround
#############################
%:
	@:
