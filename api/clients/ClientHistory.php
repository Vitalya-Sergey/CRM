<?php
session_start();
require_once '../DB.php';
$_SESSION['clients-errors']='';

if ($_SERVER['REQUEST_METHOD'] === 'GET' &&
isset($_GET['id'])){
    $clietID = $_GET['id'];
    $dateFROM = $_GET['from'];
    $dateTO = $_GET['to'];

    if (new DateTime($dateFROM) > new DateTime($dateTO)){
        $_SESSION['clients-errors']= 'Некорректное значение ввода даты';
        header('Location: ../../clients.php');
    }

}
?>