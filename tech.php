<?php session_start();

// Обработка AJAX запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    require_once 'api/DB.php';
    require_once 'api/helpers/getUserType.php';
    
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Необходима авторизация']);
        exit;
    }

    $userType = getUserType($db);
    if ($userType !== 'tech') {
        echo json_encode(['success' => false, 'message' => 'Недостаточно прав']);
        exit;
    }

    if (isset($_POST['action']) && isset($_POST['ticket_id'])) {
        $ticketId = $_POST['ticket_id'];
        $userId = $_SESSION['user_id'];

        if ($_POST['action'] === 'accept') {
            $sql = "UPDATE tickets SET status = 'В работе', admin = :admin_id WHERE id = :ticket_id AND status = 'Ожидает'";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':admin_id', $userId);
            $stmt->bindParam(':ticket_id', $ticketId);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Тикет успешно принят в работу']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при принятии тикета']);
            }
        } elseif ($_POST['action'] === 'complete') {
            $sql = "UPDATE tickets SET status = 'Выполнено' WHERE id = :ticket_id AND admin = :admin_id AND status = 'В работе'";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':admin_id', $userId);
            $stmt->bindParam(':ticket_id', $ticketId);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Тикет успешно завершен']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при завершении тикета']);
            }
        }
        exit;
    }
}

// Обычная загрузка страницы
if(isset($_GET['do']) && $_GET['do']==='logout'){
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/DB.php';
    LogoutUser('login.php', $db, $_SESSION['token']);
    exit;
}
require_once 'api/auth/AuthCheck.php';
require_once 'api/helpers/inputDefaultValue.php';
require_once 'api/helpers/selectDefaultValue.php';
AuthCheck('', 'login.php');

require 'api/DB.php';
require_once 'api/helpers/getUserType.php';
$userType = getUserType($db);
if ($userType !== 'tech') {
    header('Location: clients.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Клиенты</title>
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/clients.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
    <link rel="stylesheet" href="styles/pages/tech.css">
</head>

<body>
    <header class="header">
        <div class="container">
            <p class="header_admin">
                <?php
                require 'api/DB.php';
                require_once 'api/clients/AdminName.php';
                echo AdminName($_SESSION['token'], $db);
            
            ?></p>
            <ul class="header_link">
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <?php
                if ($userType == 'tech') {
                    echo'
                    <li><a href="tech.php">Обращения пользователей</a></li>
                    ';
                }
                ?>
            </ul>
            <a class="header_login" href="?do=logout">Выйти <i class="fa fa-sign-out" aria-hidden="true"></i>
            </a>
        </div>
    </header>
    <!-- Add Modal for notifications -->
    <div class="modal micromodal-slide" id="notification-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="notification-modal-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="notification-modal-title">
                        Уведомление
                    </h2>
                </header>
                <main class="modal__content" id="notification-modal-content">
                </main>
                <footer class="modal__footer">
                    <button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">Закрыть</button>
                </footer>
            </div>
        </div>
    </div>

    <main class="main">
    <h1 class="page-title">Обращения пользователей</h1>
    <div class="ticket-filters">
        <label for="statusFilter">Фильтр по статусу:</label>
                <select id="statusFilter" onchange="filterTickets(this.value)">
                    <option value="all">Все обращения</option>
                    <option value="Ожидает">В ожидании</option>
                    <option value="В работе">В работе</option>
                    <option value="Выполнено">Выполненные</option>
                </select>
            </div>
        <div class="container">   
            <?php if(isset($_SESSION['ticket-message'])): ?>
                <div class="alert">
                    <?php 
                        echo $_SESSION['ticket-message'];
                        unset($_SESSION['ticket-message']);
                    ?>
                </div>
            <?php endif; ?>
            
         

            <div class="tickets-container">
                <button class="scroll-button left" onclick="scrollTickets('left')">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <div class="tickets-wrapper">
                    <div class="tickets-grid">
                        <?php
                        // Fetch tickets with user and admin information
                        $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
                        
                        $sql = "SELECT t.*, 
                               creator.name as creator_name,
                               creator.surname as creator_surname,
                               admin.name as admin_name,
                               admin.surname as admin_surname
                               FROM tickets t
                               LEFT JOIN users creator ON t.client = creator.id
                               LEFT JOIN users admin ON t.admin = admin.id";
                        
                        if ($statusFilter !== 'all') {
                            $sql .= " WHERE t.status = :status";
                        }
                        
                        $sql .= " ORDER BY t.create_at DESC";
                        
                        $stmt = $db->prepare($sql);
                        if ($statusFilter !== 'all') {
                            $stmt->bindParam(':status', $statusFilter);
                        }
                        $stmt->execute();
                        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($tickets as $ticket) {
                            $ticketType = $ticket['type'] === 'tech' ? 'Технические неполадки' : 'Проблема с CRM';
                            $status = $ticket['status'];
                            $adminName = $ticket['admin_name'] ? $ticket['admin_name'] . ' ' . $ticket['admin_surname'] : 'Не назначен';
                            $creatorName = $ticket['creator_name'] . ' ' . $ticket['creator_surname'];
                            $createDate = date('d.m.Y H:i', strtotime($ticket['create_at']));
                            
                            $statusClass = '';
                            switch($status) {
                                case 'Выполнено':
                                    $statusClass = 'status-completed';
                                    break;
                                case 'В работе':
                                    $statusClass = 'status-in-progress';
                                    break;
                                default:
                                    $statusClass = 'status-waiting';
                            }
                            
                            echo "
                            <div class='ticket-card'>
                                <div class='ticket-header'>
                                    <span class='ticket-id'>Тикет #" . $ticket['id'] . "</span>
                                    <span class='ticket-status " . $statusClass . "'>" . $status . "</span>
                                </div>
                                <div class='ticket-body'>
                                    <p class='ticket-type'><strong>Тип:</strong> " . $ticketType . "</p>
                                    <p class='ticket-message'><strong>Сообщение:</strong> " . htmlspecialchars($ticket['message']) . "</p>
                                    <p class='ticket-client'><strong>Пользователь:</strong> " . htmlspecialchars($creatorName) . "</p>
                                    <p class='ticket-admin'><strong>Администратор:</strong> " . htmlspecialchars($adminName) . "</p>
                                    <p class='ticket-date'><strong>Создан:</strong> " . $createDate . "</p>
                                    <div class='ticket-actions'>";
                            if ($status === 'Ожидает') {
                                echo "<form class='ticket-form' onsubmit='handleTicketAction(event, this)'>
                                        <input type='hidden' name='ticket_id' value='" . $ticket['id'] . "'>
                                        <input type='hidden' name='action' value='accept'>
                                        <button type='submit' class='btn-accept'>Принять в работу</button>
                                    </form>";
                            } elseif ($status === 'В работе' && $ticket['admin'] == $_SESSION['user_id']) {
                                echo "<form class='ticket-form' onsubmit='handleTicketAction(event, this)'>
                                        <input type='hidden' name='ticket_id' value='" . $ticket['id'] . "'>
                                        <input type='hidden' name='action' value='complete'>
                                        <button type='submit' class='btn-complete'>Завершить</button>
                                    </form>";
                            }
                            echo "</div>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
                <button class="scroll-button right" onclick="scrollTickets('right')">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script>
        // Инициализация модального окна
        MicroModal.init();

        // Функция фильтрации тикетов
        function filterTickets(status) {
            const currentUrl = new URL(window.location.href);
            if (status === 'all') {
                currentUrl.searchParams.delete('status');
            } else {
                currentUrl.searchParams.set('status', status);
            }
            window.location.href = currentUrl.toString();
        }

        // При загрузке страницы устанавливаем значение фильтра
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status') || 'all';
            document.getElementById('statusFilter').value = status;
        });

        // Функция показа уведомления
        function showNotification(message) {
            document.getElementById('notification-modal-content').textContent = message;
            MicroModal.show('notification-modal');
        }

        // Обработка действий с тикетами
        async function handleTicketAction(event, form) {
            event.preventDefault();
            
            const formData = new FormData(form);
            formData.append('ajax', true); // Добавляем флаг AJAX запроса
            
            try {
                const response = await fetch('tech.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message);
                    // Обновляем карточку тикета
                    const ticketCard = form.closest('.ticket-card');
                    const statusSpan = ticketCard.querySelector('.ticket-status');
                    const actionsDiv = ticketCard.querySelector('.ticket-actions');
                    
                    if (formData.get('action') === 'accept') {
                        statusSpan.className = 'ticket-status status-in-progress';
                        statusSpan.textContent = 'В работе';
                        actionsDiv.innerHTML = `
                            <form class='ticket-form' onsubmit='handleTicketAction(event, this)'>
                                <input type='hidden' name='ticket_id' value='${formData.get('ticket_id')}'>
                                <input type='hidden' name='action' value='complete'>
                                <button type='submit' class='btn-complete'>Завершить</button>
                            </form>`;
                    } else if (formData.get('action') === 'complete') {
                        statusSpan.className = 'ticket-status status-completed';
                        statusSpan.textContent = 'Выполнено';
                        actionsDiv.innerHTML = '';
                    }
                } else {
                    showNotification(data.message);
                }
            } catch (error) {
                showNotification('Произошла ошибка при обработке запроса');
            }
        }

        function scrollTickets(direction) {
            const wrapper = document.querySelector('.tickets-grid');
            const cardWidth = document.querySelector('.ticket-card').offsetWidth + 24;
            const currentScroll = wrapper.style.transform ? 
                parseInt(wrapper.style.transform.replace('translateX(', '').replace('px)', '')) : 0;
            
            const scrollAmount = direction === 'right' ? 
                Math.max(currentScroll - cardWidth * 3, -cardWidth * (wrapper.children.length - 3)) :
                Math.min(currentScroll + cardWidth * 3, 0);
            
            wrapper.style.transform = `translateX(${scrollAmount}px)`;
        }
    </script>

</body>

</html>