-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 25, 2015 at 11:09 AM
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
-- Table structure for table `handyman`
--

CREATE TABLE IF NOT EXISTS `handyman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_no` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `service` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `experience` varchar(200) NOT NULL,
  `age` varchar(200) NOT NULL,
  `profile_photo` varchar(200) NOT NULL,
  `current_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_paid` varchar(50) NOT NULL DEFAULT '0',
  `passport` varchar(10) DEFAULT 'NO',
  `identity` varchar(10) DEFAULT '0',
  `action` varchar(10) NOT NULL DEFAULT '1',
  `marital_status` varchar(20) DEFAULT 'Single',
  `works_on_weekends` varchar(10) NOT NULL,
  `work_on_weekdays` varchar(10) NOT NULL,
  `service_on_call` varchar(10) NOT NULL DEFAULT 'No',
  `date_of_collection` varchar(200) NOT NULL,
  `time_of_data_collection` varchar(200) NOT NULL,
  `is_disabled` varchar(200) NOT NULL DEFAULT '0',
  `location` varchar(200) NOT NULL,
  `vendors_area_of_operation` varchar(200) NOT NULL,
  `bank_account` varchar(200) NOT NULL,
  `bank_ac_no` varchar(200) NOT NULL,
  `id_proof_name` varchar(200) NOT NULL,
  `id_proof_no` varchar(200) NOT NULL,
  `id_proof_photo` varchar(200) NOT NULL,
  `handyman_previous_customers` varchar(200) NOT NULL,
  `Other_handyman_contact` varchar(200) NOT NULL,
  `Rating_by_Agent` varchar(200) NOT NULL,
  `Agent` varchar(200) NOT NULL,
  `updatedate` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

--
-- Dumping data for table `handyman`
--

INSERT INTO `handyman` (`id`, `serial_no`, `name`, `phone`, `service`, `address`, `experience`, `age`, `profile_photo`, `current_time`, `is_paid`, `passport`, `identity`, `action`, `marital_status`, `works_on_weekends`, `work_on_weekdays`, `service_on_call`, `date_of_collection`, `time_of_data_collection`, `is_disabled`, `location`, `vendors_area_of_operation`, `bank_account`, `bank_ac_no`, `id_proof_name`, `id_proof_no`, `id_proof_photo`, `handyman_previous_customers`, `Other_handyman_contact`, `Rating_by_Agent`, `Agent`, `updatedate`) VALUES
(1, '1', 'Naresh', '9990825502', '["Party Cook"]', 'gali no.1 challera noida sec-44', '10-12', '', 'a6b009c2aaab8503820663d3006e3d0d.jpg', '2015-03-24 04:57:13', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '2/1/2015', 'N/A', 'N/A', '{"longitude":"28.5611144","lattitude":"77.3405408"}', 'sec-37, Noida', 'No', 'N/A', 'identity card', 'KBY2130342', 'IMG_20150103_152459.jpg', 'bijendra singh-9818349175', 'dinesh', 'Good', 'Manish Pal Singh', ''),
(2, '2', 'Sahil', '8586094383', '["Repair-GasBurner\\/Cooker"]', 'sec-31 pani ki tanki ke pass', '0-1', '', '7426a63ad1e6f6b8c1ae9fc6735e14ef.jpg', '2015-03-24 04:57:13', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '2/1/2015', 'N/A', 'No', '{"longitude":"28.563500842829868","lattitude":"77.34011012379416"}', 'nithari, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'saleem-9650702985', 'Average', 'Manish Pal Singh', ''),
(3, '3', 'Sunil', '9643836905', '["Fruit @ Home"]', 'sec-37 anand vihar harijan basti', '15-20', '', '6e5492fac6a1ffd5d415e44201e23717.jpg', '2015-03-24 04:57:13', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '2/1/2015', '5/1/2015 6:41', 'No', '{"longitude":"28.56362486999803","lattitude":"77.34117892999242"}', 'sec-36, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(4, '4', 'Anuj Kumar', '9818571326', '["Carpenter"]', 'morna petrol lump vali gali aec-35', '2-5', '', '67fa2e023dc3d41fe0f6626fce18ab9e.jpg', '2015-03-24 04:57:14', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '4/1/2015', '5/1/2015 9:36', 'No', '{"longitude":"28.5656493","lattitude":"77.3428493"}', 'sec-36, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'sir-9412092414', 'umesh-9971935747', 'Good', 'Manish Pal Singh', ''),
(5, '6', 'Mithun', '8527661146', '["Tailor"]', 'mahaveer apartment ho no.1377 sec-29', '10-12', '', '31ae4a161744752c584bbfeabe47c77b.jpg', '2015-03-24 04:57:14', '0', 'No', '0', '1', 'Single', '', '', 'No', '4/1/2015', '5/1/2015 11:52', 'No', '{"longitude":"28.56951887669109","lattitude":"77.33809020656916"}', 'sec-36, Noida', 'Yes', '145000000000000', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(6, '7', 'Ajad Singh', '8860763897', '["Tailor"]', 'harijam basti sec-37', '6-10', '', '6c9efe5ab6d2158456a6a3b9c533c57b.jpg', '2015-03-24 04:57:14', '0', 'No', '0', '1', 'Married', '', '', 'No', '4/1/2015', '5/1/2015 11:46', 'Yes', '{"longitude":"28.5662792","lattitude":"77.3408533"}', 'sec-37, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(7, '8', 'Vishal', '9560497078', '["Gardener"]', 'sec-8 bash balli', '2-5', '', '2bf4c7a716c60f924893479d19bbe7a0.jpg', '2015-03-24 04:57:14', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '4/1/2015', '5/1/2015 12:17', 'No', '{"longitude":"28.5745482","lattitude":"77.3498774"}', 'sec-36, Noida', 'No', 'N/A', 'pass no', '1570', 'IMG_20150105_160331.jpg', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(8, '9', 'Arvind Chauhan', '9312816191', '["Electrician"]', 'k-4 godwari shopping comlplex sec-37', '>25', '', '0123a482e5be922c07c4bea0ffa853c4.jpg', '2015-03-24 04:57:14', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '5/1/2015', '6/1/2015 5:56', 'No', '{"longitude":"28.565474","lattitude":"77.3395555"}', 'sec-19, 21, 26, 31, 36, 41, 46, 47 Noida\n\n', 'Yes', '164 vijya bank', 'voter card', 'ZYH0038091', 'IMG_20150106_111401.jpg', 'ved sagal-9811201973', 'N/A', 'Exceptional', 'Manish Pal Singh', ''),
(9, '11', 'Md. Saleem', '9810139140', '["Carpenter"]', 'salarpur main bhangel u turn sec-101', '10-12', '', '63ef5fc28addff93a61c6fef926ebea8.jpg', '2015-03-24 04:57:14', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '5/1/2015', '6/1/2015 10:21', 'No', '{"longitude":"28.5427055","lattitude":"77.3863892"}', 'sec-37,36,40, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(10, '12', 'Dayanand', '9560448507', '["Contractor"]', 'sec-18 near atta market', '6-10', '', '6b64ce1e7c139e6d84eef04bd2c37a27.jpg', '2015-03-24 04:57:14', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '5/1/2015', '7/1/2015 8:07', 'No', '{"longitude":"28.5581335","lattitude":"77.3468321"}', 'sec-36,37,40, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'mr sahil-8527875348', 'N/A', 'Good', 'Manish Pal Singh', ''),
(11, '13', 'Rajesh Kumar Gupta', '9718861701', '["Contractor"]', 'surajpur durga takij', '6-10', '', '8bcf3cd69118fd50683ddb7e97ec987e.jpg', '2015-03-24 04:57:15', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '6/1/2015', '7/1/2015 10:35', 'No', '{"longitude":"28.5581335","lattitude":"77.3468321"}', 'sec- 36,37,40,110, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(12, '14', 'Radhe Shayam', '9810223041', '["Electrician"]', 'sadar pur colony noida sec-45', '6-10', '', '2731870f89b7d25833910e5b46ffd3e6.jpeg', '2015-03-24 04:57:15', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '4/1/2015', '7/1/2015 14:24', 'No', '{"longitude":"28.556243393895528","lattitude":"77.34486364429783"}', 'sec-36,37,40,41, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'mr.rohit 8750137254', 'N/A', 'Average', 'Manish Pal Singh', ''),
(13, '15', 'Narayan', '9910430565', '["Plumber"]', 'challera gali no 1 noida sec-37', '2-5', '', '958616164ca929ca7188eb7768dc3748.png', '2015-03-24 04:57:15', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '5/1/2015', '7/1/2015 14:50', 'No', '{"longitude":"28.5581335","lattitude":"77.3468321"}', 'sec-36,37, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(14, '16', 'Dilshad', '9971716012', '["Vegetable @ Home"]', 'shop no. 1 sec-82', '12-15', '', 'fcdc6af4e5717f0e47a3af5460eb2ecd.jpg', '2015-03-24 04:57:15', '200', 'No', '0', '1', 'Married', '', '', 'Yes', '6/1/2015', '7/1/2015 15:24', 'No', '{"longitude":"28.5342328","lattitude":"77.3880954"}', 'sec-82, 110, 92, 93, 105, 104, 108, Noida', 'No', 'N/A', 'licence card', '90082/eh/2006', 'IMG_20150107_180646.jpg', 'N/A', 'N/A', 'Exceptional', 'Manish Pal Singh', ''),
(15, '17', 'Dilshad', '9582108627', '["Fruit @ Home"]', 'shop no 1 sector 82', '10-12', '', 'a10657d59e52a529670a70b4790e734f.jpg', '2015-03-24 04:57:15', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '6/1/2015', '7/1/2015 15:38', 'No', '{"longitude":"28.5342328","lattitude":"77.3880954"}', 'sec-82, 110, 92, 93, 105, 104, 108, Noida', 'No', 'N/A', 'license card', '90082/eh/2006', 'IMG_20150107_180810.jpg', 'N/A', 'N/A', 'Exceptional', 'Manish Pal Singh', ''),
(16, '19', 'Naresh', '7053660204', '["Tailor"]', 'geja roar salarpur bhangel', '6-10', '', '8ef0859aa1a4457d05ba452e5379bdfd.jpg', '2015-03-24 04:57:15', '0', 'No', '0', '1', 'Single', '', '', 'No', '7/1/2015', '8/1/2015 9:27', 'No', '{"longitude":"28.535636458519427","lattitude":"77.38587595180987"}', 'sec-110,105,108, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(17, '21', 'Ganesh ', '8882696430', '["Party Cook"]', 'noida sec-110', '6-10', '', 'abaf11239024d3a2e8c48ec5ec12b6e9.jpg', '2015-03-24 04:57:15', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '7/1/2015', '9/1/2015 9:07', 'No', '{"longitude":"28.5581335","lattitude":"77.3468321"}', 'sec-110,82,93,105, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(18, '22', 'Ganga Ram Saini', '9810395749', '["Tailor"]', 'salarpur geja road noida ', '2-5', '', 'f67a37e0499078bb3850c2769db2807e.JPG', '2015-03-24 04:57:16', '0', 'No', '0', '1', 'Single', '', '', 'No', '7/1/2015', '9/1/2015 9:08', 'No', '{"longitude":"28.5580022","lattitude":"77.3453281"}', 'sec-110, 108, 105, 82, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(19, '24', 'Pramod', '7053839384', '["Fruit @ Home"]', 'near salarpur bhangel', '6-10', '', '15d1ac932649d809bbfaff94ad3309b0.jpg', '2015-03-24 04:57:16', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '7/1/2015', '9/1/2015 9:08', 'No', '{"longitude":"28.56179766934248","lattitude":"77.34834489570581"}', 'sec-110, 105, 108, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(20, '25', 'Ram Avtar', '9560655218', '["Fruit @ Home"]', 'salarpur geja road noida ', '6-10', '', 'fa635b5bc24f75d977e9462e26566d5c.jpg', '2015-03-24 04:57:16', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '7/1/2015', '9/1/2015 11:25', 'No', '{"longitude":"28.535567294181803","lattitude":"77.39144102843458"}', 'sec-110, 105, 108, 82, 93, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(21, '26', 'Mohd Arif', '9818666394', '["Carpenter"]', 'geja road bhangel seç-110', '15-20', '', '0393d22c1cd67b566792402c06719315.jpg', '2015-03-24 04:57:16', '200', 'No', '0', '1', 'Married', '', '', 'Yes', '8/1/2015', '9/1/2015 10:03', 'No', '{"longitude":"28.536917831165066","lattitude":"77.38720099154845"}', 'Sec-110, 105, 108, 93, 83, Noida', 'No', 'N/A', 'ahara card', '2328 3319 8002', 'IMG_20150109_152300.jpg', 'a.k jain 9811581710', 'asif-9910790515', 'Exceptional', 'Manish Pal Singh', ''),
(22, '27', 'M.K Singh', '9999803218', '["Electrician"]', 'lal market near domines pizza,sec-110', '10-12', '', '8558e7621f7c6bcb334b914a272c1ef4.jpg', '2015-03-24 04:57:16', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '8/1/2015', '9/1/2015 11:20', 'No', '{"longitude":"28.534232","lattitude":"77.3880987"}', 'sec-110, 107, 108, 93, 82, Noida', 'Yes', 'N/A', 'adhar card', '4539 9885 1206', 'IMG_20150109_161422.jpg', 'N/A', 'N/A', 'Exceptional', 'Manish Pal Singh', ''),
(23, '28', 'Pradeep', '8585998858', '["Electrician"]', 'geja road bhangel', '2-5', '', 'd6e72b79a71da9d1674b59cb8c06636c.jpg', '2015-03-24 04:57:16', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '8/1/2015', '9/1/2015 13:06', 'No', '{"longitude":"28.5585341","lattitude":"77.3451859"}', 'sec-110, 105, 108, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(24, '29', 'Shyam', '8882436220', '["Gardener"]', 'geja road bhangel ', '2-5', '', '1d684e3644a5b1a2c531a38f330a93fb.jpg', '2015-03-24 04:57:16', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '8/1/2015', '9/1/2015 13:31', 'No', '{"longitude":"28.5581325","lattitude":"77.3468175"}', 'sec-110, 108, 109, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(25, '30', 'Rahul Singh', '8802941235', '["Key maker"]', 'sec-82 near police choki', '2-5', '', '0c9c6339c6bbec6fabce9c44ce405d7a.jpg', '2015-03-24 04:57:16', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '9/1/2015', '10/1/2015 8:15', 'No', '{"longitude":"28.528499835239025","lattitude":"77.38389894834667"}', 'sec-108, 110, 105, 83, 93, 92, 104, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'mr. rakesh singh 8527683552', 'Good', 'Manish Pal Singh', ''),
(26, '31', 'Sarvan Dubey', '8800376288', '["Panditji"]', 'bhangel ghav', '2-5', '', 'cca3a00c4b3d494755549d45d184b90f.jpg', '2015-03-24 04:57:17', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '9/1/2015', '10/1/2015 8:22', 'No', '{"longitude":"28.5303366","lattitude":"77.391872"}', 'sec-82, 93, 110, 109, 105, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(27, '32', 'Ashok', '9458050364', '["Plumber"]', 'barola', '6-10', '', '5c7be7a8ea32af99c1483c15f8dd2185.jpg', '2015-03-24 04:57:17', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '9/1/2015', '10/1/2015 9:21', 'No', '{"longitude":"28.5213126","lattitude":"77.3940797"}', 'sec-110, 82, 93, 108, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(28, '34', 'Santosh', '8905658695', '["Painter"]', 'kendiraya vihar sec-82 noida', '6-10', '', 'd1bf29db648f9979c6495447122ca90a.jpg', '2015-03-24 04:57:17', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '9/1/2015', '10/1/2015 11:00', 'No', '{"longitude":"28.5581325","lattitude":"77.3468175"}', 'sec-110, 82, 93, 108, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(29, '38', 'Shorab', '9871279981', '["Repair-TV"]', 'bhangel ', '6-10', '', '4342dbee708206ad81314fdc256bc1dd.jpg', '2015-03-24 04:57:17', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '11/1/2015', '12/1/2015 11:52', 'No', '{"longitude":"28.5581532","lattitude":"77.3470305"}', 'sec-83, 93, 108, 110, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'ram-7505349870', 'N/A', 'Good', 'Manish Pal Singh', ''),
(30, '38', 'Shorab', '9871279981', '["Repair-Fridge"]', 'bhangel ', '6-10', '', 'f9a4597b23971ab6259db307e124a883.jpg', '2015-03-24 04:57:17', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '11/1/2015', '12/1/2015 11:52', 'No', '{"longitude":"28.5581532","lattitude":"77.3470305"}', 'sec-83, 93, 108, 110, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'ram-7505349870', 'N/A', 'Good', 'Manish Pal Singh', ''),
(31, '38', 'Shorab', '9871279981', '["Repair-Washing Machine"]', 'bhangel ', '6-10', '', '761fdc8274a85035d2ea5fd447e3a734.jpg', '2015-03-24 04:57:17', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '11/1/2015', '12/1/2015 11:52', 'No', '{"longitude":"28.5581532","lattitude":"77.3470305"}', 'sec-83, 93, 108, 110, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'ram-7505349870', 'N/A', 'Good', 'Manish Pal Singh', ''),
(32, '39', 'Lalit', '9871657242', '["Repair-Fridge"]', 'geja ghav noida sec-93', '6-10', '', '3d5534f8066fa1c7729c99611d9cdd29.jpg', '2015-03-24 04:57:17', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '11/1/2015', '12/1/2015 12:15', 'No', '{"longitude":"28.5581532","lattitude":"77.3470305"}', 'sec-110, 108, 93, 82, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(33, '41', 'A Khan', '9911119208', '["Repair-AC"]', 'bhangel noida', '6-10', '', '111c46173e308be1b06863035a888297.jpg', '2015-03-24 04:57:18', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '11/1/2015', '12/1/2015 11:46', 'No', '{"longitude":"28.5581532","lattitude":"77.3470305"}', 'sec-93, 82, 110, 108, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'r arun-8750137888', 'N/A', 'Good', 'Manish Pal Singh', ''),
(34, '42', 'Omkar Nath', '9910858640', '["Repair-Fridge"]', 'barola noida sec-49', '10-12', '', '65f23ca111440a8a15c76952070a6e1b.jpg', '2015-03-24 04:57:18', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '11/1/2015', '12/1/2015 14:00', 'No', '{"longitude":"28.569970819515188","lattitude":"77.32673248929514"}', 'sec-82, 93, 110, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(35, '43', 'Manoj Dubey', '9891466827', '["Repair-RO"]', 'bhangel salapur noida', '6-10', '', 'b79be85ae23e87a304f4d7f905e55c56.jpeg', '2015-03-24 04:57:18', '0', 'No', '0', '1', 'Single', '', '', 'No', '11/1/2015', '12/1/2015 11:51', 'No', '{"longitude":"28.5572071","lattitude":"77.3461341"}', 'sec-110, 108, 82, 93, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(36, '44', 'Ankit', '9717317177', '["Repair-MixerGrinder"]', 'geja ghav sec-93', '2-5', '', '63593afd60920d5fe9574aa82a411e98.jpg', '2015-03-24 04:57:18', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '11/1/2015', '12/1/2015 14:10', 'No', '{"longitude":"28.5764467","lattitude":"77.3158591"}', 'sec-93, 82, 110, 106, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(37, '45', 'Sikandar Das', '9546389709', '["Cobbler"]', 'shipra mall gate no-6', '>25', '', 'f62c6713f06bc1b8dcb917d0d5dd2ec6.jpg', '2015-03-24 04:57:18', '0', 'No', '0', '1', 'Married', '', '', 'No', '14-01-2015', '15-01-2015 08:14', 'No', '{"longitude":"28.6392246","lattitude":"77.368803"}', 'indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Exceptional', 'Manish Pal Singh', ''),
(38, '46', 'R K', '9911484806', '["Repair-TV"]', 'bhangel', '6-10', '', 'd00f45a0dd36dc489db77db06d6ca629.png', '2015-03-24 04:57:18', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '12/1/2015', '13-01-2015 12:43', 'No', '{"longitude":"28.5579987","lattitude":"77.3454433"}', 'sec-82, 93, 110, 106, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'vipin singh 8790453481', 'N/A', 'Good', 'Manish Pal Singh', ''),
(39, '47', 'Vikas', '7566054903', '["Repair-TV"]', 'geja ghav sec-93', '10-12', '', '2b96e960217622b3655534d4e8b7d39f.jpeg', '2015-03-24 04:57:18', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '12/1/2015', '15-01-2015 06:44', 'No', '{"longitude":"28.5581532","lattitude":"77.3470305"}', 'sec-110, 108, 106, 93, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'mr sanjay 9803507305', 'N/A', 'Good', 'Manish Pal Singh', ''),
(40, '48', 'Krishna', '9958764549', '["Repair-TV"]', 'near salarpur pani ki tanki', '10-12', '', 'fe363695e69eda6ea121d7135a97bd59.jpeg', '2015-03-24 04:57:19', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '12/1/2015', '15-01-2015 06:44', 'No', '{"longitude":"28.5581532","lattitude":"77.3470305"}', 'sec-110, 108, 106, 83, 93, Noida', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'rajiv 88836110988', 'N/A', 'Good', 'Manish Pal Singh', ''),
(41, '49', 'Shivam', '9818472528', '["Vegetable @ Home"]', 'vaishali', '6-10', '', '12910266c46be13aa4586cd8fa969798.jpeg', '2015-03-24 04:57:19', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '14-01-2015', '15-01-2015 12:13', 'No', '{"longitude":"28.5581504","lattitude":"77.3470244"}', 'vaishali , indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(42, '53', 'Sanjay', '8587986128', '["Vegetable @ Home"]', 'indriapuram main road', '2-5', '', 'ecdfd392472a29ad65f3135c484c5794.jpeg', '2015-03-24 04:57:19', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '13-01-2015', '15-01-2015 12:28', 'No', '{"longitude":"28.559472474492054","lattitude":"77.34544079866723"}', 'Indrapuram, Vasundhara', 'Yes', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(43, '56', 'Gaurav', '9811439041', '["Electrician"]', 'indrapuram ghaziabad.', '10-12', '', '8c93338aa5ba3ad62d7fe5af5b936f89.png', '2015-03-24 04:57:19', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '14-01-2015', '15-01-2015 13:25', 'No', '{"longitude":"28.5581504","lattitude":"77.3470244"}', 'Indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(44, '60', 'Mohd. Bhai', '8860764572', '["Contractor"]', 'Gurgaon', '6-10', '', 'e8a7ea2d88470217d76bde9715075537.jpeg', '2015-03-24 04:57:19', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '18-01-2015', '19-01-2015 12:56', 'No', '{"longitude":"28.55558922727259","lattitude":"77.34429927149094"}', 'Indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(45, '61', 'Narendar', '9810971473', '["Vegetable @ Home"]', 'vaishali', '6-10', '', '86b980cc64bbcedae74fff85d099d207.png', '2015-03-24 04:57:19', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '18-01-2015', '19-01-2015 13:11', 'No', '{"longitude":"28.5581397","lattitude":"77.3470777"}', 'Indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(46, '62', 'Sumit', '9312817152', '["Vegetable @ Home"]', 'ghaziabad', '6-10', '', '7b23dd2c1c147e2f3841a71e1c3f7155.png', '2015-03-24 04:57:19', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '18-01-2015', '19-01-2015 13:15', 'No', '{"longitude":"28.5581397","lattitude":"77.3470777"}', 'vashundra, indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Good', 'Manish Pal Singh', ''),
(47, '63', 'Ratan Sood', '9810849181', '["Party Cook"]', 'Sec23, Sanjay Nagar, Ghaziabad', '10-12', '', 'acd060c2baa2976e4431ec8b0d05660c.jpeg', '2015-03-24 04:57:20', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '18-01-2015', '19-01-2015 17:16', 'No', '{"longitude":"28.5581397","lattitude":"77.3470777"}', 'indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'smiriti-9789456321', 'N/A', 'Good', 'Manish Pal Singh', ''),
(48, '69', 'Ankit', '9811893468', '["Electrician"]', 'ghan khand 4', '6-10', '', '7d6727d7ea7ca014624bfe1f1d7daf41.jpeg', '2015-03-24 04:57:20', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '20-01-2015', '21-01-2015 12:07', 'No', '{"longitude":"28.5578129","lattitude":"77.3438359"}', 'indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'rajesh-9845896753', 'N/A', 'Good', 'Manish Pal Singh', ''),
(49, '71', 'Manoj kumar', '9810628927', '["Plumber"]', 'vashali sec 4', '2-5', '', '48abef69855b8351d6044a56508056a0.png', '2015-03-24 04:57:20', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '20-01-2015', '21-01-2015 12:42', 'No', '{"longitude":"28.5579763","lattitude":"77.3454568"}', 'indrapuram', 'Yes', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Average', 'Manish Pal Singh', ''),
(50, '73', 'Manoj', '9810756373', '["Electrician"]', 'ghayan khand 4', '15-20', '', '5e1667e82100d62aa8f8061f0e1d7386.png', '2015-03-24 04:57:20', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '20-01-2015', '21-01-2015 12:14', 'No', '{"longitude":"28.5578129","lattitude":"77.3438359"}', 'indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'mr amit 9284563423', 'N/A', 'Good', 'Manish Pal Singh', ''),
(51, '77', 'Mohan', '9818719116', '["Plumber"]', 'nayay khand-2', '2-5', '', '202236472b3365b71fd0e1b642afa14f.png', '2015-03-24 04:57:20', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '1/2/2015', '2/2/2015 12:36', 'No', '{"longitude":"28.5581397","lattitude":"77.3470777"}', 'indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'rajesh  8750136467', 'Good', 'Manish Pal Singh', ''),
(52, '78', 'Kishan', '9958494741', '["Plumber"]', 'nayay khand-2', '10-12', '', '64849bf69bf6dec432b110f67ff51ba1.png', '2015-03-24 04:57:20', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '1/2/2015', '2/2/2015 17:52', 'No', '{"longitude":"28.5580556","lattitude":"77.347027"}', 'indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'rajesh 8527182687', 'N/A', 'Good', 'Manish Pal Singh', ''),
(53, '79', 'Om pal', '8860601683', '["Plumber"]', 'nayay khand-2', '6-10', '', '4a4a6af51c660ab77f55112a7d3b78ac.png', '2015-03-24 04:57:20', '0', 'No', '0', '1', 'Single', '', '', 'Yes', '1/2/2015', '3/2/2015 8:40', 'No', '{"longitude":"28.6349197","lattitude":"77.3694688"}', 'indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'rohan 8527182789', 'N/A', 'Good', 'Manish Pal Singh', ''),
(54, '80', 'Vinod Kumar', '9899827156', '["Electrician"]', 'shakti khand-1', '12-15', '', '2d5b2e6ce89c4d532ce6e549aa5109b6.jpg', '2015-03-24 04:57:20', '0', 'No', '0', '1', 'Married', '', '', 'Yes', '2/2/2015', '3/2/2015 8:53', 'No', '{"longitude":"28.634982","lattitude":"77.3695165"}', 'indrapuram', 'No', 'N/A', 'N/A', 'N/A', 'N/A', 'rohit 9899675408', 'N/A', 'Good', 'Manish Pal Singh', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
