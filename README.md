### 使用
拉取镜像 (也可以直接使用配置启动，会自己拉取镜像)
```
docker push flxxyz/alpine-nginx-php7
```

拉取配置
```
git clone https://github.com/flxxyz/alpine-nginx-php7.git && cd alpine-nginx-php7
```

启动实例
```
docker-compose up -d
```

配置项均放在`data`目录下

### 配置实例时使用的版本
#### docker
version: 18.09.5

#### docker-compose

version: 1.24.0
compose file format: 3.7


