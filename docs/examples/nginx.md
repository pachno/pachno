# Nginx installation

## Example configuration file
A complete example for Pachno using HTTPS, X-Accel for downloading files, and setting expiry/caching headers. 

Replace the server name, PATH_TO_PACHNO and PATH_TO_YOUR_SSLs according to your server. Upload the configuration file to your server (usually into /etc/nginx/available-sites/) and enable it, then restart the nginx service or reload the configuration. 

```nginx
server {
        listen 80;
        listen [::]:80;

        server_name pachno.example.com;
        # Permanently rewrite to the SSL secured version.
        return 301 https://$host$request_uri;
}

server {
        listen 443 ssl;
        listen [::]:443 ssl;

        server_name pachno.example.com;
        root {PATH_TO_PACHNO}/public;
        index index.php index.html index.htm;

        # Your SSL setup. Consider the following as example.
	ssl_certificate             {PATH_TO_YOUR_SSLs}/pachno.example.com.crt;
	ssl_certificate_key         {PATH_TO_YOUR_SSLs}/pachno.example.com.key;
	ssl_protocols               TLSv1 TLSv1.1 TLSv1.2;
	ssl_prefer_server_ciphers   on;
	ssl_ciphers                 DHE-RSA-AES128-GCM-SHA256:DHE-DSS-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES256-GCM-SHA384:kEDH+AESGCM:DHE-RSA-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-DSS-AES128-SHA256:DHE-RSA-AES256-SHA256:DHE-DSS-AES256-SHA:DHE-RSA-AES256-SHA:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES128-SHA:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA:ECDHE-ECDSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384:AES128-SHA256:AES256-SHA256:AES128-SHA:AES256-SHA:AES:CAMELLIA:DES-CBC3-SHA:!aNULL:!eNULL:!EXPORT:!DES:!RC4:!MD5:!PSK:!aECDH:!EDH-DSS-DES-CBC3-SHA:!EDH-RSA-DES-CBC3-SHA:!KRB5-DES-CBC3-SHA;
	ssl_session_timeout         5m;
	ssl_session_cache           shared:SSL:10m;
	ssl_dhparam                 {PATH_TO_YOUR_SSLs}/dhparams.pem;

        # Deny all attempts to access hidden files such as .htaccess, .htpasswd, .DS_Store (Mac).
        location ~ /\. {
                deny all;
                access_log off;
                log_not_found off;
        }

        # Your PHP setup. This example is using a unix socket for PHP-FPM and sets some additional parameters.
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;

                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                fastcgi_intercept_errors on;
                fastcgi_buffer_size 128k;
                fastcgi_buffers 256 16k;
                fastcgi_busy_buffers_size 256k;
                fastcgi_temp_file_write_size 256k;
                fastcgi_read_timeout 1200;

                # With an PHP-FPM pool listening on Unix socket php-fpm.sock:
                fastcgi_pass unix:/var/run/php-fpm.sock;
        }

        # X-Accel-Redirect: Internal alias for uploaded files. This feature can be enabled within the `Uploads and attachments` section of the Pachno configuration web interface.
        location ^~ /private {
                internal;
                alias {PATH_TO_PACHNO}/files;
        }

        # Try serving static assets first, then PHP rewrite.
        location ~ /(.*) {
                set $suburi $1;
                try_files $uri $uri/ /index.php?url=$suburi&$args;
        }

        client_max_body_size 50M;

        # Add caching headers to static assets.
        location ~* \.(js|css|eot|woff|woff2|ttf|svg|svgz|png|jpe?g|gif|ico)$ {
                expires 180d;
                add_header Pragma public;
                add_header Cache-Control "public";
                # Optional: Disable logging for static assets.
                log_not_found off;
                access_log off;
        }
}
```