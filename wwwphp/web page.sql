-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Lis 26, 2024 at 11:33 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web page`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `first_name`, `last_name`, `email`, `PASSWORD`, `phone`, `address`, `registration_date`) VALUES
(1, 'John', 'Smith', 'john.smith@example.com', 'hashed_password_1', '123456789', '123 Main St, Springfield', '2024-11-19 22:04:52'),
(2, 'Jane', 'Doe', 'jane.doe@example.com', 'hashed_password_2', '987654321', '456 Elm St, Springfield', '2024-11-19 22:04:52'),
(3, 'Michael', 'Brown', 'michael.brown@example.com', 'hashed_password_3', '555555555', '789 Oak St, Springfield', '2024-11-19 22:04:52'),
(4, 'Emily', 'Davis', 'emily.davis@example.com', 'hashed_password_4', '444444444', '321 Maple St, Springfield', '2024-11-19 22:04:52'),
(5, 'David', 'Wilson', 'david.wilson@example.com', 'hashed_password_5', '666666666', '654 Pine St, Springfield', '2024-11-19 22:04:52');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `customer_id` int(11) DEFAULT NULL,
  `STATUS` enum('In Cart','Placed','Processing','Completed','Cancelled') DEFAULT 'In Cart',
  `total_price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_date`, `customer_id`, `STATUS`, `total_price`) VALUES
(1, '2024-11-19 10:00:00', 1, 'Placed', 3800.00),
(2, '2024-11-19 11:30:00', 2, 'Processing', 4500.00),
(3, '2024-11-19 14:00:00', 3, 'Completed', 1597.00),
(4, '2024-11-20 09:00:00', 4, 'In Cart', 279.97),
(5, '2024-11-20 12:00:00', 1, 'Placed', 259.99);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_details`
--

CREATE TABLE `order_details` (
  `detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`detail_id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 2, 2400.00),
(2, 1, 3, 1, 1400.00),
(3, 2, 5, 3, 2100.00),
(4, 2, 10, 2, 2400.00),
(5, 3, 21, 1, 999.00),
(6, 3, 25, 2, 598.00),
(7, 4, 31, 1, 99.99),
(8, 4, 36, 2, 179.98),
(9, 5, 38, 1, 59.99),
(10, 5, 40, 1, 200.00);

--
-- Wyzwalacze `order_details`
--
DELIMITER $$
CREATE TRIGGER `after_order_details_change` AFTER INSERT ON `order_details` FOR EACH ROW BEGIN
    -- Oblicz sumę unit_price dla danego order_id
    UPDATE orders
    SET total_price = (
        SELECT SUM(unit_price)
        FROM order_details
        WHERE order_id = NEW.order_id
    )
    WHERE order_id = NEW.order_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_order_details_delete` AFTER DELETE ON `order_details` FOR EACH ROW BEGIN
    -- Oblicz sumę unit_price dla danego order_id po usunięciu rekordu
    UPDATE orders
    SET total_price = (
        SELECT COALESCE(SUM(unit_price), 0.00)
        FROM order_details
        WHERE order_id = OLD.order_id
    )
    WHERE order_id = OLD.order_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_order_details_update` AFTER UPDATE ON `order_details` FOR EACH ROW BEGIN
    -- Oblicz sumę unit_price dla danego order_id po aktualizacji rekordu
    UPDATE orders
    SET total_price = (
        SELECT SUM(unit_price)
        FROM order_details
        WHERE order_id = NEW.order_id
    )
    WHERE order_id = NEW.order_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_order_details` BEFORE INSERT ON `order_details` FOR EACH ROW BEGIN
    DECLARE product_price DECIMAL(10, 2);
    
    -- Pobierz cenę jednostkową z tabeli products
    SELECT price INTO product_price
    FROM products
    WHERE product_id = NEW.product_id;
    
    -- Oblicz wartość unit_price jako cena produktu * ilość
    SET NEW.unit_price = product_price * NEW.quantity;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `NAME`, `description`, `price`, `stock_quantity`, `category_id`) VALUES
(1, 'Dell XPS 13', 'Ultrabook with Intel i7 processor and 16GB RAM', 1200.00, 10, 1),
(2, 'MacBook Air', 'Lightweight laptop with M2 chip and 256GB SSD', 999.99, 15, 1),
(3, 'Lenovo ThinkPad X1 Carbon', 'Business laptop with Intel i5 and 512GB SSD', 1400.00, 8, 1),
(4, 'Asus ROG Zephyrus', 'Gaming laptop with NVIDIA RTX 3060', 1500.00, 5, 1),
(5, 'HP Pavilion 15', 'Affordable laptop with AMD Ryzen 5 and 8GB RAM', 700.00, 20, 1),
(6, 'Acer Aspire 5', 'Affordable laptop with Intel i3 and 4GB RAM', 450.00, 30, 1),
(7, 'Razer Blade 15', 'Premium gaming laptop with RTX 3070', 2000.00, 5, 1),
(8, 'Microsoft Surface Laptop 5', 'Ultra-slim laptop with touch screen', 1100.00, 10, 1),
(9, 'Gigabyte Aorus 15', 'Gaming laptop with Intel i7 and RTX 3070', 1800.00, 8, 1),
(10, 'Huawei MateBook X Pro', 'Sleek laptop with 3K display', 1200.00, 12, 1),
(11, 'Dell Inspiron Desktop', 'Desktop computer with Intel i5 and 16GB RAM', 800.00, 12, 2),
(12, 'iMac 24\"', 'All-in-one computer with M1 chip and 4K display', 1500.00, 10, 2),
(13, 'HP Omen 25L', 'Gaming desktop with AMD Ryzen 7 and RTX 3060', 1200.00, 7, 2),
(14, 'Custom Gaming PC', 'Custom build with Ryzen 9 and RTX 4090', 2500.00, 3, 2),
(15, 'Lenovo IdeaCentre', 'Compact desktop for home and office', 600.00, 25, 2),
(16, 'Asus VivoPC', 'Compact desktop for everyday tasks', 500.00, 18, 2),
(17, 'Apple Mac Mini', 'Compact desktop with M2 chip', 899.00, 15, 2),
(18, 'Dell Alienware Aurora', 'High-end gaming PC with RTX 4080', 3500.00, 3, 2),
(19, 'HP Envy Desktop', 'Desktop with Intel i7 and 16GB RAM', 1100.00, 10, 2),
(20, 'Lenovo Legion Tower 5', 'Gaming tower with AMD Ryzen 5 and RTX 3060', 1300.00, 7, 2),
(21, 'iPhone 14', 'Apple smartphone with A16 Bionic chip', 999.00, 20, 3),
(22, 'Samsung Galaxy S23', 'Flagship Android phone with 120Hz display', 899.99, 15, 3),
(23, 'Google Pixel 7', 'Google phone with stock Android experience', 599.99, 30, 3),
(24, 'OnePlus 11', 'High-performance Android phone with Snapdragon 8 Gen 2', 699.00, 10, 3),
(25, 'Xiaomi Redmi Note 12', 'Budget-friendly phone with 108MP camera', 299.00, 50, 3),
(26, 'Samsung Galaxy A54', 'Mid-range smartphone with 5G support', 399.99, 40, 3),
(27, 'Sony Xperia 1 IV', 'Premium phone with 4K OLED display', 1199.00, 10, 3),
(28, 'Oppo Find X5 Pro', 'Flagship phone with Hasselblad cameras', 899.00, 20, 3),
(29, 'Realme GT 2 Pro', 'Affordable flagship with Snapdragon 8 Gen 1', 549.99, 25, 3),
(30, 'Motorola Edge 30', 'Stylish phone with curved display', 499.99, 30, 3),
(31, 'Logitech MX Master 3S', 'Ergonomic wireless mouse with customizable buttons', 99.99, 50, 4),
(32, 'Corsair K70 RGB', 'Mechanical gaming keyboard with RGB backlighting', 129.99, 40, 4),
(33, 'Anker PowerCore 20K', 'Portable charger with 20,000mAh capacity', 49.99, 100, 4),
(34, 'Samsung T7 Portable SSD', 'Fast external SSD with 1TB capacity', 120.00, 30, 4),
(35, 'Sony WH-1000XM5', 'Noise-cancelling wireless headphones', 349.99, 25, 4),
(36, 'Kingston A2000 NVMe SSD', '1TB NVMe SSD for fast storage', 89.99, 40, 4),
(37, 'HyperX Cloud II', 'Gaming headset with surround sound', 99.99, 50, 4),
(38, 'Belkin USB-C Hub', 'Multi-port hub for laptops and tablets', 59.99, 70, 4),
(39, 'Apple Magic Keyboard', 'Wireless keyboard for Apple devices', 129.00, 20, 4),
(40, 'SanDisk Extreme Portable SSD', 'Compact external SSD with 2TB capacity', 200.00, 15, 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Laptops', 'Various types of laptops, including ultrabooks, gaming laptops, and business laptops'),
(2, 'Computers', 'Desktop computers for home, office, and gaming'),
(3, 'Smartphones', 'Latest smartphones from top brands'),
(4, 'Accessories', 'Computer and smartphone accessories, such as cables, mice, keyboards, and chargers');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeksy dla tabeli `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeksy dla tabeli `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
