<?php

require_once '../DB.php';


$id = $_GET['id'];
$name = $_POST['full-name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

$request = $db->prepare("
    UPDATE `clients` 
    SET 
        `name`=:name,
        `email`=:email,
        `phone`=:phone
    WHERE `id`=:id
");

$request->bindParam(':name', $name);
$request->bindParam(':email', $email);
$request->bindParam(':phone', $phone);
$request->bindParam(':id', $id);

$request->execute();

header("Location: ../../clients.php");
?>