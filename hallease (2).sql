-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2026 at 10:11 AM
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
-- Database: `hallease`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Super Admin', 'admin@hallease.com', '$2y$10$AXypef7HA0Zfrt0Q0UoPWeGhu7Exik2xxTBb2317Uo/pBbeiC3Klu', '2026-02-01 09:08:43'),
(3, 'zeel', 'zeelshah430@gmail.com', '$2y$12$8K1pQY4LwHj7rTnZx2cFKe3mY7uVbW9sDqLpR5xZaJcHtUoNiP2Qa\r\n', '2026-02-04 04:43:22');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hall_id` int(11) NOT NULL,
  `booking_status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `booking_start_date` date NOT NULL,
  `booking_end_date` date NOT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `hall_id`, `booking_status`, `total_amount`, `booking_start_date`, `booking_end_date`, `payment_status`) VALUES
(1, 12, 2, 'pending', 200.00, '2026-02-02', '2026-02-03', 'pending'),
(2, 12, 1, 'confirmed', 4000.00, '2026-02-27', '2026-02-28', 'pending'),
(3, 12, 1, 'confirmed', 4000.00, '2026-02-27', '2026-02-28', 'pending'),
(4, 13, 3, 'confirmed', 20000.00, '2026-02-08', '2026-02-08', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `halls`
--

CREATE TABLE `halls` (
  `hall_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `hall_name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `facilities` text DEFAULT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `description` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `halls`
--

INSERT INTO `halls` (`hall_id`, `owner_id`, `hall_name`, `location`, `city`, `capacity`, `facilities`, `price_per_day`, `status`, `description`, `images`, `created_at`) VALUES
(1, 1, 'muncipalty hall', 'raiya road', 'Rajkot', 100, 'nothing is there', 20000.00, 'available', 'come at our hall', '[\"https://www.wdesignhub.com/wp-content/uploads/2021/04/Banquet-Hall-Designing-9-700x500.jpg\",\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRy8nnz-zF2SZ0ewuT9JA-XMhEBhtqF9HpIQ&s\",\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQa3TN6g-i37QZqsgaFTwrK5o95-thvQphXWg&s\",\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQzzE5iSa4bUde3G22pmOI35gSiXo32zwke3g&s\"]', '2026-02-01 09:45:01'),
(2, 2, 'best hall', 'race corse', 'Jamanagr', 150, 'WIFI,AC', 10000.00, 'available', 'dvhghgvdvbdn db', '[\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS0FSq5LbHb8YDi_c8ELA64jRBNrkyxMQ4Y9A&s\",\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTvODJrEOfWkNnIqIdBWLAIpMyMV4WSk3Mopg&s\",\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQeOdpY-4S-2N6U4OdtxRy97qpOwXq7CG1EMA&s\"]', '2026-02-01 09:54:34'),
(3, 3, 'Achyukta', 'Madhapar', 'Rajkot', 500, 'AC, Wifi , Aesthetic Lighting, Parking', 20000.00, 'available', 'A spacious and elegantly designed banquet hall ideal for weddings, receptions, and corporate events.\r\nFully air-conditioned with modern lighting, stage setup, and premium décor options for a grand ambiance.\r\nAmple parking, in-house catering, and professional event support ensure a seamless celebration experience.', '[\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTR0FFy_k-Vm-464RgKcKLRbLjX7sAEOJ8OrA&s\",\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT7B7IDPsxD64gt-JOa3qfxhUe5RKOeK_ylXA&s\",\"https://i.pinimg.com/736x/03/4e/34/034e34b3df3fdd55a73c66fabca2693f.jpg\",\"https://png.pngtree.com/background/20250106/original/pngtree-decorated-wedding-banquet-hall-in-classic-style-picture-image_15459550.jpg\"]', '2026-02-04 04:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `hall_owners`
--

CREATE TABLE `hall_owners` (
  `owner_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hall_owners`
--

INSERT INTO `hall_owners` (`owner_id`, `full_name`, `email`, `password`, `phone`, `created_at`) VALUES
(1, 'zeel hall', 'zeelshah430@gmail.com', '$2y$10$nBoaTyPGRZNXct4R7uNqo./bHyPO0JfFi86TVJoGtqzyELZehYsna', '846458458', '2026-02-01 09:45:01'),
(2, 'Rag', 'rag@yahoo.com', '$2y$10$m1BgI1VftS6Q9wIPUrDg4exIC0wX8RCr3ji.P7WkBNi.jfbG0AZXy', '9409257097', '2026-02-01 09:54:34'),
(3, 'Kripa Moliya', 'kmoliya12@gmail.com', '$2y$10$.emI7M1qfyCc7wmDBZiXQOMWGVswpZAUtbqJUwXXavv29.INR.6Cu', '999999444444', '2026-02-04 04:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT 'Card',
  `transaction_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','owner','admin') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone`, `role`, `status`, `created_at`) VALUES
(1, 'Admin', 'admin@hallease.com', 'admin123', '22222555555', 'admin', 'active', '2026-02-01 09:31:08'),
(2, 'Hall Owner One', 'owner1@hallease.com', 'owner123', '9999999999', 'owner', 'active', '2026-02-01 09:31:08'),
(3, 'Client One', 'client1@hallease.com', 'client123', '6464646464', 'user', 'active', '2026-02-01 09:31:08'),
(4, 'zeel', 'zeelshah430@gmail.com', '123', '7777744444', 'user', 'active', '2026-02-01 09:31:08'),
(11, 'Rag', 'rag@gmail.com', '12345', '5555588888', 'user', 'active', '2026-02-01 09:31:08'),
(12, 'newuser', 'user@gmail.com', '$2y$10$qFGsEyKI/.VnfDUmcaK05e.IdrHsIV6HTLS8j5AaCQ4DiuKQ471MC', '9409257789', 'user', 'active', '2026-02-01 09:32:50'),
(13, 'Kripa Moliya', 'kmoliya1612@gmail.com', '$2y$10$atklhZ6lgB/yyoMBjVg33.xwDCNOt7UjaGWbve.EUo0IuiROcS0w6', '2342323233', 'user', 'active', '2026-02-04 04:35:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `client_id` (`user_id`),
  ADD KEY `hall_id` (`hall_id`);

--
-- Indexes for table `halls`
--
ALTER TABLE `halls`
  ADD PRIMARY KEY (`hall_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `hall_owners`
--
ALTER TABLE `hall_owners`
  ADD PRIMARY KEY (`owner_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `halls`
--
ALTER TABLE `halls`
  MODIFY `hall_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hall_owners`
--
ALTER TABLE `hall_owners`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`hall_id`);

--
-- Constraints for table `halls`
--
ALTER TABLE `halls`
  ADD CONSTRAINT `halls_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
