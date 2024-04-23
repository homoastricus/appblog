<?php

use App\Core\App;
use App\Core\Database\{QueryBuilder, Connection};

$configFilePath = __DIR__ . '/../config.php';
$configExampleFilePath = __DIR__ . '/../config.php.example';

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
}
