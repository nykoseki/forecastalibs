FROM php:8.0-fpm

RUN mkdir -p /opt/product
WORKDIR /opt/product

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
COPY php.ini /usr/local/etc/php/conf.d/php.ini
COPY composer.json /opt/product/composer.json

# library install
RUN apt-get update \
  && apt-get install -y zlib1g-dev libzip-dev zip libpng-dev libjpeg-dev procps curl dnsutils iputils-ping net-tools netcat traceroute wget \
  libjpeg-dev \
  libfreetype6-dev \
  libmagick++-dev \
  libmagickwand-dev \
  libpq-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  libwebp-dev \
  libxpm-dev \
  && docker-php-ext-install zip pdo_mysql \
  && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd \
  && curl -sL https://deb.nodesource.com/setup_12.x | bash - \
  && apt-get install -y nodejs \
  && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('SHA384', 'composer-setup.php') === '$(wget -q -O - https://composer.github.io/installer.sig)') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer \
  && composer install

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin
ENV PATH $PATH:/opt/product/vendor/bin


