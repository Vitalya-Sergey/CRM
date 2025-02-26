<?php

function ClientsSearch($params, $db){
    $search = isset($params['search']) ? $params['search'] : '';
    $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
    $sort = isset($params['sort']) ? $params['sort'] : '';
    
    // Получаем maxClients и offset из сессии
    $maxClients = isset($_SESSION['maxClients']) ? $_SESSION['maxClients'] : 5;
    $offset = isset($_SESSION['offset']) ? $_SESSION['offset'] : 0;

    // Убедитесь, что offset не отрицательный
    $offset = max(0, $offset);

    if ($sort) {
        $sort = "ORDER BY $search_name $sort";
    } 
    
    $search = trim(strtolower($search));
    $clients = $db->query(
        "SELECT * FROM clients WHERE LOWER(name) LIKE '%$search%' $sort LIMIT $maxClients OFFSET $offset
    ")->fetchAll();

    return $clients;
}
?>