## 开始
### 拉取配置
```
git clone https://github.com/flxxyz/fast-deploy-website.git && cd fast-deploy-website
```

### 启动实例
```
docker-compose up -d
```

### 添加站点
在`wwwroot`目录新建**站点目录**存放**站点文件**

1. 添加下列代码块至`docker-compose.yml`
```
站点名称:
    container_name: 站点名称
    restart: always
    image: flxxyz/php:7.3-fpm
    volumes:
      - ./wwwroot/站点名称:/var/www/html
    networks:
      - site
```

2. 新建`nginx.conf`文件到`wwwroot/站点名称/conf`目录下保存
```
server
{
    listen 80;
    server_name example.com;  #注意修改域名
    root /usr/share/nginx/html/站点目录/www;
    index index.html index.htm index.php;

    #可以使用一些默认的 rewrite 规则(laravel, phpwind, thinkphp, typecho,wordpress), 使用自定义规则添加放置 nginx/rewrite.d 中
    #include /etc/nginx/rewrite.d/typecho.rewrite;

    location ~ \.php$
    {
        fastcgi_pass  站点名称:9000;
        fastcgi_index index.php;
        include       fastcgi_params;
        fastcgi_param SCRIPT_FILENAME /var/www/html/$fastcgi_script_name;
    }
           
    error_log /var/log/nginx/站点名称.error.log;
    access_log /var/log/nginx/站点名称.access.log;
}
```

需要修改 **php.ini** 或 **php-fpm** 配置可按照以下路径配置
```
    volumes:
      - ./wwwroot/站点名称/conf/fpm.conf:/usr/local/etc/php-fpm.d/www.conf
      - ./wwwroot/站点名称/conf/php.ini:/usr/local/etc/php/php.ini
```

### 添加CLI应用
> CLI容器开放端口 9000-9050
```
default-cli:
    container_name: default-cli
    restart: always
    image: flxxyz/php:7.3-cli
    volumes:
      - ./wwwroot/default-cli/www:/usr/src/myapp
    command: php /usr/src/myapp/index.php
    networks:
      - site
```

### 使用laravelS
```
laravels:
    container_name: laravels
    restart: always
    image: flxxyz/php:7.3-cli
    ports:
      - "自定义TCP本地端口:9001/tcp"
      - "自定义UDP本地端口:9002/udp"
    volumes:
      - ./wwwroot/laravels/www:/usr/src/myapp
    command: php bin/laravels start
    networks:
      - site
```
> 具体代码可查看 [docker-compose.yml](https://github.com/flxxyz/fast-deploy-website/blob/master/docker-compose.yml)，如何实现TCP,UDP,Websocket相关功能查看 [app/Services](https://github.com/flxxyz/fast-deploy-website/tree/master/wwwroot/laravels/www/app/Services)


### composer安装依赖
> flxxyz/php:7.3-cli 容器已集成composer

添加一个composer实例映射需要执行的路径
```
composer:
    container_name: composer
    image: composer:latest
    volumes:
      - ./wwwroot/站点名称/www:/app
    command: composer install
    networks:
      - site
```

### ngx-fancyindex
[flxxyz/nginx](https://github.com/edogDocker/nginx/blob/master/Dockerfile) 所有tag默认安装

### 一些额外的php扩展
- bcmath
- gd
- inotify
- intl
- libxml
- mcrypt
- memcached
- mongodb
- mysqli
- mysqlnd
- openssl
- pcntl
- opcache
- pcntl
- pdo_mysql
- pdo_sqlite
- readline
- redis
- SimpleXML
- soap
- sockets
- sqlite3
- standard
- swoole (仅 [flxxyz/php:7.3-cli](https://github.com/edogDocker/php/blob/master/cli/Dockerfile) 安装)
- xml
- xmlrpc
- xmlwriter
- xsl
- tokenizer
- yaml
- zip
- zlib

## 数据库
### mariadb
```
db:
  container_name: db
  restart: always
  image: mariadb:10.5-focal
  environment:
    MYSQL_ROOT_PASSWORD: 12345678
  volumes:
    - ./database/mariadb:/var/lib/mysql
  networks:
    - site
```

> 更多设置请查看[mariadb说明](https://hub.docker.com/_/mariadb)

### mongo
```
db:
  container_name: db
  restart: always
  image: mongo:4.4-bionic
  environment:
    MONGO_INITDB_ROOT_USERNAME: root
    MONGO_INITDB_ROOT_PASSWORD: 12345678
  volumes:
    - ./database/mongo:/data/db
  networks:
    - site
```
> 更多设置请查看[mongo说明](https://hub.docker.com/_/mongo)


## 缓存
### redis
```
cache:
  container_name: cache
  restart: always
  image: redis:6-alpine
  networks:
    - site
```

#### 持久化存储
```
volumes:
  - ./database/redis:/data
command: redis-server --appendonly yes
```
> 更多设置请查看[redis说明](https://hub.docker.com/_/redis)

### memcached
```
cache:
  container_name: cache
  restart: always
  image: memcached:1.6-alpine
  networks:
    - site
```

#### 设置使用内存大小
```
command: memcached -m 64
```
> 更多设置请查看[mamcached说明](https://hub.docker.com/_/memcached)


## 搜索引擎
### elasticsearch
```
es01:
  container_name: es01
  restart: always
  image: elasticsearch:7.9.0
  environment:
    - node.name=es01
    - cluster.name=es-docker-cluster
    - discovery.seed_hosts=es02
    - cluster.initial_master_nodes=es01,es02
    - bootstrap.memory_lock=true
    - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
  ulimits:
    memlock:
      soft: -1
      hard: -1
  volumes:
    - ./database/es/node01:/usr/share/elasticsearch/data
  ports:
    - "9200:9200"
    - "9300:9300"
  networks:
    - site
es02:
  container_name: es02
  restart: always
  image: elasticsearch:7.9.1
  environment:
    - node.name=es02
    - cluster.name=es-docker-cluster
    - discovery.seed_hosts=es01
    - cluster.initial_master_nodes=es01,es02
    - bootstrap.memory_lock=true
    - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
  ulimits:
    memlock:
      soft: -1
      hard: -1
  volumes:
    - ./database/es/node02:/usr/share/elasticsearchdata
  networks:
    - site
```
> 更多设置请查看[elasticsearch说明](https://hub.docker.com/_/elasticsearch)

> [使用Docker安装Elasticsearch](https://www.elastic.co/guide/en/elasticsearch/reference/7.5/docker.html)


## 消息队列
### rabbitmq
```
amqp:
  container_name: amqp
  restart: always
  image: rabbitmq:3.8
  environment:
    - RABBITMQ_DEFAULT_USER=amqp
    - RABBITMQ_DEFAULT_PASS=12345678
  volumes:
   - ./database/amqp:/var/lib/rabbitmq
  ports:
    - "15672:15672"
  networks:
    - site
```
> 更多设置请查看[rabbitmq说明](https://hub.docker.com/_/rabbitmq)

## DNS
### pi-hole
```
dns:
  container_name: dns
  restart: unless-stopped
  image: pihole/pihole:latest
  environment:
    - TZ='Asia/Shanghai'
    - WEBPASSWORD=12345678
  volumes:
    - ./database/pihole/:/etc/pihole/:z
    - ./database/dnsmasq.d/:/etc/dnsmasq.d/:z
  ports:
    - "53:53/tcp"
    - "53:53/udp"
    - "67:67/udp"
    - "9080:80"
    - "9443:443"
  networks:
    - site
```
> 更多设置请查看[pihole说明](https://hub.docker.com/r/pihole/pihole)

## 配置实例时使用的版本
### docker
> version: 19.03.12

#### docker-compose
> version: 1.24.2
> compose file format: 3.7

