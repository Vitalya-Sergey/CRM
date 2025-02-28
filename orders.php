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
    <title>CRM | Заказы</title>
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/orders.css">
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
            
            ?>
            </p>
            <ul class="header_link">
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
            </ul>
            <a class="header_login" href="?do=logout">Выйти <i class="fa fa-sign-out" aria-hidden="true"></i>
            </a>
        </div>
    </header>
    <main>
        <section class="filters">
            <div class="container">
                <form action="" class="main__form">

                    <label for="search">Поиск по названию</label>
                    <input <?php inputDefaultValue('search', ''); ?> type="text" id="search" name="search" placeholder="Введите название" >
                    <label for="search">Сортировка</label>
                    <select name="search_name" id="sort">
                    <?php $selectNameOptions = [[
                            'key' => 'clients.name',
                            'value' => 'По клиенту'
                        ],[
                            'key' => 'orders.id',
                            'value' => 'По Ид'
                        ],[
                            'key' => 'orders.order_date',
                            'value' => 'По дате'
                        ],[
                            'key' => 'orders.total',
                            'value' => 'По сумме'
                        ],[
                            'key' => 'orders.status',
                            'value' => 'По статусу'
                        ]];
                        selectDefaultValue('search_name', $selectNameOptions, 'name');
                        ?>
                    </select>
                    <label for="search">Сортировать </label>
                    <select name="sort" id="sort">
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
                    <label for="search">Выводить </label>
                    <select name="checkbox" id="checkbox">
                    <?php $selectCheckboxOptions = [[
                            'key' => '',
                            'value' => 'Все'
                        ],[
                            'key' => 'AND orders.status = "1"',
                            'value' => 'Активные'
                        ],[
                            'key' => 'AND orders.status = "0"',
                            'value' => 'Неактивные'
                        ]];
                        selectDefaultValue('checkbox', $selectCheckboxOptions, '');
                        ?>
                    </select>
                    <button type="submit">Поиск</button>
                    <a class="search" href="?" >Сбросить</a>
                </form>
            </div>
        </section>
        <section class="orders">
            <h2 class="orders_title">Список товаров</h2>
            <button onclick="MicroModal.show('add-modal')" class="orders_add"><i class="fa fa-plus-square fa-2x"
                    aria-hidden="true"></i></button>

                    <div style="text-align: center;">
                <?php 
                require 'api/DB.php';
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $maxOrders = 5;
                $_SESSION['maxOrders'] = $maxOrders;
                $offset = ($currentPage - 1) * $maxOrders;
                $_SESSION['offset'] = $offset;

                function CountSearch($params, $db){
                    $search = isset($params['search']) ? $params['search'] : '';
                    $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
                    $sort = isset($params['sort']) ? $params['sort'] : '';
                    $status = isset($params['checkbox']) ? $params['checkbox'] : '';
                
                    if ($sort) { 
                        $sort = "ORDER BY $search_name $sort";
                    } 
                    $search = trim(strtolower($search));
                
                    $orders = $db->query(
                        "SELECT *
                        FROM orders 
                        JOIN clients ON orders.client_id = clients.id 
                        JOIN order_items ON orders.id = order_items.order_id 
                        JOIN products ON order_items.product_id = products.id 
                        JOIN users ON orders.admin = users.id 
                        WHERE (LOWER(clients.name) LIKE '%$search%' OR LOWER(products.name) LIKE '%$search%') $status
                        GROUP BY orders.id, clients.name, orders.order_date, orders.total 
                        $sort
                        ")->fetchAll();
                   
                    return count(array_keys($orders));
                }
                $totalOrders = CountSearch($_GET, $db);
                $maxPage = ceil($totalOrders / $maxOrders);

                // Проверка на корректность значения текущей страницы
                if ($currentPage < 1) {
                    $currentPage = 1;
                } elseif ($currentPage > $maxPage) {
                    $currentPage = $maxPage;
                }


                function paramsToString(array $params){
                    $queryParams = [];
                    foreach ($params as $key => $value){
                        $queryParams[] = "$key=$value";
                    }
                    return implode('&', $queryParams);
                }

                $prev = $currentPage - 1;
                if ($currentPage > 1) {
                    $copyParams = $_GET;
                    $copyParams['page'] = $prev;
                    $prevQueryParams = paramsToString($copyParams);
                    echo "<button><a href='?$prevQueryParams'><i class='fa fa-chevron-left' aria-hidden='true'></i></a></button>";
                } else {
                    echo "<button style='color: gray; cursor: not-allowed;' disabled><i class='fa fa-chevron-left' aria-hidden='true'></i></button>";
                }

                for ($i = 1; $i <= $maxPage; $i++) {
                    $copyParams = $_GET;
                    $copyParams['page'] = $i;
                    $pageQueryParams = paramsToString($copyParams);
                    
                    if ($currentPage == $i) {
                        echo "<a href='?$pageQueryParams' style='color: red; cursor: not-allowed;'>$i</a>";
                    } else {
                        echo "<a href='?$pageQueryParams' style='color: green;'>$i</a>";
                    }
                }

                $next = $currentPage + 1;     
                if ($currentPage < $maxPage) {
                    $copyParams = $_GET;
                    $copyParams['page'] = $next;
                    $nextQueryParams = paramsToString($copyParams);
                    echo "<button><a href='?$nextQueryParams'><i class='fa fa-chevron-right' aria-hidden='true'></i></a></button>";
                } else {
                    echo "<button style='color: gray; cursor: not-allowed;' disabled><i class='fa fa-chevron-right' aria-hidden='true'></i></button>";
                }
                // echo "  <p>$currentPage / $maxPage</p> ";

                  ?>
                
            </div>

            <div class="container">
                <table>
                    <thead>
                        <th>ИД</th>
                        <th>ФИО клиента</th>
                        <th>Дата заказа</th>
                        <th>Цена</th>
                        <th>Инфор. о заказе</th>
                        <th>Кто добавил</th>
                        <th>Статус</th>
                        <th>Редак.</th>
                        <th>Удалить</th>
                        <th>Ген. чека</th>
                        <th>Подр.</th>
                    </thead>
                    <tbody>
                    <?php
                        require 'api/DB.php';
                        require_once 'api/orders/OutputOrders.php';
                        require_once 'api/orders/OrdersSearch.php';
                        $orders = OrdersSearch($_GET,$db);
                        OutputOrders($orders);
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
                        Создание заказа
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form action="api/orders/AddOrders.php" method="POST" id="registration-form">
                        <label for="name">ФИО клиента:</label>
                        <div class='modal_form-group'>
                        <select class="main-select" name="client" id="client">
                            <option value="new">Новый пользователь</option>
                            <?php 
                            $clients = $db->query("SELECT id, name FROM clients")->fetchAll();
                            foreach($clients as $key => $client){

                                $id = $client['id'];
                                $name =  $client['name'];
                                echo "<option value='$id'>$name</option>";
                            }
                            ?>
                            </select> </div>
                            <div id='email-field' class='modal_form-group'>
                        <label for="email">Почта:</label>
                        <input type="email" id="email" name="email" >
                                </div>
                                <div class='modal_form-group'>
                        <label for="products">Товар:</label>
                        <select class="main-select" name="products[]" id="products" multiple>
                        <?php 
                            $products = $db->query("SELECT id, name, price, stock FROM products")->fetchAll();
                            foreach($products as $key => $product){

                                $id = $product['id'];
                                $name =  $product['name'];
                                $price = $product['price'];
                                $stock = $product['stock'];
                                echo "<option value='$id'>$name : $price\$ : $stock шт.</option>";
                            }
                            ?>
                            </select></div>

                        <button class="create" type="submit">Создать</button>
                        <button onclick="MicroModal.close('add-modal')" class="cancel" type="button">Отмена</button>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide" id="edit-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Редактировать заказ
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form id="registration-form">
                        <label for="name">Название:</label>
                        <input type="text" id="name" name="name" required>

                        <label for="data">Дата заказа:</label>
                        <input type="data" id="data" name="data" required>

                        <label for="price">Цена:</label>
                        <input type="price" id="price" name="price" required>

                        <label for="stock">Количество:</label>
                        <input type="stock" id="stock" name="stock" required>

                        <button class="create" type="submit">Редактировать</button>
                        <button onclick="MicroModal.close('edit-modal')" class="cancel" type="button">Отмена</button>
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
                        Вы уверены, что хотите удалить заказ?
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
    //проверить $_SESSION['orders-errors']
    //на существование и пустоту
    //существует и не пустой  echo 'open'
    if (isset($_SESSION['orders-errors']) && !empty($_SESSION['orders-errors'])) {
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
                    if (isset($_SESSION['orders-errors']) && !empty($_SESSION['orders-errors'])) {
                        echo $_SESSION['orders-errors'];
                        $_SESSION['orders-errors']="";
                    }
                    ?>
                </main>
            </div>
        </div>
    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initClientsModal.js"></script>
    <script defer src="scripts/orders.js"></script>
    <script>
    function updateCheckbox() {
        const checkbox = document.getElementById('checkbox');
        const url = new URL(window.location.href);
        
        // Устанавливаем или удаляем параметр checkbox в URL
        if (checkbox.checked) {
            url.searchParams.set('checkbox', '1');
        } else {
            url.searchParams.delete('checkbox');
        }
        
        // Обновляем страницу с новыми параметрами
        window.location.href = url.toString();
    }
    </script>
</body>
</html>