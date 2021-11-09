# SURVEY API

## Installation

```sh
docker-compose --env-file [env filename] up --build
```

## Generate jwt keys
```sh
php bin/console lexik:jwt:generate-keypair
```

## Run app

```sh
docker-compose --env-file [env filename] up
```

## Run migrations

```sh
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Login to postgres

```sh
psql -d [dbname] -U user
```

Other useful commands:

```sh
\du - list users
\l - list dbs
\dt = show tables
```

## php.ini location

```sh
/usr/local/etc/php
```

## Testing
reset sequence for functional tests
```sh
php bin/console doctrine:query:sql "ALTER SEQUENCE users_id_seq RESTART WITH 1"
bin/phpunit --group users

php bin/console doctrine:query:sql "ALTER SEQUENCE exams_id_seq RESTART WITH 1"
bin/phpunit --group exams
```

Run bash test.sh <entity>:

```sh
bash test-group.sh exams
```

to test single class, or:

```sh
bash test-all.sh exams
```
to test all classes.

## Redis

```sh
redis-cli
KEYS *
GET [key]
```

## TODO

- finish user tests

- roles?

The stream or file "/var/www/var/log/test.log" could not be opened in append mode: Failed to open stream: Permission denied