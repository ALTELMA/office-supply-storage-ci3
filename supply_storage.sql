-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2016 at 06:50 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `suppy_storage`
--

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

CREATE TABLE IF NOT EXISTS `asset` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `sub_cat_id` int(11) DEFAULT NULL,
  `assetThumbPic` varchar(150) DEFAULT NULL,
  `assetFullPic` varchar(150) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `detail` varchar(150) DEFAULT NULL,
  `value` varchar(10) DEFAULT NULL,
  `soldDate` date DEFAULT NULL,
  `warrantyStartDate` date DEFAULT NULL,
  `warrantyEndDate` date DEFAULT NULL,
  `responseUser` varchar(150) DEFAULT NULL,
  `responseDepartment` int(1) DEFAULT NULL,
  `locationStorage` varchar(100) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `IsApproved` int(1) DEFAULT NULL,
  `UpdateDate` datetime DEFAULT NULL,
  `remark` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `asset_attachment`
--

CREATE TABLE IF NOT EXISTS `asset_attachment` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `fileName` varchar(150) DEFAULT NULL,
  `filePath` varchar(150) DEFAULT NULL,
  `IsApproved` int(1) DEFAULT NULL,
  `remark` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `asset_status`
--

CREATE TABLE IF NOT EXISTS `asset_status` (
  `status_id` int(1) NOT NULL,
  `statusName` varchar(150) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `asset_status`
--

INSERT INTO `asset_status` (`status_id`, `statusName`) VALUES
(1, 'ปกติ'),
(2, 'ชำรุด'),
(3, 'อยู่ในระหว่างซ่อม'),
(4, 'จำหน่ายออก'),
(5, 'สูญหาย');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `cat_id` int(11) NOT NULL,
  `catType` char(1) DEFAULT NULL,
  `catName` varchar(150) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cat_id`, `catType`, `catName`) VALUES
(1, 'A', 'โต๊ะ'),
(2, 'B', 'ตู้'),
(3, 'C', 'เก้าอี้');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE IF NOT EXISTS `department` (
  `department_id` int(1) NOT NULL,
  `departmentName` varchar(150) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `departmentName`) VALUES
(1, 'Admin'),
(2, 'HR'),
(3, 'Account'),
(4, 'Marketing'),
(5, 'IT');

-- --------------------------------------------------------

--
-- Table structure for table `sub_category`
--

CREATE TABLE IF NOT EXISTS `sub_category` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `subTypeName` varchar(150) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `password_format` varchar(150) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `registDate` datetime DEFAULT NULL,
  `lastLoginDate` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `IsApproved` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `password_format`, `name`, `registDate`, `lastLoginDate`, `status`, `IsApproved`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '123456', 'administrator', '0000-00-00 00:00:00', '2016-05-02 06:30:42', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asset`
--
ALTER TABLE `asset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `asset_attachment`
--
ALTER TABLE `asset_attachment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `asset_status`
--
ALTER TABLE `asset_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asset`
--
ALTER TABLE `asset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `asset_attachment`
--
ALTER TABLE `asset_attachment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `asset_status`
--
ALTER TABLE `asset_status`
  MODIFY `status_id` int(1) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(1) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `sub_category`
--
ALTER TABLE `sub_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
