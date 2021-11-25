#!/usr/bin/php

php bin/console doctrine:query:sql "ALTER SEQUENCE users_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE exams_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE questions_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE answers_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE categories_id_seq RESTART WITH 1"

php bin/phpunit --group users

php bin/console doctrine:query:sql "ALTER SEQUENCE users_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE exams_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE questions_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE answers_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE categories_id_seq RESTART WITH 1"

php bin/phpunit --group exams

php bin/console doctrine:query:sql "ALTER SEQUENCE users_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE exams_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE questions_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE answers_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE categories_id_seq RESTART WITH 1"

php bin/phpunit --group questions

php bin/console doctrine:query:sql "ALTER SEQUENCE users_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE exams_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE questions_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE answers_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE categories_id_seq RESTART WITH 1"

php bin/phpunit --group answers

php bin/console doctrine:query:sql "ALTER SEQUENCE users_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE exams_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE questions_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE answers_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE categories_id_seq RESTART WITH 1"

php bin/phpunit --group categories