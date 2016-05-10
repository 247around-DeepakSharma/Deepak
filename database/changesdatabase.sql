/********************add coloumn in service date-25/3 ********************/
ALTER TABLE  `services` ADD  `distance` VARCHAR( 200 ) NOT NULL ;

- --------------------------------------------------------

--
-- Table structure for table `popularSearch`
--

CREATE TABLE IF NOT EXISTS `popularSearch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `searchkeyword` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

---------------------Table   --  Services  -------update Services Image name--------------------------------------------
UPDATE  `services` SET  `service_image` =  '89b210307e08495de9636454157c1a28ACRepair.jpg' WHERE  `services`.`id` =1;
UPDATE  `services` SET  `service_image` =  'cd1280c7af723aab95e00e8244442fd2BurnerRepair.png' WHERE  `services`.`id` =3;
UPDATE  `services` SET  `service_image` =  '9fbe21e4fb99a725ee490457fa22f395CookerRepair.png' WHERE  `services`.`id` =7;
UPDATE  `services` SET  `service_image` =  '940c0d939399a8f6b878ad068d173a92FalseCeiling.png' WHERE  `services`.`id` =11;
UPDATE  `services` SET  `service_image` =  'ef8b38879133531e275cc5ecba1113eaFruitVendor.png' WHERE  `services`.`id` =13;
UPDATE  `services` SET  `service_image` =  'e9e0b0fad5aa0eca43b7eb151db05a2aInverterRepair.jpg' WHERE  `services`.`id` =15;
UPDATE  `services` SET  `service_image` =  'a3dd4132e64903db5885019d47018f33RefRepair.png' WHERE  `services`.`id` =23;
UPDATE  `services` SET  `service_image` =  'efd292d7a155c6f89aa039edd0f28d80TVRepair.png' WHERE  `services`.`id` =26;
UPDATE  `services` SET  `service_image` =  '017150821e4a1208d7ed65961bfe6f0aWMRepair.jpg' WHERE  `services`.`id` =28;


-- --------------------------------------------------------

--
-- Table structure for table `sharetext`
--

CREATE TABLE IF NOT EXISTS `sharetext` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sharetext` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sharetext`
--

INSERT INTO `sharetext` (`id`, `sharetext`) VALUES
(1, 'test');

---------------------------table  handyman---------------------------------
ALTER TABLE  `handyman` ADD  `service_id` VARCHAR( 50 ) NOT NULL AFTER  `phone` ;
UPDATE  `handyman` SET  `service_id` =  '6' WHERE  `handyman`.`id` =1;
UPDATE  `handyman` SET  `service_id` =  '3' WHERE  `handyman`.`id` =2;
UPDATE  `handyman` SET  `service_id` =  '13' WHERE  `handyman`.`id` =3;
UPDATE  `handyman` SET  `service_id` =  '30' WHERE  `handyman`.`id` =4;
UPDATE  `handyman` SET  `service_id` =  '25' WHERE  `handyman`.`id` =5;
UPDATE  `handyman` SET  `service_id` =  '25' WHERE  `handyman`.`id` =6;
UPDATE  `handyman` SET  `service_id` =  '14' WHERE  `handyman`.`id` =7;
UPDATE  `handyman` SET  `service_id` =  '10' WHERE  `handyman`.`id` =8;
UPDATE  `handyman` SET  `service_id` =  '30' WHERE  `handyman`.`id` =9;
UPDATE  `handyman` SET  `service_id` =  '5' WHERE  `handyman`.`id` =10;
UPDATE  `handyman` SET  `service_id` =  '5' WHERE  `handyman`.`id` =11;
UPDATE  `handyman` SET  `service_id` =  '10' WHERE  `handyman`.`id` =12;
UPDATE  `handyman` SET  `service_id` =  '22' WHERE  `handyman`.`id` =13;
UPDATE  `handyman` SET  `service_id` =  '27' WHERE  `handyman`.`id` =14;
UPDATE  `handyman` SET  `service_id` =  '13' WHERE  `handyman`.`id` =15;
UPDATE  `handyman` SET  `service_id` =  '25' WHERE  `handyman`.`id` =16;
UPDATE  `handyman` SET  `service_id` =  '6' WHERE  `handyman`.`id` =17;
UPDATE  `handyman` SET  `service_id` =  '25' WHERE  `handyman`.`id` =18;
UPDATE  `handyman` SET  `service_id` =  '13' WHERE  `handyman`.`id` =19;
UPDATE  `handyman` SET  `service_id` =  '13' WHERE  `handyman`.`id` =20;
UPDATE  `handyman` SET  `service_id` =  '30' WHERE  `handyman`.`id` =21;
UPDATE  `handyman` SET  `service_id` =  '10' WHERE  `handyman`.`id` =22;
UPDATE  `handyman` SET  `service_id` =  '10' WHERE  `handyman`.`id` =23;
UPDATE  `handyman` SET  `service_id` =  '14' WHERE  `handyman`.`id` =24;
UPDATE  `handyman` SET  `service_id` =  '16' WHERE  `handyman`.`id` =25;
UPDATE  `handyman` SET  `service_id` =  '20' WHERE  `handyman`.`id` =26;
UPDATE  `handyman` SET  `service_id` =  '22' WHERE  `handyman`.`id` =27;
UPDATE  `handyman` SET  `service_id` =  '19' WHERE  `handyman`.`id` =28;
UPDATE  `handyman` SET  `service_id` =  '26' WHERE  `handyman`.`id` =29;
UPDATE  `handyman` SET  `service_id` =  '37' WHERE  `handyman`.`id` =30;
UPDATE  `handyman` SET  `service_id` =  '25' WHERE  `handyman`.`id` =31;
UPDATE  `handyman` SET  `service_id` =  '37' WHERE  `handyman`.`id` =32;
UPDATE  `handyman` SET  `service_id` =  '1' WHERE  `handyman`.`id` =33;
UPDATE  `handyman` SET  `service_id` =  '37' WHERE  `handyman`.`id` =34;
UPDATE  `handyman` SET  `service_id` =  '38' WHERE  `handyman`.`id` =35;
UPDATE  `handyman` SET  `service_id` =  '31' WHERE  `handyman`.`id` =36;
UPDATE  `handyman` SET  `service_id` =  '4' WHERE  `handyman`.`id` =37;
UPDATE  `handyman` SET  `service_id` =  '26' WHERE  `handyman`.`id` =38;
UPDATE  `handyman` SET  `service_id` =  '26' WHERE  `handyman`.`id` =39;
UPDATE  `handyman` SET  `service_id` =  '26' WHERE  `handyman`.`id` =40;
UPDATE  `handyman` SET  `service_id` =  '27' WHERE  `handyman`.`id` =41;
UPDATE  `handyman` SET  `service_id` =  '27' WHERE  `handyman`.`id` =42;
UPDATE  `handyman` SET  `service_id` =  '10' WHERE  `handyman`.`id` =43;
UPDATE  `handyman` SET  `service_id` =  '5' WHERE  `handyman`.`id` =44;
UPDATE  `handyman` SET  `service_id` =  '27' WHERE  `handyman`.`id` =45;
UPDATE  `handyman` SET  `service_id` =  '27' WHERE  `handyman`.`id` =46;

UPDATE  `handyman` SET  `service_id` =  '6' WHERE  `handyman`.`id` =47;
UPDATE  `handyman` SET  `service_id` =  '10' WHERE  `handyman`.`id` =48;
UPDATE  `handyman` SET  `service_id` =  '22' WHERE  `handyman`.`id` =49;
UPDATE  `handyman` SET  `service_id` =  '10' WHERE  `handyman`.`id` =50;
UPDATE  `handyman` SET  `service_id` =  '22' WHERE  `handyman`.`id` =51;

UPDATE  `handyman` SET  `service_id` =  '22' WHERE  `handyman`.`id` =52;
UPDATE  `handyman` SET  `service_id` =  '22' WHERE  `handyman`.`id` =53;
UPDATE  `handyman` SET  `service_id` =  '10' WHERE  `handyman`.`id` =54;

-------------------------------------table Services---------------------------------------------
ALTER TABLE  `services` ADD  `keywords` VARCHAR( 200 ) NOT NULL ;
<<<<<<< HEAD
ALTER TABLE  `services` ADD  `keywords` VARCHAR( 200 ) NULL DEFAULT NULL ;
ALTER TABLE  `handyman` CHANGE  `is_disabled`  `is_disabled` VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT  'No';

CREATE TABLE IF NOT EXISTS `user_handyman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL,
  `handyman_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
=======


--------------------------handyman-------------------------------------------------
/*ALTER TABLE  `handyman` ADD  `min_experience` VARCHAR( 50 ) NOT NULL AFTER  `experience` ,
ADD  `max_experience` VARCHAR( 50 ) NOT NULL AFTER  `min_experience` ;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =1;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =1;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '0' WHERE  `handyman`.`id` =2;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '1' WHERE  `handyman`.`id` =2;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '15' WHERE  `handyman`.`id` =3;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '20' WHERE  `handyman`.`id` =3;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =4;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =4;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =5;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =5;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =6;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =6;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =7;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =7;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '0' WHERE  `handyman`.`id` =8;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '25+' WHERE  `handyman`.`id` =8;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =9;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =9;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =10;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =10;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =11;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =11;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =12;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =12;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =13;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =13;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '12' WHERE  `handyman`.`id` =14;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =15;


UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =16;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =17;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =18;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =19;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =20;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '15' WHERE  `handyman`.`id` =21;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =22;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =23;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =24;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =25;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =26;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =27;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =28;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =29;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =30;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '15' WHERE  `handyman`.`id` =14;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =15;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =16;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =17;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =18;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =19;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =20;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '20' WHERE  `handyman`.`id` =21;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =22;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =23;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =24;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =25;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =26;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =27;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =28;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =29;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =30;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =31;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =31;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =32;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =32;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =33;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =33;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =34;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =34;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =35;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =35;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =36;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =36;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '0' WHERE  `handyman`.`id` =37;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '25+' WHERE  `handyman`.`id` =37;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =38;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =38;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =39;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =39;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =40;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =40;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =41;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =41;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =42;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =42;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =43;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =43;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =44;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =44;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =45;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =45;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =46;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =46;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =47;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =47;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =48;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =48;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =49;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =49;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '15' WHERE  `handyman`.`id` =50;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '20' WHERE  `handyman`.`id` =50;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '2' WHERE  `handyman`.`id` =51;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '5' WHERE  `handyman`.`id` =51;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '10' WHERE  `handyman`.`id` =52;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '12' WHERE  `handyman`.`id` =52;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '6' WHERE  `handyman`.`id` =53;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '10' WHERE  `handyman`.`id` =53;
UPDATE  `boloaaka`.`handyman` SET  `min_experience` =  '12' WHERE  `handyman`.`id` =54;
UPDATE  `boloaaka`.`handyman` SET  `max_experience` =  '15' WHERE  `handyman`.`id` =54;*/

-----------------------------handyman-----------------------------
ALTER TABLE  `handyman` CHANGE  `work_on_weekdays`  `work_on_weekdays` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `handyman` CHANGE  `works_on_weekends`  `works_on_weekends` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;





--
-- Table structure for table `saveHandyman`
--

CREATE TABLE IF NOT EXISTS `saveHandyman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(200) NOT NULL,
  `handyman_id` varchar(50) NOT NULL,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

RENAME TABLE  `boloaaka`.`saveHandyman` TO  `boloaaka`.`save_handyman` ;
ALTER TABLE  `save_handyman` ADD  `create_date` DATE NOT NULL ;


-----------------------------popularsearch-------------------------------
ALTER TABLE  `popularSearch` ADD  `update_date` TIMESTAMP NOT NULL ,
ADD  `create_date` TIMESTAMP NOT NULL ;


----------------------------------------services-------------------
ALTER TABLE  `services` ADD  `update_date` TIMESTAMP NOT NULL ,
ADD  `create_date` TIMESTAMP NOT NULL ;

----------------------------------------services-------------------
ALTER TABLE  `services` ADD  `priority` INT( 20 ) NOT NULL ;
UPDATE  `boloaaka`.`services` SET  `priority` =  '1' WHERE  `services`.`id` =1;
UPDATE  `boloaaka`.`services` SET  `priority` =  '2' WHERE  `services`.`id` =2;
UPDATE  `boloaaka`.`services` SET  `priority` =  '3' WHERE  `services`.`id` =3;
UPDATE  `boloaaka`.`services` SET  `priority` =  '4' WHERE  `services`.`id` =4;
UPDATE  `boloaaka`.`services` SET  `priority` =  '5' WHERE  `services`.`id` =5;
UPDATE  `boloaaka`.`services` SET  `priority` =  '6' WHERE  `services`.`id` =6;
UPDATE  `boloaaka`.`services` SET  `priority` =  '7' WHERE  `services`.`id` =7;
UPDATE  `boloaaka`.`services` SET  `priority` =  '8' WHERE  `services`.`id` =8;
UPDATE  `boloaaka`.`services` SET  `priority` =  '9' WHERE  `services`.`id` =9;
UPDATE  `boloaaka`.`services` SET  `priority` =  '10' WHERE  `services`.`id` =10;
UPDATE  `boloaaka`.`services` SET  `priority` =  '11' WHERE  `services`.`id` =11;
UPDATE  `boloaaka`.`services` SET  `priority` =  '12' WHERE  `services`.`id` =12;
UPDATE  `boloaaka`.`services` SET  `priority` =  '13' WHERE  `services`.`id` =13;
UPDATE  `boloaaka`.`services` SET  `priority` =  '14' WHERE  `services`.`id` =14;
UPDATE  `boloaaka`.`services` SET  `priority` =  '15' WHERE  `services`.`id` =15;
UPDATE  `boloaaka`.`services` SET  `priority` =  '16' WHERE  `services`.`id` =16;
UPDATE  `boloaaka`.`services` SET  `priority` =  '17' WHERE  `services`.`id` =17;
UPDATE  `boloaaka`.`services` SET  `priority` =  '18' WHERE  `services`.`id` =18;
UPDATE  `boloaaka`.`services` SET  `priority` =  '19' WHERE  `services`.`id` =19;
UPDATE  `boloaaka`.`services` SET  `priority` =  '20' WHERE  `services`.`id` =20;
UPDATE  `boloaaka`.`services` SET  `priority` =  '21' WHERE  `services`.`id` =21;
UPDATE  `boloaaka`.`services` SET  `priority` =  '22' WHERE  `services`.`id` =22;
UPDATE  `boloaaka`.`services` SET  `priority` =  '23' WHERE  `services`.`id` =23;
UPDATE  `boloaaka`.`services` SET  `priority` =  '24' WHERE  `services`.`id` =24;
UPDATE  `boloaaka`.`services` SET  `priority` =  '25' WHERE  `services`.`id` =25;
UPDATE  `boloaaka`.`services` SET  `priority` =  '26' WHERE  `services`.`id` =26;
UPDATE  `boloaaka`.`services` SET  `priority` =  '27' WHERE  `services`.`id` =27;
UPDATE  `boloaaka`.`services` SET  `priority` =  '28' WHERE  `services`.`id` =28;
UPDATE  `boloaaka`.`services` SET  `priority` =  '29' WHERE  `services`.`id` =29;
UPDATE  `boloaaka`.`services` SET  `priority` =  '30' WHERE  `services`.`id` =30;
UPDATE  `boloaaka`.`services` SET  `priority` =  '31' WHERE  `services`.`id` =31;
UPDATE  `boloaaka`.`services` SET  `priority` =  '32' WHERE  `services`.`id` =32;
UPDATE  `boloaaka`.`services` SET  `priority` =  '33' WHERE  `services`.`id` =33;
UPDATE  `boloaaka`.`services` SET  `priority` =  '37' WHERE  `services`.`id` =37;
UPDATE  `boloaaka`.`services` SET  `priority` =  '38' WHERE  `services`.`id` =38;
UPDATE  `boloaaka`.`services` SET  `priority` =  '39' WHERE  `services`.`id` =39;


--------------------------handyman Review--------------------------------------------------
ALTER TABLE  `handyman_review` ADD  `create_date` TIMESTAMP NOT NULL ,
ADD  `status` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE  `handyman_review` CHANGE  `status`  `status` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT  '0';


-------------------------------------18/4/2015---------------------------------------------------------------------------------------
-------------------------------Services----------------------------------------------------------------------
ALTER TABLE  `services` ADD  `action` INT NOT NULL DEFAULT  '1';

--
-- Table structure for table `signup_message`
--

CREATE TABLE IF NOT EXISTS `signup_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `signup_message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `signup_message`
--

INSERT INTO `signup_message` (`id`, `signup_message`) VALUES
(1, 'update signup_message');


--
-- Table structure for table `reviewmessage`
--

CREATE TABLE IF NOT EXISTS `reviewmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reviewmessage` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `reviewmessage`
--

INSERT INTO `reviewmessage` (`id`, `reviewmessage`) VALUES
(1, 'update review message');


--
-- Table structure for table `report_message`
--

CREATE TABLE IF NOT EXISTS `report_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `report_message`
--

INSERT INTO `report_message` (`id`, `report_message`) VALUES
(1, 'update Report messgae');

--
-- Table structure for table `advertise`
--

CREATE TABLE IF NOT EXISTS `advertise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ads` varchar(255) NOT NULL,
  `ads_picture` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `advertise`
--

INSERT INTO `advertise` (`id`, `ads`, `ads_picture`) VALUES
(1, 'test', '3f5faae337e90131a2078715fae31adb.png');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE IF NOT EXISTS `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(255) NOT NULL,
  `employee_password` varchar(255) NOT NULL,
  `right_for_add_handyman` varchar(50) NOT NULL DEFAULT '0',
  `right_for_add_service` varchar(50) NOT NULL DEFAULT '0',
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-------------------------20/4/2014-------------------------
-------------------------advertise----------------------------------------------------
ALTER TABLE  `advertise` ADD  `url` VARCHAR( 255 ) NOT NULL ;

-----------------------------------------employee--------------------------
ALTER TABLE  `employee` ADD  `right_for_edit_handyman` INT( 50 ) NOT NULL DEFAULT  '0',
ADD  `right_for_activate_deactivate` INT( 50 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `employee` DROP  `right_for_edit_handyman` ;
ALTER TABLE  `employee` CHANGE  `right_for_activate_deactivate`  `right_for_activate_deactivate_handyman` INT( 50 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `employee` ADD  `right_for_activate_deactivate_service` INT( 50 ) NOT NULL DEFAULT  '0';

--------------------------------21/4/2015---------------employee------------------------
ALTER TABLE  `employee` ADD  `right_for_xls_for_handyman` INT( 50 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `employee` ADD  `right_for_add_employee` INT( 50 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `employee` ADD  `right_for_update_employee` INT( 50 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `employee` ADD  `right_for_report_messgae` INT( 50 ) NOT NULL DEFAULT  '0',
ADD  `right_for_signup_message` INT( 50 ) NOT NULL DEFAULT  '0',
ADD  `right_for_review_message` INT( 50 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `employee` ADD  `right_for_approve_handyman` INT NOT NULL DEFAULT  '0';

--------------------------------------save_used_handyman-------------------------------------------
ALTER TABLE  `save_used_handyman` ADD  `user_id` VARCHAR( 100 ) NOT NULL ;

-------------------------------handyman------------------------------------------------------------------
ALTER TABLE  `handyman` ADD  `approved` INT NOT NULL DEFAULT  '1';
ALTER TABLE  `handyman` ADD  `employee_id_by_approve` INT NOT NULL ;
ALTER TABLE  `handyman` CHANGE  `employee_id_by_approve`  `approve_by` INT( 11 ) NOT NULL ;
ALTER TABLE  `handyman` CHANGE  `approve_by`  `approve_by` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `handyman` ADD  `approve_date` DATETIME NOT NULL ;


-----------------------handyman --------------------------------------------------------------
ALTER TABLE  `employee` ADD  `right_for_delete` INT( 11 ) NOT NULL DEFAULT  '0',
ADD  `right_for_verifyhandyman` INT( 11 ) NOT NULL DEFAULT  '1';
ALTER TABLE  `employee` CHANGE  `right_for_verifyhandyman`  `right_for_verifyhandyman` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `handyman` ADD  `verified` VARCHAR( 50 ) NOT NULL DEFAULT  '1';
ALTER TABLE  `handyman` ADD  `verify_by` VARCHAR( 255 ) NOT NULL ,
ADD  `verify_date` DATETIME NOT NULL ;


-----------------------------save_used_handyman-----------------------------

ALTER TABLE  `save_used_handyman` ADD  `comment` VARCHAR( 255 ) NOT NULL ,
ADD  `deactivate` INT( 11 ) NOT NULL DEFAULT  '1';
ALTER TABLE  `save_used_handyman` CHANGE  `deactivate`  `isreport_active` INT( 11 ) NOT NULL DEFAULT  '1';


--
-- Table structure for table `marketing_mail`
--

CREATE TABLE IF NOT EXISTS `marketing_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `marketing_mail`
--

INSERT INTO `marketing_mail` (`id`, `message`, `subject`) VALUES
(1, 'hiiii', 'infos');


----------------------employee----------------1/06/2015-----------

ALTER TABLE  `employee` ADD  `right_for_popularsearch` INT( 11 ) NOT NULL DEFAULT  '0';
------------------handyman-------------------------------------
ALTER TABLE  `handyman` CHANGE  `approved`  `approved` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `handyman` CHANGE  `verified`  `verified` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT  '0';
ALTER TABLE  `handyman` CHANGE  `action`  `action` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT  '0';

-----------------------------handyman--------------------------------------------------------------------------------------
ALTER TABLE  `handyman` ADD  `Android_Phone` VARCHAR( 255 ) NOT NULL ,
ADD  `common_charges` VARCHAR( 255 ) NOT NULL ;

ALTER TABLE  `handyman` CHANGE  `time_of_data_collection`  `time_of_data_collection` VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;

-----------------------employee---------------------------4/06/2015------------------
ALTER TABLE  `employee` ADD  `right_for_review` INT( 11 ) NOT NULL DEFAULT  '0';







--
-- Table structure for table `booking`
--

CREATE TABLE IF NOT EXISTS `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `time` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `handyman_id` varchar(255) NOT NULL,
  `data` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



---------------handyman...................................

ALTER TABLE  `handyman` ADD  `image_processing` VARCHAR( 100 ) NOT NULL DEFAULT  '1';




/************************ user_profile - 09/06/2015 ***************************/

ALTER TABLE `user_profile` ADD `is_verified` TINYINT( 1 ) NOT NULL DEFAULT '0',
ADD `create_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
ADD `update_date` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;


CREATE TABLE IF NOT EXISTS `passthru_misscall_log` (
  `s.no` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `callSid` varchar(255) NOT NULL,
  `From` varchar(255) NOT NULL,
  `To` varchar(255) NOT NULL,
  `Direction` varchar(255) NOT NULL,
  `DialCallDuration` varchar(255) NOT NULL,
  `StartTime` varchar(255) NOT NULL,
  `EndTime` varchar(255) NOT NULL,
  `CallType` varchar(255) NOT NULL,
  `DialWhomNumber` varchar(255) NOT NULL,
  `digits` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);



ALTER TABLE `passthru_misscall_log` CHANGE `callSid` `callSid` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `From` `From` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `To` `To` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `Direction` `Direction` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `DialCallDuration` `DialCallDuration` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `StartTime` `StartTime` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `EndTime` `EndTime` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `CallType` `CallType` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `DialWhomNumber` `DialWhomNumber` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
CHANGE `digits` `digits` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ;

ALTER TABLE `users` ADD `is_verified` TINYINT( 1 ) NOT NULL DEFAULT '0';

/************************ 11/06/2015 ***************************/
ALTER TABLE `handyman` CHANGE `profile_photo` `profile_photo` VARCHAR( 200 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;