-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 27 2025 г., 23:13
-- Версия сервера: 5.7.39
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `fitzone`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Тренажёры'),
(2, 'Одежда'),
(3, 'Питание'),
(4, 'Электроника'),
(5, 'Аксессуары');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` enum('cash','card') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `full_name`, `address`, `payment_method`, `total`, `created_at`) VALUES
(1, 9, 'Марсель', 'Марсель', 'cash', '6398.00', '2025-03-22 07:03:43'),
(2, 10, 'гусейнова лэйла ', 'п', 'cash', '898.00', '2025-03-22 11:48:28'),
(3, 9, 'фуа', 'пцнц', 'cash', '3199.00', '2025-03-24 19:52:24'),
(4, 9, 'ыпыр', 'ыппфп', 'cash', '410.00', '2025-03-24 19:58:53'),
(5, 9, 'яяяя', 'яяяя', 'cash', '432.00', '2025-03-24 19:59:32'),
(6, 9, 'ы', 'ып', 'cash', '3199.00', '2025-03-26 16:04:04'),
(7, 9, 'й', 'й', 'cash', '22000.00', '2025-03-26 16:04:23'),
(8, 9, 'фыв', 'фывфыв', 'card', '410.00', '2025-03-26 16:04:42'),
(9, 9, 'Проверка покупки', 'Проверка покупки', 'cash', '10000.00', '2025-03-26 18:21:21'),
(10, 9, 'Проверка добавления', 'Проверка добавления', 'cash', '120000.00', '2025-03-26 18:38:05'),
(11, 9, 'gag', 'aga', 'cash', '110000.00', '2025-03-27 07:05:30'),
(12, 9, 'asga', 'rtje', 'cash', '10000000.00', '2025-03-27 07:20:38'),
(13, 9, 'яя', 'яя', 'cash', '2398.00', '2025-03-27 11:54:16'),
(14, 9, 'ысыс', 'ысы', 'cash', '898.00', '2025-03-27 12:19:13'),
(15, 9, 'афыа', 'фы', 'cash', '411.00', '2025-03-27 12:51:47'),
(16, 9, 'ур', 'укр', 'cash', '531.00', '2025-03-27 12:57:30'),
(17, 9, '3124', '124124', 'cash', '459.00', '2025-03-27 18:25:13');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 53, 2, '3199.00'),
(2, 2, 51, 1, '499.00'),
(3, 3, 53, 1, '3199.00'),
(4, 4, 54, 1, '11.00'),
(5, 5, 54, 3, '11.00'),
(6, 6, 53, 1, '3199.00'),
(7, 7, 54, 2000, '11.00'),
(8, 8, 54, 1, '11.00'),
(9, 9, 55, 1, '10000.00'),
(10, 10, 55, 12, '10000.00'),
(11, 11, 55, 11, '10000.00'),
(12, 12, 55, 1000, '10000.00'),
(13, 13, 19, 1, '1999.00'),
(14, 14, 51, 1, '499.00'),
(15, 15, 56, 1, '12.00'),
(16, 16, 56, 11, '12.00'),
(17, 17, 56, 5, '12.00');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `stock` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `color`, `material`, `country`, `size`, `price`, `category_id`, `subcategory_id`, `created_at`, `status`, `stock`) VALUES
(19, 'Гантели Demix, 5 кг                                           ', 'Гантель эргономичной формы со скругленными краями. Предназначена для безопасного использования и хранения в домашних условиях. Нескользящее неопреновое покрытие обеспечивает надежный хват гантелей во время занятий и амортизацию при соприкосновении с полом', 'Чёрный', 'Чугун, неопрен', 'Китай, Россия', '', '1999.00', 1, 1, '2025-03-17 10:15:09', 'active', 1),
(20, 'Гантель гексагональная обрезиненная KETTLER, 4кг', 'Гантели Kettler — отличный выбор для силовых и функциональных тренировок в зале и дома. Обрезиненная поверхность предотвращает повреждение самой гантели и напольного покрытия при случайных падениях. Ребристая форма дополняет функционал тренировок: помимо классических базовых упражнений их можно использовать как упоры для отжиманий.', 'Чёрный', 'Железо, резина, сталь', 'Китай', '', '2199.00', 1, 1, '2025-03-17 11:30:46', 'active', 1),
(21, 'Гантель гексагональная обрезиненная KETTLER, 10 кг', 'Гантели весом 10 кг — оптимальный выбор для силовых упражнений и функциональных тренировок. Грузы имеют шестиугольную форму и покрытие из высокопрочного синтетического каучука, что обеспечивает стабильность гантели как на ровной, так и на наклонной поверхности пола. Каучуковое покрытие защищает поверхность при соприкосновении гантели с ней. Шестиугольная форма весов придает устойчивость, что позволяет использовать их в качестве упоров для отжиманий и планки. Гриф анатомической формы имеет хромированное покрытие с фрезерованным профилем, которое обеспечивает надежный захват. Благодаря цельной конструкции отсутствует риск падения дисков, что свойственно гантелям с наборными весами', 'Чёрный', 'Железо, резина, сталь', 'Китай', '', '4599.00', 1, 1, '2025-03-17 13:04:18', 'active', 1),
(22, 'Гантель гексагональная обрезиненная Athlex, 30 кг', 'Гантели Athlex весом 30 кг. Это оптимальный вариант для силовых и функциональных тренировок. Шестигранная форма модели обеспечивает стабильное положение как на ровной, так и на наклонной поверхности, а также позволяет использовать гантели в качестве упоров для отжиманий и планки. Благодаря обрезиненному покрытию гантели не повредят напольное покрытие, а также не будут скользить в руках во время выполнения упражнений. Гриф имеет анатомическую форму, что помогает снизить нагрузку на запястья и кисти рук.', 'Чёрный', 'Чугун, резина', 'Китай', '', '14999.00', 1, 1, '2025-03-17 13:05:25', 'active', 1),
(23, 'Гантель гексагональная обрезиненная KETTLER, 15 кг', 'Гантель весом 15 кг — оптимальный выбор для силовых упражнений и функциональных тренировок. Грузы имеют шестиугольную форму и покрытие из высокопрочного синтетического каучука, что обеспечивает стабильность гантели как на ровной, так и на наклонной поверхности пола, а также позволяет использовать их в качестве упоров для отжиманий и планки. Каучуковое покрытие защищает поверхность при соприкосновении гантели с ней. Гриф анатомической формы имеет фрезерованный профиль и хромированное покрытие, что обеспечивает надежный захват. Благодаря цельной конструкции отсутствует риск падения дисков, что свойственно гантелям с наборными весами.', 'Чёрный', 'Железо, резина, сталь', 'Китай', '', '6599.00', 1, 1, '2025-03-17 13:06:23', 'active', 1),
(24, 'Гиря 4 кг чугунная обрезиненная Torneo', 'Чугунная гиря Torneo в оболочке из ПВХ. Благодаря оболочке снаряд не портит напольное покрытие. Рукоять окрашена специальной эмалью, которая обеспечивает идеальное сцепление с ладонью. Гиря позволяет заниматься 1 или 2 руками и отлично подходит для выполнения упражнений кроссфита.', 'Чёрная', 'Чугун, ПВХ', 'Китай', '', '1999.00', 1, 1, '2025-03-17 13:08:15', 'active', 1),
(42, 'Эллиптический тренажер Torneo Stella C-520BL', 'Эллиптический тренажер Torneo Stella с ультимативным набором функциональных возможностей. Помимо внушительной длины шага в 432 мм и достаточно малого Q-фактора в 198 мм, тренажер имеет 32 уровня нагрузки и позволяет тренироваться по программе WATT. Оснащен трехкомпонентным педальным узлом для максимальной плавности движений без рывков.\r\nКонсоль с большим экраном отображает сразу все показатели тренировки. Под экраном находятся эргономичные кнопки с подписями на русском языке.', 'Чёрный, серый', 'Металл, пластик', 'Китай', '', '39999.00', 1, 2, '2025-03-21 20:41:50', 'active', 1),
(43, 'Эллиптический тренажер складной Torneo Flex C-220F', 'Складной эллиптический тренажер, позволяющий заниматься по целевым программам, повышающим мотивацию к тренировкам. Идеально подходит для небольших жилых помещений: в сложенном виде занимает места чуть больше, чем обычный степпер.\r\nДля максимально компактного размещения при складывании, достаточно отсоединить педали от маховика и закрепить их на заднем стабилизаторе. При наличии свободного пространства, можно не отсоединять педали, а складывать тренажер сразу.\r\nКонсоль с LED-подсветкой отображает различные показатели тренировки, включая пульс, время тренировки, пройденную дистанцию, количество сожженных калорий, скорость вращения педалей. Консоль сообщит о скором разряде батареек, а если тренировка поставлена на паузу, на ее экране будет отображаться индикация STOP.', 'Чёрный, серый', 'Металл, пластик, резина', 'Китай', '', '24999.00', 1, 2, '2025-03-21 20:46:28', 'active', 1),
(44, 'Скакалка Demix', 'Скоростная скакалка от Demix — отличный снаряд для тех, кто привык поддерживать себя в хорошей форме. Удобные эргономичные ручки сделают тренировки максимально эффективными. Прочный шнур гарантирует, что скакалка прослужит долго. Длина 3 м.', 'Серый', 'ПВХ', 'Китай', '300 см', '399.00', 1, 2, '2025-03-21 20:47:44', 'active', 1),
(45, 'Скакалка с утяжелителями Demix', 'Скакалка с утяжелителями от Demix идеально подходит для эффективных тренировок. Модель изготовлена из прочных и долговечных материалов. Эргономичные ручки для удобства во время занятий. Шнур регулируется по длине.', 'Чёрный', 'Шнур: ПВХ, ручки: полипропилен', 'Китай', '300 см', '999.00', 1, 2, '2025-03-21 20:49:16', 'active', 1),
(46, 'Велотренажер магнитный Torneo Raceter B-532BL', 'Велотренажер для сайклинг-тренировок в домашних условиях, имитирующий езду на шоссейном велосипеде. Тренажер предназначен для повышения выносливости, придания рельефа мышцам и быстрого снижения веса. Тренировка на спин-байке позволяет чередовать уровень нагрузки и попеременно нагружать разные группы мышц с помощью регулировок тренажера: нагрузки, скорости, изменения позы и использования различных встроенных программ тренировки.\r\nЭлектронная система c 24 уровнями регулировки сопротивления.\r\n23 встроенные программы и функция быстрого старта.', 'Чёрный, серый', 'Сталь, пластик, пена', 'Китай', '', '45999.00', 1, 2, '2025-03-21 20:51:06', 'active', 1),
(47, 'Мини-велотренажер Torneo B-005', 'Компактный мини-велотренажер Torneo поможет развить мышцы ног, бедер, ягодиц и рук. Легкая и мобильная конструкция позволяет использовать модель дома и в офисе, сидя на стуле, кресле или на диване. Можно вращать педали вперед и назад, сидя с вертикальной спиной или откинувшись на спинку кресла. Модель удобна для тренировок людям старшего возраста. Внимание: на тренажер вставать нельзя! Подходит для тренировок только в сидячем положении.\r\nМногофункциональный дисплей показывает время тренировки, пройденную дистанцию, израсходованные калории.\r\nРегулируемая нагрузка и плавный ход педалей за счет магнитной системы.', '', 'Чёрный, бирюзовый', 'Китай', '', '7199.00', 1, 2, '2025-03-21 20:52:53', 'active', 1),
(48, 'Набор мини-лент Demix', 'Набор из 3 мини-лент с сумочкой для хранения. Упражнения с использованием лент позволяют укрепить мышцы рук, спины, ног, бедер и ягодиц. Каждая лента имеет свой уровень сопротивления: light — 5 кг, medium — 7 кг, heavy — 9 кг. Толщина лент от 0.05 см до 0.11 см. Длина окружности 50 см, ширина 5 см. Ленты выполнены из термоэластопласта, легко растягиваются и восстанавливают свою форму.', 'Розовый, красный, фиолетовый', 'Термопластичный эластомер', 'Китай', '50 х 5', '299.00', 1, 9, '2025-03-21 20:55:38', 'active', 1),
(49, 'Набор длинных силовых лент Demix', 'Набор из 4 лент с разным уровнем сопротивления: 5–15 кг, 15–25 кг, 25–40 кг и 40–50 кг. Это отличная замена привычному инвентарю: тренажерам, гантелям, гирям и штангам. Они позволяют проработать практически все группы мышц, помогают улучшить физическую форму и повысить выносливость.\r\nЛенты пригодятся не только для силовых тренировок, но и для упражнений на растяжку или для восстановления после травм. А благодаря компактному размеру их удобно брать собой для занятий на улице или в зале.\r\nЛенты выполнены из эластичного материала, легко растягиваются и восстанавливают свою форму. В набор входит тканевый чехол для удобного хранения и переноски.', 'Красный, зелёный, оранжевый, фиолетовый', 'Эластичные силовые ленты: 100% термопластичный эластомер; сумка для переноски: 100% полиэстер', 'Китай', '27.5 x 12', '2499.00', 1, 9, '2025-03-21 21:01:33', 'active', 1),
(50, 'Мяч гимнастический с насосом Demix, 75 см', 'Гимнастический мяч предназначен для упражнений, выполняемых сидя, упражнений для пресса и спины, а также для игр. Система Anti-burst обеспечивает защиту от взрыва: при нарушении целостности мяч постепенно сдувается. Мяч рассчитан на пользователя весом до 200 кг и ростом до 200 см. Диаметр мяча 75 см. Насос идет в комплекте.', 'Синий', 'ПВХ', 'Китай', '75 см', '1599.00', 1, 9, '2025-03-21 21:03:12', 'active', 1),
(51, 'Мяч для пилатеса Sensana, 20 см', 'Легкий мяч от Sensana подойдет для занятий пилатесом, фитнесом, йогой, гимнастикой. Выполняя упражнения с мячом вы сможете плавно и без лишней нагрузки проработать различные группы мышц. Аксессуар выполнен из прочного материала. Поверхность не скользит — вам будет максимально комфортно заниматься. В комплект входит трубочка, с помощью которой можно очень быстро надуть мяч. Не стоит надувать изделие сразу после того, как вы принесли его с улицы — материалу необходимо адаптироваться к температуре дома. Рекомендуется оставить мяч надутым на 24 ч, после чего дополнительно поддуть до нужного размера. Диаметр 20 см. Максимальный вес пользователя 80 кг.', 'Белый', 'Поливинилхлорид (ПВХ)', 'Китай', '20 см', '499.00', 1, 9, '2025-03-21 21:04:40', 'active', 1),
(52, 'Коврик для йоги Demix', 'Прочный коврик Demix — отличный выбор для занятий йогой, пилатесом и фитнесом. Нескользящая поверхность обеспечивает комфортные тренировки.', 'Жёлтый', 'Поливинилхлорид', 'Китай', '160 х 61', '899.00', 1, 9, '2025-03-21 21:05:40', 'active', 1),
(53, 'Коврик для фитнеса Demix', 'Коврик Demix отлично подойдет для занятий фитнесом и пилатесом. Толщина коврика 15 мм, что обеспечивает оптимальный уровень мягкости и защиты суставов и коленей. Из-за увеличенной толщины коврик не подходит для статичных упражнений, в которых важно держать баланс. В комплекте ремень для хранения и переноски. Рекомендуется заниматься на коврике без обуви, чтобы не повредить его поверхность.', 'Серый, оранжевый', 'Бутадиен-нитрильный каучук', 'Китай', '173 x 61 x 1.5', '3199.00', 1, 9, '2025-03-21 21:06:47', 'active', 10),
(54, '111', '111', '1', '11', '11', '1', '11.00', 1, 2, '2025-03-24 19:40:21', 'archived', 1),
(55, 'Проверка добавления', 'Проверка добавления', 'Проверка добавления', 'Проверка добавления', 'Проверка добавления', 'Проверка добавления', '10000.00', 1, 1, '2025-03-26 18:20:40', 'archived', 10),
(56, 'Проверка работы', 'Проверка работы', 'Проверка работы', 'Проверка работы', 'Проверка работы', 'Проверка работы', '12.00', 1, 1, '2025-03-27 11:55:13', 'archived', 11);

-- --------------------------------------------------------

--
-- Структура таблицы `product_photos`
--

CREATE TABLE `product_photos` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `product_photos`
--

INSERT INTO `product_photos` (`id`, `product_id`, `photo`) VALUES
(1, 19, 'uploads/products/19_1.jpg'),
(2, 19, 'uploads/products/19_2.jpg'),
(3, 20, 'uploads/products/20_1.jpg'),
(4, 20, 'uploads/products/20_2.jpg'),
(5, 21, 'uploads/products/21_1.jpg'),
(6, 21, 'uploads/products/21_2.jpg'),
(7, 22, 'uploads/products/22_1.jpg'),
(8, 22, 'uploads/products/22_2.jpg'),
(9, 22, 'uploads/products/22_3.jpg'),
(10, 23, 'uploads/products/23_1.jpg'),
(11, 23, 'uploads/products/23_2.jpg'),
(12, 24, 'uploads/products/24_1.jpg'),
(13, 24, 'uploads/products/24_2.jpg'),
(14, 42, 'uploads/products/42_1.jpg'),
(15, 42, 'uploads/products/42_2.jpg'),
(16, 42, 'uploads/products/42_3.jpg'),
(17, 42, 'uploads/products/42_4.jpg'),
(18, 42, 'uploads/products/42_5.jpg'),
(19, 43, 'uploads/products/43_1.jpg'),
(20, 43, 'uploads/products/43_2.jpg'),
(21, 43, 'uploads/products/43_3.jpg'),
(22, 43, 'uploads/products/43_4.jpg'),
(23, 43, 'uploads/products/43_5.jpg'),
(24, 44, 'uploads/products/44_1.jpg'),
(25, 44, 'uploads/products/44_2.jpg'),
(26, 44, 'uploads/products/44_3.jpg'),
(27, 45, 'uploads/products/45_1.jpg'),
(28, 45, 'uploads/products/45_2.jpg'),
(29, 45, 'uploads/products/45_3.jpg'),
(30, 45, 'uploads/products/45_4.jpg'),
(31, 46, 'uploads/products/46_1.jpg'),
(32, 46, 'uploads/products/46_2.jpg'),
(33, 46, 'uploads/products/46_3.jpg'),
(34, 46, 'uploads/products/46_4.jpg'),
(35, 46, 'uploads/products/46_5.jpg'),
(36, 47, 'uploads/products/47_1.jpg'),
(37, 47, 'uploads/products/47_2.jpg'),
(38, 47, 'uploads/products/47_3.jpg'),
(39, 47, 'uploads/products/47_4.jpg'),
(40, 47, 'uploads/products/47_5.jpg'),
(41, 48, 'uploads/products/48_1.jpg'),
(42, 48, 'uploads/products/48_2.jpg'),
(43, 48, 'uploads/products/48_3.jpg'),
(44, 48, 'uploads/products/48_4.jpg'),
(45, 48, 'uploads/products/48_5.jpg'),
(46, 49, 'uploads/products/49_1.jpg'),
(47, 49, 'uploads/products/49_2.jpg'),
(48, 49, 'uploads/products/49_3.jpg'),
(49, 49, 'uploads/products/49_4.jpg'),
(50, 49, 'uploads/products/49_5.jpg'),
(51, 50, 'uploads/products/50_1.jpg'),
(52, 50, 'uploads/products/50_2.jpg'),
(53, 50, 'uploads/products/50_3.jpg'),
(54, 50, 'uploads/products/50_4.jpg'),
(55, 50, 'uploads/products/50_5.jpg'),
(56, 51, 'uploads/products/51_1.jpg'),
(57, 51, 'uploads/products/51_2.jpg'),
(58, 51, 'uploads/products/51_3.jpg'),
(59, 51, 'uploads/products/51_4.jpg'),
(60, 51, 'uploads/products/51_5.jpg'),
(61, 52, 'uploads/products/52_1.jpg'),
(62, 52, 'uploads/products/52_2.jpg'),
(63, 52, 'uploads/products/52_3.jpg'),
(64, 53, 'uploads/products/53_1.jpg'),
(65, 53, 'uploads/products/53_2.jpg'),
(66, 53, 'uploads/products/53_3.jpg'),
(67, 55, 'uploads/products/55_1.png'),
(68, 56, 'uploads/products/56_1.png');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_text` text COLLATE utf8mb4_unicode_ci,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `review_text`, `rating`, `created_at`) VALUES
(1, 53, 9, 'Хороший товар', 5, '2025-03-22 07:03:57'),
(2, 54, 9, 'Крутой товар', 5, '2025-03-27 09:00:51'),
(3, 51, 9, 'Интересный товар', 4, '2025-03-27 12:19:41');

-- --------------------------------------------------------

--
-- Структура таблицы `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `name`) VALUES
(1, 1, 'Силовые тренажёры'),
(2, 1, 'Кардиотренажёры'),
(3, 2, 'Мужская одежда'),
(4, 2, 'Женская одежда'),
(5, 2, 'Детская одежда'),
(6, 3, 'Спортивное питание'),
(7, 3, 'Фитнес питание'),
(8, 3, 'Красота и здоровье'),
(9, 1, 'Для фитнеса'),
(10, 4, 'Фитнес-браселты'),
(11, 4, 'Электромассажёры'),
(12, 4, 'Весы'),
(13, 5, 'Повязки'),
(14, 5, 'Перчатки'),
(15, 5, 'Шейкеры и бутылки');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date NOT NULL,
  `profile_pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `nickname`, `password`, `full_name`, `email`, `phone`, `birth_date`, `profile_pic`, `role`, `created_at`) VALUES
(9, 'Layashich', '$2y$10$IOH67kA3jpI1U4iUJd8uxuc/4OOGdPON3vTCmKeSo31hWRYBUGj9S', 'Камаев Марсель Фанисовичч', '2005kamaevmarsel160@gmail.com', '+7 937 499 59 92', '2005-10-25', 'uploads/profiles/Layashich.png', 'admin', '2025-03-21 19:56:23'),
(10, 'Люлёк', '$2y$10$FeYSp6rCSTaOBePN4eWTbuw9owbdFqHqRy4F9RJSxyNu9uHOmZsf2', 'Гусейнова Лэйла Фархадовна', 'legla@gmail.com', '+7 937 499 59 92', '2005-09-06', 'uploads/profiles/Люлёк.png', 'user', '2025-03-22 11:42:38');

-- --------------------------------------------------------

--
-- Структура таблицы `user_cart`
--

CREATE TABLE `user_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user_favorites`
--

INSERT INTO `user_favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(3, 9, 53, '2025-03-22 06:11:40'),
(8, 9, 54, '2025-03-27 09:01:34'),
(10, 9, 56, '2025-03-27 12:32:11'),
(20, 9, 55, '2025-03-27 18:22:04');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Индексы таблицы `product_photos`
--
ALTER TABLE `product_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nickname` (`nickname`);

--
-- Индексы таблицы `user_cart`
--
ALTER TABLE `user_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT для таблицы `product_photos`
--
ALTER TABLE `product_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `user_cart`
--
ALTER TABLE `user_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`);

--
-- Ограничения внешнего ключа таблицы `product_photos`
--
ALTER TABLE `product_photos`
  ADD CONSTRAINT `product_photos_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_cart`
--
ALTER TABLE `user_cart`
  ADD CONSTRAINT `user_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `user_favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
