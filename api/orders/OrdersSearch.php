<?php
function OrdersSearch($params, $db){
    $search = isset($params['search']) ? $params['search'] : '';
    $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
    $sort = isset($params['sort']) ? $params['sort'] : '';
    $status = isset($params['checkbox']) ? $params['checkbox'] : '';

    $maxOrders = isset($_SESSION['maxOrders']) ? $_SESSION['maxOrders'] : 5;
    $offset = isset($_SESSION['offset']) ? $_SESSION['offset'] : 0;

    // Убедитесь, что offset не отрицательный
    $offset = max(0, $offset);

    if ($sort) { 
        $sort = "ORDER BY $search_name $sort";
    } 
    $search = trim(strtolower($search));


    $orders = $db->query(
        "SELECT orders.id, clients.name, orders.order_date, orders.total, orders.status, users.name as admin_name,
        GROUP_CONCAT(CONCAT(products.name, ' : ', order_items.price, ' : ', order_items.quantity, ' кол.') SEPARATOR ', ') AS product_names
        FROM orders 
        JOIN clients ON orders.client_id = clients.id 
        JOIN order_items ON orders.id = order_items.order_id 
        JOIN products ON order_items.product_id = products.id 
        JOIN users ON orders.admin = users.id 
        WHERE (LOWER(clients.name) LIKE '%$search%' OR LOWER(products.name) LIKE '%$search%') $status
        GROUP BY orders.id, clients.name, orders.order_date, orders.total 
        $sort LIMIT $maxOrders OFFSET $offset
        ")->fetchAll();
   
    return $orders;
}
?>