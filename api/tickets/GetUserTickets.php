<?php
session_start();
require_once '../DB.php';

header('Content-Type: text/html; charset=utf-8');

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Получаем общее количество тикетов
    $totalQuery = $db->prepare("SELECT COUNT(*) as total FROM tickets WHERE client = :user_id");
    $totalQuery->execute(['user_id' => $userId]);
    $total = $totalQuery->fetch()['total'];
    
    // Параметры пагинации
    $ticketsPerPage = 2;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $totalPages = ceil($total / $ticketsPerPage);
    
    // Проверяем корректность номера страницы
    if ($page < 1) $page = 1;
    if ($page > $totalPages) $page = $totalPages;
    
    $offset = ($page - 1) * $ticketsPerPage;
    
    // Получаем тикеты для текущей страницы
    $query = $db->prepare("
        SELECT * FROM tickets 
        WHERE client = :user_id 
        ORDER BY create_at DESC 
        LIMIT :limit OFFSET :offset
    ");
    
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':limit', $ticketsPerPage, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    
    $tickets = $query->fetchAll();
    
    if (empty($tickets)) {
        echo '<p>У вас пока нет обращений</p>';
    } else {
        echo '<div class="tickets-list">';
        foreach ($tickets as $ticket) {
            // Нормализуем статус
            $status = mb_strtolower(trim($ticket['status']));
            $statusClass = str_replace(' ', '_', $status);
            $displayStatus = mb_convert_case($status, MB_CASE_TITLE, "UTF-8");
            
            echo '<div class="ticket-item">
                    <div class="ticket-header">
                        <span class="ticket-type">' . 
                        ($ticket['type'] === 'tech' ? 'Техническая неполадка' : 'Проблема с CRM') . 
                        '</span>
                        <span class="ticket-status ' . $statusClass . '">' . $displayStatus . '</span>
                        <button class="chat-button" onclick="openChat(' . $ticket['id'] . ')" title="Открыть чат">
                            <i class="fa fa-comments"></i>
                        </button>
                    </div>
                    <div class="ticket-message">' . htmlspecialchars($ticket['message']) . '</div>
                    <div class="ticket-date">Создано: ' . date('d.m.Y H:i:s', strtotime($ticket['create_at'])) . '</div>
                </div>';
        }
        echo '</div>';
        
        // Добавляем пагинацию если есть больше одной страницы
        if ($totalPages > 1) {
            echo '<div class="tickets-pagination">';
            
            // Кнопка "Предыдущая"
            $prevDisabled = $page <= 1 ? ' disabled' : '';
            echo '<button onclick="showTickets(' . ($page - 1) . ')" 
                class="pagination-btn' . $prevDisabled . '"' . $prevDisabled . '>Предыдущая</button>';
            
            echo '<span class="pagination-info">Страница ' . $page . ' из ' . $totalPages . '</span>';
            
            // Кнопка "Следующая"
            $nextDisabled = $page >= $totalPages ? ' disabled' : '';
            echo '<button onclick="showTickets(' . ($page + 1) . ')" 
                class="pagination-btn' . $nextDisabled . '"' . $nextDisabled . '>Следующая</button>';
            
            echo '</div>';
        }
    }
} else {
    echo '<p>Необходимо авторизоваться</p>';
}
?> 