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