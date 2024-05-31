-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2024 at 02:33 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
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
(1, 'UTETIWABO Diane', 'diane', 'utetidiane@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 'WhatsApp Image 2023-09-21 at 06.36.10.jpeg', 0, 60422);

-- --------------------------------------------------------

--
-- Table structure for table `authorized`
--

CREATE TABLE `authorized` (
  `created_at` datetime NOT NULL,
  `auID` int(10) NOT NULL,
  `authorized_name` varchar(255) NOT NULL,
  `authorized_gender` varchar(255) NOT NULL,
  `authorized_username` varchar(255) NOT NULL,
  `authorized_tel` varchar(12) NOT NULL,
  `authorized_mail` varchar(255) NOT NULL,
  `authorized_password` varchar(255) NOT NULL,
  `authorized_pin` int(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `role` varchar(25) NOT NULL,
  `department` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authorized`
--

INSERT INTO `authorized` (`created_at`, `auID`, `authorized_name`, `authorized_gender`, `authorized_username`, `authorized_tel`, `authorized_mail`, `authorized_password`, `authorized_pin`, `photo`, `status`, `role`, `department`) VALUES
('2023-11-01 04:12:12', 5, 'MWITENDE Sarah', 'male', 'Sarah', '250782305041', 'idtbusy@gmail.com', '37c39a3159861fb4eef2851c6697a289', 7874, 'optional', 'active', 'doctor', 'Surgery'),
('2023-11-01 03:25:52', 6, ' HATEGEKIMANA Paul', 'male', 'Paul', '250780490702', 'idtbusy@gmail.com', '130e4d59ec0334a41c52ce74a550f9b4', 4071, 'optional', 'active', 'doctor', 'Cardiology'),
('2023-11-03 03:17:34', 7, 'MWIZERWA Jean Pierre', 'male', 'jean', '250780490702', 'idtdanny@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 4727, 'optional', 'active', 'doctor', 'Internal Medecine');

-- --------------------------------------------------------

--
-- Table structure for table `cashier`
--

CREATE TABLE `cashier` (
  `created_at` datetime NOT NULL,
  `caID` int(25) NOT NULL,
  `cashier_name` varchar(255) NOT NULL,
  `cashier_username` varchar(255) NOT NULL,
  `cashier_gender` varchar(255) NOT NULL,
  `cashier_tel` varchar(12) NOT NULL,
  `cashier_mail` varchar(255) NOT NULL,
  `cashier_password` varchar(255) NOT NULL,
  `cashier_pin` int(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `cashier_balance` int(220) NOT NULL,
  `status` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cashier`
--

INSERT INTO `cashier` (`created_at`, `caID`, `cashier_name`, `cashier_username`, `cashier_gender`, `cashier_tel`, `cashier_mail`, `cashier_password`, `cashier_pin`, `photo`, `cashier_balance`, `status`, `role`) VALUES
('2023-11-01 08:11:27', 82, 'IDUKUNDATWESE Danny', 'Danny', 'male', '250780490702', 'idtbusy@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 4214, 'IMG_6505.JPG', 0, 'active', 'cashier'),
('2023-11-01 08:14:23', 83, 'INEZA Aliane', 'Dadyne', 'female', '250780312644', 'inezadadyne@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 1665, 'WhatsApp Image 2023-09-21 at 06.36.10.jpeg', 0, 'active', 'receptionist'),
('2023-11-03 03:09:47', 85, 'UTETIWABO Diane', 'diane', 'female', '250780547047', 'idtbusy@gmail.com', 'fa24933cca507d72dd9e0c42fc3237a2', 9646, 'WhatsApp Image 2023-09-21 at 06.36.10.jpeg', 0, 'active', 'receptionist'),
('2023-11-03 03:12:41', 86, 'IDUKUNDATWESE', 'Danny', 'male', '250780547047', 'idtbusy@gmail.com', 'a064ca7a813e17b8f56850ba165984dd', 9042, 'WhatsApp Image 2023-09-21 at 06.22.56.jpeg', 0, 'active', 'receptionist'),
('2023-11-05 01:28:24', 88, 'INEZA', 'Danny', 'female', '250780547047', 'idtdanny@gmail.com', 'dc093d6b19f2cab49c0a2dde55bf508a', 1454, 'WhatsApp Image 2023-08-31 at 8.31.31 PM.jpeg', 0, 'active', 'receptionist');

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
(26, 82, 'IDUKUNDATWESE Danny', 'Gasabo', 'Kacyiru'),
(27, 83, 'INEZA Aliane', 'Gasabo', 'Kacyiru'),
(29, 85, 'UTETIWABO Diane', 'Gasabo', 'Kacyiru'),
(30, 86, 'IDUKUNDATWESE', 'Gasabo', 'Kacyiru'),
(32, 88, 'INEZA', 'Kicukiro', 'Gatenga');

-- --------------------------------------------------------

--
-- Table structure for table `consult_form`
--

CREATE TABLE `consult_form` (
  `coID` int(255) NOT NULL,
  `created_on` date NOT NULL,
  `created_at` time NOT NULL,
  `patient_id` varchar(25) NOT NULL,
  `complains` text NOT NULL,
  `findings` text NOT NULL,
  `treatment` text DEFAULT NULL,
  `medecine` text DEFAULT NULL,
  `allergy` text DEFAULT NULL,
  `filled_by` varchar(25) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consult_form`
--

INSERT INTO `consult_form` (`coID`, `created_on`, `created_at`, `patient_id`, `complains`, `findings`, `treatment`, `medecine`, `allergy`, `filled_by`, `status`) VALUES
(1, '2023-11-01', '18:51:00', '0000695191', 'Chest Pain', 'Possible block in Artery', 'Angiogram, Surgery', 'none', 'none', 'Sarah', 'paid'),
(5, '2023-11-03', '16:19:00', '0000575321', 'GKJGJH', 'dhgdfhjdg', 'none', 'none', 'none', 'jean', 'paid'),
(6, '2023-11-03', '16:24:00', '0000575321', 'gdfjkdgd', 'dfsgsdg', 'none', 'none', 'none', 'jean', 'paid'),
(7, '2023-12-17', '17:14:00', '0000695191', 'Headache, Back Pain, Stomachache', 'gjhgjk', 'cnjhhkgj', 'jhfhjg', 'ttfyjmhjk', 'jean', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `diagnose_form`
--

CREATE TABLE `diagnose_form` (
  `dgID` int(255) NOT NULL,
  `created_on` date NOT NULL,
  `created_at` time NOT NULL,
  `patient_id` varchar(25) NOT NULL,
  `diagnose_name` text NOT NULL,
  `description` text NOT NULL,
  `complications` text DEFAULT NULL,
  `allergy` text DEFAULT NULL,
  `filled_by` varchar(25) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diagnose_form`
--

INSERT INTO `diagnose_form` (`dgID`, `created_on`, `created_at`, `patient_id`, `diagnose_name`, `description`, `complications`, `allergy`, `filled_by`, `status`) VALUES
(1, '2023-11-01', '19:54:00', '0000695191', 'Angiogram', 'Blocked artery', 'none', 'none', 'Sarah', 'paid'),
(7, '2023-12-17', '17:18:00', '0000695191', 'EEYTUYU', 'jhkljkgjfg', 'fjhfhjgj', 'hghjkuih', 'jean', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `fees_list`
--

CREATE TABLE `fees_list` (
  `created_at` datetime NOT NULL,
  `feID` int(25) NOT NULL,
  `fees_name` varchar(25) NOT NULL,
  `fees_description` text NOT NULL,
  `amount` varchar(25) NOT NULL,
  `modified_by` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees_list`
--

INSERT INTO `fees_list` (`created_at`, `feID`, `fees_name`, `fees_description`, `amount`, `modified_by`) VALUES
('2023-11-02 09:36:35', 1, 'Consultation', 'consultation', '10000', 'admin'),
('2023-11-02 09:46:21', 2, 'diagnosis', 'optional', '10000', 'admin'),
('2023-11-02 09:47:08', 3, 'surgery', 'optional', '50000', 'admin'),
('2023-11-02 10:45:15', 5, 'treatment', 'optional', '12000', 'admin'),
('2023-11-02 10:45:33', 6, 'medecine', 'optional', '5000', 'admin');

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
(68, '2023-09-20', '08:09:35', '0000695191', '6133', '1000', 'topup', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `created_at` datetime DEFAULT NULL,
  `pID` int(255) NOT NULL,
  `patient_id` varchar(255) DEFAULT NULL,
  `patient_profile` text NOT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `patient_mname` varchar(25) NOT NULL,
  `patient_lname` varchar(25) NOT NULL,
  `patient_gender` varchar(25) NOT NULL,
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

INSERT INTO `patient` (`created_at`, `pID`, `patient_id`, `patient_profile`, `patient_name`, `patient_mname`, `patient_lname`, `patient_gender`, `patient_tel`, `patient_mail`, `patient_balance`, `referral_cashier`, `status`, `approve`, `patient_pin`) VALUES
('2023-11-02 07:40:00', 32, '0000695191', 'optional', 'SHEMA ', 'MUTABARUKA', 'Fidele', 'male', '250780547047', 'optional@gmail.com', 500, 'Dadyne', 'active', 'Admitted', 3597),
('2023-11-02 07:42:32', 33, '0000575321', 'optional', 'MUBERARUGO', 'Marie', 'Jeanne', 'female', '250780547047', 'optional@gmail.com', 0, 'Dadyne', 'active', 'Admitted', 7541),
('2023-11-03 03:29:30', 34, '0000575322', 'optional', 'UTETIWABO', '', 'Diane', 'female', '250789893626', 'optional@gmail.com', 0, 'Dadyne', 'active', 'Admitted', 5625),
('2023-12-12 05:57:39', 35, '0000575322', 'optional', 'MUTIMA', '', 'Marriam', 'Choose ...', '250780547047', 'optional@gmail.com', 0, 'Admin', 'active', 'Admitted', 1248),
('2023-12-17 04:09:54', 36, '0000695181', 'optional', 'CYEMAYIRE', '', 'Ildephonse', 'male', '250788674352', 'optional@gmail.com', 0, 'Dadyne', 'active', 'Admitted', 4772);

-- --------------------------------------------------------

--
-- Table structure for table `patient_location`
--

CREATE TABLE `patient_location` (
  `no` int(255) NOT NULL,
  `pID` int(255) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `sector` varchar(255) NOT NULL,
  `cell` varchar(25) NOT NULL,
  `village` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_location`
--

INSERT INTO `patient_location` (`no`, `pID`, `patient_name`, `district`, `sector`, `cell`, `village`) VALUES
(1, 22, 'MUGISHA EMMANUEL', 'Optional', 'Optional', '', ''),
(11, 32, 'SHEMA ', 'Gasabo', 'Kacyiru', 'Kamutwa ', 'Kanserege'),
(12, 33, 'MUBERARUGO', 'Kicukiro', 'Gatenga', 'Gatenga', 'Gatenga'),
(13, 34, 'UTETIWABO', 'Kicukiro', 'Gatenga', 'Gatenga', 'Gatenga'),
(14, 35, 'MUTIMA', 'Kicukiro', 'Gatenga', 'Gatenga', 'Gatenga'),
(15, 36, 'CYEMAYIRE', 'Kicukiro', 'Gatenga', 'Gatenga', 'Gatenga');

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
('2023-09-16 06:34:58', 55, 'BIZIMANA Eugene', 111189338, 'idtbusy@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 9703, 'others', 10900, 'Inactive', 'SimbaLogo1.jpeg', 'admin'),
('2023-09-20 07:25:40', 56, 'NDIHOKUBWAYO Emmanuel', 102345698, 'rubis@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 5125, 'gas', 0, 'Inactive', 'rubis.png', 'admin');

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
  `status` text DEFAULT NULL,
  `refer_id` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`no`, `rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`, `refer_id`) VALUES
(10, '2023-11-02', '12:21:00', 5, '0000695191', 'SHEMA ', 10000, 'Consultation', 'approved', '1'),
(17, '2023-11-03', '16:19:00', 7, '0000575321', 'MUBERARUGO', 10000, 'Consultation', 'approved', '5'),
(18, '2023-11-03', '16:24:00', 7, '0000575321', 'MUBERARUGO', 15000, 'Consultation', 'approved', '6'),
(19, '2023-12-17', '16:25:00', 7, '0000695191', 'SHEMA ', 50000, 'surgery', 'approved', '6'),
(20, '2023-12-17', '17:14:00', 7, '0000695191', 'SHEMA ', 15000, 'Consultation', 'approved', '7'),
(21, '2023-12-17', '17:18:00', 7, '0000695191', 'SHEMA ', 10000, 'diagnosis', 'approved', '7'),
(22, '2023-12-17', '17:19:00', 7, '0000695191', 'SHEMA ', 50000, 'surgery', 'approved', '7');

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

-- --------------------------------------------------------

--
-- Table structure for table `surgery_form`
--

CREATE TABLE `surgery_form` (
  `suID` int(255) NOT NULL,
  `created_on` date NOT NULL,
  `created_at` time NOT NULL,
  `patient_id` varchar(25) NOT NULL,
  `description` text NOT NULL,
  `complications` text DEFAULT NULL,
  `allergy` text DEFAULT NULL,
  `filled_by` varchar(25) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surgery_form`
--

INSERT INTO `surgery_form` (`suID`, `created_on`, `created_at`, `patient_id`, `description`, `complications`, `allergy`, `filled_by`, `status`) VALUES
(1, '2023-11-01', '20:55:00', '0000695191', 'Artery Block Remove', 'Vein cut in the left', 'Reactive to sedatives', 'Sarah', 'paid'),
(6, '2023-12-17', '16:25:00', '0000695191', 'Gjhghasd', 'Gjgjgsdfksj', 'Gjhjgdsfsks', 'jean', 'paid'),
(7, '2023-12-17', '17:19:00', '0000695191', 'cgfhgk', 'fjgjkhk', 'gkkhkj', 'jean', 'paid');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_ID`);

--
-- Indexes for table `authorized`
--
ALTER TABLE `authorized`
  ADD PRIMARY KEY (`auID`);

--
-- Indexes for table `cashier`
--
ALTER TABLE `cashier`
  ADD PRIMARY KEY (`caID`);

--
-- Indexes for table `cashier_location`
--
ALTER TABLE `cashier_location`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `consult_form`
--
ALTER TABLE `consult_form`
  ADD PRIMARY KEY (`coID`);

--
-- Indexes for table `diagnose_form`
--
ALTER TABLE `diagnose_form`
  ADD PRIMARY KEY (`dgID`);

--
-- Indexes for table `fees_list`
--
ALTER TABLE `fees_list`
  ADD PRIMARY KEY (`feID`);

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
-- Indexes for table `surgery_form`
--
ALTER TABLE `surgery_form`
  ADD PRIMARY KEY (`suID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_ID` int(225) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `authorized`
--
ALTER TABLE `authorized`
  MODIFY `auID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cashier`
--
ALTER TABLE `cashier`
  MODIFY `caID` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `cashier_location`
--
ALTER TABLE `cashier_location`
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `consult_form`
--
ALTER TABLE `consult_form`
  MODIFY `coID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `diagnose_form`
--
ALTER TABLE `diagnose_form`
  MODIFY `dgID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fees_list`
--
ALTER TABLE `fees_list`
  MODIFY `feID` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notification_all`
--
ALTER TABLE `notification_all`
  MODIFY `nID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `pID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `patient_location`
--
ALTER TABLE `patient_location`
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `reID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `surgery_form`
--
ALTER TABLE `surgery_form`
  MODIFY `suID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pharmacy_location`
--
ALTER TABLE `pharmacy_location`
  ADD CONSTRAINT `pharmacy_location_ibfk_1` FOREIGN KEY (`phID`) REFERENCES `pharmacy` (`phID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
