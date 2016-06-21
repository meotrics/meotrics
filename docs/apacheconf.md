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