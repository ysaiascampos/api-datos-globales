# Para usar oracle se debe incluir el package "yajra/laravel-oci8": "^13" en composer.json
FROM registryserver.mds.cl/mds/nginx-php8.3-oci8:1.3 AS base

ENV MY_PROJECT_ROOT /var/www/html/site
WORKDIR $MY_PROJECT_ROOT

RUN echo        "memory_limit = 1024M\\n" > /usr/local/etc/php/conf.d/site.ini \
 && mkdir /var/log/site \
 && chown www-data:www-data /var/log/site  \
 && chown www-data:www-data /usr/local/etc/php/conf.d/site.ini \
 && envsubst "\$MY_PROJECT_ROOT" < /etc/nginx/templates/site.nginx.conf.template > /etc/nginx/nginx.conf

FROM base AS dev

COPY --from=composer:2.9.5 /usr/bin/composer /usr/local/bin/composer


FROM dev AS compile

COPY . .

RUN "/deletegitmetadata.sh" \
    && rm -rf ci \
    && composer install --no-dev


FROM base AS deploy

ENV MDSF_OPCACHE_DISABLED=0

RUN echo  "opcache.jit_buffer_size=128M\\n" \
   "opcache.validate_timestamps = \${MDSF_OPCACHE_DISABLED}\\n" > /usr/local/etc/php/conf.d/site.ini

RUN rm -f bootstrap/cache/*.php || true

COPY --from=compile --chown=www-data:www-data $MY_PROJECT_ROOT .

COPY docker/laravel-init.sh /docker-entrypoint.d/laravel-init.sh
RUN chmod +x /docker-entrypoint.d/laravel-init.sh
