# Pachno open source project management and ticket handling 

[![Build Status](https://travis-ci.org/pachno/pachno.png?branch=master)](https://travis-ci.org/pachno/pachno) 
[![Join the chat at https://gitter.im/pachno/general](https://badges.gitter.im/pachno/general.svg)](https://gitter.im/pachno/general?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Pachno is free and open source system for project management, development and ticket handling. 
Main features includes:
* Responsive, modern interface
* Integrated wiki and docs
* Interactive project planning
* Project Kanban, Scrum and generic planning boards
* Fully customizable workflow
* Built-in time tracking
* Complete source code integration
* LDAP authentication, OAuth2-enabled logins and pluggable auth backend
* Remote API (JSON-based)
* Great web-based configuration
* Multiple hosted installations on single setups
* Command-line interface for both local and remote installations
* Module-based and extensible architecture

... and a lot more!

For up-to-date installation and setup notes, visit the FAQ:
https://projects.pachno.com/pachno/docs/FAQ


## Installation

### 1: Download and install Composer

Pachno uses a dependency resolution tool called Composer, which must
be downloaded and run before Pachno can be installed or used.

Download and install Composer into the main `pachno` directory from http://getcomposer.org


### 2: Install Pachno dependencies

After you have followed the instructions in step 1, run `php composer.phar install` from the 
main `pachno` directory. Composer will download and install all necessary components for Pachno, 
and you can continue to the actual installation as soon as it is completed.


### 3a: Install via web

Visit the subfolder `https://example.com/pachno/public/index.php` in your web-browser.

The installation script will start automatically and guide you through the
installation process.


### 3b: Install via command-line (unix/linux only)

You can use the included command-line client to install Pachno.
The command line utility can be found in the root folder: `$ php ./bin/pachno`

To install Pachno use the following command:
```
$ ./bin/pachno install
```


## REPORTING ISSUES

If you find any issues, please report them in the issue tracker on our website:
https://projects.pachno.com


## RUNNING PHPUNIT TESTS

By executing `composer.phar install --dev` during the installation process, phpunit 4.2 will get installed. 
The phpunit tests can be run using the following command:
```
vendor/bin/phpunit
```


## Development and testing using Vagrant

If you want to write code for Pachno, either to test out extensions or contribute bug-fixes or features, 
you can get up and running quickly using the provided [Vagrant](https://www.vagrantup.com/) and [Ansible](https://www.ansible.com/) configuration. 

Take note that provided configuration and set-up is tailored for a development environment and should *not* be used in production.

For more information see [Pachno wiki page](https://projects.pachno.com/pachno/docs/Development%3AVagrant).
