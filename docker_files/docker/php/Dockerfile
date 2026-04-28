FROM php:7.3.29-fpm
RUN apt-get update \
    && apt-get install -y \
        vim \
        git \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libjpeg-dev
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN docker-php-ext-install \
    pdo_mysql \
    bcmath
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir==/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd
RUN curl -SL https://deb.nodesource.com/setup_16.x | bash
RUN apt-get install -y nodejs
RUN apt-get install -y ffmpeg

ENV LANG C.UTF-8
ENV LANGUAGE en_US:

# rootだと実行できない
#RUN composer install
