Hệ thống thu thập và phân tích hành vi người dùng

[![](http://blog.meotrics.com/wp-content/uploads/2016/03/Logo_Blue_word-1.png)](http://meotrics.com) [![License](https://img.shields.io/crates/l/rustc-serialize.svg)](https://opensource.org/licenses/Apache-2.0)
* * *

Cài đặt môi trường DEV
----
### Cài đặt chung

#### Trỏ tên miền

Trỏ danh sách tên miền sau để tiện phát triển hệ thống:

| Tên miền              | Địa chỉ           |
|-----------------------|-------------------|
|`meotrics.dev`         | `127.0.0.1`       |
|`app.meotrics.dev`		  | `127.0.0.1`		    |
|`client.meotrics.dev` 	| `127.0.0.1`		    |
|`api.meotrics.dev`     | `127.0.0.1`       |

Trong hệ thống Window, sửa file, `Windows\\System32\\drivers\\etc\\host`, trong Linux hoặc Mac, sửa file `/etc/hosts` (`/private/etc/hosts`)

#### Yêu cầu cài đặt các module sau
* [Git LFS](https://git-lfs.github.com/)
* [Typescript](https://www.typescriptlang.org/)
* Nodejs
* MySQL
* MongoDb
* Redis
* Composer (xem cách cài đặt composer ở [đây](docs/composerinstall.md))
* Npm
* Nginx hoặc Apache


#### config file
* Front-end

  *Trong thư mục `/dashboard` tạo một file .env có nội dung giống với `.env.example`, sau đó sửa lại cấu hình cơ sở dữ liệu trong file .env*

  Đặt `DB_USERNAME/DB_PASSWORD` là `meotrics/meotrics123`

* Back-end

  Vào thư mục `meotrics/core/config`, copy file `production.json` thành `default.json`

  Ở Git Bash, gõ
  ```bash
  git lfs install
  git lfs pull
  ```

### Cài đặt Front End
Chuyển vào thư mục dashboard (`meotrics/dashboard`), gõ

```bash
composer install
composer update
chmod -R 777 storage
```

### Cài đặt Backend

Chuyển vào thư mục core (`meotrics/core`), gõ `npm install` để download các package cần thiết.

Backend được viết chủ yếu bằng ngôn ngữ Typescript, muốn chạy được bằng NodeJS, phải biên dịch mã
nguồn này ra file typescript bằng lệnh `tsc`. Mã nguồn typescript lại tham chiếu tới một số file ở
project [DefinitelyTyped](https://github.com/DefinitelyTyped/DefinitelyTyped). Do vậy, muốn biên dịch
thành công, cần phải tải prject DefinitelyTyped về máy. Vẫn ở thư mục `meotrics/core`, gõ

```bash
cd ../..
# download project DefinitelyTyped
git clone https://github.com/DefinitelyTyped/DefinitelyTyped.git
cd meotrics/core
# compile to javascript code
tsc
```

### Cài đặt web server
Có thể chạy hệ thống bằng apache hoặc nginx, với mỗi server, copy và sửa các đoạn config như dưới
#### APACHE
**Chú ý** : Chỉ support Apache phiên bản lớn hơn 2.4, đảm bảo các module dưới đều được load trong file `httpd.conf`

```apache
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_http_module modules/mod_proxy_http.so
LoadModule proxy_ajp_module modules/mod_proxy_ajp.so
LoadModule proxy_connect_module modules/mod_proxy_connect.so
LoadModule proxy_wstunnel_module modules/mod_proxy_wstunnel.so
```
Thêm toàn bộ nội dung ở [cấu hình](docs/apacheconf.md) vào file `http-vhosts.conf`

Sau khi cấu hình xong, truy cập vào địa chỉ [https://meotrics.com](https://meotrics.com) và [https://app.meotrics.com](https://app.meotrics.com),
[https://api.meotrics.com](https://api.meotrics.com), để accept certification cho trình duyệt, với mỗi một trình duyệt mới, đều phải accept lại
certification.

Restart Apache sau khi thực hiện xong các bước trên.
#### Nginx

Thêm toàn bộ nội dung ở [cấu hình nginx](docs/nginxconf.md) vào một file mới có tên là `meotrics.conf` trong thư mục `/etc/nginx/conf.d/`
Restart nginx bằng lệnh
```sh
  sudo service nginx restart
```
### Database server
* Mongodb

  Chỉ cần cài đặt và chạy mongod ở cổng 27017
* MySql

  Tạo tài khoản mysql có tên `meotrics/meotrics123`

  Import database từ file  `\resources\meotrics_dashboard.sql`


Cập nhật mã nguồn
---
Sau một thời gian, các package của hệ thống sẽ bị outdated, tại thư mục `meotrics` dùng chuỗi lệnh sau để cập nhật lại mã nguồn

```bash
git pull
git lfs pull
cd ../meotrics/dashboard
composer update
cd ../../DefinitelyTyped
git pull
cd ../meotrics/core
npm install
tsc
```

Khởi động hệ thống
---

* Chạy apache (hoặc nginx) ở cổng `80`
* Chay Mysql ở cổng `3306` _(cổng mặc định của mysql)_
* Chạy mongodb ở cổng `27017` _(cổng mặc định của mongodb)_

* Chuyển vào thư mục `meotrics/core`, khởi động các backend services bằng lệnh
  ```bash
  node app.js
  ```
  Các backend services sẽ lắng nghe ở 3 cổng `2108`, `1711` và `2910`.

* Truy cập vào địa chỉ [client.meotrics.dev](client.meotrics.dev) để vào website.
* Truy cập vào địa chỉ [app.meotrics.dev/auth/login](http://app.meotrics.dev/auth/login) để đăng nhập.
* Truy cập vào địa chỉ [client.meotrics.dev](client.meotrics.dev) để chạy web site client.
