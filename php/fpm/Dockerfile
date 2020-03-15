FROM php:7.3-fpm-alpine

WORKDIR /usr/local/etc

COPY www.conf ./php-fpm.d/

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

# tac $(which docker-php-ext-install) | sed '1,3d' | tac > $(which docker-php-ext-install); \
# sed -i 's/rm -rf/cd \/usr\/src\n                ls -al/' $(which docker-php-source); \

# BUG: 进行‘rm -rf’操作会造成目录不为空的错误，“Storage Driver”的问题
RUN sed -i 's/rm -rf "$dir"/rm -rf "$dir/*"\n        ls -al "$dir"/' $(which docker-php-source); \
    apk add --no-cache libxml2-dev libzip-dev; \
    docker-php-ext-install -j$(nproc) bcmath \
    pdo_mysql \
    mysqli \
    xmlrpc \
    tokenizer \
    zip \
    opcache; \
    echo 'ok';

EXPOSE 9000