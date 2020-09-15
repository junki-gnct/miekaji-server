FROM php:7.2-fpm
COPY php.ini /usr/local/etc/php/
RUN apt-get update
RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN /usr/local/bin/docker-php-ext-install mysqli
RUN /usr/local/bin/docker-php-ext-configure gd --with-freetype-dir --with-jpeg-dir=/usr/include/
RUN /usr/local/bin/docker-php-ext-install gd