INSTALLATION
============

NOTE: For the latest installation notes, troubleshooting and tips,
visit the FAQ: https://projects.pachno.com/pachno/docs/FAQ

GENERAL INSTALLATION NOTES
--------------------------

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


### 3: Minify static resources

After you have followed the instructions in step 2, run
`npm install`
from the main directory of Pachno. The node package manager will
download and install all necessary components for minifying Pachno's
static resources as JavaScripts and CSS.  

After the installations finished, run
`node_modules/.bin/grunt`
You can continue to the actual installation as soon as it is completed.


### 4: (recommended) Set up a web server virtual host

If you have access to a web server setup, configure a separate virtual host for
Pachno, with the document root pointing to the public/ subfolder of the 
main pachno/ directory.

If you are using Apache, enable url rewriting (the installation routine will 
help you complete the url rewriting setup), and if you're using IIS, Nginx or
others, look at the examples included in the online documentation at
https://projects.pachno.com/pachno/docs/FAQ


### 5: Install via web

Visit the subfolder `/public/index.php` in your web-browser, or point your web
browser to the virtual host domain you set up in step 3.

The installation script will start automatically and guide you through the
installation process.


### 6: Install via command-line (unix/linux only)

You can use the included command-line client to install, if you prefer that.
Run `./bin/pachno` from this folder.

To install:
`./bin/pachno install`
