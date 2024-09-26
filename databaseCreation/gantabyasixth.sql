-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2024 at 06:02 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gantabyasixth`
--

-- --------------------------------------------------------

--
-- Table structure for table `admininfo`
--

CREATE Database IF NOT EXISTS `gantabyasixth`;
USE `gantabyasixth`;

CREATE TABLE `admininfo` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` bigint(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admininfo`
--

INSERT INTO `admininfo` (`id`, `username`, `email`, `contact`, `password`) VALUES
(1, 'admin', 'admin@gmail.com', 9800000000, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `carrierdetails`
--

CREATE TABLE IF NOT EXISTS `carrierdetails` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `img_srcs` varchar(255) NOT NULL DEFAULT 'img/images/user-regular.png',
  `email` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` bigint(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_otp_hash` varchar(64) DEFAULT NULL,
  `reset_otp_expires_at` datetime DEFAULT NULL,
  `availability` varchar(10) DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consignordetails`
--

CREATE TABLE IF NOT EXISTS `consignordetails` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `img_srcs` varchar(255) NOT NULL DEFAULT 'img/images/user-regular.png',
  `email` varchar(255) NOT NULL,
  `contact` bigint(10) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `reset_otp_hash` varchar(64) DEFAULT NULL,
  `reset_otp_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loaddetails`
--

CREATE TABLE IF NOT EXISTS `loaddetails` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `dateofpost` datetime DEFAULT current_timestamp(),
  `origin` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `distance` int(11) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `status` varchar(55) DEFAULT 'notBooked',
  `consignor_id` int(11) DEFAULT NULL,
  `img_srcs` varchar(124) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `origin_latitude` decimal(10,8) DEFAULT NULL,
  `origin_longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE IF NOT EXISTS`reviews` (
  `id` int(11) NOT NULL,
  `shipment_id` int(11) DEFAULT NULL,
  `consignor_id` int(11) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `reviewer_type` enum('consignor','carrier') DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipment`
--

CREATE TABLE IF NOT EXISTS `shipment` (
  `id` int(11) NOT NULL,
  `load_id` int(11) NOT NULL,
  `consignor_id` int(11) NOT NULL,
  `carrier_id` int(11) NOT NULL,
  `scheduled_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `admininfo`
--
ALTER TABLE `admininfo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `carrierdetails`
--
ALTER TABLE `carrierdetails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `reset_otp_hash` (`reset_otp_hash`);

--
-- Indexes for table `consignordetails`
--
ALTER TABLE `consignordetails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `reset_otp_hash` (`reset_otp_hash`);

--
-- Indexes for table `loaddetails`
--
ALTER TABLE `loaddetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consignor_id` (`consignor_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipment_id` (`shipment_id`),
  ADD KEY `consignor_id` (`consignor_id`),
  ADD KEY `carrier_id` (`carrier_id`);

--
-- Indexes for table `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carrier_id` (`carrier_id`),
  ADD KEY `load_id` (`load_id`),
  ADD KEY `consignor_id` (`consignor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admininfo`
--
ALTER TABLE `admininfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `carrierdetails`
--
ALTER TABLE `carrierdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `consignordetails`
--
ALTER TABLE `consignordetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loaddetails`
--
ALTER TABLE `loaddetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipment`
--
ALTER TABLE `shipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loaddetails`
--
ALTER TABLE `loaddetails`
  ADD CONSTRAINT `loaddetails_ibfk_1` FOREIGN KEY (`consignor_id`) REFERENCES `consignordetails` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`consignor_id`) REFERENCES `consignordetails` (`id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`carrier_id`) REFERENCES `carrierdetails` (`id`);

--
-- Constraints for table `shipment`
--
ALTER TABLE `shipment`
  ADD CONSTRAINT `shipment_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `carrierdetails` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shipment_ibfk_3` FOREIGN KEY (`load_id`) REFERENCES `loaddetails` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shipment_ibfk_4` FOREIGN KEY (`consignor_id`) REFERENCES `consignordetails` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
