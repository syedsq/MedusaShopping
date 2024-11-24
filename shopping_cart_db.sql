-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 16, 2024 at 05:34 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopping_cart_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `product_id`, `quantity`, `session_id`) VALUES
(1, 1, 12, '8qa0p8c343pgvp6rc94tjgbcb3');

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_codes`
--

INSERT INTO `discount_codes` (`id`, `code`, `discount_percentage`, `created_at`) VALUES
(1, 'Dogru', 0.25, '2024-09-10 22:29:13'),
(2, 'Drjava', 0.50, '2024-09-10 22:29:13'),
(3, 'UTSA', 1.00, '2024-09-10 22:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `created_at`) VALUES
(1, 1, 10.81, '2024-09-10 22:09:47'),
(2, 1, 32.44, '2024-09-10 22:14:26'),
(3, 1, 259.76, '2024-09-10 22:19:44'),
(4, 1, 194.82, '2024-09-10 22:37:08'),
(5, 1, 17.31, '2024-09-10 22:42:55'),
(6, 1, 13.52, '2024-09-10 22:48:49'),
(7, 1, 13.52, '2024-09-10 22:49:11'),
(8, 1, 329.96, '2024-09-10 23:47:04'),
(9, 1, 286.81, '2024-09-11 00:01:46'),
(10, 1, 411.24, '2024-09-11 00:48:26'),
(11, 1, 222.94, '2024-09-11 13:54:06'),
(12, 1, 281.39, '2024-09-11 14:05:02'),
(13, 1, 281.40, '2024-09-11 19:42:10'),
(14, 1, 324.70, '2024-09-16 14:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 4, 10, 3, 59.99),
(2, 5, 4, 1, 15.99),
(3, 6, 7, 1, 12.49),
(4, 7, 7, 1, 12.49),
(5, 8, 5, 4, 34.95),
(6, 8, 9, 3, 34.95),
(7, 9, 10, 4, 24.99),
(8, 9, 6, 1, 24.99),
(9, 10, 10, 5, 15.99),
(10, 10, 4, 5, 15.99),
(11, 11, 10, 3, 9.99),
(12, 11, 4, 1, 9.99),
(13, 11, 1, 1, 9.99),
(14, 12, 10, 2, 49.99),
(15, 12, 3, 3, 49.99),
(16, 12, 5, 1, 49.99),
(17, 13, 10, 3, 49.99),
(18, 13, 3, 1, 49.99),
(19, 13, 5, 1, 49.99),
(20, 14, 10, 5, 59.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `created_at`) VALUES
(1, 'Product 1', 'This is a description for Product 1. A fantastic item to have!', 9.99, 'pic1.jpg', '2024-09-10 19:37:24'),
(2, 'Product 2', 'This is a description for Product 2. Popular among customers.', 19.99, 'image2.jpg', '2024-09-10 19:37:24'),
(3, 'Product 3', 'This is a description for Product 3. It offers great value for its price.', 29.99, 'image3.jpg', '2024-09-10 19:37:24'),
(4, 'Product 4', 'This is a description for Product 4. A must-have for any household.', 15.99, 'image4.jpg', '2024-09-10 19:37:24'),
(5, 'Product 5', 'This is a description for Product 5. A premium item with excellent reviews.', 49.99, 'image5.jpg', '2024-09-10 19:37:24'),
(6, 'Product 6', 'This is a description for Product 6. Known for its durability and quality.', 24.99, 'image6.jpg', '2024-09-10 19:37:24'),
(7, 'Product 7', 'This is a description for Product 7. A budget-friendly choice for all.', 12.49, 'image7.jpg', '2024-09-10 19:37:24'),
(8, 'Product 8', 'This is a description for Product 8. Compact, lightweight, and versatile.', 22.75, 'image8.jpg', '2024-09-10 19:37:24'),
(9, 'Product 9', 'This is a description for Product 9. Top-rated in its category.', 34.95, 'image9.jpg', '2024-09-10 19:37:24'),
(10, 'Product 10', 'This is a description for Product 10. Designed for efficiency and style.', 59.99, 'image10.jpg', '2024-09-10 19:37:24');

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `product_id` int(11) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `address`, `created_at`) VALUES
(1, 'test', '$2y$10$R8TIwzGN5tLssOIzwycF2O.xShdRDXg1xPZDQKR0rhyTqH9MFI/GG', 'test@gmail.com', NULL, '2024-09-10 21:54:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;