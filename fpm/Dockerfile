FROM php:7.4-fpm-alpine

WORKDIR /usr/local/etc

COPY www.conf ./php-fpm.d/

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories; \
    apk add --no-cache tzdata; \
    ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime; \
    echo "Asia/Shanghai" > /etc/timezone;

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    php -v;

# RUN pecl channel-update pecl.php.net; \
#     pecl install swoole; \
#     docker-php-ext-install pdo_mysql;
