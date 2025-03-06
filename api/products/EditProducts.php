<?php

require_once '../DB.php';


$id = $_GET['id'];
$name = $_POST['name'];
$desc = $_POST['desc'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$request = $db->prepare("
    UPDATE `products` 
    SET 
        `name`=:name,
        `description`=:desc,
        `price`=:price,
        `stock`=:stock
    WHERE `id`=:id
");

$request->bindParam(':name', $name);
$request->bindParam(':desc', $desc);
$request->bindParam(':price', $price);
$request->bindParam(':stock', $stock);
$request->bindParam(':id', $id);

$request->execute();

header("Location: ../../products.php");
?>