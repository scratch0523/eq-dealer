-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2024 at 02:11 PM
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
-- Database: `equipride_dealer`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`, `status`, `created_at`) VALUES
(1, 'admin@gmail.com', '$2y$10$aH9UW8PDlm930S3hIzeuwO7hPM1SXvV3kj0iwI5bvQyrGx65xPg9C', 'Created', '2023-11-20 22:19:20');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_type` varchar(100) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_type`, `category_name`, `status`, `created_at`) VALUES
(1, 'horse', 'Boots', 'Created', '2024-01-09 14:53:38'),
(2, 'rider', 'Coat', 'Created', '2024-01-09 18:33:45');

-- --------------------------------------------------------

--
-- Table structure for table `color_product`
--

CREATE TABLE `color_product` (
  `color_product_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_category_type` varchar(255) NOT NULL,
  `product_category_name` varchar(255) NOT NULL,
  `product_sub_category_name` varchar(255) NOT NULL,
  `product_color` varchar(100) NOT NULL,
  `product_description` text NOT NULL,
  `product_specification` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `color_product`
--

INSERT INTO `color_product` (`color_product_id`, `product_id`, `product_name`, `product_category_type`, `product_category_name`, `product_sub_category_name`, `product_color`, `product_description`, `product_specification`, `created_at`) VALUES
(17, 15, 'Riding Coat', 'rider', 'Coat', 'Big Coat', 'Black', 'fv sdf sdf sdfs df dsf', 'ds fsdf fds sfd', '2024-01-10 11:45:26'),
(22, 15, 'Riding Coat', 'rider', 'Coat', 'Big Coat', 'White Pattern', 'fv sdf sdf sdfs df dsf', 'ds fsdf fds sfd', '2024-01-10 12:04:34'),
(23, 16, 'test product 2', 'horse', 'Boots', 'Riding Boot', 'Black', 'g fdg ', 'fdg dfg dfg', '2024-01-10 17:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `color_product_details`
--

CREATE TABLE `color_product_details` (
  `color_product_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_product_id` int(11) NOT NULL,
  `product_color` varchar(100) NOT NULL,
  `product_size` varchar(100) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_msrp_price` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `color_product_details`
--

INSERT INTO `color_product_details` (`color_product_detail_id`, `product_id`, `color_product_id`, `product_color`, `product_size`, `product_quantity`, `product_price`, `product_msrp_price`, `created_at`) VALUES
(17, 15, 17, 'Black', '43', 24354, 345, 34543, '2024-01-10 11:45:27'),
(22, 15, 22, 'White Pattern', '43', 24354, 345, 34543, '2024-01-10 12:04:34'),
(24, 16, 23, 'Black', '213', 32, 1, 1, '2024-01-10 17:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `color_product_images`
--

CREATE TABLE `color_product_images` (
  `color_product_image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_product_id` int(11) NOT NULL,
  `product_image` varchar(200) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `color_product_images`
--

INSERT INTO `color_product_images` (`color_product_image_id`, `product_id`, `color_product_id`, `product_image`, `created_at`) VALUES
(13, 15, 17, '20240110_659e35fef1254_800x700-1.jpg', '2024-01-10 11:45:26'),
(14, 15, 17, '20240110_659e35fef2b59_800x700-2.jpg', '2024-01-10 11:45:26'),
(24, 15, 22, '20240110_659e3a7a9d203_800x700-10.jpg', '2024-01-10 12:04:34'),
(25, 15, 22, '20240110_659e3a7a9e6f6_800x700-15.jpg', '2024-01-10 12:04:34'),
(26, 15, 22, '20240110_659e3c1bcd224_800x700-17.jpg', '2024-01-10 12:11:31'),
(27, 15, 22, '20240110_659e66648a6b1_800x700-1.jpg', '2024-01-10 15:11:56'),
(28, 15, 22, '20240110_659e66648a9ff_800x700-2.jpg', '2024-01-10 15:11:56'),
(29, 15, 22, '20240110_659e66648ac8d_800x700-3.jpg', '2024-01-10 15:11:56'),
(30, 15, 22, '20240110_659e66648aefd_800x700-4.jpg', '2024-01-10 15:11:56'),
(31, 16, 23, '20240110_659e8c294e35d_800x700-1.jpg', '2024-01-10 17:53:05'),
(32, 16, 23, '20240110_659e8c2950c03_800x700-3.jpg', '2024-01-10 17:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `dealer_cart`
--

CREATE TABLE `dealer_cart` (
  `cart_id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_product_id` int(11) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_color` varchar(255) NOT NULL,
  `product_size` varchar(100) NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_msrp_price` int(11) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `cart_quantity` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_profile_image`
--

CREATE TABLE `dealer_profile_image` (
  `profile_image_id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dealer_profile_image`
--

INSERT INTO `dealer_profile_image` (`profile_image_id`, `dealer_id`, `profile_image`, `created_at`) VALUES
(1, 1, '20240108_111046.jpg', '2024-01-05 16:37:08');

-- --------------------------------------------------------

--
-- Table structure for table `dealer_register`
--

CREATE TABLE `dealer_register` (
  `dealer_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` int(11) NOT NULL,
  `password` varchar(100) NOT NULL,
  `confirmpassword` varchar(100) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dealer_register`
--

INSERT INTO `dealer_register` (`dealer_id`, `firstname`, `lastname`, `gender`, `email`, `phone`, `password`, `confirmpassword`, `company_name`, `address`, `city`, `postcode`, `country`, `status`, `created_at`) VALUES
(1, 'Jebin', 'Raj', 'Male', 'jebinr82@gmail.com', 3214324, '$2y$10$AT1iKbYtm5aZQLjUslo6retj1jFEIj3FxUHqCNDPhEqLaUwCnuTb2', 'dev@123', 'scratch', 'karungal', 'colachel', '324324', 'India', 'Approved', '2024-01-10 14:17:32');

-- --------------------------------------------------------

--
-- Table structure for table `new_product_updates`
--

CREATE TABLE `new_product_updates` (
  `product_updates_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_product_updates`
--

INSERT INTO `new_product_updates` (`product_updates_id`, `product_id`, `created_at`) VALUES
(1, 1, '2024-01-09'),
(2, 2, '2024-01-09'),
(3, 3, '2024-01-10'),
(4, 4, '2024-01-10'),
(5, 5, '2024-01-10'),
(6, 7, '2024-01-10'),
(7, 8, '2024-01-10'),
(8, 9, '2024-01-10'),
(9, 10, '2024-01-10'),
(10, 11, '2024-01-10'),
(11, 12, '2024-01-10'),
(12, 13, '2024-01-10'),
(13, 14, '2024-01-10'),
(14, 15, '2024-01-10'),
(15, 16, '2024-01-10');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_category_type` varchar(100) NOT NULL,
  `product_category_name` varchar(100) NOT NULL,
  `product_sub_category_name` varchar(100) NOT NULL,
  `product_color` varchar(100) NOT NULL,
  `product_description` text NOT NULL,
  `product_specification` text NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_msrp_price` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_category_type`, `product_category_name`, `product_sub_category_name`, `product_color`, `product_description`, `product_specification`, `product_price`, `product_msrp_price`, `created_at`) VALUES
(15, 'Riding Coat', 'rider', 'Coat', 'Big Coat', 'Black', 'fv sdf sdf sdfs df dsf', 'ds fsdf fds sfd', 345, 34543, '2024-01-10 11:45:26'),
(16, 'test product 2', 'horse', 'Boots', 'Riding Boot', 'Black', 'g fdg ', 'fdg dfg dfg', 1, 1, '2024-01-10 17:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `product_alert`
--

CREATE TABLE `product_alert` (
  `product_alert_id` int(11) NOT NULL,
  `color_product_id` int(11) NOT NULL,
  `cart_quantity` int(11) NOT NULL,
  `product_size` varchar(100) NOT NULL,
  `arrive_date` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_alert`
--

INSERT INTO `product_alert` (`product_alert_id`, `color_product_id`, `cart_quantity`, `product_size`, `arrive_date`, `created_at`) VALUES
(7, 2, 50, '43', '2024-01-16', '2024-01-09 18:45:53');

-- --------------------------------------------------------

--
-- Table structure for table `product_color`
--

CREATE TABLE `product_color` (
  `color_id` int(11) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `color_value` varchar(100) NOT NULL,
  `color_type` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_color`
--

INSERT INTO `product_color` (`color_id`, `color_name`, `color_value`, `color_type`, `created_at`) VALUES
(1, 'Black', '2.jpg', 'image', '2024-01-09 14:55:16'),
(2, 'White Pattern', '1.jpg', 'image', '2024-01-09 18:39:37');

-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE `product_details` (
  `product_details_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_size` varchar(255) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_msrp_price` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_details`
--

INSERT INTO `product_details` (`product_details_id`, `product_id`, `product_size`, `product_quantity`, `product_price`, `product_msrp_price`, `created_at`) VALUES
(16, 15, '43', 24354, 345, 345, '2024-01-10 11:45:26'),
(17, 16, '213', 32, 1, 1, '2024-01-10 17:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `product_image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`product_image_id`, `product_id`, `product_image`, `created_at`) VALUES
(40, 15, '20240110_659e35fef1254_800x700-1.jpg', '2024-01-10 11:45:26'),
(41, 15, '20240110_659e35fef2b59_800x700-2.jpg', '2024-01-10 11:45:26'),
(42, 16, '20240110_659e8c294e35d_800x700-1.jpg', '2024-01-10 17:53:05'),
(43, 16, '20240110_659e8c2950c03_800x700-3.jpg', '2024-01-10 17:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `product_orders`
--

CREATE TABLE `product_orders` (
  `order_id` int(11) NOT NULL,
  `unique_orderid` varchar(50) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_color` varchar(255) NOT NULL,
  `product_size` varchar(255) NOT NULL,
  `product_price` int(11) NOT NULL,
  `order_quantity` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `order_status` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_orders`
--

INSERT INTO `product_orders` (`order_id`, `unique_orderid`, `dealer_id`, `product_id`, `color_product_id`, `product_name`, `product_color`, `product_size`, `product_price`, `order_quantity`, `total_amount`, `order_status`, `created_at`) VALUES
(1, 'OrderEqp_000001', 1, 1, 1, 'Riding Boot Junior', 'Black', '14', 199, 40, 7960, 'Completed', '2024-01-09 18:43:02'),
(2, 'OrderEqp_000001', 1, 2, 2, 'Riding ', 'White Pattern', '43', 110, 15, 1650, 'Completed', '2024-01-09 18:43:02'),
(3, 'OrderEqp_000002', 1, 2, 2, 'Riding ', 'White Pattern', '43', 110, 50, 5500, 'Completed', '2024-01-09 18:45:49');

-- --------------------------------------------------------

--
-- Table structure for table `quick_orders`
--

CREATE TABLE `quick_orders` (
  `quick_orderid` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `order_file` varchar(200) NOT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_update`
--

CREATE TABLE `stock_update` (
  `stock_update_id` int(11) NOT NULL,
  `color_product_id` int(11) NOT NULL,
  `product_size` varchar(255) NOT NULL,
  `updated_quantity` int(11) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_category`
--

CREATE TABLE `sub_category` (
  `sub_category_id` int(11) NOT NULL,
  `category_type` varchar(100) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `sub_category_name` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_category`
--

INSERT INTO `sub_category` (`sub_category_id`, `category_type`, `category_name`, `sub_category_name`, `status`, `created_at`) VALUES
(1, 'horse', 'Boots', 'Riding Boot', 'Created', '2024-01-09 14:53:57'),
(2, 'rider', 'Coat', 'Big Coat', 'Created', '2024-01-09 18:34:00');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `dealer_id`, `product_id`, `product_name`, `product_description`, `created_at`) VALUES
(2, 1, 15, 'Riding Coat', 'fv sdf sdf sdfs df dsf', '2024-01-10 16:51:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `color_product`
--
ALTER TABLE `color_product`
  ADD PRIMARY KEY (`color_product_id`);

--
-- Indexes for table `color_product_details`
--
ALTER TABLE `color_product_details`
  ADD PRIMARY KEY (`color_product_detail_id`);

--
-- Indexes for table `color_product_images`
--
ALTER TABLE `color_product_images`
  ADD PRIMARY KEY (`color_product_image_id`);

--
-- Indexes for table `dealer_cart`
--
ALTER TABLE `dealer_cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `dealer_profile_image`
--
ALTER TABLE `dealer_profile_image`
  ADD PRIMARY KEY (`profile_image_id`);

--
-- Indexes for table `dealer_register`
--
ALTER TABLE `dealer_register`
  ADD PRIMARY KEY (`dealer_id`);

--
-- Indexes for table `new_product_updates`
--
ALTER TABLE `new_product_updates`
  ADD PRIMARY KEY (`product_updates_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_alert`
--
ALTER TABLE `product_alert`
  ADD PRIMARY KEY (`product_alert_id`);

--
-- Indexes for table `product_color`
--
ALTER TABLE `product_color`
  ADD PRIMARY KEY (`color_id`);

--
-- Indexes for table `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`product_details_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`product_image_id`);

--
-- Indexes for table `product_orders`
--
ALTER TABLE `product_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `quick_orders`
--
ALTER TABLE `quick_orders`
  ADD PRIMARY KEY (`quick_orderid`);

--
-- Indexes for table `stock_update`
--
ALTER TABLE `stock_update`
  ADD PRIMARY KEY (`stock_update_id`);

--
-- Indexes for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD PRIMARY KEY (`sub_category_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `color_product`
--
ALTER TABLE `color_product`
  MODIFY `color_product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `color_product_details`
--
ALTER TABLE `color_product_details`
  MODIFY `color_product_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `color_product_images`
--
ALTER TABLE `color_product_images`
  MODIFY `color_product_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `dealer_cart`
--
ALTER TABLE `dealer_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_profile_image`
--
ALTER TABLE `dealer_profile_image`
  MODIFY `profile_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dealer_register`
--
ALTER TABLE `dealer_register`
  MODIFY `dealer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `new_product_updates`
--
ALTER TABLE `new_product_updates`
  MODIFY `product_updates_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_alert`
--
ALTER TABLE `product_alert`
  MODIFY `product_alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product_color`
--
ALTER TABLE `product_color`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_details`
--
ALTER TABLE `product_details`
  MODIFY `product_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `product_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `product_orders`
--
ALTER TABLE `product_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quick_orders`
--
ALTER TABLE `quick_orders`
  MODIFY `quick_orderid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_update`
--
ALTER TABLE `stock_update`
  MODIFY `stock_update_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_category`
--
ALTER TABLE `sub_category`
  MODIFY `sub_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
