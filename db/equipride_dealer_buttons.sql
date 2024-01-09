-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2024 at 02:06 PM
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
(28, 'horse', 'riding boot', 'Created', '2023-11-22 21:24:47'),
(29, 'horse', 'riding boot junior', 'Created', '2023-11-22 21:25:01'),
(30, 'horse', 'riding boot adult', 'Created', '2023-11-22 21:25:11'),
(31, 'horse', 'riding junior coat', 'Created', '2023-11-22 21:25:25'),
(32, 'horse', 'riding adult coat', 'Created', '2023-11-22 21:25:33'),
(33, 'dog', 'collar adult', 'Created', '2023-11-22 21:26:06'),
(34, 'dog', 'collar junior', 'Created', '2023-11-22 21:26:15'),
(36, 'rider', 'riding boot', 'Created', '2023-12-08 12:51:34'),
(37, 'dog', 'belt', 'Created', '2023-12-12 15:34:53'),
(38, 'horse', 'Test001', 'Created', '2023-12-12 16:21:28');

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
(23, 13, 'test ', 'rider', 'riding boot', 'long riding boot', 'Black', 'df dsf dsf', 'fd fdgd', '2024-01-06 18:03:45'),
(24, 13, 'test ', 'rider', 'riding boot', 'long riding boot', 'Red', 'df dsf dsf', 'fd fdgd', '2024-01-06 18:04:16'),
(25, 14, 'riding boot', 'horse', 'riding boot', 'long riding boots', 'blue', 'sdf sdf sd fssd', 'f sd fdsf ', '2024-01-08 11:38:17'),
(26, 15, 'test dog product', 'dog', 'collar adult', 'small collar', 'Red', 'ds fdsf sdf', 'dsf sdf ds', '2024-01-08 11:39:35');

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
(28, 13, 23, 'Black', '13', 92, 324, 234, '2024-01-06 18:03:45'),
(29, 13, 24, 'Red', '13', 100, 324, 234, '2024-01-06 18:04:16'),
(30, 14, 25, 'blue', 'sd', 324, 4, 234, '2024-01-08 11:38:17'),
(31, 15, 26, 'Red', '34', 1, 7, 432, '2024-01-08 11:39:35');

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
(1, 13, 23, '800x700-3.jpg', '2024-01-06 18:03:45'),
(2, 13, 24, '800x700-5.jpg', '2024-01-06 18:04:16'),
(3, 13, 24, '800x700-6.jpg', '2024-01-06 18:04:16'),
(4, 14, 25, '800x700-4.jpg', '2024-01-08 11:38:17'),
(5, 14, 25, '800x700-12.jpg', '2024-01-08 11:38:17'),
(6, 15, 26, '800x700-2.jpg', '2024-01-08 11:39:35'),
(7, 15, 26, '800x700-8.jpg', '2024-01-08 11:39:35');

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

--
-- Dumping data for table `dealer_cart`
--

INSERT INTO `dealer_cart` (`cart_id`, `dealer_id`, `product_id`, `color_product_id`, `product_image`, `product_name`, `product_color`, `product_size`, `product_price`, `product_msrp_price`, `product_quantity`, `cart_quantity`, `total_amount`, `created_at`) VALUES
(51, 1, 13, 23, '800x700-3.jpg', 'test ', 'Black', '13', 324, 234, 96, 3, 972, '2024-01-08 10:47:38');

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
(1, 'Dev', 'eloper', 'Male', 'jebinr82@gmail.com', 12348765, '$2y$10$epc6BLwYTgVXzO.Ndplxc.sW9g/yBziHLq1IqwgbChwSIItqGZ02i', 'dev@123', 'scratch', 'India', 'karungal', '342342', 'United Kingdom', 'Approved', '2024-01-03 12:55:25'),
(2, 'Jebin', 'Raj', 'Male', 'jebin.20@gmail.com', 12345678, '$2y$10$.ei06Jd4EF/3cmAaiOoA1.V3d7Kru/Od21868VmVxC/L/RFuQ37r.', 'jebin', 'scratch', 'karungal', 'chennai', '34234', 'India', 'Approved', '2024-01-04 16:12:24');

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
(1, 14, '2024-01-08'),
(2, 15, '2024-01-08');

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
(13, 'test ', 'rider', 'riding boot', 'long riding boot', 'Black', 'df dsf dsf', 'fd fdgd', 324, 234, '2024-01-06 18:03:45'),
(14, 'riding boot', 'horse', 'riding boot', 'long riding boots', 'blue', 'sdf sdf sd fssd', 'f sd fdsf ', 4, 234, '2024-01-08 11:38:17'),
(15, 'test dog product', 'dog', 'collar adult', 'small collar', 'Red', 'ds fdsf sdf', 'dsf sdf ds', 7, 432, '2024-01-08 11:39:35');

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
(1, 'Black', '#000000', 'code', '2023-12-14 14:32:19'),
(2, 'Red', '#d90d0d', 'code', '2023-12-14 14:32:26'),
(3, 'Yellow', '#dfed1d', 'code', '2023-12-14 14:32:38'),
(4, 'Pink', '#e316d2', 'code', '2023-12-14 14:32:57'),
(5, 'blaaa', '2.jpg', 'image', '2023-12-16 14:13:46'),
(6, 'qwerwq', '800x700-8.jpg', 'image', '2024-01-03 14:56:17'),
(7, 'white', '800x700-5.jpg', 'image', '2024-01-03 15:01:34'),
(8, 'wer', '800x700-12.jpg', 'image', '2024-01-03 15:03:12'),
(9, 'ewr', '800x700-4.jpg', 'image', '2024-01-03 15:03:26'),
(10, 'dsf', '800x700-8.jpg', 'image', '2024-01-03 15:03:40'),
(11, 'dsfsd', '800x700-4.jpg', 'image', '2024-01-03 15:14:20'),
(12, 'ewrewr', '800x700-7.jpg', 'image', '2024-01-03 15:17:25'),
(13, 'blue', '800x700-9.jpg', 'image', '2024-01-03 15:20:51'),
(14, 'test color', '4.jpg', 'image', '2024-01-03 16:11:24');

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
(14, 13, '13', 234, 324, 234, '2024-01-06 18:03:45'),
(15, 14, 'sd', 324, 4, 234, '2024-01-08 11:38:17'),
(16, 15, '34', 1, 7, 432, '2024-01-08 11:39:35');

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
(26, 13, '800x700-3.jpg', '2024-01-06 18:03:45'),
(27, 14, '800x700-4.jpg', '2024-01-08 11:38:17'),
(28, 14, '800x700-12.jpg', '2024-01-08 11:38:17'),
(29, 15, '800x700-2.jpg', '2024-01-08 11:39:35'),
(30, 15, '800x700-8.jpg', '2024-01-08 11:39:35');

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
(1, 'OrderEqp_000001', 1, 1, 1, 'test product ', 'Yellow', '14', 344, 324, 111456, 'Completed', '2024-01-06 15:54:54'),
(2, 'OrderEqp_000001', 1, 1, 2, 'test product ', 'Pink', '12', 344, 32, 11008, 'Completed', '2024-01-06 15:54:54'),
(3, 'OrderEqp_000002', 1, 13, 23, 'test ', 'Black', '13', 324, 234, 75816, 'Completed', '2024-01-06 18:13:00'),
(4, 'OrderEqp_000003', 1, 13, 23, 'test ', 'Black', '13', 324, 100, 32400, 'Completed', '2024-01-06 18:25:19'),
(5, 'OrderEqp_000004', 1, 13, 23, 'test ', 'Black', '13', 324, 500, 162000, 'Completed', '2024-01-06 18:27:08'),
(6, 'OrderEqp_000005', 1, 13, 24, 'test ', 'Red', '13', 324, 234, 75816, 'Completed', '2024-01-06 18:35:39'),
(7, 'OrderEqp_000006', 1, 13, 23, 'test ', 'Black', '13', 324, 4, 1296, 'Completed', '2024-01-08 10:47:12'),
(8, 'OrderEqp_000007', 1, 13, 23, 'test ', 'Black', '13', 324, 4, 1296, 'Completed', '2024-01-08 16:24:47');

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

--
-- Dumping data for table `quick_orders`
--

INSERT INTO `quick_orders` (`quick_orderid`, `dealer_id`, `order_file`, `status`, `created_at`) VALUES
(1, 2, 'day_2024-01-04.csv', 'Completed', '2024-01-04 16:14:26'),
(2, 2, 'day_2024-01-04.csv', 'Completed', '2024-01-04 16:16:23'),
(3, 2, 'file_example_XLS_10_2024-01-04.xls', 'Completed', '2024-01-04 16:16:28'),
(4, 2, 'file_example_XLS_10_2024-01-04.xls', 'Completed', '2024-01-04 16:16:50'),
(5, 2, 'file_example_XLS_10_2024-01-04_2024-01-04.xls', 'Completed', '2024-01-04 16:36:27'),
(6, 1, 'file_example_XLS_10_2024-01-04_2024-01-04_2024-01-05.xls', 'Completed', '2024-01-05 10:13:59');

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

--
-- Dumping data for table `stock_update`
--

INSERT INTO `stock_update` (`stock_update_id`, `color_product_id`, `product_size`, `updated_quantity`, `created_at`) VALUES
(1, 23, '13', 100, '2023-12-30'),
(2, 23, '13', 500, '2024-01-06'),
(3, 23, '13', 100, '2024-01-06'),
(4, 24, '13', 100, '2024-01-04');

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
(22, 'horse', 'riding boot', 'long riding boots', 'Created', '2023-11-22 21:27:15'),
(23, 'horse', 'riding boot', 'short riding boots', 'Created', '2023-11-22 21:27:27'),
(24, 'horse', 'riding junior coat', 'water proof coat', 'Created', '2023-11-22 21:27:51'),
(25, 'horse', 'riding junior coat', 'normal coat', 'Created', '2023-11-22 21:28:06'),
(28, 'dog', 'collar junior', 'big collar', 'Created', '2023-12-08 12:50:10'),
(29, 'dog', 'collar adult', 'small collar', 'Created', '2023-12-08 12:50:21'),
(30, 'rider', 'riding boot', 'long riding boot', 'Created', '2023-12-08 12:51:53'),
(31, 'horse', 'riding boot', 'tdfdsf', 'Created', '2023-12-12 16:24:46');

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
(2, 1, 13, 'test ', 'df dsf dsf', '2024-01-08 10:14:41'),
(3, 1, 14, 'riding boot', 'sdf sdf sd fssd', '2024-01-08 15:10:44'),
(4, 1, 15, 'test dog product', 'ds fdsf sdf', '2024-01-08 15:10:48');

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
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `color_product`
--
ALTER TABLE `color_product`
  MODIFY `color_product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `color_product_details`
--
ALTER TABLE `color_product_details`
  MODIFY `color_product_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `color_product_images`
--
ALTER TABLE `color_product_images`
  MODIFY `color_product_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dealer_cart`
--
ALTER TABLE `dealer_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `dealer_profile_image`
--
ALTER TABLE `dealer_profile_image`
  MODIFY `profile_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dealer_register`
--
ALTER TABLE `dealer_register`
  MODIFY `dealer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `new_product_updates`
--
ALTER TABLE `new_product_updates`
  MODIFY `product_updates_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_alert`
--
ALTER TABLE `product_alert`
  MODIFY `product_alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_color`
--
ALTER TABLE `product_color`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_details`
--
ALTER TABLE `product_details`
  MODIFY `product_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `product_image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_orders`
--
ALTER TABLE `product_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `quick_orders`
--
ALTER TABLE `quick_orders`
  MODIFY `quick_orderid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stock_update`
--
ALTER TABLE `stock_update`
  MODIFY `stock_update_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sub_category`
--
ALTER TABLE `sub_category`
  MODIFY `sub_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
