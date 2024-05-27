<?php

namespace App\Components;

use App\Core\Service\EventManager;


class InformerComponent extends EventManager
{
    public static string $admin_email = "my@email";


    public static function adminNotifyEvent(string $event): void
    {
        EventManager::trigger($event, function($param){
            echo 'Event '. $param .' logout fired! <br>';
        });
        self::sendEmail(self::$admin_email);
    }

    private static function sendEmail($admin_email){

    }

}