<?php

namespace App\Core\Service;

class SecurityManager
{
    const password_salt = "SALT";

    const csrf_token_length = 32;

    public static function get_password_hash($pass): string
    {
        return md5(md5(self::password_salt) . $pass . md5(self::password_salt));
    }

    /*
     * // (A) START SESSION & GENERATE RANDOM TOKEN
session_start();
$_SESSION["token"] = bin2hex(random_bytes(32));

// (B) EMBED TOKEN INTO HTML FORM
<input type="hidden" name="token" value="<?=$_SESSION["token"]?>">

// (C) ON FORM SUBMIT, CHECK SUBMITTED TOKEN AGAINST SESSION
if (isset($_POST["token"]) && isset($_SESSION["token"]) && $_POST["token"]==$_SESSION["token"]) { PROCEED }
     * */

    /**
     * @throws \Exception
     */
    public function generateToken(): string
    {
        $csrf_token = bin2hex(random_bytes(self::csrf_token_length));
        SessionManager::set('csrf', $csrf_token);
        return $csrf_token;
    }

    /**
     * @param $post_csrf
     * @return bool
     */
    public function checkToken($post_csrf): bool
    {
        if (SessionManager::has('csrf') && $post_csrf == SessionManager::get('csrf')) {
            return true;
        }
        return false;
    }
}