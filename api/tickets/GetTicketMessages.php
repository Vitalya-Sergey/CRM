<?php
require_once '../DB.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

if (!isset($_GET['ticket_id'])) {
    http_response_code(400);
    exit('Ticket ID is required');
}

$ticketId = intval($_GET['ticket_id']);
$userId = $_SESSION['user_id'];

// Get messages for the ticket
$query = "SELECT tm.*, u.name as user_name, u.type as user_type 
          FROM tickets_message tm 
          LEFT JOIN users u ON tm.user_id = u.id 
          WHERE tm.ticket_id = ? 
          ORDER BY tm.created_at ASC";

$stmt = $db->prepare($query);
$stmt->bindParam(1, $ticketId);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    foreach ($result as $message) {
        $messageClass = $message['user_id'] == $userId ? 'sent' : 'received';
        $time = date('H:i', strtotime($message['created_at']));
        $date = date('d.m.Y', strtotime($message['created_at']));
        
        echo '<div class="chat-message ' . $messageClass . '">';
        echo '<div class="message-content">' . htmlspecialchars($message['message']) . '</div>';
        echo '<div class="message-time">';
        echo '<span class="message-sender">' . htmlspecialchars($message['user_name']) . '</span> • ';
        echo $time . ' ' . $date;
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="no-messages">Нет сообщений</div>';
} 