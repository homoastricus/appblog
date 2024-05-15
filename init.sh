#!/bin/bash
mkdir -p ./logs
composer update

if [[ $1 == dev ]]; then
    rm -rf ./config.php
    mv ./config_dev.php ./config.php
    echo 'Development build is success!'
else
    rm -rf ./config.php
    mv ./config_prod.php ./config.php
    echo 'Production build is success!'
fi
