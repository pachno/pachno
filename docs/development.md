# Setting up a development environment
A local development environment can be set up with the included vagrant setup. The vagrant box is configured to be 
accessed via https://pachno.local:8443 for https access and http://pachno.local:8080 for plain http access.

Take note that provided configuration and set-up is tailored for a development environment and should *not* be used in production.

# Install vagrant and ansible
Download and install [Vagrant](https://vagrantup.com) and [Ansible](https://ansible.com) for your platform.

# Bring up the vagrant box
From the pachno root directory, run `vagrant up` to bring up the vagrant box.

# Vagrant box configuration details:
## Apache
Apache is configured with a self-signed ssl certificate on the pachno.local hostname.

## MySQL / MariaDB
MySQL (MariaDB) is configured with the username `pachno` and password `pachno`. 

### Accessing the mysql server from outside the vagrant box
The vagrant box forwards access to the mysql service on the port `13306`. To access the mysql server this way,
connect to the vagrant box (using `vagrant ssh`) and alter the mariadb configuration file:
`sudoedit /etc/mysql/mariadb.conf.d/50-server.cnf`

Change the value of the `bind_address` to `0.0.0.0` and restart the mysql service `sudo service mysql restart`.

Try connecting to the mysql server from outside the vagrant box and note down which ip address is denied access.
From inside the vagrant box (using `vagrant ssh`) connect to mariadb and add a user entry to allow connecting from your host machine:
```bash
sudo mysql
```
```mysql
CREATE USER 'root'@'10.0.2.2' IDENTIFIED BY 'password'; # replace 10.0.2.2 with your host machine ip address
GRANT ALL PRIVILEGES ON *.* TO 'root'@'10.0.2.2' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

You can now connect directly to the mysql service running in the vagrant box, via port 13306.

# Install Pachno
Pachno is not installed on the vagrant box, so you will need to follow the installation process. 
Access https://pachno.local:8443 and complete the installation.

## Debugging
The vagrant box comes with xdebug ready to use. Configure your development environment to connect to xdebug inside 
vagrant for full php debugging.

# JS or CSS changes
Javascript and css files are compiled and bundled before use. From inside the pachno main directory, first install webpack if not yet available
```shell script
npm install webpack
```
and then, run 
```shell script
npm run build
```
for one-time builds or
```shell script
npm run watch
```
for continued builds watching file changes during development.

## Running phpunit tests
By executing `composer.phar install --dev` during the installation process, phpunit will get installed. 
The phpunit tests can be run using the following command:
```
vendor/bin/phpunit
```
