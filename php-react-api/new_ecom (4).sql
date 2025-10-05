-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2025 at 05:38 AM
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
-- Database: `new_ecom`
--

-- --------------------------------------------------------

--
-- Table structure for table `ecom_categories`
--

CREATE TABLE `ecom_categories` (
  `id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecom_categories`
--

INSERT INTO `ecom_categories` (`id`, `name`, `is_active`) VALUES
(1, 'Men', 1),
(2, 'Women', 1),
(7, 'Kids', 0),
(8, 'Electronic', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ecom_customers`
--

CREATE TABLE `ecom_customers` (
  `id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecom_customers`
--

INSERT INTO `ecom_customers` (`id`, `name`, `email`, `phone`, `address`) VALUES
(1, 'Cash Customer', '', '', ''),
(2, 'Raju', 'raju@maqil.com', '123456', 'Khulna');

-- --------------------------------------------------------

--
-- Table structure for table `ecom_orders`
--

CREATE TABLE `ecom_orders` (
  `id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `total_amount` float NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status_id` int(10) NOT NULL,
  `payment_method_id` int(10) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecom_orders`
--

INSERT INTO `ecom_orders` (`id`, `customer_id`, `total_amount`, `shipping_address`, `date`, `order_status_id`, `payment_method_id`) VALUES
(1, 1, 9500, '', '0000-00-00 00:00:00', 1, 1),
(2, 1, 3100, '', '0000-00-00 00:00:00', 1, 1),
(3, 1, 1200, '', '0000-00-00 00:00:00', 1, 1),
(4, 1, 1200, '', '2025-08-11 06:21:57', 2, 1),
(5, 2, 2400, '', '2025-08-12 03:20:14', 1, 1),
(6, 1, 1200, '', '2025-08-12 03:23:30', 1, 1),
(7, 1, 3100, '', '2025-08-12 04:19:53', 3, 1),
(8, 1, 3100, '', '2025-08-12 04:20:20', 1, 1),
(9, 1, 4300, 'Dhaka', '2025-08-12 04:53:43', 1, 1),
(10, 1, 4300, 'Mirpur', '2025-08-12 04:56:23', 1, 1),
(11, 1, 1200, '', '2025-08-12 05:47:51', 1, 1),
(12, 2, 1200, '', '2025-09-12 05:56:02', 1, 1),
(13, 1, 5000, '', '2025-09-12 05:56:25', 1, 1),
(14, 1, 3100, 'fgfd', '0000-00-00 00:00:00', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ecom_order_details`
--

CREATE TABLE `ecom_order_details` (
  `id` int(10) NOT NULL,
  `order_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `price` float NOT NULL,
  `qty` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecom_order_details`
--

INSERT INTO `ecom_order_details` (`id`, `order_id`, `product_id`, `price`, `qty`) VALUES
(1, 9, 3, 1200, 2),
(2, 10, 3, 1200, 3),
(3, 10, 2, 1900, 1),
(4, 11, 1, 1200, 1),
(5, 12, 1, 1200, 1),
(6, 13, 2, 1900, 2),
(7, 13, 1, 1200, 1),
(8, 14, 1, 1200, 4),
(9, 14, 2, 1900, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ecom_products`
--

CREATE TABLE `ecom_products` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(10) NOT NULL,
  `price` float NOT NULL,
  `discount` float NOT NULL,
  `quantity` int(10) DEFAULT 0,
  `reorder_point` int(10) NOT NULL,
  `product_tag_id` int(10) NOT NULL DEFAULT 1,
  `photo` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecom_products`
--

INSERT INTO `ecom_products` (`id`, `name`, `category_id`, `price`, `discount`, `quantity`, `reorder_point`, `product_tag_id`, `photo`, `is_active`) VALUES
(1, 'Blue T-shirt', 1, 1200, 0, 50, 5, 1, 'uploads/products/20250809-054710.jpg', 1),
(2, 'Silk Saree', 2, 2000, 100, 20, 3, 1, 'uploads/products/20250809-061834.jpg', 1),
(3, 'Red Kamiz', 2, 1200, 0, 15, 5, 1, 'uploads/products/20250809-061945.png', 1),
(4, 'Apple iPad Mini', 8, 100000, 1500, 5, 2, 3, 'uploads/products/20250825-061356.png', 1),
(5, 'EOS Rebel T7i Kit', 8, 80000, 0, 10, 5, 2, 'uploads/products/20250825-061450.png', 1),
(6, 'Headset', 8, 4000, 500, 20, 8, 3, 'uploads/products/20250825-061602.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ecom_product_tags`
--

CREATE TABLE `ecom_product_tags` (
  `id` int(10) NOT NULL,
  `title` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecom_product_tags`
--

INSERT INTO `ecom_product_tags` (`id`, `title`) VALUES
(1, 'None'),
(2, 'Featured'),
(3, 'Offer');

-- --------------------------------------------------------

--
-- Table structure for table `ecom_roles`
--

CREATE TABLE `ecom_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecom_roles`
--

INSERT INTO `ecom_roles` (`id`, `name`) VALUES
(1, 'Admin'),
(4, 'Vendor'),
(5, 'Sr. Manager'),
(6, 'Sales Officer'),
(8, 'Editor');

-- --------------------------------------------------------

--
-- Table structure for table `ecom_users`
--

CREATE TABLE `ecom_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ecom_users`
--

INSERT INTO `ecom_users` (`id`, `name`, `email`, `password`, `role_id`, `address`, `photo`) VALUES
(1, 'Keya', 'keya@mail.com', '', 1, 'Dhaka', 'uploads/users/1.png'),
(2, 'Ridoy', 'ridoy@mail.com', '', 3, NULL, ''),
(8, 'Jakir', 'jakir.sr@mail.com', '', 5, 'Palton', ''),
(9, 'Riyaz', 'abc@gmail.com', '', 2, 'Khulna', ''),
(10, 'Mitu', 'mity@gmail.com', '', 3, '', ''),
(12, 'Raju', 'raj@mail.com', '', 8, '', ''),
(13, 'Raju', 'raj@mail.com', '', 4, '', ''),
(14, 'Raju', 'raj@mail.com', '', 6, 'Dhaka', ''),
(15, 'Keya', 'raj@mail.com', '', 4, '', ''),
(16, 'Keya', 'raj@mail.com', '', 4, 'Dhaka', 'uploads/users/20250804-085759.png'),
(17, 'Keya', 'raj@mail.com', '', 4, 'Dhaka', 'uploads/users/20250804-085825.png');

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

CREATE TABLE `order_status` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`id`, `name`) VALUES
(1, 'Pending'),
(2, 'Delivered'),
(3, 'Canceled');

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`id`, `name`) VALUES
(1, 'Cash'),
(2, 'bKash');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ecom_categories`
--
ALTER TABLE `ecom_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ecom_customers`
--
ALTER TABLE `ecom_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ecom_orders`
--
ALTER TABLE `ecom_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ecom_order_details`
--
ALTER TABLE `ecom_order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ecom_products`
--
ALTER TABLE `ecom_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ecom_product_tags`
--
ALTER TABLE `ecom_product_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ecom_roles`
--
ALTER TABLE `ecom_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ecom_users`
--
ALTER TABLE `ecom_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ecom_categories`
--
ALTER TABLE `ecom_categories`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ecom_customers`
--
ALTER TABLE `ecom_customers`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ecom_orders`
--
ALTER TABLE `ecom_orders`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ecom_order_details`
--
ALTER TABLE `ecom_order_details`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ecom_products`
--
ALTER TABLE `ecom_products`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ecom_product_tags`
--
ALTER TABLE `ecom_product_tags`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ecom_roles`
--
ALTER TABLE `ecom_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ecom_users`
--
ALTER TABLE `ecom_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_status`
--
ALTER TABLE `order_status`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
