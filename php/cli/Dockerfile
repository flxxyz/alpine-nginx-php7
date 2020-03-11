FROM php:7.3-cli-alpine

WORKDIR /usr/src/myapp

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories; \
    apk add --no-cache tzdata; \
    ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime; \
    echo "Asia/Shanghai" > /etc/timezone;

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    php -v;

RUN apk upgrade --update \
    && apk add --no-cache \
    autoconf \
    libc-dev \
    make \
    musl-dev \
    re2c \
    bison \
    libtool \
    zlib-dev \
    && rm -rf /var/cache/apk/*

RUN apk add --no-cache gcc libmemcached-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && apk del gcc

RUN apk add --no-cache gcc libmcrypt-dev \
    && pecl install mcrypt \
    && docker-php-ext-enable mcrypt \
    && apk del gcc

RUN apk add --no-cache gcc \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del gcc

RUN apk add --no-cache gcc yaml-dev \
    && pecl install yaml \
    && docker-php-ext-enable yaml \
    && apk del gcc

RUN apk add --no-cache gcc \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apk del gcc

RUN apk add --no-cache g++ \
    && pecl install swoole \
    && docker-php-ext-enable swoole \
    && apk del g++

RUN tac $(which docker-php-ext-install) | sed '1,3d' | tac > $(which docker-php-ext-install); \
    apk add --no-cache libxml2-dev \
    docker-php-ext-install -j$(nproc) bcmath \
    pdo_mysql \
    mysqli \
    xmlrpc \
    tokenizer \
    zip \
    opcache; \
    ls -al /usr/src; \
    rm -rf /usr/src/php;

EXPOSE 9501 9502 9503