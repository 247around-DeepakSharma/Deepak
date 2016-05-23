<!--  --Abhay 9/4/16-->

ALTER TABLE  `bookings_sources` ADD  `partner_email_for_to` VARCHAR( 100 ) NOT NULL AFTER  `partner_id` ,
ADD  `partner_email_for_cc` VARCHAR( 100 ) NOT NULL AFTER  `patner_email_for_to` ;

UPDATE  `247around_test`.`bookings_sources` SET  `partner_email_for_cc` =  'heyrajcool@gmail.com,abhaya@247around' WHERE  `bookings_sources`.`id` =8;


<!-- Abhay 11/04/16  -->

CREATE TABLE IF NOT EXISTS `escalation_policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `escalation_reason` varchar(50) NOT NULL,
  `mail_to_owner` varchar(10) NOT NULL DEFAULT '0',
  `mail_to_poc` varchar(10) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `escalation_policy`
--

INSERT INTO `escalation_policy` (`id`, `escalation_reason`, `mail_to_owner`, `mail_to_poc`, `create_date`) VALUES
(1, 'The Engineer did not contact with a customer', '1', '1', '2016-04-11 07:58:00'),
(2, 'The Engineer did not visit on time', '1', '1', '2016-04-11 07:58:00'),
(3, 'Booking status is not updated', '1', '1', '2016-04-11 08:06:39'),
(4, 'Appliance is not returned yet', '1', '1', '2016-04-11 08:08:13'),
(5, 'Follow up visit is not happened', '1', '1', '2016-04-11 08:09:11'),
(6, 'They have not reply to booking summary mail', '1', '1', '2016-04-11 08:14:03');


<-- Abhay 12/4/16  -->

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;




<-- Abhay 12/4/16  -->

ALTER TABLE  `service_centres` ADD  `state` VARCHAR( 50 ) NOT NULL ,
ADD  `district` VARCHAR( 50 ) NOT NULL ,
ADD  `pincode` VARCHAR( 10 ) NOT NULL ;


<!-- Abhay 13/4/16 --->

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


<!-- Abhay 13/4/16  --->

ALTER TABLE  `partner_leads` ADD  `Processed_with_in_24 hrs` VARCHAR( 50 ) NOT NULL AFTER  `247aroundBookingRemarks` ;

ALTER TABLE  `partner_leads` CHANGE  `Processed_with_in_24 hrs`  `Processed_with_in_24_hrs` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;


<!-- Abhay 14/4/16 -->

CREATE TABLE IF NOT EXISTS `tax_rates_by_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax` varchar(50) NOT NULL,
  `Date` varchar(10) NOT NULL,
  `state` varchar(50) NOT NULL,
  `appliance` varchar(50) NOT NULL,
  `accessory` varchar(50) NOT NULL,
  `percentage_rate` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

RENAME TABLE  `247around_test`.`vendor_escalation_details` TO  `247around_test`.`vendor_escalation_log` ;
ALTER TABLE  `vendor_escalation_log` ADD  `booking_date` DATETIME NOT NULL AFTER  `escalation_reason` ;
ALTER TABLE  `vendor_escalation_log` CHANGE  `booking_date`  `booking_date` DATE NOT NULL ;
ALTER TABLE  `vendor_escalation_log` CHANGE  `booking_date`  `booking_date` VARCHAR( 50 ) NOT NULL ;


CREATE TABLE IF NOT EXISTS `excel_file_name_uploaded_in_s3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bucket_name` varchar(50) NOT NULL,
  `file_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

RENAME TABLE  `247around_test`.`excel_file_name_uploaded_in_s3` TO  `247around_test`.`excel_file_uploaded_in_s3` ;


<!-- Abhay 15/04/16  -->

ALTER TABLE  `excel_file_uploaded_in_s3` ADD  `create_date` TIMESTAMP NOT NULL AFTER  `file_name` ;
ALTER TABLE  `excel_file_uploaded_in_s3` CHANGE  `create_date`  `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE  `service_centres` ADD  `near_landmark` VARCHAR( 50 ) NOT NULL AFTER  `pincode` ;


<!-- Anuj 18/04/16  -->

ALTER TABLE  `vendor_escalation_log` ADD  `booking_date` DATE NOT NULL AFTER  `booking_id` ,
ADD  `booking_time` VARCHAR( 50 ) NOT NULL AFTER  `booking_date` ;

ALTER TABLE  `service_centres` ADD  `landmark` VARCHAR( 500 ) AFTER  `pincode`

CREATE TABLE IF NOT EXISTS `escalation_policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `escalation_reason` varchar(255) NOT NULL,
  `mail_to_owner` varchar(10) NOT NULL DEFAULT '0',
  `mail_to_poc` varchar(10) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

INSERT INTO `escalation_policy` VALUES
(1, 'Engineer has not contacted with customer.', '1', '1', '2016-04-11 02:28:00'),
(2, 'Engineer did not visit on time.', '1', '1', '2016-04-11 02:28:00'),
(3, 'Booking status is not updated.', '1', '1', '2016-04-11 02:36:39'),
(4, 'Appliance has not been returned yet.', '1', '1', '2016-04-11 02:38:13'),
(5, 'Follow up visit has not happened.', '1', '1', '2016-04-11 02:39:11'),
(6, 'No reply received for Pending Bookings Summary mail.', '1', '1', '2016-04-11 02:44:03');

CREATE TABLE IF NOT EXISTS `bookings_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(25) NOT NULL,
  `code` varchar(5) NOT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `partner_email_for_to` text NOT NULL,
  `partner_email_for_cc` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `source` (`source`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `vendor_escalation_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` varchar(10) NOT NULL,
  `booking_id` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` varchar(50) NOT NULL,
  `escalation_reason` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pincode_mapping_s3_upload_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bucket_name` varchar(50) NOT NULL,
  `file_name` varchar(50) NOT NULL,
    `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE  `escalation_policy` ADD  `sms_to_owner` VARCHAR( 1 ) NOT NULL AFTER  `mail_to_poc` ,
ADD  `sms_to_poc` VARCHAR( 1 ) NOT NULL AFTER  `sms_to_owner` ;

RENAME TABLE  `boloaaka`.`escalation_policy` TO  `boloaaka`.`vendor_escalation_policy` ;



<!-- Abhay 19/04/16 -- >

ALTER TABLE  `vendor_escalation_log` ADD  `escalation_policy_flag` VARCHAR( 100 ) NOT NULL AFTER  `escalation_reason` ;

--
-- Table structure for table `sms_template`
--

CREATE TABLE IF NOT EXISTS `sms_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL,
  `template` text NOT NULL,
  `active` varchar(10) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

<!-- Anuj 19/04/16 -- >

ALTER TABLE  `vendor_escalation_policy` ADD  `active` VARCHAR( 1 ) NOT NULL AFTER  `sms_to_poc` ;

ALTER TABLE `service_centres` ADD `sc_code` VARCHAR(10) NOT NULL AFTER `landmark`;


<!-- Abhay 19/04/16   -->

ALTER TABLE  `booking_details` ADD  `city` VARCHAR( 100 ) NOT NULL AFTER  `booking_address` ;
ALTER TABLE  `booking_details` ADD  `state` VARCHAR( 50 ) NOT NULL AFTER  `city` ;

ALTER TABLE  `users` ADD  `city` VARCHAR( 50 ) NOT NULL AFTER  `home_address` ,
ADD  `state` VARCHAR( 50 ) NOT NULL AFTER  `city` ;

<!-- Abhay 25/04/2016  -->

--
-- Table structure for table `booking_unit_details`
--

CREATE TABLE IF NOT EXISTS `booking_unit_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` varchar(20) NOT NULL,
  `appliance_brand` varchar(25) DEFAULT NULL,
  `appliance_category` varchar(50) DEFAULT NULL,
  `appliance_capacity` varchar(50) DEFAULT NULL,
  `model_number` varchar(50) DEFAULT NULL,
  `price_tags` varchar(100) DEFAULT NULL,
  `appliance_tag` varchar(50) NOT NULL,
  `vendor_svc_charge` varchar(10) NOT NULL,
  `vendor_tax` varchar(10) NOT NULL,
  `around_svc_charge` varchar(10) NOT NULL,
  `around_tax` varchar(10) NOT NULL,
  `customer_total` varchar(10) NOT NULL,
  `partner_payment` varchar(10) NOT NULL,
  `customer_charges` varchar(10) NOT NULL,
  `discount_code` varchar(20) NOT NULL,
  `discount_amount` varchar(10) NOT NULL,
  `discount_offered_by` varchar(50) NOT NULL,
  `product_or_service` varchar(10) NOT NULL,
  `final_paid_by_customer` varchar(10) NOT NULL,
  `purchase_year` varchar(10) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `booking_picture_file` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-
-- Table structure for table `service_centre_charges`
--

CREATE TABLE IF NOT EXISTS `service_centre_charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_code` varchar(10) NOT NULL,
  `city` varchar(25) NOT NULL,
  `service_id` varchar(10) NOT NULL COMMENT 'appliance category like tv, ac, refrigerator etc',
  `category` varchar(50) NOT NULL,
  `capacity` varchar(50) DEFAULT NULL,
  `service_category` varchar(100) NOT NULL,
  `product_or_services` varchar(10) NOT NULL,
  `tax_code` varchar(10) NOT NULL,
  `active` varchar(2) DEFAULT NULL COMMENT 'Row is active or not',
  `check_box` varchar(2) DEFAULT NULL COMMENT 'Displayed as CheckBox or not?',
  `vendor_svc_charge` varchar(10) NOT NULL,
  `vendor_tax` varchar(10) NOT NULL,
  `around_svc_charge` varchar(10) NOT NULL,
  `around_tax` varchar(10) NOT NULL,
  `customer_total` varchar(10) NOT NULL,
  `partner_payment` varchar(10) NOT NULL,
  `customer_charges` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE  `bookings_sources` ADD  `price_mapping_code` VARCHAR( 10 ) NOT NULL AFTER  `code` ;

<!-- Abhay 24-04-2016 --->

ALTER TABLE  `booking_unit_details` ADD  `tax_code` VARCHAR( 10 ) NOT NULL AFTER  `product_or_service` ;

ALTER TABLE  `booking_unit_details` CHANGE  `product_or_service`  `product_or_services` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;


<!-- Abhay 09-04-2016  -->

ALTER TABLE  `service_centres` CHANGE  `sc_code`  `sc_code` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;


<!-- Abhay 18-04-2016 -->


--
-- Table structure for table `service_center_booking_action`
--

CREATE TABLE IF NOT EXISTS `service_center_booking_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` varchar(50) NOT NULL,
  `service_center_id` varchar(50) NOT NULL,
  `service_charge` varchar(50) NOT NULL,
  `additional_service_charge` varchar(50) NOT NULL,
  `parts_cost` varchar(50) NOT NULL,
  `closing_remarks` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `service_center_booking_action`
--

INSERT INTO `service_center_booking_action` (`id`, `booking_id`, `service_center_id`, `service_charge`, `additional_service_charge`, `parts_cost`, `closing_remarks`, `create_date`) VALUES
(1, 'SW-05381605141', '1', '120', '500', '120', 'test', '2016-05-18 11:17:03');



--
-- Table structure for table `service_centers_login`
--

CREATE TABLE IF NOT EXISTS `service_centers_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `service_center_id` varchar(50) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `active` varchar(10) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reset_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `service_centers_login`
--

INSERT INTO `service_centers_login` (`id`, `email`, `service_center_id`, `user_name`, `password`, `active`, `create_date`, `reset_date`) VALUES
(1, 'abhaya@247around.com', '1', 'test', '25d55ad283aa400af464c76d713c07ad', '1', '2016-05-17 11:56:54', '0000-00-00 00:00:00');


<!-- Prashant 20/05/2016 -->
--
-- Table structure for table `bank_ac_statements`
--

CREATE TABLE IF NOT EXISTS `bank_ac_statements` (
  `id` int(10) NOT NULL,
  `partner_vendor` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `bankname` varchar(255) NOT NULL,
  `credit_debit` varchar(20) NOT NULL,
  `credit_of` int(10) NOT NULL,
  `debit_of` int(10) NOT NULL,
  `transaction_mode` varchar(50) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(1024) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--