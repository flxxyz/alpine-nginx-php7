FROM alpine

RUN echo 'https://mirrors.aliyun.com/alpine/v3.9/main/' > /etc/apk/repositories; \
    echo 'https://mirrors.aliyun.com/alpine/v3.9/community/' >> /etc/apk/repositories; \
    apk add --no-cache musl-dev nginx openssl; \
    apk add --no-cache musl-dev php7 php7-mysqli php7-pdo_mysql php7-mbstring php7-json php7-zlib php7-gd php7-session php7-fpm php7-curl php7-xml php7-zip php7-iconv php7-sockets php7-ctype php7-openssl;

RUN mkdir -p /data/site/; \
    mkdir -p /run/nginx; \
    touch /run/nginx/nginx.pid

WORKDIR /data

CMD ["/usr/sbin/php-fpm7"]
CMD ["/usr/sbin/nginx", "-g", "daemon off;"]
#CMD ["/usr/sbin/nginx"]
