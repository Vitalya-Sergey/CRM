<?php
session_start();
require_once '../DB.php';
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}
$userId = $_SESSION['user_id'];
$type = $_POST['type'];
$message = $_POST['message'];

$sql = "INSERT INTO tickets (type, message, client, admin) VALUES (:type, :message, :client, NULL)";
$stmt = $db->prepare($sql);
$stmt->bindParam(':type', $type);
$stmt->bindParam(':message', $message);
$stmt->bindParam(':client', $userId);
if ($stmt->execute()) {
    $_SESSION['ticket-notification'] = 'Тикет успешно создан.';
} else {
    $_SESSION['ticket-notification'] = 'Ошибка при создании тикета.';
}
header('Location: ../../clients.php');
exit();
?>
