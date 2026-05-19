-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 08, 2026 at 07:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_cms`
--
CREATE DATABASE IF NOT EXISTS `restaurant_cms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `restaurant_cms`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Fine Dining'),
(2, 'Casual Dining'),
(3, 'Fast Food'),
(4, 'Coffee & Cafe'),
(5, 'Ethnic Cuisine');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `restaurant_id`, `user_name`, `comment_text`, `created_at`) VALUES
(1, 1, 'testing1', 'I am groot', '2026-04-16 23:46:15'),
(2, 1, 'testing_2', 'testing_again!', '2026-04-17 03:43:34'),
(3, 1, 'testing1', 'test', '2026-04-17 03:58:56'),
(4, 3, 'testing1', 'again?', '2026-04-17 04:28:51'),
(5, 3, 'testin2', 'yes , me again', '2026-04-17 04:35:21'),
(6, 1, 'testing3', 'me!!!!!', '2026-04-17 04:47:37'),
(7, 1, 'testing4', 'hi', '2026-04-17 04:52:16'),
(8, 1, 'testing5', 'Again', '2026-04-17 04:57:49'),
(9, 1, 'testing6', 'fuck!!!!!', '2026-04-17 04:59:46'),
(10, 1, 'testing6', 'fgrgg', '2026-04-17 05:01:49'),
(11, 3, 'testing3', 'wdad', '2026-04-17 05:02:52'),
(12, 5, 'testing1', 'fr', '2026-04-17 05:10:29'),
(13, 10, 'testing1', 'hg', '2026-04-17 05:13:58'),
(14, 4, 'testing1', 'hi', '2026-04-17 05:25:34'),
(15, 8, 'testing1', 'email', '2026-04-17 05:28:46'),
(16, 1, 'testing31', 'dw', '2026-04-17 05:34:53'),
(17, 8, 'testing3', 'hu', '2026-04-17 05:36:54');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `address`, `phone`, `image_path`, `created_at`, `updated_at`, `category_id`, `website`) VALUES
(1, 'The Forks Market', 'A historic market place with diverse food vendors.', '1 Forks Market Rd, Winnipeg, MB R3C 4T4', '204-957-9080', NULL, '2026-04-15 20:44:49', '2026-04-17 04:10:42', 2, 'https://theforks.com/market'),
(2, 'Chopstix', 'Popular Asian fusion restaurant with great noodle dishes.', '236 Portage Ave, Winnipeg, MB R3B 2E5', '204-956-7200', NULL, '2026-04-15 20:44:49', '2026-04-17 04:24:09', 4, 'https://www.chopstixwinnipeg.com'),
(3, 'Caffe Medici', 'Artisan coffee and pastries in a cozy atmosphere. Testingx2', '494 Portage Ave, Winnipeg, MB R3B 2E3', '204-949-1177', NULL, '2026-04-15 20:44:49', '2026-04-17 04:24:23', 4, 'https://www.caffemedici.ca'),
(4, 'El Parian', 'Traditional Mexican cuisine with a vibrant atmosphere.', '1150 Regent Ave W, Winnipeg, MB R2C 4M6', '204-632-6060', NULL, '2026-04-15 20:44:49', '2026-04-17 04:24:37', 5, 'https://www.elparian.com'),
(5, 'Kyo-Ji', 'Japanese sushi and hibachi grill.', '308 Portage Ave, Winnipeg, MB R3B 2E5', '204-986-7200', NULL, '2026-04-15 20:44:49', '2026-04-17 04:24:49', 5, 'https://www.kyoji.ca'),
(6, 'The Keg Steakhouse + Bar', 'Premium steaks and seafood in a lively setting.', '170 Portage Ave, Winnipeg, MB R3B 2C5', '204-947-7200', NULL, '2026-04-15 20:44:49', '2026-04-17 04:25:02', 1, 'https://www.kegsteakhouse.com'),
(7, 'Pizza Pizza', 'Classic pizza slices and whole pies.', '350 McDermot Ave, Winnipeg, MB R3B 0S9', '204-985-7437', NULL, '2026-04-15 20:44:49', '2026-04-17 04:25:10', 3, 'https://www.pizzapizza.ca'),
(8, 'Subway', 'Fresh sandwiches and salads made to order.', 'Various locations', '204-986-7437', NULL, '2026-04-15 20:44:49', '2026-04-17 04:25:20', 3, 'https://www.subway.com'),
(9, 'Sakura Sushi', 'Authentic Japanese sushi and sashimi.', '1010 Henderson Hwy, Winnipeg, MB R2G 1M8', '204-256-7777', NULL, '2026-04-15 20:44:49', '2026-04-17 04:25:31', 5, 'https://www.sakurasushi.ca'),
(10, 'Dairy Queen', 'Classic ice cream, burgers, and shakes.', '1055 Regent Ave W, Winnipeg, MB R2C 4M8', '204-632-6060', NULL, '2026-04-15 20:44:49', '2026-04-17 04:25:40', 3, 'https://www.dairyqueen.ca'),
(11, 'testing_1', 'For teting only!!!', 'testing avemue', '911911911', 'restaurant_69e158bd9a41d9.52319358.jpg', '2026-04-16 21:46:37', '2026-04-16 22:16:38', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-04-15 20:44:49'),
(2, 'admin1', '$2y$10$dONCYXjm.E6GnpsNBJrwFu9hX55Kvc.RXAUaQWhopvzN28AVNa8Oi', 'admin', '2026-04-16 22:27:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
