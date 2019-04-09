# Pachno open source project management and ticket handling 

[![Build Status](https://travis-ci.org/pachno/pachno.png?branch=master)](https://travis-ci.org/pachno/pachno) 
[![Join the chat at https://gitter.im/pachno/general](https://badges.gitter.im/pachno/general.svg)](https://gitter.im/pachno/general?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Pachno is an open source, enterprise-grade system for project management, development and ticket handling. 
Main features includes:
* Gorgeous, modern interface
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

Download and install Composer from http://getcomposer.org


### 2: Install Pachno dependencies

After you have followed the instructions in step 1, run
`php composer.phar install`
from the main directory of Pachno. Composer will download and install
all necessary components for Pachno, and you can continue to the actual
installation as soon as it is completed.


### 3a: Install via web

Visit the subfolder `https://example.com/pachno/public/index.php` in your web-browser.

The installation script will start automatically and guide you through the
installation process.


### 3b: Install via command-line (unix/linux only)

You can use the included command-line client to install, if you prefer that.
The command line utility can be found in the root folder: `$ php ./bin/pachno`

To install:
`$ ./bin/pachno install`


## REPORTING ISSUES

If you find any issues, please report them in the issue tracker on our website:
https://projects.pachno.com


## RUNNING PHPUNIT TESTS

By executing `composer.phar install --dev` during the installation process, phpunit 4.2 will get installed. The phpunit test can be run by the following command:
```
vendor/bin/phpunit
```


## Development and testing using Vagrant

If you are interested in contributing some code to Pachno, you can get quickly up and running using the provided [Vagrant](https://www.vagrantup.com/) and [Ansible](https://www.ansible.com/) configuration. This can save you both time, and reduce the number of software packages you need to install and configure for working with Pachno.

Take note that provided configuration and set-up should *not* be used in production.

For more details and some introduction see [Pachno wiki page](https://projects.pachno.com/pachno/docs/Development%3AVagrant).
