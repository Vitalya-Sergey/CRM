<?php
require_once '../../api/DB.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['ticket_id']) || !isset($_POST['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$ticketId = intval($_POST['ticket_id']);
$message = trim($_POST['message']);
$userId = $_SESSION['user_id'];

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit();
}

// Insert the message
$query = "INSERT INTO tickets_message (ticket_id, user_id, message) VALUES (?, ?, ?)";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $ticketId);
$stmt->bindParam(2, $userId);
$stmt->bindParam(3, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
} 