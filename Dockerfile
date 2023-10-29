FROM --platform=linux/amd64 ubuntu:20.04

# php 8.0
RUN apt-get update \
    && DEBIAN_FRONTEND="noninteractive" apt-get install -y nginx curl zip unzip git software-properties-common supervisor sqlite3 libxrender1 libxext6 mysql-client libssh2-1-dev autoconf libz-dev\
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y php8.0-fpm php8.0-cli php8.0-gd php8.0-mysql php8.0-intl php8.0-pgsql \
       php8.0-imap php-memcached php8.0-mbstring php8.0-xml php8.0-curl \
       php8.0-sqlite3 php8.0-zip php8.0-pdo-dblib php8.0-bcmath php8.0-ssh2 php8.0-dev php8.0-redis php-pear \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && mkdir /run/php

RUN apt-get install -y --no-install-recommends --no-install-suggests \
    nginx \
    ca-certificates \
    gettext \
    mc \
    libmcrypt-dev  \
    libicu-dev \
    libcurl4-openssl-dev \
    mysql-client \
    libldap2-dev \
    libfreetype6-dev \
    libfreetype6 \
    libpcre3-dev  \
    curl \
    libpcsclite-dev \
    vim \
    unzip

# extsensions for php
RUN apt-get update && \
    apt-get install -y --no-install-recommends --no-install-suggests \
    php8.0-common \
    php8.0-mongodb \
    php8.0-curl \
    php8.0-intl \
    php8.0-soap \
    php8.0-xml \
    php8.0-bcmath \
    php8.0-mysql \
    php8.0-amqp \
    php8.0-mbstring \
    php8.0-ldap \
    php8.0-zip \
    php8.0-xml \
    php8.0-xmlrpc \
    php8.0-gmp \
    php8.0-ldap \
    php8.0-gd \
    php8.0-dev \
    php8.0-redis \
    php8.0-xmlreader \
    php8.0-dom \
    php8.0-fpm \
    php8.0-imagick \
    php8.0-tokenizer \
    php8.0-posix \
    php8.0-sockets \
    php8.0-iconv \
    php8.0-exif \
    php8.0-ftp \
    php8.0-simplexml \
    php8.0-xmlreader \
    php8.0-xdebug && \
    echo "extension=apcu.so" | tee -a /etc/php/8.0/mods-available/cache.ini
#    php-mcrypt \

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Install node.js
RUN apt install -y gpg-agent && \
    curl -sL https://deb.nodesource.com/setup_14.x | bash - && \
    apt update && apt install -y nodejs yarn

# set timezone Asia/Almaty
RUN cp /usr/share/zoneinfo/Asia/Almaty /etc/localtime

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log \
	&& ln -sf /dev/stderr /var/log/php8.0-fpm.log

RUN rm -f /etc/nginx/sites-enabled/*
RUN rm -f /etc/nginx/sites-available/*
COPY ./docker_files/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker_files/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker_files/opt/. /opt/
COPY ./docker_files/php-fpm/kalkancrypt.so /usr/lib/php/20200930
COPY ./docker_files/php-fpm/kalkancrypt.ini /etc/php/8.0/fpm/conf.d/40-kalkancrypt.ini
COPY ./docker_files/php-fpm/kalkancrypt.ini /etc/php/8.0/cli/conf.d/40-kalkancrypt.ini
COPY ./docker_files/php-fpm/kalkancrypt.ini /etc/php/8.0/mods-available/40-kalkancrypt.ini
RUN sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g" /etc/php/8.0/fpm/php.ini
RUN sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g" /etc/php/8.0/cli/php.ini
COPY ./docker_files/certs/. /usr/local/share/ca-certificates/
COPY ./docker_files/certs/. /usr/share/ca-certificates/

RUN update-ca-certificates --fresh

RUN echo 'alias "export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/opt/kalkancrypt/:/opt/kalkancrypt/lib/engines"' >> ~/.bashrc

RUN mkdir -p /etc/nginx/ssl

COPY . /var/www/
RUN mkdir -p /var/run/php && touch /var/run/php/php8.0-fpm.sock && touch /var/run/php/php8.0-fpm.pid

COPY entrypoint.sh /entrypoint.sh

WORKDIR /var/www/
RUN chmod 755 /entrypoint.sh

RUN chown -R www-data:www-data /var/www
RUN chmod 775 /var/www/
RUN chmod -R 777 /var/www/storage
RUN chmod -R 777 /var/www/bootstrap/cache
RUN chmod -R 777 /var/www/public

EXPOSE 80
CMD ["/entrypoint.sh"]
