# meotrics
Analytics software




# Cài đặt môi trường dev

## Cài đặt chung
1. Trỏ tên miền

[window: Windows\System32\drivers\etc\host]

[linux: ]

[mac: ]

trỏ danh sách tên miền sau

| Tên miền              | Địa chỉ           |
|-----------------------|-------------------|
|meotrics.dev           | 127.0.0.1         |
|backend.meotrics.dev   | 127.0.0.1         |
|meotrics.run           | 198.98.102.113    |
|backend.meotrics.run   | 198.98.102.113    |

2. Danh sách port chuẩn

|process	| port	|
|-----------|-------|
|nginx     	|80		|
|nodejs    	|2108|
|mysql     	|3306|
|mongodb   	|27017|

## Cài đặt PHP
1. Cài đặt Composer
	1. Môi trường window

		Download tại [đây](https://getcomposer.org/Composer-Setup.exe)

		**Chú ý** enable module openssl (tìm file php.ini, bỏ comment tất cả các dòng chứa `extension=php_openssl.dll`)
	1. Môi trường linux (ubuntu)

		`curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer`

2. Khởi tạo Laravel framework

	Chuyển vào thư mục dashboard (`meotrics/dashboard`), tạo thư mục database, gõ

	```
	composer require "laravel/installer"
	```
	
	**Tạo một file .env có nội dung giống với .env.example, sửa config csdl ở đây**
3. Cài đặt nodejs
	
	Chuyển vào thư mục core (`meotrics/core`), gõ
	```
	npm install
	```

3. Cấu hình Web Server

	1.  Cấu hình apache
		```
		<VirtualHost *:80>
			DocumentRoot "E:\workspace\nodemeotrics\meotrics\dashboard\public"
			ServerName meotrics.dev
			ErrorLog "logs/meotrics.dev-error.log"
			CustomLog "logs/meotrics.dev-access.log" common

			<Directory />
				Require all granted
				AllowOverride FileInfo Options=MultiViews
			</Directory>
		</VirtualHost>
 		```

	2.  Cấu hình nginx
		```
		server {
			charset utf-8;
			client_max_body_size 128M;
			listen 80; ## listen for ipv4
			#listen [::]:80 default_server ipv6only=on; ## listen for ipv6

			server_name meotrics.run;
			root        /home/thanhpk/space/meotrics/dashboard/public/;
			index       index.php;

			access_log  /home/thanhpk/tmp/frontend-access.log;
			error_log   /home/thanhpk/tmp/frontend-error.log;

			location / {
				# Redirect everything that isn't a real file to index.php
				try_files $uri $uri/ /index.php?$args;
			}
			
			location ~ \.php$ {
				include fastcgi_params;
		           fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
		           fastcgi_pass   unix:/var/run/php5-fpm.sock;
		           try_files $uri =404;
			}

			location ~ /\.(ht|svn|git) {
				deny all;
			}
		}
		```
4. Đường dẫn chuẩn
  * Dashboard:
    * Frontend: meotrics.dev | meotrics.run
  * Core API

    127.0.0.1:2108
