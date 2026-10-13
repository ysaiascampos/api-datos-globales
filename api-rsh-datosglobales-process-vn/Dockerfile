FROM registryserver.mds.cl/mds/nginx-php8.3-nodb:3.1 as config

ENV MY_PROJECT_ROOT /var/www/html/site
WORKDIR $MY_PROJECT_ROOT

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    && docker-php-ext-install -j$(nproc) soap \
    && apt-get clean \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/*

RUN echo        "memory_limit = 1024M\\n" > /usr/local/etc/php/conf.d/site.ini \
 && mkdir /var/log/site \
 && chown www-data:www-data /var/log/site  \
 && chown www-data:www-data /usr/local/etc/php/conf.d/site.ini \
 && envsubst "\$MY_PROJECT_ROOT" < /etc/nginx/templates/site.nginx.conf.template > /etc/nginx/nginx.conf

FROM config as dev

COPY --from=composer:2.9.5 /usr/bin/composer /usr/local/bin/composer


FROM dev as compile

COPY . .

RUN "/deletegitmetadata.sh" \
    && rm -rf ci \
    && composer install --no-dev


FROM config as deploy

ENV MDSF_OPCACHE_DISABLED=0

RUN echo  "opcache.jit_buffer_size=128M\\n" \
   "opcache.validate_timestamps = \${MDSF_OPCACHE_DISABLED}\\n" > /usr/local/etc/php/conf.d/site.ini

RUN rm -f bootstrap/cache/*.php || true

COPY --from=compile --chown=www-data:www-data $MY_PROJECT_ROOT .

COPY docker/laravel-init.sh /docker-entrypoint.d/laravel-init.sh
RUN chmod +x /docker-entrypoint.d/laravel-init.sh
