#!/bin/bash

php-fpm --daemonize

nginx -g 'daemon off;'