#!/usr/bin/php

php bin/console doctrine:query:sql "ALTER SEQUENCE users_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE exams_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE questions_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE answers_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE categories_id_seq RESTART WITH 1"
php bin/console doctrine:query:sql "ALTER SEQUENCE media_objects_id_seq RESTART WITH 1"

php bin/phpunit --group $1