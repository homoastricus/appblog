<?php

namespace App\Core\Service;

class SessionManager
{
    public static function init(string $cacheExpire = null, string $cacheLimiter = null): void
    {
        if (session_status() === PHP_SESSION_NONE) {

            if ($cacheLimiter !== null) {
                session_cache_limiter($cacheLimiter);
            }

            if ($cacheExpire !== null) {
                session_cache_expire($cacheExpire);
            }

            session_start();
        }
    }

    /**
     * @param string $key
     * @return string|null
     */
    public static function get(string $key): ?string
    {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    public static function clear(): void
    {
        session_unset();
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }
}