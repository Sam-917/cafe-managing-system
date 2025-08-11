-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 05:37 AM
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
-- Database: `cafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `display_order`, `is_active`) VALUES
(1, 'Coffee', 'Our handcrafted coffee selections', 1, 1),
(2, 'Tea', 'Premium loose-leaf teas', 2, 1),
(3, 'Breakfast', 'Morning sandwiches & pastries', 3, 1),
(4, 'Lunch', 'Paninis and salads', 4, 1),
(5, 'Bakery', 'Freshly baked goods', 5, 1),
(6, 'Seasonal', 'Limited-time offerings', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `nutritional_info`
--

CREATE TABLE `nutritional_info` (
  `nutrition_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `serving_size` varchar(50) DEFAULT NULL,
  `caffeine_mg` int(11) DEFAULT NULL,
  `sugar_g` int(11) DEFAULT NULL,
  `fat_g` int(11) DEFAULT NULL,
  `protein_g` int(11) DEFAULT NULL,
  `allergens` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nutritional_info`
--

INSERT INTO `nutritional_info` (`nutrition_id`, `product_id`, `serving_size`, `caffeine_mg`, `sugar_g`, `fat_g`, `protein_g`, `allergens`) VALUES
(1, 1, '1 shot', 75, 0, 0, 0, 'None'),
(2, 2, '12oz', 150, 12, 8, 10, 'Dairy'),
(3, 3, '12oz', 175, 28, 6, 8, 'Dairy'),
(4, 4, '12oz', 80, 10, 5, 6, 'Dairy'),
(5, 5, '12oz', 50, 22, 5, 7, 'Dairy, Tree Nuts'),
(6, 6, '1 serving', 2, 5, 22, 8, 'Gluten'),
(7, 7, '1 sandwich', 45, 3, 28, 22, 'Gluten, Dairy, Egg'),
(8, 8, '1 panini', 5, 4, 18, 26, 'Gluten, Dairy'),
(9, 10, '1 muffin', 5, 32, 16, 5, 'Gluten, Dairy, Egg'),
(10, 11, '12oz', 150, 32, 7, 6, 'Dairy'),
(11, 12, '12oz', 125, 38, 8, 7, 'Dairy'),
(12, 13, '16oz', 120, 18, 4, 3, 'Dairy'),
(13, 15, '1 cookie', 0, 22, 8, 2, 'Gluten, Dairy, Molasses');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `order_type` enum('dine_in','takeaway','delivery') NOT NULL,
  `table_number` varchar(11) DEFAULT NULL,
  `pickup_location_id` int(11) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `delivery_time` varchar(50) DEFAULT NULL,
  `payment_method` enum('credit_card','cash','ewallet') NOT NULL DEFAULT 'cash',
  `total_amount` decimal(10,2) NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `status` enum('pending','preparing','ready','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `phone`, `email`, `order_type`, `table_number`, `pickup_location_id`, `delivery_address`, `delivery_time`, `payment_method`, `total_amount`, `special_instructions`, `status`, `created_at`) VALUES
(1, 'Rahah', '+60 174911297', NULL, 'takeaway', NULL, 2, NULL, NULL, 'credit_card', 90.00, 'Jangan letak sayur.', 'preparing', '2025-08-11 03:27:54'),
(2, 'Syikin', '+60 174911297', 'someting983@gmail.com', 'dine_in', 'B1', NULL, NULL, NULL, 'credit_card', 45.00, NULL, 'completed', '2025-08-11 03:27:54');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `special_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `base_price`, `calories`, `image_url`, `is_featured`, `is_active`, `display_order`) VALUES
(1, 1, 'Espresso', ' Rich single-origin espresso shot ', 3.50, 5, 'espresso.png', 1, 1, 0),
(2, 1, 'Caffè Latte', 'Espresso with steamed milk', 4.25, 190, 'latte.png', 1, 1, NULL),
(3, 1, 'Caramel Macchiato', 'Vanilla syrup with espresso and caramel drizzle', 5.25, 250, 'macchiato.png', 0, 1, NULL),
(4, 2, 'Matcha Latte', 'Ceremonial-grade matcha with milk', 4.75, 180, 'matcha.png', 1, 1, NULL),
(5, 2, 'Chai Latte', 'Spiced black tea with steamed milk', 4.50, 240, 'chai.png', 0, 1, NULL),
(6, 3, 'Avocado Toast', 'Sourdough with avocado, chili flakes', 7.50, 320, 'avocado_toast.png', 1, 1, NULL),
(7, 3, 'Breakfast Sandwich', 'Egg, cheddar & bacon on croissant', 6.75, 450, 'breakfast_sandwich.png', 0, 1, NULL),
(8, 4, 'Caprese Panini', 'Mozzarella, tomato & pesto', 8.25, 520, 'caprese.png', 1, 1, NULL),
(9, 4, 'Chicken Caesar Wrap', 'Grilled chicken with romaine', 8.75, 580, 'caesar_wrap.png', 0, 1, NULL),
(10, 5, 'Croissant', 'Buttery French-style', 3.25, 310, 'croissant.png', 1, 1, NULL),
(11, 5, 'Blueberry Muffin', 'Fresh blueberries in muffin', 3.75, 420, 'blueberry_muffin.png', 0, 1, NULL),
(12, 6, 'Pumpkin Spice Latte', 'Signature fall drink with spices', 5.95, 380, 'pumpkin_spice.png', 1, 1, NULL),
(13, 6, 'Peppermint Mocha', 'Holiday chocolate-mint espresso', 5.75, 420, 'peppermint_mocha.png', 1, 1, NULL),
(14, 6, 'Iced Lavender Latte', 'Floral summer cold brew', 5.25, 210, 'lavender_latte.png', 0, 1, NULL),
(15, 6, 'Watermelon Mint Tea', 'Refreshing summer cooler', 4.95, 90, 'watermelon_tea.png', 0, 1, NULL),
(16, 6, 'Gingerbread Cookie', 'Festive molasses spice cookie', 3.50, 280, 'gingerbread.png', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_options`
--

CREATE TABLE `product_options` (
  `option_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `option_type` enum('size','milk','syrup','topping','temperature') NOT NULL,
  `name` varchar(50) NOT NULL,
  `additional_price` decimal(10,2) DEFAULT 0.00,
  `calories_addition` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_options`
--

INSERT INTO `product_options` (`option_id`, `product_id`, `option_type`, `name`, `additional_price`, `calories_addition`) VALUES
(1, 1, 'size', 'Single', 0.00, 5),
(2, 1, 'size', 'Double', 1.00, 10),
(3, 2, 'size', '12oz', 0.00, 190),
(4, 2, 'size', '16oz', 0.75, 240),
(5, 2, 'milk', 'Almond', 0.50, 30),
(6, 2, 'milk', 'Oat', 0.50, 50),
(7, 2, 'syrup', 'Vanilla', 0.75, 80),
(8, 4, 'size', '12oz', 0.00, 180),
(9, 4, 'size', '16oz', 0.75, 220),
(10, 4, 'milk', 'Coconut', 0.75, 40),
(11, 7, 'topping', 'Extra Bacon', 1.50, 120),
(12, 7, 'topping', 'Avocado', 1.25, 90);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `table_id` varchar(10) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `guests` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('confirmed','pending','cancelled','completed','no-show') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `confirmation_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_locations`
--

CREATE TABLE `restaurant_locations` (
  `location_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `description` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `rating` decimal(10,1) NOT NULL,
  `reviews` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_locations`
--

INSERT INTO `restaurant_locations` (`location_id`, `name`, `address`, `description`, `city`, `phone`, `opening_time`, `closing_time`, `is_active`, `rating`, `reviews`) VALUES
(1, 'Downtown Café', '123 Main St', '', 'New York', '+1 555-1234', '08:00:00', '22:00:00', 1, 4.3, 312),
(2, 'Mall Branch', '456 Shopping Ave', '', 'Chicago', '+1 555-5678', '10:00:00', '21:00:00', 1, 4.5, 121),
(3, 'Waterfront Café', '789 Harbor Rd', '', 'Seattle', '+1 555-9012', '07:00:00', '20:00:00', 1, 4.8, 518);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `location_id` int(11) NOT NULL,
  `table_id` varchar(10) NOT NULL,
  `table_number` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` enum('standard','round','booth','private','bar','outdoor') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `x_position` int(11) DEFAULT NULL,
  `y_position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`location_id`, `table_id`, `table_number`, `capacity`, `description`, `type`, `is_active`, `x_position`, `y_position`) VALUES
(3, 'B1', 'B1', 4, 'Private booth seating with comfortable cushions and intimate setting.', 'booth', 1, 100, 150),
(3, 'B2', 'B2', 4, 'Cozy booth perfect for private conversations and romantic dinners.', 'booth', 1, 100, 250),
(3, 'B3', 'B3', 4, 'Comfortable booth with excellent service access.', 'booth', 1, 250, 200),
(2, 'C1', 'C1', 4, 'Central location with easy access and lively atmosphere.', 'standard', 1, 200, 150),
(2, 'C2', 'C2', 6, 'Large round table perfect for family gatherings and celebrations.', 'round', 1, 200, 250),
(2, 'C3', 'C3', 4, 'Well-positioned table in the heart of the restaurant.', 'standard', 1, 300, 200),
(1, 'P1', 'P1', 8, 'Exclusive table for large groups with enhanced privacy and service.', 'private', 1, 350, 100),
(1, 'P2', 'P2', 8, 'Premium table for special occasions and business meetings.', 'private', 1, 350, 220),
(1, 'W1', 'W1', 2, 'Intimate table with beautiful street view, perfect for couples.', 'standard', 1, 50, 100),
(1, 'W2', 'W2', 2, 'Cozy window seat with natural lighting throughout the day.', 'standard', 1, 50, 180),
(1, 'W3', 'W3', 4, 'Spacious window table ideal for small groups with scenic views.', 'standard', 1, 50, 260);

-- --------------------------------------------------------

--
-- Table structure for table `seasonal_items`
--

CREATE TABLE `seasonal_items` (
  `special_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_limited_edition` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seasonal_items`
--

INSERT INTO `seasonal_items` (`special_id`, `product_id`, `start_date`, `end_date`, `is_limited_edition`) VALUES
(1, 11, '2023-11-01', '2024-01-31', 1),
(2, 12, '2023-11-01', '2024-01-31', 1),
(3, 13, '2024-05-15', '2024-08-31', 0),
(4, 14, '2024-05-15', '2024-08-31', 0),
(5, 15, '2023-12-01', '2023-12-31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `username`, `password_hash`, `role`, `is_active`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'Admin', 'User', 'admin@example.com', '1234567890', 'admin', '$2y$10$081GjWLPcFgbaA1ryKJVxOzpTMAGtp4KRqI0r17qUe0btD00/92gi', 'admin', 1, '2025-08-11 03:13:15', '2025-08-11 03:13:33', '2025-08-11 11:13:33'),
(2, 'Alya', 'Diayana', 'ali123@gmail.com', '+60 174911297', 'Aliyaya', '$2y$10$UlAcvY6WTL1GB89azCCrtelrmCdUR3WwtcA8c2Kh9r5PQKBB3Wgya', 'customer', 1, '2025-08-11 03:32:51', '2025-08-11 03:34:07', '2025-08-11 11:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `nutritional_info`
--
ALTER TABLE `nutritional_info`
  ADD PRIMARY KEY (`nutrition_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `pickup_location_id` (`pickup_location_id`),
  ADD KEY `table_number` (`table_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_options`
--
ALTER TABLE `product_options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `restaurant_locations`
--
ALTER TABLE `restaurant_locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`table_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `seasonal_items`
--
ALTER TABLE `seasonal_items`
  ADD PRIMARY KEY (`special_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user_sessions` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `nutritional_info`
--
ALTER TABLE `nutritional_info`
  MODIFY `nutrition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_options`
--
ALTER TABLE `product_options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_locations`
--
ALTER TABLE `restaurant_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seasonal_items`
--
ALTER TABLE `seasonal_items`
  MODIFY `special_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nutritional_info`
--
ALTER TABLE `nutritional_info`
  ADD CONSTRAINT `nutritional_info_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`pickup_location_id`) REFERENCES `restaurant_locations` (`location_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`table_number`) REFERENCES `restaurant_tables` (`table_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `product_options`
--
ALTER TABLE `product_options`
  ADD CONSTRAINT `product_options_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`table_id`);

--
-- Constraints for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD CONSTRAINT `restaurant_tables_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `restaurant_locations` (`location_id`);

--
-- Constraints for table `seasonal_items`
--
ALTER TABLE `seasonal_items`
  ADD CONSTRAINT `seasonal_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
