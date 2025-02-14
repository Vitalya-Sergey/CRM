<?php 

    require_once '../DB.php';
    require_once '../../vendor/autoload.php';
    use Dompdf\Dompdf;

if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['id'])){
    $orderID = $_GET['id'];
    

    $orderQuery = "SELECT o.id, o.order_date, o.total as orderTotal, c.name as clientName, 
    u.name as adminName
    FROM orders o
    JOIN clients c ON o.client_id = c.id
    JOIN users u ON o.admin = u.id
    WHERE o.id = ?";

    $stmt = $db->prepare($orderQuery);
    $stmt->execute([$orderID]);
    $orderData = $stmt->fetch(PDO::FETCH_ASSOC);


// Получаем товары заказа
    $itemsQuery = "SELECT p.name, oi.quantity, (oi.quantity * oi.price) as total 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?";

    $stmt = $db->prepare($itemsQuery);
    $stmt->execute([$orderID]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        "orderID" => $orderID,
        "orderDate" => $orderData['order_date'],
        "adminName" => $orderData['adminName'],
        "clientName" => $orderData['clientName'],
        "orderItems" => $orderItems,
        "orderTotal" => $orderData['orderTotal']
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
            <h1>Чек №' . $data['orderID'] . '</h1>
            <p>Дата заказа: ' . $data['orderDate'] . '</p>
            <p>Клиент: ' . $data['clientName'] . '</p>
            <p>Менеджер: ' . $data['adminName'] . '</p>
        </div>

        <table>
            <tr>
                <th>Наименование</th>
                <th>Количество</th>
                <th>Сумма</th>
            </tr>';

    foreach($data['orderItems'] as $item) {
        $html .= '
            <tr>
                <td>' . $item['name'] . '</td>
                <td>' . $item['quantity'] . '</td>
                <td>' . $item['total'] . ' руб.</td>
            </tr>';
    }

    $html .= '
        </table>
        <div class="total">
            Итого: ' . $data['orderTotal'] . ' руб.
        </div>
    </body>
    </html>';

    $dompdf = new Dompdf();
    $dompdf->set_option('defaultFont', 'DejaVu Sans');
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait'); // изменил на портретную ориентацию
    $dompdf->render();
    $dompdf->stream("Чек_№" . $data['orderID'] . ".pdf");
}
?>
