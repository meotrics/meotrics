```apache
<VirtualHost *:80>
  ServerName api.meotrics.dev
  ProxyPreserveHost On
  ProxyPass / http://127.0.0.1:1711/
  ProxyPassReverse / http://127.0.0.1:1711/
</VirtualHost>

<VirtualHost *:443>
  ServerName api.meotrics.dev

  SSLEngine on
  SSLCertificateFile "conf/ssl.crt/server.crt"
  SSLCertificateKeyFile "conf/ssl.key/server.key"
  SSLProxyEngine on
  ProxyPreserveHost On
  ProxyPass / http://127.0.0.1:1711/
  ProxyPassReverse / http://127.0.0.1:1711/
  
</VirtualHost>

<VirtualHost *:443>
  ServerName app.meotrics.dev
  DocumentRoot "C:\Users\thanh\workspace\meotrics\dashboard\public"
  ErrorLog "logs/appmeotrics.dev-error443.log"
  CustomLog "logs/appmeotrics.dev-access443.log" common

  SSLEngine on
  SSLCertificateFile "conf/ssl.crt/server.crt"
  SSLCertificateKeyFile "conf/ssl.key/server.key"

  <Directory />
    Require all granted
    AllowOverride FileInfo Options=MultiViews
  </Directory>

  SSLProxyEngine on
  ProxyPreserveHost on
  ProxyPass /ws ws://127.0.0.1:2910/
  ProxyPassReverse /ws ws://127.0.0.1:2910/
</VirtualHost>

<VirtualHost *:80>
  ServerName app.meotrics.dev
  # Redirect to https
  RewriteEngine On
  RewriteCond %{HTTPS} off
  RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
</VirtualHost>

<VirtualHost *:443>
  ServerName meotrics.dev
  DocumentRoot "C:\Users\thanh\workspace\meotrics\landing"
  ErrorLog "logs/meotricslanding.dev-error443.log"
  CustomLog "logs/meotricslanding.dev-access443.log" common

  SSLEngine on
  SSLCertificateFile "conf/ssl.crt/server.crt"
  SSLCertificateKeyFile "conf/ssl.key/server.key"
  <Directory />
    Require all granted
    AllowOverride FileInfo Options=MultiViews
  </Directory>
  
</VirtualHost>

<VirtualHost *:80>
  ServerName meotrics.dev
  # Redirect to https
  RewriteEngine On
  RewriteCond %{HTTPS} off
  RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
</VirtualHost>

<VirtualHost *:80>
  DocumentRoot "C:/Users/thanh/workspace/meotrics/client/client.com"
  ServerName client.meotrics.dev

  ErrorLog "logs/client.meotrics.dev-error.log"
  CustomLog "logs/client.meotrics.dev-access.log" common

  <Directory />
    Require all granted
    AllowOverride FileInfo Options=MultiViews
  </Directory>
</VirtualHost>
```