<?php
/*
 * This is where configuration information is stored about the framework. We can add extra options such as the PDO error mode,
 * PDO timeout, or any other attributes that may be useful. Make sure to copy config.php.example to config.php.
 */
return [
    'database' => [
        'name' => 'appblog',
        'username' => 'root',
        'password' => '',
        'connection' => 'mysql:host=127.0.0.1',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ],
    'options' => [
        'debug' => true,
        'production' => false,
        'array_routing' => false
    ],
    'pagination' => [
        'per_page' => 5,
        'show_first_last' => true,
        'show_latest_page_on_add' => true,
        'show_latest_page_on_delete' => true,
    ],
    'redis' => [
        'port' => 6379,
        'host' => '127.0.0.1'
    ]
];
