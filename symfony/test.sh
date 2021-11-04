#!/usr/bin/php

php bin/console doctrine:query:sql "ALTER SEQUENCE $1_id_seq RESTART WITH 1"
php bin/phpunit --group $1