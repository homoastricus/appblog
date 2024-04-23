#!/bin/bash
mkdir -p ./logs
composer update

if [[$env=="dev"]]
then
  rm -rf ./config.php
  mv ./config_dev.php ./config.php
else
   rm -rf ./config.php
   mv ./config_prod.php ./config.php
fi

echo 'Production build is success!'
