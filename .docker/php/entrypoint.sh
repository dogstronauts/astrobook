#!/bin/sh

set -e

#first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
    composer install --prefer-dist --no-interaction
    bin/console doctrine:migrations:migrate --no-interaction
    bin/console lexik:jwt:generate-keypair --skip-if-exists
fi

exec "$@"
