-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2016 at 12:48 PM
-- Server version: 10.0.17-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `247around-aws`
--

-- --------------------------------------------------------

--
-- Table structure for table `holiday_list`
--

CREATE TABLE `holiday_list` (
  `id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_name` varchar(512) NOT NULL,
  `delhi` int(2) NOT NULL COMMENT '1->Holiday in Delhi, 0->No Holiday',
  `chennai` int(2) NOT NULL COMMENT '1->holiday in chennai, 0->No holiday',
  `mumbai` int(2) NOT NULL COMMENT '1->Holiday in mumbai, 0->No holiday',
  `kolkata` int(2) NOT NULL COMMENT '1->Holiday in Kolkata, 0->No holiday',
  `active` int(2) NOT NULL DEFAULT '1' COMMENT '1->Active, 0->Non-Active',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `holiday_list`
--

INSERT INTO `holiday_list` (`id`, `event_date`, `event_name`, `delhi`, `chennai`, `mumbai`, `kolkata`, `active`, `create_date`) VALUES
(1, '2017-01-14', 'Pongal', 0, 1, 0, 0, 1, '2016-12-17 11:34:38'),
(2, '2017-01-16', 'Uzhavar Tirunal', 0, 1, 0, 0, 1, '2016-12-17 11:47:11'),
(3, '2017-01-26', 'REPUBLIC DAY', 1, 1, 1, 1, 1, '2016-12-17 11:47:11'),
(4, '2017-02-24', 'MAHA SHIVRATRI', 1, 0, 1, 0, 1, '2016-12-17 11:47:11'),
(5, '2017-03-28', 'UGADI/GUDI PADVA', 0, 0, 1, 0, 1, '2016-12-17 11:47:11'),
(6, '2017-04-04', 'RAM NAVANI', 1, 0, 0, 0, 1, '2016-12-17 11:47:11'),
(7, '2017-04-14', 'Tamil New Year', 0, 1, 0, 0, 1, '2016-12-17 11:47:11'),
(8, '2017-05-01', 'MAY DAY/MAHARASHTRA FORMATION DAY', 1, 1, 1, 1, 1, '2016-12-17 11:47:11'),
(9, '2017-06-02', 'TELENANA FORMATION DAY', 0, 0, 0, 0, 1, '2016-12-17 11:47:11'),
(10, '2017-06-26', 'RAMZAN/IDUL FITR', 1, 1, 1, 1, 1, '2016-12-17 11:47:11'),
(11, '2017-08-07', 'RAKSHA BANDHAN', 1, 0, 1, 0, 1, '2016-12-17 11:47:11'),
(12, '2017-08-15', 'INDEPENDENCE DAY', 1, 1, 1, 1, 1, '2016-12-17 11:47:11'),
(13, '2017-08-25', 'GANESH CHATURTHI', 1, 1, 1, 0, 1, '2016-12-17 11:47:11'),
(14, '2017-09-28', 'AYUDHA POOJA', 0, 1, 0, 0, 1, '2016-12-17 11:47:11'),
(15, '2017-10-02', 'MAHATMA GANDHI BIRTHDAY', 1, 1, 1, 1, 1, '2016-12-17 11:47:11'),
(16, '2017-10-19', 'DIWALI', 1, 1, 1, 1, 1, '2016-12-17 11:47:11'),
(17, '2017-10-20', 'BHAI DOOJ', 1, 0, 1, 0, 1, '2016-12-17 11:47:11'),
(18, '2017-11-01', 'KANNADA RAJYOTHSAVA', 0, 0, 0, 0, 1, '2016-12-17 11:47:11'),
(19, '2017-12-25', 'CHRISMAS DAY', 1, 1, 1, 1, 1, '2016-12-17 11:47:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `holiday_list`
--
ALTER TABLE `holiday_list`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `holiday_list`
--
ALTER TABLE `holiday_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
