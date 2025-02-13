<?php session_start();
require_once '../DB.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    $formData = $_POST;

    $fields = ['client','products'];
    $errors = [];

    
    $_SESSION['orders-errors']='';

    foreach($fields as $key => $field){
        if(!isset($_POST[$field]) || empty($_POST[$field])){
            $errors[$field][]= 'Field is required';
        }
    }

    if (!empty($errors)){
        $errorMessages = '<ul>';
        foreach ($errors as $key => $error) {
            $to_string = implode(',', $error);
            $errorMessages = $errorMessages . "<li>$key : $to_string </li>";
        }
        $errorMessages = $errorMessages . '</ul>';
        $_SESSION['orders-errors'] = $errorMessages;
        header('Location: ../../orders.php');
        exit;
    }

    $total = $db->query("SELECT SUM(price) FROM products 
    WHERE id IN (" . implode(',', $formData['products']) . ")")->fetchColumn();
    $token = $_SESSION['token'];
    $adminID = $db->query("SELECT id FROM users WHERE token = '$token'")->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
    $clientID = $formData['client'] === 'new' ? 
    time() :
    $formData['client'];

    if ($formData['client'] === 'new'){
        //добавить запись клиента в бд id=$clientID, email=$formData['email'], created_at само подставится а остальные поля будут пустыми
        $db->prepare(
            "INSERT INTO `clients` (`id`, `name`, `email`, `phone`) VALUES (?, ?, ?, ?)"
        )->execute([
            $clientID,
            'Новый клиент',
            $formData['email'],
            "0(000)000-00-00"
        ]);
    }

    $orders = [
        'id' => time(),
        'client_id' => $clientID,
        'total' => $total,
        'admin' => $adminID
    ];


    $db->prepare(
        "INSERT INTO `orders` (`id`, `client_id`, `total`, `admin`) VALUES (?, ?, ?, ?)"
    )->execute([
        $orders['id'],
        $orders['client_id'], 
        $orders['total'],
        $orders['admin']
    ]);
    // записать элементы заказа в orders_items order_id, product_id, quantity=1, price=цена продукта
    foreach ($formData['products'] as $key => $product) {
        $db->prepare(
            "INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES (?, ?, ?, ?)"
        )->execute([
            $orders['id'],
            $product,
            1,
            $db->query("SELECT price FROM products WHERE id = $product")->fetchColumn(),
        ]);
    }

    header('Location: ../../orders.php');

}else{
    echo json_encode(
        ["error"=> 'Неверный запрос']
    );
}

?>