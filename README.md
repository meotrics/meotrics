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
**Chú ý** : Đảm bảo các module dưới đều được load trong file `httpd.conf`

```apache
LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_ftp_module modules/mod_proxy_ftp.so
LoadModule proxy_http_module modules/mod_proxy_http.so
LoadModule proxy_ajp_module modules/mod_proxy_ajp.so
LoadModule proxy_connect_module modules/mod_proxy_connect.so
```

1.  Apache from 2.2, 2.4, 2.6, ...
  ```apache
  <VirtualHost *:80>
    DocumentRoot "E:\workspace\nodemeotrics\meotrics\dashboard\public"
    ServerName app.meotrics.dev

    ErrorLog "logs/meotrics.dev-error.log"
    CustomLog "logs/meotrics.dev-access.log" common

    ProxyPreserveHost On
    ProxyPass /api http://127.0.0.1:1711/api
    ProxyPassReverse /api http://127.0.0.1:1711/api
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
2. Apache before 2.2
  ```apache
  <VirtualHost *:80>
    DocumentRoot "E:\workspace\nodemeotrics\meotrics\dashboard\public"
    ServerName app.meotrics.dev

    ErrorLog "logs/meotrics.dev-error.log"
    CustomLog "logs/meotrics.dev-access.log" common
      
    ProxyPreserveHost On
    ProxyPass /api http://127.0.0.1:1711/api
    ProxyPassReverse /api http://127.0.0.1:1711/api

    <Directory />
      AllowOverride FileInfo Options=MultiViews
    </Directory>
  </VirtualHost>

  <VirtualHost *:80>
    DocumentRoot "E:\workspace\nodemeotrics\meotrics\landing"
    ServerName meotrics.dev

    ErrorLog "logs/meotricslanding.dev-error.log"
    CustomLog "logs/meotricslanding.dev-access.log" common
   
    <Directory />
      AllowOverride FileInfo Options=MultiViews
    </Directory>
  </VirtualHost>

  <VirtualHost *:80>
    DocumentRoot "E:\workspace\nodemeotrics\meotrics\client\client.com"
    ServerName client.meotrics.dev
	      
    ErrorLog "logs/client.meotrics.dev-error.log"
    CustomLog "logs/client.meotrics.dev-access.log" common
    
    <Directory />
      AllowOverride FileInfo Options=MultiViews
    </Directory>
  </VirtualHost>
  ```

#### Nginx

```nginx
server {
  charset utf-8;
  listen 80;

  server_name app.meotrics.dev;
  root        /home/thanhpk/space/meotrics/dashboard/public/;
  index       index.php;

  access_log  /home/thanhpk/tmp/meotrics-access.log;
  error_log   /home/thanhpk/tmp/meotrics-error.log;

  location /api {
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header Host $http_host;
    proxy_pass http://127.0.0.1:1711/api;
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

server {
  charset utf-8;
  listen 80;

  server_name meotrics.dev;
  root        /home/thanhpk/space/meotrics/landing/;
  index       index.html;

  access_log  /home/thanhpk/tmp/meotricslanding-access.log;
  error_log   /home/thanhpk/tmp/meotricslanding-error.log;

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
	
server {
  charset utf-8;
  listen 80;
	
  server_name client.meotrics.dev;
  root        /home/thanhpk/space/meotrics/dashboard/public/;
  index       index.php;
	
  access_log  /home/thanhpk/tmp/client.meotrics-access.log;
  error_log   /home/thanhpk/tmp/client.meotrics-error.log;
	
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
