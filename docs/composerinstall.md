Cài đặt composer
----
## Với môi trường Windows

Download tại [đây](https://getcomposer.org/Composer-Setup.exe)

**Chú ý** enable module `openssl` (tìm file `php.ini`, bỏ comment tất cả các dòng chứa *extension=php_openssl.dll*), enable thêm module `mbstring`
## Với môi trường Linux

Gõ lệnh sau trong terminal

```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

## Với môi trường Mac
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