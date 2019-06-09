FROM alpine:3.9

RUN adduser -D -u 1000 -g 1000 -s /bin/sh www-data && \
    mkdir -p /data/site /data/log /data/conf && \
    chown -R www-data:www-data /data;

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories;

RUN apk add --no-cache musl-dev openssl supervisor git go zsh nginx php7 php7-opcache php7-mysqli php7-pdo_mysql php7-mbstring php7-json php7-zlib php7-gd php7-session php7-fpm php7-curl php7-xml php7-zip php7-iconv php7-sockets php7-ctype php7-openssl;

ENV PATH $PATH:/root/go/bin

COPY data/conf/supervisord.conf /data/conf/supervisord.conf
COPY data/conf/nginx/nginx.conf /etc/nginx/nginx.conf
COPY data/conf/php7/php.ini /etc/php7/php.ini
COPY data/conf/php7/php-fpm.conf /etc/php7/php-fpm.conf

RUN go version; \
    git config --global http.sslVerify false; \
    go get -u -v -ldflags "-s -w" github.com/yudai/gotty; \
    rm -rf /root/go/src /root/go/pkg /var/cache/apk /usr/share/man /var/cache/apk/* /tmp/*; \
    apk del git go; \
    ls /data /data/nginx /data/site /data/conf/nginx;

CMD ["/usr/bin/supervisord", "-c", "/data/conf/supervisord.conf"]

EXPOSE 80 443 7070

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1/fpm-ping
