<?php

use App\Core\App;
use App\Core\Database\{QueryBuilder, Connection};

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
    define('ROOT',  __DIR__ . DIRECTORY_SEPARATOR . '..');
}
/**
 * Path to the temporary file's directory.
 */
if (!defined('TMP')) {
    define('TMP', __DIR__ .DS . 'tmp');
}

$configFilePath = __DIR__ . '/../config.php';
$configExampleFilePath = __DIR__ . '/../config.php.example';
const APP_DIR = __DIR__ . '/../app';
const VIEW_DIR = APP_DIR . DS . "views";

if (!file_exists($configFilePath) && file_exists($configExampleFilePath)) {
    copy($configExampleFilePath, $configFilePath);
}

require 'helpers.php';

App::bind('config', require $configFilePath);

try {
    App::bind('database', new QueryBuilder(
        Connection::make(App::get('config')['database'])
    ));
} catch (Exception $e) {
    echo $e->getMessage();
}
