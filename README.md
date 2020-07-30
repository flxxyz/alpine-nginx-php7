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
      - "自定义TCP端口:9502/tcp"
      - "自定义UDP端口:9503/udp"
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
[flxxyz/nginx:latest](https://github.com/edogDocker/nginx/blob/master/Dockerfile) 默认安装

### 一些额外的php扩展
- inotify
- memcached
- mongodb
- mysqli
- mysqlnd
- opcache
- pcntl
- pdo_mysql
- pdo_sqlite
- readline
- redis
- sqlite3
- swoole (仅 [flxxyz/php:7.3-cli](https://github.com/edogDocker/php/blob/master/cli/Dockerfile) 安装)
- xmlrpc
- tokenizer
- yaml
- zip

## 数据库
### mariadb
```
db:
  container_name: db
  restart: always
  image: mariadb:10.5.1-bionic
  environment:
    MYSQL_ROOT_PASSWORD: 12345678
  volumes:
    - 数据目录:/var/lib/mysql
  networks:
    - site
```

> 更多设置请查看[mariadb说明](https://hub.docker.com/_/mariadb)

### mongo
```
db:
  container_name: db
  restart: always
  image: mongo:4.2.3-bionic
  environment:
    MONGO_INITDB_ROOT_USERNAME: root
    MONGO_INITDB_ROOT_PASSWORD: 12345678
  volumes:
    - 数据目录:/data/db
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
  image: redis:5.0.7-alpine
  networks:
    - site
```

#### 持久化存储
```
volumes:
  - 数据目录:/data
command: redis-server --appendonly yes
```
> 更多设置请查看[redis说明](https://hub.docker.com/_/redis)

### memcached
```
cache:
  container_name: cache
  restart: always
  image: memcached:1.6.0-alpine
  networks:
    - site
```

#### 设置使用内存大小
```
command: memcached -m 64
```
> 更多设置请查看[mamcached说明](https://hub.docker.com/_/memcached)

## 配置实例时使用的版本
### docker
> version: 19.03.12

#### docker-compose
> version: 1.24.1  
> compose file format: 3.7

