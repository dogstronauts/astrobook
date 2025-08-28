# syntax=docker/dockerfile:1.7

FROM mztrix/php-fpm AS app_base

WORKDIR /var/www/app

RUN --mount=type=cache,target=/var/cache/apk \
    set -eux; \
    apk add --no-cache --no-progress \
      acl \
      file \
      gettext \
      git;

RUN --mount=type=cache,target=/var/cache/apk \
    set -eux; \
    apk add --no-cache --no-progress \
      php84-phar \
      php84-mbstring \
      php84-iconv \
      php84-openssl \
      php84-ctype \
      php84-sodium \
      php84-xml \
      php84-tokenizer \
      php84-dom \
      php84-simplexml \
      php84-xmlwriter \
      php84-intl \
      php84-session \
      php84-pdo \
      php84-pdo_pgsql;

RUN git config --global --add safe.directory /var/www/app

COPY --link .docker/php/conf.d/app.ini /etc/php84/php.ini

COPY --from=composer/composer:2-bin --link /composer /usr/local/bin/composer

COPY --chown=www-data:www-data composer.json composer.lock ./

VOLUME /var/www/app/vendor

COPY --link .docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN set -eux; chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]

FROM app_base AS app_dev

WORKDIR /var/www/app

RUN --mount=type=cache,target=/var/cache/apk \
    set -eux; \
    apk add --no-cache --no-progress php84-pecl-xdebug;

COPY --link .docker/php/conf.d/50_xdebug.ini /etc/php84/conf.d/50_xdebug.ini

FROM nginx:alpine AS nginx_dev

WORKDIR /var/www/app/public

RUN set -eux; \
    adduser -u 82 -S -D -G www-data -H -s /sbin/nologin www-data; \
    chown -R www-data:www-data /var/www/app;

COPY --link .docker/nginx/sites-enabled/api.conf /etc/nginx/conf.d/default.conf
COPY --link .docker/nginx/nginx.conf /etc/nginx/nginx.conf

CMD ["nginx", "-g", "daemon off;"]
