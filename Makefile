.PHONY: build

RM = rm -rf

composer-version:
	composer --version

composer-install:
	composer install --prefer-dist

composer-autoload:
	composer dump-autoload -o

cleanup:
	@$(RM) build/api/*
	@$(RM) build/coverage/*
	@$(RM) build/logs/*
	@$(RM) build/pdepend/*
	@$(RM) build/phpdox/*

lint:
	vendor/bin/parallel-lint -j $(shell nproc --all) --blame src tests

phploc:
	vendor/bin/phploc --count-tests --log-csv=./build/logs/phploc.csv --names="*.php,*.phtml" --log-xml=./build/logs/phploc.xml src tests

pdepend:
	vendor/bin/pdepend --summary-xml=./build/logs/pdepend.xml --jdepend-xml=./build/logs/jdepend.xml --jdepend-chart=./build/pdepend/dependencies.svg --overview-pyramid=./build/pdepend/overview-pyramid.svg src,tests

phpmd:
	vendor/bin/phpmd src,tests xml ./build/phpmd.xml --reportfile ./build/logs/pmd.xml || true

phpcs:
	vendor/bin/phpcs --report=checkstyle --report-file=./build/logs/checkstyle.xml --standard=./build/phpcs.xml --extensions=php,phtml src tests || true

phpcpd:
	vendor/bin/phpcpd --log-pmd=./build/logs/pmd-cpd.xml --fuzzy --min-lines=3 --min-tokens=30 src || true

phpunit:
	php -d zend_extension=xdebug.so vendor/bin/phpunit --configuration=./build/phpunit-all.xml

phpunit-no-coverage:
	php vendor/bin/phpunit --configuration=./build/phpunit-all.xml --no-coverage

phpunit-nofail:
	php -d zend_extension=xdebug.so vendor/bin/phpunit --configuration=./build/phpunit-all.xml || true

phpdox:
	vendor/bin/phpdox -f ./build/phpdox.xml


metrics: cleanup lint phploc pdepend phpmd phpcs phpcpd phpunit-nofail phpdox

build: composer-install lint phpunit

deploy: build
	# No Deployment here -- library only

