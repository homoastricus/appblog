<?php

namespace App\Core\Service;

class EventManager
{
    private static array $events = [];

    public static function listen($name, $callback): void
    {
        self::$events[$name][] = $callback;
    }

    public static function trigger($name, $argument = null): void
    {
        foreach (self::$events[$name] as $event => $callback) {
            if($argument && is_array($argument)) {
                call_user_func_array($callback, $argument);
            }
            elseif ($argument && !is_array($argument)) {
                call_user_func($callback, $argument);
            }
            else {
                call_user_func($callback);
            }
        }
    }
}