<?php session_start();


if(isset($_GET['do']) && $_GET['do']==='logout'){
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/DB.php';
    LogoutUser('login.php', $db, $_SESSION['token']);
    exit;
}
require_once 'api/auth/AuthCheck.php';
AuthCheck('', 'login.php');
require_once 'api/helpers/inputDefaultValue.php';
require_once 'api/helpers/selectDefaultValue.php';

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
                require_once 'api/helpers/getUserType.php';
                    $userType = getUserType($db);
                if ($userType == 'tech') {
                    echo'
                    <li><a href="tech.php">Обращения пользователя</a></li>
                    ';
                }
                ?>
            </ul>
            <a class="header_login" href="?do=logout">Выйти <i class="fa fa-sign-out" aria-hidden="true"></i>
            </a>
        </div>
    </header>
    <main>
        <section class="filters">
            <div class="container">
                <form action="" method="GET" class="main__form">
                    <label for="search">Поиск по имени</label>
                    <input <?php inputDefaultValue('search', ''); ?> type="text" id="search" name="search" placeholder="Введите имя" >
                    <label for="search">Сортировка</label>
                    <select  name="search_name" id="search_name">
                        <?php $selectNameOptions = [[
                            'key' => 'name',
                            'value' => 'По имени'
                        ],[
                            'key' => 'email',
                            'value' => 'По почте'
                        ]];
                        selectDefaultValue('search_name', $selectNameOptions, 'name');
                        ?>
                    </select>

                    <label for="search">Сортировка</label>
                    <select  name="sort" id="sort">
                        <?php $selectSortOptions = [[
                            'key' => '',
                            'value' => 'По умолчанию'
                        ],[
                            'key' => 'ASC',
                            'value' => 'По возрастанию'
                        ],[
                            'key' => 'DESC',
                            'value' => 'По убыванию'
                        ]];
                        selectDefaultValue('sort', $selectSortOptions, '');
                        ?>
                    </select>
                    <button type="submit">Поиск</button>
                    <a class="search" href="?" >Сбросить</a>

                </form>
            </div>
        </section>
        <section class="clients">
            <h2 class="clients_title">Список клиентов</h2>
            <button onclick="MicroModal.show('add-modal')" class="clients_add"><i class="fa fa-plus-square fa-2x"
                    aria-hidden="true"></i></button>
            
            <div style="text-align: center;">
                <?php 
                require 'api/DB.php';
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $maxClients = 5;
                $_SESSION['maxClients'] = $maxClients;
                $offset = ($currentPage - 1) * $maxClients;
                $_SESSION['offset'] = $offset;

                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
                $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

                $totalClients =  $db -> query("
                SELECT COUNT(*) as count FROM clients WHERE LOWER(name) LIKE '%$search%'
                ")->fetchAll()[0]['count'];

                $maxPage = ceil($totalClients / $maxClients);

                // Проверка на корректность значения текущей страницы
                if ($currentPage < 1) {
                    $currentPage = 1;
                } elseif ($currentPage > $maxPage) {
                    $currentPage = $maxPage;
                }

                $prev = $currentPage - 1;
                if ($currentPage > 1) {
                    echo "  <button><a href='?page=$prev&search=".urlencode($search)."&search_name=$search_name&sort=$sort'><i class='fa fa-chevron-left' aria-hidden='true'></i></a></button>
                            ";
                } else {
                    echo "  <button style='color: gray; cursor: not-allowed;' disabled><i class='fa fa-chevron-left' aria-hidden='true'></i></button>
                            ";
                }

                for ($i=1; $i <= $maxPage; $i++){
                            
                                        if ($currentPage == $i) {
                                            echo "  <a  href='?page=$i&search=".urlencode($search)."&search_name=$search_name&sort=$sort' style='color: red; cursor: not-allowed;'>$i</a>
                                                    ";
                                        } else {
                                            echo "   <a  href='?page=$i&search=".urlencode($search)."&search_name=$search_name&sort=$sort' style='color: green;'>$i </a>
                                                    ";
                                        }
                                }
                              

                $next = $currentPage + 1;     
                if ($currentPage < $maxPage) {
                    echo "  <button><a href='?page=$next&search=".urlencode($search)."&search_name=$search_name&sort=$sort'><i class='fa fa-chevron-right' aria-hidden='true'></i></a></button>
                            ";
                } else {
                    echo "  <button style='color: gray; cursor: not-allowed;' disabled><i class='fa fa-chevron-right' aria-hidden='true'></i></button>
                            ";
                }
                // echo "  <p>$currentPage / $maxPage</p> ";

                  ?>
                
            </div>
            
            <div class="container">
                <table>
                    <thead>
                        <th>ИД</th>
                        <th>ФИО</th>
                        <th>Почта</th>
                        <th>Телефон</th>
                        <th>День рождения</th>
                        <th>Дата создания</th>
                        <th>История заказов</th>
                        <th>Редактировать</th>
                        <th>Удалить</th>
                    </thead>
                    <tbody>
                        <?php
                        require 'api/DB.php';
                        require_once 'api/clients/OutputClients.php';
                        require_once 'api/clients/ClientsSearch.php';

                        $clients = ClientsSearch($_GET, $db);
                        OutputClients($clients);
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div class="modal micromodal-slide" id="add-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Добавить клиента
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form action="api/clients/AddClients.php" method="POST" id="registration-form">
                        <label for="full-name">ФИО:</label>
                        <input type="text" id="full-name" name="full-name" >

                        <label for="email">Почта:</label>
                        <input type="email" id="email" name="email" >

                        <label for="phone">Телефон:</label>
                        <input type="tel" id="phone" name="phone" >

                        <label for="birth-date">День рождения:</label>
                        <input type="date" id="birth-date" name="birth-date" >

                        <button class="create" type="submit">Создать</button>
                        <button onclick="MicroModal.close('add-modal')" class="cancel" type="button">Отмена</button>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide" id="delete-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Вы уверены, что хотите удалить клиента?
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form id="registration-form">
                        <button class="cancel" type="submit">Удалить</button>
                        <button onclick="MicroModal.close('delete-modal')" class="create" type="button">Отмена</button>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide <?php
        if (isset($_GET['edit-user']) && !empty($_GET['edit-user'])) {
            echo 'open';
        }
        ?>"  id="edit-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>

            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Редактировать клиента
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
<?php
if (isset($_GET['edit-user']) && !empty($_GET['edit-user'])) {
    $userId = $_GET['edit-user'];

    $client = $db->query(
        "SELECT * FROM clients WHERE id = $userId
    ")->fetchAll()[0];

    if ($client) {
        $clientName = $client['name'];
        $clientEmail = $client['email']; 
        $clientPhone = $client['phone'];
    }
}
?>

<?php
if (isset($_GET['edit-user']) && !empty($_GET['edit-user'])) {
    echo " <form method='POST' action='api/clients/EditClients.php?id=$userId'>
    <label for='full-name'>ФИО:</label>
    <input type='text' id='full-name' name='full-name' value='$clientName'>

    <label for='email'>Почта:</label>
    <input type='email' id='email' name='email' value='$clientEmail'>

    <label for='phone'>Телефон:</label>
    <input type='tel' id='phone' name='phone' value='$clientPhone'>

    <button class='create' type='submit'>Редактировать</button>
    <button type='button' class='cancel' data-micromodal-close>Отмена</button>
</form>";
}
?>
                    
            </main>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide" id="history-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        История заказов
                    </h2>
                    <small> Фамилия Имя Отчество</small>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form id="registration-form">
                        <div class="order">
                            <div class="order_info">
                                <h3 class="order_number">Заказ №1</h3>
                                <time class="order_date">Дата оформления : 2025-01-13 09:25:30</time>
                                <p class="order_total">Общая сумма: 300.00</p>
                            </div>
                            <table class="order_items">
                                <tr>
                                    <th>ИД</th>
                                    <th>Название товара</th>
                                    <th>Количество</th>
                                    <th>Цена</th>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Футболка</td>
                                    <td>10</td>
                                    <td>10000</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Футболка</td>
                                    <td>5</td>
                                    <td>5000</td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>
    
    <div class="modal micromodal-slide <?php
    //проверить $_SESSION['clients-errors']
    //на существование и пустоту
    //существует и не пустой  echo 'open'
    if (isset($_SESSION['clients-errors']) && !empty($_SESSION['clients-errors'])) {
        echo 'open';
    }
    ?>" id="error-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Ошибка
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <?php
                    if (isset($_SESSION['clients-errors']) && !empty($_SESSION['clients-errors'])) {
                        echo $_SESSION['clients-errors'];
                        $_SESSION['clients-errors']="";
                    }
                    ?>
                </main>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide <?php
    if (isset($_GET['send-email']) && !empty($_GET['send-email'])) {
        echo 'open';
    }
    ?>" id="send-email-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Рассылка
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <?php
                    if (isset($_GET['send-email']) && !empty($_GET['send-email'])) {
                        echo '<form method="POST" action="api/clients/SendEmail.php?email=$email">
                                <input type="hidden" name="email" value="'.$_GET['send-email'].'">
                                
                                <label for="header">Заголовок письма:</label>
                                <input type="text" id="header" name="header" required>
                                
                                <label for="main">Основной текст:</label>
                                <textarea id="main" name="main" rows="4" required style="border: 1px solid #ccc; border-radius: 4px; padding: 8px;"></textarea>
                                
                                <label for="footer">Подпись письма:</label>
                                <input type="text" id="footer" name="footer" required>
                                
                                <div class="button-group">
                                    <button type="submit" class="create">Отправить</button>
                                    <button type="button" class="cancel" data-micromodal-close>Отмена</button>
                                </div>
                            </form>';
                    }
                    ?>
                </main>
            </div>
        </div>
    </div>



<div class="support-button" onclick="toggleSupportForm()">
    <i class="fa fa-question-circle"></i>
    Поддержка
</div>

<div class="support-create-tickets" id="supportForm">
    <div class="support-header">
        <h3>Создать тикет</h3>
        <span class="close-button" onclick="toggleSupportForm()">&times;</span>
    </div>
    <form action="api/tickets/CreateTicket.php" method="POST">
        <div class="form-group">
            <label for="type">Тип обращения</label>
            <select name="type" id="type">
                <option value="tech">Техническая неполадка</option>
                <option value="crm">Проблема с CRM</option>
            </select>
        </div>
        <div class="form-group">
            <label for="message">Текст обращения</label>  
            <textarea name="message" id="message" placeholder="Опишите вашу проблему..."></textarea>
        </div>
        <div class="form-group">
            <label for="files" class="file-label">
                <i class="fa fa-paperclip"></i> Прикрепить файл
            </label>
            <input type="file" name="files" id="files">
        </div>
        <button type="submit">Создать тикет</button>
        <button type="button" class="my-tickets-btn" onclick="showTickets()">Мои обращения</button>
    </form>
</div>

<!-- Модальное окно для списка обращений -->
<div class="modal micromodal-slide" id="tickets-list-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
                <h2 class="modal__title">Мои обращения</h2>
                <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="tickets-list-content">
                <!-- Здесь будет список обращений -->
            </main>
        </div>
    </div>
</div>

<div class="modal micromodal-slide" id="chat-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
                <h2 class="modal__title">Чат обращения #<span id="chat-ticket-id"></span></h2>
                <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content">
                <div class="chat-messages" id="chat-messages">
                    <!-- Messages will be loaded here -->
                </div>
                <form id="chat-form" class="chat-form">
                    <input type="text" id="chat-input" placeholder="Введите сообщение...">
                    <button type="submit" class="send-message">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            </main>
        </div>
    </div>
</div>

<?php
if (isset($_SESSION['token'])) {
    $userIdQuery = $db->prepare("SELECT id FROM users WHERE token = :token");
    $userIdQuery->execute(['token' => $_SESSION['token']]);
    $user = $userIdQuery->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
    }
}
?>


<script>
function toggleSupportForm() {
    const form = document.getElementById('supportForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function showTickets(page = 1) {
    fetch(`api/tickets/GetUserTickets.php?page=${page}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('tickets-list-content').innerHTML = html;
            MicroModal.show('tickets-list-modal');
        })
        .catch(error => {
            console.error('Ошибка при загрузке тикетов:', error);
            document.getElementById('tickets-list-content').innerHTML = '<p>Произошла ошибка при загрузке тикетов</p>';
        });
}

function openChat(ticketId) {
    // Prevent modal close
    event.stopPropagation();
    
    // Update URL with ticket ID
    const url = new URL(window.location.href);
    url.searchParams.set('msg', ticketId);
    window.history.pushState({}, '', url);
    
    // Set ticket ID in chat modal
    document.getElementById('chat-ticket-id').textContent = ticketId;
    
    // Load messages
    loadChatMessages(ticketId);
    
    // Show chat modal
    MicroModal.show('chat-modal');
}

function loadChatMessages(ticketId) {
    fetch(`api/tickets/GetTicketMessages.php?ticket_id=${ticketId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('chat-messages').innerHTML = html;
            scrollToBottom();
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            document.getElementById('chat-messages').innerHTML = '<p>Ошибка при загрузке сообщений</p>';
        });
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Handle message submission
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    const ticketId = document.getElementById('chat-ticket-id').textContent;
    
    if (message) {
        fetch('api/tickets/SendMessage.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `ticket_id=${ticketId}&message=${encodeURIComponent(message)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadChatMessages(ticketId);
            }
        });
    }
});

// Auto-load chat if msg parameter exists
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const msgParam = urlParams.get('msg');
    if (msgParam) {
        openChat(msgParam);
    }
    
    // Initialize MicroModal
    MicroModal.init({
        onClose: modal => {
            // Clear content when closing tickets list modal
            if (modal.id === 'tickets-list-modal') {
                document.getElementById('tickets-list-content').innerHTML = '';
            }
        }
    });
});

</script>

<script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
<script defer src="scripts/initClientsModal.js"></script>

</body>

</html>