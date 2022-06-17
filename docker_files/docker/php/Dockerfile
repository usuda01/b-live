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
RUN cd /root \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer
RUN docker-php-ext-install \
    pdo_mysql \
    bcmath
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir==/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd
RUN curl -SL https://deb.nodesource.com/setup_16.x | bash
RUN apt-get install -y nodejs
RUN apt-get install -y ffmpeg=7:4.3.4-0+deb11u1

ENV LANG C.UTF-8
ENV LANGUAGE en_US:

# rootだと実行できない
#RUN composer install
