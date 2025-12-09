# syntax=docker/dockerfile:1.19.0

ARG VERSION=latest

FROM mztrix/php-fpm:${VERSION} AS app_base

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
    PHP_PACKAGES="php85-phar php85-mbstring php85-iconv php85-openssl php85-ctype php85-sodium php85-xml php85-tokenizer php85-dom php85-simplexml php85-xmlwriter php85-intl php85-session php85-pdo php85-pdo_pgsql "; \
    apk add --no-cache --no-progress ${PHP_PACKAGES}

COPY --link .docker/php/php.ini /etc/php85/php.ini
COPY --link .docker/php/conf.d/* /etc/php85/conf.d/

COPY --from=composer/composer:2-bin --link /composer /usr/local/bin/composer

COPY --chown=www-data:www-data composer.json composer.lock ./

VOLUME /var/www/app/vendor

COPY --link .docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN set -eux; chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]

FROM nginx:alpine AS nginx_base

WORKDIR /var/www/app/public

RUN set -eux; \
    adduser -u 82 -S -D -G www-data -H -s /sbin/nologin www-data; \
    chown -R www-data:www-data /var/www/app;

COPY --link .docker/nginx/sites-enabled/api.conf /etc/nginx/conf.d/default.conf
COPY --link .docker/nginx/nginx.conf /etc/nginx/nginx.conf

CMD ["nginx", "-g", "daemon off;"]
