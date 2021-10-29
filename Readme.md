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
docker-compose exec php bin/console doctrine:migrations:diff
docker-compose exec php bin/console doctrine:migrations:migrate
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

```sh
bin/console hautelook:fixtures:load
```
```sh
bin/phpunit
```

## Redis

```sh
redis-cli
KEYS *
GET [key]
```

## TODO

- finish user tests
- add username
- jwt keys chmod!!!

- roles