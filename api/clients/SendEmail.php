<?php
$email = $_POST['email'];
$header = $_POST['header'];
$main = $_POST['main'];
$footer = $_POST['footer'];

// Добавляем подключение к базе данных
require_once '../DB.php';

// Получаем имя клиента из базы данных
$sql = "SELECT name FROM clients WHERE email = :email";
$stmt = $db->prepare($sql);
$stmt->execute(['email' => $email]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $userName = $result['name'];
} else {
    $userName = "Уважаемый клиент"; // Значение по умолчанию, если клиент не найден
}

$html = "
<!DOCTYPE html>
<html lang='ru'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body {
                //background-image: url('../../f.png');
                 background-image: url('../../y.jpg');
                background-size: cover;
                background-position: center;
                font-family: Arial, sans-serif;
            }
            .container {
                max-width: 700px;
                margin: 20px auto;
                padding: 20px;
                background-color: rgba(255, 255, 255, 0.9);
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                padding: 20px 0;
                border-bottom: 2px solid #ddd;
            }
            .main {
                padding: 20px 0;
            }
            .footer {
                text-align: center;
                padding: 20px 0;
                border-top: 2px solid #ddd;
                font-size: 14px;
                color: #666;
            }
        </style>       
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>$header</h2>
            </div> 
            <div class='main'>
                <p>Уважаемый(ая) $userName,</p>
                <p>$main</p>
            </div>
            <div class='footer'>
                <p>&copy; $footer</p>
            </div> 
        </div>   
    </body>
    </html>
";
$stmt = null;
$db = null;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = 'smtp.mail.ru';
$mail->SMTPAuth = true;
$mail->Username = 'dima.haunov@mail.ru';
$mail->Password = 'ikW5x1urvtS6bnm7afNp';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = 465;
// Почта отправителя
$mail->setFrom('dima.haunov@mail.ru', 'Ilchenko_Danilenko');
// Почта получателя
$mail->addAddress('matviei.maksimov@bk.ru', 'Matviei Maksimov');
$mail->isHTML(true);
$mail->Subject = 'Ильченко';
$mail->CharSet = 'UTF-8';
$mail->Body = $html;
$mail->send();
?>

