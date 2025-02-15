<?php
    require_once '../DB.php';
    require_once '../../vendor/autoload.php';
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\Writer\PngWriter;

if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['id'])){
    $id = $_GET['id'];
//в qr нужно записать id и название товара, описание, цена
$productInfo = $db->query(
    "SELECT id, name, description, price FROM products WHERE id = $id
")->fetchAll()[0];

$productName = $productInfo['name'];
$productDescription = $productInfo['description'];
$productPrice = $productInfo['price'];
$qrText = "
    ИД товара : $id
    Название товара : $productName
    Описание товара : $productDescription
    Цена товара : $productPrice
";
// require 'vendor/autoload.php';
// use Endroid\QrCode\QrCode;
// use Endroid\QrCode\Writer\PngWriter;

$QrCode= new QrCode($qrText);
$writer= new PngWriter();
$result= $writer->write($QrCode);
header('Content-Type: '.$result->getMimeType());
echo $result->getString();
}
?>