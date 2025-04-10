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
    <title>CRM | Товары</title>
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/products.css">
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
                <li><a href="promotions.php">Акции</a></li>
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
                <form action="" class="main__form">
                    <label for="search">Поиск по названию</label>
                    <input <?php inputDefaultValue('search', ''); ?> type="text" id="search" name="search" placeholder="Введите название" >
                    <label for="search">Сортировка по </label>
                    <select name="search_name" id="sort">
                    <?php $selectNameOptions = [[
                            'key' => 'name',
                            'value' => 'По названию'
                        ],[
                            'key' => 'price',
                            'value' => 'По цене'
                        ],[
                            'key' => 'stock',
                            'value' => 'По количеству'
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
                    <button type="submit">Поиск</button>
                    <a class="search" href="?" >Сбросить</a>
                </form>
            </div>
        </section>
        <section class="products">
            <h2 class="products_title">Список товаров</h2>
            <button onclick="MicroModal.show('add-modal')" class="products_add"><i class="fa fa-plus-square fa-2x"
                    aria-hidden="true"></i></button>

                    <div style="text-align: center;">
                <?php 
                require 'api/DB.php';
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $maxProducts = 5;
                $_SESSION['maxProducts'] = $maxProducts;
                $offset = ($currentPage - 1) * $maxProducts;
                $_SESSION['offset'] = $offset;

                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
                $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

                $totalProducts =  $db -> query("
                SELECT COUNT(*) as count FROM products WHERE LOWER(name) LIKE '%$search%'
                ")->fetchAll()[0]['count'];

                $maxPage = ceil($totalProducts / $maxProducts);

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
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Редактировать</th>
                        <th>Удалить</th>
                        <th>Создать qr</th>
                    </thead>
                    <tbody>
                    <?php
                        require 'api/DB.php';
                        require_once 'api/products/OutputProducts.php';
                        require_once 'api/products/ProductsSearch.php';
                        $products = ProductsSearch($_GET,$db);
                        OutputProducts($products);
                        ?>
                        <!-- <tr>
                            <td>0</td>
                            <td>Футболка</td>
                            <td>Красная</td>
                            <td>1000.00</td>
                            <td>100</td>
                            <td onclick="MicroModal.show('edit-modal')"><i class="fa fa-pencil" aria-hidden="true"></i>
                            </td>
                            <td onclick="MicroModal.show('delete-modal')"><i class="fa fa-trash" aria-hidden="true"></i>
                            </td>
                            <td><i class="fa fa-qrcode" aria-hidden="true"></i></td>
                        </tr> -->
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
                        Добавить товар
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <form action="api/products/AddProducts.php" method="POST" id="registration-form">
                        <label for="name">Название:</label>
                        <input type="text" id="name" name="name">

                        <label for="desc">Описание:</label>
                        <input type="desc" id="desc" name="description">

                        <label for="price">Цена:</label>
                        <input type="price" id="price" name="price">

                        <label for="stock">Количество:</label>
                        <input type="stock" id="stock" name="stock">

                        <button class="create" type="submit">Создать</button>
                        <button onclick="MicroModal.close('add-modal')" class="cancel" type="button">Отмена</button>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide <?php
        if (isset($_GET['edit-product']) && !empty($_GET['edit-product'])) {
            echo 'open';
        }
        ?>" id="edit-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Редактировать товар
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                <?php
                        if (isset($_GET['edit-product']) && !empty($_GET['edit-product'])) {
                            $productId = $_GET['edit-product'];

                            $product = $db->query(
                                "SELECT * FROM products WHERE id = $productId
                            ")->fetchAll()[0];

                            if ($product) {
                                $productName = $product['name'];
                                $productDesc = $product['description']; 
                                $productPrice = $product['price'];
                                $productStock = $product['stock'];
                            }
                        }
                        ?>
                        <?php
if (isset($_GET['edit-product']) && !empty($_GET['edit-product'])) {
    echo "<form method='POST' action='api/products/EditProducts.php?id=$productId'>
                        <label for='name'>Название:</label>
                        <input type='text' id='name' name='name' value='$productName'>

                        <label for='desc'>Описание:</label>
                        <input type='desc' id='desc' name='desc' value='$productDesc'>

                        <label for='price'>Цена:</label>
                        <input type='price' id='price' name='price' value='$productPrice'>

                        <label for='stock'>Количество:</label>
                        <input type='stock' id='stock' name='stock' value='$productStock'>

                        <button class='create' type='submit'>Редактировать</button>
                        <button type='button' class='cancel' data-micromodal-close>Отмена</button>
                    </form>";
                }
                ?>
                </main>
            </div>
        </div>
    </div>

    <div class="modal micromodal-slide" id="delete-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">
                        Вы уверены, что хотите удалить товар?
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
    //проверить $_SESSION['clients-errors']
    //на существование и пустоту
    //существует и не пустой  echo 'open'
    if (isset($_SESSION['products-errors']) && !empty($_SESSION['products-errors'])) {
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
                    if (isset($_SESSION['products-errors']) && !empty($_SESSION['products-errors'])) {
                        echo $_SESSION['products-errors'];
                        $_SESSION['products-errors']="";
                    }
                    ?>
                </main>
            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initClientsModal.js"></script>
</body>

</html>