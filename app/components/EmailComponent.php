<?php

namespace App\Components;

use App\Core\Components\Component;
use App\Core\Service\EmailManager;

class EmailComponent extends Component
{
    const default_sender = "homoastricus2011@gmail.com";
    const default_sender_name = "ADMIN";

    public static function sendEmail($email, $subject, $content): void
    {
        $mail = new EmailManager;
        // От кого.
        $mail->from(self::default_sender, self::default_sender_name);
        // Кому, можно указать несколько адресов через запятую.
        $mail->to($email, 'artur');
        // Тема письма.
        $mail->subject = $subject;
        // Текст.
        $mail->body = $content;

        // Отправка.
        $mail->send();
    }

}