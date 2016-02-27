# meotrics
Analytics software




# Cài đặt môi trường dev

## Cài đặt chung
1. Trỏ tên miền
[window: Windows\System32\drivers\etc\host]
[linux: ]
[mac: ]
trỏ danh sách tên miền sau

meotrics.dev            -> 127.0.0.1
backend.meotrics.dev    -> 127.0.0.1
meotrics.run            -> 198.98.102.113
backend.meotrics.run    -> 198.98.102.113

2. Danh sách port chuẩn
process		port
------------------
nginx     :80
nodejs    :2108
mysql     :3306
mongodb   :27017

## Cài đặt PHP
1. Cài đặt Composer
1.1 Môi trường window
Download tại: https://getcomposer.org/Composer-Setup.exe
*Chú ý* enable module mbstring (tìm file php.ini, bỏ comment tất cả các dòng chứa `extension=php_mbstring.dll`)
1.2 Môi trường linux (ubuntu)
`curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer`
2. Khởi tạo Yii framework
Chuyển vào thư mục dashboard2 (`meotrics/dashboard2`)
gõ
`$ php init`
chọn môi trường là development, gõ tiếp
`composer self-update
composer install`
3. Cấu hình Web Server
3.1 Cấu hình apache
`<VirtualHost *:80>
    DocumentRoot "E:\workspace\nodemeotrics\meotrics\dashboard2\frontend\web"
    ServerName meotrics.dev
    ErrorLog "logs/meotrics.dev-error.log"
    CustomLog "logs/meotrics.dev-access.log" common
    <Directory />
    	AllowOverride none
    	Require all granted
        # use mod_rewrite for pretty URL support
           RewriteEngine on
           # If a directory or a file exists, use the request directly
           RewriteCond %{REQUEST_FILENAME} !-f
           RewriteCond %{REQUEST_FILENAME} !-d
           # Otherwise forward the request to index.php
           RewriteRule . index.php
           # use index.php as index file
           DirectoryIndex index.php
           # ...other settings...
    </Directory>
</VirtualHost>

<VirtualHost *:80>
       ServerName backend.meotrics.dev
       DocumentRoot "E:\workspace\nodemeotrics\meotrics\dashboard2\backend\web"

       <Directory />
           # use mod_rewrite for pretty URL support
           RewriteEngine on
           # If a directory or a file exists, use the request directly
           RewriteCond %{REQUEST_FILENAME} !-f
           RewriteCond %{REQUEST_FILENAME} !-d
           # Otherwise forward the request to index.php
           RewriteRule . index.php
           # use index.php as index file
           DirectoryIndex index.php
           # ...other settings...
           AllowOverride none
           Require all granted
    </Directory>
</VirtualHost>
`
3.2 Cấu hình nginx
`server {
       charset utf-8;
       client_max_body_size 128M;

       listen 80; ## listen for ipv4
       #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

       server_name meotrics.run;
       root        /home/thanhpk/space/meotrics/dashboard2/frontend/web/;
       index       index.php;

       access_log  /home/thanhpk/tmp/frontend-access.log;
       error_log   /home/thanhpk/tmp/frontend-error.log;

       location / {
           # Redirect everything that isn't a real file to index.php
           try_files $uri $uri/ /index.php?$args;
       }

       # uncomment to avoid processing of calls to non-existing static files by Yii
       #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
       #    try_files $uri =404;
       #}
       #error_page 404 /404.html;

       location ~ \.php$ {
           include fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
           fastcgi_pass   unix:/var/run/php5-fpm-meotrics.sock;
           try_files $uri =404;
       }

       location ~ /\.(ht|svn|git) {
           deny all;
       }
   }

   server {
       charset utf-8;
       client_max_body_size 128M;

       listen 80; ## listen for ipv4
       #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

       server_name backend.meotrics.run;
       root        /home/thanhpk/space/meotrics/dashboard2/backend/web/;
       index       index.php;

       access_log  /home/thanhpk/tmp/backend-access.log;
       error_log   /home/thanhpk/tmp/backend-error.log;

       location / {
           # Redirect everything that isn't a real file to index.php
           try_files $uri $uri/ /index.php?$args;
       }

       # uncomment to avoid processing of calls to non-existing static files by Yii
       #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
       #    try_files $uri =404;
       #}
       #error_page 404 /404.html;

       location ~ \.php$ {
           include fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
           fastcgi_pass   unix:/var/run/php5-fpm-meotrics.sock;
           try_files $uri =404;
       }

       location ~ /\.(ht|svn|git) {
           deny all;
       }
   }`
4. Đường dẫn chuẩn
..* Dashboard:
....* Frontend: meotrics.dev | meotrics.run
....* Backend: backend.meotrics.dev | backend.meotrics.dev
..* Core API
127.0.0.1:2108
