<?php
/*
 * Author: Artur Mataryan
 * The starting point for the framework.
 */
require '../vendor/autoload.php';
require '../core/bootstrap.php';

use App\Core\{Router, Request, App};
use App\Core\Service\SessionManager;

SessionManager::init();

//If we are not in production mode, we will display errors to the web browser.
try {
    if (!App::get('config')['options']['production']) {
        display_errors();
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

//This is where we load the routes from the routes file.
try {
    $request = new Request();
    Router::load('../app/routes.php')->direct($request->uri(), $request->method(), $request);
    //print_r(get_defined_vars());
} catch (Exception $e) {
    echo $e->getMessage();
}
