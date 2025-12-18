-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 18, 2025 at 04:45 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `directory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-folder',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `created_at`) VALUES
(1, 'Food & Dining', 'food-dining', 'fa-utensils', '2025-12-17 16:35:03'),
(2, 'Real Estate', 'real-estate', 'fa-home', '2025-12-17 16:35:03'),
(3, 'Automotive', 'automotive', 'fa-car', '2025-12-17 16:35:03'),
(4, 'Health & Medical', 'health-medical', 'fa-heartbeat', '2025-12-17 16:35:03'),
(5, 'Shopping', 'shopping', 'shopping-bag', '2025-12-17 17:20:09'),
(6, 'Travel', 'travel', 'plane', '2025-12-17 17:20:09');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE IF NOT EXISTS `favorites` (
  `user_id` int NOT NULL,
  `listing_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`listing_id`),
  KEY `listing_id` (`listing_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

DROP TABLE IF EXISTS `listings`;
CREATE TABLE IF NOT EXISTS `listings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `status` enum('pending','active','rejected') DEFAULT 'pending',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `location_id` (`location_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `user_id`, `category_id`, `location_id`, `title`, `image`, `slug`, `description`, `address`, `phone`, `email`, `website`, `is_featured`, `status`, `views`, `created_at`) VALUES
(1, 1, 1, 1, 'The Gourmet Kitchen', NULL, 'the-gourmet-kitchen', 'Best Italian food in NYC.', '123 Broadway, NY', NULL, NULL, NULL, 1, 'active', 0, '2025-12-17 16:35:05'),
(2, 1, 2, 2, 'Sunset Villa', NULL, 'sunset-villa', 'Luxury villa with ocean view.', '456 Sunset Blvd, LA', NULL, NULL, NULL, 1, 'active', 0, '2025-12-17 16:35:05'),
(3, 1, 3, 3, 'London Auto Repair', NULL, 'london-auto-repair', 'Reliable car mechanics.', '789 Kings Rd, London', NULL, NULL, NULL, 0, 'active', 0, '2025-12-17 16:35:05'),
(4, 2, 6, 1, 'Urban Agency 457', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=800&q=80', 'urban-agency-457-6942ea4f08a4b', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0142', 'contact@demo.com', 'https://example.com', 0, 'active', 4082, '2025-12-17 17:37:19'),
(5, 3, 2, 6, 'Modern Agency 511', 'https://images.unsplash.com/photo-1600596542815-6ad4c727dd2d?auto=format&fit=crop&w=800&q=80', 'modern-agency-511-6942ea4f09239', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0146', 'contact@demo.com', 'https://example.com', 0, 'active', 2468, '2025-12-17 17:37:19'),
(6, 3, 1, 4, 'Exclusive Garage 375', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80', 'exclusive-garage-375-6942ea4f095de', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0186', 'contact@demo.com', 'https://example.com', 1, 'active', 4778, '2025-12-17 17:37:19'),
(7, 4, 1, 2, 'Family Apartments 122', 'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=800&q=80', 'family-apartments-122-6942ea4f096c2', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0159', 'contact@demo.com', 'https://example.com', 0, 'active', 1583, '2025-12-17 17:37:19'),
(8, 1, 2, 4, 'Best Studio 950', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80', 'best-studio-950-6942ea4f097a2', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0194', 'contact@demo.com', 'https://example.com', 0, 'active', 1102, '2025-12-17 17:37:19'),
(9, 4, 4, 2, 'Exclusive Apartments 392', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=800&q=80', 'exclusive-apartments-392-6942ea4f09867', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0119', 'contact@demo.com', 'https://example.com', 0, 'active', 2110, '2025-12-17 17:37:19'),
(10, 3, 2, 5, 'Affordable Apartments 368', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80', 'affordable-apartments-368-6942ea4f09937', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0139', 'contact@demo.com', 'https://example.com', 0, 'active', 1032, '2025-12-17 17:37:19'),
(11, 1, 5, 6, 'Affordable Garage 437', 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=800&q=80', 'affordable-garage-437-6942ea4f09a21', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0116', 'contact@demo.com', 'https://example.com', 0, 'active', 721, '2025-12-17 17:37:19'),
(12, 1, 5, 3, 'Cozy Cafe 200', 'https://images.unsplash.com/photo-1472851294608-4155f2118c67?auto=format&fit=crop&w=800&q=80', 'cozy-cafe-200-6942ea4f09b11', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0119', 'contact@demo.com', 'https://example.com', 0, 'active', 4473, '2025-12-17 17:37:19'),
(13, 3, 2, 6, 'Luxury Apartments 143', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80', 'luxury-apartments-143-6942ea4f09bb7', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0114', 'contact@demo.com', 'https://example.com', 0, 'active', 1423, '2025-12-17 17:37:19'),
(14, 1, 2, 1, 'Urban Garage 864', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80', 'urban-garage-864-6942ea4f09cb5', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0128', 'contact@demo.com', 'https://example.com', 1, 'active', 411, '2025-12-17 17:37:19'),
(15, 1, 6, 2, 'Best Bistro 268', 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=800&q=80', 'best-bistro-268-6942ea4f09d5e', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0114', 'contact@demo.com', 'https://example.com', 1, 'active', 1967, '2025-12-17 17:37:19'),
(16, 1, 6, 6, 'Luxury Apartments 122', 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=800&q=80', 'luxury-apartments-122-6942ea4f09dff', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0174', 'contact@demo.com', 'https://example.com', 1, 'active', 792, '2025-12-17 17:37:19'),
(17, 4, 6, 7, 'Exclusive Motors 592', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=800&q=80', 'exclusive-motors-592-6942ea4f09e86', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0157', 'contact@demo.com', 'https://example.com', 0, 'active', 3315, '2025-12-17 17:37:19'),
(18, 3, 1, 6, 'Exclusive Apartments 450', 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=800&q=80', 'exclusive-apartments-450-6942ea4f09f7a', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0117', 'contact@demo.com', 'https://example.com', 0, 'active', 4229, '2025-12-17 17:37:19'),
(19, 3, 3, 6, 'Family Motors 334', 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80', 'family-motors-334-6942ea4f0a014', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0168', 'contact@demo.com', 'https://example.com', 0, 'active', 2704, '2025-12-17 17:37:19'),
(20, 1, 1, 7, 'Best Agency 720', 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80', 'best-agency-720-6942ea4f0a152', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0131', 'contact@demo.com', 'https://example.com', 0, 'active', 4544, '2025-12-17 17:37:19'),
(21, 1, 3, 3, 'Urban Cafe 371', 'https://images.unsplash.com/photo-1503376763036-066120622c74?auto=format&fit=crop&w=800&q=80', 'urban-cafe-371-6942ea4f0a1e9', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0175', 'contact@demo.com', 'https://example.com', 0, 'active', 3889, '2025-12-17 17:37:19'),
(22, 1, 6, 4, 'Modern Motors 778', 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=800&q=80', 'modern-motors-778-6942ea4f0a268', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0118', 'contact@demo.com', 'https://example.com', 1, 'active', 2486, '2025-12-17 17:37:19'),
(23, 1, 2, 7, 'Best Studio 512', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80', 'best-studio-512-6942ea4f0a345', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0163', 'contact@demo.com', 'https://example.com', 0, 'active', 3103, '2025-12-17 17:37:19'),
(24, 1, 4, 3, 'Vintage Garage 867', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=800&q=80', 'vintage-garage-867-6942ea4f162cb', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0128', 'contact@demo.com', 'https://example.com', 0, 'active', 1398, '2025-12-17 17:37:19'),
(25, 3, 1, 6, 'Premium Boutique 641', 'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=800&q=80', 'premium-boutique-641-6942ea4f165f5', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0178', 'contact@demo.com', 'https://example.com', 1, 'active', 2867, '2025-12-17 17:37:19'),
(26, 4, 4, 2, 'Premium Motors 456', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80', 'premium-motors-456-6942ea4f166fc', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0187', 'contact@demo.com', 'https://example.com', 0, 'active', 1578, '2025-12-17 17:37:19'),
(27, 2, 4, 1, 'Premium Boutique 273', 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=800&q=80', 'premium-boutique-273-6942ea4f1678b', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0190', 'contact@demo.com', 'https://example.com', 1, 'active', 923, '2025-12-17 17:37:19'),
(28, 1, 6, 2, 'Best Resort 360', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80', 'best-resort-360-6942ea4f16840', 'Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.', '123 Fake St, City Center', '+1 555 0141', 'contact@demo.com', 'https://example.com', 1, 'active', 1757, '2025-12-17 17:37:19');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT 'USA',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `city`, `state`, `country`) VALUES
(1, 'New York', 'NY', 'USA'),
(2, 'Los Angeles', 'CA', 'USA'),
(3, 'London', 'Greater London', 'UK'),
(4, 'New York', 'NY', 'USA'),
(5, 'Los Angeles', 'CA', 'USA'),
(6, 'London', 'UK', 'UK'),
(7, 'Dubai', 'UAE', 'UAE');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int DEFAULT NULL,
  `sender_name` varchar(100) NOT NULL,
  `sender_email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `listing_id` (`listing_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `listing_id` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `listing_id` (`listing_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `listing_id` (`listing_id`),
  KEY `user_id` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(100) DEFAULT 'BizFinder',
  `site_email` varchar(100) DEFAULT 'admin@bizfinder.com',
  `site_description` text,
  `about_text` text,
  `footer_text` varchar(255) DEFAULT '© 2025 BizFinder. All Rights Reserved.',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `site_email`, `site_description`, `about_text`, `footer_text`, `updated_at`) VALUES
(1, 'BizFinder', 'admin@bizfinder.com', 'The best local business directory.', 'Welcome to BizFinder, your number one source for all local businesses.', '© 2025 BizFinder. All Rights Reserved.', '2025-12-17 16:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','vendor','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'John Doe', 'john@example.com', 'password123', 'vendor', '2025-12-17 16:35:03'),
(2, 'Super Admin', 'admin@gmail.com', '$2y$10$X5vsTkKrSa/ydnr4QJperecDoC1o34VY2NphbA9cMi5WLlsymFwUa', 'admin', '2025-12-17 17:20:09'),
(3, 'user', 'user@gmail.com', '$2y$10$.Il9p.7wLANErggMHq3KHeAdWe7lEXrNIsgpWBvr9gYX1fYCUmdTu', 'user', '2025-12-17 17:24:36'),
(4, 'vendor', 'vendor@gmail.com', '$2y$10$LhQTU2A.wVvmEYYcikKXBeT488xG0efcpeXUwCd.tyhvjM3PkWZ.S', 'vendor', '2025-12-17 17:34:16');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
