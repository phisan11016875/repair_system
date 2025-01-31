-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 09:15 AM
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
-- Database: `repair_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `dept_name`, `created_at`) VALUES
(1, 'ฝ่ายบริหาร', '2025-01-09 07:38:10'),
(2, 'ฝ่ายการพยาบาล', '2025-01-09 07:38:10'),
(3, 'ฝ่ายเภสัชกรรม', '2025-01-09 07:38:10'),
(4, 'ฝ่ายทันตกรรม', '2025-01-09 07:38:10'),
(5, 'งานซ่อมบำรุง', '2025-01-09 07:38:10'),
(6, 'แพทย์ทางเลือก(ฝังเข็ม)', '2025-01-15 02:24:59'),
(7, 'แพทย์แผนไทย(IPD)', '2025-01-15 02:25:15'),
(8, 'แพทย์แผนไทย(OPD', '2025-01-15 02:25:49'),
(9, 'อุบัติเหตุฉุกเฉิน', '2025-01-15 02:27:16'),
(10, 'งานกายภาพบำบัด', '2025-01-15 02:27:19'),
(11, 'งานธุรการ', '2025-01-15 02:27:53'),
(12, 'งานการเงิน', '2025-01-15 02:28:00'),
(13, 'งานบัญชื', '2025-01-15 02:28:14'),
(14, 'จุดซักประวัติ', '2025-01-15 02:28:34'),
(15, 'งานพัสุด', '2025-01-15 02:28:51'),
(16, 'งานประกันสุขภาพ', '2025-01-15 02:29:09'),
(17, 'งานเทคนิกการแพทย์', '2025-01-15 02:30:50'),
(18, 'งานผลิต', '2025-01-15 02:31:13'),
(19, 'งานไอที', '2025-01-15 02:31:27'),
(20, 'Wellness Center', '2025-01-15 02:31:54'),
(21, 'งานเวชระเบียน', '2025-01-15 02:32:17'),
(22, 'จุดซักประวัติ', '2025-01-15 02:32:31'),
(23, 'ห้องตรวจแพทย์', '2025-01-15 02:32:50'),
(24, 'งานซ่อมบำรุง', '2025-01-15 02:33:09'),
(25, 'งานยานพาหนะ', '2025-01-15 02:33:43'),
(26, 'งานซักฟอกจ่ายกลาง', '2025-01-15 02:34:35');

-- --------------------------------------------------------

--
-- Table structure for table `fsn_classes`
--

CREATE TABLE `fsn_classes` (
  `group_id` varchar(2) NOT NULL,
  `class_id` varchar(2) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `fsn_classes`
--

INSERT INTO `fsn_classes` (`group_id`, `class_id`, `class_name`, `description`, `created_by`, `created_at`) VALUES
('20', '01', 'เครื่องมือตรวจโรคทั่วไป', 'อุปกรณ์ตรวจวินิจฉัยทั่วไป', 1, '0000-00-00 00:00:00'),
('20', '02', 'เครื่องมือผ่าตัด', 'อุปกรณ์ในห้องผ่าตัด', 1, '0000-00-00 00:00:00'),
('20', '03', 'เครื่องมือวัดสัญญาณชีพ', 'อุปกรณ์วัดและติดตามสัญญาณชีพ', 1, '0000-00-00 00:00:00'),
('20', '04', 'เครื่องมือทันตกรรม', 'อุปกรณ์ทำฟันและรักษาช่องปาก', 1, '0000-00-00 00:00:00'),
('20', '05', 'เครื่องมือกายภาพบำบัด', 'อุปกรณ์กายภาพบำบัด', 1, '0000-00-00 00:00:00'),
('20', '06', 'เครื่องมือวิเคราะห์', 'อุปกรณ์ตรวจวิเคราะห์ทางการแพทย์', 1, '0000-00-00 00:00:00'),
('20', '07', 'เครื่องมือช่วยชีวิต', 'อุปกรณ์ช่วยชีวิตฉุกเฉิน', 1, '0000-00-00 00:00:00'),
('20', '08', 'เครื่องเอกซเรย์', 'อุปกรณ์เอกซเรย์และถ่ายภาพ', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `fsn_groups`
--

CREATE TABLE `fsn_groups` (
  `group_id` varchar(2) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `fsn_groups`
--

INSERT INTO `fsn_groups` (`group_id`, `group_name`, `description`, `created_by`, `created_at`) VALUES
('10', 'ครุภัณฑ์สำนักงาน', 'เครื่องใช้สำนักงานต่างๆ', 1, '0000-00-00 00:00:00'),
('20', 'ครุภัณฑ์การแพทย์', 'อุปกรณ์และเครื่องมือทางการแพทย์', 1, '0000-00-00 00:00:00'),
('30', 'ครุภัณฑ์คอมพิวเตอร์', 'อุปกรณ์คอมพิวเตอร์และอิเล็กทรอนิกส์', 1, '0000-00-00 00:00:00'),
('40', 'ครุภัณฑ์งานบ้าน', 'อุปกรณ์เครื่องใช้ในครัวและงานบ้าน', 1, '0000-00-00 00:00:00'),
('50', 'ครุภัณฑ์ยานพาหนะ', 'ยานพาหนะและอุปกรณ์การขนส่ง', 1, '0000-00-00 00:00:00'),
('60', 'ครุภัณฑ์การศึกษา', 'อุปกรณ์การเรียนการสอน', 1, '0000-00-00 00:00:00'),
('70', 'ครุภัณฑ์โฆษณาและเผยแพร่', 'อุปกรณ์สื่อสารและประชาสัมพันธ์', 1, '0000-00-00 00:00:00'),
('80', 'ครุภัณฑ์การเกษตร', 'อุปกรณ์การเกษตรและการประมง', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `fsn_types`
--

CREATE TABLE `fsn_types` (
  `group_class` varchar(4) NOT NULL,
  `type_id` varchar(3) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `fsn_types`
--

INSERT INTO `fsn_types` (`group_class`, `type_id`, `type_name`, `description`, `created_by`, `created_at`) VALUES
('2001', '001', 'เครื่องวัดความดันโลหิต', 'เครื่องวัดความดันแบบดิจิตอล', 1, '0000-00-00 00:00:00'),
('2001', '002', 'เครื่องวัดอุณหภูมิ', 'เครื่องวัดไข้ดิจิตอล', 1, '0000-00-00 00:00:00'),
('2001', '003', 'หูฟังแพทย์', 'Stethoscope', 1, '0000-00-00 00:00:00'),
('2001', '004', 'เครื่องชั่งน้ำหนัก', 'เครื่องชั่งน้ำหนักดิจิตอล', 1, '0000-00-00 00:00:00'),
('2001', '005', 'เครื่องวัดออกซิเจน', 'Oxygen Saturation Monitor', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `psd_log`
--

CREATE TABLE `psd_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `psd_id` int(11) NOT NULL,
  `depid_old` int(11) NOT NULL,
  `depid_new` int(11) NOT NULL,
  `date_move` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psd_move`
--

CREATE TABLE `psd_move` (
  `move_id` int(11) NOT NULL,
  `psd_id` int(11) NOT NULL,
  `deptid_old` int(11) NOT NULL,
  `deptid_new` int(11) NOT NULL,
  `move_cause` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `date_move` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psd_recieve`
--

CREATE TABLE `psd_recieve` (
  `psd_id` int(11) NOT NULL,
  `fsn_number` varchar(11) DEFAULT NULL,
  `fsn_group` varchar(2) DEFAULT NULL,
  `fsn_class` varchar(2) DEFAULT NULL,
  `fsn_type` varchar(3) DEFAULT NULL,
  `fsn_description` varchar(4) DEFAULT NULL,
  `brand_name` varchar(100) NOT NULL,
  `models` varchar(100) DEFAULT NULL,
  `type_recieve` varchar(50) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `psd_total` int(11) DEFAULT 1,
  `date_recieve` date DEFAULT NULL,
  `psd_status` enum('active','repair','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psd_repair`
--

CREATE TABLE `psd_repair` (
  `repair_id` int(11) NOT NULL,
  `psd_id` int(11) NOT NULL,
  `repair_cause` text NOT NULL,
  `repair_detail` text DEFAULT NULL,
  `repair_status` enum('waiting','in_progress','completed','cancelled') DEFAULT 'waiting',
  `user1` int(11) NOT NULL,
  `technician` int(11) DEFAULT NULL,
  `date_1` date NOT NULL,
  `date_2` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psd_users`
--

CREATE TABLE `psd_users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `pass_word` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `level` enum('admin','technician','user') DEFAULT 'user',
  `position` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `psd_users`
--

INSERT INTO `psd_users` (`user_id`, `user_name`, `pass_word`, `fullname`, `email`, `telephone`, `dept_id`, `level`, `position`, `reset_token`, `reset_expires`, `created_at`, `last_login`) VALUES
(1, 'admin', '$2y$10$L65FYsdWLGCw9nwBlzNhLerecFRUfvH4Se6GwQJtdHbGFLMJWbZKS', 'ผู้ดูแลระบบ', 'admin@hospital.local', NULL, 1, 'admin', NULL, NULL, NULL, '2025-01-09 07:38:10', '2025-01-29 11:07:38'),
(2, 'phisan', '$2y$10$L65FYsdWLGCw9nwBlzNhLerecFRUfvH4Se6GwQJtdHbGFLMJWbZKS', 'พิศาล  ศรีเชียงสา', 'hinotas02@gmail.com', '0917864975', NULL, 'admin', 'IT', NULL, NULL, '2025-01-10 04:06:26', '2025-01-16 08:45:49'),
(6, 'user', '$2y$10$L65FYsdWLGCw9nwBlzNhLerecFRUfvH4Se6GwQJtdHbGFLMJWbZKS', 'usertest', 'livesome10@hotmail.com', '0917894975', 5, 'user', NULL, NULL, NULL, '2025-01-14 05:01:16', '2025-01-22 13:58:43'),
(7, 'technic', '$2y$10$L65FYsdWLGCw9nwBlzNhLerecFRUfvH4Se6GwQJtdHbGFLMJWbZKS', 'techmic_test1', 'livesome15@hotmail.com', '0917894975', 2, 'technician', NULL, NULL, NULL, '2025-01-14 07:40:57', '2025-01-22 12:08:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `fsn_classes`
--
ALTER TABLE `fsn_classes`
  ADD PRIMARY KEY (`group_id`,`class_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `fsn_groups`
--
ALTER TABLE `fsn_groups`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `fsn_types`
--
ALTER TABLE `fsn_types`
  ADD PRIMARY KEY (`group_class`,`type_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `psd_log`
--
ALTER TABLE `psd_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `psd_id` (`psd_id`),
  ADD KEY `depid_old` (`depid_old`),
  ADD KEY `depid_new` (`depid_new`);

--
-- Indexes for table `psd_move`
--
ALTER TABLE `psd_move`
  ADD PRIMARY KEY (`move_id`),
  ADD KEY `psd_id` (`psd_id`),
  ADD KEY `deptid_old` (`deptid_old`),
  ADD KEY `deptid_new` (`deptid_new`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `psd_recieve`
--
ALTER TABLE `psd_recieve`
  ADD PRIMARY KEY (`psd_id`),
  ADD UNIQUE KEY `fsn_number` (`fsn_number`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `idx_fsn_number` (`fsn_number`),
  ADD KEY `idx_fsn_group` (`fsn_group`),
  ADD KEY `idx_fsn_class` (`fsn_class`),
  ADD KEY `idx_fsn_type` (`fsn_type`);

--
-- Indexes for table `psd_repair`
--
ALTER TABLE `psd_repair`
  ADD PRIMARY KEY (`repair_id`),
  ADD KEY `psd_id` (`psd_id`),
  ADD KEY `user1` (`user1`),
  ADD KEY `technician` (`technician`);

--
-- Indexes for table `psd_users`
--
ALTER TABLE `psd_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `dept_id` (`dept_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `psd_log`
--
ALTER TABLE `psd_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psd_move`
--
ALTER TABLE `psd_move`
  MODIFY `move_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psd_recieve`
--
ALTER TABLE `psd_recieve`
  MODIFY `psd_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psd_repair`
--
ALTER TABLE `psd_repair`
  MODIFY `repair_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psd_users`
--
ALTER TABLE `psd_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fsn_classes`
--
ALTER TABLE `fsn_classes`
  ADD CONSTRAINT `fsn_classes_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `fsn_groups` (`group_id`),
  ADD CONSTRAINT `fsn_classes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `psd_users` (`user_id`);

--
-- Constraints for table `fsn_groups`
--
ALTER TABLE `fsn_groups`
  ADD CONSTRAINT `fsn_groups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `psd_users` (`user_id`);

--
-- Constraints for table `fsn_types`
--
ALTER TABLE `fsn_types`
  ADD CONSTRAINT `fsn_types_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `psd_users` (`user_id`);

--
-- Constraints for table `psd_log`
--
ALTER TABLE `psd_log`
  ADD CONSTRAINT `psd_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `psd_users` (`user_id`),
  ADD CONSTRAINT `psd_log_ibfk_2` FOREIGN KEY (`psd_id`) REFERENCES `psd_recieve` (`psd_id`),
  ADD CONSTRAINT `psd_log_ibfk_3` FOREIGN KEY (`depid_old`) REFERENCES `department` (`dept_id`),
  ADD CONSTRAINT `psd_log_ibfk_4` FOREIGN KEY (`depid_new`) REFERENCES `department` (`dept_id`);

--
-- Constraints for table `psd_move`
--
ALTER TABLE `psd_move`
  ADD CONSTRAINT `psd_move_ibfk_1` FOREIGN KEY (`psd_id`) REFERENCES `psd_recieve` (`psd_id`),
  ADD CONSTRAINT `psd_move_ibfk_2` FOREIGN KEY (`deptid_old`) REFERENCES `department` (`dept_id`),
  ADD CONSTRAINT `psd_move_ibfk_3` FOREIGN KEY (`deptid_new`) REFERENCES `department` (`dept_id`),
  ADD CONSTRAINT `psd_move_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `psd_users` (`user_id`);

--
-- Constraints for table `psd_recieve`
--
ALTER TABLE `psd_recieve`
  ADD CONSTRAINT `psd_recieve_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`);

--
-- Constraints for table `psd_repair`
--
ALTER TABLE `psd_repair`
  ADD CONSTRAINT `psd_repair_ibfk_1` FOREIGN KEY (`psd_id`) REFERENCES `psd_recieve` (`psd_id`),
  ADD CONSTRAINT `psd_repair_ibfk_2` FOREIGN KEY (`user1`) REFERENCES `psd_users` (`user_id`),
  ADD CONSTRAINT `psd_repair_ibfk_3` FOREIGN KEY (`technician`) REFERENCES `psd_users` (`user_id`);

--
-- Constraints for table `psd_users`
--
ALTER TABLE `psd_users`
  ADD CONSTRAINT `psd_users_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
