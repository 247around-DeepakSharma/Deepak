-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 12, 2016 at 09:58 AM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `247around_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `vendor_escalation_details`
--

CREATE TABLE IF NOT EXISTS `vendor_escalation_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` varchar(10) NOT NULL,
  `booking_id` varchar(10) NOT NULL,
  `escalation_reason` varchar(50) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

--
-- Dumping data for table `vendor_escalation_details`
--

INSERT INTO `vendor_escalation_details` (`id`, `vendor_id`, `booking_id`, `escalation_reason`, `create_date`) VALUES
(1, '29', 'SS-0007160', '2', '2016-04-11 10:25:16'),
(2, '29', 'SS-0007160', '2', '2016-04-11 11:31:46'),
(3, '29', 'SS-0007160', '2', '2016-04-11 13:03:00'),
(4, '29', 'SS-0007160', '2', '2016-04-11 13:04:14'),
(5, '29', 'SS-0007160', '2', '2016-04-11 13:06:15'),
(6, '29', 'SS-0007160', '2', '2016-04-11 13:06:48'),
(7, '29', 'SS-0007160', '2', '2016-04-11 13:13:53'),
(8, '29', 'SS-0007160', '2', '2016-04-11 13:18:58'),
(9, '29', 'SS-0007160', '2', '2016-04-11 13:29:16'),
(10, '29', 'SS-0007160', '2', '2016-04-11 13:29:32');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
