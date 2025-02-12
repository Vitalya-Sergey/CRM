<?php 

    require_once '../DB.php';
    require_once '../../vendor/autoload.php';
    use Dompdf\Dompdf;

if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['id'])){
    $orderID = $_GET['id'];
    

    $orderQuery = "SELECT o.id, o.order_date, o.total as orderTotal, c.name as clientName
    FROM orders o
    JOIN clients c ON o.client_id = c.id
    WHERE o.id = ?";

    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $orderData = $stmt->get_result()->fetch_assoc();


// Получаем товары заказа
    $itemsQuery = "SELECT p.name, oi.quantity, (oi.quantity * oi.price) as total 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?";

    $stmt = $conn->prepare($itemsQuery);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $orderItems = [];

    foreach($itemsResult as $item) {
    $orderItems[] = $item;
    }   

    $data = [
        "orderID" => $orderID,
        "orderDate" => $orderData['order_date'],
        "adminName" => $orderData['adminName'],
        "clientName" => $orderData['clientName'],
        "orderItems" => $orderItems,
        "orderTotal" => $orderData['orderTotal']
    ];

    $dompdf = new Dompdf();
    $dompdf->loadHtml('Hello world!');
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream();
    $dompdf->stream("order_$orderID.pdf");
// }
?>