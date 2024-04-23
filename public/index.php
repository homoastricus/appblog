<?php
/*
 * Author: Artur Mataryan
 * The starting point for the framework.
 */
require '../vendor/autoload.php';
require '../core/bootstrap.php';

use App\Core\{Router, Request, App};

session_start();

//If we are not in production mode, we will display errors to the web browser.
try {
    if (!App::get('config')['options']['production']) {
        display_errors();
    }
} catch (Exception $e) {
}

//This is where we load the routes from the routes file.
try {
    Router::load('../app/routes.php')->direct(Request::uri(), Request::method());
} catch (Exception $e) {
}
