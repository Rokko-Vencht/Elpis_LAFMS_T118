CREATE DATABASE IF NOT EXISTS lafcms_sampledb;
USE lafcms_sampledb;


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 01:20 PM
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
-- Database: `lafcms_sampledb`
--

-- --------------------------------------------------------

--
-- Table structure for table `category_id_counter`
--

CREATE TABLE `category_id_counter` (
  `id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_id_counter`
--

INSERT INTO `category_id_counter` (`id`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10);

-- --------------------------------------------------------

--
-- Table structure for table `category_table`
--

CREATE TABLE `category_table` (
  `categ_id` varchar(255) NOT NULL,
  `categ_item` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_table`
--

INSERT INTO `category_table` (`categ_id`, `categ_item`) VALUES
('CAT001', 'Electronics'),
('CAT002', 'Sports/Recreation'),
('CAT003', 'Documents'),
('CAT004', 'Personal Care/Items'),
('CAT005', 'Education'),
('CAT006', 'Academe'),
('CAT007', 'Jewelry'),
('CAT008', 'Cash'),
('CAT009', 'Perishables (Food and Items)'),
('CAT010', 'Furnitures');

--
-- Triggers `category_table`
--
DELIMITER $$
CREATE TRIGGER `before_insert_category_table` BEFORE INSERT ON `category_table` FOR EACH ROW BEGIN
	DECLARE next_num INT;
    INSERT INTO category_id_counter VALUES(NULL);
    SET next_num = LAST_INSERT_ID();
    SET NEW.categ_id = CONCAT('CAT', LPAD(next_num, 3, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `item_id_counter`
--

CREATE TABLE `item_id_counter` (
  `id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_id_counter`
--

INSERT INTO `item_id_counter` (`id`) VALUES
(8),
(9),
(10),
(11),
(12),
(13),
(14);

-- --------------------------------------------------------

--
-- Table structure for table `item_table`
--

CREATE TABLE `item_table` (
  `item_id` varchar(30) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_details` varchar(255) DEFAULT NULL,
  `categ_id` varchar(30) NOT NULL,
  `loc_found_id` varchar(30) DEFAULT NULL,
  `loc_stored_id` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_table`
--

INSERT INTO `item_table` (`item_id`, `item_name`, `item_details`, `categ_id`, `loc_found_id`, `loc_stored_id`) VALUES
('ITE0001', 'Cellphone', 'Vivo', 'CAT001', 'LF00-0001', 'CZIT-1104'),
('ITE0002', 'Charger', '', 'CAT001', 'LF00-0001', 'CZIT-1104'),
('ITE0003', 'Basketball', '', 'CAT002', 'LF00-0004', 'CZIT-1100'),
('ITE0004', 'Library Card', '', 'CAT003', 'LF00-0003', 'CZIT-1101'),
('ITE0005', 'Frisbee', '', 'CAT002', 'LF00-0004', 'CZIT-1100'),
('ITE0006', 'Headset', '', 'CAT001', 'LF00-0011', NULL),
('ITE0007', 'Mouse', '', 'CAT001', 'LF00-0012', NULL);

--
-- Triggers `item_table`
--
DELIMITER $$
CREATE TRIGGER `before_insert_item_id` BEFORE INSERT ON `item_table` FOR EACH ROW BEGIN
	DECLARE next_num int;
    INSERT INTO item_id_counter VALUES(NULL);
    SET next_num = LAST_INSERT_ID();
    SET NEW.item_id = CONCAT('ITE', LPAD((SELECT COUNT(*) FROM item_table) + 1, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `loc_found_id_counter`
--

CREATE TABLE `loc_found_id_counter` (
  `id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loc_found_id_counter`
--

INSERT INTO `loc_found_id_counter` (`id`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12);

-- --------------------------------------------------------

--
-- Table structure for table `loc_found_table`
--

CREATE TABLE `loc_found_table` (
  `loc_found_id` varchar(30) NOT NULL,
  `loc_found_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loc_found_table`
--

INSERT INTO `loc_found_table` (`loc_found_id`, `loc_found_name`) VALUES
('LF00-0001', 'ESL'),
('LF00-0002', 'Library'),
('LF00-0003', 'Canteen'),
('LF00-0004', 'Gym'),
('LF00-0005', 'Auditorium'),
('LF00-0006', 'CPAG  Bldg.'),
('LF00-0007', 'COB Bldg.'),
('LF00-0008', 'COL  Bldg.'),
('LF00-0009', 'COED  Bldg.'),
('LF00-0010', 'CON  Bldg.'),
('LF00-0011', 'Computer Laboratory'),
('LF00-0012', 'CSDT');

--
-- Triggers `loc_found_table`
--
DELIMITER $$
CREATE TRIGGER `before_insert_loc_found_table` BEFORE INSERT ON `loc_found_table` FOR EACH ROW BEGIN
	DECLARE next_num INT;
  	INSERT INTO loc_found_id_counter VALUES(NULL);
  	SET next_num = LAST_INSERT_ID();
  	SET NEW.loc_found_id = CONCAT('LF00-', LPAD(next_num, 4, '0'));
    
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `loc_stored_id_counter`
--

CREATE TABLE `loc_stored_id_counter` (
  `id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loc_stored_id_counter`
--

INSERT INTO `loc_stored_id_counter` (`id`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12),
(13),
(14),
(15),
(16),
(17);

-- --------------------------------------------------------

--
-- Table structure for table `loc_stored_table`
--

CREATE TABLE `loc_stored_table` (
  `loc_stored_id` varchar(30) NOT NULL,
  `loc_stored_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loc_stored_table`
--

INSERT INTO `loc_stored_table` (`loc_stored_id`, `loc_stored_name`) VALUES
('CZIT-1100', 'BPED Society Office'),
('CZIT-1101', 'CPAG Faculty Office'),
('CZIT-1102', 'CPAG Society Office'),
('CZIT-1103', 'CON SBO Office'),
('CZIT-1104', 'COT SBO Office'),
('CZIT-1105', 'CAS SBO Office'),
('CZIT-1106', 'COB SBO Office'),
('CZIT-1107', 'CON SBO Office'),
('CZIT-1108', 'COE SBO Office'),
('CZIT-1109', 'CAS Society Office'),
('CZIT-1110', 'COB Society Office'),
('CZIT-1111', 'COT Society Office'),
('CZIT-1112', 'COT Society Office'),
('CZIT-1113', 'Finance'),
('CZIT-1114', 'Main Entrance Guard'),
('CZIT-1115', 'Exit Guard'),
('CZIT-1116', 'Dormitory Guard');

--
-- Triggers `loc_stored_table`
--
DELIMITER $$
CREATE TRIGGER `before_insert_loc_stored_table` BEFORE INSERT ON `loc_stored_table` FOR EACH ROW BEGIN
	DECLARE next_num INT;
    INSERT INTO loc_stored_id_counter VALUES(NULL);
    SET next_num = LAST_INSERT_ID();
    SET NEW.loc_stored_id = CONCAT('CZIT-', next_num + 1099);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pub_id_counter`
--

CREATE TABLE `pub_id_counter` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pub_id_counter`
--

INSERT INTO `pub_id_counter` (`id`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8);

-- --------------------------------------------------------

--
-- Table structure for table `reports_id_counter`
--

CREATE TABLE `reports_id_counter` (
  `id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports_id_counter`
--

INSERT INTO `reports_id_counter` (`id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `reports_table`
--

CREATE TABLE `reports_table` (
  `report_id` varchar(255) NOT NULL,
  `report_month` date DEFAULT NULL,
  `total_items_found` int(11) DEFAULT NULL,
  `total_items_lost` int(11) DEFAULT NULL,
  `total_items_claimed` int(11) DEFAULT NULL,
  `total_items_unclaimed` int(11) DEFAULT NULL,
  `claim_rate` float DEFAULT NULL,
  `top_category` varchar(255) DEFAULT NULL,
  `monthly_summary` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports_table`
--

INSERT INTO `reports_table` (`report_id`, `report_month`, `total_items_found`, `total_items_lost`, `total_items_claimed`, `total_items_unclaimed`, `claim_rate`, `top_category`, `monthly_summary`) VALUES
('R0001', '2025-03-01', 5, 2, 3, 2, 60, 'Electronics', 'Majority of found items were electronics, with a steady claim rate of 60%.');

--
-- Triggers `reports_table`
--
DELIMITER $$
CREATE TRIGGER `before_insert_reports_table` BEFORE INSERT ON `reports_table` FOR EACH ROW BEGIN
	DECLARE next_num int;
    INSERT INTO reports_id_counter VALUES(NULL);
    SET next_num = LAST_INSERT_ID();
    SET NEW.report_id = CONCAT('R', LPAD(next_num, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `response_id_counter`
--

CREATE TABLE `response_id_counter` (
  `id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `response_id_counter`
--

INSERT INTO `response_id_counter` (`id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `response_table`
--

CREATE TABLE `response_table` (
  `response_id` varchar(30) NOT NULL,
  `foundloc_respo` varchar(30) NOT NULL,
  `storeloc_respo` varchar(30) DEFAULT NULL,
  `other_info` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `response_table`
--

INSERT INTO `response_table` (`response_id`, `foundloc_respo`, `storeloc_respo`, `other_info`) VALUES
('R1012-00000', 'Found near the ICT office', 'It was kept on ICT office', 'Black item');

--
-- Triggers `response_table`
--
DELIMITER $$
CREATE TRIGGER `before_insert_response_table` BEFORE INSERT ON `response_table` FOR EACH ROW BEGIN
	DECLARE next_num INT;
  	INSERT INTO response_id_counter VALUES(NULL);
  	SET next_num = LAST_INSERT_ID();
  	SET NEW.response_id = CONCAT('R1012-', LPAD(next_num - 1, 5, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_id_counter`
--

CREATE TABLE `transaction_id_counter` (
  `id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_id_counter`
--

INSERT INTO `transaction_id_counter` (`id`) VALUES
(1),
(12),
(13),
(14),
(15),
(16),
(17);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_table`
--

CREATE TABLE `transaction_table` (
  `transaction_id` varchar(20) NOT NULL,
  `pub_id` varchar(20) NOT NULL,
  `item_id` varchar(20) NOT NULL,
  `report_status` varchar(50) DEFAULT NULL,
  `claim_status` varchar(50) DEFAULT NULL,
  `response_status` varchar(50) DEFAULT NULL,
  `user_respo` varchar(20) DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `response_id` varchar(50) DEFAULT NULL,
  `transaction_status` varchar(50) DEFAULT NULL,
  `date_filed` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_table`
--

INSERT INTO `transaction_table` (`transaction_id`, `pub_id`, `item_id`, `report_status`, `claim_status`, `response_status`, `user_respo`, `transaction_date`, `response_id`, `transaction_status`, `date_filed`) VALUES
('TRNSC 0000', 'TX001 X4441', 'ITE0001', 'Found', 'Claimed', NULL, 'TX008 X4448', '2025-04-03', NULL, 'Resolved', '2025-04-03'),
('TRNSC 0012', 'TX001 X4441', 'ITE0002', 'Found', 'Claimed', NULL, 'TX006 X4446', '2025-04-03', NULL, 'Resolved', '2025-03-02'),
('TRNSC 0013', 'TX002 X4442', 'ITE0003', 'Found', 'Claimed', NULL, 'TX003 X4443', '2025-03-03', NULL, 'Resolved', '2025-03-03'),
('TRNSC 0014', 'TX003 X4443', 'ITE0004', 'Found', 'Unclaimed', NULL, NULL, NULL, NULL, 'Yet To Be Resolved', '2025-03-06'),
('TRNSC 0015', 'TX006 X4446', 'ITE0005', 'Found', 'Unclaimed', NULL, NULL, NULL, NULL, 'Yet To Be Resolved', '2025-03-08'),
('TRNSC 0016', 'TX007 X4447', 'ITE0006', 'Lost', NULL, 'Responded', 'TX002 X4442', '2025-03-10', 'R1012-00000', 'Resolved', '2025-03-09'),
('TRNSC 0017', 'TX008 X4448', 'ITE0007', 'Lost', NULL, 'Pending', NULL, NULL, NULL, 'Yet To Be Resolved', '2025-03-09');

--
-- Triggers `transaction_table`
--
DELIMITER $$
CREATE TRIGGER `before_insert_transaction_table` BEFORE INSERT ON `transaction_table` FOR EACH ROW BEGIN
	DECLARE next_num INT;
	INSERT INTO transaction_id_counter VALUES (NULL);
	SET next_num = LAST_INSERT_ID();
	SET NEW.transaction_id = CONCAT('TRNSC ', LPAD(next_num, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `pub_id` varchar(20) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_num` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`pub_id`, `user_name`, `first_name`, `last_name`, `password`, `contact_num`, `email`) VALUES
('TX001 X4441', 'James Lopez', 'James', 'Lopez', '12345678', '0992-113-0110', 'jlo@gmail.com'),
('TX002 X4442', 'Kent Seridon', 'Kent', 'Seridon', '12345678', '0992-113-0111', 'kenny@gmail.com'),
('TX003 X4443', 'Emmerson Dimalig', 'Emmerson', 'Dimalig', '12345678', '0992-113-0112', 'emmetton@gmail.com'),
('TX004 X4444', 'Khristine Carretas', 'Khristine', 'Carretas', '12345678', '0992-113-0115', 'cielo@gmail.com'),
('TX005 X4445', 'Jan Jacutin', 'Jan', 'Jacutin', '12345678', '0992-113-0116', 'mikko@gmail.com'),
('TX006 X4446', 'Nick Melloria', 'Nick', 'Melloria', '12345678', '0992-113-0117', 'nizki@gmail.com'),
('TX007 X4447', 'Philip Arty', 'Philip', 'Artianza', '12345678', '0992-113-0118', 'pleps@gmail.com'),
('TX008 X4448', 'Zoid Balba', 'Zoid', 'Balba', '12345678', '0992-113-0119', 'zbalbs@gmail.com');

--
-- Triggers `user_info`
--
DELIMITER $$
CREATE TRIGGER `before_insert_user_info` BEFORE INSERT ON `user_info` FOR EACH ROW BEGIN
    DECLARE next_num INT;

    -- INSERTING TO COUNTER FOR AUTO INCREMENTATION
    INSERT INTO pub_id_counter VALUES (NULL);
    SET next_num = LAST_INSERT_ID();

    -- FORMATTING OF THE USER ID
    SET NEW.pub_id = CONCAT('TX', LPAD(next_num, 3, '0'), ' X', LPAD(next_num + 4440, 4, '0'));
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category_id_counter`
--
ALTER TABLE `category_id_counter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_table`
--
ALTER TABLE `category_table`
  ADD PRIMARY KEY (`categ_id`);

--
-- Indexes for table `item_id_counter`
--
ALTER TABLE `item_id_counter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_table`
--
ALTER TABLE `item_table`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `categ_id` (`categ_id`),
  ADD KEY `loc_found_id` (`loc_found_id`),
  ADD KEY `loc_stored_id` (`loc_stored_id`);

--
-- Indexes for table `loc_found_id_counter`
--
ALTER TABLE `loc_found_id_counter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loc_found_table`
--
ALTER TABLE `loc_found_table`
  ADD PRIMARY KEY (`loc_found_id`);

--
-- Indexes for table `loc_stored_id_counter`
--
ALTER TABLE `loc_stored_id_counter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loc_stored_table`
--
ALTER TABLE `loc_stored_table`
  ADD PRIMARY KEY (`loc_stored_id`);

--
-- Indexes for table `pub_id_counter`
--
ALTER TABLE `pub_id_counter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports_id_counter`
--
ALTER TABLE `reports_id_counter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports_table`
--
ALTER TABLE `reports_table`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `response_id_counter`
--
ALTER TABLE `response_id_counter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `response_table`
--
ALTER TABLE `response_table`
  ADD PRIMARY KEY (`response_id`);

--
-- Indexes for table `transaction_id_counter`
--
ALTER TABLE `transaction_id_counter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_table`
--
ALTER TABLE `transaction_table`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `pub_id` (`pub_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_respo` (`user_respo`),
  ADD KEY `response_id` (`response_id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`pub_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category_id_counter`
--
ALTER TABLE `category_id_counter`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `item_id_counter`
--
ALTER TABLE `item_id_counter`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `loc_found_id_counter`
--
ALTER TABLE `loc_found_id_counter`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `loc_stored_id_counter`
--
ALTER TABLE `loc_stored_id_counter`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pub_id_counter`
--
ALTER TABLE `pub_id_counter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reports_id_counter`
--
ALTER TABLE `reports_id_counter`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `response_id_counter`
--
ALTER TABLE `response_id_counter`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaction_id_counter`
--
ALTER TABLE `transaction_id_counter`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `item_table`
--
ALTER TABLE `item_table`
  ADD CONSTRAINT `item_table_ibfk_1` FOREIGN KEY (`categ_id`) REFERENCES `category_table` (`categ_id`),
  ADD CONSTRAINT `item_table_ibfk_2` FOREIGN KEY (`loc_found_id`) REFERENCES `loc_found_table` (`loc_found_id`),
  ADD CONSTRAINT `item_table_ibfk_3` FOREIGN KEY (`loc_stored_id`) REFERENCES `loc_stored_table` (`loc_stored_id`);

--
-- Constraints for table `transaction_table`
--
ALTER TABLE `transaction_table`
  ADD CONSTRAINT `transaction_table_ibfk_1` FOREIGN KEY (`pub_id`) REFERENCES `user_info` (`pub_id`),
  ADD CONSTRAINT `transaction_table_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item_table` (`item_id`),
  ADD CONSTRAINT `transaction_table_ibfk_3` FOREIGN KEY (`user_respo`) REFERENCES `user_info` (`pub_id`),
  ADD CONSTRAINT `transaction_table_ibfk_4` FOREIGN KEY (`response_id`) REFERENCES `response_table` (`response_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
