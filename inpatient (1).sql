-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 02, 2023 at 12:16 AM
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
-- Database: `inpatient`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_ID` int(225) NOT NULL,
  `admin_name` varchar(25) NOT NULL,
  `admin_username` varchar(25) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_profile` varchar(255) NOT NULL,
  `Balance` int(255) NOT NULL,
  `admin_pin` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_ID`, `admin_name`, `admin_username`, `admin_email`, `admin_password`, `admin_profile`, `Balance`, `admin_pin`) VALUES
(1, 'UTETIWABO DIANE', 'diane', 'utetidiane@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 'WhatsApp Image 2023-09-21 at 06.36.10.jpeg', 614600, 60422);

-- --------------------------------------------------------

--
-- Table structure for table `cashier`
--

CREATE TABLE `cashier` (
  `created_at` datetime NOT NULL,
  `caID` int(10) NOT NULL,
  `cashier_name` varchar(255) NOT NULL,
  `cashier_gender` varchar(255) NOT NULL,
  `cashier_username` varchar(255) NOT NULL,
  `cashier_tel` varchar(12) NOT NULL,
  `cashier_mail` varchar(255) NOT NULL,
  `cashier_password` varchar(255) NOT NULL,
  `cashier_pin` int(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `cashier_balance` int(220) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cashier`
--

INSERT INTO `cashier` (`created_at`, `caID`, `cashier_name`, `cashier_gender`, `cashier_username`, `cashier_tel`, `cashier_mail`, `cashier_password`, `cashier_pin`, `photo`, `cashier_balance`, `status`) VALUES
('2023-06-06 02:22:03', 36, 'RUMUMBA Patrice', 'male', 'Patrice', '257886754123', 'patrice@gmail.com', '597673be8ea7215c682c809347ba60ec', 5502, 'download.jpg', 23000, 'active'),
('2023-09-01 17:02:12', 46, 'NYIRAMINANI Maria', 'female', 'Maria', '257886754123', 'maria@gmail.com', '202cb962ac59075b964b07152d234b70', 5617, 're.PNG', 51300, 'active'),
('2023-09-04 09:06:27', 49, 'NSHUTI Yves', 'male', 'Yves', '257886754123', 'nshuti@gmail.com', '202cb962ac59075b964b07152d234b70', 2703, 'NSHUTI.PNG', 10000, 'active'),
('2023-09-02 03:10:09', 50, 'KAGABO Yvette', 'female', 'kagabo', '257886754123', 'kagabo@gmail.com', '202cb962ac59075b964b07152d234b70', 2307, 'mik.PNG', 290000, 'active'),
('2023-09-08 06:50:58', 53, 'IDUKUNDATWESE DANNY', 'male', 'Idt', '257886754123', 'idtdanny@gmail.com', '11c8b3d8abc3809f566f9e259e1d55e7', 5931, 'WhatsApp Image 2023-08-07 at 21.27.01.jpeg', 225600, 'active'),
('2023-09-19 09:05:32', 75, 'MUJAWABERA Josee', 'female', 'josee', '250780012634', 'ireneudiane@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 6133, 'user_photo_2.png', 99000, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `cashier_location`
--

CREATE TABLE `cashier_location` (
  `no` int(255) NOT NULL,
  `caID` int(255) NOT NULL,
  `cashier_name` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `sector` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashier_location`
--

INSERT INTO `cashier_location` (`no`, `caID`, `cashier_name`, `district`, `sector`) VALUES
(19, 75, 'MUJAWABERA Josee', 'Nyamagabe', 'Ubutwari');

-- --------------------------------------------------------

--
-- Table structure for table `notification_all`
--

CREATE TABLE `notification_all` (
  `nID` int(255) NOT NULL,
  `date_sent` date NOT NULL,
  `time_sent` time NOT NULL,
  `receiver_id` varchar(255) NOT NULL,
  `sender_id` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `action` varchar(25) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_all`
--

INSERT INTO `notification_all` (`nID`, `date_sent`, `time_sent`, `receiver_id`, `sender_id`, `amount`, `action`, `status`) VALUES
(1, '2023-09-16', '00:00:00', '0000575321', '5931', '200', 'topup', 'read'),
(2, '2023-09-16', '00:00:00', '0000575321', '5931', '1000', 'topup', 'read'),
(44, '2023-09-16', '04:37:42', '0000575322', '5931', '1000', 'topup', 'unread'),
(45, '2023-09-16', '04:39:58', '0000575322', '5931', '1000', 'topup', 'unread'),
(46, '2023-09-16', '04:40:45', '0000575322', '5931', '2000', 'withdraw', 'unread'),
(47, '2023-09-17', '12:15:49', '111189338', '0000575321', '1000', 'payment', 'read'),
(48, '2023-09-17', '12:19:40', '111189338', '0000695191', '10000', 'payment', 'read'),
(49, '2023-09-17', '12:22:06', '111189338', '0000695192', '900', 'payment', 'unread'),
(50, '2023-09-17', '07:31:03', '5931', 'admin', '1000', 'recharge', 'read'),
(51, '2023-09-17', '07:32:48', '5931', 'admin', '1000', 'recharge', 'unread'),
(52, '2023-09-17', '10:46:03', '5931', 'admin', '10000', 'transfer', 'unread'),
(53, '2023-09-17', '10:47:35', '5931', 'admin', '10000', 'transfer', 'unread'),
(54, '2023-09-17', '10:54:55', '5931', 'admin', '50000', 'recharge', 'unread'),
(55, '2023-09-17', '11:08:43', '5931', 'admin', '10000', 'transfer', 'unread'),
(56, '2023-09-17', '11:38:14', '2703', 'admin', '10000', 'recharge', 'unread'),
(57, '2023-09-18', '01:39:34', '111189338', 'admin', '500', 'transfer', 'unread'),
(58, '2023-09-20', '06:55:07', '6133', 'admin', '1000', 'recharge', 'unread'),
(59, '2023-09-20', '07:21:52', '6133', 'admin', '1000', 'transfer', 'unread'),
(60, '2023-09-20', '07:31:05', '102345698', '0000695191', '3900', 'payment', 'unread'),
(61, '2023-09-20', '07:32:38', '102345698', '0000575321', '2000', 'payment', 'unread'),
(62, '2023-09-20', '07:32:42', '102345698', '0000575321', '2000', 'payment', 'unread'),
(63, '2023-09-20', '07:37:43', '102345698', 'admin', '7900', 'transfer', 'unread'),
(64, '2023-09-20', '07:40:26', '6133', 'admin', '100000', 'recharge', 'unread'),
(65, '2023-09-20', '07:44:14', '0000695191', '6133', '50000', 'topup', 'unread'),
(66, '2023-09-20', '07:44:17', '0000695191', '6133', '50000', 'topup', 'unread'),
(67, '2023-09-20', '08:05:15', '0000695191', '6133', '2000', 'topup', 'unread'),
(68, '2023-09-20', '08:09:35', '0000695191', '6133', '1000', 'topup', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `created_at` datetime DEFAULT NULL,
  `pID` int(255) NOT NULL,
  `patient_id` varchar(255) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `patient_tel` varchar(12) NOT NULL,
  `patient_mail` varchar(25) NOT NULL,
  `patient_balance` int(255) NOT NULL,
  `referral_cashier` varchar(255) NOT NULL,
  `status` text DEFAULT NULL,
  `approve` text DEFAULT NULL,
  `patient_pin` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`created_at`, `pID`, `patient_id`, `patient_name`, `patient_tel`, `patient_mail`, `patient_balance`, `referral_cashier`, `status`, `approve`, `patient_pin`) VALUES
('2023-01-28 01:12:23', 20, '0000695191', 'MUKESHIMANA', '250788778999', 'idtbusy@gmail.com', 103000, '5931', 'active', 'Approved', 7965),
('2023-04-28 01:12:23', 21, '0000695192', 'MUGISHA', '25078855788', 'mugisha@gmail.com', 11000, '5931', 'active', 'Approved', 4325),
('2023-09-14 12:48:32', 22, '0000575321', 'MUGISHA EMMANUEL', 'Optional', 'idtbusy@gmail.com', 9000, '5931', 'active', 'Approved', 87843),
('2023-09-16 12:59:55', 23, '0000575322', 'INEZA Aliane', '250780012634', 'ireneudiane@gmail.com', 3000, '5931', 'active', 'Approved', 21701);

-- --------------------------------------------------------

--
-- Table structure for table `patient_location`
--

CREATE TABLE `patient_location` (
  `no` int(255) NOT NULL,
  `pID` int(255) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `sector` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_location`
--

INSERT INTO `patient_location` (`no`, `pID`, `patient_name`, `district`, `sector`) VALUES
(1, 22, 'MUGISHA EMMANUEL', 'Optional', 'Optional'),
(2, 23, 'INEZA Aliane', 'Gasabo', 'Kacyiru');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy`
--

CREATE TABLE `pharmacy` (
  `Date` datetime NOT NULL,
  `phID` int(255) NOT NULL,
  `pharmacy_name` varchar(255) NOT NULL,
  `pharmacy_tin` int(9) NOT NULL,
  `pharmacy_mail` varchar(255) NOT NULL,
  `pharmacy_password` varchar(255) NOT NULL,
  `pharmacy_pin` int(255) NOT NULL,
  `pharmacy_type` varchar(255) NOT NULL,
  `balance` int(255) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `approved_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy`
--

INSERT INTO `pharmacy` (`Date`, `phID`, `pharmacy_name`, `pharmacy_tin`, `pharmacy_mail`, `pharmacy_password`, `pharmacy_pin`, `pharmacy_type`, `balance`, `status`, `photo`, `approved_by`) VALUES
('2022-12-27 01:08:06', 11, 'IDA TECHNOLOGY', 120582059, 'ida@gmail.com', 'e7e158399a1fe6378cf2dcc1996b1848', 1748, 'others', 4100, 'Active', 'IMG_6505.JPG', 'N/A'),
('2023-01-28 09:34:12', 12, 'ENGEN RDA', 100800300, 'engen@gmail.com', 'e2a01a3c474b5068e68073afe5669468', 1496, 'gas', 800, 'Active', 'ENGEN.PNG', 'admin'),
('2022-12-22 01:32:16', 14, 'Quincallelie', 100999777, 'quin@gmail.com', '5524e1290a1549764984c32c23b06938', 8491, 'others', 300000, 'Active', 'NSHUTI.PNG', 'N/A'),
('2023-09-16 06:34:58', 55, 'SIMBA SuperMarket Ltd', 111189338, 'idtbusy@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 9703, 'others', 10900, 'Inactive', 'SimbaLogo1.jpeg', 'admin'),
('2023-09-20 07:25:40', 56, 'Rubis Energy Rwanda', 102345698, 'rubis@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 5125, 'gas', 0, 'Inactive', 'rubis.png', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_location`
--

CREATE TABLE `pharmacy_location` (
  `no` int(255) NOT NULL,
  `phID` int(255) NOT NULL,
  `pharmacy_tin` int(9) NOT NULL,
  `district` varchar(255) NOT NULL,
  `sector` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_location`
--

INSERT INTO `pharmacy_location` (`no`, `phID`, `pharmacy_tin`, `district`, `sector`) VALUES
(9, 55, 111189338, 'Gasabo', 'Kacyiru'),
(10, 56, 102345698, 'Gasabo', 'Kacyiru');

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `no` int(255) NOT NULL,
  `rdate` date DEFAULT NULL,
  `rtime` time NOT NULL,
  `rID` int(9) NOT NULL,
  `patient_id` varchar(255) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `amount` int(255) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `status` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`no`, `rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`) VALUES
(2, '2023-09-17', '12:15:49', 111189338, '0000575321', 'MUGISHA EMMANUEL', 1000, 'pcaID', 'approved'),
(3, '2023-09-17', '12:19:40', 111189338, '0000695191', 'MUKESHIMANA', 10000, 'pcaID', 'approved'),
(4, '2023-09-17', '12:22:06', 111189338, '0000695192', 'MUGISHA', 900, 'pcaID', 'approved'),
(5, '2023-09-20', '07:31:05', 102345698, '0000695191', 'MUKESHIMANA', 3900, 'pcaID', 'approved'),
(6, '2023-09-20', '07:32:38', 102345698, '0000575321', 'MUGISHA', 2000, 'pcaID', 'approved'),
(7, '2023-09-20', '07:32:42', 102345698, '0000575321', 'MUGISHA', 2000, 'pcaID', 'approved'),
(8, '2023-09-20', '08:05:15', 6133, '0000695191', 'MUKESHIMANA', NULL, 'topup', 'approved'),
(9, '2023-09-20', '08:09:35', 6133, '0000695191', 'MUKESHIMANA', NULL, 'topup', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `reID` int(255) NOT NULL,
  `request_date` date NOT NULL,
  `request_time` time NOT NULL,
  `confirmed_date` date DEFAULT NULL,
  `confirmed_time` time DEFAULT NULL,
  `request_type` varchar(25) NOT NULL,
  `user_id` varchar(25) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `activation_key` varchar(25) NOT NULL,
  `status` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`reID`, `request_date`, `request_time`, `confirmed_date`, `confirmed_time`, `request_type`, `user_id`, `amount`, `activation_key`, `status`) VALUES
(1, '2023-09-17', '10:06:15', '2023-09-17', '11:08:43', 'withdraw', '5931', '10000', 'pIEGWMrsnb', 'confirmed'),
(2, '2023-09-18', '01:26:59', '2023-09-18', '01:39:34', 'withdraw', '111189338', '500', 'ZJO2cKS7Ft', 'confirmed'),
(3, '2023-09-20', '07:01:21', '2023-09-20', '07:21:52', 'withdraw', '6133', '1000', 'bKkGZedNwh', 'confirmed'),
(4, '2023-09-20', '07:35:46', '2023-09-20', '07:37:43', 'withdraw', '102345698', '7900', 'r6cDu1FVwz', 'confirmed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_ID`);

--
-- Indexes for table `cashier`
--
ALTER TABLE `cashier`
  ADD PRIMARY KEY (`caID`);

--
-- Indexes for table `cashier_location`
--
ALTER TABLE `cashier_location`
  ADD PRIMARY KEY (`no`),
  ADD KEY `caID` (`caID`),
  ADD KEY `caID_2` (`caID`);

--
-- Indexes for table `notification_all`
--
ALTER TABLE `notification_all`
  ADD PRIMARY KEY (`nID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`pID`);

--
-- Indexes for table `patient_location`
--
ALTER TABLE `patient_location`
  ADD PRIMARY KEY (`no`),
  ADD KEY `pID` (`pID`);

--
-- Indexes for table `pharmacy`
--
ALTER TABLE `pharmacy`
  ADD PRIMARY KEY (`phID`),
  ADD UNIQUE KEY `pharmacy_tin` (`pharmacy_tin`);

--
-- Indexes for table `pharmacy_location`
--
ALTER TABLE `pharmacy_location`
  ADD PRIMARY KEY (`no`),
  ADD KEY `phID` (`phID`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`reID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_ID` int(225) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cashier`
--
ALTER TABLE `cashier`
  MODIFY `caID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `cashier_location`
--
ALTER TABLE `cashier_location`
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notification_all`
--
ALTER TABLE `notification_all`
  MODIFY `nID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `pID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `patient_location`
--
ALTER TABLE `patient_location`
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pharmacy`
--
ALTER TABLE `pharmacy`
  MODIFY `phID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `pharmacy_location`
--
ALTER TABLE `pharmacy_location`
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `reID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cashier_location`
--
ALTER TABLE `cashier_location`
  ADD CONSTRAINT `cashier_location_ibfk_1` FOREIGN KEY (`caID`) REFERENCES `cashier` (`caID`);

--
-- Constraints for table `patient_location`
--
ALTER TABLE `patient_location`
  ADD CONSTRAINT `patient_location_ibfk_1` FOREIGN KEY (`pID`) REFERENCES `patient` (`pID`);

--
-- Constraints for table `pharmacy_location`
--
ALTER TABLE `pharmacy_location`
  ADD CONSTRAINT `pharmacy_location_ibfk_1` FOREIGN KEY (`phID`) REFERENCES `pharmacy` (`phID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
