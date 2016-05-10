-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 06, 2016 at 04:06 PM
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
-- Table structure for table `service_price`
--

CREATE TABLE IF NOT EXISTS `service_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner` varchar(10) NOT NULL,
  `state` varchar(25) NOT NULL,
  `service_id` varchar(10) NOT NULL,
  `category` varchar(50) NOT NULL,
  `capacity` varchar(50) DEFAULT NULL,
  `service_category` varchar(100) NOT NULL,
  `active` varchar(2) DEFAULT NULL,
  `check_box` varchar(2) DEFAULT NULL,
  `verndor_service_charge` decimal(10,2) NOT NULL,
  `vendor_svc_tax` decimal(10,2) NOT NULL,
  `total_vendor_svc_charge` decimal(10,2) NOT NULL,
  `around_service_charge` decimal(10,2) NOT NULL,
  `around_svc_tax` decimal(10,2) NOT NULL,
  `around_total_svc_charge` decimal(10,2) NOT NULL,
  `total_svc_tax` decimal(10,2) NOT NULL,
  `customer_service_charge` decimal(10,2) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `service_price_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner` varchar(10) NOT NULL,
  `state` varchar(25) NOT NULL,
  `service_id` varchar(10) NOT NULL,
  `category` varchar(50) NOT NULL,
  `capacity` varchar(50) DEFAULT NULL,
  `service_category` varchar(100) NOT NULL,
  `active` varchar(2) DEFAULT NULL,
  `check_box` varchar(2) DEFAULT NULL,
  `verndor_service_charge` decimal(10,2) NOT NULL,
  `vendor_svc_tax` decimal(10,2) NOT NULL,
  `total_vendor_svc_charge` decimal(10,2) NOT NULL,
  `around_service_charge` decimal(10,2) NOT NULL,
  `around_svc_tax` decimal(10,2) NOT NULL,
  `around_total_svc_charge` decimal(10,2) NOT NULL,
  `total_svc_tax` decimal(10,2) NOT NULL,
  `customer_service_charge` decimal(10,2) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
