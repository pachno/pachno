# Installation
Pachno is written in PHP and js for evergreen browsers. Running Pachno requires a server with php 7.4 (or newer), and a web server such as
Apache, nginx, IIS, Wamp or similar with url rewriting configured. For specific example installation instructions, see:
* [Apache](examples/apache.md)
* [nginx](examples/nginx.md)

(...missing an example? Feel free to contribute your own!)

## 1: Download and install dependencies
Pachno uses Composer for php dependencies and npm for development dependencies.
Download and install Composer into the main `pachno` directory (or a location of your choice) from http://getcomposer.org
If you want to change CSS or javascript, or other kind of development, you should download and install [NPM](https://nodejs.org).

## 2: Install dependencies
After installing Composer, run `php composer.phar install` from the 
main `pachno` directory. Composer will download and install all necessary components for Pachno, 
and you can continue to the actual installation as soon as it is completed.

### 2b: Install development dependencies
If you want to install the development dependencies, run `npm install` from the main `pachno` directory.

## 3: Run the pachno installation procedure
Pachno can be installed either via web or command line. 
Follow the installation procedure below depending on your desired installation method.

### 3a: Installation via web
Pachno should be installed via a top-level directory on your web server, with its own hostname.
Visit the configured hostname `https://pachno.example.com` in your web-browser.

The installation script will start automatically and guide you through the
installation process.

### 3b: Alternative installation via command-line (unix/linux only)

You can use the included command-line client to install Pachno.
The command line utility can be found in the root folder: `$ php ./bin/pachno`

To install Pachno use the following command:
```
$ ./bin/pachno install
```
