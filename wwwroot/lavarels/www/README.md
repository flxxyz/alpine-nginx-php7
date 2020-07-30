## 目录权限

```
chmod 0755 -R storage
```

## 提升执行速度

设置配置缓存（尽可能在生产环境使用）
```
php artisan config:cache
```

设置路由缓存
```
php artisan route:cache
```

优化自动加载器
```
composer install --optimize-autoloader --no-dev
```

## composer

### 安装(临时安装使用)
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

### aliyun源
```
//全局
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
//取消全局配置
composer config -g --unset repos.packagist

//项目
composer config repo.packagist composer https://mirrors.aliyun.com/composer/
//取消项目配置
composer config --unset repos.packagist
```

### 升级最新版
```
composer self-update
```

### 清除缓存
```
composer clear
```