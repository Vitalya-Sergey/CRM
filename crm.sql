-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 13 2025 г., 09:03
-- Версия сервера: 10.4.28-MariaDB
-- Версия PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `crm`
--

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `birthday` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `birthday`, `created_at`) VALUES
(1, 'Ivan Ivanov', 'ivan.ivanov@example.com', '+79991234567', '1985-05-15', '2025-01-13 09:21:57'),
(2, 'Maria Petrovna', 'maria.petrova@example.com', '+79991234568', '1990-07-20', '2025-01-13 09:21:57'),
(3, 'Sergei Sidorov', 'sergey.sidorov@example.com', '+79991234569', '1988-03-30', '2025-01-13 09:21:57'),
(6, 'Pedik Petr', 'dmitry.dmitriev@mail.ru', '13895714124', '2025-01-31', '2025-02-03 03:04:50'),
(1738895546, 'не указано', 'lox@mail.ru', 'не указано', '0000-00-00', '2025-02-07 02:32:26'),
(1738895547, 'Pushkin Alex Kirilovich', 'PAK@mail.ru', '+7897235553535', '2010-02-01', '2025-03-01 02:39:46'),
(1738895548, 'hh', 'tmb35kepy@vobau.net', '+78972345499', '2025-02-26', '2025-03-01 03:22:00');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('1','0') DEFAULT '1',
  `admin` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `client_id`, `order_date`, `total`, `status`, `admin`) VALUES
(3, 1, '2025-01-13 09:25:36', 200.00, '0', 1),
(5, 2, '2025-01-14 03:15:45', 300.00, '0', 1),
(1738732062, 6, '2025-02-05 05:07:42', 1600.00, '1', 1),
(1738732563, 3, '2025-02-05 05:16:03', 250.50, '0', 1),
(1738732643, 2, '2025-02-05 05:17:23', 500.00, '0', 1),
(1738732738, 6, '2025-02-05 05:18:58', 500.00, '0', 1),
(1738818886, 3, '2025-02-06 05:14:46', 623.00, '1', 1),
(1738818896, 2, '2025-02-06 05:14:56', 1750.50, '1', 1),
(1738895546, 1738895546, '2025-02-07 02:32:26', 250.50, '1', 1),
(1739250762, 2, '2025-02-11 05:12:42', 1750.50, '1', 1),
(1739423871, 2, '2025-02-13 05:17:51', 2123.00, '0', 2),
(1739424654, 1738895546, '2025-02-13 05:30:54', 2123.00, '0', 2),
(1739424731, 1738895546, '2025-02-13 05:32:11', 250.50, '0', 2),
(1739424751, 6, '2025-02-13 05:32:31', 2123.00, '0', 3),
(1739425148, 6, '2025-02-13 05:39:08', 1500.00, '1', 3),
(1739426074, 6, '2025-02-13 05:54:34', 623.00, '1', 3),
(1739426459, 6, '2025-02-13 06:00:59', 1500.00, '1', 1),
(1739426499, 1738895546, '2025-02-13 06:01:39', 2123.00, '1', 2),
(1739426533, 3, '2025-02-13 06:02:13', 1750.50, '1', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(23, 3, 6, 12, 450.00),
(26, 5, 8, 13, 500.00),
(36, 1738732062, 1, 1, 100.00),
(37, 1738732062, 6, 1, 1500.00),
(38, 1738732563, 1, 1, 100.00),
(39, 1738732563, 2, 1, 150.50),
(40, 1738732643, 9, 1, 500.00),
(41, 1738732738, 9, 1, 500.00),
(42, 1738818886, 8, 1, 123.00),
(43, 1738818886, 9, 1, 500.00),
(44, 1738818896, 1, 1, 100.00),
(45, 1738818896, 2, 1, 150.50),
(46, 1738818896, 6, 1, 1500.00),
(47, 1738895546, 1, 1, 100.00),
(48, 1738895546, 2, 1, 150.50),
(49, 1739250762, 1, 1, 100.00),
(50, 1739250762, 2, 1, 150.50),
(51, 1739250762, 6, 1, 1500.00),
(52, 1739423871, 6, 1, 1500.00),
(55, 1739424654, 6, 1, 1500.00),
(56, 1739424654, 8, 1, 123.00),
(57, 1739424654, 9, 1, 500.00),
(58, 1739424731, 1, 1, 100.00),
(59, 1739424731, 2, 1, 150.50),
(60, 1739424751, 6, 1, 1500.00),
(61, 1739424751, 8, 1, 123.00),
(62, 1739424751, 9, 1, 500.00),
(63, 1739425148, 6, 1, 1500.00),
(64, 1739426074, 8, 1, 123.00),
(65, 1739426074, 9, 1, 500.00),
(66, 1739426459, 6, 1, 1500.00),
(67, 1739426499, 6, 1, 1500.00),
(68, 1739426499, 8, 1, 123.00),
(69, 1739426499, 9, 1, 500.00),
(70, 1739426533, 1, 1, 100.00),
(71, 1739426533, 2, 1, 150.50),
(72, 1739426533, 6, 1, 1500.00);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`) VALUES
(1, 'Tovar 1', 'Opisanie tovara 1', 100.00, 50),
(2, 'Tovar 2', 'Opisanie tovara 2', 150.50, 30),
(6, 'какашки', 'очень вкусные', 1500.00, 75),
(8, 'товарик3', 'не будет', 123.00, 15),
(9, 'стул', 'кожанный', 500.00, 30);

-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `type` enum('tech','crm') NOT NULL,
  `message` varchar(256) DEFAULT NULL,
  `client` int(11) NOT NULL,
  `admin` int(11) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Ожидает','В работе','Выполнено') NOT NULL DEFAULT 'Ожидает'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tickets`
--

INSERT INTO `tickets` (`id`, `type`, `message`, `client`, `admin`, `create_at`, `status`) VALUES
(1, 'tech', 'проблема', 1, 2, '2025-03-10 04:04:44', 'Ожидает'),
(2, 'crm', 'ОНА НЕ РАБОТАЕТ ААААААА', 3, 2, '2025-03-10 04:05:50', 'Ожидает'),
(3, 'tech', 'виталя лох', 1, NULL, '2025-03-13 07:54:48', 'Ожидает'),
(4, 'tech', 'gfhgfh', 2, NULL, '2025-03-13 07:55:29', 'Ожидает');

-- --------------------------------------------------------

--
-- Структура таблицы `tickets_message`
--

CREATE TABLE `tickets_message` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(256) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(256) NOT NULL,
  `token` varchar(256) DEFAULT NULL,
  `type` enum('admin','tech') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`, `surname`, `token`, `type`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'kitchen', NULL, 'admin'),
(2, 'manager', '456', 'Manager', '', 'bG9naW49J21hbmFnZXInJnBhc3N3b3JkPSc0NTYnJnVuaXF1ZT0xNzQxODUyNTA5', 'tech'),
(3, 'sales', 'sales789', 'Sales Representative', '', '', 'admin');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `orders_ibfk_1` (`admin`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tickets_message`
--
ALTER TABLE `tickets_message`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1738895549;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1739426534;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `tickets_message`
--
ALTER TABLE `tickets_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`admin`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
