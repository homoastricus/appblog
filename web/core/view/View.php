<?php

namespace App\Core\View;

class View
{
    public static array $storage = [];

    public static array $hide_login_form = [
        ['Users', 'register']
        ];

    /*
     * This function returns the view of a page.
     */
    public static function view($controller, $name, $data = [])
    {
        extract($data);
        extract(self::$storage);
        $hide_login_form = self::showLoginForm($controller, $name);
        if ($controller == null) {
            return require "../app/views/{$name}.php";
        } else {
            $view_dir = ucfirst(strtolower($controller));
            return require "../app/views/{$view_dir}/{$name}.php";
        }
    }

    /**
     * @param $controller
     * @param $action
     * @return bool
     */
    private static function showLoginForm($controller, $action): bool
    {
        $hide = false;
        foreach (self::$hide_login_form as $item){
            if ($controller == $item[0] && $action == $item[1]) {
                $hide = true;
                break;
            }
        }
        return $hide;
    }

    public static function render($name, $value): void
    {
        self::$storage[$name] = $value;
    }
}