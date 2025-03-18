#!/bin/bash

php bin/console cache:clear

php bin/console cache:warmup

php bin/console doctrine:database:create --if-not-exists

php bin/console doctrine:schema:update --force

if [ ! -d "keys" ]; then
    mkdir keys
    openssl genpkey -algorithm RSA -out keys/private_key.pem -pkeyopt rsa_keygen_bits:2048 
    openssl rsa -in keys/private_key.pem -pubout -out keys/public_key.pem
fi

chown -R www-data:www-data var

chown -R www-data:www-data keys

php-fpm --daemonize

nginx -g 'daemon off;'