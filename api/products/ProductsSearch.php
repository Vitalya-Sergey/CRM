<?php
function ProductsSearch($params, $db){
    $search = isset($params['search']) ? $params['search'] : '';
    $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
    $sort = isset($params['sort']) ? $params['sort'] : '';
    

    // Получаем maxClients и offset из сессии
    $maxProducts = isset($_SESSION['maxProducts']) ? $_SESSION['maxProducts'] : 5;
    $offset = isset($_SESSION['offset']) ? $_SESSION['offset'] : 0;

    // Убедитесь, что offset не отрицательный
    $offset = max(0, $offset);

    if ($sort) {
        $sort = "ORDER BY $search_name $sort";
    } 
    
    $search = trim(strtolower($search));
        $products = $db->query(
        "SELECT * FROM products WHERE LOWER(name) LIKE '%$search%' $sort LIMIT $maxProducts OFFSET $offset
    ")->fetchAll();
   

    return $products;
}
?>