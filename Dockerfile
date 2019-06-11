FROM alpine:3.9

RUN adduser -D -u 1000 -g 1000 -s /bin/sh www-data; \
    mkdir -p /data/site /data/log /data/conf /data/run; \
    chown -R www-data:www-data /data;
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories;
RUN apk add --no-cache openssl supervisor zsh;
RUN apk add --no-cache musl-dev nginx php7 php7-cli php7-opcache php7-pdo php7-pdo_mysql php7-mysqli php7-mongodb php7-sqlite3 php7-pdo_sqlite php7-pgsql php7-pdo_pgsql php7-mbstring php7-json php7-zlib php7-gd php7-session php7-fpm php7-curl php7-xml php7-xmlreader php7-zip php7-iconv php7-sockets php7-ctype php7-mcrypt php7-bcmath php7-openssl php7-redis php7-memcached php7-bz2 php7-xsl php7-pear php7-pcntl php7-common php7-snmp php7-timezonedb php7-exif php7-posix php7-gettext php7-sysvshm php7-shmop php7-odbc php7-imap php7-calendar php7-tidy php7-dom php7-soap php7-sysvmsg php7-ssh2 php7-ftp php7-phar php7-tokenizer php7-xmlwriter;

ENV PATH $PATH:/root/go/bin

COPY data/conf/supervisord.conf /data/conf/supervisord.conf
COPY data/conf/nginx/nginx.conf /etc/nginx/nginx.conf
COPY data/conf/php/php.ini /etc/php7/php.ini
COPY data/conf/php/php-fpm.conf /etc/php7/php-fpm.conf

RUN apk add --no-cache git go; \
    go version; \
    git config --global http.sslVerify false; \
    go get -u -v -ldflags "-s -w" github.com/yudai/gotty; \
    apk del git go; \
    rm -rf /root/go/src /root/go/pkg /var/cache/apk /usr/share/man /var/cache/apk/* /tmp/*; \
    ls /data;

CMD ["/usr/bin/supervisord", "-c", "/data/conf/supervisord.conf"]

HEALTHCHECK --interval=60s --timeout=10s CMD curl --silent --fail http://127.0.0.1/fpm-ping
