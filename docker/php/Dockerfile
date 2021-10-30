FROM php:8.0.3-fpm-alpine

COPY wait-for-it.sh /usr/bin/wait-for-it
COPY jwt.sh /usr/bin/jwt

RUN apk add --no-cache bash
RUN chmod +x /usr/bin/wait-for-it
RUN chmod +x /usr/bin/jwt

RUN apk --update --no-cache add git acl vim
RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql

RUN sed -i -e 's/;extension=pdo_pgsql/extension=pdo_pgsql/' /usr/local/etc/php/php.ini-production

RUN apk del postgresql-dev
RUN apk add --upgrade postgresql --update-cache --repository http://dl-3.alpinelinux.org/alpine/edge/main/

COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www

CMD composer install ; jwt ; wait-for-it database:5432 -- bin/console doctrine:migrations:migrate ; php-fpm

EXPOSE 9000