# syntax=docker/dockerfile:1.4

#FROM mariadb as database_upstream
FROM postgres as database_upstream

FROM mztrix/php-fpm as app_dev

WORKDIR /var/www/app

RUN set -eux; \
    apk add --no-cache \
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
    php84-pdo  \
    php84-pdo_pgsql \
    php84-pecl-xdebug \
    acl \
    file \
    gettext \
    git \
    ;

RUN git config --global --add safe.directory /var/www/app

COPY --link .docker/php/conf.d/app.ini  /etc/php84/php.ini
COPY --link .docker/php/conf.d/50_xdebug.ini  /etc/php84/conf.d/50_xdebug.ini
#  Copy Composer from the composer/composer image
COPY --from=composer/composer:2-bin --link /composer /usr/local/bin/composer

# Copy only composer.json and composer.lock to leverage Docker caching
COPY --chown=www-data:www-data composer.json composer.lock ./

VOLUME /var/www/app/vendor
# Add the Docker entrypoint script
COPY --link .docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN set -eux; chmod +x /usr/local/bin/entrypoint

# Set the default entrypoint script
ENTRYPOINT ["entrypoint"]

FROM database_upstream as database_dev

#ENV MARIADB_ROOT_PASSWORD=password \
#    MARIADB_USER=user \
#    MARIADB_PASSWORD=password \
#    MARIADB_DATABASE=dbname
#
#RUN set -eux; \
#    apk --no-cache update; \
#    apk add --no-cache mariadb mariadb-client mariadb-server-utils;
#
#RUN mkdir -p /run/mysqld && chown -R mysql:mysql /run/mysqld; \
#    mkdir -p /var/lib/mysql && chown -R mysql:mysql /var/lib/mysql;
#
#COPY --link  .docker/mariadb/entrypoint.sh /usr/bin/entrypoint
#
#ENTRYPOINT ["entrypoint"]
#
#COPY --link .docker/mariadb/my.cnf.d/mariadb-server.cnf  /etc/my.cnf.d/mariadb-server.cnf
## Add the Docker entrypoint script
#COPY --link .docker/mariadb/healthcheck.sh /usr/local/bin/healthcheck

# Define a healthcheck command for the container
#HEALTHCHECK  \
#  --interval=10s  \
#  --timeout=5s  \
#  --start-period=10s  \
#  --retries=3 \
#  CMD ["healthcheck","--connect", "--innodb_initialized"]

#EXPOSE 3306

# Build Stage for Nginx
FROM nginx:alpine AS nginx_dev

# Définir le répertoire de travail
WORKDIR /var/www/app/public

RUN set -eux; \
    echo -e "\e[1;33m===> Creating www-data user for PHP-FPM\e[0m"; \
    adduser -D -u 82 -S -G www-data -s /sbin/nologin www-data; \
    echo -e "\e[1;33m===> www-data user created with UID 82 and GID 82\e[0m"; \
    chown -R www-data:www-data /var/www/app; \
    echo -e "\e[1;33m===> Set ownership of /var/www to www-data:www-data\e[0m";

# Copier la configuration spécifique de Nginx
COPY --link .docker/nginx/sites-enabled/api.conf /etc/nginx/conf.d/default.conf
COPY --link .docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Commande par défaut pour démarrer Nginx en mode foreground
CMD ["nginx", "-g", "daemon off;"]
