-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 22 2025 г., 18:37
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

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
(2, 'Maria Sosi', 'maria.sosovna@example.com', '+79991234856', '1990-07-20', '2025-01-13 09:21:57'),
(3, 'Sergei Sidorov', 'sergey.sidorov@example.com', '+79991234569', '1988-03-30', '2025-01-13 09:21:57'),
(6, 'Pedik Petr', 'dmitry.dmitriev@mail.ru', '13895714124', '2025-01-31', '2025-02-03 03:04:50'),
(1738895546, 'не указано', 'lox@mail.ru', 'не указано', '0000-00-00', '2025-02-07 02:32:26'),
(1738895547, 'Ivan Ivanov', 'ivan.ivanov@mail.ru', '88005553535', '2025-01-27', '2025-02-25 14:52:18'),
(1738895548, 'Mac Traher', 'HUI@mail.ru', '7 923 2275088', '2025-01-27', '2025-02-26 08:19:17'),
(1738895552, 'Дибил Какашков', 'magaaaN228@mail.ru', '88005553530', '2025-02-11', '2025-02-26 08:27:10'),
(1738895553, 'Хых Хохов', 'kakish@moimail.ru', '99005553535', '2025-03-06', '2025-03-06 04:48:14'),
(1738895554, 'Писюнчик', 'penis228@mail.ru', '770044443535', '2025-03-07', '2025-03-15 17:25:06');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `admin` int(255) DEFAULT NULL,
  `promotion_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `client_id`, `order_date`, `total`, `status`, `admin`, `promotion_id`) VALUES
(3, 1, '2025-01-13 09:25:36', 200.00, '0', 1, NULL),
(5, 2, '2025-01-14 03:15:45', 300.00, '0', 1, NULL),
(1738732062, 6, '2025-02-05 05:07:42', 1600.00, '1', 1, NULL),
(1738732563, 3, '2025-02-05 05:16:03', 250.50, '0', 1, NULL),
(1738732643, 2, '2025-02-05 05:17:23', 500.00, '0', 1, NULL),
(1738732738, 6, '2025-02-05 05:18:58', 500.00, '0', 1, NULL),
(1738818886, 3, '2025-02-06 05:14:46', 623.00, '1', 1, NULL),
(1738818896, 2, '2025-02-06 05:14:56', 1750.50, '1', 1, NULL),
(1738895546, 1738895546, '2025-02-07 02:32:26', 250.50, '1', 1, NULL),
(1739250762, 2, '2025-02-11 05:12:42', 1750.50, '0', 1, NULL),
(1739250763, 0, '2025-02-13 05:08:16', NULL, '1', 1, NULL),
(1739423871, 2, '2025-02-13 05:17:51', 2123.00, '0', 1, NULL),
(1739424654, 1738895546, '2025-02-13 05:30:54', 2123.00, '0', 1, NULL),
(1739424731, 1738895546, '2025-02-13 05:32:11', 250.50, '0', 1, NULL),
(1739424751, 6, '2025-02-13 05:32:31', 2123.00, '0', 1, NULL),
(1739425148, 6, '2025-02-13 05:39:08', 1500.00, '1', 1, NULL),
(1739426074, 6, '2025-02-13 05:54:34', 623.00, '1', 1, NULL),
(1739426459, 6, '2025-02-13 06:00:59', 1500.00, '0', 1, NULL),
(1739426499, 1738895546, '2025-02-13 06:01:39', 2123.00, '1', 1, NULL),
(1739426533, 3, '2025-02-13 06:02:13', 1750.50, '0', 1, NULL),
(1740541371, 6, '2025-02-26 03:42:51', 2373.50, '0', 1, NULL),
(1740541438, 1738895547, '2025-02-26 03:43:58', 2373.50, '0', 1, NULL),
(1740541480, 2, '2025-02-26 03:44:40', 250.50, '0', 1, NULL),
(1740541731, 2, '2025-02-26 03:48:51', 1750.50, '0', 1, NULL),
(1740542055, 6, '2025-02-25 21:54:15', 2723.50, '0', 1, NULL),
(1740542099, 6, '2025-02-25 21:54:59', 850.00, '1', NULL, NULL),
(1740542439, 6, '2025-02-25 22:00:39', 473.00, '0', 1, NULL),
(1740542488, 6, '2025-02-25 22:01:28', 2000.00, '0', 1, NULL),
(1740542544, 6, '2025-02-26 04:02:24', 1850.00, '1', NULL, NULL),
(1740542638, 6, '2025-02-26 04:03:58', 250.50, '1', NULL, NULL),
(1740542912, 6, '2025-02-26 04:08:32', 850.00, '1', 1, NULL),
(1741236404, 2, '2025-03-06 04:46:44', 3350.00, '1', 1, NULL),
(1741236412, 6, '2025-03-06 04:46:52', 1750.50, '0', 1, NULL),
(1742058940, 1738895553, '2025-03-15 17:15:40', 2900.00, '0', 1, 1),
(1742059111, 1738895552, '2025-03-15 17:18:31', 400.00, '0', 1, 1),
(1742059199, 1738895548, '2025-03-15 17:19:59', 2850.00, '0', 1, 1),
(1742059389, 1738895553, '2025-03-15 17:23:09', 3250.00, '0', 1, NULL),
(1742059421, 1738895552, '2025-03-15 17:23:41', 2900.00, '0', 1, 1),
(1742059723, 1738895554, '2025-03-15 17:28:43', 2900.00, '0', 1, 1),
(1742060000, 1738895554, '2025-03-15 17:33:20', 250.50, '1', 1, NULL),
(1742060131, 1738895548, '2025-03-15 17:35:31', 3250.00, '1', 1, NULL),
(1742060404, 1738895547, '2025-03-15 17:40:04', 5500.00, '0', 1, 1),
(1742060722, 1738895554, '2025-03-15 17:45:22', 5500.00, '0', 1, NULL),
(1742061048, 1738895552, '2025-03-15 17:50:48', 8400.00, '1', 1, 1),
(1742191346, 1738895554, '2025-03-17 06:02:26', 6250.00, '0', 1, 1),
(1742191452, 1738895553, '2025-03-17 06:04:12', 5500.00, '0', 1, 1),
(1742661534, 1738895554, '2025-03-22 16:38:54', 5500.00, '1', 2, 7),
(1742663052, 1738895553, '2025-03-22 17:04:12', 5500.00, '1', 2, 7);

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
(53, 1739423871, 8, 1, 123.00),
(54, 1739423871, 9, 1, 500.00),
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
(72, 1739426533, 6, 1, 1500.00),
(73, 1740541371, 1, 1, 100.00),
(74, 1740541371, 2, 1, 150.50),
(75, 1740541371, 6, 1, 1500.00),
(76, 1740541371, 8, 1, 123.00),
(77, 1740541371, 9, 1, 500.00),
(78, 1740541438, 1, 1, 100.00),
(79, 1740541438, 2, 1, 150.50),
(80, 1740541438, 6, 1, 1500.00),
(81, 1740541438, 8, 1, 123.00),
(82, 1740541438, 9, 1, 500.00),
(83, 1740541480, 1, 1, 100.00),
(84, 1740541480, 2, 1, 150.50),
(85, 1740541731, 1, 1, 100.00),
(86, 1740541731, 2, 1, 150.50),
(87, 1740541731, 6, 1, 1500.00),
(88, 1740542055, 1, 1, 100.00),
(89, 1740542055, 2, 1, 150.50),
(90, 1740542055, 6, 1, 1500.00),
(91, 1740542055, 8, 1, 123.00),
(92, 1740542055, 9, 1, 500.00),
(93, 1740542055, 10, 1, 350.00),
(94, 1740542099, 9, 1, 500.00),
(95, 1740542099, 10, 1, 350.00),
(96, 1740542439, 8, 1, 123.00),
(97, 1740542439, 10, 1, 350.00),
(98, 1740542488, 6, 1, 1500.00),
(99, 1740542488, 9, 1, 500.00),
(100, 1740542544, 6, 1, 1500.00),
(101, 1740542544, 10, 1, 350.00),
(102, 1740542638, 1, 1, 100.00),
(103, 1740542638, 2, 1, 150.50),
(104, 1740542912, 9, 1, 500.00),
(105, 1740542912, 10, 1, 350.00),
(106, 1741236404, 9, 1, 500.00),
(107, 1741236404, 10, 1, 350.00),
(108, 1741236404, 11, 1, 2500.00),
(109, 1741236412, 1, 1, 100.00),
(110, 1741236412, 2, 1, 150.50),
(111, 1741236412, 6, 1, 1500.00),
(112, 1742058940, 11, 1, 2500.00),
(113, 1742058940, 12, 1, 400.00),
(114, 1742059111, 12, 1, 400.00),
(115, 1742059199, 10, 1, 350.00),
(116, 1742059199, 11, 1, 2500.00),
(117, 1742059389, 10, 1, 350.00),
(118, 1742059389, 11, 1, 2500.00),
(119, 1742059389, 12, 1, 400.00),
(120, 1742059421, 11, 1, 2500.00),
(121, 1742059421, 12, 1, 400.00),
(122, 1742059723, 11, 1, 2500.00),
(123, 1742059723, 12, 1, 400.00),
(124, 1742060000, 1, 1, 100.00),
(125, 1742060000, 2, 1, 150.50),
(126, 1742060131, 10, 1, 350.00),
(127, 1742060131, 11, 1, 2500.00),
(128, 1742060131, 12, 1, 400.00),
(129, 1742060404, 13, 1, 5500.00),
(130, 1742060722, 13, 1, 5500.00),
(131, 1742061048, 11, 1, 2500.00),
(132, 1742061048, 12, 1, 400.00),
(133, 1742061048, 13, 1, 5500.00),
(134, 1742191346, 10, 1, 350.00),
(135, 1742191346, 12, 1, 400.00),
(136, 1742191346, 13, 1, 5500.00),
(137, 1742191452, 13, 1, 5500.00),
(138, 1742661534, 13, 1, 5500.00),
(139, 1742663052, 13, 1, 5500.00);

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
(1, 'Tovar 1 и всё', 'Opisanie tovara 1', 100.00, 50),
(2, 'Tovar 2', 'Opisanie tovara 2', 150.50, 30),
(6, 'какашки', 'очень вкусные', 1500.00, 75),
(8, 'товарик3', 'не будет', 123.00, 15),
(9, 'стул', 'кожанный', 500.00, 30),
(10, 'дай денег', 'кошёлек бабла', 350.00, 10),
(11, 'пипися', 'реальная пипися', 2500.00, 3),
(12, 'СВО', 'Поддержка', 400.00, 5),
(13, 'Нереальный Дилдак', 'Мощная штука', 5500.00, 52);

-- --------------------------------------------------------

--
-- Структура таблицы `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `path_to_image` varchar(256) DEFAULT NULL,
  `title` varchar(256) NOT NULL,
  `body` varchar(256) NOT NULL,
  `code_promo` varchar(256) NOT NULL,
  `discount` int(11) NOT NULL DEFAULT 0,
  `uses` int(11) NOT NULL,
  `max_uses` int(11) NOT NULL,
  `cancel_at` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `promotions`
--

INSERT INTO `promotions` (`id`, `path_to_image`, `title`, `body`, `code_promo`, `discount`, `uses`, `max_uses`, `cancel_at`, `created_at`) VALUES
(1, 'piski.png', 'Весенняя распродажа!', 'Бесплатные дилдо!', 'VES25', 20, 10, 100, '2025-03-31', '2025-03-15 02:53:10'),
(4, 'YourMom.png', 'Хочешь её/', 'Мамки на час!', 'PORNMAM', 150, 0, 0, '0000-00-00', '2025-03-22 16:04:28'),
(7, '', 'ОГРОМНЫЕ ПИСЬКИ!!!', 'ТЫ ЖЕ ХОЧЕШЬ ИХ ДАААА?? Так соси!', 'RQ974JYK', 50, 2, 150, '2025-04-21', '2025-03-22 16:35:43'),
(8, '', 'Каки?', 'У макаки!', '29QU92JH', 30, 0, 100, '2025-04-21', '2025-03-22 17:21:31'),
(9, '', 'ЖОПА', 'её не будет', 'XWSQ3P5I', 31, 0, 100, '2025-04-21', '2025-03-22 17:22:06'),
(10, '', 'барабулька', 'мелкий пизьдюк!', 'Q0ZLLHXD', 32, 0, 100, '2025-04-21', '2025-03-22 17:22:27'),
(11, '', 'НЕГРЫ', 'ПАПИЧ...', 'PFGJHVKP', 33, 0, 100, '2025-04-21', '2025-03-22 17:22:45');

-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `type` enum('tech','crm') NOT NULL,
  `message` varchar(256) DEFAULT NULL,
  `status` enum('waiting','work','complete') NOT NULL,
  `clients` int(11) NOT NULL,
  `admin` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tickets`
--

INSERT INTO `tickets` (`id`, `type`, `message`, `status`, `clients`, `admin`, `created_at`, `file_path`) VALUES
(1, 'tech', 'как срать?', 'waiting', 2, 1, '2025-03-10 04:03:45', NULL),
(2, 'crm', 'crm гавно переделывай!', 'waiting', 6, 1, '2025-03-10 04:03:48', NULL),
(3, 'tech', 'БЛЯЯЯТЬ', 'work', 1, NULL, '2025-03-10 04:56:21', NULL),
(4, 'crm', 'Я ваш рот ебал', 'complete', 1, NULL, '2025-03-10 04:56:58', NULL),
(5, 'crm', 'Ваш кал не работает', 'work', 2, NULL, '2025-03-13 04:01:07', NULL),
(6, 'tech', 'Дай денег на ноут', 'work', 2, NULL, '2025-03-13 04:05:58', NULL),
(7, 'crm', 'Фу, покажи(', 'waiting', 2, NULL, '2025-03-13 04:06:09', NULL),
(8, 'tech', 'НЕ РАБИЕТ!', 'waiting', 2, NULL, '2025-03-13 04:06:45', NULL),
(9, 'crm', 'ГДЕ ИНТЕРФАК?', 'complete', 2, NULL, '2025-03-13 04:07:00', NULL),
(10, 'tech', 'Хахахах, разраб лох не работает!', 'waiting', 2, NULL, '2025-03-13 04:07:19', NULL),
(11, 'crm', 'Где кнопки?', 'complete', 2, NULL, '2025-03-13 04:07:25', NULL),
(12, 'tech', 'ЧТо это такое, почему вылазит всякая АНИМЕШНАЯ ХУЙНЯ!\r\nЯ НЕ СМОТРЮ АНИМЕ У МЕНЯ ДЕД МУСУЛЬМАНЕН!', 'complete', 2, NULL, '2025-03-13 04:07:53', NULL),
(31, 'tech', 'НЕГР', 'work', 2, NULL, '2025-03-13 07:35:59', 'uploads/tickets/67d28adf0a26c_70c72df306756b04dba9a50076cd8edd8619fb5d_full.jpg'),
(32, 'tech', 'кто тже это?', 'work', 2, NULL, '2025-03-13 07:43:14', 'uploads/tickets/67d28c92e607c_1498538061_Ngx8j.jpg'),
(34, 'crm', 'ГИфка', 'work', 2, NULL, '2025-03-13 10:33:12', 'uploads/tickets/67d2b468953e6_updating.gif'),
(35, 'tech', 'БУМ', 'waiting', 2, NULL, '2025-03-13 10:34:06', 'uploads/tickets/67d2b49e7cc2c_Взрыв для кружка в тг.mp4'),
(36, 'tech', '-яндекс гаи', 'waiting', 2, NULL, '2025-03-13 10:41:48', 'uploads/tickets/67d2b66c25152_Counter-strike 2 2025.03.12 - 20.29.18.17.DVR.mp4'),
(40, 'crm', 'ХУЙ', 'waiting', 2, NULL, '2025-03-14 01:34:06', NULL),
(41, 'crm', 'КАКАШКИ', 'waiting', 2, 2, '2025-03-14 01:37:52', NULL),
(42, 'tech', 'Фулл есть? А если найду', 'waiting', 1, 1, '2025-03-14 01:38:18', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_messages`
--

CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(256) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `ticket_messages`
--

INSERT INTO `ticket_messages` (`id`, `ticket_id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 1, 'Поч?', '2025-03-10 04:11:25'),
(2, 2, 1, 'Ты даун, сосал?!', '2025-03-10 04:11:27'),
(3, 36, 2, 'А вы в курсе, админ даун?', '2025-03-13 10:56:06'),
(4, 41, 2, 'Вкусные', '2025-03-14 02:28:41'),
(5, 40, 2, 'ТЫ КОМУ ХУЙ ПИШЕШЬ ДАУН ОБОССАНЫЙ!', '2025-03-14 02:30:56'),
(6, 42, 2, 'админ не лох', '2025-03-14 03:37:57'),
(7, 41, 2, 'Бибка', '2025-03-14 06:21:28'),
(8, 41, 2, 'Скинь титьки', '2025-03-14 06:25:57'),
(9, 5, 2, 'ВАШ КАЛ НЕ РОБИЕТ', '2025-03-14 06:27:09'),
(10, 42, 1, 'Спасибо, дура)', '2025-03-14 06:31:18'),
(11, 42, 1, 'Сиськи)', '2025-03-14 06:31:29'),
(12, 40, 1, 'МАМЕ ТВОЕЙ ЗАСЕРЫШ!', '2025-03-14 06:35:38'),
(13, 41, 1, 'Ща я сек', '2025-03-15 17:36:36');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `type` enum('admin','tech') DEFAULT 'admin',
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(256) NOT NULL,
  `token` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `type`, `login`, `password`, `name`, `surname`, `token`) VALUES
(1, 'admin', 'admin', 'admin123', 'Administrator', 'kitchen', NULL),
(2, 'tech', 'manager', 'manager456', 'Manager', '', 'bG9naW49bWFuYWdlciZwYXNzd29yZD1tYW5hZ2VyNDU2JnVuaXF1ZT0xNzQyNjYwNjEw'),
(3, 'admin', 'sales', 'sales789', 'Sales Representative', '', '');

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
-- Индексы таблицы `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ticket_messages`
--
ALTER TABLE `ticket_messages`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1738895555;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1742663053;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT для таблицы `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
