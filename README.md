## 开始
### 拉取配置
```
git clone https://github.com/flxxyz/alpine-nginx-php7.git && cd alpine-nginx-php7
```

### 启动实例
```
docker-compose up -d
```

### 添加站点
在`wwwroot`目录新建**站点目录**存放**站点文件**

添加下列代码块至`docker-compose.yml`
```
站点名称:
    restart: always
    image: flxxyz/php:7.3-fpm
    volumes:
      - ./wwwroot/站点目录:/var/www/html
    networks:
      - site
```

新建`站点名称.conf`文件到`conf`目录
```
server
{
    listen 80;
    server_name example.com;  #注意修改域名
    root /usr/share/nginx/html/站点目录;
    index index.html index.htm index.php;

    #也可以新建 站点名称.rule 文件到conf/rule.d目录统一管理
    #include /etc/nginx/conf.d/rule.d/站点名称.rule;

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
> version: 19.03.6

#### docker-compose
> version: 1.24.1  
> compose file format: 3.7

