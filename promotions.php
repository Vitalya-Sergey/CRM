<?php session_start();

if (isset($_GET['do']) && $_GET['do'] === 'logout') {
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/DB.php';

    LogoutUser('login.php', $db, $_SESSION['token']);

    exit;
}

require_once 'api/auth/AuthCheck.php';
require_once 'api/helpers/InputDefaultValue.php';

AuthCheck('', 'login.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Акции</title>
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/clients.css">
    <link rel="stylesheet" href="styles/pages/promotions.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <p class="header_admin">
                <?php 
                    require 'api/DB.php';
                    require_once 'api/clients/AdminName.php';
                    require_once 'api/helpers/getUserType.php';

                    echo AdminName($_SESSION['token'], $db);
                    $userType = getUserType($db);
                    
                ?>
            </p>
            <ul class="header_link">
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <li><a href="promotions.php">Акции</a></li>
                <?php
                    if ($userType === 'tech') {
                        echo '<li><a href="tech.php">Обращение пользователя</a></li>';
                    }
                ?>
            </ul>
            <a href="?do=logout" class="header_login">Выйти <i class="fa fa-sign-out" aria-hidden="true"></i></a>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <div class="promotions-header">
                <h2 class="main__clients__title">Список акций</h2>
                <div class="main__clients__controls">
                    <button class="main__clients__add" onclick="MicroModal.show('add-promotion-modal')">
                        <i class="fa fa-plus-circle"></i>
                    </button>
                </div>
            </div>
            
            <div style="text-align: center;">
                <?php 
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $maxPromotions = 4;
                $_SESSION['maxPromotions'] = $maxPromotions;
                $offset = ($currentPage - 1) * $maxPromotions;
                $_SESSION['offset'] = $offset;

                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
                $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

                $totalPromotions = $db->query("SELECT COUNT(*) as count FROM promotions")->fetchAll()[0]['count'];

                $maxPage = ceil($totalPromotions / $maxPromotions);

                // Проверка на корректность значения текущей страницы
                if ($currentPage < 1) {
                    $currentPage = 1;
                } elseif ($currentPage > $maxPage) {
                    $currentPage = $maxPage;
                }

                $prev = $currentPage - 1;
                if ($currentPage > 1) {
                    echo "<button><a href='?page=$prev&search=".urlencode($search)."&search_name=$search_name&sort=$sort'><i class='fa fa-chevron-left' aria-hidden='true'></i></a></button>";
                } else {
                    echo "<button style='color: gray; cursor: not-allowed;' disabled><i class='fa fa-chevron-left' aria-hidden='true'></i></button>";
                }

                for ($i = 1; $i <= $maxPage; $i++) {
                    if ($currentPage == $i) {
                        echo "<a href='?page=$i&search=".urlencode($search)."&search_name=$search_name&sort=$sort' style='color: red; cursor: not-allowed;'>$i</a>";
                    } else {
                        echo "<a href='?page=$i&search=".urlencode($search)."&search_name=$search_name&sort=$sort' style='color: green;'>$i</a>";
                    }
                }

                $next = $currentPage + 1;     
                if ($currentPage < $maxPage) {
                    echo "<button><a href='?page=$next&search=".urlencode($search)."&search_name=$search_name&sort=$sort'><i class='fa fa-chevron-right' aria-hidden='true'></i></a></button>";
                } else {
                    echo "<button style='color: gray; cursor: not-allowed;' disabled><i class='fa fa-chevron-right' aria-hidden='true'></i></button>";
                }
                ?>
            </div>

            <div class="promotions-container">
        <?php
                // Получаем акции с учетом пагинации
                $query = "SELECT * FROM promotions ORDER BY created_at DESC LIMIT $maxPromotions OFFSET $offset";
                $promotions = $db->query($query)->fetchAll();
                
                foreach ($promotions as $promo) {
                    // Изменяем логику определения пути к изображению
                    $imagePath = !empty($promo['path_to_image']) 
                        ? 'uploads/promotions/' . $promo['path_to_image'] 
                        : 'images/spring-banner.jpg';
                    
                    // Проверяем существование файла
                    if (!file_exists($imagePath)) {
                        $imagePath = 'images/spring-banner.jpg'; // Fallback на дефолтное изображение
                    }
                    
                    $usesInfo = $promo['uses'] . '/' . $promo['max_uses'];
                    $startDate = date('d.m.Y', strtotime($promo['created_at']));
                    $endDate = date('d.m.Y', strtotime($promo['cancel_at']));
                    $isActive = strtotime($promo['cancel_at']) > time() && $promo['uses'] < $promo['max_uses'];
                    $statusClass = $isActive ? 'promotion-active' : 'promotion-inactive';
                    $statusText = $isActive ? 'Активна' : 'Неактивна';

                    echo "
                    <div class='promotion-card $statusClass'>
                        <div class='promotion-status'>$statusText</div>
                        <div class='promotion-image'>
                            <img src='$imagePath' alt='{$promo['title']}'>
                        </div>
                        <div class='promotion-content'>
                            <h3 class='promotion-title'>{$promo['title']}</h3>
                            <div class='promotion-body'>{$promo['body']}</div>
                            <div class='promotion-info'>
                                <div class='promotion-promo'>
                                    <span>Промокод: </span>
                                    <span class='promo-code'>{$promo['code_promo']}</span>
                                    <button class='copy-btn' data-promo='{$promo['code_promo']}'>
                                        <i class='fa fa-copy'></i>
                                    </button>
                                </div>
                                <div class='promotion-discount'>Скидка: {$promo['discount']}%</div>
                                <div class='promotion-uses'>Использований: $usesInfo</div>
                                <div class='promotion-dates'>Период: $startDate - $endDate</div>
                        </div>
                            <div class='promotion-actions'>
                                <button class='edit-promotion-btn' 
                                        onclick='editPromotion({$promo['id']})'>
                                    <i class='fa fa-pencil'></i> Редактировать
                            </button>
                                <button class='delete-promotion-btn' 
                                        onclick='deletePromotion({$promo['id']})'>
                                    <i class='fa fa-trash'></i> Удалить
                            </button>
                            </div>
                        </div>
                        </div>";
                    }
                    
                if (count($promotions) === 0) {
                    echo "<div class='no-promotions'>Акции не найдены. Создайте новую акцию!</div>";
                }
            ?>
        </div>
        </div>
    </main>

    <!-- Модальное окно для добавления акции -->
    <div class="modal micromodal-slide" id="add-promotion-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-title">
                        Создать акцию
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <form action="api/promotions/AddPromotion.php" method="POST" enctype="multipart/form-data" class="modal__form">
                        <div class="modal__form-group">
                            <label for="promo-title">Заголовок</label>
                            <input type="text" id="promo-title" name="title" required>
                        </div>
                        <div class="modal__form-group">
                            <label for="promo-body">Описание</label>
                            <textarea id="promo-body" name="body" rows="4" required></textarea>
                        </div>
                        <div class="modal__form-group">
                            <label for="promo-image">Изображение</label>
                            <input type="file" id="promo-image" name="image" accept="image/*">
                        </div>
                        <div class="modal__form-group">
                            <label for="promo-code">Промокод</label>
                            <div class="promo-code-container">
                                <input type="text" id="promo-code" name="code_promo" required>
                                <button type="button" id="generate-promo-btn">Сгенерировать</button>
                            </div>
                        </div>
                        <div class="modal__form-group">
                            <label for="promo-discount">Скидка (%)</label>
                            <input type="number" id="promo-discount" name="discount" min="1" max="100" required>
                        </div>
                        <div class="modal__form-group">
                            <label for="promo-max-uses">Максимальное количество использований</label>
                            <input type="number" id="promo-max-uses" name="max_uses" min="1" required>
                        </div>
                        <div class="modal__form-group">
                            <label for="promo-cancel-date">Дата окончания</label>
                            <input type="date" id="promo-cancel-date" name="cancel_at" required>
                        </div>
                        <div class="modal__form-actions">
                            <button type="submit" class="modal__btn modal__btn-primary">Создать</button>
                            <button type="button" class="modal__btn modal__btn-secondary" data-micromodal-close>Отменить</button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования акции -->
    <div class="modal micromodal-slide" id="edit-promotion-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">
            <header class="modal__header">
                    <h2 class="modal__title" id="edit-modal-title">
                        Редактировать акцию
              </h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
                <main class="modal__content">
                    <form action="api/promotions/EditPromotion.php" method="POST" enctype="multipart/form-data" class="modal__form">
                        <input type="hidden" id="edit-promo-id" name="id">
                        <div class="modal__form-group">
                            <label for="edit-promo-title">Заголовок</label>
                            <input type="text" id="edit-promo-title" name="title" required>
                        </div>
                        <div class="modal__form-group">
                            <label for="edit-promo-body">Описание</label>
                            <textarea id="edit-promo-body" name="body" rows="4" required></textarea>
                        </div>
                        <div class="modal__form-group">
                            <label for="edit-promo-image">Изображение</label>
                            <div class="current-image-container">
                                <img id="current-promo-image" src="" alt="Текущее изображение">
                            </div>
                            <input type="file" id="edit-promo-image" name="image" accept="image/*">
                            <input type="hidden" id="edit-current-image" name="current_image">
                        </div>
                        <div class="modal__form-group">
                            <label for="edit-promo-code">Промокод</label>
                            <div class="promo-code-container">
                                <input type="text" id="edit-promo-code" name="code_promo" required>
                                <button type="button" id="edit-generate-promo-btn">Сгенерировать</button>
                            </div>
                        </div>
                        <div class="modal__form-group">
                            <label for="edit-promo-discount">Скидка (%)</label>
                            <input type="number" id="edit-promo-discount" name="discount" min="1" max="100" required>
                        </div>
                        <div class="modal__form-group">
                            <label for="edit-promo-max-uses">Максимальное количество использований</label>
                            <input type="number" id="edit-promo-max-uses" name="max_uses" min="1" required>
                        </div>
                        <div class="modal__form-group">
                            <label for="edit-promo-cancel-date">Дата окончания</label>
                            <input type="date" id="edit-promo-cancel-date" name="cancel_at" required>
                        </div>
                        <div class="modal__form-actions">
                            <button type="submit" class="modal__btn modal__btn-primary">Сохранить</button>
                            <button type="button" class="modal__btn modal__btn-secondary" data-micromodal-close>Отменить</button>
                        </div>
                    </form>
            </main>
          </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div class="modal micromodal-slide" id="delete-promotion-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="delete-modal-title">
                        Удалить акцию?
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <p>Вы уверены, что хотите удалить эту акцию? Это действие нельзя отменить.</p>
                    <form id="registration-form">
                        <input type="hidden" id="delete-promo-id" name="id">
                        <div class="modal__form-actions" style="display: flex; gap: 10px; justify-content: center;">
                            <button class="cancel" type="submit">Удалить</button>
                            <button onclick="MicroModal.close('delete-promotion-modal')" class="create" type="button">Отмена</button>
                        </div>
                    </form>
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

    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
            // Инициализация модальных окон
        MicroModal.init({
            disableScroll: true,
            awaitOpenAnimation: false,
                awaitCloseAnimation: false
            });
            
            // Функция для генерации случайного промокода
            function generatePromoCode() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let promoCode = '';
                for (let i = 0; i < 8; i++) {
                    promoCode += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return promoCode;
            }
            
            // Обработчик для кнопки генерации промокода (добавление)
            document.getElementById('generate-promo-btn').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('promo-code').value = generatePromoCode();
            });
            
            // Обработчик для кнопки генерации промокода (редактирование)
            document.getElementById('edit-generate-promo-btn').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('edit-promo-code').value = generatePromoCode();
            });
            
            // Устанавливаем минимальную дату окончания акции как сегодня
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('promo-cancel-date').min = today;
            document.getElementById('edit-promo-cancel-date').min = today;
            
            // Установка значения по умолчанию для даты окончания (сегодня + 30 дней)
            const defaultDate = new Date();
            defaultDate.setDate(defaultDate.getDate() + 30);
            const defaultDateString = defaultDate.toISOString().split('T')[0];
            document.getElementById('promo-cancel-date').value = defaultDateString;
            
            // Копирование промокода в буфер обмена
            document.querySelectorAll('.copy-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const promoCode = this.getAttribute('data-promo');
                    navigator.clipboard.writeText(promoCode).then(() => {
                        // Временно меняем иконку для подтверждения
                        const icon = this.querySelector('i');
                        icon.classList.remove('fa-copy');
                        icon.classList.add('fa-check');
                        setTimeout(() => {
                            icon.classList.remove('fa-check');
                            icon.classList.add('fa-copy');
                        }, 1500);
                    });
                });
            });
        });
        
        // Функция для редактирования акции
        function editPromotion(id) {
            fetch(`api/promotions/GetPromotion.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // Показываем уведомление об ошибке
                        showNotification(data.error, 'error');
                        return;
                    }
                    
                    document.getElementById('edit-promo-id').value = data.id;
                    document.getElementById('edit-promo-title').value = data.title;
                    document.getElementById('edit-promo-body').value = data.body;
                    document.getElementById('edit-promo-code').value = data.code_promo;
                    document.getElementById('edit-promo-discount').value = data.discount;
                    document.getElementById('edit-promo-max-uses').value = data.max_uses;
                    
                    // Преобразовать дату в формат YYYY-MM-DD для input type="date"
                    let formattedDate;
                    try {
                        const cancelDate = new Date(data.cancel_at);
                        // Проверим, является ли дата действительной
                        if (isNaN(cancelDate.getTime())) {
                            // Если дата недействительна, используем текущую дату + 30 дней
                            const defaultDate = new Date();
                            defaultDate.setDate(defaultDate.getDate() + 30);
                            formattedDate = defaultDate.toISOString().split('T')[0];
                            console.warn('Неверный формат даты в базе данных, установлена дата по умолчанию');
                        } else {
                            formattedDate = cancelDate.toISOString().split('T')[0];
                        }
                    } catch (e) {
                        // В случае ошибки используем текущую дату + 30 дней
                        const defaultDate = new Date();
                        defaultDate.setDate(defaultDate.getDate() + 30);
                        formattedDate = defaultDate.toISOString().split('T')[0];
                        console.error('Ошибка при обработке даты:', e);
                    }
                    
            document.getElementById('edit-promo-cancel-date').value = formattedDate;
            
            // Отображаем текущее изображение
            if (data.path_to_image) {
                document.getElementById('current-promo-image').src = data.path_to_image;
                document.getElementById('current-promo-image').style.display = 'block';
                document.getElementById('edit-current-image').value = data.path_to_image;
                    } else {
                document.getElementById('current-promo-image').style.display = 'none';
                    }
            
            MicroModal.show('edit-promotion-modal');
                })
                .catch(error => {
            console.error('Ошибка при загрузке данных акции:', error);
            showNotification('Не удалось загрузить данные акции. Проверьте консоль для подробностей.', 'error');
        });
    }
        
        // Функция для удаления акции
        function deletePromotion(id) {
            document.getElementById('delete-promo-id').value = id;
            MicroModal.show('delete-promotion-modal');
        }
        
        // Обработка уведомлений
        document.addEventListener('DOMContentLoaded', function() {
            // Показываем уведомление об успешной операции
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                const successType = urlParams.get('success');
                let message = '';
                
                switch(successType) {
                    case '1':
                        message = 'Акция успешно создана!';
                        break;
                    case '2':
                        message = 'Акция успешно обновлена!';
                        break;
                    case '3':
                        message = 'Акция успешно удалена!';
                        break;
                }
                
                if (message) {
                    showNotification(message, 'success');
                }
            }
            
            if (urlParams.has('error')) {
                const errorMsg = '<?php echo isset($_SESSION["promotion_error"]) ? $_SESSION["promotion_error"] : "Произошла ошибка"; ?>';
                showNotification(errorMsg, 'error');
                <?php unset($_SESSION["promotion_error"]); ?>
            }
        });
        
        // Функция для отображения уведомлений
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Показываем уведомление
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // Скрываем и удаляем через 5 секунд
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }

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
        event.stopPropagation();
        
        const url = new URL(window.location.href);
        url.searchParams.set('msg', ticketId);
        window.history.pushState({}, '', url);
        
        document.getElementById('chat-ticket-id').textContent = ticketId;
        
        loadChatMessages(ticketId);
        
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

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const msgParam = urlParams.get('msg');
        if (msgParam) {
            openChat(msgParam);
        }
    });
    </script>
</body>
</html>