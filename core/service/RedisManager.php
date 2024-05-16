<?php

namespace App\Core\Service;

use App\Core\App;
use Redis;

class RedisManager
{
    private $connected = false;

    public $redis = null;

    public function __construct()
    {
        $redis_port = App::get('config')['redis']['port'];
        $redis_host = App::get('config')['redis']['host'];
        $this->redis = new Redis();
        $this->redis->connect($redis_host, $redis_port);
        if (!is_null($this->redis)) {
            $this->connected = true;
        }
    }

    /**
     * @throws \RedisException
     */
    public function get($key){
        return $this->redis->get($key);
    }


}