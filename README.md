![](http://blog.meotrics.com/wp-content/uploads/2016/03/Logo_Blue_word-1.png)

Hệ thống phân tích website
* * *

Cài đặt môi trường DEV
----
### Cài đặt chung

#### Trỏ tên miền

Trỏ danh sách tên miền sau để tiện phát triển hệ thống:

| Tên miền              | Địa chỉ           |
|-----------------------|-------------------|
|`meotrics.dev`           | `127.0.0.1`         |
|`app.meotrics.dev`		| `127.0.0.1`		|
|`client.meotrics.dev`		| `127.0.0.1`		|

Trong hệ thống Window, sửa file, `Windows\\System32\\drivers\\etc\\host`, trong Linux hoặc Mac, sửa file `/etc/hosts` (`/private/etc/hosts`)

#### config file
* Front-end

  *Trong thư mục `/dashboard` tạo một file .env có nội dung giống với `.env.example`, sửa config csdl ở đây*

  Đặt `DB_USERNAME/DB_PASSWORD` là `meotrics/meotrics123`

* Back-end

  Vào thư mục `meotrics/core/config`, copy file `production.json` thành `default.json`

#### Yêu cầu cài đặt các module sau

* Nodejs
* Mysql
* Mongodb
* Redis
* Composer
* Npm
* Nginx hoặc Apache

### Cài đặt Front End
1. Cài đặt Composer
  * Với môi trường Windows

    Download tại [đây](https://getcomposer.org/Composer-Setup.exe)

    **Chú ý** enable module `openssl` (tìm file `php.ini`, bỏ comment tất cả các dòng chứa *extension=php_openssl.dll*), enable thêm module `mbstring`
  * Với môi trường Linux

    Gõ lệnh sau trong terminal

    ```bash
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
    ```
  * Với môi trường Mac
    Cài đặt mcrypt trước, sau đó gõ

    ```bash
    brew update
    brew upgrade
    brew tap homebrew/dupes
    brew tap josegonzalez/homebrew-php
    brew install php54-mcrypt
    php --version // To Test your php
    sudo composer update
    ```

2. Khởi tạo Laravel framework
  Chuyển vào thư mục dashboard (`meotrics/dashboard`), tạo thư mục database, gõ

  ```bash
  sudo composer install
  sudo composer update
  sudo chmod -R 777 storage
  ```
3. [optional] Xóa cache
  ```bash
  php artisan route:clear
  php artisan clear-compiled
  php artisan cache:clear
  ```
### Cài đặt Backend
1. Cài đặt nodejs
  Chuyển vào thư mục core (`meotrics/core`), gõ

  ```bash
  npm install
  ```

### Cài đặt web server
Có thể chạy hệ thống bằng apache hoặc nginx, với mỗi server, copy và sửa các đoạn config như dưới
#### APACHE
**Chú ý** : Chỉ support Apache phiên bản lớn hơn 2.4, đảm bảo các module dưới đều được load trong file `httpd.conf`

```apache
LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_http_module modules/mod_proxy_http.so
LoadModule proxy_ajp_module modules/mod_proxy_ajp.so
LoadModule proxy_connect_module modules/mod_proxy_connect.so
LoadModule proxy_wstunnel_module modules/mod_proxy_wstunnel.so
```

```apache
<VirtualHost *:80>
    ServerName api.meotrics.com
    ProxyPreserveHost On
    ProxyPass / http://127.0.0.1:1711/
    ProxyPassReverse / http://127.0.0.1:1711/
</VirtualHost>

<VirtualHost *:443>
  ServerName app.meotrics.com
  SSLProxyEngine on
  ErrorLog "logs/meotrics.dev-error443.log"
  CustomLog "logs/meotrics.dev-access443.log" common

  SSLEngine on
  SSLCertificateFile E:/key/chained.pem
  SSLCertificateKeyFile E:/key/domain.key

  ProxyPreserveHost On
  ProxyPass /ws ws://127.0.0.1:2910/
  ProxyPassReverse /ws ws://127.0.0.1:2910/
</VirtualHost>

<VirtualHost *:80>
  DocumentRoot "E:\workspace\nodemeotrics\meotrics\dashboard\public"
  ServerName app.meotrics.dev

  ErrorLog "logs/meotrics.dev-error.log"
  CustomLog "logs/meotrics.dev-access.log" common

  <Directory />
    Require all granted
    AllowOverride FileInfo Options=MultiViews
  </Directory>
</VirtualHost>

<VirtualHost *:80>
  DocumentRoot "E:\workspace\nodemeotrics\meotrics\landing"
  ServerName meotrics.dev

  ErrorLog "logs/meotricslanding.dev-error.log"
  CustomLog "logs/meotricslanding.dev-access.log" common

  <Directory />
    Require all granted
    AllowOverride FileInfo Options=MultiViews
  </Directory>
</VirtualHost>

<VirtualHost *:80>
  DocumentRoot "E:\workspace\nodemeotrics\meotrics\client\client.com"
  ServerName client.meotrics.dev

  ErrorLog "logs/client.meotrics.dev-error.log"
  CustomLog "logs/client.meotrics.dev-access.log" common

  <Directory />
    Require all granted
    AllowOverride FileInfo Options=MultiViews
  </Directory>
</VirtualHost>
```
#### Nginx

```nginx
server {
  listen 80;
  charset utf-8;
  client_max_body_size 128M;

  server_name meotrics.com;
  return 301 https://$host$request_uri;
}

server {
  listen 80;
  server_name www.meotrics.com;
  return 301 http://meotrics.com$request_uri;
}

server {
  listen 443;
  server_name www.meotrics.com meotrics.com;
  ssl on;
  ssl_certificate /etc/ssl/certs/chained.pem;
  ssl_certificate_key /etc/ssl/private/domain.key;
  ssl_session_timeout 5m;
  ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
  ssl_ciphers ECDHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-G$
  ssl_session_cache shared:SSL:50m;
  ssl_dhparam /etc/ssl/certs/dhparam.pem;
  ssl_prefer_server_ciphers on;

  root /home/thanhpk/meotrics/landing/;

  location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
    expires 1y;
    log_not_found off;
  }
  server_tokens off;
  #more_set_headers "Server: Meotrics";

  index index.html;
  location ~ /\.(ht|svn|git) {
    deny all;
  }

  # Common bandwidth hoggers and hacking tools.
  if ($http_user_agent ~ "libwww-perl") {
    set $block_user_agents 1;
  }

  if ($http_user_agent ~ "GetRight") {
    set $block_user_agents 1;
  }

  if ($http_user_agent ~ "GetWeb!") {
    set $block_user_agents 1;
  }

  if ($http_user_agent ~ "Go!Zilla") {
    set $block_user_agents 1;
  }

  if ($http_user_agent ~ "Download Demon") {
    set $block_user_agents 1;
  }

  if ($http_user_agent ~ "Go-Ahead-Got-It") {
    set $block_user_agents 1;
  }

  if ($http_user_agent ~ "TurnitinBot") {
    set $block_user_agents 1;
  }

  if ($http_user_agent ~ "GrabNet") {
    set $block_user_agents 1;
  }

  if ($block_user_agents = 1) {
    return 403;
  }
}

server {
  listen 80;
  server_name api.meotrics.com;

  location / {
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header Host $http_host;
    proxy_pass http://127.0.0.1:1711/api;
  }
}

server {
  listen 443;
  server_name api.meotrics.com;

  ssl on;
  ssl_certificate /etc/ssl/certs/chained.pem;
  ssl_certificate_key /etc/ssl/private/domain.key;
  ssl_session_timeout 5m;
  ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
  ssl_ciphers ECDHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-RSA-AES128-SHA256:ECDHE-RSA-AES256-SHA:ECDHE-RSA-AES128-SHA:DHE-RSA-AES256-SHA:DHE-RSA-AES128-SHA;
  ssl_session_cache shared:SSL:50m;
  ssl_dhparam /etc/ssl/certs/dhparam.pem;
  ssl_prefer_server_ciphers on;

  large_client_header_buffers 8 32k;

  location / {
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header Host $http_host;
    proxy_pass http://127.0.0.1:1711/api;
  }
}

server {
  listen 80;
  server_name app.meotrics.com;
  return 301 https://$server_name$request_uri;
}

map $http_upgrade $connection_upgrade{
  default upgrade;
  '' close;
}

upstream websocket {
  server 127.0.0.1:2910;
}

server {
  charset utf-8;
  listen 443;

  server_name app.meotrics.com;
  root        /home/thanhpk/meotrics/dashboard/public/;
  index       index.php;
  ssl on;
  ssl_certificate /etc/ssl/certs/chained.pem;
  ssl_certificate_key /etc/ssl/private/domain.key;
  ssl_session_timeout 5m;
  ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
  ssl_ciphers ECDHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-RSA-AES128-SHA256:ECDHE-RSA-AES256-SHA:ECDHE-RSA-AES128-SHA:DHE-RSA-AES256-SHA:DHE-RSA-AES128-SHA;
  ssl_session_cache shared:SSL:50m;
  ssl_dhparam /etc/ssl/certs/dhparam.pem;
  ssl_prefer_server_ciphers on;

  access_log  /home/thanhpk/tmp/meotrics-access443.log;
  error_log   /home/thanhpk/tmp/meotrics-error443.log;

  location /ws {
    proxy_pass http://websocket;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection $connection_upgrade;
  }

  location / {
    try_files $uri $uri/ /index.php?$args;
  }

  location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param REMOTE_ADDR $http_x_real_ip;
    fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
    fastcgi_pass   unix:/var/run/php5-fpm.sock;
    try_files $uri =404;
  }
          
  location ~ /\.(ht|svn|git) {
    deny all;
  }
}
```
### Database server
* Mongodb

  Chỉ cần cài đặt và chạy mongod ở cổng 27017
* MySql

  Tạo tài khoản mysql có tên `meotrics/meotrics123`

  Import database từ file  `\resources\meotrics_dashboard.sql`

Chạy chương trình
---

* Chạy apache (hoặc nginx) ở cổng `80`
* Chay Mysql ở cổng `3306` _(cổng mặc định của mysql)_
* Chạy mongodb ở cổng `27017` _(cổng mặc định của mongodb)_

* Chạy backend bằng lệnh
  ```bash
  cd core
  node app.js
  ```
  Backend sẽ lắng nghe ở 2 cổng `2108` và `1711`.

* Truy cập vào địa chỉ [meotrics.dev](client.meotrics.dev) để vào website.
* Truy cập vào địa chỉ [app.meotrics.dev/auth/login](http://app.meotrics.dev/auth/login) để đăng nhập.
* Truy cập vào địa chỉ [client.meotrics.dev](client.meotrics.dev) để chạy web site client.
