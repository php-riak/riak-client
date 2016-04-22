# vim: ts=4:sw=4:noexpandtab!:

BASEDIR    := $(shell pwd)
COMPOSER   := $(shell which composer)
RIAK_ADMIN ?= $(shell which riak-admin)

help:
	@echo "---------------------------------------------"
	@echo "List of available targets:"
	@echo "  composer-install         - Installs composer dependencies."
	@echo "  create-data-types        - Create riak datatypes."
	@echo "  phpcs                    - Runs PHP Code Sniffer."
	@echo "  phpunit                  - Runs tests."
	@echo "  phpunit-coverage-clover  - Runs tests to genereate coverage clover."
	@echo "  phpunit-coverage-html    - Runs tests to genereate coverage html."
	@echo "  help                     - Shows this dialog."
	@exit 0

all: install phpunit

install: create-data-types composer-install

test: phpcs phpunit

composer-install:
ifdef COMPOSER
	$(COMPOSER) install --prefer-source --no-interaction;
else
	@echo "Composer not found !!"
	@echo
	@echo "curl -sS https://getcomposer.org/installer | php"
	@echo "mv composer.phar /usr/local/bin/composer"
endif

create-data-types:
ifeq ($(RIAK_ADMIN),)
	@echo "riak-admin not found !!"
	@echo
	@echo "Please add riak-admin to your path or set the environment RIAK_ADMIN=/path-to-riak/bin/riak-admin"
else
	$(RIAK_ADMIN) bucket-type create counters '{"props":{"datatype":"counter"}}' || true
	$(RIAK_ADMIN) bucket-type create maps '{"props":{"datatype":"map"}}' || true
	$(RIAK_ADMIN) bucket-type create sets '{"props":{"datatype":"set"}}' || true
	$(RIAK_ADMIN) bucket-type create thunder_cats || true
	$(RIAK_ADMIN) bucket-type activate thunder_cats || true
	$(RIAK_ADMIN) bucket-type activate counters || true
	$(RIAK_ADMIN) bucket-type activate maps || true
	$(RIAK_ADMIN) bucket-type activate sets || true
endif

phpunit-functional:
	php $(BASEDIR)/vendor/bin/phpunit --group functional -v;

phpunit-unit:
	php $(BASEDIR)/vendor/bin/phpunit --exclude-group functional -v;

phpunit: phpunit-unit phpunit-functional

phpunit-coverage-clover:
	php $(BASEDIR)/vendor/bin/phpunit -v --coverage-clover ./build/logs/clover.xml;

phpunit-coverage-html:
	php $(BASEDIR)/vendor/bin/phpunit -v --coverage-html ./build/coverage;

phpcs:
	php $(BASEDIR)/vendor/bin/phpcs -p --extensions=php --standard=ruleset.xml src;

.PHONY: composer-install create-data-types phpunit phpcs help