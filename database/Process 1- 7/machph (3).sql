-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2024 at 06:26 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `machph`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`id`, `user_id`, `car_id`, `status`, `reason`, `timestamp`) VALUES
(115, 89, 182, 1, '', '2024-04-11 14:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `mechanic_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `user_id`, `car_id`, `mechanic_id`) VALUES
(754, 89, 182, 21);

-- --------------------------------------------------------

--
-- Table structure for table `autoshop`
--

CREATE TABLE `autoshop` (
  `companyid` int(11) NOT NULL,
  `companyname` varchar(255) DEFAULT NULL,
  `companyemail` varchar(255) DEFAULT NULL,
  `companyphonenumber` varchar(15) DEFAULT NULL,
  `streetaddress` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `country` enum('Philippines','China','Japan','Korea') DEFAULT NULL,
  `cname` varchar(100) DEFAULT NULL,
  `cpassword` varchar(255) DEFAULT NULL,
  `companyimage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `autoshop`
--

INSERT INTO `autoshop` (`companyid`, `companyname`, `companyemail`, `companyphonenumber`, `streetaddress`, `city`, `region`, `zipcode`, `country`, `cname`, `cpassword`, `companyimage`) VALUES
(3, 'Hikaru Autoshop', 'hikaruautoshop@gmail.com', '09485011228', 'Brgy. Toril Road', 'Davao city', 'Davao Region', '8000', 'Philippines', 'hikaruadmin', 'admin', 'uploaded_img3d82f7d50a2b767e9724871ef7ec2922.jpg'),
(4, 'snow', 'snow1@gmail.com', '09485011228', 'Brgy. Toril Road', 'Davao city', 'Davao Region', '8000', 'Philippines', 'snowadmin', 'admin', 'uploaded_img29badfa51eb5ac0040df9cef60be29e5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `car`
--

CREATE TABLE `car` (
  `car_id` int(11) NOT NULL,
  `plateno` varchar(20) NOT NULL,
  `manufacturer` varchar(50) NOT NULL,
  `carmodel` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `bodyno` varchar(20) NOT NULL,
  `enginecc` int(11) NOT NULL,
  `gas` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `manufacturer_id` int(11) DEFAULT NULL,
  `car_model_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `manuname` varchar(255) DEFAULT NULL,
  `color` varchar(255) NOT NULL,
  `autoshop_id` int(11) DEFAULT NULL,
  `companyid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`car_id`, `plateno`, `manufacturer`, `carmodel`, `year`, `bodyno`, `enginecc`, `gas`, `user_id`, `created_at`, `manufacturer_id`, `car_model_id`, `added_at`, `manuname`, `color`, `autoshop_id`, `companyid`) VALUES
(182, 'c', '', 'Civic', 2024, 'c', 1499, 'Regular', 89, '2024-04-11 14:25:10', 2, NULL, '2024-04-11 14:25:10', 'Honda', 'Red', NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `car_model`
--

CREATE TABLE `car_model` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `manufacturer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_model`
--

INSERT INTO `car_model` (`id`, `name`, `manufacturer_id`) VALUES
(1, 'Fortuner', 1),
(2, 'Yaris', 1),
(3, 'Innova', 1),
(4, 'Civic', 2),
(5, 'Pilot', 2),
(6, 'Brio', 2),
(7, 'Jimny', 3),
(8, 'Vitara', 3),
(9, 'Ignis', 3),
(10, 'Strada', 4),
(13, 'Countach', 8),
(14, 'Urus', 8);

-- --------------------------------------------------------

--
-- Table structure for table `complete`
--

CREATE TABLE `complete` (
  `complete_id` int(11) NOT NULL,
  `complete` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complete`
--

INSERT INTO `complete` (`complete_id`, `complete`, `user_id`) VALUES
(35, 'complete', 85),
(36, 'complete', 85);

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `manager_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'manager',
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manufacturer`
--

CREATE TABLE `manufacturer` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manufacturer`
--

INSERT INTO `manufacturer` (`id`, `name`) VALUES
(1, 'Toyota'),
(2, 'Honda'),
(8, 'Lamborghini');

-- --------------------------------------------------------

--
-- Table structure for table `mechadata`
--

CREATE TABLE `mechadata` (
  `mechaid` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `employment` enum('full time','part time','intern/temporary') DEFAULT NULL,
  `jobrole` set('General automotive mechanic','Brake and transmission technicians','Small engine mechanic','Tire mechanics') DEFAULT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mechanic`
--

CREATE TABLE `mechanic` (
  `mechanic_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `employment` enum('full_time','part_time','intern_temporary') NOT NULL,
  `jobrole` enum('Automotive mechanic','Brake technicians','Small engine mechanic','Tire mechanics') NOT NULL,
  `last_progress_saved` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `autoshop_id` int(11) DEFAULT NULL,
  `apply` enum('pending','approved','declined') DEFAULT 'pending',
  `companyid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanic`
--

INSERT INTO `mechanic` (`mechanic_id`, `user_id`, `employment`, `jobrole`, `last_progress_saved`, `autoshop_id`, `apply`, `companyid`) VALUES
(21, 90, 'full_time', 'Small engine mechanic', '2024-04-11 14:09:42', NULL, 'pending', 3),
(22, 91, 'part_time', 'Tire mechanics', '2024-04-11 14:12:13', NULL, 'pending', 4);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parts`
--

CREATE TABLE `parts` (
  `part_id` int(11) NOT NULL,
  `part_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `date_arrival` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `profit` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `system` enum('Engine system','Maintenance system') DEFAULT NULL,
  `companyid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `barcode`, `item_name`, `category`, `date_arrival`, `expiry_date`, `selling_price`, `original_price`, `profit`, `total`, `product_image`, `quantity`, `system`, `companyid`) VALUES
(11, 'cssc', 'Komi - san', 'Komisan is super cute', '2024-04-11', NULL, '1200.00', '500.00', '700.00', NULL, 'uploaded_img/29badfa51eb5ac0040df9cef60be29e5.jpg', 6, 'Engine system', 3);

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `progress_percentage` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repair`
--

CREATE TABLE `repair` (
  `repairid` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `plateno` varchar(255) DEFAULT NULL,
  `problem` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `part_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair`
--

INSERT INTO `repair` (`repairid`, `user_id`, `plateno`, `problem`, `diagnosis`, `remarks`, `part_id`) VALUES
(632, 89, '182', 'Engine Overhaul', 'Piston and Piston Rings', NULL, NULL),
(633, 89, '182', 'Battery', 'Battery Age', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `selected_checkboxes`
--

CREATE TABLE `selected_checkboxes` (
  `id` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `checkbox_value` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `selected_checkboxes`
--

INSERT INTO `selected_checkboxes` (`id`, `category`, `checkbox_value`, `quantity`, `price`, `user_id`, `car_id`) VALUES
(79, 'Mechanical Issues', 'Cylinder Walls/Liners', 2, '150.00', 89, 182),
(80, 'Mechanical Issues', 'Piston Pins/Piston Wrist Pins', 2, '120.00', 89, 182),
(81, 'Battery', 'Battery Replacement', 0, '350.00', 89, 182);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `serviceno` int(11) NOT NULL,
  `eo` varchar(255) DEFAULT NULL,
  `elp` varchar(255) DEFAULT NULL,
  `ep` varchar(255) DEFAULT NULL,
  `battery` varchar(255) DEFAULT NULL,
  `light` varchar(255) DEFAULT NULL,
  `oil` varchar(255) DEFAULT NULL,
  `water` varchar(255) DEFAULT NULL,
  `brake` varchar(255) DEFAULT NULL,
  `air` varchar(255) DEFAULT NULL,
  `gas` varchar(255) DEFAULT NULL,
  `tire` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `companyid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`serviceno`, `eo`, `elp`, `ep`, `battery`, `light`, `oil`, `water`, `brake`, `air`, `gas`, `tire`, `user_id`, `car_id`, `service_name`, `companyid`) VALUES
(175, '1', NULL, NULL, '2', '1', '1', '1', '1', '1', '1', '1', 89, 182, NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `homeaddress` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `mechanic_id` int(11) DEFAULT NULL,
  `manager_role` varchar(20) DEFAULT 'none',
  `employment` enum('full time','part time','intern/temporary') DEFAULT NULL,
  `jobrole` set('General automotive mechanic','Brake and transmission technicians','Small engine mechanic','Tire mechanics') DEFAULT NULL,
  `autoshop_id` int(11) DEFAULT NULL,
  `companyid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `firstname`, `middlename`, `lastname`, `homeaddress`, `email`, `password`, `image`, `barangay`, `province`, `municipality`, `zipcode`, `role`, `mechanic_id`, `manager_role`, `employment`, `jobrole`, `autoshop_id`, `companyid`) VALUES
(89, 'hikaru', 'Jhon Rexey', 'h', 'Cabrera', 'Brgy. Toril Road', 'hikaru@gmail.com', 'ec0c02c2884ec60d59cb38ec711e34f4', '3d82f7d50a2b767e9724871ef7ec2922.jpg', 'lubogan', 'Davao Region', 'Davao city', '8000', 'user', NULL, '', NULL, NULL, NULL, 3),
(90, 'slime', 'Jhon Rexey', 's', 'Cabrera', 'Brgy. Toril Road', 'slime@gmail.com', 'f79f0528037f833a5e9901a26490be94', '99b37c18a64ef188c9b919a36bf70d40.jpg', 'Bago oshiro', 'Davao Region', 'Davao city', '8000', 'mechanic', 21, 'none', NULL, NULL, NULL, NULL),
(91, 'snow', 'Jhon Rexey', 's', 'Cabrera', 'Brgy. Toril Road', 'snow@gmail.com', '2b93fbdf27d43547bec8794054c28e00', '88ca6897b841cea082cfe2c1aa9f469c.jpg', 'Bago oshiro', 'Davao Region', 'Davao city', '8000', 'mechanic', 22, 'none', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `userpart`
--

CREATE TABLE `userpart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `mechanical_issues` varchar(255) DEFAULT NULL,
  `fuel_and_air_intake_system` varchar(255) DEFAULT NULL,
  `cooling_and_lubrication` varchar(255) DEFAULT NULL,
  `battery` varchar(255) DEFAULT NULL,
  `light` varchar(255) DEFAULT NULL,
  `oil` varchar(255) DEFAULT NULL,
  `water` varchar(255) DEFAULT NULL,
  `brake` varchar(255) DEFAULT NULL,
  `air` varchar(255) DEFAULT NULL,
  `gas` varchar(255) DEFAULT NULL,
  `tire` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `validation`
--

CREATE TABLE `validation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `status` enum('valid','invalid') DEFAULT 'valid',
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_car` (`user_id`,`car_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `mechanic_id` (`mechanic_id`);

--
-- Indexes for table `autoshop`
--
ALTER TABLE `autoshop`
  ADD PRIMARY KEY (`companyid`);

--
-- Indexes for table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`car_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_model_id` (`car_model_id`),
  ADD KEY `fk_manufacturer` (`manufacturer_id`),
  ADD KEY `autoshop_id` (`autoshop_id`);

--
-- Indexes for table `car_model`
--
ALTER TABLE `car_model`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manufacturer_id` (`manufacturer_id`);

--
-- Indexes for table `complete`
--
ALTER TABLE `complete`
  ADD PRIMARY KEY (`complete_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`manager_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `manufacturer`
--
ALTER TABLE `manufacturer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mechadata`
--
ALTER TABLE `mechadata`
  ADD PRIMARY KEY (`mechaid`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mechanic`
--
ALTER TABLE `mechanic`
  ADD PRIMARY KEY (`mechanic_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `autoshop_id` (`autoshop_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `parts`
--
ALTER TABLE `parts`
  ADD PRIMARY KEY (`part_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `repair`
--
ALTER TABLE `repair`
  ADD PRIMARY KEY (`repairid`),
  ADD KEY `fk_part_id` (`part_id`);

--
-- Indexes for table `selected_checkboxes`
--
ALTER TABLE `selected_checkboxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`serviceno`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `fk_companyid` (`companyid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autoshop_id` (`autoshop_id`);

--
-- Indexes for table `userpart`
--
ALTER TABLE `userpart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `validation`
--
ALTER TABLE `validation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=755;

--
-- AUTO_INCREMENT for table `autoshop`
--
ALTER TABLE `autoshop`
  MODIFY `companyid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `car`
--
ALTER TABLE `car`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `car_model`
--
ALTER TABLE `car_model`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `complete`
--
ALTER TABLE `complete`
  MODIFY `complete_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `manager_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `manufacturer`
--
ALTER TABLE `manufacturer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mechadata`
--
ALTER TABLE `mechadata`
  MODIFY `mechaid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mechanic`
--
ALTER TABLE `mechanic`
  MODIFY `mechanic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `parts`
--
ALTER TABLE `parts`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `repair`
--
ALTER TABLE `repair`
  MODIFY `repairid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=634;

--
-- AUTO_INCREMENT for table `selected_checkboxes`
--
ALTER TABLE `selected_checkboxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `serviceno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `userpart`
--
ALTER TABLE `userpart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `validation`
--
ALTER TABLE `validation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `car` (`car_id`),
  ADD CONSTRAINT `assignments_ibfk_3` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanic` (`mechanic_id`);

--
-- Constraints for table `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `car_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `car_ibfk_2` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`id`),
  ADD CONSTRAINT `car_ibfk_3` FOREIGN KEY (`car_model_id`) REFERENCES `car_model` (`id`),
  ADD CONSTRAINT `car_ibfk_4` FOREIGN KEY (`autoshop_id`) REFERENCES `autoshop` (`companyid`),
  ADD CONSTRAINT `fk_manufacturer` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`id`);

--
-- Constraints for table `car_model`
--
ALTER TABLE `car_model`
  ADD CONSTRAINT `car_model_ibfk_1` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`id`);

--
-- Constraints for table `complete`
--
ALTER TABLE `complete`
  ADD CONSTRAINT `complete_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `manager`
--
ALTER TABLE `manager`
  ADD CONSTRAINT `manager_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `manager_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `car` (`car_id`),
  ADD CONSTRAINT `manager_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `service` (`serviceno`);

--
-- Constraints for table `mechadata`
--
ALTER TABLE `mechadata`
  ADD CONSTRAINT `mechadata_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `mechanic`
--
ALTER TABLE `mechanic`
  ADD CONSTRAINT `mechanic_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `mechanic_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `mechanic_ibfk_3` FOREIGN KEY (`autoshop_id`) REFERENCES `autoshop` (`companyid`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `repair`
--
ALTER TABLE `repair`
  ADD CONSTRAINT `fk_part_id` FOREIGN KEY (`part_id`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE;

--
-- Constraints for table `selected_checkboxes`
--
ALTER TABLE `selected_checkboxes`
  ADD CONSTRAINT `selected_checkboxes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `selected_checkboxes_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `car` (`car_id`);

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `fk_companyid` FOREIGN KEY (`companyid`) REFERENCES `autoshop` (`companyid`),
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `service_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `car` (`car_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`autoshop_id`) REFERENCES `autoshop` (`companyid`);

--
-- Constraints for table `userpart`
--
ALTER TABLE `userpart`
  ADD CONSTRAINT `userpart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `userpart_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `car` (`car_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
