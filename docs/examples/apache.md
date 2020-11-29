# Apache installation

## Example configuration file
A complete example virtual host file for Pachno using url rewriting (required) and https.

Replace the server name, PATH_TO_PACHNO and PATH_TO_YOUR_SSLs according to your server. 
Upload the configuration file to your server (usually into /etc/apache2/sites-available/) and enable it, then restart the apache2 service or reload the configuration. 

```apacheconfig
<VirtualHost *:443>
        ServerName projects.pachno.l

        DocumentRoot PATH_TO_PACHNO/public
        <Directory PATH_TO_PACHNO/public>
                Options FollowSymLinks
                AllowOverride All
                <IfModule mod_rewrite.c>
                        RewriteEngine On
                        RewriteBase /

                        RewriteCond %{REQUEST_URI} \..+$
                        RewriteCond %{REQUEST_URI} !\.(html|wsdl|json|xml)$
                        RewriteCond %{REQUEST_FILENAME} -f [OR]
                        RewriteCond %{REQUEST_FILENAME} -d
                        RewriteRule .* - [L]

                        RewriteRule ^(.*)$ index.php?url=$1 [NC,QSA,L]

                        # Automatically forward to https if accessing via http
                        # RewriteCond %{HTTPS} !=on
                        # RewriteRule ^/?(.*) https://%{SERVER_NAME}/$1 [R,L]
                </IfModule>
        </Directory>

        SSLEngine on

        SSLCertificateFile      PATH_TO_YOUR_SSLs/certs/projects.pachno.crt
        SSLCertificateKeyFile PATH_TO_YOUR_SSLs/private/projects.pachno.key
        
        ErrorLog /var/log/apache2/pachno.error.log

</VirtualHost>

<VirtualHost *:80>
        ServerName projects.pachno.l
        RewriteCond %{HTTPS} !=on
        RewriteRule ^/?(.*) https://%{SERVER_NAME}/$1 [R,L]
</VirtualHost>
```