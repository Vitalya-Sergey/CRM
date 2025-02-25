<?php
session_start();
require_once '../DB.php';
require_once '../../vendor/autoload.php';
use Dompdf\Dompdf;

$_SESSION['clients-errors']='';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])){
    $clientID = $_GET['id'];
    $dateFROM = $_GET['from'];
    $dateTO = $_GET['to'];

    if (new DateTime($dateFROM) > new DateTime($dateTO)){
        $_SESSION['clients-errors']= 'Некорректное значение ввода даты';
        header('Location: ../../clients.php');
    }
    
    $history = [
        'user' => '',
        'orders' => []
    ];

    // Получаем информацию о клиенте
    $clientQuery = "SELECT name FROM clients WHERE id = ?";
    $clientStmt = $db->prepare($clientQuery);
    $clientStmt->execute([$clientID]);
    $client = $clientStmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        $history['user'] = $client['name']; // Сохраняем ФИО пользователя
    }

    // Добавляем запрос для получения заказов по ID клиента
    $query = "SELECT * FROM orders WHERE client_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$clientID]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as $order) {
        // Запрос для получения элементов заказа
        $itemsQuery = "SELECT * FROM order_items WHERE order_id = ?";
        $itemsStmt = $db->prepare($itemsQuery);
        $itemsStmt->execute([$order['id']]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        $history['orders'][] = [
            "id" => $order['id'],
            "date" => $order['order_date'],
            "total" => $order['total'],
            "items" => $items // Добавляем элементы заказа
        ];
    }
    

    // Добавляем код для генерации чека
    $data = [
        "clientID" => $clientID,
        "orderDate" => (!empty($dateFROM) && !empty($dateTO)) ? $dateFROM . ' - ' . $dateTO : 'Все время', // если данные не выбраны, выводим "Все время"
        "orders" => $history['orders']
    ];

    $html = '
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <style>
            @font-face {
                font-family: "DejaVu Sans";
                src: url("../../fonts/DejaVuSans.ttf") format("truetype");
            }
            body {
                font-family: "DejaVu Sans", sans-serif;
            }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            .header { margin-bottom: 20px; }
            .total { font-weight: bold; text-align: right; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>История заказов клиента №' . $data['clientID'] . '</h1>
            <p>Период: ' . $data['orderDate'] . '</p>
        </div>
        <table>
            <tr>
                <th>Заказ ID</th>
                <th>Дата</th>
                <th>Сумма</th>
            </tr>';

    foreach ($data['orders'] as $order) {
        $html .= '
            <tr>
                <td>' . $order['id'] . '</td>
                <td>' . $order['date'] . '</td>
                <td>' . $order['total'] . ' руб.</td>
            </tr>';
    }

    $html .= '
        </table>
    </body>
    </html>';

    $dompdf = new Dompdf();
    $dompdf->set_option('defaultFont', 'DejaVu Sans');
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("История_заказов_клиента_№" . $data['clientID'] . ".pdf");
}

?>