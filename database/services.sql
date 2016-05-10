-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2015 at 07:44 AM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

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
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `services` varchar(200) NOT NULL,
  `action` varchar(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/**********************Delete action Column ********************************/

ALTER TABLE  `services` DROP  `action` ;

/*****************Add service image Column*********************/
ALTER TABLE  `services` ADD  `service_image` VARCHAR( 200 ) NOT NULL ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `services`, `service_image`) VALUES
(1, 'AC Repair', '89b210307e08495de9636454157c1a28AC Repair.jpg'),
(2, 'Beautician', '1487b3ad18ddf951f4df4c5bb14b89d7Beautician.jpg'),
(3, 'Burner Repair', 'cd1280c7af723aab95e00e8244442fd2Burner Repair.png'),
(4, 'Cobbler', 'c2543881c70af71d31b6acd31435a2e6Cobbler.jpg'),
(5, 'Contractor', 'd9dcf18eba1c1eae5b4bd0fb30e253a0Contractor.png'),
(6, 'Cook', 'afce5dbf031276f6fe97b729a618fd48Cook.png'),
(7, 'Cooker Repair', '9fbe21e4fb99a725ee490457fa22f395Cooker Repair.png'),
(8, 'Drillman', 'cbde0bab592902c6aeb8272ecb7256b8Drillman.jpg'),
(9, 'Driver', '7d087bcc2c53245d98fee872f23390a6Driver.jpg'),
(10, 'Electrician', '1694e1d91d7af039a4e9044fee7aa351Electrician.jpg'),
(11, 'False Ceiling', '940c0d939399a8f6b878ad068d173a92False Ceiling.png'),
(12, 'Florist', 'c788fa9215c732a9246693979ec54397Florist.jpg'),
(13, 'Fruit Vendor', 'ef8b38879133531e275cc5ecba1113eaFruit Vendor.png'),
(14, 'Gardner', '04a487dc760f9aa82b7c32eb0a5fef27Gardner.png'),
(15, 'Inverter Repair', 'e9e0b0fad5aa0eca43b7eb151db05a2aInverter Repair.jpg'),
(16, 'Keymaker', '98daa0b21cbeb85029588e6304a60804Keymaker.png'),
(17, 'Mason', '6112c36d011f1185147ee051aee5dd8aMason.png'),
(18, 'Nurse', '544fed2612f8071e0c11ef53a2fba027Nurse.png'),
(19, 'Painter', '0e8c8ff555644e982072d709446021a4Painter.png'),
(20, 'Pandit', 'ab3ec47ca15a01ca628d2efde8516f03Pandit.png'),
(21, 'Physiotherapist', '6f1dd0031d0fa859eef004a483d66a40Physiotherapist.png'),
(22, 'Plumber', 'a9abce04cf221983539dbd4c6fa0d322Plumber.jpg'),
(23, 'Refrigerator Repair', 'a3dd4132e64903db5885019d47018f33Ref Repair.png'),
(24, 'Rickshaw', '5ab4168bc61b4c5ecad4fb6193bcd310Rickshaw.jpg'),
(25, 'Tailor', '2958d2e616cb2d67ce7d9a5eca1380e8Tailor.png'),
(26, 'TV Repair', 'efd292d7a155c6f89aa039edd0f28d80TV Repair.png'),
(27, 'Vegetable', '1c07976a077ef22678760a3226b0536dVegetable.png'),
(28, 'Washing Machine Repair', '017150821e4a1208d7ed65961bfe6f0aWM Repair.jpg'),
(29, 'Yoga', 'b56e285f4fdad015d50d0692416a54deYoga.png');