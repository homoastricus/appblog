<?php
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

//use App\Core\{Router, Request, App};
$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';

$yourEmail = "arturmataryan@yandex.ru"; // ваш email на яндексе
$password = "waiihqfqytutyplm"; // ваш пароль к яндексу или пароль приложения "4Goaskuns4";//lzyekvvidgtpaulw

// настройки SMTP
$mail->Mailer = 'smtp';
$mail->Host = 'ssl://smtp.yandex.ru';
$mail->SMTPDebug = 3;
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = $yourEmail; // ваш email - тот же что и в поле From:
$mail->Password = $password; // ваш пароль;


// формируем письмо

// от кого: это поле должно быть равно вашему email иначе будет ошибка
$mail->setFrom($yourEmail, 'Ваше Имя');

// кому - получатель письма
$mail->addAddress('backendspb@gmail.com', 'Имя Получателя');  // кому

$mail->Subject = 'Проверка';  // тема письма

$mail->msgHTML("<html><body>
				<h1>Проверка связи!</h1>
				<p>Это тестовое письмо.</p>
				</html></body>");


if ($mail->send()) { // отправляем письмо
    echo 'Письмо отправлено!';
} else {
    echo 'Ошибка: ' . $mail->ErrorInfo;
}
