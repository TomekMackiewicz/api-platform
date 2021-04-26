# SURVEY API

## Installation

- docker-compose up --build
- Set JWT keys:
```sh
docker-compose exec php sh -c '
    set -e
    apk add openssl
    mkdir -p config/jwt
    jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}
    echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
'
```

## Run app
docker-compose --env-file [env filename] up

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

bin/console hautelook:fixtures:load

bin/phpunit