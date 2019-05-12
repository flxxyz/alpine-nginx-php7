FROM alpine:3.9

RUN adduser -D -u 1000 -g 1000 -s /bin/sh www-data && \
    mkdir -p /data/site /data/log /data/conf && \
    chown -R www-data:www-data /data

RUN echo 'https://mirrors.aliyun.com/alpine/v3.9/main/' > /etc/apk/repositories; \
    echo 'https://mirrors.aliyun.com/alpine/v3.9/community/' >> /etc/apk/repositories; \
    apk add --no-cache musl-dev openssl supervisor nginx php7 php7-mysqli php7-pdo_mysql php7-mbstring php7-json php7-zlib php7-gd php7-session php7-fpm php7-curl php7-xml php7-zip php7-iconv php7-sockets php7-ctype php7-openssl;

COPY data/conf/supervisord.conf /data/conf/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/data/conf/supervisord.conf"]
