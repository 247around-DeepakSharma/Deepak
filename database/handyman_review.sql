-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 18, 2015 at 07:05 PM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `boloaaka`
--

-- --------------------------------------------------------

--
-- Table structure for table `handyman_review`
--

CREATE TABLE IF NOT EXISTS `handyman_review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `behaviour` varchar(10) NOT NULL,
  `expertise` varchar(10) NOT NULL,
  `review` varchar(200) NOT NULL,
  `handyman_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `handyman_review`
--

INSERT INTO `handyman_review` (`id`, `behaviour`, `expertise`, `review`, `handyman_id`, `user_id`) VALUES
(1, '1', '2', 'aaaaaaa', '1', '2'),
(2, '8', '9', 'cxvx', '2', '2'),
(3, '4', '4', 'nbvnvnb', '1', '3');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
