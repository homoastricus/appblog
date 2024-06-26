# Simple-PHP-MVC-Framework
This is a simple PHP MVC framework. it uses Composer and is designed to be very simple to use. Inspired by Laravel. 

# Requirements
* PHP >= 7.3.0
* MySQL >= 5.6.10
* Composer

# How to Install
Note: It is recommended that you install LAMP with my LAMP install script to ensure that you have all of the requirements.


Clone the repository


Change to the repository directory

```
cd Simple-PHP-MVC-Framework
```

Run composer to install any PHP dependencies

```
composer install
```

Change to the public directory

```
cd public
```

Start the PHP server (0.0.0.0 is the default route, this makes PHP listen on all IPv4 interfaces)

```
php -S 0.0.0.0:8000
```

Visit the IP address (127.0.0.1 if you are running Linux natively, or the IP address of your VM/VPS/etc) http://127.0.0.1:8000 in your web browser.

To run the included unit tests, make sure you are still in the public directory, and then type the following command

```
../vendor/bin/phpunit ../tests
```
