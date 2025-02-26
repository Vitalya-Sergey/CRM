<?php
session_start();
require_once '../DB.php';
require_once '../../vendor/autoload.php';
use Dompdf\Dompdf;
require_once '../helpers/convertDate.php'; // Подключаем файл с функциями



if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])){
    $clientID = $_GET['id'];
    $dateFROM = $_GET['from'] ?? null; // Устанавливаем значение по умолчанию
    $dateTO = $_GET['to'] ?? null; // Устанавливаем значение по умолчанию
    $_SESSION['clients-errors']='';
    
    // Проверяем, указаны ли даты
    if ($dateFROM && $dateTO && new DateTime($dateFROM) > new DateTime($dateTO)) {
        $_SESSION['clients-errors'] = 'Некорректное значение ввода даты';
        header('Location: ../../clients.php');
        exit; // Завершаем выполнение скрипта
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

    // Добавляем запрос для получения заказов по ID клиента в указанный период
    $query = "SELECT * FROM orders WHERE client_id = ?";
    $params = [$clientID];

    // Если даты указаны, добавляем условия для выборки
    if ($dateFROM && $dateTO) {
        $query .= " AND order_date BETWEEN ? AND ?";
        $params[] = $dateFROM;
        $params[] = $dateTO;
    }

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Проверяем, есть ли заказы в указанном периоде
    if (empty($orders)) {
        $_SESSION['clients-errors'] = 'В этот период данный клиент ничего не заказывал';
        header('Location: ../../clients.php');
        exit; // Завершаем выполнение скрипта
    }

    foreach ($orders as $order) {
        // Запрос для получения элементов заказа
        $itemsQuery = "SELECT oi.id, oi.quantity, oi.price, p.name 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?";
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
        "orderDate" => (!empty($dateFROM) && !empty($dateTO)) ? convertDate($dateFROM) . ' - ' . convertDate($dateTO) : 'Все время', // Применяем функцию convertDate
        "orders" => []
    ];

    // Получаем заказы и элементы заказа
    foreach ($history['orders'] as $order) {
        // Запрос для получения элементов заказа с данными из таблицы products
        $itemsQuery = "
            SELECT oi.id, oi.quantity, oi.price, p.name 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?";
        
        $itemsStmt = $db->prepare($itemsQuery);
        $itemsStmt->execute([$order['id']]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Добавляем заказ и его элементы в массив $data
        $data['orders'][] = [
            'id' => $order['id'],
            'date' => $order['date'],
            'total' => $order['total'],
            'items' => $items // Добавляем элементы заказа
        ];
    }

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
        </div>';

    foreach ($data['orders'] as $order) {
        $html .= '
            <h3 class="order_number">Заказ №' . $order['id'] . '</h3>
            <time class="order_date">Дата оформления : ' . convertDateTime($order['date']) . '</time>
            <table>
                <tr>
                    <th>Название товаров</th>
                    <th>Количество</th>
                    <th>Цена</th>
                </tr>';

        // Перебираем элементы заказа
        $totalPrice = 0; // Инициализируем переменную для подсчета общей суммы
        foreach ($order['items'] as $item) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($item['name']) . '</td>
                    <td>' . htmlspecialchars($item['quantity']) . '</td>
                    <td>' . htmlspecialchars($item['price']) . ' руб.</td>
                </tr>';
            $totalPrice += $item['price'] * $item['quantity']; // Считаем общую сумму
        }

        // Добавляем строку с итоговой суммой
        $html .= '
                <tr>
                    <td colspan="2" class="total">Итого:</td>
                    <td class="total">' . htmlspecialchars($totalPrice) . ' руб.</td>
                </tr>';

        $html .= '
            </table>';
    }

    $html .= '
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