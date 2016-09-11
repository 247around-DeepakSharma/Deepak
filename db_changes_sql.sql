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

-- Anuj 23 May 2016

RENAME TABLE  `boloaaka`.`bank_ac_statements` TO  `boloaaka`.`bank_transactions` ;
ALTER TABLE  `bank_transactions` ADD PRIMARY KEY (  `id` ) ;
ALTER TABLE  `bank_transactions` CHANGE  `id`  `id` INT( 10 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE  `bank_transactions` CHANGE  `name`  `partner_vendor_id` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE  `bank_transactions` CHANGE  `credit_of`  `credit_amount` INT( 10 ) NOT NULL ;
ALTER TABLE  `bank_transactions` CHANGE  `debit_of`  `debit_amount` INT( 10 ) NOT NULL ;


--
-- Table structure for table `sms_template`
-- Prashant 27 May 2016

CREATE TABLE IF NOT EXISTS `sms_template` (
  `id` int(11) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `template` text NOT NULL,
  `active` varchar(10) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sms_template`
--

INSERT INTO `sms_template` (`id`, `tag`, `template`, `active`, `create_date`) VALUES
(1, 'new_vendor_creation', 'Welcome dear %s, thanks for joining 247around network. Hope to have a long lasting working relationship with you. 247around Team 011-39595200.', '1', '2016-04-20 07:54:24'),
(2, 'add_new_booking', 'Got it! Request for %s Repair is confirmed for %s, %s. 247Around Indias 1st Multibrand Appliance repair App goo.gl/m0iAcS. 011-39595200', '1', '2016-05-25 11:06:10'),
(3, 'complete_booking', 'Your request for %s Repair completed. Like us on Facebook goo.gl/Y4L6Hj For discounts download app goo.gl/m0iAcS. For feedback call 011-39595200.', '1', '2016-05-25 11:53:48'),
(4, 'cancel_booking', 'Your request for %s Repair is cancelled. For discounts download app 247Around goo.gl/m0iAcS. Like us on Facebook goo.gl/Y4L6Hj. 011-39595200', '1', '2016-05-25 12:00:00'),
(5, 'reschedule_booking', 'Your request for %s Repair is rescheduled to %s, %s. To avail discounts book on App 247Around goo.gl/m0iAcS. 011-39595200', '1', '2016-05-26 12:48:21'),
(6, 'new_snapdeal_booking', 'Got it! Request for %s Installation is confirmed for %s,%s. 247around India''s 1st Multibrand Appliance Care & Snapdeal Partner. 9555000247', '1', '2016-05-26 10:00:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sms_template`
--
ALTER TABLE `sms_template`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sms_template`
--
ALTER TABLE `sms_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Anuj 27 May 2016
ALTER TABLE `bank_transactions` CHANGE `partner_vendor_id` `partner_vendor_id` INT NOT NULL;

-- Anuj 30 May 2016
ALTER TABLE `vendor_partner_invoices` ADD `amount_collected_paid` INT NOT NULL COMMENT 'Final amount which needs to be collected from vendor or to be paid to vendor' AFTER `around_royalty`;
ALTER TABLE `vendor_partner_invoices` CHANGE  `amount_collected_paid`  `amount_collected_paid` INT( 11 ) NOT NULL COMMENT 'Final amount which needs to be collected from vendor or to be paid to vendor. +ve => collect from vendor, -ve => pay to vendor';

<!-- Abhay 30 May -->
ALTER TABLE  `service_center_booking_action` ADD  `admin_remarks`  text NOT NULL  AFTER  `closing_remarks`  ;
ALTER TABLE  `service_center_booking_action` CHANGE  `create_date`  `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE  `service_center_booking_action` ADD  `status` VARCHAR( 50 ) NOT NULL AFTER  `create_date` ,
ADD  `close_date` DATETIME NOT NULL AFTER  `status` ;
ALTER TABLE  `service_center_booking_action` CHANGE  `close_date`  `closed_date` DATETIME NOT NULL ;
ALTER TABLE  `service_center_booking_action` ADD  `amount_paid` VARCHAR( 50 ) NOT NULL AFTER  `parts_cost` ;
ALTER TABLE  `service_center_booking_action` CHANGE  `status`  `current_status` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;

ALTER TABLE  `service_center_booking_action` ADD  `internal_status` VARCHAR( 100 ) NOT NULL AFTER  `current_status` ;
ALTER TABLE  `service_center_booking_action` CHANGE  `closing_remarks`  `service_center_remarks` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE  `booking_details` CHANGE  `closing_remarks`  `closing_remarks` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;


ALTER TABLE  `bank_transactions` CHANGE  `invoice_id`  `invoice_id` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;




<!-- Abhay 2 June -- >

ALTER TABLE  `booking_unit_details` ADD  `service_id` VARCHAR( 10 ) NOT NULL AFTER  ` booking_id` ;

ALTER TABLE  `booking_unit_details` ADD  `appliance_id` VARCHAR( 10 ) NOT NULL AFTER  `service_id` ;
ALTER TABLE  `booking_unit_details` ADD  `customer_total` VARCHAR( 50 ) NOT NULL AFTER  `appliance_tag` ,
ADD  `customer_net_payable` VARCHAR( 10 ) NOT NULL AFTER  `customer_total` ,
ADD  `partner_net_payable` VARCHAR( 10 ) NOT NULL AFTER  `customer_net_payable` ,
ADD  `around_net_payable` VARCHAR( 10 ) NOT NULL AFTER  `partner_net_payable` ,
ADD  `customer_paid_basic_charges` VARCHAR( 10 ) NOT NULL COMMENT  'what customer finally paid' AFTER  `around_net_payable` ,
ADD  `partner_paid_basic_charges` VARCHAR( 10 ) NOT NULL AFTER  `customer_paid_basic_charges` ,
ADD  `around_paid_basic_charges` VARCHAR( 10 ) NOT NULL AFTER  `partner_paid_basic_charges` ,
ADD  `around_comm_basic_charges` VARCHAR( 10 ) NOT NULL AFTER  `around_paid_basic_charges` ,
ADD  `around_st_basic_charges` VARCHAR( 10 ) NOT NULL AFTER  `around_comm_basic_charges` ,
ADD  `around_vat_basic_charges` VARCHAR( 10 ) NOT NULL AFTER  `around_st_basic_charges` ;

ALTER TABLE  `booking_unit_details` ADD  `vendor_basic_charges` VARCHAR( 10 ) NOT NULL AFTER  `around_vat_basic_charges` ,
ADD  `vendor_to_around - type A` VARCHAR( 10 ) NOT NULL AFTER  `vendor_basic_charges` ,
ADD  `around_to_vendor - type B` VARCHAR( 10 ) NOT NULL AFTER  `vendor_to_around - type A` ,
ADD  `vendor_st_basic_charges` VARCHAR( 10 ) NOT NULL AFTER  `around_to_vendor - type B` ,
ADD  `vendor_vat_basic_charges` VARCHAR( 10 ) NOT NULL AFTER  `vendor_st_basic_charges` ,
ADD  `customer_paid_extra_charges` VARCHAR( 10 ) NOT NULL AFTER  `vendor_vat_basic_charges` ,
ADD  `around_comm_extra_charges` VARCHAR( 10 ) NOT NULL AFTER  `customer_paid_extra_charges` ,
ADD  `around_st_extra_charges` VARCHAR( 10 ) NOT NULL AFTER  `around_comm_extra_charges` ,
ADD  `vendor_extra_charges` VARCHAR( 10 ) NOT NULL AFTER  `around_st_extra_charges` ,
ADD  `vendor_st_extra_charges` VARCHAR( 10 ) NOT NULL AFTER  `vendor_extra_charges` ;

ALTER TABLE  `booking_unit_details` ADD  `customer_paid_parts` VARCHAR( 10 ) NOT NULL AFTER  `vendor_st_extra_charges` ,
ADD  `around_comm_parts` VARCHAR( 10 ) NOT NULL AFTER  `customer_paid_parts` ,
ADD  `around_st_parts` VARCHAR( 10 ) NOT NULL AFTER  `around_comm_parts` ,
ADD  `vendor_parts` VARCHAR( 10 ) NOT NULL AFTER  `around_st_parts` ,
ADD  `vendor_st_parts` VARCHAR( 10 ) NOT NULL AFTER  `vendor_parts` ;


ALTER TABLE  `booking_unit_details` CHANGE  `vendor_to_around - type A`  `vendor_to_around` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT  'type A';
ALTER TABLE  `booking_unit_details` CHANGE  `around_to_vendor - type B`  `around_to_vendor` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT  'type B';
ALTER TABLE  `booking_unit_details` ADD  `tax_code` VARCHAR( 10 ) NOT NULL AFTER  `appliance_tag` ;
ALTER TABLE  `booking_unit_details` ADD  `product_or_services` VARCHAR( 50 ) NOT NULL AFTER  `appliance_tag` ;
ALTER TABLE  `booking_unit_details` ADD  `partner_id` INT( 10 ) NOT NULL AFTER  `booking_id` ;

ALTER TABLE  `service_centre_charges` CHANGE  `partner_payment`  `partner_net_payable` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE  `booking_unit_details` ADD  `purchase_month` VARCHAR( 20 ) NOT NULL AFTER  `purchase_year` ;


<!-- Abhay 3 June -->

ALTER TABLE  `bookings_sources` CHANGE  `price_mapping_code`  `price_mapping_id` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;

<!-- Abhay 7 June -->

ALTER TABLE  `booking_details` ADD  `order_id` VARCHAR( 25 ) NOT NULL AFTER  `booking_id` ;

ALTER TABLE  `booking_details` ADD  `product_type` VARCHAR( 250 ) NOT NULL AFTER  `order_id` ,
ADD  `delivery_date` DATETIME NOT NULL AFTER  `product_type` ,
ADD  `request_type` VARCHAR( 25 ) NOT NULL AFTER  `delivery_date` ;
ALTER TABLE  `booking_details` CHANGE  `appliance_id`  `appliance_id` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;

--
-- Table structure for table `booking_unit_details`
--

CREATE TABLE IF NOT EXISTS `booking_unit_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` varchar(20) NOT NULL,
  `partner_id` int(10) DEFAULT NULL,
  `service_id` varchar(10) NOT NULL,
  `appliance_id` varchar(10) NOT NULL,
  `appliance_brand` varchar(25) DEFAULT NULL,
  `appliance_category` varchar(50) DEFAULT NULL,
  `appliance_capacity` varchar(50) DEFAULT NULL,
  `appliance_size` varchar(10) DEFAULT NULL,
  `model_number` varchar(50) DEFAULT NULL,
  `price_tags` varchar(100) DEFAULT NULL,
  `appliance_tag` varchar(50) NOT NULL,
  `product_or_services` varchar(50) NOT NULL,
  `tax_rate` varchar(10) NOT NULL,
  `customer_total` varchar(50) NOT NULL,
  `customer_net_payable` varchar(50) NOT NULL,
  `partner_net_payable` varchar(50) NOT NULL,
  `around_net_payable` varchar(50) NOT NULL,
  `customer_paid_basic_charges` varchar(50) NOT NULL COMMENT 'what customer finally paid',
  `partner_paid_basic_charges` varchar(50) NOT NULL,
  `around_paid_basic_charges` varchar(50) NOT NULL,
  `around_comm_basic_charges` varchar(50) NOT NULL,
  `around_st_or_vat_basic_charges` varchar(50) NOT NULL,
  `vendor_basic_charges` varchar(50) NOT NULL,
  `vendor_to_around` varchar(50) NOT NULL COMMENT 'type A',
  `around_to_vendor` varchar(50) NOT NULL COMMENT 'type B',
  `vendor_st_or_vat_basic_charges` varchar(50) NOT NULL,
  `customer_paid_extra_charges` varchar(50) NOT NULL,
  `around_comm_extra_charges` varchar(50) NOT NULL,
  `around_st_extra_charges` varchar(50) NOT NULL,
  `vendor_extra_charges` varchar(50) NOT NULL,
  `vendor_st_extra_charges` varchar(50) NOT NULL,
  `customer_paid_parts` varchar(50) NOT NULL,
  `around_comm_parts` varchar(50) NOT NULL,
  `around_st_parts` varchar(50) NOT NULL,
  `vendor_parts` varchar(50) NOT NULL,
  `vendor_st_parts` varchar(50) NOT NULL,
  `purchase_year` varchar(10) DEFAULT NULL,
  `purchase_month` varchar(20) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `booking_picture_file` varchar(50) DEFAULT NULL,
  `total_price` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

<!-- Abhay 30-06-2016 -->


--
-- Table structure for table `booking_details`
--

CREATE TABLE IF NOT EXISTS `booking_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(10) NOT NULL,
  `service_id` varchar(10) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `order_id` varchar(25) NOT NULL,
  `product_type` varchar(250) NOT NULL,
  `delivery_date` datetime NOT NULL,
  `request_type` varchar(25) NOT NULL,
  `type` varchar(10) NOT NULL,
  `source` varchar(30) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL COMMENT 'partner id if booking was given by any partner',
  `booking_address` varchar(100) NOT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `booking_pincode` varchar(10) DEFAULT NULL,
  `booking_location` varchar(200) DEFAULT NULL,
  `booking_primary_contact_no` varchar(15) DEFAULT NULL,
  `booking_alternate_contact_no` varchar(20) DEFAULT NULL,
  `booking_date` varchar(100) NOT NULL,
  `booking_timeslot` varchar(10) NOT NULL,
  `booking_remarks` varchar(200) DEFAULT NULL,
  `query_remarks` varchar(200) DEFAULT NULL,
  `quantity` varchar(2) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` datetime DEFAULT NULL,
  `closed_date` datetime DEFAULT NULL,
  `current_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `internal_status` varchar(50) NOT NULL,
  `cancellation_reason` varchar(100) DEFAULT NULL,
  `assigned_vendor_id` varchar(5) DEFAULT NULL,
  `backup_vendor_id` varchar(5) DEFAULT NULL,
  `vendor_rating_stars` varchar(5) DEFAULT NULL,
  `vendor_rating_comments` varchar(500) DEFAULT NULL,
  `amount_due` varchar(5) DEFAULT NULL,
  `rating_stars` varchar(5) DEFAULT NULL,
  `rating_comments` varchar(200) DEFAULT NULL,
  `payment_method` varchar(10) DEFAULT NULL,
  `payment_txn_id` varchar(10) DEFAULT NULL,
  `closing_remarks` text,
  `booking_jobcard_filename` varchar(50) DEFAULT NULL,
  `mail_to_vendor` varchar(5) NOT NULL DEFAULT '0',
  `potential_value` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


<!-- Abhay  7/07/16 -->
ALTER TABLE  `service_centre_charges` CHANGE  `product_type`  `product_type` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;

ALTER TABLE  `service_center_booking_action` ADD  `unit_details_id` INT( 25 ) NULL DEFAULT NULL AFTER  `booking_id` ;

ALTER TABLE  `booking_details` CHANGE  `order_id`  `order_id` VARCHAR( 25 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;

--Abhya 06/07

ALTER TABLE  `booking_unit_details` ADD  `product_or_services` VARCHAR( 50 ) NOT NULL AFTER  `purchase_month` ,
ADD  `tax_rate` DECIMAL( 10, 3 ) NOT NULL AFTER  `product_or_services` ,
ADD  `customer_total` DECIMAL( 10, 2 ) NOT NULL AFTER  `tax_rate` ,
ADD  `customer_net_payable` DECIMAL( 10, 2 ) NOT NULL AFTER  `customer_total` ,
ADD  `partner_net_payable` DECIMAL( 10, 2 ) NOT NULL AFTER  `customer_net_payable` ,
ADD  `around_net_payable` DECIMAL( 10, 2 ) NOT NULL AFTER  `partner_net_payable` ,
ADD  `customer_paid_basic_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `around_net_payable` ,
ADD  `partner_paid_basic_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `customer_paid_basic_charges` ,
ADD  `around_paid_basic_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `partner_paid_basic_charges` ,
ADD  `around_comm_basic_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `around_paid_basic_charges` ;


ALTER TABLE  `booking_unit_details` ADD  `around_st_or_vat_basic_charges` DECIMAL( 10, 2 ) NOT NULL AFTER `around_comm_basic_charges` ,
ADD  `vendor_basic_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `around_st_or_vat_basic_charges` ,
ADD  `vendor_st_or_vat_basic_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `vendor_basic_charges` ,
ADD  `customer_paid_extra_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `vendor_st_or_vat_basic_charges` ,
ADD  `around_comm_extra_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `customer_paid_extra_charges` ,
ADD  `around_st_extra_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `around_comm_extra_charges` ,
ADD  `vendor_extra_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `around_st_extra_charges` ,
ADD  `vendor_st_extra_charges` DECIMAL( 10, 2 ) NOT NULL AFTER  `vendor_extra_charges` ,
ADD  `customer_paid_parts` DECIMAL( 10, 2 ) NOT NULL AFTER  `vendor_st_extra_charges` ,
ADD  `around_comm_parts` DECIMAL( 10, 2 ) NOT NULL AFTER  `customer_paid_parts` ;


ALTER TABLE  `booking_unit_details` ADD  `around_st_parts` DECIMAL( 10, 2 ) NOT NULL AFTER  `around_comm_parts` ,
ADD  `vendor_parts` DECIMAL( 10, 2 ) NOT NULL AFTER  `around_st_parts` ,
ADD  `vendor_st_parts` DECIMAL( 10, 2 ) NOT NULL AFTER  `vendor_parts` ,
ADD  `vendor_to_around` DECIMAL( 10, 2 ) NOT NULL AFTER  `vendor_st_parts` ,
ADD  `around_to_vendor` DECIMAL( 10, 2 ) NOT NULL AFTER  `vendor_to_around` ;


ALTER TABLE  `vendor_pincode_mapping_temp` CHANGE  `Region`  `Region` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;

<!-- Abhay 09 Aug-->
ALTER TABLE  `booking_details` ADD  `booking_landmark` VARCHAR( 200 ) NOT NULL AFTER  `booking_location` ;
ALTER TABLE  `booking_details` ADD  `reference_date` DATETIME NOT NULL AFTER  `appliance_id` ;

ALTER TABLE  `booking_unit_details` ADD  `appliance_size` VARCHAR( 25 ) NOT NULL AFTER  `model_number` ;


<!--Abhay 12 Aug -->
ALTER TABLE  `service_center_booking_action` ADD  `serial_number` VARCHAR( 50 ) NOT NULL AFTER  `parts_cost` ;


<!-- 17 Aug -->
ALTER TABLE  `booking_unit_details` CHANGE  `tax_rate`  `tax_rate` DECIMAL( 10, 2 ) NOT NULL ;

ALTER TABLE `booking_unit_details` CHANGE `customer_total` `customer_total` DECIMAL(10,2) NOT NULL, CHANGE `customer_net_payable` `customer_net_payable` DECIMAL(10,2) NOT NULL, CHANGE `partner_net_payable` `partner_net_payable` DECIMAL(10,2) NOT NULL, CHANGE `around_net_payable` `around_net_payable` DECIMAL(10,2) NOT NULL, CHANGE `customer_paid_basic_charges` `customer_paid_basic_charges` DECIMAL(10,2) NOT NULL, CHANGE `partner_paid_basic_charges` `partner_paid_basic_charges` DECIMAL(10,2) NOT NULL, CHANGE `around_paid_basic_charges` `around_paid_basic_charges` DECIMAL(10,2) NOT NULL, CHANGE `around_comm_basic_charges` `around_comm_basic_charges` DECIMAL(10,2) NOT NULL, CHANGE `around_st_or_vat_basic_charges` `around_st_or_vat_basic_charges` DECIMAL(10,2) NOT NULL, CHANGE `vendor_basic_charges` `vendor_basic_charges` DECIMAL(10,2) NOT NULL, CHANGE `vendor_to_around` `vendor_to_around` DECIMAL(10,2) NOT NULL, CHANGE `around_to_vendor` `around_to_vendor` DECIMAL(10,2) NOT NULL, CHANGE `vendor_st_or_vat_basic_charges` `vendor_st_or_vat_basic_charges` DECIMAL(10,2) NOT NULL, CHANGE `customer_paid_extra_charges` `customer_paid_extra_charges` DECIMAL(10,2) NOT NULL, CHANGE `around_comm_extra_charges` `around_comm_extra_charges` DECIMAL(10,2) NOT NULL, CHANGE `around_st_extra_charges` `around_st_extra_charges` DECIMAL(10,2) NOT NULL, CHANGE `vendor_extra_charges` `vendor_extra_charges` DECIMAL(10,2) NOT NULL, CHANGE `vendor_st_extra_charges` `vendor_st_extra_charges` DECIMAL(10,2) NOT NULL, CHANGE `customer_paid_parts` `customer_paid_parts` DECIMAL(10,2) NOT NULL, CHANGE `around_comm_parts` `around_comm_parts` DECIMAL(10,2) NOT NULL, CHANGE `around_st_parts` `around_st_parts` DECIMAL(10,2) NOT NULL, CHANGE `vendor_parts` `vendor_parts` DECIMAL(10,2) NOT NULL, CHANGE `vendor_st_parts` `vendor_st_parts` DECIMAL(10,2) NOT NULL, CHANGE `total_price` `total_price` VARCHAR(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;


ALTER TABLE  `booking_unit_details` CHANGE  `partner_paid_basic_charges`  `partner_paid_basic_charges` DECIMAL( 10, 2 ) NULL DEFAULT  '0.00' COMMENT  'store partner basic charge with tax';

/** Vendor invoices snapshot**/

CREATE TABLE IF NOT EXISTS `vendor_invoices_snapshot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(255) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `type_code` varchar(10) NOT NULL,
  `booking_id` varchar(100) NOT NULL,
  `city` varchar(100),
  `appliance` varchar(100) NOT NULL,
  `appliance_category` varchar(100) DEFAULT NULL,
  `appliance_capacity` varchar(100) DEFAULT NULL,
  `closed_date` datetime NOT NULL,
  `service_category` varchar(100) NOT NULL,
  `service_charge` decimal(10,2) DEFAULT '0.00',
  `service_tax` decimal(10,2) DEFAULT '0.00',
  `stand` decimal(10,2) DEFAULT '0.00',
  `vat` decimal(10,2) DEFAULT '0.00',
  `around_discount` decimal(10,2) DEFAULT '0.00',
  `addtional_service_charge` decimal(10,2) DEFAULT '0.00',
  `parts_cost` decimal(10,2) DEFAULT '0.00',
  `amount_paid` decimal(10,2) DEFAULT '0.00',
  `rating` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

<!-- Abhay -->

ALTER TABLE  `service_centre_charges` ADD  `pod` VARCHAR( 10 ) NOT NULL AFTER  `customer_net_payable` ;
ALTER TABLE  `service_centre_charges` CHANGE  `pod`  `pod` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT  '1' COMMENT 'Proof of Delivery. Default 1 i.e. for every service, proof is required like S No of the unit. In some cases, PoD is not required like stand or out-of-warranty repair. So flag would be 0.';

ALTER TABLE  `booking_details` CHANGE  `rating_stars`  `rating_stars` VARCHAR( 5 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;

CREATE TABLE IF NOT EXISTS `engineer_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `alternate_phone` varchar(20) NOT NULL,
  `phone_type` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `identity_proof` varchar(250) NOT NULL,
  `identity_proof_number` varchar(250) NOT NULL,
  `bank_name` varchar(250) NOT NULL,
  `banck_ac_no` varchar(250) NOT NULL,
  `bank_ifsc_code` varchar(100) NOT NULL,
  `bank_holder_name` varchar(250) NOT NULL,
  `service_center_id` int(10) NOT NULL,
  `appliance_id` text NOT NULL,
  `active` int(10) NOT NULL DEFAULT '1',
  `identity_proof_pic` varchar(250) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE  `engineer_details` ADD  `delete` INT( 10 ) NOT NULL DEFAULT  '0' AFTER  `active` ;

ALTER TABLE  `engineer_details` CHANGE  `delete`  `delete` INT( 10 ) NOT NULL DEFAULT  '0' COMMENT '0 means engineer not deleted and 1 means engineer deleted';

ALTER TABLE  `vendor_partner_invoices` CHANGE  `total_service_charge`  `total_service_charge` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `vendor_partner_invoices` CHANGE  `total_additional_service_charge`  `total_additional_service_charge` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `vendor_partner_invoices` CHANGE  `service_tax`  `service_tax` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `vendor_partner_invoices` CHANGE  `parts_cost`  `parts_cost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `vendor_partner_invoices` CHANGE  `vat`  `vat` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `vendor_partner_invoices` CHANGE  `total_amount_collected`  `total_amount_collected` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `vendor_partner_invoices` CHANGE  `around_royalty`  `around_royalty` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `vendor_partner_invoices` CHANGE  `amount_collected_paid`  `amount_collected_paid` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
