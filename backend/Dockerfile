FROM php:8.4-fpm

RUN apt update && apt install -y git zip unzip nginx libpq-dev

ARG BUILD prod

ENV APP_ENV ${BUILD}

COPY --chown='www-data:www-data' . /workspace/backend

WORKDIR /workspace/backend

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN set -eux; \
    if [ "${BUILD}" = "prod" ]; \ 
    then \
        mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
        composer install --no-dev --optimize-autoloader --classmap-authoritative; \
    else \
        mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; \
        pecl install xdebug && docker-php-ext-enable xdebug; \
        echo "[xdebug]" >> "$PHP_INI_DIR/xdebug.ini"; \
        echo "xdebug.mode=develop,debug" >> "$PHP_INI_DIR/xdebug.ini"; \
        echo "xdebug.start_with_request=yes" >> "$PHP_INI_DIR/xdebug.ini"; \
        echo "xdebug.discover_client_host=1" >> "$PHP_INI_DIR/xdebug.ini"; \
    fi

RUN curl -sS https://get.symfony.com/cli/installer | bash && mv ~/.symfony5/bin/symfony /usr/local/bin/symfony

RUN pecl install apcu && docker-php-ext-enable apcu

RUN pecl install ds && docker-php-ext-enable ds

RUN apt update && apt install -y git zip unzip nginx libpq-dev

RUN docker-php-ext-install pdo pdo_pgsql

COPY nginx.conf /etc/nginx/sites-enabled/default

COPY startup.sh /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh

CMD ["/usr/local/bin/startup.sh"]