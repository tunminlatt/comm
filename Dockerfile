FROM php:7.3-alpine

RUN apk upgrade --update -q \
  && apk --no-cache -q add openssl zip unzip git mysql-client vim coreutils freetype-dev libpng-dev libjpeg-turbo-dev freetype libpng libjpeg-turbo libltdl libmcrypt-dev libzip-dev \
  && apk --no-cache -q add npm yarn \
  && docker-php-ext-configure gd \
    --with-gd \
    --with-freetype-dir=/usr/include/ \
    --with-png-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ && \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
  && docker-php-ext-configure zip --with-libzip \
  && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql opcache zip calendar pcntl exif \
  && apk del --no-cache -q freetype-dev libpng-dev libjpeg-turbo-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apk --update -q add supervisor

RUN composer global require hirak/prestissimo

# Configure supervisord
COPY ./supervisord.conf /etc/supervisord.conf
ADD root /etc/crontabs/
ADD php.ini /usr/local/etc/php

WORKDIR /app

COPY . /app

RUN composer install

RUN yarn install

RUN chmod -R 775 /app

RUN ln -s /app/storage/app /app/public/storage
# www-data
RUN adduser -u 1000 -D -S -G root apache && \
rm -rf /var/cache/apk/*

ENTRYPOINT sh -c "/app/supervisor.sh && php artisan serve --host=0.0.0.0 --port=80"

EXPOSE 80
