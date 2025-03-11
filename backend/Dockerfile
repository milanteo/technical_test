FROM php:8.4-fpm

ARG BUILD prod

ENV APP_ENV ${BUILD}

RUN set -eux; \
    if [ "${BUILD}" = "prod" ]; \ 
    then \
        mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    else \
        mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; \
    fi

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash && mv ~/.symfony5/bin/symfony /usr/local/bin/symfony

RUN pecl install apcu   && docker-php-ext-enable apcu

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apt update && apt install -y git zip unzip nginx libpq-dev

RUN docker-php-ext-install pdo pdo_pgsql

COPY nginx.conf /etc/nginx/sites-enabled/default

COPY startup.sh /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh

CMD ["/usr/local/bin/startup.sh"]