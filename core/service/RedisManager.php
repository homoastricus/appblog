<?php

namespace App\Core\Service;

use App\Core\App;
use Redis;
use RedisException;

class RedisManager
{

    public ?Redis $redis = null;

    /**
     * @throws RedisException
     * @throws \Exception
     */
    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(App::get('config')['redis']['host'], App::get('config')['redis']['port']);
    }

    /**
     * @throws RedisException
     */
    public function get($key){
        return $this->redis->get($key);
    }

    /**
     * @throws RedisException
     */
    public function set($key, $value): bool|Redis
    {
        return $this->redis->set($key, $value);
    }

    /**
     * @throws RedisException
     */
    public function expire($key, $lifetime): bool|Redis
    {
        return $this->redis->expire($key, $lifetime);
    }

    /**
     * @throws RedisException
     */
    public function ttl($key): bool|Redis
    {
        return $this->redis->ttl($key);
    }

    /**
     * @throws RedisException
     */
    public function __destruct()
    {
        $this->redis->close();
    }

}