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

2. Danh sách port chuẩn

|process	| port	|
|-----------|-------|
|nginx     	|80		|
|nodejs    	|2108|
|mysql     	|3306|
|mongodb   	|27017|

## Yêu cầu
* Nodejs
* Mysql
* Mongodb
* Redis
* Composer
* Npm
* Nginx or Apache
## Cài đặt PHP
1. Cài đặt Composer
	1. Môi trường window

		Download tại [đây](https://getcomposer.org/Composer-Setup.exe)

		**Chú ý** enable module openssl (tìm file php.ini, bỏ comment tất cả các dòng chứa `extension=php_openssl.dll`)
	1. Môi trường linux (ubuntu)

		`curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer`
	1. Moi truong Mac

		Install mcrypt
		```
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

	```
	composer install
	```
	
	```
	sudo chmod -R 777 storage
	```
## Cài đặt nodejs
1. Cài đặt nodejs
	
	Chuyển vào thư mục core (`meotrics/core`), gõ
	```
	npm install
	```

	**go in to meotrics/core/config, copy production.json to default.json, config your database in default.json**

## Cấu hình

1. Cấu hình Web Server
	
	Có thể chạy hệ thống bằng apache hoặc nginx, với mỗi server, copy và sửa các đoạn config như dưới
	1.  Apache > 2.2
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
	2. Apache <= 2.2
		```
		<VirtualHost *:80>
			DocumentRoot "E:\workspace\nodemeotrics\meotrics\dashboard\public"
			ServerName meotrics.dev
			ErrorLog "logs/meotrics.dev-error.log"
			CustomLog "logs/meotrics.dev-access.log" common

			<Directory />
				AllowOverride FileInfo Options=MultiViews
			</Directory>
		</VirtualHost>
 		```
	2.  Cấu hình nginx
		```
		server {
			charset utf-8;
			listen 80;

			server_name meotrics.run;
			root        /home/thanhpk/space/meotrics/dashboard/public/;
			index       index.php;

			access_log  /home/thanhpk/tmp/meotrics-access.log;
			error_log   /home/thanhpk/tmp/meotrics-error.log;

			location / {
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
4. Cấu hình Mysql
	
	Tạo tài khoản mysql có tên meotrics/meotrics123

    **Trong thư mục `/dashboard` tạo một file .env có nội dung giống với .env.example, sửa config csdl ở đây**
    
	Đặt `DB_USERNAME/DB_PASSWORD` là `meotrics/meotrics123`
	
    Import database file  `\resources\meotrics_dashboard.sql`
4. Đường dẫn chuẩn
  * Dashboard:
    * Frontend: meotrics.dev | meotrics.run
  * Core API

    127.0.0.1:2108

4. Chạy chương trình
	1. Vào thư mục core, gõ
	
		```node app.js```
	
	2. Truy cập vào địa chỉ `http://meotrics.dev/auth/login` để đăng nhập