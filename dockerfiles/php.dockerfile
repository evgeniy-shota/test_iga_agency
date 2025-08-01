FROM php:8.4-fpm-bookworm

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN apt-get update \
&& apt-get install -y git \
zip \
libzip-dev \
sudo \
vim \
libpq-dev \
&& docker-php-ext-install pdo pdo_pgsql zip

#RUN apk --no-cache --update add \
#postgresql17-dev \
#&& docker-php-ext-install pdo pdo_pgsql  
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
#RUN sed -i "s/;extension=zip/extension=zip/g" $PHP_INI_DIR/php.ini

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

RUN addgroup --gid ${GID} --system laravel
RUN adduser --ingroup laravel --system --shell /bin/sh --uid ${UID} laravel

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf
    
USER laravel

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]

