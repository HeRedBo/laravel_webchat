###  laravel + swoole web聊天室项目

项目使用 laravel + Vue + swoole 构建的的聊天模块。主要涉及如下技术

 - 后端基于 Laravel 5.8 + LaravelS 扩展包引入对 Swoole 的支持
 - 基于 Swoole 提供的 WebSocket 服务器功能进行聊天通信
 - 结合 Swoole 的异步任务和协程机制提升系统响应速度和性能
 - 使用 Redis + MySQL 作为数据存储介质
 - 实现用户注册登录功能，登录后用户才可以进入聊天室
 - 支持文字 + 图片 + 表情 + 链接消息
 - 支持查看历史消息和离线消息
 - 支持与普通用户/机器人聊天
 - 前端基于 Vue.js 框架实现 UI，并且引入 Vuex、Vue-Router 实现前后端分离
 
 
### 项目演示地址：
    http://webchat.yirenkeji.com
 
 用户账号可自行创建，用户名请使用邮箱地址
 
## 安装

### 环境要求
- php >=7.0
- 安装好 composer
- 安装好 npm 
- 安装好npm(建议使用国内镜像cnpm,安装速度更快)
- redis
- MySQL 

### 安装方法

- 1.git clone或者下载解压到本地
```
git clone https://github.com/HeRedBo/laravel_webchat
```
- 2.更改storage目录权限，
```
chomd -R 777 ./storage
```
- 3.使用 composer 安装 项目扩展包
```
composer install 
```
- 4.执行如下命令
```
# 数据表创建
php artisan migrate 

# 设置文件存储文件夹软链接 =>"public/storage" to "storage/app/public"
php artisan storage:link 
```
- 5.前端样式构建
```
# 安装前端需要的扩展包
npm install // 建议使用 cnpm install 安装 npm 包 
# 包安装成功后构建前端样式文件 
npm run dev 
```
- 6. 配置nginx 站点信息 
```
upstream laravels {
    # Connect IP:Port
    server 127.0.0.1:5200 weight=5 max_fails=3 fail_timeout=30s;
    keepalive 16;
}
server {
    listen 80;
    ## 根据自己的实际情况配置 本地记得配置 hosts 文件
    server_name webchat.yirenkeji.com;
    access_log  /usr/local/var/log/nginx/webchat.yirenkeji.com.access.log;
    error_log /usr/local/var/log/nginx/webchat.yirenkeji.com.error.log;
    root /var/www/laravel/webchat/public;
    index index.php index.html index.htm;
    # Nginx 处理静态资源，LaravelS 处理动态资源
    location / {
        try_files $uri @laravels;
    }
    
    location @laravels {
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_pass http://laravels;
    }
}
```
- 7.启动项目
```
# 在项目根目录下如下命令 即可启动项目
php bin/laravels start 
```
接下来就可以见证奇迹的时刻，打开浏览器，输入自己配置好的域名，即可进入项目开启聊天之旅了


 

   
   

    
    
    
    
 
 
