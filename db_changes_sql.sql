
<!--  --Abhay 9/4/16-->

ALTER TABLE  `bookings_sources` ADD  `partner_email_for_to` VARCHAR( 100 ) NOT NULL AFTER  `partner_id` ,
ADD  `partner_email_for_cc` VARCHAR( 100 ) NOT NULL 

ALTER TABLE  `bank_transactions` ADD  `tds_amount` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00' AFTER  `debit_amount` ;
ALTER TABLE  `vendor_partner_invoices` ADD  `settel_mount` INT( 10 ) NOT NULL DEFAULT  '0' AFTER  `tds_amount` ;  `patner_email_for_to` ;

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




ALTER TABLE  `engineer_details` CHANGE  `delete`  `delete` INT( 10 ) NOT NULL DEFAULT  '0' COMMENT '0 means engineer not deleted and 1 means engineer deleted';
ALTER TABLE  `vendor_partner_invoices` ADD  `tds_amount` DECIMAL( 10, 2 ) NOT NULL AFTER  `amount_collected_paid` ;
ALTER TABLE  `vendor_partner_invoices` CHANGE  `total_service_charge`  `total_service_charge` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
CHANGE  `total_additional_service_charge`  `total_additional_service_charge` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
CHANGE  `service_tax`  `service_tax` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
CHANGE  `parts_cost`  `parts_cost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
CHANGE  `vat`  `vat` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
CHANGE  `total_amount_collected`  `total_amount_collected` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
CHANGE  `around_royalty`  `around_royalty` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
CHANGE  `amount_collected_paid`  `amount_collected_paid` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0' COMMENT 'Final amount which needs to be collected from vendor or to be paid to vendor. +ve => collect from vendor, -ve => pay to vendor',
CHANGE  `tds_amount`  `tds_amount` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `bank_transactions` ADD  `tds_amount` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00' AFTER  `debit_amount` ;
ALTER TABLE  `vendor_partner_invoices` ADD  `settle_amount` INT( 10 ) NOT NULL DEFAULT  '0' AFTER  `tds_amount` ;
ALTER TABLE  `engineer_details` CHANGE  `identity_proof_pic`  `identity_proof_pic` VARCHAR( 250 ) CHARACTER SET latin1 
COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `engineer_details` ADD  `bank_proof_pic` VARCHAR( 250 ) NULL AFTER  `identity_proof_pic` ;



<!-- Abhay -- 14 Sept -->

ALTER TABLE  `vendor_partner_invoices` ADD  `amount_paid` DECIMAL( 10, 2 ) NOT NULL AFTER  `tds_amount` ;
ALTER TABLE  `vendor_partner_invoices` CHANGE  `amount_paid`  `amount_paid` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0.00';
ALTER TABLE  `engineer_details` CHANGE  `banck_ac_no`  `bank_ac_no` VARCHAR( 250 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;


<-- Abhay 17Sept -->
ALTER TABLE  `booking_state_change` ADD  `partner_id` INT( 10 ) NOT NULL AFTER  `agent_id` ;

<-- Belal 19Sept -->

ALTER TABLE `partners`  ADD `address` VARCHAR(100) NOT NULL  AFTER `public_name`,  
ADD `district` VARCHAR(50) NOT NULL  AFTER `address`,  ADD `state` VARCHAR(50) NOT NULL  AFTER `district`,  
ADD `pincode` VARCHAR(10) NOT NULL  AFTER `state`,  ADD `landmark` VARCHAR(500) NOT NULL  AFTER `pincode`,  
ADD `registration_number` VARCHAR(50) NOT NULL  AFTER `landmark`,  
ADD `primary_contact_name` VARCHAR(50) NOT NULL  AFTER `registration_number`,  
ADD `primary_contact_email` VARCHAR(50) NOT NULL  AFTER `primary_contact_name`,  
ADD `primary_contact_phone_1` VARCHAR(20) NOT NULL  AFTER `primary_contact_email`,  
ADD `primary_contact_phone_2` VARCHAR(20) NOT NULL  AFTER `primary_contact_phone_1`,  
ADD `owner_name` VARCHAR(50) NOT NULL  AFTER `primary_contact_phone_2`,  
ADD `owner_email` VARCHAR(50) NOT NULL  AFTER `owner_name`,  
ADD `owner_phone_1` VARCHAR(20) NOT NULL  AFTER `owner_email`,  
ADD `owner_phone_2` VARCHAR(20) NOT NULL  AFTER `owner_phone_1`,  
ADD `invoice_email_to` VARCHAR(2048) NOT NULL  AFTER `owner_phone_2`,  
ADD `invoice_email_cc` VARCHAR(2048) NOT NULL  AFTER `invoice_email_to`,  
ADD `invoice_email_bcc` VARCHAR(2048) NOT NULL  AFTER `invoice_email_cc`,  
ADD `summary_email_to` VARCHAR(2048) NOT NULL  AFTER `invoice_email_bcc`,  
ADD `summary_email_cc` VARCHAR(2048) NOT NULL  AFTER `summary_email_to`,  
ADD `summary_email_bcc` VARCHAR(2048) NOT NULL  AFTER `summary_email_cc`;

CREATE TABLE `sms_sent_details` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `user_id` int(20) NOT NULL,
 `user_type` varchar(20) NOT NULL,
 `phone` varchar(20) NOT NULL,
 `booking_id` varchar(50) NOT NULL,
 `content` mediumtext NOT NULL,
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;


<!-- Abhay 20 Sept -->
ALTER TABLE  `partners` ADD  `is_reporting_mail` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `auth_token` ;

-- ANUJ 21 Sept --
ALTER TABLE  `vendor_partner_invoices` CHANGE  `settle_amount`  `settle_amount` INT( 10 ) NOT NULL 
DEFAULT  '0' COMMENT 'Flag to check whether invoice is settled fully or not';

<!-- Abhay 22 Sept-->
ALTER TABLE  `sms_sent_details` CHANGE  `user_id`  `type_id` INT( 20 ) NULL DEFAULT NULL ,
CHANGE  `user_type`  `type` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;


-- ANUJ 22 Sept --

INSERT INTO  `sms_template` (
`id` ,
`tag` ,
`template` ,
`comments` ,
`active` ,
`create_date`
)
VALUES (
NULL ,  'missed_call_confirmed', 'Thank you for the delivery confirmation, %s Installation & Demo of your %s would be done %s. Installation Powered by 247around.com', 'SMS sent when customer gives a missed call to confirm delivery',  '1', 
CURRENT_TIMESTAMP
), (
NULL ,  'sd_shipped_missed_call_initial', 'Your %s from Snapdeal is shipped. After delivery give Missed Call at 011-30017601 for %s Installation. Installation Powered by 247around.com', '1st SMS sent to customer when SD shipped file is uploaded.',  '1', 
CURRENT_TIMESTAMP
);

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES 
(NULL, 'sd_edd_missed_call_reminder', 'Your %s from Snapdeal will be delivered tomorrow. Give Missed Call @ 011-30017601 for %s Installation. Installation Powered by 247around.com', 'Reminder SMS to SD customer before EDD for delivery confirmation', '1', CURRENT_TIMESTAMP);

INSERT INTO  `sms_template` (
`id` ,
`tag` ,
`template` ,
`comments` ,
`active` ,
`create_date`
)
VALUES (
NULL ,  'missed_call_booking_not_found', 'Oops, we could not find your booking. Please give missed call from your registered mobile no for Installation & Demo. Installation Powered by 247around.com.', 'SMS sent when missed call is received but no booking is found for user',  '1',  '2016-09-22 12:37:24'
);



--Abhay 23Sept

ALTER TABLE  `booking_state_change` CHANGE  `agent_id`  `agent_id` INT( 11 ) NULL ,
CHANGE  `partner_id`  `partner_id` INT( 10 ) NULL ;


-- Belal 23 Sep

ALTER TABLE `booking_state_change` DROP `old_reason`, DROP `new_reason`;
ALTER TABLE `booking_state_change` ADD `response` VARCHAR(500) NOT NULL AFTER `new_reason`;

--  Belal 24 Sep
ALTER TABLE `booking_state_change` CHANGE `response` `remarks` VARCHAR(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;


-- Belal 26 Sep
CREATE TABLE `booking_updation_reasons` (
  `id` int(11) NOT NULL,
  `old_state` varchar(30) NOT NULL,
  `new_state` varchar(30) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `reason_of` varchar(25) NOT NULL,
  `show_on_app` varchar(1) NOT NULL COMMENT 'Will this be shown to mobile app users?',
  `active` int(1) NOT NULL DEFAULT '1' COMMENT '1->Enabled, 0->Disabled'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `booking_updation_reasons`
--

INSERT INTO `booking_updation_reasons` (`id`, `old_state`, `new_state`, `reason`, `reason_of`, `show_on_app`, `active`) VALUES
(1, '', '', 'Your problem is resolved.', '247around', '1', 1),
(2, '', '', 'You entered a wrong booking.', '247around', '1', 1),
(3, '', '', 'You found a better option for this job.', '247around', '1', 1),
(4, '', '', 'You will not be available at this time.', '247around', '1', 1),
(5, '', '', 'You believe someone else did this booking.', '247around', '1', 1),
(7, '', '', 'Customer is not reachable.', '247around', '0', 1),
(8, '', '', 'Vendor issue', '247around', '0', 1),
(9, '', '', 'Other', '247around', '1', 1),
(10, '', '', 'Our charges are higher.', '247around', '0', 1),
(11, '', '', 'Customer will contact Brand Service Centre directly.', '247around', '0', 1),
(12, '', '', 'Installation already done.', 'vendor', '0', 1),
(13, '', '', 'Installation not required.', 'vendor', '0', 1),
(14, '', '', 'Customer problem is solved.', 'vendor', '0', 1),
(15, '', '', 'Repair not required.', 'vendor', '0', 1),
(16, '', '', 'Customer is not reachable.', 'vendor', '0', 1),
(17, '', '', 'Damaged product, customer will return it.', 'vendor', '0', 1),
(18, '', '', 'Customer will gift the product.', 'vendor', '0', 1),
(19, '', '', 'Wrong call - Not in our area.', 'vendor', '0', 1),
(20, '', '', 'Wrong call - We do not handle TV.', 'vendor', '0', 1),
(21, '', '', 'Wrong call - We do not handle AC.', 'vendor', '0', 1),
(22, '', '', 'Wrong call - We do not handle Refrigerator.', 'vendor', '0', 1),
(23, '', '', 'Wrong call - We do not handle Water Purifier.', 'vendor', '0', 1),
(24, '', '', 'Wrong call - We do not handle Chimney.', 'vendor', '0', 1),
(25, '', '', 'Wrong call - We do not handle Washing Machine.', 'vendor', '0', 1),
(26, '', '', 'Wrong call - We do not handle Microwave.', 'vendor', '0', 1),
(27, '', '', 'Vendor provided wrong information', '247around', '0', 1),
(28, 'Completed', 'Pending', 'Problem Not Resolved.', '247around', '1', 1),
(29, 'Completed', 'Pending', 'Customer  Not Satisfied.', '247around', '1', 1),
(31, 'Cancelled', 'Pending', 'Customer Request', '247around', '1', 1),
(32, 'Pending', 'Pending', 'Customer late response.', '247around', '1', 1),
(33, 'Pending', 'Pending', 'Customer Rescheduled', '247around', '1', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_updation_reasons`
--
ALTER TABLE `booking_updation_reasons`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_updation_reasons`
--
ALTER TABLE `booking_updation_reasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;


-- Belal 27 Sep

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'vendor_login_details', 'Dear Partner,<br><br> Following are the login credentials to 247Around CRM.<br><br> <b>Username : </b> %s <br> <b>Password : </b> %s <br> For any confusion, write to us or call us.<br><br> Regards,<br> 247around Team', '', '', '', '', '1', '2016-09-27 00:00:00');

-- Abhay 27 Sept --
ALTER TABLE  `booking_details` ADD  `assigned_engineer_id` INT( 20 ) NOT NULL AFTER  `assigned_vendor_id` ;
ALTER TABLE  `booking_details` CHANGE  `assigned_engineer_id`  `assigned_engineer_id` INT( 20 ) NULL ;


--
-- Table structure for table `assigned_engineer`
--

CREATE TABLE IF NOT EXISTS `assigned_engineer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` varchar(250) NOT NULL,
  `engineer_id` varchar(20) NOT NULL,
  `current_state` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE  `booking_state_change` ADD  `service_center_id` INT( 20 ) NULL AFTER  `partner_id` ;

-- Belal 29 Sep

CREATE TABLE `247around_vendor_pincode_mapping` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `Vendor_Name` varchar(200) NOT NULL,
 `Vendor_ID` int(11) NOT NULL,
 `Appliance` varchar(100) NOT NULL,
 `Appliance_ID` int(11) NOT NULL,
 `Brand` varchar(50) NOT NULL,
 `Area` varchar(50) NOT NULL,
 `Pincode` varchar(6) NOT NULL,
 `Region` varchar(25) NOT NULL,
 `City` varchar(255) NOT NULL,
 `State` varchar(255) NOT NULL,
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `active` int(1) NOT NULL DEFAULT '1',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Anuj 04 Oct
CREATE TABLE `vendor_invoices_snapshot_draft` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `type_code` varchar(10) NOT NULL,
  `booking_id` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
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
  `rating` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `vendor_invoices_snapshot_draft`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `vendor_invoices_snapshot_draft`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- Belal 01 Oct

CREATE TABLE  `247around_email_template` (
 `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `entity` VARCHAR( 50 ) NOT NULL ,
 `template` VARCHAR( 4096 ) NOT NULL ,
 `subject` VARCHAR( 512 ) NOT NULL ,
 `body` VARCHAR( 4096 ) NOT NULL ,
 `from` VARCHAR( 128 ) NOT NULL ,
 `to` VARCHAR( 1024 ) NOT NULL ,
 `cc` VARCHAR( 1024 ) NOT NULL ,
 `bcc` VARCHAR( 1024 ) NOT NULL ,
 `template_values` VARCHAR( 1024 ) NOT NULL COMMENT  'tablename.columnname.primarykey',
 `attachment` VARCHAR( 128 ) NOT NULL ,
 `active` VARCHAR( 1 ) NOT NULL COMMENT  '1->Active, 0->Disabled',
 `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (  `id` )
) ENGINE = INNODB AUTO_INCREMENT =1 DEFAULT CHARSET = latin1

INSERT INTO `247around_email_template` (`id`, `entity`, `template`, `subject`, `body`, `from`, `to`, `cc`, `bcc`, `template_values`, `attachment`, `active`, `create_date`) VALUES
(1, 'vendor', 'vendor_login_details', '247Around Login Details', 'Dear Partner,<br><br>\nFollowing are the login credentials to 247Around CRM.<br><br>\n<b>Username : </b> %s <br>\n<b>Password : </b> %s <br>\nFor any confusion, write to us or call us.<br><br>\nRegards,<br>\n247around Team', 'booking@247around.com', '', '', '', 'service_centers_login.user_name.service_center_id,service_centers_login.user_name.service_center_id', '', '1', '2016-09-30 06:08:55');

--Belal 04 Oct
INSERT INTO `247around_email_template` (`id`, `entity`, `template`, `subject`, `body`, `from`, `to`, `cc`, `bcc`, `template_values`, `attachment`, `active`, `create_date`) VALUES (NULL, 'partner', 'partner_login_details', '247Around Login Details', 'Dear Partner,<br><br> Following are the login credentials to 247Around CRM.<br><br> <b>Username : </b> %s <br> <b>Password : </b> %s <br> For any confusion, write to us or call us.<br><br> Regards,<br> 247around Team', 'booking@247around.com', '', '', '', 'partner_login.user_name.partner_id,partner_login.user_name.partner_id', '', '1', '2016-09-30 11:38:55');

-- Abhay 06 OCT
ALTER TABLE `vendor_escalation_policy` ADD `entity` VARCHAR(20) NULL DEFAULT NULL AFTER `escalation_reason`;



--Abhay 08 OCT

ALTER TABLE  `booking_state_change` ADD  `service_center_id` VARCHAR( 20 ) NULL DEFAULT NULL AFTER  `partner_id` 

--Belal 10 Oct

CREATE TABLE `query_report` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `description` varchar(500) NOT NULL,
 `query` varchar(1000) NOT NULL,
 `active` int(11) NOT NULL DEFAULT '1' COMMENT '1->Active, 0->Disabled',
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1


INSERT INTO `query_report` (`id`, `description`, `query`, `active`, `create_date`) VALUES
(1, 'Count completed booking this month.', 'SELECT COUNT(id) as count from booking_details where current_status=''Completed'' AND MONTH(closed_date) = MONTH(CURDATE())', 1, '2016-10-10 06:18:03'),
(2, 'Count completed booking this month.', 'SELECT COUNT(id) as count from booking_details where current_status=''Completed'' AND MONTH(closed_date) = MONTH(CURDATE())', 1, '2016-10-10 05:41:56'),
(3, 'Count completed booking this month.', 'SELECT COUNT(id) as count from booking_details where current_status=''Completed'' AND MONTH(closed_date) = MONTH(CURDATE())', 1, '2016-10-10 05:41:56');

-- Abhay 15OCT
ALTER TABLE `sms_sent_details` ADD `sms_tag` VARCHAR(50) NULL AFTER `booking_id`;
-- Belal 14 Oct

-- ALTER TABLE `employee` ADD `official_mail` VARCHAR(128) NOT NULL AFTER `phone`, ADD `personal_mail` VARCHAR(128) NOT NULL AFTER `official_mail`;

ALTER TABLE `employee` CHANGE `email` `official_email` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `email_personal` `personal_email` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

--Abhay 21 OCT
CREATE TABLE `assigned_engineer` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(250) NOT NULL,
  `service_center_id` int(20) DEFAULT NULL,
  `engineer_id` varchar(20) DEFAULT NULL,
  `current_state` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Indexes for table `assigned_engineer`
--
ALTER TABLE `assigned_engineer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assigned_engineer`
--
ALTER TABLE `assigned_engineer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE  `booking_state_change` ADD  `service_center_id` VARCHAR( 20 ) NULL DEFAULT NULL AFTER  `partner_id` ;
ALTER TABLE `vendor_escalation_policy` ADD `entity` VARCHAR(20) NULL DEFAULT NULL AFTER `escalation_reason`;

CREATE TABLE `penalty_details` (
  `id` int(11) NOT NULL,
  `partner_id` int(20) DEFAULT NULL,
  `escalation_id` int(20) DEFAULT NULL,
  `criteria` varchar(200) DEFAULT NULL,
  `penalty_amount` varchar(20) DEFAULT NULL,
  `unit_%_rate` int(20) DEFAULT NULL,
  `active` int(10) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Indexes for table `penalty_details`
--
ALTER TABLE `penalty_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penalty_details`
--
ALTER TABLE `penalty_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `internal_status` ADD `sf_update_active` INT NULL DEFAULT '0' AFTER `active`, ADD `method_name` VARCHAR(100) NULL DEFAULT NULL AFTER `sf_update_active`, ADD `redirect_url` VARCHAR(100) NULL DEFAULT NULL AFTER `method_name`;
ALTER TABLE `service_centres` ADD `is_update` INT NULL DEFAULT '0' AFTER `sc_code`, ADD `is_penalty` INT NULL DEFAULT '0' AFTER `is_update`;
ALTER TABLE `service_centres` ADD `penalty_activation_date` DATE NULL DEFAULT NULL AFTER `is_penalty`;

ALTER TABLE `sms_sent_details` ADD `sms_tag` VARCHAR(50) NULL AFTER `booking_id`;


CREATE TABLE `penalty_on_booking` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(100) NOT NULL,
  `service_center_id` int(20) NOT NULL,
  `criteria_id` int(20) NOT NULL,
  `penalty_amount` int(20) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Indexes for table `penalty_on_booking`
--
ALTER TABLE `penalty_on_booking`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penalty_on_booking`
--
ALTER TABLE `penalty_on_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `booking_details` ADD `count_reschedule` INT(10) NULL DEFAULT '0' AFTER `potential_value`, ADD `count_escalation` INT(10) NULL DEFAULT '0' AFTER `count_reschedule`;



CREATE TABLE `sc_crimes` (
  `id` int(11) NOT NULL,
  `service_center_id` int(11) DEFAULT NULL,
  `un_assigned_engineer` int(11) DEFAULT NULL,
  `not_update_booking` int(11) DEFAULT NULL,
  `old_crimes` int(11) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sc_crimes`
--
ALTER TABLE `sc_crimes`
  ADD PRIMARY KEY (`id`);



-- ALTER TABLE `employee` ADD `official_mail` VARCHAR(128) NOT NULL AFTER `phone`, ADD `personal_mail` VARCHAR(128) NOT NULL AFTER `official_mail`;

ALTER TABLE `employee` CHANGE `email` `official_email` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `email_personal` `personal_email` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `booking_unit_details` ADD `update_date` DATETIME on update CURRENT_TIMESTAMP NULL AFTER `create_date`;
-- Belal 12 November

ALTER TABLE `service_centres` ADD `name_on_pan` VARCHAR(512) NOT NULL AFTER `service_tax_no`, ADD `pan_no` VARCHAR(256) NOT NULL AFTER `name_on_pan`, ADD `vat_cst_no` VARCHAR(256) NOT NULL AFTER `pan_no`, ADD `pan_file` VARCHAR(512) NOT NULL AFTER `vat_cst_no`, ADD `vat_cst_file` VARCHAR(512) NOT NULL AFTER `pan_file`, ADD `service_tax_file` VARCHAR(512) NOT NULL AFTER `vat_cst_file`, ADD `account_type` VARCHAR(256) NOT NULL AFTER `service_tax_file`;

ALTER TABLE `service_centres` CHANGE `vat_cst_no` `vat_no` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `service_centres` CHANGE `vat_cst_file` `vat_file` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `service_centres` ADD `cst_no` VARCHAR(256) NOT NULL AFTER `vat_file`, ADD `cst_file` VARCHAR(512) NOT NULL AFTER `cst_no`, ADD `tin_no` VARCHAR(256) NOT NULL AFTER `cst_file`, ADD `tin_file` VARCHAR(512) NOT NULL AFTER `tin_no`;

ALTER TABLE `service_centres` ADD `address_proof_file` VARCHAR(512) NOT NULL AFTER `service_tax_file`;

ALTER TABLE `service_centres` ADD `company_type` VARCHAR(512) NOT NULL AFTER `account_type`, ADD `id_proof_1_file` VARCHAR(512) NOT NULL AFTER `company_type`, ADD `id_proof_2_file` VARCHAR(512) NOT NULL AFTER `id_proof_1_file`, ADD `contract_file` VARCHAR(512) NOT NULL AFTER `id_proof_2_file`, ADD `cancelled_cheque_file` VARCHAR(512) NOT NULL AFTER `contract_file`;



<!-- Abhay 14 NOV -->
ALTER TABLE `service_centres` ADD `is_vat_doc` INT(2) NOT NULL DEFAULT '1' AFTER `penalty_activation_date`, ADD `is_st_doc` INT(2) NOT NULL DEFAULT '1' AFTER `is_vat_doc`;
ALTER TABLE `service_centres` ADD `is_pan_doc` INT(2) NOT NULL DEFAULT '1' AFTER `is_st_doc`, ADD `is_cst_doc` INT(2) NOT NULL DEFAULT '1' AFTER `is_pan_doc`;

-- Belal 14 November

CREATE TABLE `brackets` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `order_id` varchar(32) NOT NULL,
 `order_received_from` int(32) NOT NULL,
 `order_given_to` int(32) NOT NULL,
 `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `shipment_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `received_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `19_24_requested` int(32) NOT NULL,
 `26_32_requested` int(32) NOT NULL,
 `36_42_requested` int(32) NOT NULL,
 `total_requested` int(64) NOT NULL,
 `19_24_shipped` int(32) NOT NULL,
 `26_32_shipped` int(32) NOT NULL,
 `36_42_shipped` int(32) NOT NULL,
 `total_shipped` int(64) NOT NULL,
 `19_24_received` int(32) NOT NULL,
 `26_32_received` int(32) NOT NULL,
 `36_42_received` int(32) NOT NULL,
 `total_received` int(64) NOT NULL,
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `brackets` ADD `is_shipped` INT(2) NULL DEFAULT '0' COMMENT '1->Shipped, 0->Unshipped' AFTER `total_received`, ADD `is_received` INT(2) NOT NULL DEFAULT '0' COMMENT '1->received, 0->Not Received' AFTER `is_shipped`;
ALTER TABLE `brackets` ADD `shipment_receipt` VARCHAR(256) NOT NULL AFTER `36_42_shipped`;

-- Belal 22 Oct --
CREATE TABLE `inventory` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `vendor_id` varchar(256) NOT NULL,
 `order_id` varchar(256) NOT NULL,
 `19_24_current_count` varchar(256) NOT NULL,
 `26_32_current_count` varchar(256) NOT NULL,
 `36_42_current_count` varchar(256) NOT NULL,
 `remarks` varchar(256) NOT NULL COMMENT 'Requested, Shipped, Received',
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `service_centres` ADD `brackets_flag` INT(2) NOT NULL DEFAULT '0' COMMENT '1->Taking Brackets, 0->Not taking Brackets' AFTER `sc_code`;

ALTER TABLE `inventory` CHANGE `order_id` `order_booking_id` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `inventory` ADD `increment/decrement` INT NOT NULL COMMENT '1->increment,0->Decrement' AFTER `36_42_current_count`;

ALTER TABLE `vendor_partner_invoices` ADD `order_id` VARCHAR(512) NOT NULL AFTER `invoice_id`;

-- Belal 15 Nov

ALTER TABLE `brackets` CHANGE `order_date` `order_date` TIMESTAMP NULL DEFAULT NULL;

--Abhay 17 Nov
ALTER TABLE  `service_centres` CHANGE  `service_tax_no`  `service_tax_no` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE  `vat_no`  `vat_no` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;

-- Belal 18 Nov

ALTER TABLE `service_centres` ADD `company_name` VARCHAR(512) NOT NULL AFTER `name`;


--Abhay 20 NOV

ALTER TABLE `booking_unit_details` ADD `update_date` DATETIME on update CURRENT_TIMESTAMP NULL AFTER `create_date`;
ALTER TABLE `booking_unit_details` ADD `ud_closed_date` DATETIME NULL DEFAULT NULL AFTER `update_date`;


--Abhay 23 NOv
ALTER TABLE `service_centre_charges` ADD `vendor_basic_percentage` DECIMAL(10,3) NULL DEFAULT NULL AFTER `vendor_total`;
ALTER TABLE `booking_unit_details` ADD `vendor_basic_percentage` DECIMAL(10,3) NULL DEFAULT NULL AFTER `customer_total`;

-- Belal 23 Nov

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'brackets_requested_from_vendor', 'An order has been placed for Brackets <br><br> <strong>Order Details:</strong><br><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> <strong>Requested From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br> City: %s <br> State: %s <br> Pincode: %s <br> Phone Number: %s, %s<br><br> Please notify when you have shipped the following orders.', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com,vijaya@247around.com', '', '1', '2016-09-26 18:30:00');

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'brackets_order_received_from_vendor', '%s order has been placed sucessfully.<br><br> <strong>Order Details are:</strong><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> We will update you as soon as order is shipped.', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com,vijaya@247around.com', '', '1', '2016-09-26 18:30:00');


ALTER TABLE `service_centres` CHANGE `is_vat_doc` `is_tin_doc` INT(2) NOT NULL DEFAULT '1';

-- Belal 24 Nov

ALTER TABLE `brackets` ADD `active` INT(2) NOT NULL DEFAULT '1' COMMENT '0->Non Active, 1->Active' AFTER `is_received`;

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'cancel_brackets_order_received_from_vendor', '%s order has been Cancelled sucessfully.<br><br> <strong>Order Details are:</strong><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> Thanks Team 247Around', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com,vijaya@247around.com', '', '1', '2016-09-26 18:30:00');

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'cancel_brackets_requested_from_vendor', 'An order has been <b>Cancelled</b> for Brackets <br><br> <strong>Order Details:</strong><br><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> <strong>Requested From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br> City: %s <br> State: %s <br> Pincode: %s <br> Phone Number: %s, %s<br><br> Please <b>don''t</b> ship the following orders.', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com,vijaya@247around.com', '', '1', '2016-09-26 18:30:00');

-- Belal 25 Nov

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'brackets_shipment_mail', '<b>%s</b> shipmet is sent to you.<br><br> Please confirm when you receive the stands, if you find any mismatch in Total number of Brackets, <br> please inform us immediately along with the <b>Delivery Box Picture</b>.', 'booking@247around.com', '', 'anuj@247around.com, vijaya@247around.com', '', '1', '2016-09-26 18:30:00');

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'brackets_received_mail_vendor_order_requested_from', '<b>%s</b> brackets has been delivered to you successfully.<br><br> Thanks<br> 247Around Team', 'booking@247around.com', '', 'anuj@247around.com, vijaya@247around.com', '', '1', '2016-09-26 18:30:00');

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'brackets_received_mail_vendor_order_given_to', '<b>%s </b> brackets has been delivered successfully to <b> %s </b> <br><br> Please contact us in case of any query.<br><br> Thanks<br> 247Around Team', 'booking@247around.com', '', 'anuj@247around.com, vijaya@247around.com', '', '1', '2016-09-26 18:30:00');

ALTER TABLE `service_centres` ADD `on_off` VARCHAR(2) NOT NULL DEFAULT '1' COMMENT '1->On,0->Off' AFTER `beneficiary_name`;

ALTER TABLE `employee` ADD `groups` VARCHAR(256) NOT NULL AFTER `personal_email`;

--Abhay 28 NOV
ALTER TABLE `spare_parts_details` CHANGE `panel_pic` `serial_number_pic` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'store serial number picture';
ALTER TABLE `spare_parts_details` ADD `defective_parts_pic` VARCHAR(200) NULL DEFAULT NULL AFTER `invoice_pic`;
ALTER TABLE `booking_details` ADD `initial_booking_date` VARCHAR(100) NULL DEFAULT NULL AFTER `booking_date`;


--Abhay 30 NOV
ALTER TABLE `booking_unit_details` ADD `vendor_invoice_id` VARCHAR(100) NULL DEFAULT NULL AFTER `ud_closed_date`, ADD `partner_invoice_id` VARCHAR(100) NULL DEFAULT NULL AFTER `vendor_invoice_id`;

--Abhay 3 DEC
ALTER TABLE `booking_unit_details` ADD `pay_to_sf` INT(2) NULL DEFAULT '0' AFTER `vendor_invoice_id`;

--Abhay 5 NOv
ALTER TABLE `booking_unit_details` CHANGE `vendor_invoice_id` `vendor_cash_invoice_id` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `booking_unit_details` ADD `vendor_foc_invoice_id` VARCHAR(100) NULL DEFAULT NULL AFTER `vendor_cash_invoice_id`;

--Belal 8 Dec

CREATE TABLE `login_logout_details` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `action` int(1) NOT NULL COMMENT '1->Login, 0->Logout',
 `ip` varchar(32) NOT NULL,
 `browser` varchar(128) NOT NULL,
 `employee_name` varchar(256) NOT NULL,
 `employee_id` varchar(128) NOT NULL,
 `employee_type` varchar(128) NOT NULL,
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

---Abhay 09-12-2016
ALTER TABLE `service_centres` CHANGE `company_type` `company_type` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `service_centres` CHANGE `pan_no` `pan_no` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;


ALTER TABLE `partner_login` ADD `full_name` VARCHAR(256) NOT NULL AFTER `partner_id`;
ALTER TABLE `service_centers_login` ADD `full_name` VARCHAR(256) NOT NULL AFTER `service_center_id`;

--Abhay 12-12-2016
ALTER TABLE  `vendor_partner_invoices` ADD  `invoice_detailed_excel` VARCHAR( 100 ) NULL DEFAULT NULL AFTER  `invoice_file_excel` ;
-- Belal 24 Nov

CREATE TABLE `employee_relation` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `agent_id` int(128) NOT NULL,
 `service_centres_id` varchar(256) NOT NULL,
 `appliance_id` varchar(256) NOT NULL,
 `partner_id` varchar(256) NOT NULL,
 `active` int(2) NOT NULL DEFAULT '1' COMMENT '1->Active,0->Not Active',
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1

ALTER TABLE `employee_relation` CHANGE `service_centres_id` `service_centres_id` VARCHAR(1064) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

-- Belal 1 Dec
ALTER TABLE `employee` ADD `full_name` VARCHAR(512) NOT NULL AFTER `employee_id`;

-- Belal 3 Dec
ALTER TABLE `brackets` ADD `cancellation_reason` VARCHAR(512) NOT NULL AFTER `total_received`;


UPDATE `email_template` SET `template` = 'Dear partner your order has been placed sucessfully.<br><br> Your Order ID is : <b>%s</b> <br> <strong>Order Details are:</strong><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> We will update you as soon as order is shipped.<br><br> Regards,<br> 247Around Team' WHERE `email_template`.`id` = 11;

UPDATE `email_template` SET `template` = 'Dear Partner brackets has been delivered successfully to <b> %s </b> for the Order ID<b> %s </b> <br><br> Please contact us in case of any query.<br><br> Regards, <br> 247Around Team' WHERE `email_template`.`id` = 21;

UPDATE `email_template` SET `template` = 'Dear Partner brackets for your Order ID <b> %s </b> have been delivered to you sucessfully.<br><br> Tnakyou for placing an order with us.<br.<br> Regards,<br> 247Around Team' WHERE `email_template`.`id` = 20;

UPDATE `email_template` SET `template` = 'Dear Partner brackets for your Order ID <b> %s </b> has been shipped to you.<br><br> Please confirm when you receive the brackets.<br> If you find any mismatch in Total number of Brackets, please inform us immediately along with the <b>Delivery Box Picture</b>.<br><br> Regards,<br> 247Around Team' WHERE `email_template`.`id` = 19;

UPDATE `email_template` SET `template` = 'Dear Partner you have received a new order for brackets.<br><br> Your Order ID is : <b>%s</b> <br> <strong>Order Details:</strong><br><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> <strong>Requested From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br> City: %s <br> State: %s <br> Pincode: %s <br> Phone Number: %s, %s<br><br> Please notify when you ship the above order.<br><br> Regards,<br> 247Around Team' WHERE `email_template`.`id` = 10;

-- Belal 8 Dec
CREATE TABLE `scheduler_tasks_log` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `task_name` varchar(256) NOT NULL,
 `executed_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1

ALTER TABLE `login_logout_details` ADD `agent_string` VARCHAR(256) NOT NULL AFTER `browser`;

-- Belal 14 Dec

ALTER TABLE `login_logout_details` CHANGE `employee_name` `agent_id` INT NOT NULL;

ALTER TABLE `login_logout_details` CHANGE `employee_id` `entity_id` INT NOT NULL;

ALTER TABLE `login_logout_details` CHANGE `employee_type` `entity_type` VARCHAR(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;


-- Belal 17 Dec

ALTER TABLE `brackets` CHANGE `order_id` `order_id` INT(50) NULL DEFAULT NULL;

ALTER TABLE `brackets` ADD UNIQUE(`order_id`);

ALTER TABLE `employee` ADD `exotel_phone` VARCHAR(15) NULL DEFAULT NULL AFTER `phone`;



INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'sf_temporary_on_off', 'Dear %s,<br><br> <b> %s </b> Service Franchise has been made Temporarily <b> %s </b> <br><br> Thanks<br> 247Around Team', 'booking@247around.com', '', 'anuj@247around.com', '', '1', '2016-09-26 18:30:00');

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'sf_permanent_on_off', 'Dear %s,<br><br> <b> %s </b> Service Franchise has been made Permanent <b> %s </b> <br><br> Thanks<br> 247Around Team', 'booking@247around.com', '', 'anuj@247around.com', '', '1', '2016-09-26 18:30:00');

--Abhay 19 DEC
ALTER TABLE `spare_parts_details` ADD `defective_part_shipped` VARCHAR(100) NULL DEFAULT NULL AFTER `awb_by_partner`, 
ADD `defective_part_shipped_date` DATE NULL DEFAULT NULL AFTER `defective_part_shipped`, 
ADD `awb_by_sf` VARCHAR(100) NULL DEFAULT NULL AFTER `defective_part_shipped_date`, 
ADD `courier_name_by_sf` VARCHAR(100) NULL DEFAULT NULL AFTER `awb_by_sf`, 
ADD `remarks_defective_part` VARCHAR(200) NULL DEFAULT NULL AFTER `courier_name_by_sf`, 
ADD `defective_part_required` INT(10) NULL DEFAULT '1' AFTER `remarks_defective_part`;

ALTER TABLE `spare_parts_details` CHANGE `status` `status` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `spare_parts_details` CHANGE `remarks_defective_part` `remarks_defective_part_by_sf` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `spare_parts_details` ADD `remarks_defective_part_by_partner` VARCHAR(200) NULL DEFAULT NULL AFTER `remarks_defective_part_by_sf`;

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'sf_temporary_on_off', 'Dear %s,<br><br> <b> %s </b> Service Franchise has been made Temporarily <b> %s </b> <br><br> Thanks<br> 247Around Team', 'booking@247around.com', '', 'anuj@247around.com', '', '1', '2016-09-26 18:30:00');


INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'sf_permanent_on_off', 'Dear %s,<br><br> <b> %s </b> Service Franchise has been made Permanent <b> %s </b> <br><br> Thanks<br> 247Around Team', 'booking@247around.com', '', 'anuj@247around.com', '', '1', '2016-09-26 18:30:00');

-- Belal 19 Dec

CREATE TABLE `partner_operation_region` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `partner_id` int(11) NOT NULL,
 `service_id` int(11) NOT NULL,
 `state` varchar(256) NOT NULL,
 `active` int(2) NOT NULL DEFAULT '1' COMMENT '1->active region, 0->Not active region',
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `partner_service_brand_relation` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `partner_id` int(11) DEFAULT NULL,
 `service_id` int(11) DEFAULT NULL,
 `brand_name` varchar(256) DEFAULT NULL,
 `active` int(2) NOT NULL DEFAULT '1',
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `247around-aws`.`partner_operation_region` ADD INDEX `Partner_id` (`partner_id`);
ALTER TABLE `247around-aws`.`partner_service_brand_relation` ADD INDEX `Partner_id` (`partner_id`);

UPDATE `email_template` SET `template` = 'Dear Partner brackets for your Order ID <b> %s </b> have been delivered to you sucessfully.<br><br> Thnakyou for placing an order with us.<br.<br> Regards,<br> 247Around Team' WHERE `email_template`.`tag` = 'brackets_received_mail_vendor_order_requested_from';

RENAME TABLE `partner_service_brand_relation` TO `partner_appliance_details`;

UPDATE `email_template` SET `template` = 'An order has been <b>Cancelled</b> for Brackets of <strong>Order ID : %s </strong> <br><br><strong>Reason : </strong> %s <br><br> <strong>Order Details:</strong><br><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> <strong>Requested From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br> City: %s <br> State: %s <br> Pincode: %s <br> Phone Number: %s, %s<br><br> Please <b>don''t</b> ship the following orders.' WHERE `email_template`.`tag` = 'cancel_brackets_requested_from_vendor';

UPDATE `email_template` SET `template` = '%s order has been Cancelled sucessfully for the <strong>Order ID : %s </strong><br><br><strong>Reason : </strong> %s <br><br> <strong>Order Details are:</strong><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> Thanks Team 247Around' WHERE `email_template`.`tag` = 'cancel_brackets_order_received_from_vendor';


--Abhay 26 DEC

ALTER TABLE `spare_parts_details` ADD `approved_defective_parts_by_partner` INT(2) NULL DEFAULT '0' AFTER `defective_part_required`;

-- Belal 24 Dec

UPDATE `partner_appliance_details` SET `category` = 'TV-LED';

ALTER TABLE `partners` ADD `contract_file` VARCHAR(256) NOT NULL AFTER `service_tax`;

--Belal 27 Dec
INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'un-cancel_brackets_order_received_from_vendor', '%s order has been Un-Cancelled sucessfully for the <strong>Order ID : %s </strong><br><br> <strong>Order Details are:</strong><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> Thanks Team 247Around', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com,vijaya@247around.com', '', '1', '2016-09-26 18:30:00');

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'un-cancel_brackets_requested_from_vendor', 'An order has been <b>Un-Cancelled</b> for Brackets of <strong>Order ID : %s </strong><br><br> <strong>Order Details:</strong><br><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Total Requested : %s<br><br> <strong>Requested From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br> Phone Number: %s, %s<br><br> Please <b>ship</b> the following orders.', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com,vijaya@247around.com', '', '1', '2016-09-26 18:30:00');


----Abhay 27 Dec
ALTER TABLE  `agent_outbound_call_log` ADD  `call_duration` INT( 50 ) NULL DEFAULT NULL ;


ALTER TABLE `partners` ADD `upcountry` INT(2) NULL DEFAULT '0' AFTER `is_reporting_mail`;

---Abhay 28 DEC
ALTER TABLE `booking_details` DROP `discount_coupon`, DROP `discount_amount`, 
DROP `appliance_brand`, DROP `appliance_category`, DROP `appliance_capacity`, 
DROP `items_selected`, DROP `appliance_tags`, DROP `service_charge`, 
DROP `service_charge_collected_by`, DROP `additional_service_charge`,
 DROP `additional_service_charge_collected_by`, DROP `parts_cost`, 
DROP `parts_cost_collected_by`, DROP `payment_method`, 
DROP `payment_txn_id`;


ALTER TABLE `booking_details` ADD `is_upcountry` INT NULL DEFAULT '0' 
AFTER `count_escalation`, ADD `upcountry_pincode` INT(20) NULL DEFAULT NULL AFTER `is_upcountry`, 
ADD `sub_vendor_id` INT(11) NULL DEFAULT NULL AFTER `upcountry_pincode`, 
ADD `upcountry_rate` INT(11) NULL DEFAULT NULL AFTER `sub_vendor_id`, 
ADD `upcountry_distance` INT(11) NULL DEFAULT NULL AFTER `upcountry_rate`;

ALTER TABLE `booking_details` ADD `upcountry_price` INT(11) NULL DEFAULT NULL AFTER `upcountry_distance`, 
ADD `all_upcountry_pincode_details` TEXT NULL DEFAULT NULL AFTER `upcountry_price`;

ALTER TABLE `bank_transactions` CHANGE `credit_amount` `credit_amount` DECIMAL(10,2) NOT NULL, CHANGE `debit_amount` `debit_amount` DECIMAL(10,2) NOT NULL;

-- Belal 28 Dec

CREATE TABLE `partner_missed_calls` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `phone` varchar(20) DEFAULT NULL,
 `counter` int(11) NOT NULL DEFAULT '0',
 `status` varchar(20) NOT NULL DEFAULT 'FollowUp',
 `updation_reason` varchar(512) DEFAULT NULL,
 `cancellation_reason` varchar(512) DEFAULT NULL,
 `action_date` datetime DEFAULT NULL,
 `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `create_date` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'partner_missed_call_welcome_sms', 'Yippie! Your request for Installation and Repair services has been Acknowledged by 247around Team. Like us on Facebook goo.gl/Y4L6Hj. For any issues & feedback, call @ 9555000247.', NULL, '1', '2016-07-22 14:46:17');

--Belal 29 Dec

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Mobile no invalid / not in use', 'missed_cancellation', '1');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer gave missed call by mistake', 'missed_cancellation', '1');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Installation not Required', 'missed_cancellation', '1');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer not reachable', 'missed_cancellation', '1');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer Not Picking Call', 'missed_updation', '1');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer asked to call after 1 day', 'missed_updation', '1');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer asked to call after 2 day', 'missed_updation', '1');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer asked to call after 3 day', 'missed_updation', '1');


ALTER TABLE `partners` ADD `is_upcountry` INT(2) NULL DEFAULT '0' AFTER `is_active`;

--Abhay
ALTER TABLE `vendor_partner_invoices` ADD `upcountry_booking` INT(11) NOT NULL DEFAULT '0' AFTER `due_date`, ADD `upcountry_rate` DOUBLE(10,2) NOT NULL DEFAULT '0' AFTER `upcountry_booking`, ADD `upcountry_service_tax` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `upcountry_rate`, ADD `upcountry_distance` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `upcountry_service_tax`;
ALTER TABLE `vendor_partner_invoices` ADD `upcountry_price` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `upcountry_distance`;

ALTER TABLE `service_centres` CHANGE `on_off` `on_off` INT(2) NOT NULL DEFAULT '1' COMMENT '1->On,0->Off. Used to disable vendors temporarily in case of pending bookings cross a threhold';
ALTER TABLE `service_centres` CHANGE `active` `active` INT(2) NULL DEFAULT '1';

-- Sachin 4 Jan 2017

ALTER TABLE  `service_centres` ADD `is_verified` int(2) NOT NULL DEFAULT '0' AFTER `is_cst_doc`;


-- =========================  SERVER DB UPDATED, ADD YOUR CHANGES BELOW THIS LINE  ========================= --


-- Belal 5 Jan

ALTER TABLE `partners` ADD `owner_alternate_email` VARCHAR(50) NOT NULL AFTER `owner_email`;

ALTER TABLE `partners` ADD `pan_file` VARCHAR(512) NOT NULL AFTER `contract_file`, ADD `registration_no` VARCHAR(50) NOT NULL AFTER `pan_file`, ADD `registration_file` VARCHAR(512) NOT NULL AFTER `registration_no`;


-- Belal 6 Jan

ALTER TABLE `partners` ADD `upcountry_rate` INT(11) NOT NULL AFTER `is_upcountry`;

--Belal 9 Jan

--Abhay
ALTER TABLE `vendor_partner_invoices` ADD `upcountry_booking` INT(11) NOT NULL DEFAULT '0' AFTER `due_date`, ADD `upcountry_rate` DOUBLE(10,2) NOT NULL DEFAULT '0' AFTER `upcountry_booking`, ADD `upcountry_service_tax` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `upcountry_rate`, ADD `upcountry_distance` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `upcountry_service_tax`;
ALTER TABLE `vendor_partner_invoices` ADD `upcountry_price` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `upcountry_distance`;


CREATE TABLE `file_uploads` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `file_name` varchar(512) DEFAULT NULL,
 `file_type` varchar(256) DEFAULT NULL,
 `tag` varchar(128) DEFAULT NULL,
 `agent_id` varchar(128) DEFAULT NULL,
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

CREATE TABLE `sf_snapshot` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `sc_id` int(11) DEFAULT NULL,
 `yesterday_booked` int(11) DEFAULT NULL,
 `yesterday_completed` int(11) DEFAULT NULL,
 `yesterday_cancelled` int(11) DEFAULT NULL,
 `month_completed` int(11) DEFAULT NULL,
 `month_cancelled` int(11) DEFAULT NULL,
 `last_2_day` int(11) DEFAULT NULL,
 `last_3_day` int(11) DEFAULT NULL,
 `greater_than_5_days` int(11) DEFAULT NULL,
 `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Abhay 10 Jan 
ALTER TABLE `booking_details` ADD `sms_count` INT(5) NULL DEFAULT '0' AFTER `count_reschedule`;

ALTER TABLE  `service_centres` ADD `is_verified` int(2) NOT NULL DEFAULT '0' AFTER `is_cst_doc`;


ALTER TABLE `service_centres` ADD `is_upcountry` INT(2) NULL DEFAULT '0' AFTER `is_penalty`;

--Belal 12 Jan

ALTER TABLE `file_uploads` DROP `tag`;

ALTER TABLE `partners` DROP `upcountry`;
--- sachin 12 jan

CREATE TABLE `scheduler_tasks_status` (
  `id` int(11) NOT NULL,
  `job_name` varchar(55) NOT NULL,
  `agent_name` varchar(55) NOT NULL,
  `file_link` varchar(200) DEFAULT NULL,
  `processing_type` varchar(55) DEFAULT NULL,
  `from_date` datetime DEFAULT NULL,
  `to_date` datetime DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `scheduler_tasks_status`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `scheduler_tasks_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;



--
-- Table structure for table `sub_service_center_details`
--

CREATE TABLE `sub_service_center_details` (
  `id` int(11) NOT NULL,
  `service_center_id` int(11) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `district` varchar(150) DEFAULT NULL,
  `pincode` int(50) DEFAULT NULL,
  `upcountry_rate` int(10) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sub_service_center_details`
--
ALTER TABLE `sub_service_center_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sub_service_center_details`
--
ALTER TABLE `sub_service_center_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- Abhay 14 Jan ---
ALTER TABLE `bookings_sources` ADD `partner_type` VARCHAR(50) NULL DEFAULT NULL AFTER `partner_id`;

--- Sachin 19 Dec --- 

ALTER TABLE `booking_details` ADD `partner_current_status` VARCHAR(128) NULL AFTER `internal_status`;

ALTER TABLE `booking_details` ADD `partner_internal_status` VARCHAR(128) NULL AFTER `partner_current_status`;


CREATE TABLE `partner_booking_status_mapping` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `247around_current_status` varchar(128) DEFAULT NULL,
  `247around_internal_status` varchar(128) DEFAULT NULL,
  `partner_current_status` varchar(128) DEFAULT NULL,
  `partner_internal_status` varchar(128) DEFAULT NULL,
   PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `partner_booking_status_mapping` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

-- Abhay 16 Jan 
ALTER TABLE `service_centre_charges` ADD `brand` VARCHAR(150) NULL DEFAULT NULL AFTER `category`;



-- Abhay 18 Jan
ALTER TABLE `partners` ADD `upcountry_max_distance_threshold` INT(10) NULL DEFAULT NULL AFTER `upcountry_rate`, ADD `upcountry_min_distance_threshold` INT(10) NULL DEFAULT NULL AFTER `upcountry_max_distance_threshold`;

--Belal 18 Jan

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'new_vendor_creation', 'Dear Partner,<br><br>
247around welcomes you to its Partner Network, we hope to have a long lasting relationship with you.<br><br>
As informed earlier, serial number of appliance is mandatory when you close a booking. All bookings without serial numbers will be cancelled.<br><br> 
Engineer has to note the serial number when installation is done. In case serial number is not found on the appliance, he needs to bring one of the following proofs:<br><br> 
1st Option : Serial Number Of Appliance<br><br>
2nd Option : Invoice Number Of The Appliance<br><br>
3rd Option : Customer ID Card Number - PAN / Aadhar / Driving License etc.<br><br>
No completion will be allowed without any one of the above. For any confusion, write to us or call us.<br><br><br>
Regards,<br>
247around Team', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com', '', '1', '2016-09-26 18:30:00');

-- Belal 21 Jan

UPDATE `email_template` SET `template` = 'Dear Partner brackets for your Order ID <b> %s </b> have been delivered to you sucessfully.<br><br> Thankyou for placing an order with us.<br.<br> Regards,<br> 247Around Team' WHERE `email_template`.`tag` = 'brackets_received_mail_vendor_order_requested_from
';


--Abhay 23 Jan
ALTER TABLE `partners` CHANGE `upcountry_max_distance_threshold` `upcountry_max_distance_threshold` DECIMAL(10,2) NULL DEFAULT NULL, CHANGE `upcountry_min_distance_threshold` `upcountry_min_distance_threshold` DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE `partners` ADD `upcountry_mid_distance_threshold` DECIMAL(10,2) NULL DEFAULT NULL AFTER `upcountry_min_distance_threshold`;
ALTER TABLE `partners` ADD `upcountry_rate1` INT(10) NULL DEFAULT NULL AFTER `upcountry_rate`;

ALTER TABLE `booking_details` ADD `partner_upcountry_rate` INT(10) NULL DEFAULT NULL AFTER `upcountry_rate`;
ALTER TABLE `booking_details` CHANGE `upcountry_rate` `sf_upcountry_rate` INT(11) NULL DEFAULT NULL;
ALTER TABLE `partners` ADD `upcountry_approval` INT(2) NULL DEFAULT '1' AFTER `upcountry_mid_distance_threshold`;
ALTER TABLE `partners` ADD `upcountry_approval_email` VARCHAR(256) NULL DEFAULT NULL AFTER `upcountry_approval`;
ALTER TABLE `booking_details` ADD `upcountry_partner_approved` INT(2) NULL DEFAULT '1' AFTER `upcountry_distance`;
ALTER TABLE `booking_details` ADD `upcountry_paid_by_customer` INT(2) NULL DEFAULT '0' AFTER `upcountry_partner_approved`;
ALTER TABLE `booking_details` ADD `customer_paid_upcountry_charges` DECIMAL(10,2) NULL DEFAULT '0' AFTER `upcountry_paid_by_customer`;
ALTER TABLE `service_center_booking_action` ADD `upcountry_charges` DECIMAL(10,2) NULL DEFAULT '0' AFTER `parts_cost`;

--- Sachin 24JAN

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES
(1, 1, 'Cancelled', 'Cancelled', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(2, 1, 'FollowUp', 'Callback Scheduled', 'DEFERRED_BY_CUSTOMER', 'Pending'),
(3, 1, 'FollowUp', 'Customer Not Reachable', 'CUSTOMER_NOT_AVAILABLE', 'Pending'),
(4, 1, 'Completed', 'Completed', 'SERVICE_DELIVERED', 'Completed'),
(5, 1, 'Rescheduled', 'Rescheduled', 'SERVICE_RESCHEDULED', 'RESCHEDULED'),
(6, 1, 'FollowUp', 'Missed_call_not_confirmed', 'PENDING', 'Pending'),
(7, 1, 'Pending', 'Scheduled', 'SERVICE_SCHEDULED', 'Pending'),
(8, 1, 'FollowUp', 'FollowUp', 'PENDING', 'Pending'),
(9, 1, 'FollowUp', 'Missed_call_confirmed', 'SERVICE_SCHEDULED', 'Pending'),
(10, 1, 'Cancelled', 'Cancelled by Snapdeal', NULL, NULL),
(11, 247001, 'Cancelled', 'Cancelled', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(12, 247001, 'FollowUp', 'Callback Scheduled', 'DEFERRED_BY_CUSTOMER', 'Pending'),
(13, 247001, 'FollowUp', 'Customer Not Reachable', 'CUSTOMER_NOT_AVAILABLE', 'Pending'),
(14, 247001, 'Completed', 'Completed', 'SERVICE_DELIVERED', 'Completed'),
(15, 247001, 'Rescheduled', 'Rescheduled', 'SERVICE_RESCHEDULED', 'RESCHEDULED'),
(16, 247001, 'FollowUp', 'Missed_call_not_confirmed', 'PENDING', 'Pending'),
(17, 247001, 'Pending', 'Scheduled', 'SERVICE_SCHEDULED', 'Pending'),
(18, 247001, 'FollowUp', 'FollowUp', 'PENDING', 'Pending'),
(19, 247001, 'FollowUp', 'Missed_call_confirmed', 'SERVICE_SCHEDULED', 'Pending'),
(20, 247001, 'Cancelled', 'Cancelled by Snapdeal', NULL, NULL),
(21, 1, 'Cancelled', 'Already Installed', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(22, 1, 'Cancelled', 'Product To Be Returned', 'PARENT_PRODUCT_FAULTY', 'Cancelled'),
(23, 1, 'Cancelled', 'Customer Not Reachable', 'CUSTOMER_NOT_AVAILABLE', 'Cancelled'),
(24, 247001, 'Cancelled', 'Customer Not Reachable', 'CUSTOMER_NOT_AVAILABLE', 'Cancelled'),
(25, 1, 'Cancelled', 'Installation Not Required', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(26, 247001, 'Cancelled', 'Installation Not Required', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(27, 1, 'Cancelled', 'Denied By Vendor', 'DENIED_BY_VENDOR', 'Cancelled'),
(28, 247001, 'Cancelled', 'Denied By Vendor', 'DENIED_BY_VENDOR', 'Cancelled'),
(29, 1, 'Cancelled', 'Duplicate Booking (STS)', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(30, 247001, 'Cancelled', 'Duplicate Booking (STS)', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(31, 1, 'Cancelled', 'Order Cancelled', 'PARENT_PRODUCT_FAULTY', 'Cancelled'),
(32, 247001, 'Cancelled', 'Order Cancelled', 'PARENT_PRODUCT_FAULTY', 'Cancelled'),
(33, 1, 'Cancelled', 'Product to be Gifted', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(34, 247001, 'Cancelled', 'Product to be Gifted', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(35, 1, 'Cancelled', 'Your problem is resolved.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(36, 247001, 'Cancelled', 'Your problem is resolved.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(37, 1, 'Cancelled', 'You entered a wrong booking.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(38, 247001, 'Cancelled', 'You entered a wrong booking.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(39, 1, 'Cancelled', 'You found a better option for this job. ', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(40, 247001, 'Cancelled', 'You found a better option for this job. ', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(41, 1, 'Cancelled', 'You will not be available at this time.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(42, 247001, 'Cancelled', 'You will not be available at this time.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(43, 1, 'Cancelled', 'You believe someone else did this booking.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(44, 247001, 'Cancelled', 'You believe someone else did this booking.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(45, 1, 'Cancelled', 'Customer is not reachable.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(46, 247001, 'Cancelled', 'Customer is not reachable.', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(47, 247001, 'Cancelled', 'Already Installed', 'REFUSED_BY_CUSTOMER', 'Cancelled'),
(48, 247001, 'Cancelled', 'Product To Be Returned', 'PARENT_PRODUCT_FAULTY', 'Cancelled');






-- Sachin 1 Feb

ALTER TABLE `spare_parts_details` ADD `courier_charges_by_sf` DECIMAL(10,2) NULL DEFAULT '0' AFTER `awb_by_sf`;

ALTER TABLE `inventory` ADD `43_current_count` VARCHAR(256)  NOT NULL DEFAULT '0' AFTER `36_42_current_count`;

ALTER TABLE `brackets` ADD `43_requested` INT(32) NOT NULL AFTER `36_42_requested`;

ALTER TABLE `brackets` ADD `43_shipped` INT(32) NOT NULL AFTER `36_42_shipped`;

ALTER TABLE `brackets` ADD `43_received` INT(32) NOT NULL AFTER `36_42_received`;
--Belal 24 Jan

INSERT INTO `email_template` (`id`, `tag`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'penalty_on_booking', '<br>Booking Report has been created for the following booking id : <strong> %s </strong> <br><br> For any confusion, write to us or call us.<br><br> Regards,<br> 247around Team', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com', '', '1', '2016-09-26 18:30:00');

UPDATE `email_template` SET `template` = '<br>Booking Report has been created for the following booking id : <strong> %s </strong> <br> Reason : <strong> %s </strong> <br><br> For any confusion, write to us or call us.<br><br> Regards,<br> 247around Team' WHERE `email_template`.`tag` = 'penalty_on_booking';


INSERT INTO `vendor_escalation_policy` (`id`, `escalation_reason`, `entity`, `mail_to_owner`, `mail_to_poc`, `sms_to_owner`, `sms_to_poc`, `sms_body`, `mail_subject`, `mail_body`, `active`, `create_date`) VALUES (NULL, 'Incentive Cut - Reschedule without reason', '247around', '0', '0', '0', '0', NULL, NULL, NULL, '1', '2016-04-11 07:58:00');
INSERT INTO `vendor_escalation_policy` (`id`, `escalation_reason`, `entity`, `mail_to_owner`, `mail_to_poc`, `sms_to_owner`, `sms_to_poc`, `sms_body`, `mail_subject`, `mail_body`, `active`, `create_date`) VALUES (NULL, 'Penalty - Fake Cancel', '247around', '0', '0', '0', '0', NULL, NULL, NULL, '1', '2016-04-11 07:58:00');
INSERT INTO `vendor_escalation_policy` (`id`, `escalation_reason`, `entity`, `mail_to_owner`, `mail_to_poc`, `sms_to_owner`, `sms_to_poc`, `sms_body`, `mail_subject`, `mail_body`, `active`, `create_date`) VALUES (NULL, 'Penalty - Fake Complete', '247around', '0', '0', '0', '0', NULL, NULL, NULL, '1', '2016-04-11 07:58:00');

INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, '12', NULL, '50', NULL, '1');
INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, '13', NULL, '300', NULL, '1');
INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, '14', NULL, '100', NULL, '1');

ALTER TABLE `vendor_escalation_policy` ADD `process_type` VARCHAR(32) NULL DEFAULT NULL AFTER `entity`;
ALTER TABLE `vendor_escalation_policy` CHANGE `process_type` `process_type` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'escalations, report types';
UPDATE `vendor_escalation_policy` SET `process_type`= 'report' WHERE id IN(12,13,14);
UPDATE `vendor_escalation_policy` SET `process_type` = 'escalation' WHERE `entity` = '247around' AND `process_type` IS NULL

UPDATE `penalty_details` SET `penalty_amount` = '10' WHERE `penalty_details`.`id` = 3;

ALTER TABLE `partner_login` ADD `email` VARCHAR(32) NULL DEFAULT NULL AFTER `partner_id`;

UPDATE `vendor_escalation_policy` SET `process_type` = 'report_complete' WHERE `vendor_escalation_policy`.`id` = 14;
UPDATE `vendor_escalation_policy` SET `process_type` = 'report_cancel' WHERE `vendor_escalation_policy`.`id` = 13;
UPDATE `vendor_escalation_policy` SET `process_type` = 'report_cancel' WHERE `vendor_escalation_policy`.`id` = 12;

UPDATE `vendor_escalation_policy` SET `escalation_reason` = 'Penalty - Fake Complete - Customer want Installation' WHERE `vendor_escalation_policy`.`id` = 14;

INSERT INTO `vendor_escalation_policy` (`id`, `escalation_reason`, `entity`, `process_type`, `mail_to_owner`, `mail_to_poc`, `sms_to_owner`, `sms_to_poc`, `sms_body`, `mail_subject`, `mail_body`, `active`, `create_date`) VALUES (NULL, 'Penalty - Fake Complete - Customer NOT want Installation', '247around', 'report_complete', '0', '0', '0', '0', NULL, NULL, NULL, '1', '2016-04-11 07:58:00');
INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, '15', NULL, '300', NULL, '1');

ALTER TABLE `vendor_escalation_policy`
  DROP `mail_to_owner`,
  DROP `mail_to_poc`,
  DROP `mail_subject`,
  DROP `mail_body`;

ALTER TABLE `penalty_on_booking` ADD `agent_id` INT NULL AFTER `penalty_amount`, ADD `remarks` VARCHAR(512) NULL AFTER `agent_id`, ADD `current_state` VARCHAR(128) NULL AFTER `remarks`;
ALTER TABLE `email_template` ADD `subject` VARCHAR(512) NULL AFTER `tag`;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'escalation_on_booking', 'Booking ID : %s Escalated', '<br>Dear SF,<br> Booking ID : <strong>%s</strong> is escalated <b>%s</b> times. <br> Reason : %s <br> Attend this booking immediately. <br><br> Regards,<br> 247around Team', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com', '', '1', '2016-09-26 18:30:00');
UPDATE `email_template` SET `template` = '<br>Dear SF,<br> Penalty of Rs: <b>%s</b> is leived on Booking ID : <strong>%s</strong> <br> Reason : %s <br> Try to avoid such cases in future. <br><br> Regards,<br> 247around Team' WHERE `email_template`.`tag` = 'penalty_on_booking';
UPDATE `email_template` SET `subject` = 'Penalty of Rs : %s on Booking ID : %s' WHERE `email_template`.`tag` = 'penalty_on_booking';

ALTER TABLE `penalty_on_booking` ADD `active` INT(2) NOT NULL DEFAULT '1' COMMENT '1->Penalty to be Taken, 0->Penalty Not Taken' AFTER `current_state`;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'remove_penalty_on_booking', 'Penalty Removed on Booking ID : %s', '<br>Dear SF,<br> Penalty has been <b>Removed</b> from Booking ID : <strong>%s</strong> <br> <br><br> Regards,<br> 247around Team', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com', '', '1', '2016-09-26 18:30:00');

--Belal 2 Feb

UPDATE `sms_template` SET `template` = 'Give missed call after delivery for %s Installation %s. Installation Charges %s. Installation by 247around, Snapdeal Partner' WHERE `sms_template`.`tag` = 'sd_delivered_missed_call_initial';

UPDATE `sms_template` SET `template` = 'Give missed call after delivery for %s Installation %s. Installation Charges %s. Installation by 247around, Snapdeal Partner' WHERE `sms_template`.`tag` = 'sd_shipped_missed_call_initial';


--- Abhay --
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'home_theater_repair', 'Thank you, your %s Service is confirmed. Please contact %s for your service center visit. Address %s. 9555000247, 247around', '', '1', CURRENT_TIMESTAMP);
-

CREATE TABLE `distance_between_pincode` (
  `id` int(11) NOT NULL,
  `pincode1` int(15) NOT NULL,
  `pincode2` int(15) NOT NULL,
  `distance` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `distance_between_pincode`
--
ALTER TABLE `distance_between_pincode`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `distance_between_pincode`
--
ALTER TABLE `distance_between_pincode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- Belal 6 Feb

ALTER TABLE `penalty_on_booking` ADD `penalty_remove_reason` VARCHAR(500) NULL AFTER `active`;

ALTER TABLE `penalty_on_booking` ADD `penalty_remove_agent_id` INT NULL AFTER `penalty_remove_reason`, ADD `penalty_remove_date` TIMESTAMP NULL AFTER `penalty_remove_agent_id`;


--Abhay 10 Feb
ALTER TABLE `vendor_partner_invoices` ADD `penalty_amount` DECIMAL(10,2) NULL DEFAULT NULL AFTER `upcountry_price`;
ALTER TABLE `penalty_on_booking` ADD `foc_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `create_date`;


-- Sachin 13 Feb

CREATE TABLE `agent_daily_report_stats` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(128) NOT NULL,
  `followup_to_cancel` varchar(11) NOT NULL,
  `followup_to_pending` varchar(11) NOT NULL,
  `calls_placed` varchar(11) NOT NULL,
  `calls_recevied` varchar(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `agent_daily_report_stats`
  ADD PRIMARY KEY (`id`)

ALTER TABLE `agent_daily_report_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `partners` ADD `company_type` VARCHAR(512) NULL DEFAULT NULL AFTER `type`;

ALTER TABLE `partners` CHANGE `company_address` `company_address` VARCHAR(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `partners` CHANGE `address` `address` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `partners` CHANGE `registration_number` `registration_number` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `partners` CHANGE `partner_code` `partner_code` INT(11) NULL DEFAULT NULL COMMENT 'This is the Partner ID which is used in Booking Sources table.';

ALTER TABLE `partners` CHANGE `landmark` `landmark` VARCHAR(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `partners` CHANGE `contract_file` `contract_file` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `partners` CHANGE `pan_file` `pan_file` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `partners` CHANGE `registration_file` `registration_file` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `partners` CHANGE `registration_no` `registration_no` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `partners` CHANGE `upcountry_rate` `upcountry_rate` INT(11) NULL DEFAULT NULL;
ALTER TABLE `bookings_sources` CHANGE `partner_email_for_to` `partner_email_for_to` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `bookings_sources` CHANGE `partner_email_for_cc` `partner_email_for_cc` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

-- ANUJ 10 Feb
ALTER TABLE  `vendor_partner_invoices` ADD  `penalty_bookings_count` INT NOT NULL COMMENT  'On how many bookings penalty is imposed?' AFTER `upcountry_price` ;

------------------------- ALL CHANGES TAKEN TILL THIS POINT 14 FEB 2017 ------------------------------------

ALTER TABLE `booking_details` ADD `is_penalty` INT(2) NULL DEFAULT '0' AFTER `customer_paid_upcountry_charges`;

--sachin 17 feb

ALTER TABLE `partners` ADD `partner_type` VARCHAR(50) NULL DEFAULT NULL AFTER `type`;


--sachin 20 feb



UPDATE  `email_template` SET  `template` =  '
Dear Partner,<br>You have received a new order for brackets.<br><br>Your Order ID is : <b>%s</b> <br> <strong>Order Details:</strong><br><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Greater than 43 Inch Brackets : %s <br> Total Requested : %s<br><br> <strong>Requested From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br> City: %s <br> State: %s <br> Pincode: %s <br> Phone Number: %s, %s<br><br> Please notify when you ship the above order.<br><br> Regards,<br> 247Around Team
' WHERE  `email_template`.`tag` ='brackets_requested_from_vendor';

UPDATE  `email_template` SET  `template` =  '
Dear Partner,<br>Your brackets order has been placed sucessfully.<br><br>Your Order ID is: <b>%s</b> <br> <strong>Order Details:</strong><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Greater Than 43 Inch Brackets : %s <br> Total Requested : %s<br><br> We will update you as soon as the brackets are shipped.<br><br>Regards,<br>247Around Team' WHERE  `email_template`.`tag` ='brackets_order_received_from_vendor';

UPDATE  `email_template` SET  `template` =  '
%s order has been Cancelled sucessfully for the <strong>Order ID : %s </strong><br><br><strong>Reason : </strong> %s <br><br> <strong>Order Details are:</strong><br> 19 to 24 Inch Brackets : %s <br> 26 to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Greater than 43 Inch Brackets : %s <br> Total Requested : %s<br><br> Thanks Team 247Around' WHERE  `email_template`.`tag` ='cancel_brackets_order_received_from_vendor';

UPDATE  `email_template` SET  `template` = 'Brackets order <strong>%s</strong> has been Cancelled. <br><br><strong>Reason : </strong> %s <br><br> <strong>Order Details:</strong><br><br> 19 to 24 Inch Brackets : %s <br>26to 32 Inch Brackets : %s <br> 36 to 42 Inch Brackets : %s <br> Greater than 43 Inch Brackets : %s <br> Total Requested : %s<br><br> <strong>Requested From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br>City: %s <br> State: %s <br> Pincode: %s <br> Phone Number: %s, %s<br><br> Please <b>don''''t</b> ship this order.' WHERE  `email_template`.`tag` ='cancel_brackets_requested_from_vendor';

-- Abhay 21 FEB
ALTER TABLE `sc_crimes` ADD `total_pending_booking` INT(10) NULL DEFAULT NULL AFTER `total_missed_target`;

ALTER TABLE `partners` ADD `seller_code` VARCHAR(56) NULL DEFAULT NULL AFTER `public_name`;
ALTER TABLE `booking_details` ADD `district` VARCHAR(128) NULL DEFAULT NULL AFTER `city`, ADD `taluk` VARCHAR(128) NULL DEFAULT NULL AFTER `district`;


CREATE TABLE `appliance_product_description` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `capacity` varchar(50) DEFAULT NULL,
  `brand` varchar(255) NOT NULL,
  `product_description` varchar(255) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appliance_product_description`
--
ALTER TABLE `appliance_product_description`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Product` (`service_id`,`category`,`capacity`,`brand`,`product_description`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appliance_product_description`
--
ALTER TABLE `appliance_product_description`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


---- Abhay 28 FEB
ALTER TABLE `vendor_partner_invoices` ADD `courier_charges` DECIMAL(10,2) NULL DEFAULT '0' AFTER `penalty_amount`;


--Abhay 2 March
ALTER TABLE `booking_details` ADD `upcountry_partner_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `is_penalty`;


--Abhay 3 March
ALTER TABLE `vendor_partner_invoices` CHANGE `upcountry_booking` `upcountry_booking` INT(11) NULL DEFAULT '0';
ALTER TABLE `vendor_partner_invoices` CHANGE `upcountry_rate` `upcountry_rate` DOUBLE(10,2) NULL DEFAULT '0.00', 
CHANGE `upcountry_distance` `upcountry_distance` DECIMAL(10,2) NULL DEFAULT '0.00', 
CHANGE `upcountry_price` `upcountry_price` DECIMAL(10,2) NULL DEFAULT '0.00', 
CHANGE `penalty_bookings_count` `penalty_bookings_count` INT(11) NULL COMMENT 'On how many bookings penalty is imposed?';

--Abhay 23 March
ALTER TABLE `vendor_partner_invoices` ADD `credit_penalty_amount` DECIMAL(10,2) NULL DEFAULT NULL AFTER `penalty_amount`, 
ADD `credit_penalty_bookings_count` INT(10) NULL DEFAULT NULL AFTER `credit_penalty_amount`;


-- sachin 06 april
ALTER TABLE `partners` ADD `cst_no` VARCHAR(256) NOT NULL AFTER `tin`, ADD `tin_file` VARCHAR(512) NOT NULL AFTER `tin`, 
ADD `cst_file` VARCHAR(512) NOT NULL AFTER `service_tax`, ADD `service_tax_file` VARCHAR(512) NOT NULL AFTER `cst_file`;

-- sachin 07 April
CREATE TABLE `challan_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_no` int(11) NOT NULL,
  `cin_no` varchar(256) NOT NULL,
  `type` varchar(128) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bank_name` varchar(256) NOT NULL,
  `paid_by` varchar(128) NOT NULL,
  `challan_file` varchar(256) NOT NULL,
  `remarks` varchar(256) DEFAULT NULL,
  `challan_tender_date` date NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `challan_details`
  ADD PRIMARY KEY (`id`);

CREATE TABLE `invoice_challan_id_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `challan_id` int(11) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `invoice_challan_id_mapping`
  ADD PRIMARY KEY (`id`);



---Abhay 11-04--
ALTER TABLE `bank_transactions` ADD `agent_id` INT(11) NULL DEFAULT NULL AFTER `description`;

--- Sachin 15-04-2017

ALTER TABLE `invoice_challan_id_mapping` ADD `active` TINYINT(1) NULL DEFAULT '1' COMMENT '1=active,0=inactive' AFTER `invoice_id`;

--- Sachin 18-04-2017

ALTER TABLE `challan_details` ADD `annexure_file` VARCHAR(256) NOT NULL AFTER `challan_file`;

-- Abhay 19-04-2017

ALTER TABLE `booking_unit_details` ADD `pod` INT(2) NOT NULL DEFAULT '1' AFTER `pay_to_sf`;

-- Abhay 19-04-2017
ALTER TABLE `penalty_details` ADD `cap_amount` INT NOT NULL DEFAULT '0' AFTER `unit_%_rate`;


-- sachin 24-04-2017

CREATE TABLE `bank_details` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(256) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEF

ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bank_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- sachin 25-04-2017

ALTER TABLE `booking_details` ADD `support_file` VARCHAR(256) NULL AFTER `upcountry_price`;


--Abhay 25 Aprl
UPDATE `workbook2` SET `service_id` = 46 WHERE `service_name` = "Television";
UPDATE `workbook2` SET `service_id` = 37 WHERE `service_name` = "Refrigerator";
UPDATE `workbook2` SET `service_id` = 28 WHERE `service_name` = "Washing Machine";
UPDATE `workbook2` SET `service_id` = 50 WHERE `service_name` = "Air Conditioner";
UPDATE `workbook2` SET `service_id` = 42 WHERE `service_name` = "Microwave";

ALTER TABLE `booking_unit_details` ADD `sub_order_id` VARCHAR(256) NULL DEFAULT NULL AFTER `partner_id`;

-- sachin 25-04-2017

ALTER TABLE `booking_details` ADD `support_file` VARCHAR(256) NULL AFTER `upcountry_price`;

-- sachin 01-05-2017

ALTER TABLE `service_centres` CHANGE `create_date` `create_date` TIMESTAMP NOT NULL;
ALTER TABLE `service_centres` ADD `update_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `active`;


-- sachin 06-05-2017

ALTER TABLE `distance_between_pincode` ADD `agent_id` int(11) NULL AFTER `calculated_using_do`;

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) 
VALUES (NULL, 'poor_rating_on_completion', 'Hmm! You Rated Us %d. We Would Come Back With Better Experience. Book MultiBrand Appliance Installation Repair on www.247around.com or Call us at 9555000247', 'send this sms for poor rating on completed booking', '1', CURRENT_TIMESTAMP),
(NULL, 'avg_rating_on_completion', 'Hmm! You Rated Us %d. We Would Come Back With Better Experience. Book MultiBrand Appliance Installation Repair on www.247around.com or Call us at 9555000247', 'send this sms on average rating on completed booking', '1', CURRENT_TIMESTAMP),
(NULL, 'good_rating_on_completion', 'Wow! You Rated Us %d.Appreciate Your Feedback.For MultiBrand Appliance Installation/Repair Across India, call 9555000247 or book on www.247around.com', 'send this sms on good rating on completed booking', '1', CURRENT_TIMESTAMP);

-- sachin 12-05-2107

ALTER TABLE `vendor_partner_invoices` ADD `agent_id` INT( 11 ) NOT NULL DEFAULT '1' COMMENT 'Agent ID' AFTER `remarks` ;

-- sachin 15-05-2017

ALTER TABLE `partners` ADD `is_sms_allowed` VARCHAR(1) NOT NULL DEFAULT '1' COMMENT '1 = \'sms allowed\', 0 = \'sms not allowed\'' AFTER `is_verified`;

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) 
VALUES (NULL, 'completed_booking_promotional_sms_1', 'We are delighted to have served you in the past. Avail Rs.%s discount on your next appliance repair. Book on 9555000247 | goo.gl/m0iAcS | www.247around.com', 'Sms sent when booking status is completed and month is even for promotional sms', '1', CURRENT_TIMESTAMP);

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) 
VALUES (NULL, 'completed_booking_promotional_sms_2', 'We are delighted to have served you in past & added Rs.%s balance. Use it in your next appliance repair. Book on 9555000247 | goo.gl/m0iAcS | www.247around.com', 'Sms sent when booking status is completed and month is odd for promotional sms', '1', CURRENT_TIMESTAMP)




--Abhay 18 May
UPDATE `booking_details` SET partner_id = "247001", partner_source = "AndroidApp" WHERE partner_id = 247002;
UPDATE `booking_details` SET partner_id = "247001", partner_source = "CallCenter" WHERE partner_id = 247003;
UPDATE `booking_details` SET partner_source = "Website" WHERE partner_id = 247001;

UPDATE `booking_unit_details` SET partner_id = "247001" WHERE partner_id = 247002;
UPDATE `booking_unit_details` SET partner_id = "247001" WHERE partner_id = 247003;

-- sachin 19-05-2017

ALTER TABLE vendor_partner_invoices CHANGE invoice_file_excel invoice_file_main varchar(255);
ALTER TABLE vendor_partner_invoices CHANGE invoice_file_pdf invoice_file_excel varchar(255);

--Abhay 18 May
UPDATE `booking_details` SET partner_id = "247001", partner_source = "AndroidApp" WHERE partner_id = 247002
UPDATE `booking_details` SET partner_id = "247001", partner_source = "CallCenter" WHERE partner_id = 247003
UPDATE `booking_details` SET  partner_source = "Website" WHERE partner_id = 247001


-- sachin 26 May
ALTER TABLE `agent_daily_report_stats` ADD `rating` VARCHAR(11) NOT NULL AFTER `calls_recevied`;

--Abhay 27 May
--
-- Table structure for table `sf_not_exist_booking_details`
--

CREATE TABLE `sf_not_exist_booking_details` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(128) NOT NULL,
  `pincode` int(50) NOT NULL,
  `city` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sf_not_exist_booking_details`
--
ALTER TABLE `sf_not_exist_booking_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sf_not_exist_booking_details`
--
ALTER TABLE `sf_not_exist_booking_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- sachin 29 May

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'partner_invoice_detailed', '247around - %s. Invoice for period: %s to %s', 'Dear Partner, <br>

Please find attached invoice for jobs completed <br>

With Regards,
<br>247around Team
<br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
<br>Follow us on Facebook: www.facebook.com/247around
<br>Website: www.247around.com
<br>Playstore - 247around -
<br>https://play.google.com/store/apps/details?id=com.handymanapp', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'cash_details_invoices_for_vendors', '247around - %s - Cash Invoice for period: %s to %s', 'Dear Partner,<br/><br/>
Please find attached CASH invoice. 
Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice.
<br/><br/>Thanks,<br/>247around Team', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'foc_details_invoices_for_vendors', '247around - %s - FOC Invoice for period: %s to %s', 
'Dear Partner,Please find attached FOC invoice.
Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice.
<br/><br/>Thanks,<br/>247around Team', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'send_brackets_invoice_mail', 'Brackets Invoice - %s', 'Dear Partner,<br/><br/>
Please find attached invoice for Brackets delivered in %s.
Hope to have a long lasting working relationship with you.
<br><br>With Regards,
<br>247around Team<br>
<br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
<br>Follow us on Facebook: www.facebook.com/247around
<br>Website: www.247around.com
<br>Playstore - 247around -
<br>https://play.google.com/store/apps/details?id=com.handymanapp', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'send_draft_brackets_invoice_mail', 'Draft-Brackets Invoice - %s', 'Dear Partner,<br/><br/>
Please find attached invoice for Brackets delivered in %s.
Hope to have a long lasting working relationship with you.
<br><br>With Regards,
<br>247around Team<br>
<br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
<br>Follow us on Facebook: www.facebook.com/247around
<br>Website: www.247around.com
<br>Playstore - 247around -
<br>https://play.google.com/store/apps/details?id=com.handymanapp', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'crm_setup_invoice', 'PARTNER CRM SETUP INVOICE- 247around - %s Invoice for period: %s to %s', '', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'brackets_credit_note_invoice', 'Credit Note - Brackets Invoice - %s', 'Dear Partner,<br/><br/>
Please find attached invoice for Brackets delivered.
Hope to have a long lasting working relationship with you.
<br><br>With Regards,
<br>247around Team<br>
<br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015
<br>Follow us on Facebook: www.facebook.com/247around
<br>Website: www.247around.com
<br>Playstore - 247around -

<br>https://play.google.com/store/apps/details?id=com.handymanapp', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

---- ANUJ 29 May ----

ALTER TABLE  `sf_not_exist_booking_details` ADD  `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE  `sf_not_exist_booking_details` AUTO_INCREMENT =10000;

-- Abhay 31 May ---
INSERT INTO `partner_login` (`id`, `partner_id`, `full_name`, `email`, `user_name`, `password`, `clear_text`, `active`, `create_date`) VALUES
(978990, 247030, 'STS', 'anuj@247around.com', 'jeeves-sts', '216f5a89fca6bc085d2a6a3c88e6615d', 'jeeves-sts', 1, '2016-10-31 10:23:42'),
(978991, 3, 'STS', 'anuj@247around.com', 'paytm-sts', '0f2cda64eb7640e66611d97b4de09465', 'paytm-sts', 1, '2016-10-31 10:23:42');

--sachin 31-may

CREATE TABLE `dealer_details` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `dealer_name` VARCHAR(100) NOT NULL , 
`dealer_phone_number_1` VARCHAR(50) NOT NULL , `owner_name` VARCHAR(100) NOT NULL , 
`owner_phone_number_1` VARCHAR(50) NOT NULL , `city` VARCHAR(100) NOT NULL , 
`update_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`create_date` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;


-- CREATE TABLE `dealer_brand_mapping` (
--   `id` int(11) NOT NULL,
--   `dealer_id` int(11) NOT NULL,
--   `partner_id` int(11) NOT NULL,
--   `service_id` varchar(5) NOT NULL,
--   `brand` varchar(25) NOT NULL,
--   `city` varchar(100) NOT NULL,
--   `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- 
-- ALTER TABLE `dealer_brand_mapping`
--   ADD PRIMARY KEY (`id`);
-- 
-- ALTER TABLE `dealer_brand_mapping`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--Abhay 
CREATE TABLE `dealer_partner_mapping` (
  `id` int(11) NOT NULL,
  `dealer_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `service_id` varchar(5) NOT NULL,
  `brand` varchar(25) NOT NULL,
  `city` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `dealer_partner_mapping`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dealer_partner_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `booking_details` ADD `dealer_id` INT(11) NULL DEFAULT NULL AFTER `assigned_vendor_id`;

-- sachin 5 jun 2017

ALTER TABLE  `pincode_mapping_s3_upload_details` ADD  `agent_id`  int(11) DEFAULT NULL AFTER  `file_name` ;

CREATE TABLE `rating_passthru_misscall_log` (
  `s.no` int(11) NOT NULL,
  `callSid` varchar(255) DEFAULT NULL,
  `from_number` varchar(255) DEFAULT NULL,
  `To` varchar(255) DEFAULT NULL,
  `Direction` varchar(255) DEFAULT NULL,
  `DialCallDuration` varchar(255) DEFAULT NULL,
  `StartTime` varchar(255) DEFAULT NULL,
  `EndTime` varchar(255) DEFAULT NULL,
  `CallType` varchar(255) DEFAULT NULL,
  `DialWhomNumber` varchar(255) DEFAULT NULL,
  `digits` varchar(255) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `rating_passthru_misscall_log`
  ADD PRIMARY KEY (`s.no`),
  ADD KEY `from_number` (`from_number`(10)),
  ADD KEY `To` (`To`(11));

ALTER TABLE `rating_passthru_misscall_log`
  MODIFY `s.no` int(11) NOT NULL AUTO_INCREMENT;



-- sachin 8 jun 2017

UPDATE `sms_template` SET `template` = 'Your %s request is completed by 247around. 
If you are HAPPY with the service,give miss call @ %s. If not, give miss call @ %s' WHERE `sms_template`.`id` = 3;

UPDATE `sms_template` SET `template` = 'Your %s request is completed by 247around. 
If you are HAPPY with the service,give miss call @ %s. If not, give miss call @ %s' WHERE `sms_template`.`id` = 14;



-- BUYBACK -- sachin 19 june 2017
ALTER TABLE `service_centres` ADD `is_sf` TINYINT(1) NOT NULL DEFAULT '1' AFTER `active`, 
ADD `is_cp` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_sf`;


--Buyback -- sachin 21 june 2017
CREATE TABLE `bb_cp_order_action` 
( `id` INT(11) NOT NULL AUTO_INCREMENT , 
`partner_order_id` VARCHAR(256) NOT NULL , 
`cp_id` INT(11) NOT NULL , 
`category` VARCHAR(128) NOT NULL ,
 `brand` VARCHAR(128) NOT NULL , 
`physical_condition` VARCHAR(256) NOT NULL , 
`working_condition` VARCHAR(256) NOT NULL , 
`status` VARCHAR(128) NOT NULL,
`remarks` VARCHAR(256) NOT NULL,
`current_status` VARCHAR(128) NOT NULL , 
`internal_status` VARCHAR(128) NOT NULL , 
`create_date` DATETIME NOT NULL , 
`update_date` TIMESTAMP NOT NULL , 
`closed_date` DATETIME NOT NULL  , 
PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE bb_cp_order_action MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000


CREATE TABLE `bb_order_image_mapping` 
( `id` INT(11) NOT NULL AUTO_INCREMENT , 
`partner_order_id` VARCHAR(256) NOT NULL , 
`cp_id` INT(11) NOT NULL , 
`image_name` VARCHAR(256) NOT NULL ,
 `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
 PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE bb_order_image_mapping MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000


---Abhay
ALTER TABLE `bb_shop_address` ADD `alternate_conatct_number2` VARCHAR(28) NULL DEFAULT NULL AFTER `alternate_conatct_number`;

-- Abhay 28- June
ALTER TABLE `service_centres` ADD `is_gst` INT(2) NOT NULL DEFAULT NULL AFTER `is_cp`, ADD `gst_number` VARCHAR(20) NULL DEFAULT NULL AFTER `is_gst`;
ALTER TABLE `service_centres` ADD `gst_certificate_file` VARCHAR(64) NULL DEFAULT NULL AFTER `gst_number`;

--
-- Table structure for table `sc_gst_details`
--

CREATE TABLE `sc_gst_details` (
  `id` int(11) NOT NULL,
  `service_center_id` int(50) NOT NULL,
  `company_name` varchar(128) DEFAULT NULL,
  `company_address` varchar(128) DEFAULT NULL,
  `company_pan_number` varchar(20) DEFAULT NULL,
  `is_gst` int(2) NOT NULL DEFAULT '0',
  `company_gst_number` varchar(50) DEFAULT NULL,
  `gst_certificate_file` varchar(128) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sc_gst_details`
--
ALTER TABLE `sc_gst_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sc_gst_details`
--
ALTER TABLE `sc_gst_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `partners` ADD `gst_number` VARCHAR(20) NULL DEFAULT NULL AFTER `pan`;


--
-- Table structure for table `bb_around_credit`
--

CREATE TABLE `bb_around_credit` (
  `id` int(11) NOT NULL,
  `add_credit` int(11) DEFAULT '0',
  `remove_credit` int(11) NOT NULL DEFAULT '0',
  `previous_credit` int(11) NOT NULL DEFAULT '0',
  `final_credit` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bb_around_credit`
--
ALTER TABLE `bb_around_credit`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bb_around_credit`
--
ALTER TABLE `bb_around_credit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- --------------------------------------------------------

--
-- Table structure for table `bb_charges`
--

CREATE TABLE `bb_charges` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `cp_id` int(11) NOT NULL,
  `service_id` int(50) NOT NULL,
  `category` varchar(128) NOT NULL,
  `brand` varchar(128) DEFAULT NULL,
  `physical_condition` varchar(256) NOT NULL,
  `working_condition` varchar(256) NOT NULL,
  `city` varchar(128) NOT NULL,
  `partner_basic` decimal(10,2) NOT NULL,
  `partner_tax` decimal(10,2) NOT NULL,
  `partner_total` decimal(10,2) NOT NULL,
  `cp_basic` decimal(10,2) NOT NULL,
  `cp_tax` decimal(10,2) NOT NULL,
  `cp_total` decimal(10,2) NOT NULL,
  `around_basic` decimal(10,2) NOT NULL,
  `around_tax` decimal(10,2) NOT NULL,
  `around_total` decimal(10,2) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `visible_to_partner` int(1) NOT NULL DEFAULT '1',
  `visible_to_cp` int(1) NOT NULL DEFAULT '1',
  `order_key` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bb_charges`
--
ALTER TABLE `bb_charges`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bb_charges`
--
ALTER TABLE `bb_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Table structure for table `bb_cp_order_action`
--

CREATE TABLE `bb_cp_order_action` (
  `id` int(11) NOT NULL,
  `partner_order_id` varchar(256) NOT NULL,
  `cp_id` int(11) NOT NULL,
  `category` varchar(128) DEFAULT NULL,
  `brand` varchar(128) DEFAULT NULL,
  `physical_condition` varchar(256) DEFAULT NULL,
  `working_condition` varchar(256) DEFAULT NULL,
  `remarks` varchar(256) DEFAULT NULL,
  `current_status` varchar(128) NOT NULL,
  `internal_status` varchar(128) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `closed_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bb_cp_order_action`
--
ALTER TABLE `bb_cp_order_action`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bb_cp_order_action`
--
ALTER TABLE `bb_cp_order_action`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



--
-- Table structure for table `bb_order_details`
--

CREATE TABLE `bb_order_details` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `partner_order_id` varchar(256) NOT NULL,
  `partner_gc_id` varchar(256) DEFAULT NULL,
  `partner_tracking_id` varchar(128) DEFAULT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `city` varchar(128) NOT NULL,
  `assigned_cp_id` int(11) DEFAULT NULL,
  `current_status` varchar(56) NOT NULL,
  `internal_status` varchar(128) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bb_order_details`
--
ALTER TABLE `bb_order_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bb_order_details`
--
ALTER TABLE `bb_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Table structure for table `bb_order_image_mapping`
--

CREATE TABLE `bb_order_image_mapping` (
  `id` int(11) NOT NULL,
  `partner_order_id` varchar(256) NOT NULL,
  `cp_id` int(11) NOT NULL,
  `image_name` varchar(256) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bb_order_image_mapping`
--
ALTER TABLE `bb_order_image_mapping`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bb_order_image_mapping`
--
ALTER TABLE `bb_order_image_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Table structure for table `bb_shop_address`
--

CREATE TABLE `bb_shop_address` (
  `id` int(11) NOT NULL,
  `cp_id` int(11) NOT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `shop_address_line1` varchar(128) NOT NULL,
  `shop_address_city` varchar(64) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `contact_person` varchar(128) DEFAULT NULL,
  `primary_contact_number` varchar(50) DEFAULT NULL,
  `contact_email` varchar(50) DEFAULT NULL,
  `shop_address_line2` varchar(128) DEFAULT NULL,
  `shop_address_state` varchar(50) DEFAULT NULL,
  `shop_address_pincode` varchar(6) DEFAULT NULL,
  `tin_number` varchar(28) DEFAULT NULL,
  `alternate_conatct_number` varchar(28) NOT NULL,
  `alternate_conatct_number2` varchar(28) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bb_shop_address`
--
ALTER TABLE `bb_shop_address`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bb_shop_address`
--
ALTER TABLE `bb_shop_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Table structure for table `bb_state_change`
--

CREATE TABLE `bb_state_change` (
  `id` int(11) NOT NULL,
  `order_id` varchar(128) DEFAULT NULL,
  `old_state` varchar(128) NOT NULL,
  `new_state` varchar(128) NOT NULL,
  `remarks` varchar(128) DEFAULT NULL,
  `agent_id` int(11) NOT NULL,
  `service_center_id` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bb_state_change`
--
ALTER TABLE `bb_state_change`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bb_state_change`
--
ALTER TABLE `bb_state_change`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Table structure for table `bb_unit_details`
--

CREATE TABLE `bb_unit_details` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `service_id` int(50) NOT NULL,
  `partner_order_id` varchar(256) NOT NULL,
  `category` varchar(128) DEFAULT NULL,
  `brand` varchar(128) DEFAULT NULL,
  `physical_condition` varchar(256) DEFAULT NULL,
  `working_condition` varchar(256) DEFAULT NULL,
  `order_key` varchar(128) DEFAULT NULL,
  `order_status` varchar(128) DEFAULT NULL,
  `partner_basic_charge` decimal(10,2) NOT NULL,
  `partner_tax_charge` decimal(10,2) NOT NULL,
  `cp_basic_charge` decimal(10,2) NOT NULL,
  `cp_tax_charge` decimal(10,2) NOT NULL,
  `around_commision_basic_charge` decimal(10,2) NOT NULL,
  `around_commision_tax` decimal(10,2) NOT NULL,
  `partner_sweetner_charges` decimal(10,2) DEFAULT NULL,
  `partner_invoice_id` varchar(128) DEFAULT NULL,
  `cp_invoice_id` varchar(128) DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bb_unit_details`
--
ALTER TABLE `bb_unit_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bb_unit_details`
--
ALTER TABLE `bb_unit_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- buyback -sachin 27-june-2017
ALTER TABLE `bb_order_image_mapping` ADD `tag` VARCHAR(128) NULL AFTER `image_name`;
ALTER TABLE `bb_cp_order_action` ADD `order_key` VARCHAR(256) NULL AFTER `internal_status`;



INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'gst_notification', 'Request for Details of GSTIN', 'Respected Sir/Madam,
  
Greetings from 247around!

As you are aware, the introduction of Goods and Services Tax (“GST\E2\80?) will be implemented on 1st July 2017.

Government has already initiated the migration process for registration under GST and you would have received a GSTIN / Provisional GSTIN from GSTN portal.
 
In this connection, we request you to provide your GSTIN / provisional GSTIN on the link mentioned herein below on or before 7th July 2017.

URL: 

Your GSTIN will be captured on our invoices and the GST returns for passing on the seamless credit to you under the GST regime.

Kindly note provisional GSTIN of Blackmelon Advance Technology Co. Pvt. Ltd. for the state of Delhi is –  07AAFCB1281J1ZQ

If already filled, please ignore

Regards
Team 247around', 'billing@247around.com', 'billing@247around.com', 'anuj@247around.com, nits@247around.com, adila@247around.com,oza@247around.com,nilanjan@247around.com,suresh@247around.com', '', '1', CURRENT_TIMESTAMP);







ALTER TABLE `service_centres` CHANGE `is_gst` `is_gst_doc` INT(2) NULL DEFAULT '0', CHANGE `gst_number` `gst_no` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL, CHANGE `gst_certificate_file` `gst_file` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;




--
-- Table structure for table `log_action_on_entity`
--

CREATE TABLE `log_entity_action` (
  `id` int(11) NOT NULL,
  `entity` varchar(30) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `remarks` varchar(128) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log_action_on_entity`
--
ALTER TABLE `log_entity_action`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_action_on_entity`
--
ALTER TABLE `log_entity_action`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- sachin 5 july
UPDATE `email_template` SET `template` = 
'Dear Partner,<br>Your brackets order has been placed sucessfully.<br>
<br>Your Order ID is: <b>%s</b> <br> <strong>Order Details:</strong>
<br> Less Than 32 Inch Brackets : %s <br> 32 Inch & Above Brackets : %s <br> 
Total Requested : %s<br><br> We will update you as soon as the brackets are shipped.
<br><br>Regards,<br>247Around Team' WHERE `email_template`.`id` = 11;

UPDATE `email_template` SET `template` = 
'Dear Partner,<br>You have received a new order for brackets.<br><br>
Your Order ID is : <b>%s</b> <br> <strong>Order Details:</strong><br>
<br> Less Than 32 Inch Brackets : %s <br> 32 & Above Inch Brackets : %s 
<br> Total Requested : %s<br><br> <strong>Requested From: </strong><br><br> %s<br> 
c/o: %s <br> Address: %s <br> City: %s <br> State: %s <br> Pincode: %s 
<br> Phone Number: %s, %s<br><br> Please notify when you ship the above order.<br>
<br> Regards,<br> 247Around Team' WHERE `email_template`.`id` = 10;

UPDATE `email_template` SET `template` = 
'%s order has been Cancelled sucessfully for the <strong>Order ID : %s </strong><br>
<br><strong>Reason : </strong> %s <br><br> <strong>Order Details are:</strong><br> 
Less than 32 Inch Brackets : %s <br> 32 Inch & Above Brackets Shipped by Brackets : %s <br> Total 
Requested : %s<br><br> Thanks Team 247Around' WHERE `email_template`.`id` = 12;

UPDATE `email_template` SET `template` = 
'Brackets order <strong>%s</strong> has been Cancelled. <br><br><strong>Reason 
: </strong> %s <br><br> <strong>Order Details:</strong><br><br> Less than 32 Inch Brackets : 
%s <br> 32 Inch & Above Brackets : %s <br> Total Requested : %s<br><br> <strong>Requested 
From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br>City: %s <br> State: %s <br> Pincode:
 %s <br> Phone Number: %s, %s<br><br> Please <b>don\'\'t</b> ship this order.' WHERE `email_template`.`id` = 13;

UPDATE `email_template` SET `template` = 
'%s order has been Un-Cancelled sucessfully for the <strong>Order ID : %s 
</strong><br><br> <strong>Order Details are:</strong><br> Less than 32 Inch 
Brackets : %s <br> 32 Inch & Above Brackets : %s <br> Total Requested :
 %s<br><br> Team 247Around' WHERE `email_template`.`id` = 20;


UPDATE `email_template` SET `template` = 
'An order has been <b>Un-Cancelled</b> for Brackets of <strong>Order ID : %s 
</strong><br><br> <strong>Order Details:</strong><br><br> Less than 32 Inch Brackets 
: %s <br> 32 Inch & Above Brackets : %s <br> Total Requested : %s<br><br> <strong>
Requested From: </strong><br><br> %s<br> c/o: %s <br> Address: %s <br> Phone Number: %s, 
%s<br><br> Please <b>ship</b> this order.' WHERE `email_template`.`id` = 21;

-- bb -sachin 12 july 2017

ALTER TABLE `bb_state_change` ADD `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `partner_id`;

 -- bb --sachin 14 july 2017

ALTER TABLE `partners` ADD `invoice_courier_name` VARCHAR(128) NULL AFTER `invoice_email_bcc`, 
ADD `invoice_courier_address` VARCHAR(256) NULL AFTER `invoice_courier_name`, 

ADD `invoice_courier_phone_number` VARCHAR(20) NULL AFTER `invoice_courier_address`;

-- Abhay 19 July
ALTER TABLE `service_centres` ADD `min_upcountry_distance` INT(100) NULL DEFAULT '25' AFTER `gst_file`;


--Abhay 24 July
ALTER TABLE `bb_shop_address` ADD `shop_address_region` VARCHAR(64) NULL DEFAULT NULL AFTER `shop_address_city`;

--Abhay 29 Jul;y
ALTER TABLE `partners` ADD `is_def_spare_required` INT(1) NOT NULL DEFAULT '0' AFTER `upcountry_r2`


-- Sachin 2 Aug
ALTER TABLE `bb_shop_address` ADD `cp_capacity` VARCHAR(128) NULL DEFAULT NULL AFTER `tin_number`;

--sachin 4 aug
ALTER TABLE `bb_cp_order_action` ADD `cp_claimed_price` DECIMAL(10,2) NOT NULL AFTER `order_key`;
ALTER TABLE `bb_unit_details` ADD `cp_claimed_price` DECIMAL(10,2) NOT NULL AFTER `cp_invoice_id`;
ALTER TABLE `bb_cp_order_action` ADD `admin_remarks` VARCHAR(256) NULL AFTER `cp_claimed_price`;



--Abhay 1 Aug

--booking unit details - 
ALTER TABLE `booking_unit_details` ADD `rcm_tax` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `vendor_st_or_vat_basic_charges`;
ALTER TABLE `booking_unit_details` ADD `tag_name` VARCHAR(64) NULL DEFAULT NULL AFTER `price_tags`;

--Invoice
ALTER TABLE `vendor_partner_invoices` ADD `reference_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `invoice_id`;
--
-- Table structure for table `hsn_code_details`
--

CREATE TABLE `hsn_code_details` (
  `id` int(11) NOT NULL,
  `hsn_code` varchar(10) NOT NULL,
  `tag_name` varchar(32) NOT NULL,
  `price_tag` varchar(32) NOT NULL,
  `tax_rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hsn_code_details`
--
ALTER TABLE `hsn_code_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hsn_code_details`
--
ALTER TABLE `hsn_code_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  `hsn_code` varchar(10) DEFAULT NULL,
  `uom` varchar(16) DEFAULT NULL,
  `price_rate` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_type` varchar(16) NOT NULL DEFAULT '0',
  `tax_rate` int(11) NOT NULL DEFAULT '0',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,0) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- Abhay 4 Aug
ALTER TABLE `vendor_partner_invoices` 
ADD `cgst_tax_amount` DECIMAL NOT NULL DEFAULT '0' AFTER `amount_collected_paid`, 
ADD `igst_tax_amount` DECIMAL NOT NULL DEFAULT '0' AFTER `cgst_tax_amount`, 
ADD `sgst_tax_amount` DECIMAL NOT NULL DEFAULT '0' AFTER `igst_tax_amount`, 
ADD `cgst_tax_rate` DECIMAL NOT NULL DEFAULT '0' AFTER `sgst_tax_amount`, 
ADD `igst_tax_rate` DECIMAL NOT NULL DEFAULT '0' AFTER `cgst_tax_rate`, 
ADD `sgst_tax_rate` DECIMAL NOT NULL DEFAULT '0' AFTER `igst_tax_rate`;


-- Sachin 2 Aug
ALTER TABLE `bb_shop_address` ADD `cp_capacity` VARCHAR(128) NULL DEFAULT NULL AFTER `tin_number`;


--sachin 4 aug
ALTER TABLE `bb_cp_order_action` ADD `cp_claimed_price` DECIMAL(10,2) NOT NULL AFTER `order_key`;
ALTER TABLE `bb_unit_details` ADD `cp_claimed_price` DECIMAL(10,2) NOT NULL AFTER `cp_invoice_id`;
ALTER TABLE `bb_cp_order_action` ADD `admin_remarks` VARCHAR(256) NULL AFTER `cp_claimed_price`;


--Abhay 10 Aug
ALTER TABLE `vendor_partner_invoices` ADD `rcm` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `sgst_tax_rate`;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'buyback_details_invoices_for_vendors', '247around - %s - Buyback Invoice for period: %s to %s', 'Dear Partner,<br/><br/>
Please find attached Buyback invoice. 
Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice.
<br/><br/>Thanks,<br/>247around Team', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

--Abhay 11 aug
ALTER TABLE `booking_details` ADD `upcountry_vendor_invoice_id` VARCHAR(100) NULL DEFAULT NULL AFTER `upcountry_partner_invoice_id`;

-- Sachin 14 Aug

UPDATE `email_template` SET `subject` = 'NEW Brackets Requested' WHERE `email_template`.`id` = 11;

UPDATE `email_template` SET `subject` = 'New Brackets Requested From %s' WHERE `email_template`.`id` = 10;

UPDATE `email_template` SET `subject` = 'Brackets Shipped To %s' WHERE `email_template`.`id` = 14;

UPDATE `email_template` SET `subject` = 'Brackets Received by %s' WHERE `email_template`.`id` = 15;

UPDATE `email_template` SET `subject` = 'Brackets Received by %s' WHERE `email_template`.`id` = 16;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'brackets_shipment_mail_to_order_given_to', 'Brackets Shipped To %s', 
'Dear Partner, Brackets have been shipped successfully to <b> %s </b> for the Order ID<b> %s </b>.
<br><br> Regards, <br> 247Around Team', 'booking@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

--sachin 23 AUG
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'pan_notification', 'Request for PAN Number | %s', 'Respected Sir/Madam,<br><br>
Greetings from 247around!<br>
Please Update Your PAN Details<br><br>
If already Updated, please ignore<br><br>
Regards<br>
Team 247around', 'billing@247around.com', '', 'anuj@247around.com, nits@247around.com', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'bank_details_notification', 'Request for Bank Details | %s', 'Respected Sir/Madam,<br><br>
Greetings from 247around!<br>
Please Update Your Bank Details<br><br>
If already Updated, please ignore<br><br>
Regards<br>
Team 247around', 'billing@247around.com', '', 'anuj@247around.com, nits@247around.com', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'bank_details_not_verified_notification', 'Bank Details Not Verified', 'Greetings from 247around!<br>
Below are the sf for which bank details exist but they are not verified yet.<br><br>
%s  <br><br>
Please Verifiy bank details of above sf.<br><br>
If already Updated, please ignore<br><br>
Regards<br>
Team 247around', 'billing@247around.com', 'anuj@247around.com', '', '', '1', CURRENT_TIMESTAMP);


---Abhay 25 Aug
ALTER TABLE  `spare_parts_details` ADD  `defective_courier_receipt` VARCHAR( 64 ) NULL DEFAULT NULL AFTER `courier_charges_by_sf` ;

--sachin 28 aug

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'defective_parts_acknowledge_reminder', 'Defective Parts Acknowledge Report', 'Dear Partner,<br>
Defective parts for below bookings have been shipped by 247around Service Centre but Delivery has not been acknowledged by your team till now: <br><br>
%s  <br><br>
Please confirm / reject the delivery of these defective parts. Post 14 days of shipment, 247around system will mark them Delivered automatically. <br><br>
Thanks. <br> 247around Team', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com,booking@247around.com, abhaya@247around.com', '', '1', CURRENT_TIMESTAMP);


INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'auto_acknowledge_defective_parts', 'Auto Acknowledge Defective Parts Report', 'Dear Partner,<br>
Below are the bookings which are auto acknowledge by 247around as per the earlier mail: <br><br>
%s  <br><br>
If you have any issue regarding this then please contact us. <br><br>
Thanks. <br> 247around Team', 'booking@247around.com', '', 'anuj@247around.com, nits@247around.com,booking@247around.com, abhaya@247around.com', '', '1', CURRENT_TIMESTAMP);

--Abhay Anand 30 Aug

ALTER TABLE `bank_transactions` ADD `is_advance` INT(1) NOT NULL DEFAULT '0' AFTER `remarks`;

-- sachin 30 AUg
ALTER TABLE `partners` ADD `account_managers_id` INT(11) NULL DEFAULT NULL AFTER `landmark`;

-- sachin 31 Aug
UPDATE `email_template` SET `template` = 'Dear Partner,<br><br> As discussed, please find below your login details.
<br><br> URL: <a href="https://www.aroundhomzapp.com/service_center/login">https://www.aroundhomzapp.com/service_center/login</a>
<br><br> <b>Username: </b>%s<br><b>Password: </b>%s<br><br> Please use the ERP panel for your closures going forward. 
In case of any issues, write to us or call us.<br><br> Regards,<br> 247around Team' 
WHERE `email_template`.`tag` = 'vendor_login_details';


ALTER TABLE `bank_transactions` ADD `is_advance` INT(1) NOT NULL DEFAULT '0' AFTER `remarks`;

--sachin 2 sep
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'partner_activate_email', 'Your CRM Activated', 'CRM Activated', 
'booking@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'partner_deactivate_email', 'Your CRM De-Activated', 'CRM De-Activated', 
'booking@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

--Abhay 1 Sept
ALTER TABLE `spare_parts_details` ADD `approved_defective_parts_by_admin` INT(1) NOT NULL DEFAULT '0' AFTER `approved_defective_parts_by_partner`;


-- sachin 7 sep

ALTER TABLE `bb_order_details` ADD `acknowledge_date` DATETIME NULL DEFAULT NULL AFTER `delivery_date`;

-- sachin 12 sep
ALTER TABLE `bb_cp_order_action` ADD `acknowledge_date` DATETIME NULL DEFAULT NULL AFTER `admin_remarks`;

--sachin 13 sep
ALTER TABLE `appliance_product_description` ADD `is_verified` TINYINT(1) NULL DEFAULT NULL AFTER `brand`;

--sachin 15 sep
CREATE TABLE `bb_delivery_order_status_report` (
  `id` int(11) NOT NULL,
  `file_name` varchar(256) NOT NULL,
  `order_day` date NOT NULL,
  `partner_name` varchar(128) NOT NULL,
  `subcat` varchar(128) NOT NULL,
  `order_id` varchar(256) NOT NULL,
  `city` varchar(128) NOT NULL,
  `tracking_id` varchar(256) NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `order_status` varchar(128) NOT NULL,
  `old_item_del_date` date NOT NULL,
  `buyback_details` varchar(256) NOT NULL,
  `sweetner_value` decimal(10,2) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `bb_delivery_order_status_report` ADD PRIMARY KEY(`id`);

ALTER TABLE `bb_delivery_order_status_report` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

--sachin 16 sep
ALTER TABLE `bb_order_details` ADD `file_received_date` DATE NOT NULL AFTER `internal_status`;

ALTER TABLE `bb_delivery_order_status_report` ADD `file_received_date` DATE NOT NULL AFTER `file_name`;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'non_verified_appliance_mail', 'Appliance Description Details', 
'Below are the appliance description which are not verified. Please have a look and update this as soon as posssible: <br>
%s', 'noreply@2417around.com', '', '', '', '1', CURRENT_TIMESTAMP);


--sachin 18 sep

CREATE TABLE `247around`.`bb_query_report` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `description` VARCHAR(512) NOT NULL , 
`query` VARCHAR(2048) NOT NULL , `active` TINYINT(2) NOT NULL , `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
PRIMARY KEY (`id`)) ENGINE = InnoDB;


INSERT INTO `query_report` (`id`, `description`, `query`, `active`, `type`, `create_date`) VALUES
(null, 'debit_note_raised', 'select count(partner_order_id) as count from bb_order_details where current_status = \'Claim Debit Note Raised\'', 1, 'buyback','2017-09-20 13:05:48'),
(null, 'debot_note_raised_amt', 'select round(COALESCE(sum(partner_basic_charge),0)) as count from bb_order_details join bb_unit_details on bb_order_details.partner_order_id = bb_unit_details.partner_order_id where current_status = \'Claim Debit Note Raised\'', 1, 'buyback','2017-09-20 13:16:26'),
(null, 'debit_note_not_raised', 'select count(partner_order_id) as count from bb_order_details where current_status = \'Claim Approved\'', 1,'buyback', '2017-09-20 13:17:59'),
(null, 'debit_note_not_raised_amt', 'select round(COALESCE(sum(partner_basic_charge),0)) as count from bb_order_details join bb_unit_details on bb_order_details.partner_order_id = bb_unit_details.partner_order_id where current_status = \'Claim Approved\'', 1,'buyback', '2017-09-20 13:17:59'),
(null, 'last_month_order', 'SELECT (in_transit_count+deliverd_count) as count\nFROM ( \n    SELECT SUM(CASE\n        WHEN current_status = \'Delivered\' AND delivery_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND delivery_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' )THEN 1\n        ELSE 0\n    END) AS \'deliverd_count\',\n    SUM(CASE\n       WHEN current_status IN (\'In-Transit\', \'New Item In-transit\', \'Attempted\') AND order_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND order_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'in_transit_count\' FROM bb_order_details) as a', 1,'buyback', '2017-09-21 06:06:22'),
(null, 'this_month_order', 'SELECT (in_transit_count+deliverd_count) as count\nFROM ( \n    SELECT SUM(CASE\n        WHEN current_status = \'Delivered\' AND delivery_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'deliverd_count\',\n    SUM(CASE\n       WHEN current_status IN (\'In-Transit\', \'New Item In-transit\', \'Attempted\') AND order_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'in_transit_count\' FROM bb_order_details) as a', 1, 'buyback','2017-09-21 06:09:34'),
(null, 'avg_buying_price', 'SELECT round(AVG(partner_basic_charge+partner_tax_charge)) as count FROM bb_unit_details JOIN bb_order_details ON bb_unit_details.partner_order_id = bb_order_details.partner_order_id', 1, 'buyback','2017-09-21 06:48:40'),
(null, 'avg_selling_price', 'SELECT round(AVG(cp_basic_charge+cp_tax_charge)) as count FROM bb_unit_details JOIN bb_order_details ON bb_unit_details.partner_order_id = bb_order_details.partner_order_id', 1,'buyback', '2017-09-21 06:48:40');

--sachin 29 sep
ALTER TABLE `query_report` ADD `type` VARCHAR(64) NOT NULL AFTER `active`;
UPDATE `query_report` SET `type` = 'service' 

INSERT INTO `bb_query_report` (`id`, `description`, `query`, `active`, `create_date`) VALUES
(1, 'debit_note_raised', 'select count(partner_order_id) as count from bb_order_details where current_status = \'Claim Debit Note Raised\'', 1, '2017-09-20 13:05:48'),
(2, 'debot_note_raised_amt', 'select round(COALESCE(sum(partner_basic_charge),0)) as count from bb_order_details join bb_unit_details on bb_order_details.partner_order_id = bb_unit_details.partner_order_id where current_status = \'Claim Debit Note Raised\'', 1, '2017-09-20 13:16:26'),
(3, 'debit_note_not_raised', 'select count(partner_order_id) as count from bb_order_details where current_status = \'Claim Approved\'', 1, '2017-09-20 13:17:59'),
(4, 'debit_note_not_raised_amt', 'select round(COALESCE(sum(partner_basic_charge),0)) as count from bb_order_details join bb_unit_details on bb_order_details.partner_order_id = bb_unit_details.partner_order_id where current_status = \'Claim Approved\'', 1, '2017-09-20 13:17:59'),
(5, 'last_month_order', 'SELECT (in_transit_count+deliverd_count) as count\nFROM ( \n    SELECT SUM(CASE\n        WHEN current_status = \'Delivered\' AND delivery_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND delivery_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' )THEN 1\n        ELSE 0\n    END) AS \'deliverd_count\',\n    SUM(CASE\n       WHEN current_status IN (\'In-Transit\', \'New Item In-transit\', \'Attempted\') AND order_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND order_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'in_transit_count\' FROM bb_order_details) as a', 1, '2017-09-21 06:06:22'),
(6, 'this_month_order', 'SELECT (in_transit_count+deliverd_count) as count\nFROM ( \n    SELECT SUM(CASE\n        WHEN current_status = \'Delivered\' AND delivery_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'deliverd_count\',\n    SUM(CASE\n       WHEN current_status IN (\'In-Transit\', \'New Item In-transit\', \'Attempted\') AND order_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'in_transit_count\' FROM bb_order_details) as a', 1, '2017-09-21 06:09:34'),
(7, 'avg_buying_price', 'SELECT round(AVG(partner_basic_charge+partner_tax_charge)) as count FROM bb_unit_details JOIN bb_order_details ON bb_unit_details.partner_order_id = bb_order_details.partner_order_id', 1, '2017-09-21 06:48:40'),
(8, 'avg_selling_price', 'SELECT round(AVG(cp_basic_charge+cp_tax_charge)) as count FROM bb_unit_details JOIN bb_order_details ON bb_unit_details.partner_order_id = bb_order_details.partner_order_id', 1, '2017-09-21 06:48:40');





--Abhay 27 Sept
ALTER TABLE `partners` ADD `is_prepaid` INT(1) NOT NULL DEFAULT '0' AFTER `is_def_spare_required`;
ALTER TABLE `partners` ADD `prepaid_amount_limit` INT(128) NOT NULL DEFAULT '0' AFTER `is_prepaid`, ADD `grace_period` INT(11) NOT NULL DEFAULT '0' AFTER `prepaid_amount_limit`;
ALTER TABLE `partners` ADD `prepaid_grace_amount` INT(128) NOT NULL DEFAULT '0' AFTER `prepaid_amount_limit`;

ALTER TABLE `trigger_partners` ADD `is_prepaid` INT(1) NOT NULL DEFAULT '0' AFTER `is_def_spare_required`;
ALTER TABLE `trigger_partners` ADD `prepaid_amount_limit` INT(128) NOT NULL DEFAULT '0' AFTER `is_prepaid`, ADD `grace_period` INT(11) NOT NULL DEFAULT '0' AFTER `prepaid_amount_limit`;
ALTER TABLE `trigger_partners` ADD `prepaid_grace_amount` INT(128) NOT NULL DEFAULT '0' AFTER `prepaid_amount_limit`;


--Abhay 29 Sep
ALTER TABLE `vendor_partner_invoices` ADD `parts_count` INT(11) NOT NULL DEFAULT '0' AFTER `num_bookings`;
ALTER TABLE `trigger_vendor_partner_invoices` ADD `parts_count` INT(11) NOT NULL DEFAULT '0' AFTER `num_bookings`;

INSERT INTO `query_report` (`id`, `description`, `query`, `active`, `type`, `create_date`) VALUES
(null, 'debit_note_raised', 'select count(partner_order_id) as count from bb_order_details where current_status = \'Claim Debit Note Raised\'', 1, 'buyback','2017-09-20 13:05:48'),
(null, 'debot_note_raised_amt', 'select round(COALESCE(sum(partner_basic_charge),0)) as count from bb_order_details join bb_unit_details on bb_order_details.partner_order_id = bb_unit_details.partner_order_id where current_status = \'Claim Debit Note Raised\'', 1, 'buyback','2017-09-20 13:16:26'),
(null, 'debit_note_not_raised', 'select count(partner_order_id) as count from bb_order_details where current_status = \'Claim Approved\'', 1,'buyback', '2017-09-20 13:17:59'),
(null, 'debit_note_not_raised_amt', 'select round(COALESCE(sum(partner_basic_charge),0)) as count from bb_order_details join bb_unit_details on bb_order_details.partner_order_id = bb_unit_details.partner_order_id where current_status = \'Claim Approved\'', 1,'buyback', '2017-09-20 13:17:59'),
(null, 'last_month_order', 'SELECT (in_transit_count+deliverd_count) as count\nFROM ( \n    SELECT SUM(CASE\n        WHEN current_status = \'Delivered\' AND delivery_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND delivery_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' )THEN 1\n        ELSE 0\n    END) AS \'deliverd_count\',\n    SUM(CASE\n       WHEN current_status IN (\'In-Transit\', \'New Item In-transit\', \'Attempted\') AND order_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND order_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'in_transit_count\' FROM bb_order_details) as a', 1,'buyback', '2017-09-21 06:06:22'),
(null, 'this_month_order', 'SELECT (in_transit_count+deliverd_count) as count\nFROM ( \n    SELECT SUM(CASE\n        WHEN current_status = \'Delivered\' AND delivery_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'deliverd_count\',\n    SUM(CASE\n       WHEN current_status IN (\'In-Transit\', \'New Item In-transit\', \'Attempted\') AND order_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'in_transit_count\' FROM bb_order_details) as a', 1, 'buyback','2017-09-21 06:09:34'),
(null, 'avg_buying_price', 'SELECT round(AVG(partner_basic_charge+partner_tax_charge)) as count FROM bb_unit_details JOIN bb_order_details ON bb_unit_details.partner_order_id = bb_order_details.partner_order_id', 1, 'buyback','2017-09-21 06:48:40'),
(null, 'avg_selling_price', 'SELECT round(AVG(cp_basic_charge+cp_tax_charge)) as count FROM bb_unit_details JOIN bb_order_details ON bb_unit_details.partner_order_id = bb_order_details.partner_order_id', 1,'buyback', '2017-09-21 06:48:40');

--sachin 29 sep
ALTER TABLE `query_report` ADD `type` VARCHAR(64) NOT NULL AFTER `active`;
UPDATE `query_report` SET `type` = 'service' 

--Abhay 03 oct
ALTER TABLE `partners` CHANGE `prepaid_grace_amount` `prepaid_notification_amount` INT(128) NOT NULL DEFAULT '0';
ALTER TABLE `partners` CHANGE `grace_period` `grace_period_date` DATE NULL DEFAULT NULL;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'low_prepaid_amount', 'Low Balance', 'Dear Partner,<br/><br/> Please recharge your account <br/><br/>Thanks,<br/>247around Team', 
'billing@247around.com', '', 'anuj@247around.com, nits@247around.com, adityag@gmail.com', '', '1', '2017-10-03 13:05:07');

--Chhavi 6 oct
ALTER TABLE `employee_relation` ADD `state_id` VARCHAR(50) NOT NULL AFTER `service_centres_id`;

--Chhavi 9th Oct
ALTER TABLE `sf_not_exist_booking_details` ADD `rm_id` INT NOT NULL AFTER `city`;

--Chhavi 9th Oct
ALTER TABLE `service_centres` ADD `agent_id` INT(10) NULL DEFAULT NULL AFTER `create_date`;

--Chhavi 9th Oct
ALTER TABLE `sf_not_exist_booking_details` ADD `state` VARCHAR(20) NOT NULL AFTER `create_date`, ADD `service_id` INT(11) NOT NULL AFTER `state`, ADD `active_flag` INT(2) NOT NULL DEFAULT '1' AFTER `appliance_id`;


--Chhavi 06 oct
ALTER TABLE `employee_relation` ADD `state_id` VARCHAR(50) NOT NULL AFTER `service_centres_id`;

--Chhavi

  ALTER TABLE `service_centres` ADD `agent_id` INT(10) NULL DEFAULT NULL AFTER `create_date`;

-- sachin 09 oct

CREATE TABLE `email_send_details` 
( 
`id` INT(11) NOT NULL AUTO_INCREMENT , 
`email_from` VARCHAR(64) NOT NULL , 
`email_to` VARCHAR(256) NOT NULL , 
`cc` VARCHAR(256) NULL , 
`bcc` VARCHAR(256) NULL , 
`subject` VARCHAR(256) NOT NULL , 
`message` VARCHAR(256) NULL , 
`attachment_link` VARCHAR(256) NULL , 
`create_date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

--sachin 10 oct

CREATE TABLE `bb_svc_balance` 
( `id` INT(11) NOT NULL AUTO_INCREMENT , 
`tv_balance` DECIMAL(10,2) NOT NULL DEFAULT '0.00' , 
`la_balance` DECIMAL(10,2) NOT NULL DEFAULT '0.00' , 
`create_date` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;


--sachin 12 Oct
ALTER TABLE `employee` ADD `languages` VARCHAR(256) NULL DEFAULT NULL AFTER `image_link`;
UPDATE `employee` SET `languages` = 'English, Hindi, Marathi' WHERE `employee`.`id` = 24;
UPDATE `employee` SET `languages` = 'English, Hindi, Bengali' WHERE `employee`.`id` = 25;
UPDATE `employee` SET `languages` = 'English, Hindi' WHERE `employee`.`id` = 32;
UPDATE `employee` SET `languages` = 'English,Tamil,Malayalam,Telugu,Kannada' WHERE `employee`.`id` = 16;
ALTER TABLE `employee` ADD `office_centre` VARCHAR(128) NULL DEFAULT NULL AFTER `languages`;
UPDATE `employee` SET `office_centre` = 'Chennai' WHERE `employee`.`id` = 16;
UPDATE `employee` SET `office_centre` = 'Mumbai' WHERE `employee`.`id` = 24;
UPDATE `employee` SET `office_centre` = 'Kolkata' WHERE `employee`.`id` = 25;
UPDATE `employee` SET `office_centre` = 'Delhi' WHERE `employee`.`id` = 32;

-- sachin 16 oct 
ALTER TABLE `file_uploads` ADD `result` VARCHAR(64) NULL AFTER `agent_id`;


-- sachin 23 Oct

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'resend_login_details', 'New Login details', 'please find below your login details.<br><br>
<b>Username: </b>%s<br><b>Password: </b>%s<br><br>
Please use the ERP panel for your closures going forward. In case of any issues, write to us or call us.<br><br>
Regards,<br> 247around Team', 'booking@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

-- sachin 24 Oct

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) 
VALUES (NULL, 'booking_details_to_dealer', 'New Request for %s from %s is confirmed for %s,%s. Booking Id is %s.Please Contact Customer@%s.', 
'Send sms To dealer When New booking created', '1', CURRENT_TIMESTAMP);


--sachin 27 Oct
ALTER TABLE `penalty_on_booking` ADD `agent_type` VARCHAR(128) NOT NULL AFTER `agent_id`;
UPDATE penalty_on_booking SET agent_type = 'admin';


--Abhay 12 OCT
ALTER TABLE `spare_parts_details` ADD `estimate_cost_given` DECIMAL(10,2) NOT NULL AFTER `date_of_request`;
ALTER TABLE `spare_parts_details` ADD `estimate_cost_given_date` DATE NULL DEFAULT NULL AFTER `estimate_cost_given`;
ALTER TABLE `spare_parts_details` ADD `incoming_invoice_pdf` VARCHAR(128) NULL DEFAULT NULL AFTER `estimate_cost_given_date`;
ALTER TABLE `spare_parts_details` CHANGE `estimate_cost_given` `estimate_purchase_cost` DECIMAL(10,2) NOT NULL;
ALTER TABLE `spare_parts_details` ADD `estimate_sell_cost` DECIMAL(10,2) NULL DEFAULT '0' AFTER `estimate_purchase_cost`;



-- 02 Nov
ALTER TABLE `spare_parts_details` CHANGE `estimate_purchase_cost` `purchase_price` DECIMAL(10,2) NULL DEFAULT '00', CHANGE `estimate_sell_cost` `sell_price` DECIMAL(10,2) NULL DEFAULT '0.00';
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'OOW_invoice_sent', 'Repair OOW Parts Sent By Partner For Booking ID: %s', 'Spare Invoice Estimate Given %s', 'billing@247around.com', 'anuj@247around.com, adityag@247around.com', 'abhaya@247around', '', '1', '2017-11-02 23:56:57');

ALTER TABLE `login_logout_details` ADD `is_login_by_247` INT NULL DEFAULT '1' AFTER `created_on`;


--02 Nov
ALTER TABLE `login_logout_details` ADD `is_login_by_247` INT NULL DEFAULT '1' AFTER `created_on`;

--02 Nov
ALTER TABLE `partners` ADD `customer_care_contact` INT NULL DEFAULT NULL AFTER `primary_contact_phone_1`;
ALTER TABLE `partners` CHANGE `customer_care_contact` `customer_care_contact` VARCHAR(20) NULL DEFAULT NULL;

--Anuj 03 Nov
ALTER TABLE  `bb_unit_details` ADD  `approved_by_admin` INT( 1 ) NOT NULL DEFAULT  '0' COMMENT  'If Admin has approved special price' AFTER `partner_sweetner_charges` ;
ALTER TABLE  `bb_unit_details` ADD  `remarks` VARCHAR( 256 ) NOT NULL COMMENT  'Approval remarks' AFTER  `approved_by_admin` ;


-- 8 Nov sachin
CREATE TABLE `query_report` (
  `id` int(11) NOT NULL,
  `main_description` varchar(512) NOT NULL,
  `query1_description` varchar(1024) NOT NULL,
  `query2_description` varchar(1024) NOT NULL,
  `query1` varchar(2048) NOT NULL,
  `query2` varchar(2048) NOT NULL,
  `role` varchar(256) NOT NULL,
  `priority` tinyint(1) NOT NULL,
  `type` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `query_report` (`id`, `main_description`, `query1_description`, `query2_description`, `query1`, `query2`, `role`, `priority`, `type`, `active`, `create_date`) VALUES
(1, 'missed_calls_received', 'today', 'yesterday', 'SELECT COUNT(*) as count \nFROM  `sms_sent_details` \nWHERE  `content` LIKE  \'%Thank you for the delivery confirmation%\'\nAND  `created_on` >=  CURDATE()', 'SELECT COUNT( * ) AS count\nFROM  `sms_sent_details` \nWHERE  `content` LIKE  \'%Thank you for the delivery confirmation%\'\nAND created_on >= ( CURDATE( ) - INTERVAL 1 \nDAY ) \nAND created_on < ( CURDATE( ) - INTERVAL 0 \nDAY )', 'admin,closure,callcenter', 1, 'service', 1, '2017-11-06 10:33:42'),
(2, 'sMSes_sent', 'today', 'yesterday', 'SELECT COUNT(*) as count FROM `sms_sent_details` WHERE `sms_tag` IN (\'sd_edd_missed_call_reminder\', \'sd_shipped_missed_call_initial\', \'sd_delivered_missed_call_initial\') AND `created_on` >= CURDATE();', 'SELECT COUNT(*) as count FROM sms_sent_details WHERE sms_tag IN (\'sd_edd_missed_call_reminder\', \'sd_shipped_missed_call_initial\', \'sd_delivered_missed_call_initial\') AND (created_on > CURDATE() - INTERVAL 1 DAY) AND (created_on <  CURDATE())', 'admin', 1, 'service', 0, '2017-11-06 10:36:12'),
(3, 'bookings_completed', 'this month', 'last month', 'SELECT COUNT(*) as count FROM booking_details WHERE current_status LIKE \'Completed\' \nAND closed_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' )', 'SELECT COUNT(*) as count FROM booking_details WHERE current_status LIKE \'Completed\'  AND closed_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND closed_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' )', 'admin,closure,callcenter', 1, 'service', 1, '2017-11-06 10:36:12'),
(4, 'ratings_completed', 'this month', 'last month', 'SELECT COUNT(*) as count FROM `booking_details` WHERE `current_status` LIKE \'Completed\' AND `rating_stars` IN (\'0\',\'1\',\'2\',\'3\',\'4\',\'5\') AND MONTH(closed_date) = MONTH(CURRENT_DATE)\r\nAND YEAR( closed_date ) = YEAR( CURRENT_DATE )', 'SELECT COUNT(*) as count FROM `booking_details` WHERE `current_status` LIKE \'Completed\' AND `rating_stars` IN (\'0\',\'1\',\'2\',\'3\',\'4\',\'5\') AND closed_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND closed_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' )', 'admin,closure,callcenter', 1, 'service', 1, '2017-11-06 10:38:20'),
(5, 'rating', '', '', 'SELECT ROUND( AVG( rating_stars ) , 2 ) AS count\r\nFROM booking_details, service_centres AS sc\r\nWHERE rating_stars IS NOT NULL \r\nAND current_status =  "Completed"\r\nAND sc.id = booking_details.assigned_vendor_id AND sc.active = 1', '', 'admin,closure,callcenter', 2, 'service', 1, '2017-11-06 10:40:48'),
(6, 'debit_note_raised', '', '', 'select count(partner_order_id) as count from bb_order_details where current_status = \'Claim Debit Note Raised\'', '', 'admin', 1, 'buyback', 1, '2017-11-06 10:42:06'),
(7, 'debit_note_raised_amt', '', '', 'select round(COALESCE(sum(partner_basic_charge),0)) as count from bb_order_details join bb_unit_details on bb_order_details.partner_order_id = bb_unit_details.partner_order_id where current_status = \'Claim Debit Note Raised\'', '', 'admin', 1, 'buyback', 1, '2017-11-06 10:42:52'),
(8, 'debit_note_not_raised', '', '', 'select count(partner_order_id) as count from bb_order_details where current_status = \'Claim Approved\'', '', 'admin', 1, 'buyback', 1, '2017-11-06 10:43:50'),
(9, 'debit_note_not_raised_amt', '', '', 'select round(COALESCE(sum(partner_basic_charge),0)) as count from bb_order_details join bb_unit_details on bb_order_details.partner_order_id = bb_unit_details.partner_order_id where current_status = \'Claim Approved\'', '', 'admin', 1, 'buyback', 1, '2017-11-06 10:46:03'),
(10, 'orders', 'this month', 'last month', 'SELECT (in_transit_count+deliverd_count) as count\nFROM ( \n    SELECT SUM(CASE\n        WHEN current_status = \'Delivered\' AND delivery_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'deliverd_count\',\n    SUM(CASE\n       WHEN current_status IN (\'In-Transit\', \'New Item In-transit\', \'Attempted\') AND order_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' ) THEN 1\n        ELSE 0\n    END) AS \'in_transit_count\' FROM bb_order_details) as a', 'SELECT (in_transit_count+deliverd_count) as count\r\nFROM ( \r\n    SELECT SUM(CASE\r\n        WHEN current_status = \'Delivered\' AND delivery_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND delivery_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' )THEN 1\r\n        ELSE 0\r\n    END) AS \'deliverd_count\',\r\n    SUM(CASE\r\n       WHEN current_status IN (\'In-Transit\', \'New Item In-transit\', \'Attempted\') AND order_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND order_date < DATE_FORMAT( CURRENT_DATE, \'%Y/%m/01\' ) THEN 1\r\n        ELSE 0\r\n    END) AS \'in_transit_count\' FROM bb_order_details) as a', 'admin', 1, 'buyback', 1, '2017-11-06 10:47:05'),
(11, 'OTG_cancelled_by_Ranju', '', '', 'SELECT COUNT(*) as count FROM booking_state_change as bsc, booking_unit_details as bd WHERE `new_state` LIKE \'Cancelled\' AND `agent_id` = 15 AND bsc.`create_date` > \'2016-10-06 00:00:00\' AND bsc.booking_id=bd.booking_id AND bd.service_id=42 AND bsc.remarks = \'Installation Not Required\' AND bd.appliance_description LIKE \'%OTG%\'', '', 'admin', 2, 'service', 1, '2017-11-06 10:53:37'),
(12, 'find_pincodes_and_services_for_all_active_vendors', '', '', 'SELECT V.Appliance, V.`Appliance_ID` , V.`Pincode` , V.Vendor_Name\r\nFROM  `vendor_pincode_mapping` AS V, service_centres AS S\r\nWHERE V.vendor_ID = S.id\r\nAND S.active =1', '', 'admin', 3, 'service', 0, '2017-11-06 10:55:14'),
(13, 'snapdeal_leads', '', '', 'SELECT s.services, appliance_brand, bd.`booking_id` ,  `type` ,  `current_status` , bd.`city` , bd.`state` , bd.`create_date` \r\nFROM  `booking_details` AS bd,  `booking_unit_details` AS ud, services AS s\r\nWHERE bd.`partner_id` =1\r\nAND bd.`create_date` >=  \'2017-01-01 00:00:00\'\r\nAND bd.booking_id = ud.booking_id\r\nAND bd.service_id = s.id\r\nAND product_or_services !=  \'Product\'', '', 'admin', 3, 'service', 0, '2017-11-06 10:55:14'),
(14, 'Queries where SF not Available', '', '', 'SELECT services.services,\r\n            users.name as customername, users.phone_number,\r\n            bd.*\r\n            from booking_details as bd\r\n            JOIN  `users` ON  `users`.`user_id` =  `bd`.`user_id`\r\n            JOIN  `services` ON  `services`.`id` =  `bd`.`service_id`\r\n            WHERE `bd`.booking_id LIKE \'%Q-%\' AND (DATEDIFF(CURRENT_TIMESTAMP , STR_TO_DATE(bd.booking_date, \'%d-%m-%Y\')) >= 0 OR\r\n                bd.booking_date=\'\') AND `bd`.current_status=\'FollowUp\'\r\n                AND NOT EXISTS \r\n                (SELECT 1\r\n                FROM (`vendor_pincode_mapping`)\r\n                JOIN `service_centres` ON `service_centres`.`id` = `vendor_pincode_mapping`.`Vendor_ID`\r\n                WHERE `vendor_pincode_mapping`.`Appliance_ID` = bd.service_id\r\n                AND `vendor_pincode_mapping`.`Pincode` = bd.booking_pincode\r\n                AND `service_centres`.`active` = \'1\' AND `service_centres`.on_off = \'1\') ', '', 'admin', 1, 'service', 0, '2017-11-06 10:56:46'),
(15, 'upcountry_details_for_specific_bookings', '', '', 'SELECT `booking_id`, booking_details.`is_upcountry`, booking_pincode, `upcountry_pincode`, so.district, booking_details.city, sc.name, `sub_vendor_id`, `sf_upcountry_rate`, `upcountry_distance`, `upcountry_partner_approved`, `upcountry_paid_by_customer`, `closing_remarks`\r\nFROM `booking_details` , service_centres as sc, sub_service_center_details as so\r\nWHERE `booking_id` IN (\'SY-476711610241\',\'SY-539601611161\',\'SS-437851611192\',\'SS-582031611161\',\'SS-586011611101\',\'SS-597861611161\',\'SS-630011612041\',\'SS-634061611281\',\'SS-634151611271\',\'SS-639591611291\',\'SS-639751611301\',\'SS-639991611301\',\'SS-643731611271\',\'SS-644781611271\',\'SS-645521611271\',\'SS-588741611272\',\'SS-645871611271\',\'SS-648821611281\',\'SS-648831611281\',\'SY-649251611281\',\'SS-651461612031\',\'SS-651881612041\',\'SY-658001612011\',\'SY-646551611292\',\'SS-658931611301\',\'SS-658971611301\',\'SS-663081612011\',\'SS-670291612011\')\r\nAND booking_details.assigned_vendor_id=sc.id\r\nAND so.id=sub_vendor_id;', '', 'admin', 3, 'service', 0, '2017-11-06 10:57:33'),
(16, 'bookings_unit_completed', 'this month', 'last month', 'SELECT COUNT(*) as count FROM booking_unit_details WHERE booking_status = \'Completed\' \nAND ud_closed_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' )', 'SELECT COUNT(*) as count FROM booking_unit_details WHERE booking_status = \'Completed\'  AND ud_closed_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, \'%Y/%m/01\' ) AND ud_closed_date < DATE_FORMAT( CURRENT_DATE - INTERVAL 0 MONTH, \'%Y/%m/01\' )', 'admin', 1, 'service', 1, '2017-11-06 10:59:51'),
(17, 'repeat_bookings_(promo_sms)', '', '', 'SELECT count(distinct b.`booking_id`) AS count FROM `booking_details` as b, `sms_sent_details` as s WHERE b.`partner_id` = 247001 AND b.`create_date` >= \'2017-05-25 00:00:00\' AND (s.`sms_tag` = \'completed_promotional_sms_1\' OR s.`sms_tag` = \'completed_promotional_sms_2\') AND b.user_id = s.type_id', '', 'admin', 3, 'service', 1, '2017-11-06 11:02:36'),
(18, 'total_completed_repeat_bookings_(Promo SMS)', '', '', 'SELECT count(distinct b.`booking_id`) AS count FROM `booking_details` as b, `sms_sent_details` as s WHERE b.`partner_id` = 247001 AND b.`create_date` >= \'2017-05-25\' AND (s.`sms_tag` = \'completed_promotional_sms_1\' OR s.`sms_tag` = \'completed_promotional_sms_2\') AND b.user_id = s.type_id AND current_status = \'Completed\'', '', 'admin', 3, 'service', 0, '2017-11-06 11:32:50'),
(19, 'paid_completed_repeat_bookings_(Promo SMS)', '', '', 'SELECT count(distinct b.`booking_id`) AS count FROM `booking_details` as b, `sms_sent_details` as s WHERE b.`partner_id` = 247001 AND b.`create_date` >= \'2017-05-25\' AND (s.`sms_tag` = \'completed_promotional_sms_1\' OR s.`sms_tag` = \'completed_promotional_sms_2\') AND b.user_id = s.type_id AND current_status = \'Completed\' AND amount_paid > 0', '', 'admin', 3, 'service', 0, '2017-11-06 11:33:38'),
(20, 'paid_amount_repeat_bookings_(Promo SMS)', '', '', 'SELECT sum(b.`amount_paid`) AS count FROM `booking_details` as b, `sms_sent_details` as s WHERE b.`partner_id` = 247001 AND b.`create_date` >= \'2017-05-25\' AND (s.`sms_tag` = \'completed_promotional_sms_1\' OR s.`sms_tag` = \'completed_promotional_sms_2\') AND b.user_id = s.type_id AND current_status = \'Completed\' AND amount_paid > 0', '', 'admin', 3, 'service', 0, '2017-11-06 11:34:24'),
(21, 'booking_unit_details_has_0_in_column', '', '', 'select count(id) as count FROM (`booking_unit_details`) WHERE (`booking_id` = \'0\' OR `partner_id` = \'0\' OR `appliance_id` = \'0\') AND `create_date` >= \'2017-06-01\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 10:42:54'),
(22, 'check_price_tags', '', '', 'select count(booking_unit_details.id) as count FROM (`booking_unit_details`) JOIN `booking_details` ON `booking_details`.`booking_id` = `booking_unit_details`.`booking_id` WHERE `price_tags` IN (\'\', NULL) AND `booking_details`.`current_status` IN (\'Pending\', \'Completed\', \'Rescheduled\') AND `booking_details`.`create_date` >= \'2016-09-01\' AND `booking_details`.`booking_id` NOT LIKE \'%Q-%\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 10:51:39'),
(23, 'Tax Rate is 0 ', '', '', 'Select count(*) as count FROM (`booking_unit_details`) JOIN `booking_details` ON `booking_details`.`booking_id` = `booking_unit_details`.`booking_id` WHERE `tax_rate` <= \'0\' AND `booking_details`.`current_status` IN (\'Pending\', \'Completed\', \'Rescheduled\') AND `booking_details`.`create_date` >= \'2016-09-01\' AND `booking_unit_details`.`booking_status` IN (\'Completed\', \'\')', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 10:56:59'),
(24, 'booking_status_is_empty', '', '', 'Select count(*) as count FROM (`booking_unit_details`) JOIN `booking_details` ON `booking_details`.`booking_id` = `booking_unit_details`.`booking_id` WHERE `booking_status` IN (\'\', NULL) AND `booking_details`.`current_status` IN (\'Completed\') AND `booking_details`.`create_date` >= \'2016-09-01\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 10:59:24'),
(25, 'booking_id_is_0_in_sc_action_table', '', '', 'Select count(*) as count\r\nFROM (`service_center_booking_action`) WHERE (`booking_id` = \'0\' OR `unit_details_id` IS NULL OR `unit_details_id` = \'0\' OR `service_center_id` = \'0\' OR `current_status` = \'0\' OR `internal_status` = \'0\') AND `create_date` >= \'2016-09-01\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:14:15'),
(26, 'status_is_empty_for_completed_bookings', '', '', 'Select count(*) as count FROM `booking_details` as bd, booking_unit_details as ud WHERE bd.`closed_date` >= \'2016-09-01\' AND bd.`current_status`=\'Completed\' AND bd.`booking_id`=ud.`booking_id` AND ud.`booking_status`=\'\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:20:45'),
(27, 'product_OR_services_field_has_empty', '', '', 'Select count(*) as count FROM `booking_details` as bd, booking_unit_details as ud WHERE bd.`closed_date` >= \'2016-09-01\' AND bd.`current_status`=\'Completed\' AND bd.`booking_id`=ud.`booking_id` AND ud.`product_or_services`=\'\';', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:23:28'),
(28, 'prices_has_negative_value ', '', '', 'Select count(*) as count FROM booking_unit_details where (customer_net_payable <0 OR customer_total < 0 OR partner_net_payable <0 OR around_net_payable <0 OR customer_paid_basic_charges< 0 OR partner_paid_basic_charges<0 OR around_paid_basic_charges<0 OR around_comm_basic_charges<0 OR around_st_or_vat_basic_charges<0 OR vendor_basic_charges <0 OR vendor_to_around <0 OR around_to_vendor<0 OR vendor_st_or_vat_basic_charges<0 OR customer_paid_extra_charges< 0 OR around_comm_extra_charges<0 OR around_st_extra_charges<0 OR vendor_extra_charges< 0 OR vendor_st_extra_charges<0 OR customer_paid_parts<0 OR around_comm_parts<0 OR around_st_parts<0 OR vendor_parts<0 OR vendor_st_parts<0) AND create_date >= \'2016-09-01\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:27:37'),
(29, 'partner_paid_basic_charge_is_not_correct', '', '', 'Select count(*) as count FROM `booking_unit_details` WHERE `partner_net_payable` >0 AND `partner_paid_basic_charges` != ( partner_net_payable + ( `partner_net_payable` * `tax_rate` ) /100 ) AND create_date >= \'2016-09-01\' AND booking_status = \'Completed\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:33:30'),
(30, 'service_which_was_closed_at_0_prices ', '', '', 'Select count(*) as count FROM `booking_unit_details` WHERE `booking_status` LIKE \'Completed\' AND `customer_net_payable` >0 AND `customer_paid_basic_charges` =0 AND create_date >= \'2016-09-01\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:37:36'),
(31, 'stand_is_not_added_in_the_unit_details', '', '', 'Select count(*) as count FROM booking_details AS b1, booking_unit_details AS u1 WHERE u1.appliance_brand IN ( \'Sony\', \'Panasonic\', \'LG\', \'Samsung\' ) AND b1.current_status = \'Completed\' AND b1.closed_date >= \'2016-09-01\' AND b1.service_id = \'46\' AND b1.booking_id = u1.booking_id AND b1.current_status != \'Cancelled\' AND b1.partner_id IN ( \'1\', \'3\' ) AND NOT EXISTS ( SELECT * FROM booking_unit_details AS u2 WHERE b1.booking_id = u2.booking_id AND u2.price_tags = \'Wall Mount Stand\' ) ORDER BY `b1`.`create_date` DESC', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:39:45'),
(32, 'duplicate_entry_in_unit_details ', '', '', 'Select count(*) as count FROM `booking_unit_details` AS b1, `booking_unit_details` AS b2 WHERE b1.`booking_id` = b2.`booking_id` AND b1.`price_tags` = b2.`price_tags` AND b1.id != b2.id AND b1.create_date >= \'2016-09-01\' AND (b1.booking_status != \'Cancelled\' OR b2.booking_status != \'Cancelled\' ) AND (b2.booking_status IN ( \'Completed\', \'\' ) OR b1.booking_status IN ( \'Completed\', \'\' ))', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:41:47'),
(33, 'booking_Id_is_not_exist_in_unit_details', '', '', 'Select count(*) as count FROM booking_details AS b1 WHERE NOT EXISTS ( SELECT booking_id FROM booking_unit_details WHERE b1.booking_id = booking_unit_details.booking_id ) AND create_date >= \'2016-09-01\' AND b1.current_status != \'Cancelled\'', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:43:48'),
(34, 'customer_total_is_zero', '', '', 'Select count(*) as count FROM booking_unit_details, booking_details WHERE customer_total =0 AND booking_details.create_date >= \'2016-10-01\' AND booking_details.booking_id = booking_unit_details.booking_id AND booking_details.current_status IN ( \'Pending\', \'Rescheduled\', \'Completed\' ) AND booking_status IN ( \'Completed\', \'\')', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:45:39'),
(35, 'pending_bookings_without_job_cards', '', '', 'Select count(*) as count FROM `booking_details` WHERE `current_status` IN ( \'Pending\', \'Rescheduled\' ) AND `assigned_vendor_id` IS NOT NULL AND `booking_jobcard_filename` IS NULL', '', 'developer', 1, 'incorrect_data', 1, '2017-11-07 11:47:35');


ALTER TABLE `query_report`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `query_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;


ALTER TABLE `sf_not_exist_booking_details` ADD `partner_id` INT NULL AFTER `active_flag`;

ALTER TABLE `sf_not_exist_booking_details` ADD `partner_id` INT NULL AFTER `active_flag`;--Abhay 8 NOV

-- 08 Nov Sachin 

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'missing_pincode_mail', '%s file has incorrect pincode ', 'Dear Partner,<br><br>

Please have a look in <b>%s</b> file. It has incorrect pincodes. <br><br>
Find the below order id in the attached file and send us file with correct pincode.<br><br>
%s', 'booking@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);


ALTER TABLE `sf_not_exist_booking_details` ADD `partner_id` INT NULL AFTER `active_flag`;


--Abhay 8 NOV
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'oow_estimate_given', 'Repair OOW Parts Estimate Sent By Partner For Booking ID %s', 'Spare Estimate Amount: Rs. %s', 'noreply@247around.com', '', 'abhaya@247around', '', '1', '2017-11-02 23:56:57');


--Abhay 10 Nov
ALTER TABLE `sub_service_center_details` ADD `active` INT(1) NOT NULL DEFAULT '1' AFTER `create_date`;




--Abhay 14 NOV
ALTER TABLE `query_report` ADD `result` VARCHAR(2048) NULL DEFAULT NULL AFTER `active`;

INSERT INTO `query_report` (`id`, `main_description`, `query1_description`, `query2_description`, `query1`, `query2`, `role`, `priority`, `type`, `active`, `result`, `create_date`) VALUES (NULL, 'Invoice Check', '', '', '', '', 'developer', '1', 'invoice_check', '1', NULL, '2017-11-14 11:14:15');




-- ANUJ 17 NOV
ALTER TABLE  `bank_transactions` ADD  `transaction_id` VARCHAR( 32 ) NOT NULL COMMENT  'Bank Transaction ID' AFTER  `transaction_mode` ;

-- 16 Nov Sachin
CREATE TABLE `247around`.`email_attachment_parser` 
( `id` INT(11) NOT NULL , 
`email_received_from` VARCHAR(256) NOT NULL , 
`email_subject_text` VARCHAR(256) NULL DEFAULT NULL , 
`email_function_name` VARCHAR(256) NULL DEFAULT NULL , 
`email_remarks` VARCHAR(64) NULL DEFAULT NULL , 
`create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `email_attachment_parser` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `file_uploads` ADD `email_message_id` VARCHAR(256) NULL DEFAULT NULL AFTER `result`;
ALTER TABLE `email_attachment_parser` ADD `active` TINYINT(5) NOT NULL DEFAULT '0' AFTER `email_remarks`;

--20th-nov
CREATE TABLE `collateral` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(20) NOT NULL,
  `collateral_id` varchar(100) DEFAULT NULL,
  `document_description` text,
  `file` text,
  `version` varchar(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_valid` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `collateral`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `collateral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;


CREATE TABLE `collateral_type` (
  `id` int(11) NOT NULL,
  `collateral_tag` varchar(80) DEFAULT NULL,
  `collateral_type` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `collateral_type` (`id`, `collateral_tag`, `collateral_type`) VALUES
(1, 'Contract', 'NDA(None Disclosure Agreement)'),
(2, 'Contract', 'Work Order'),
(3, 'Contract', 'MSA (Master Service Agreement)'),
(4, 'Contract', 'Addendum'),
(5, 'Contract', 'Extension');

ALTER TABLE `collateral_type`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `collateral_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;COMMIT;

ALTER TABLE `sf_not_exist_booking_details` ADD `partner_id` INT NULL AFTER `active_flag`;

-- Table structure for table `account_holders_bank_details`
--

CREATE TABLE `account_holders_bank_details` (
  `id` int(11) NOT NULL,
  `entity_id` varchar(20) NOT NULL,
  `entity_type` varchar(20) NOT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_type` varchar(20) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `cancelled_cheque_file` text,
  `beneficiary_name` varchar(50) DEFAULT NULL,
  `is_verified` int(10) NOT NULL DEFAULT '0',
  `agent_id` int(10) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_holders_bank_details`
--
ALTER TABLE `account_holders_bank_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_holders_bank_details`
--
ALTER TABLE `account_holders_bank_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;
ALTER TABLE `email_attachment_parser` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `file_uploads` ADD `email_message_id` VARCHAR(256) NULL DEFAULT NULL AFTER `result`;
ALTER TABLE `email_attachment_parser` ADD `active` TINYINT(5) NOT NULL DEFAULT '0' AFTER `email_remarks`;

ALTER TABLE `partners` ADD `agent_id` INT(10) NOT NULL AFTER `create_date`;
ALTER TABLE `partners` ADD `update_date` DATETIME NOT NULL AFTER `agent_id`;
ALTER TABLE `trigger_partners` ADD `agent_id` INT(10) NOT NULL AFTER `create_date`;
ALTER TABLE `trigger_partners` ADD `update_date` DATETIME NOT NULL AFTER `agent_id`;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'zopper_estimate_send', 'zopper_estimate_send', 'Please Find Attachment.', 'sales@247around.com', 'sachinj@247around.com', 'abhaya@247around', '', '1', '2017-11-02 23:56:57');

ALTER TABLE `zopper_estimate_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--- sachin 24 Nov

INSERT INTO `email_attachment_parser` (`id`, `email_received_from`, `email_subject_text`, `email_function_name`, `email_remarks`, `active`, `create_date`) VALUES
(1, 'anuj@247around.com', 'Amazon Exchange Offer', 'buyback/upload_buyback_process/process_upload_order', 'amazon', 1, '2017-11-16 10:55:48'),
(2, 'sachinj@247around.com', 'Order delivery status ', 'buyback/upload_buyback_process/process_upload_order', 'amazon', 1, '2017-11-17 12:17:23'),
(3, 'sachinj@247around.com', 'shipped orders report', 'employee/bookings_excel/upload_booking_for_paytm', 'paytm', 1, '2017-11-18 05:05:33');


-- sachin 26 nov
INSERT INTO `email_attachment_parser` (`id`, `email_received_from`, `email_subject_text`, `email_function_name`, `email_remarks`, `active`, `create_date`) VALUES (NULL, 'sachinj@247around.com', 'snapdeal file', 'employee/do_background_upload_excel/upload_snapdeal_file', 'snapdeal', '1', CURRENT_TIMESTAMP);

-- sachin 29 nov
INSERT INTO `email_attachment_parser` (`id`, `email_received_from`, `email_subject_text`, `email_function_name`, `email_remarks`, `active`, `create_date`) VALUES (NULL, 'sachinj@247around.com', 'wybor file', 'employee/do_background_upload_excel/upload_satya_file', 'wybor', '1', CURRENT_TIMESTAMP);

---Chhavi 
ALTER TABLE `vendor_pincode_mapping` DROP `Vendor_Name`;
ALTER TABLE `vendor_pincode_mapping` DROP `Appliance`;
ALTER TABLE `vendor_pincode_mapping` DROP `Brand`,DROP `Area`,DROP `Region`;


--Abhay 29 Nov
ALTER TABLE `service_centre_charges` CHANGE `create_date` `create_date` DATETIME NULL DEFAULT NULL;
ALTER TABLE `service_centre_charges` ADD `update_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `create_date`, ADD `agent_id` INT(11) NULL DEFAULT NULL AFTER `update_date`;


CREATE TRIGGER `t_service_charge` BEFORE UPDATE ON `service_centre_charges`
 FOR EACH ROW BEGIN INSERT INTO trigger_service_charges (SELECT service_centre_charges.*,  CURRENT_TIMESTAMP AS current_updated_date FROM service_centre_charges WHERE service_centre_charges.id = NEW.id); END

ALTER TABLE `partner_appliance_details` CHANGE `create_date` `create_date` DATETIME NULL DEFAULT NULL;
ALTER TABLE `partner_appliance_details` ADD `update_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `create_date`;


-- --------------------------------------------------------

--
-- Table structure for table `trigger_service_charges`
--

CREATE TABLE `trigger_service_charges` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `state` varchar(50) NOT NULL,
  `service_id` varchar(10) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(150) DEFAULT NULL,
  `capacity` varchar(50) DEFAULT NULL,
  `service_category` varchar(100) NOT NULL,
  `product_or_services` varchar(10) NOT NULL,
  `product_type` varchar(50) DEFAULT NULL,
  `tax_code` varchar(10) NOT NULL,
  `active` varchar(2) NOT NULL,
  `check_box` varchar(2) NOT NULL,
  `pod_required` varchar(10) NOT NULL DEFAULT '0',
  `vendor_basic_charges` decimal(10,2) NOT NULL,
  `vendor_tax_basic_charges` decimal(10,2) NOT NULL,
  `vendor_total` decimal(10,2) NOT NULL,
  `vendor_basic_percentage` decimal(10,3) DEFAULT NULL,
  `around_basic_charges` decimal(10,2) NOT NULL,
  `around_tax_basic_charges` decimal(10,2) NOT NULL,
  `around_total` decimal(10,2) NOT NULL,
  `customer_total` decimal(10,2) NOT NULL,
  `partner_payable_basic` decimal(10,2) NOT NULL,
  `partner_payable_tax` decimal(10,2) NOT NULL,
  `partner_net_payable` decimal(10,2) NOT NULL,
  `customer_net_payable` decimal(10,2) NOT NULL,
  `pod` varchar(10) NOT NULL COMMENT 'Proof of delivery, If flag is 1 then we will make required serial number',
  `is_upcountry` int(2) DEFAULT '0',
  `create_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `current_update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `agent_id` int(11) DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `trigger_service_charges`
--
ALTER TABLE `trigger_service_charges`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `trigger_service_charges`
--
ALTER TABLE `trigger_service_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

<--01-Dec-2017,Chhavi-->
ALTER TABLE `dealer_details` ADD `state` VARCHAR(100) NOT NULL AFTER `city`;


INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'negative_foc_invoice_for_vendors', '247around - %s - FOC Invoice for period: %s to %s', 'Dear Partner, Your ... Negative Invoice Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice. <br/><br/>Thanks,<br/>247around Team', 'billing@247around.com', '', 'abhaya@247around', '', '1', '2017-12-01 23:56:58');
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'resend_invoice', '247around - Invoice for period: %s to %s', 'Dear Partner <br/><br/> Please find attached invoice for jobs completed between %s and %s.<br/><br/> Details with breakup by job, service category is attached. Also the service rating as given by customers is shown.<br/><br/> Hope to have a long lasting working relationship with you. Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice. <br/><br/>With Regards,<br/>247around Team', 'billing@247around.com', '', 'abhaya@247around', '', '1', '2017-12-01 23:56:58');


ALTER TABLE `trigger_partners` ADD `updated_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `update_date`;


INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'negative_foc_invoice_for_vendors', '247around - %s - FOC Invoice for period: %s to %s', 'Dear Partner, Your ... Negative Invoice Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice. <br/><br/>Thanks,<br/>247around Team', 'billing@247around.com', '', 'abhaya@247around', '', '1', '2017-12-01 23:56:58');

-- sachin 29 nov
INSERT INTO `email_attachment_parser` (`id`, `email_received_from`, `email_subject_text`, `email_function_name`, `email_remarks`, `active`, `create_date`) VALUES (NULL, 'sachinj@247around.com', 'wybor file', 'employee/do_background_upload_excel/upload_satya_file', 'wybor', '1', CURRENT_TIMESTAMP);

-- sachin 30 Nov

CREATE TABLE `inventory_master_list` (
  `id` int(11) NOT NULL,
  `part_number` varchar(256) NOT NULL,
  `part_name` varchar(256) NOT NULL,
  `model_number` varchar(256) NOT NULL,
  `serial_number` varchar(256) NOT NULL,
  `description` varchar(512) NOT NULL,
  `size` varchar(128) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `type` varchar(64) DEFAULT NULL,
  `sender_entity_id` int(11) NOT NULL,
  `sender_entity_type` varchar(64) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory_master_list`
--

INSERT INTO `inventory_master_list` (`id`, `part_number`, `part_name`, `model_number`, `serial_number`, `description`, `size`, `price`, `type`, `sender_entity_id`, `sender_entity_type`, `create_date`) VALUES
(1, 'B-24732', 'Bracket', '', '', 'Brackets less than 32"', '', '0.00', 'Bracket', 0, '', '2017-11-30 06:59:43'),
(2, 'B-24733', 'Bracket', '', '', 'Brackets greater than 32"', '', '0.00', 'Bracket', 0, '', '2017-11-30 07:01:12');

ALTER TABLE `inventory_master_list`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inventory_master_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

CREATE TABLE `inventory_ledger` (
  `id` int(11) NOT NULL,
  `receiver_entity_id` int(11) DEFAULT NULL,
  `receiver_entity_type` varchar(64) DEFAULT NULL,
  `sender_entity_id` int(11) DEFAULT NULL,
  `sender_entity_type` varchar(64) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `part_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `agent_type` varchar(64) NOT NULL,
  `order_id` varchar(32) DEFAULT NULL,
  `booking_id` varchar(64) DEFAULT NULL,
  `invoice_id` varchar(255) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `inventory_ledger`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inventory_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `inventory_stocks` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(64) NOT NULL,
  `part_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `inventory_stocks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inventory_stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--sachin 05 dec
ALTER TABLE `email_attachment_parser` ADD `email_send_to` VARCHAR(256) NULL AFTER `email_remarks`;


--Abhay
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'notification_to_send_defective_parts', 'Pending Defective Parts - %s', 'Dear Partner <br/><br/> Please Send Defective Parts for below Booking. %s <br/><br/>With Regards,<br/>247around Team', 'booking@247around.com', '', 'abhaya@247around', '', '1', '2017-12-11 23:56:58');



INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Customer asked to reschedule', 'Customer asked to reschedule', 'Customer asked to reschedule');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Product not delivered to customer', 'Product not delivered to customer', 'Product not delivered to customer');


INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Spare Estimate Approved By Customer', 'Spare Estimate Approved By Customer', 'Spare Estimate Approved By Customer');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Spare Parts Requested', 'Spare Parts Requested', 'Spare Parts Requested');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Request Quote for Spare Part', 'Request Quote for Spare Part', 'Request Quote for Spare Part');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Customer not reachable / Customer not picked phone', 'Customer not reachable / Customer not picked phone', 'Customer not reachable / Customer not picked phone');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Engineer on route', 'Engineer on route', 'Engineer on route');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Spare Parts Delivered to SF', 'Spare Parts Delivered to SF', 'Spare Parts Delivered to SF');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'InProcess_Completed', 'InProcess_Completed', 'InProcess_Completed');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247010', 'Pending', 'Defective Part Pending', 'Defective Part Pending', 'Defective Part Pending');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'InProcess_Cancelled', 'InProcess_Cancelled', 'InProcess_Cancelled');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'Spare Estimate Cost Given', 'Spare Estimate Cost Given', 'Spare Estimate Cost Given');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'Booking Opened From Completed', 'Booking Opened From Completed', 'Booking Opened From Completed');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'Booking Opened From Cancelled', 'Booking Opened From Cancelled', 'Booking Opened From Cancelled');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'Cancelled Query to FollowUp', 'Cancelled Query to FollowUp', 'Cancelled Query to FollowUp');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'Spare Parts Shipped by Partner', 'Spare Parts Shipped by Partner', 'Spare Parts Shipped by Partner');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'Defective Part Received By Partner', 'Defective Part Received By Partner', 'Defective Part Received By Partner');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'Defective Part Rejected By Partner', 'Defective Part Rejected By Partner', 'Defective Part Rejected By Partner');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'Assigned_vendor', 'Assigned_vendor', 'Assigned_vendor');

INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`) VALUES (NULL, '247001', 'Pending', 'UPCOUNTRY BOOKING NEED TO APPROVAL', 'UPCOUNTRY BOOKING NEED TO APPROVAL', 'UPCOUNTRY BOOKING NEED TO APPROVAL');

--sachin 11 dec
ALTER TABLE `email_attachment_parser` ADD `file_type` VARCHAR(128) NULL AFTER `email_function_name`;
UPDATE `email_attachment_parser` SET `email_function_name` = 'employee/do_background_upload_excel/process_upload_file' WHERE `email_attachment_parser`.`id` = 5;
UPDATE `email_attachment_parser` SET `file_type` = 'Satya-Delivered' WHERE `email_attachment_parser`.`id` = 5;
INSERT INTO `email_attachment_parser` 
(`id`, `email_received_from`, `email_subject_text`, `email_function_name`, 
`file_type`, `email_remarks`, `email_send_to`, `active`, `create_date`) 
VALUES (NULL, 'sachinj@247around.com', 'akai file', 'employee/do_background_upload_excel/process_upload_file', 'Akai-Delivered', 'Akai', 'dfg@247around.com', '1', '2017-11-29 11:42:30');



--
-- Table structure for table `blacklist_brand`
--

CREATE TABLE `blacklist_brand` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `brand` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blacklist_brand`
--

INSERT INTO `blacklist_brand` (`id`, `partner_id`, `service_id`, `brand`) VALUES
(1, 1, 46, 'Weston'),
(2, 1, 46, 'Activa'),
(3, 1, 46, 'Polaroid'),
(4, 1, 46, 'ITH'),
(5, 1, 46, 'Digismart'),
(6, 1, 46, 'Dutsun'),
(7, 1, 46, 'I Grasp'),
(8, 1, 46, 'Grasp');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blacklist_brand`
--
ALTER TABLE `blacklist_brand`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blacklist_brand`
--
ALTER TABLE `blacklist_brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;


-- sachin 18 dec
INSERT INTO `email_template` 
(`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'reset_vendor_login_details', 'Your Password Reset Request Processed Successfully - 247Around', 'Dear Partner,<br><br> Your password has been reset as per 247around security policy.<br><br> URL: <a href="https://www.aroundhomzapp.com/service_center/login">https://www.aroundhomzapp.com/service_center/login</a><br><br> <b>Username: </b>%s<br><b>Password: </b>%s<br><br> Please use the ERP panel for your closures going forward. In case of any issues, write to us or call us.<br><br> Regards,<br> 247around Team', 'noreply@247around.com', 'sachinj@247around.com', 'anuj@247around.com,nits@247around.com', '', '1', '2016-09-27 00:00:00');

-- Abhay 18 dec
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'spare_invoice_sent', '247around - %s. Invoice for period: %s to %s', 'Dear Partner, <br/> <br/> Please find attached invoice for Spare Parts. <br/> <br/> With Regards, <br>247around Team<br/> <br>247around is part of Businessworld Startup Accelerator & Google Bootcamp 2015<br/> Follow us on Facebook: www.facebook.com/247around<br/> Website: www.247around.com<br/> Playstore - 247around -<br/> https://play.google.com/store/apps/details?id=com.handymanapp<br/>', 'billing@247around.com', '', 'nits@247around.com,anuj@247around.com,abhaya@247around.com', '', '1', '2017-12-18 23:56:57');
ALTER TABLE `booking_unit_details` ADD `pay_from_sf` INT(1) NOT NULL DEFAULT '1' AFTER `pay_to_sf`;


UPDATE `email_template` SET `template` = 'Dear Partner<br><br> Please find below your updated login details.<br><br> <b>Username: </b>%s<br><b>Password: </b>%s<br><br> Please use the ERP panel for your closures going forward. In case of any issues, write to us or call us.<br><br> Regards,<br> 247around Team' WHERE `email_template`.`id` = 47;
UPDATE `email_template` SET `subject` = 'New Login Details - 247around' WHERE `email_template`.`id` = 47;

--sachin 19 dec
ALTER TABLE `inventory_master_list` CHANGE `sender_entity_id` `entity_id` INT(11) NOT NULL;
ALTER TABLE `inventory_master_list` CHANGE `sender_entity_type` `entity_type` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `inventory_ledger` CHANGE `part_id` `inventory_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `inventory_ledger` ADD `remarks` VARCHAR(1024) NULL AFTER `invoice_id`, ADD `active` TINYINT NOT NULL DEFAULT '1' AFTER `remarks`;
ALTER TABLE `inventory_master_list` CHANGE `id` `inventory_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `inventory_stocks` CHANGE `part_id` `inventory_id` INT(11) NOT NULL;
ALTER TABLE `booking_unit_details` ADD `inventory_id` INT(11) NULL DEFAULT NULL AFTER `is_spare_parts`;


--Abhay 28 Dec
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'prepaid_low_balance', 'Dear partner, your current balance is running low. Please recharge your account urgently for uninterrupted service. 247around Team', '', '1', CURRENT_TIMESTAMP);

--Chhavi 29th Dec
CREATE TABLE `header_navigation` (
  `id` int(30) NOT NULL,
  `title` varchar(80) NOT NULL,
  `link` text,
  `level` int(10) NOT NULL,
  `parent_ids` varchar(30) DEFAULT NULL,
  `groups` varchar(200) NOT NULL,
  `nav_type` varchar(20) NOT NULL DEFAULT 'main_nav',
  `is_active` int(10) NOT NULL DEFAULT '1',
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `header_navigation`
--

INSERT INTO `header_navigation` (`id`, `title`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
(1, 'Find User', 'employee/user', 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:48:40'),
(2, 'Queries', NULL, 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:48:40'),
(3, 'Pending Queries (Pincode Available)', 'employee/booking/view_queries/FollowUp/p_av', 2, '2', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:48:40'),
(4, 'Missed Calls', 'employee/booking/get_missed_calls_view', 2, '2', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 18:48:40'),
(5, 'Pending Queries (Pincode Not Available)', 'employee/booking/view_queries/FollowUp/p_nav', 2, '2', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:48:40'),
(6, 'Cancelled Queries', 'employee/booking/view_queries/Cancelled/p_all', 2, '2', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:49:39'),
(7, 'Bookings', NULL, 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:50:26'),
(8, 'Pending Bookings', 'employee/booking/view_bookings_by_status/Pending', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:51:18'),
(9, 'Spare Parts Bookings', 'employee/inventory/get_spare_parts', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:52:14'),
(10, 'OOW Bookings', 'employee/booking/get_oow_booking', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 18:52:54'),
(11, 'Completed Bookings', 'employee/booking/view_bookings_by_status/Completed', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:53:49'),
(12, 'Cancelled Bookings', 'employee/booking/view_bookings_by_status/Cancelled', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:54:30'),
(13, 'Repair Bookings', 'employee/booking/get_pending_booking_by_partner_id', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 18:55:07'),
(14, 'Assign Vendor', 'employee/vendor/get_assign_booking_form', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:55:47'),
(15, 'Review Bookings', 'employee/booking/review_bookings', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:56:19'),
(16, 'Wall Mount Fiven', 'employee/booking/update_not_pay_to_sf_booking', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 18:56:56'),
(17, 'Auto Assign Bookings', 'employee/booking/auto_assigned_booking', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 18:57:37'),
(18, 'Waiting to Approve Upcountry Bookings', 'employee/upcountry/get_waiting_for_approval_upcountry_charges', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 18:58:36'),
(19, 'Upcountry Failed Bookings', 'employee/upcountry/get_upcountry_failed_details', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 19:00:21'),
(20, 'Reassign Partner', 'employee/vendor/get_reassign_partner_form', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 19:01:04'),
(21, 'Missed Call Rating', 'employee/booking/show_missed_call_rating_data', 2, '7', 'admin,closure,developer', 'main_nav', 1, '2017-12-28 19:01:41'),
(22, 'Advance Search', 'employee/booking/booking_advance_search', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 19:02:25'),
(23, 'Bulk Search', 'employee/booking/booking_bulk_search', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 19:03:05'),
(24, 'Partners', NULL, 1, NULL, 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 19:03:35'),
(25, 'View Partners List', 'employee/partner/viewpartner', 2, '24', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 19:04:21'),
(26, 'Send Mail From Template', 'employee/vendor/get_mail_to_vendors_form', 2, '24', 'admin,developer', 'main_nav', 1, '2017-12-28 19:05:36'),
(27, 'Upload Snapdeal Products - Delivered', 'employee/bookings_excel', 2, '24', 'admin,closure,developer', 'main_nav', 1, '2017-12-28 19:07:08'),
(28, 'Upload Snapdeal Products - Shipped', 'employee/bookings_excel/upload_shipped_products_excel', 2, '24', 'admin,closure,developer', 'main_nav', 1, '2017-12-28 19:07:53'),
(29, 'Upload paytm Bookings', 'employee/bookings_excel/upload_delivered_products_for_paytm_excel', 2, '24', 'admin,closure,developer', 'main_nav', 1, '2017-12-28 19:08:34'),
(30, 'Upload Jeeves Bookings', 'employee/upload_booking_file/upload_booking_files', 2, '24', 'admin,closure,developer', 'main_nav', 1, '2017-12-28 19:09:14'),
(31, 'Upload Satya File', 'employee/bookings_excel/upload_satya_file', 2, '24', 'admin,closure,developer', 'main_nav', 1, '2017-12-28 19:09:42'),
(32, 'Upload Akai File', 'employee/bookings_excel/upload_akai_file', 2, '24', 'admin,closure,developer', 'main_nav', 1, '2017-12-28 19:10:10'),
(33, 'Partner Price List', 'employee/service_centre_charges/show_partner_service_price', 2, '24', 'admin,developer', 'main_nav', 1, '2017-12-28 19:10:39'),
(34, 'View Dealer List', 'employee/dealers/show_dealer_list', 2, '24', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-28 19:11:23'),
(35, 'Add Brackets Data', 'employee/partner/bracket_allocation', 2, '24', 'admin,developer', 'main_nav', 1, '2017-12-28 19:12:04'),
(36, 'Service Centers', NULL, 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 10:58:17'),
(37, 'View Service Centers', 'employee/vendor/viewvendor', 2, '36', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 10:59:01'),
(38, 'Search Service Centers', 'employee/vendor/vendor_availability_form', 2, '36', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 10:59:37'),
(39, 'Edit Template', NULL, 2, '36', 'admin,developer', 'main_nav', 1, '2017-12-29 11:00:25'),
(40, 'SMS Template Grid', 'employee/vendor/get_sms_template_editable_grid', 3, '39', 'admin,developer', 'main_nav', 1, '2017-12-29 11:01:25'),
(41, 'TAX RATES Templates Grid', 'employee/vendor/get_tax_rates_template_editable_grid', 3, '39', 'admin,developer', 'main_nav', 1, '2017-12-29 11:02:20'),
(42, 'Vendor Escalation Policy Template Grid', 'employee/vendor/get_tax_rates_template_editable_grid', 3, '39', 'admin,developer', 'main_nav', 1, '2017-12-29 11:03:15'),
(43, 'Appliance Description Template Grid ', 'employee/booking/get_appliance_description_editable_grid', 3, '39', 'admin,developer', 'main_nav', 1, '2017-12-29 11:04:13'),
(44, 'Send Broadcast Email', 'employee/vendor/get_broadcast_mail_to_vendors_form', 2, '36', 'admin,developer', 'main_nav', 1, '2017-12-29 11:05:17'),
(45, 'Send Mail from Template', 'employee/vendor/get_mail_to_vendors_form', 2, '36', 'admin,developer', 'main_nav', 1, '2017-12-29 11:06:19'),
(46, 'Download SF List', 'employee/vendor/download_sf_list_excel', 2, '36', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:07:10'),
(47, 'Engineers', NULL, 2, '36', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:08:01'),
(48, 'Add Engineer', 'employee/vendor/add_engineer', 3, '47', 'admin,callcenter,closure,regionalmanager', 'main_nav', 1, '2017-12-29 11:08:50'),
(49, 'View Engineers', 'employee/vendor/get_engineers', 3, '47', 'admin,callcenter,closure,regionalmanager', 'main_nav', 1, '2017-12-29 11:09:36'),
(50, 'Update Pincode Distance', 'employee/upcountry/get_distance_between_pincodes_form', 2, '36', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:10:18'),
(51, 'Bank Details', 'employee/vendor/show_bank_details', 2, '36', 'admin,developer', 'main_nav', 1, '2017-12-29 11:10:48'),
(52, 'Appliances', NULL, 1, NULL, 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-29 11:11:17'),
(53, 'Add New Brands', 'employee/booking/get_add_new_brand_form', 2, '52', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-29 11:11:55'),
(54, 'Upload Service Charges / Taxes Excel', 'employee/service_centre_charges/upload_excel_form', 2, '52', 'admin,developer', 'main_nav', 1, '2017-12-29 11:12:41'),
(55, 'Update Zooper Price', 'employee/inventory/update_part_price_details', 2, '52', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 11:13:27'),
(56, 'Invoices', NULL, 1, NULL, 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:13:59'),
(57, 'Generate Invoices ', 'employee/invoice/get_invoices_form', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 11:14:47'),
(58, 'Add New Transaction', 'employee/invoice/get_add_new_transaction', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 11:15:22'),
(59, 'Add Advance Bank Transaction', 'employee/invoice/get_advance_bank_transaction', 2, '56', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 11:15:57'),
(60, 'Search Invoice ID', 'employee/accounting/show_search_invoice_id_view', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 11:16:31'),
(61, 'Create Brackets Credit Notes', 'employee/invoice/show_purchase_brackets_credit_note_form', 2, '56', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 11:17:14'),
(62, 'Search Bank Transaction', 'employee/accounting/search_bank_transaction', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 11:17:49'),
(63, 'Partner', NULL, 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 11:18:25'),
(64, 'Create Partner Invoice', 'employee/invoice/insert_update_invoice/partner', 3, '63', 'admin,developer', 'main_nav', 1, '2017-12-29 11:19:02'),
(65, 'Partner Invoices', 'employee/invoice/invoice_partner_view', 3, '63', 'admin,developer', 'main_nav', 1, '2017-12-29 11:19:33'),
(66, 'Partner Transactions', 'employee/invoice/show_all_transactions/partner', 3, '63', 'admin,developer', 'main_nav', 1, '2017-12-29 11:20:08'),
(67, 'Partner Invoice Check', 'employee/invoiceDashboard', 3, '63', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 11:20:41'),
(68, 'Partner Invoice Summary', 'employee/invoiceDashboard/get_invoice_summary_for_partner', 3, '63', 'admin', 'main_nav', 1, '2017-12-29 11:21:15'),
(69, 'Service Center', NULL, 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 11:23:58'),
(70, 'Create SF Invoice', 'employee/invoice/insert_update_invoice/vendor', 3, '69', 'admin', 'main_nav', 1, '2017-12-29 11:25:25'),
(71, 'Service Centers Invoices', 'employee/invoice', 3, '69', 'admin', 'main_nav', 1, '2017-12-29 11:27:20'),
(72, 'Service Centers Transactions', 'employee/invoice/show_all_transactions/vendor', 3, '69', 'admin,closure', 'main_nav', 1, '2017-12-29 11:28:14'),
(73, 'SF Invoice Check', 'employee/invoiceDashboard/service_center_invoice', 3, '69', 'admin,closure', 'main_nav', 1, '2017-12-29 11:28:49'),
(74, 'SF Invoice Summary', 'employee/invoiceDashboard/get_invoice_summary_for_sf', 3, '69', 'admin', 'main_nav', 1, '2017-12-29 11:29:28'),
(75, 'Accounts', NULL, 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 11:29:55'),
(76, 'Upload Challan', 'employee/accounting/get_challan_upload_form', 3, '75', 'admin', 'main_nav', 1, '2017-12-29 11:30:27'),
(77, 'Challan History', 'employee/accounting/get_challan_details', 3, '75', 'admin', 'main_nav', 1, '2017-12-29 11:31:19'),
(78, 'Invoice Summary Report', 'employee/accounting/accounting_report', 3, '75', 'admin', 'main_nav', 1, '2017-12-29 11:32:11'),
(79, 'Search Challan ID', 'employee/accounting/show_search_challan_id_view', 3, '75', 'admin', 'main_nav', 1, '2017-12-29 11:32:45'),
(80, 'Reports', NULL, 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:33:14'),
(81, 'SF Bookings Snapshot', 'employee/vendor/show_service_center_report', 2, '80', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 11:33:49'),
(82, 'Newly Added SF (2 Months)', 'employee/vendor/new_service_center_report', 2, '80', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:34:33'),
(83, 'Download SF Pending Summary', 'BookingSummary/get_pending_bookings/0', 2, '80', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:35:11'),
(84, 'SF Missed Target Reports', 'BookingSummary/get_sc_crimes/0', 2, '80', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:35:51'),
(85, 'RM Crimes Report', 'BookingSummary/get_rm_crimes/0', 2, '80', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:36:20'),
(86, 'RM Performance Stats', 'BookingSummary/show_reports_chart', 2, '80', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:36:52'),
(87, 'New Dashboard', 'employee/dashboard', 2, '80', 'admin,developer', 'main_nav', 1, '2017-12-29 11:37:34'),
(88, 'Download serviceability Report', 'employee/vendor/get_sms_template_editable_grid', 2, '80', 'admin,closure,developer,regionalmanager', 'main_nav', 0, '2017-12-29 11:38:11'),
(89, 'Inventory', NULL, 1, NULL, 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:38:44'),
(90, 'Add Brackets', 'employee/inventory/get_bracket_add_form', 2, '89', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:39:13'),
(91, 'Show Bracket List', 'employee/inventory/show_brackets_list', 2, '89', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 11:39:42'),
(92, 'Vendor Inventory Details', 'employee/inventory/get_vendor_inventory_list_form', 2, '89', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 11:40:17'),
(93, 'Add Employee', 'employee/user/add_employee', 1, NULL, 'admin', 'right_nav', 1, '2017-12-29 12:02:05'),
(94, 'Employee List', 'employee/user/show_employee_list', 1, NULL, 'admin', 'right_nav', 1, '2017-12-29 12:02:37'),
(95, 'Holiday List ', 'employee/user/show_holiday_list', 1, NULL, 'admin', 'right_nav', 1, '2017-12-29 12:03:10'),
(96, 'Edit Profile', 'employee/user/update_employee', 1, NULL, 'admin', 'right_nav', 1, '2017-12-29 15:32:02'),
(97, 'SF Document List', 'employee/vendor/show_vendor_documents_view', 2, '36', 'regionalmanager', 'main_nav', 1, '2017-12-29 16:03:32'),
(98, 'Service Centers Invoices', 'employee/invoice', 2, '56', 'closure,regionalmanager', 'main_nav', 1, '2017-12-29 16:07:02'),
(99, 'Service Centers Reports', 'employee/vendor/show_service_center_report', 2, '80', 'regionalmanager', 'main_nav', 1, '2017-12-29 16:08:28'),
(100, 'Dashboard', 'employee/vendor/show_around_dashboard', 2, '80', 'callcenter,closure,regionalmanager', 'main_nav', 1, '2017-12-29 16:10:25'),
(101, 'Bookings', 'employee/user/get_user_count_view', 2, '80', 'regionalmanager', 'main_nav', 1, '2017-12-29 16:11:41'),
(102, 'Users', 'employee/user/user_count', 2, '80', 'regionalmanager', 'main_nav', 1, '2017-12-29 16:12:21'),
(103, 'Buyback Dashboard', 'employee/dashboard/buyback_dashboard', 2, '80', 'regionalmanager', 'main_nav', 1, '2017-12-29 16:12:59'),
(104, 'Partner Leads', 'employee/booking/get_missed_calls_view', 2, '7', 'closure', 'main_nav', 1, '2017-12-29 16:33:16'),
(105, 'No Installation, Only Stand Given', 'employee/booking/update_not_pay_to_sf_booking', 2, '7', 'closure', 'main_nav', 1, '2017-12-29 16:34:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `header_navigation`
--
ALTER TABLE `header_navigation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `header_navigation`
--
ALTER TABLE `header_navigation`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;COMMIT;

--Chhavi 02nd Jan
ALTER TABLE `sf_not_exist_booking_details` ADD `valid_pincode` INT(2) NOT NULL DEFAULT '1' AFTER `partner_id`;
ALTER TABLE `sf_not_exist_booking_details` CHANGE `valid_pincode` `is_pincode_valid` INT(2) NOT NULL DEFAULT '1';
ALTER TABLE `service_center_booking_action` ADD `reschedule_request_date` DATETIME NOT NULL AFTER `closed_date`;
CREATE TABLE `fake_reschedule_missed_call_log` (
  `id` int(10) NOT NULL,
  `callSid` varchar(40) NOT NULL,
  `from_number` varchar(20) NOT NULL,
  `to_number` varchar(20) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fake_reschedule_missed_call_log`
--
ALTER TABLE `fake_reschedule_missed_call_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fake_reschedule_missed_call_log`
--
ALTER TABLE `fake_reschedule_missed_call_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;COMMIT;


-- sachin 5 Jan 2018
CREATE TABLE `partner_file_upload_header_mapping` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `referred_date_and_time` varchar(128) DEFAULT NULL,
  `sub_order_id` varchar(256) NOT NULL,
  `brand` varchar(128) NOT NULL,
  `model` varchar(128) NOT NULL,
  `product` varchar(256) NOT NULL,
  `product_type` varchar(256) NOT NULL,
  `customer_name` varchar(256) NOT NULL,
  `customer_address` varchar(256) NOT NULL,
  `pincode` varchar(32) NOT NULL,
  `city` varchar(128) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `email_id` varchar(128) DEFAULT NULL,
  `delivery_date` varchar(128) DEFAULT NULL,
  `agent_id` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `partner_file_upload_header_mapping`
--

INSERT INTO `partner_file_upload_header_mapping` (`id`, `partner_id`, `referred_date_and_time`, `sub_order_id`, `brand`, `model`, `product`, `product_type`, `customer_name`, `customer_address`, `pincode`, `city`, `phone`, `email_id`, `delivery_date`, `agent_id`, `create_date`, `update_date`) VALUES
(1, 247034, '', 'bill_no', '', '', 'product', 'item_name', 'customer', 'address', 'pincode', '', 'contact_no', '', '', 27, '2018-01-05 11:31:28', '2018-01-05 11:35:32'),
(2, 247010, '', 'docno', '', '', 'product', 'item_name', 'customer', 'address', 'zipcodeb', '', 'phno', '', '', 27, '2018-01-05 11:41:38', '2018-01-06 05:56:44'),
(3, 3, '', 'item_id', 'brand', '', '', 'product_name', 'customer_firstname', 'address', 'pincode', 'customer_city', 'contact_number', '', 'shipped_date', 27, '2018-01-06 06:37:13', '0000-00-00 00:00:00'),
(4, 3, '', 'order_item_id', 'brand', '', '', 'product_name', 'customer_firstname', 'address', 'pincode', 'customer_city', 'contact_number', '', 'shipped_date', 27, '2018-01-06 06:37:13', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `partner_file_upload_header_mapping`
--
ALTER TABLE `partner_file_upload_header_mapping`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `partner_file_upload_header_mapping`
--
ALTER TABLE `partner_file_upload_header_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

INSERT INTO `header_navigation` (`id`, `title`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, 'Upload File Header Mapping', 'employee/bookings_excel/file_upload_header_mapping', '2', '24', 'admin,closure,developer', 'main_nav', '1', CURRENT_TIMESTAMP);
-- Chhavi
CREATE TABLE `push_notification_subscribers` (
  `id` int(10) NOT NULL,
  `entity_type` varchar(10) NOT NULL,
  `entity_id` varchar(20) NOT NULL,
  `subscriber_id` varchar(40) NOT NULL,
  `device` varchar(10) NOT NULL,
  `browser` varchar(10) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `push_notification_subscribers`
--
ALTER TABLE `push_notification_subscribers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `push_notification_subscribers`
--
ALTER TABLE `push_notification_subscribers`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;COMMIT;

CREATE TABLE `push_notification_logs` (
  `id` int(11) NOT NULL,
  `request_id` varchar(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `msg` text NOT NULL,
  `url` text NOT NULL,
  `subscriber_ids` varchar(30) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `push_notification_logs`
--
ALTER TABLE `push_notification_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `push_notification_logs`
--
ALTER TABLE `push_notification_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES
(36, 'rescheduled_confirmation_sms', 'We have received reschedule request for your %s service. If you have NOT asked for reschedule, give missed call @ 01139586111 or call 9555000247.', 'Send When SF rescheduled a booking, to confirm is reschedule fake?', '1', '2018-01-02 12:23:49');
COMMIT;

--sachin 09-jan-2018
INSERT INTO `header_navigation` (`id`, `title`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, 'Show Inventory Ledger', 'employee/inventory/show_inventory_ledger_list', '2', '89', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', '1', CURRENT_TIMESTAMP);
ALTER TABLE `partner_file_upload_header_mapping` ADD `alternate_phone` VARCHAR(64) NOT NULL AFTER `phone`;

-- Chhavi 11th Jan
ALTER TABLE  `sf_not_exist_booking_details` ADD  `invalid_pincode_marked_by` INT( 10 ) NOT NULL AFTER  `is_pincode_valid` ;

--Chhavi 12th Jan 
CREATE TABLE `push_notification_templates` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `url` text NOT NULL,
  `msg` text NOT NULL,
  `notification_type` varchar(30) NOT NULL,
  `entity_type` varchar(20) NOT NULL,
  `notification_tag` varchar(30) NOT NULL,
  `comments` varchar(60) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `push_notification_templates`
--

INSERT INTO `push_notification_templates` (`id`, `title`, `url`, `msg`, `notification_type`, `entity_type`, `notification_tag`, `comments`, `active`, `create_date`) VALUES
(1, '%s Updates %s', 'employee/booking/review_bookings', 'Booking %s Has been Updated BY %s, Review the booking', 'Normal', 'employee', 'sf_updates_the_booking', '', 1, '2018-01-10 09:16:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `push_notification_templates`
--
ALTER TABLE `push_notification_templates`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `push_notification_templates`
--
ALTER TABLE `push_notification_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;



---Abhay 4 Jan
ALTER TABLE `service_centres` ADD `isEngineerApp` INT(1) NOT NULL DEFAULT '0' AFTER `agent_id`;
ALTER TABLE trigger_service_centres ADD `isEngineerApp` INT(1) NOT NULL DEFAULT '0' AFTER `agent_id`;

--Abhay 8 jan
ALTER TABLE `spare_parts_details` ADD `old_status` VARCHAR(64) NULL DEFAULT NULL AFTER `status`;


--Abhay Anand
 

--Abhay
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'distance_pincode_api', 'Requested Municipal Limit', 'Please Find Attachment', 'noreply@247around.com', '', '', 'anuj@247around.com, abhaya@247around.com', '1', '2018-01-12 13:05:00');

-- Chhavi
ALTER TABLE  `push_notification_subscribers` CHANGE  `e_id`  `entity_id` INT( 10 ) NOT NULL ;
ALTER TABLE `push_notification_templates` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;
ALTER TABLE  `push_notification_subscribers` ADD  `device` VARCHAR( 20 ) NOT NULL AFTER  `entity_type` ;
ALTER TABLE  `push_notification_subscribers` ADD  `browser` VARCHAR( 20 ) NOT NULL AFTER  `device` ;

UPDATE  `push_notification_logs` SET  `notification_type` =  'normal'
ALTER TABLE `push_notification_templates` CHANGE `entity_type` `entity_type` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;



--Abhay 16 Jan
ALTER TABLE `bb_unit_details` ADD `gst_amount` INT NOT NULL DEFAULT '0' AFTER `cp_tax_charge`;
ALTER TABLE `vendor_partner_invoices` ADD `buyback_tax_amount` INT NOT NULL DEFAULT '0' AFTER `igst_tax_rate`;

--Chhavi 
INSERT INTO `push_notification_templates` (`id`, `title`, `url`, `msg`, `notification_type`, `entity_type`, `notification_tag`, `comments`, `active`, `create_date`) VALUES (NULL, 'New Booking From 247Around', 'service_center/booking_details/%s', 'New Booking %s Has been Assign to you, Please Proceed Further ', 'normal', 'vendor', 'booking_assign_to_sf', '', '1', '2018-01-17 14:46:19');
--sachin 17 jan
ALTER TABLE `email_attachment_parser` ADD `qc_svc` VARCHAR(32) NULL DEFAULT NULL AFTER `email_send_to`;
ALTER TABLE `bb_unit_details` ADD `qc_svc` VARCHAR(32) NULL DEFAULT NULL AFTER `partner_order_id`;
ALTER TABLE `bb_delivery_order_status_report` ADD `qc_svc` VARCHAR(32) NULL DEFAULT NULL AFTER `file_received_date`;
-- Chhavi 11th Jan
ALTER TABLE  `sf_not_exist_booking_details` ADD  `invalid_pincode_marked_by` INT( 10 ) NOT NULL AFTER  `is_pincode_valid` ;

--Chhavi
ALTER TABLE `push_notification_subscribers` ADD `unsubscription_flag` INT(10) NOT NULL DEFAULT '0' AFTER `entity_type`;
ALTER TABLE `push_notification_subscribers` ADD `unsubscription_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `unsubscription_flag`;
ALTER TABLE `push_notification_subscribers` CHANGE `create_date` `create_date` DATETIME NOT NULL;

--Sachin 20 Jan
ALTER TABLE `spare_parts_details` ADD `partner_challan_number` VARCHAR(128) NULL AFTER `edd`, ADD `sf_challan_number` VARCHAR(128) NULL AFTER `partner_challan_number`, ADD `partner_challan_file` VARCHAR(128) NULL AFTER `sf_challan_number`, ADD `sf_challan_file` VARCHAR(128) NULL AFTER `partner_challan_file`, ADD `challan_approx_value` VARCHAR(64) NULL AFTER `sf_challan_file`;

--sachin 22 jan
ALTER TABLE `email_attachment_parser` ADD `partner_id` INT(11) NULL DEFAULT NULL AFTER `id`;

INSERT INTO `email_attachment_parser` (`id`, `partner_id`, `email_received_from`, `email_subject_text`, `email_function_name`, `file_type`, `email_remarks`, `email_send_to`, `qc_svc`, `active`, `create_date`) VALUES (NULL, '247038', 'sachinj@247around.com', 'Bulk Sheet', 'employee/do_background_upload_excel/process_upload_file', 'Aquagrand-Plus-Delivered', 'Aquagrand-Plus', 'sachinj@247around.com', NULL, '1', CURRENT_TIMESTAMP);

INSERT INTO `header_navigation` (`id`, `title`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, 'Upload Aguagrand Plus File', 'employee/bookings_excel/upload_aquagrand_plus_file', '2', '24', 'admin,closure,developer', 'main_nav', '1', CURRENT_TIMESTAMP);


INSERT INTO `header_navigation` (`id`, `title`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, 'Show Inventory Stocks', 'employee/inventory/show_inventory_stock_list', '2', '89', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', '1', CURRENT_TIMESTAMP);



--Abhay
ALTER TABLE `booking_details` ADD `upcountry_remarks` VARCHAR(128) NULL DEFAULT NULL AFTER `upcountry_price`;
ALTER TABLE `push_notification_subscribers` CHANGE `create_date` `create_date` DATETIME NOT NULL;
INSERT INTO `header_navigation` (`id`, `title`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, 'Show Inventory Stocks', 'employee/inventory/show_inventory_stock_list', '2', '89', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', '1', CURRENT_TIMESTAMP);


ALTER TABLE `engineer_table_sign` ADD `cancellation_reason` VARCHAR(128) NULL DEFAULT NULL AFTER `create_date`, ADD `remarks` VARCHAR(128) NULL DEFAULT NULL AFTER `cancellation_reason`;
ALTER TABLE `engineer_table_sign` ADD `closed_date` DATETIME NULL DEFAULT NULL AFTER `remarks`;
ALTER TABLE `engineer_table_sign` ADD `latitude` VARCHAR(128) NULL DEFAULT NULL AFTER `closed_date`, ADD `longitude` VARCHAR(128) NULL DEFAULT NULL AFTER `latitude`;

ALTER TABLE `engineer_table_sign` ADD `mismatch_pincode` INT(1) NULL DEFAULT NULL AFTER `longitude`;
ALTER TABLE `service_center_booking_action` ADD `mismatch_pincode` INT(1) NULL DEFAULT NULL AFTER `update_date`;

-- sachin 06 feb

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'partner_login_details', 'Partner ERP URL and Login - 247around', 'Dear Partner,<br><br>
As discussed, please find below your login details.<br><br>
URL: <a href="https://www.aroundhomzapp.com/partner/login">https://www.aroundhomzapp.com/partner/login</a><br><br>
<b>Username: </b>%s<br><b>Password: </b>%s<br><br>
Please use the ERP panel for your closures going forward. In case of any issues, write to us or call us.<br><br>
Regards,<br> 247around Team', 'noreply@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);


--Chhavi
ALTER TABLE `collateral` ADD `brand` VARCHAR(100) NULL DEFAULT NULL AFTER `document_description`, ADD `appliance_id` INT(20) NULL DEFAULT NULL AFTER `brand`, ADD `category` VARCHAR(100) NULL DEFAULT NULL AFTER `appliance_id`, ADD `capacity` VARCHAR(100) NULL DEFAULT NULL AFTER `category`;
ALTER TABLE `collateral_type` ADD `document_type` VARCHAR(30) NULL DEFAULT NULL AFTER `collateral_type`;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'partner_login_details', 'Partner ERP URL and Login - 247around', 'Dear Partner,<br><br>
As discussed, please find below your login details.<br><br>
URL: <a href="https://www.aroundhomzapp.com/partner/login">https://www.aroundhomzapp.com/partner/login</a><br><br>
<b>Username: </b>%s<br><b>Password: </b>%s<br><br>
Please use the ERP panel for your closures going forward. In case of any issues, write to us or call us.<br><br>
Regards,<br> 247around Team', 'noreply@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);ALTER TABLE  `push_notification_logs` CHANGE  `title`  `title` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE  `push_notification_logs` CHANGE  `title`  `title` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;

ALTER TABLE  `collateral` ADD  `request_type` VARCHAR( 20 ) NULL AFTER  `capacity` ;

-- sachin 09 feb
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'buyback_price_sheet_with_quote', 'Updated Buyback Price Sheet', 'Please find the updated highest price quote buyback sheet in the attached file', 'noreply@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);




--ABhay
ALTER TABLE `booking_unit_details` ADD `user_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `partner_invoice_id`;
ALTER TABLE `booking_details` ADD `paid_by_customer` INT(1) NULL DEFAULT NULL AFTER `qr_code_id`;
ALTER TABLE `booking_details` ADD `service_promise_date` DATETIME NULL DEFAULT NULL AFTER `paid_by_customer`;


--27 FEB ABhay
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'customer_qr_download', '%s', '', '1', CURRENT_TIMESTAMP);
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'customer_paid_invoice_to_vendor', 'Customer Paid Invoice', 'Please Find Attachment', 'billing@247around.com', 'abhaya@247around.com', 'abhaya@247around.com', '', '1', '2016-06-17 00:00:00');
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'customer_paid_invoice', '%s', '', '1', '2018-02-27 19:55:03');

--ABhay
ALTER TABLE `booking_unit_details` ADD `user_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `partner_invoice_id`;
ALTER TABLE `booking_details` ADD `paid_by_customer` INT(1) NULL DEFAULT NULL AFTER `qr_code_id`;
ALTER TABLE `booking_details` ADD `service_promise_date` DATETIME NULL DEFAULT NULL AFTER `paid_by_customer`;

-- 24th April Chhavi
ALTER TABLE `email_sent` ADD `email_tag` TEXT NULL AFTER `id`;

ALTER TABLE `booking_state_change` ADD `actor` VARCHAR(256) NULL AFTER `new_state`;
ALTER TABLE `booking_state_change` ADD `next_action` TEXT NULL AFTER `actor`;
ALTER TABLE `booking_details` ADD `actor` VARCHAR(256) NULL AFTER `partner_internal_status`, ADD `next_action` TEXT NULL AFTER `actor`;
ALTER TABLE `partner_booking_status_mapping` ADD `actor` VARCHAR(256) NULL AFTER `partner_internal_status`, ADD `next_action` TEXT NULL AFTER `actor`;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`) VALUES
(NULL, 'partner_information_to_sf', 'New Brand/Appliance Added By 247ARound', 'Dear Partner,<br> We are glad to accounce that we have on borded following new brands on 247 Around Plateform for %s (%s) %s<br>\nPlease Provide your best of services to gain more and more business<br> Thank You! <br> 247 Around', 'booking@247around.com', 'anuj@247around.com', 'nits@247around.com', '', '1', '');


UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Convert Into Booking' WHERE id ='1';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call Again to Customer' WHERE id ='2';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='3';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call Again to Customer' WHERE id ='4';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call Again to Customer' WHERE id ='5';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='6';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='7';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Convert Into Booking' WHERE id ='46';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call Again to Customer' WHERE id ='47';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='48';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call Again to Customer' WHERE id ='49';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='50';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='51';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='52';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Cancelled Booking Or Update Pincode' WHERE id ='97';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Convert Into Booking' WHERE id ='101';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call Again to Customer' WHERE id ='102';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='103';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call Again to Customer' WHERE id ='104';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='105';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='106';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='107';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Call to Customer' WHERE id ='148';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Convert Into Booking' WHERE id ='175';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Product Will be Delivered ' WHERE id ='191';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Delivered the Product' WHERE id ='149';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Delivered the Product' WHERE id ='150';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Cancelled Booking Or Update Pincode' WHERE id ='151';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Cancelled Booking Or Update Pincode' WHERE id ='152';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Convert Into Booking' WHERE id ='167';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='8';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='53';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='91';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='92';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='95';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='96';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='108';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='145';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Verified From Customer' WHERE id ='153';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Delivered the Product' WHERE id ='154';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Send Spare Part' WHERE id ='155';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Send Spare Part' WHERE id ='156';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Provide Estimate' WHERE id ='157';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='158';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Complete the Booking' WHERE id ='159';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Acknowledge the Received Part' WHERE id ='160';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Review Booking' WHERE id ='161';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Send Defetive Part' WHERE id ='162';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Review Booking' WHERE id ='163';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Get Approval From Customer' WHERE id ='164';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='165';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='166';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Acknowledge the Received Part' WHERE id ='168';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Review Booking' WHERE id ='169';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Send Correct Defective Part' WHERE id ='170';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='171';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Approve / Reject Upcountry Request' WHERE id ='';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Ask Customer to Visit ' WHERE id ='174';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Assign Vendor to Booking' WHERE id ='147';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='176';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='177';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='178';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Verified From Customer' WHERE id ='179';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='180';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Ask Customer to Visit ' WHERE id ='181';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Send Defective Part' WHERE id ='182';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Complete the Booking' WHERE id ='183';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Send Defective Part' WHERE id ='184';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Complete the Booking' WHERE id ='185';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Review Booking' WHERE id ='186';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Review Booking' WHERE id ='187';
UPDATE partner_booking_status_mapping  SET actor = 'Partner ', next_action ='Approve Upcountry' WHERE id ='189';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Update the Booking' WHERE id ='192';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Update the Booking' WHERE id ='193';
UPDATE partner_booking_status_mapping  SET actor = '247Around', next_action ='Approved Courier' WHERE id ='194';
UPDATE partner_booking_status_mapping  SET actor = 'Partner', next_action ='Acknowledge the Received Part' WHERE id ='195';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Update Defective Parts' WHERE id ='196';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='9';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='54';
UPDATE partner_booking_status_mapping  SET actor = 'Vendor', next_action ='Visit to Customer' WHERE id ='109';


UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='1';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='2';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='3';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='247001';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='247002';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='247003';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247051';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247034';
UPDATE bookings_sources SET partner_type ='EXT_WARRANTY_PROVIDER' WHERE partner_id='247012';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247038';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247014';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247052';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247017';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247035';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247050';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247068';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247049';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='247023';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247024';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247025';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='247026';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='247027';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247029';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='247030';
UPDATE bookings_sources SET partner_type ='ECOMMERCE' WHERE partner_id='247032';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247069';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247019';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247065';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247043';
UPDATE bookings_sources SET partner_type ='INTERNAL' WHERE partner_id='247037';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247020';
UPDATE bookings_sources SET partner_type ='INTERNAL' WHERE partner_id='247039';
UPDATE bookings_sources SET partner_type ='INTERNAL' WHERE partner_id='247040';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247070';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247036';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247018';
UPDATE bookings_sources SET partner_type ='INTERNAL' WHERE partner_id='247044';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247013';
UPDATE bookings_sources SET partner_type ='INTERNAL' WHERE partner_id='247046';
UPDATE bookings_sources SET partner_type ='INTERNAL' WHERE partner_id='247047';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247033';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247042';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247066';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247011';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247064';
UPDATE bookings_sources SET partner_type ='EXT_WARRANTY_PROVIDER' WHERE partner_id='247053';
UPDATE bookings_sources SET partner_type ='EXT_WARRANTY_PROVIDER' WHERE partner_id='247054';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247048';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247056';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247057';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247058';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247059';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247060';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247061';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247021';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247045';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247016';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247073';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247041';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247055';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247071';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247010';
UPDATE bookings_sources SET partner_type ='OEM' WHERE partner_id='247072';
UPDATE bookings_sources SET partner_type ='BUYBACK' WHERE partner_id='247074';


CREATE TABLE IF NOT EXISTS `account_holders_bank_details_trigger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` varchar(20) NOT NULL,
  `entity_type` varchar(20) NOT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_type` varchar(20) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `cancelled_cheque_file` varchar(256) DEFAULT NULL,
  `beneficiary_name` varchar(256) DEFAULT NULL,
  `is_verified` int(10) NOT NULL DEFAULT '0',
  `agent_id` int(10) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TRIGGER `account_details_trigger` BEFORE UPDATE ON `account_holders_bank_details`
 FOR EACH ROW BEGIN 
INSERT INTO account_holders_bank_details_trigger (SELECT * FROM account_holders_bank_details WHERE id=NEW.id);
END

INSERT INTO `account_holders_bank_details_trigger` (`id`, `entity_id`, `entity_type`, `bank_name`, `account_type`, `bank_account`, `ifsc_code`, `cancelled_cheque_file`, `beneficiary_name`, `is_verified`, `agent_id`, `is_active`, `create_date`) VALUES
(609, '288', 'SF', 'Corporation Bank', 'Current', '079801601000299', 'CORP0000798', NULL, 'SKM ENTERPRISES', 0, 16, 0, '0000-00-00 00:00:00'),
(616, '358', 'SF', 'State Bank Of India', 'Current', '3672430292', 'SBIN0016826', NULL, 'S S ENTERPRISES', 0, 9, 0, '2018-04-16 10:27:44'),
(592, '372', 'SF', 'Punjab National Bank', 'Current', '4627002100001150', 'PUNB0462700', 'AligarhGURUJIENTERPRISES_cancelledchequefile_f073770592749f7.jpg', 'GURU JI ENTERPRISES', 1, 32, 0, '0000-00-00 00:00:00'),
(590, '400', 'SF', 'Bank Of Baroda', 'Saving', '07000100022052', 'ABCD', 'HindaunSKElectronics_cancelledchequefile_4cb594b425a86ac.jpg', 'SHABBIR KHAN', 0, 9, 0, '0000-00-00 00:00:00'),
(615, '409', 'SF', 'United Bank Of India', 'Current', '1447050000392', 'UTBIOSTA554', 'BareillySunCoolingHouse_cancelledchequefile_64c5379af205eb2.jpg', 'SUN COOLING HOUSE', 1, 9, 0, '0000-00-00 00:00:00'),
(614, '433', 'SF', 'Bank Of India', 'Saving', '694110110003863', 'BKID0005941', 'PauriDevElectronics_cancelledchequefile_95d5368894efb88.jpg', 'ANIL CHAUDHARY', 0, 9, 0, '0000-00-00 00:00:00');

DELETE FROM account_holders_bank_details WHERE id IN ('272','338','517','546','555','594');

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`) VALUES
(NULL, 'cashback_processed_to_customer', 'Congrats, Your cashback of Rs. %s for Booking ID %s has been processed. Hope to serve you soon again, 9555000247 247Around.', NULL, '1');
COMMIT;

ALTER TABLE `paytm_cashback_details` ADD `agent_id` INT(10) NULL AFTER `date`;

-- sachin 26 april 2018
UPDATE `sms_template` SET `template` = 'We have received reschedule request for your %s service (Booking %s) to %s. If you have not asked for reschedule, give missed call @ 01139586111 or call 9555000247.' WHERE `sms_template`.`tag` = 'rescheduled_confirmation_sms';

--Abhay 24 April
ALTER TABLE `booking_details` ADD `partner_call_status_on_completed` VARCHAR(64) NULL DEFAULT NULL AFTER `dependency_on`;

--Abhay 25April
ALTER TABLE  `partner_leads` ADD  `spd_date` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `update_date` ;

--Chhavi 2nd May
ALTER TABLE `booking_details` ADD `rating_unreachable_count` INT(10) NOT NULL DEFAULT '0' AFTER `dependency_on`;
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'customer_not_reachable_for_rating', 'Hello %s! 247around team tried to reach you for your feedback. If you are HAPPY with our service, give miss call @ %s. If not, give miss call @ %s.', "Send to Customer, when marked by no reachable in case of rating", '1', CURRENT_TIMESTAMP);


--sachin 28 april 2018
ALTER TABLE `spare_parts_details` ADD `model_number_shipped` VARCHAR(256) NULL DEFAULT NULL AFTER `date_of_request`;
ALTER TABLE `partners` ADD `is_wh` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1=\'working as a warehouse model\',0 = \'nor working as a warehouse model\'' AFTER `is_prepaid`;
ALTER TABLE `service_centres` ADD `is_wh` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1 = \'working as a warehouse\', 0 = \'not working as a warehouse\'' AFTER `is_cp`;
ALTER TABLE `inventory_master_list` ADD `hsn_code` varchar(64) NULL DEFAULT NULL AFTER `entity_type`;

DROP TABLE IF EXISTS `contact_person`;
CREATE TABLE `contact_person` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `officail_email` varchar(256) NOT NULL,
  `alternate_email` varchar(256) DEFAULT NULL,
  `official_contact_number` varchar(256) NOT NULL,
  `alternate_contact_number` varchar(256) DEFAULT NULL,
  `permanent_address` varchar(1024) NOT NULL,
  `correspondence_address` varchar(1024) NOT NULL,
  `role` varchar(64) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(128) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_person`
--
ALTER TABLE `contact_person`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

UPDATE `sms_template` SET `template` = 'We have received reschedule request for your %s service (Booking %s) to %s. If you have not asked for reschedule, give missed call @ 01139586111 or call 9555000247.' WHERE `sms_template`.`tag` = 'reschedule_booking';
UPDATE `sms_template` SET `template` = 'Dear Customer, Request for your %s for %s is confirmed for %s with booking id %s. In case of any support, call 9555000247. 247Around %s.' WHERE `sms_template`.`tag` = 'add_new_booking';


-- sachin 26 april 2018
UPDATE `sms_template` SET `template` = 'We have received reschedule request for your %s service (Booking %s) to %s. If you have not asked for reschedule, give missed call @ 01139586111 or call 9555000247.' WHERE `sms_template`.`tag` = 'rescheduled_confirmation_sms';

--Abhay 24 April
ALTER TABLE `booking_details` ADD `partner_call_status_on_completed` VARCHAR(64) NULL DEFAULT NULL AFTER `dependency_on`;

--Abhay 25April
ALTER TABLE  `partner_leads` ADD  `spd_date` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `update_date` ;

--
-- AUTO_INCREMENT for table `contact_person`


--sachin 28 april 2018
ALTER TABLE `spare_parts_details` ADD `model_number_shipped` VARCHAR(256) NULL DEFAULT NULL AFTER `date_of_request`;
ALTER TABLE `partners` ADD `is_wh` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1=\'working as a warehouse model\',0 = \'nor working as a warehouse model\'' AFTER `is_prepaid`;
ALTER TABLE `service_centres` ADD `is_wh` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1 = \'working as a warehouse\', 0 = \'not working as a warehouse\'' AFTER `is_cp`;
ALTER TABLE `inventory_master_list` ADD `hsn_code` varchar(64) NULL DEFAULT NULL AFTER `entity_type`;

DROP TABLE IF EXISTS `contact_person`;
CREATE TABLE `contact_person` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `officail_email` varchar(256) NOT NULL,
  `alternate_email` varchar(256) DEFAULT NULL,
  `official_contact_number` varchar(256) NOT NULL,
  `alternate_contact_number` varchar(256) DEFAULT NULL,
  `permanent_address` varchar(1024) NOT NULL,
  `correspondence_address` varchar(1024) NOT NULL,
  `role` varchar(64) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(128) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_person`
--
ALTER TABLE `contact_person`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_person`

DROP TABLE IF EXISTS `warehouse_details`;
CREATE TABLE `warehouse_details` (
  `id` int(11) NOT NULL,
  `warehouse_address_line1` varchar(512) NOT NULL,
  `warehouse_address_line2` varchar(512) NOT NULL,
  `warehouse_city` varchar(64) NOT NULL,
  `warehouse_region` varchar(64) NOT NULL,
  `warehouse_pincode` int(6) NOT NULL,
  `warehouse_state` varchar(256) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(256) NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `warehouse_details`
--
ALTER TABLE `warehouse_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `warehouse_details`
--
ALTER TABLE `warehouse_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `warehouse_person_relationship`;
CREATE TABLE `warehouse_person_relationship` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `contact_person_id` int(11) NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `warehouse_person_relationship`
--
ALTER TABLE `warehouse_person_relationship`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_id` (`warehouse_id`),
  ADD KEY `contact_person_id` (`contact_person_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `warehouse_person_relationship`
--
ALTER TABLE `warehouse_person_relationship`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `warehouse_person_relationship`
--
ALTER TABLE `warehouse_person_relationship`
  ADD CONSTRAINT `warehouse_person_relationship_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse_details` (`id`),
  ADD CONSTRAINT `warehouse_person_relationship_ibfk_2` FOREIGN KEY (`contact_person_id`) REFERENCES `contact_person` (`id`);

DROP TABLE IF EXISTS `warehouse_state_relationship`;
CREATE TABLE `warehouse_state_relationship` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `state` varchar(64) NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `warehouse_state_relationship`
--
ALTER TABLE `warehouse_state_relationship`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_id` (`warehouse_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `warehouse_state_relationship`
--
ALTER TABLE `warehouse_state_relationship`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `warehouse_state_relationship`
--
ALTER TABLE `warehouse_state_relationship`
  ADD CONSTRAINT `warehouse_state_relationship_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `inventory_master_list` ADD UNIQUE( `service_id`, `part_number`, `part_name`, `model_number`, `entity_id`, `entity_type`);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'out_of_stock_inventory', 'Part Name %s of Model Number %s is out of stock in warehouse', 'Dear Partner, <br/> <br/> Please Find the below details of the inventory which is currently out of stock in Our warehouse.<br> Inventory Details<br> %s <br> Please shipped this inventory as soon as possible ', 'noreply@247around.com', '', '', '', '1', '2018-02-03 18:26:57');

UPDATE `email_template` SET `subject` = 'New Login Details - %s' WHERE `email_template`.`tag` = resend_login_details;

UPDATE `email_template` SET `subject` = 'Your Password Reset Request Processed Successfully - %s' WHERE `email_template`.`tag` = reset_vendor_login_details;

--Abhay 27 April
ALTER TABLE `spare_parts_details` ADD `auto_acknowledeged` INT(1) NOT NULL DEFAULT '0' COMMENT 'Auto Ack for Spare delivered to SF' AFTER `acknowledge_date`;


--Abhay 28 April
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`) VALUES (NULL, '247001', 'Pending', 'Spare parts not received', 'Spare parts not received by SF', 'Spare parts not received by SF', 'vendor', 'Update Booking (Spare to be received by SF)');
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`) VALUES (NULL, '247001', 'Pending', 'Upcountry Booking', 'Rescheduled', 'Rescheduled - Upcountry Booking', 'vendor', 'Visit to Customer (Upcountry Booking)');

--Abhay 2 May
ALTER TABLE `trigger_partners` ADD `is_wh` INT(1) NOT NULL DEFAULT '0' AFTER `updated_date`;
ALTER TABLE `trigger_service_charges` ADD `is_wh` INT(1) NOT NULL DEFAULT '0' AFTER `deleted_by`;


--sachin 3 May

ALTER TABLE `spare_parts_details` ADD `parts_requested_type` VARCHAR(256) NOT NULL AFTER `parts_requested`;
ALTER TABLE `spare_parts_details` ADD `shipped_parts_type` VARCHAR(256) NOT NULL AFTER `parts_shipped`;


-- sachin 26 april 2018
UPDATE `sms_template` SET `template` = 'We have received reschedule request for your %s service (Booking %s) to %s. If you have not asked for reschedule, give missed call @ 01139586111 or call 9555000247.' WHERE `sms_template`.`tag` = 'rescheduled_confirmation_sms';

--Abhay 25April
ALTER TABLE  `partner_leads` ADD  `spd_date` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `update_date` ;

--Chhavi 2nd May
ALTER TABLE `booking_details` ADD `rating_unreachable_count` INT(10) NOT NULL DEFAULT '0' AFTER `dependency_on`;
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'customer_not_reachable_for_rating', 'Hello %s! 247around team tried to reach you for your feedback. If you are HAPPY with our service, give miss call @ %s. If not, give miss call @ %s.', NULL, '1', CURRENT_TIMESTAMP);


ALTER TABLE `spare_parts_details` ADD `entity_type` VARCHAR(32) NOT NULL AFTER `booking_id`;
update spare_parts_details SET entity_type = 'partner';


---7 May Released (Branch 52)

--
-- Table structure for table `partner_serial_no`
--

CREATE TABLE `partner_serial_no` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `serial_number` varchar(128) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `partner_serial_no`
--
ALTER TABLE `partner_serial_no`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `partner_serial_no`
--
ALTER TABLE `partner_serial_no`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--sachin 9 may

ALTER TABLE `spare_parts_details` ADD `booking_unit_details_id` INT(11) NULL DEFAULT NULL AFTER `booking_id`;

ALTER TABLE spare_parts_details
ADD FOREIGN KEY (unit_details_id) REFERENCES booking_unit_details(id);


--sachin 10 may

ALTER TABLE `spare_parts_details` ADD `requested_inventory_id` INT(11) NULL DEFAULT NULL AFTER `challan_approx_value`, ADD `shipped_inventory_id` INT(11) NULL DEFAULT NULL AFTER `requested_inventory_id`;


--Chhavi
CREATE TABLE `reports_log` (
  `id` int(10) NOT NULL,
  `entity_type` varchar(100) NOT NULL,
  `entity_id` int(10) NOT NULL,
  `report_type` varchar(256) NOT NULL,
  `filters` text,
  `url` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `agent_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reports_log`
--
ALTER TABLE `reports_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reports_log`
--
ALTER TABLE `reports_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;COMMIT;

ALTER TABLE `booking_unit_details` ADD `purchase_date` DATE NULL AFTER `purchase_year`;
ALTER TABLE `appliance_details` ADD `purchase_date` DATE NOT NULL AFTER `purchase_year`;
ALTER TABLE `appliance_details` ADD `sf_serial_number` VARCHAR(128) NULL AFTER `serial_number`;

--Abhay 15 April
ALTER TABLE `spare_parts_details` ADD `defective_back_parts_pic` VARCHAR(128) NULL DEFAULT NULL AFTER `defective_parts_pic`;

ALTER TABLE `sample_appliances` ADD `purchase_date` DATE NULL DEFAULT NULL AFTER `purchase_year`;
--sachin 10 may

ALTER TABLE `spare_parts_details` ADD `requested_inventory_id` INT(11) NULL DEFAULT NULL AFTER `challan_approx_value`, ADD `shipped_inventory_id` INT(11) NULL DEFAULT NULL AFTER `requested_inventory_id`;


--sachin 17 May
CREATE TABLE `spare_invoice_mapping` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `inventory_id` INT(11) NOT NULL , `quantity_in` INT(11) NOT NULL , `quantity_out` INT(11) NOT NULL , `invoice_id_in` INT NOT NULL , `invoice_id_out` INT NOT NULL , `booking_id` VARCHAR(256) NOT NULL , `is_settled` INT NOT NULL , `remarks` INT NOT NULL , `create_date` DATETIME NOT NULL , `update_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `spare_invoice_mapping` ADD `inventory_id_in_date` DATETIME NULL DEFAULT NULL AFTER `invoice_id_in`;
ALTER TABLE `spare_invoice_mapping` CHANGE `is_settled` `is_settled` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '1 = \'settled\', 0 = \'un-settled\'';
CREATE TABLE `spare_invoice_ledger` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `spare_invoice_mapping_id` INT(11) NOT NULL , `inventory_id` INT(11) NOT NULL , `quantity_out` INT(11) NOT NULL , `invoice_id_out` VARCHAR(256) NOT NULL , `remarks` VARCHAR(256) NOT NULL , `create_date` DATETIME NOT NULL , `update_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `inventory_ledger` ADD `is_wh_ack` TINYINT(1) NULL DEFAULT NULL AFTER `active`, ADD `wh_ack_date` DATETIME NULL DEFAULT NULL AFTER `is_wh_ack`, ADD `is_defective` TINYINT(1) DEFAULT '0' AFTER `wh_ack_date`, ADD `is_partner_ack` TINYINT(1) NULL DEFAULT NULL AFTER `is_defective`;
ALTER TABLE `inventory_ledger` ADD `partner_ack_date` DATETIME NOT NULL AFTER `is_partner_ack`;

ALTER TABLE `appliance_details` ADD `sf_serial_number` VARCHAR(128) NULL AFTER `serial_number`;

--Abhay 15 April
ALTER TABLE `spare_parts_details` ADD `defective_back_parts_pic` VARCHAR(128) NULL DEFAULT NULL AFTER `defective_parts_pic`;

--Chhavi 17th May
ALTER TABLE `sample_appliances` ADD `purchase_date` DATE NULL DEFAULT NULL AFTER `purchase_year`;
ALTER TABLE `file_uploads` ADD `entity_type` VARCHAR(128) NULL AFTER `file_type`;
ALTER TABLE `file_uploads` ADD `entity_id` INT(10) NULL AFTER `entity_type`;

--Abhay 19April
ALTER TABLE `inventory_master_list` ADD `gst_rate` INT(11) NULL DEFAULT NULL AFTER `hsn_code`;
ALTER TABLE `invoice` ADD `type` VARCHAR(28) NULL DEFAULT NULL AFTER `invoice_id`;
ALTER TABLE `invoice` ADD `settle_qty` INT NOT NULL DEFAULT '0' AFTER `settle`;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'partner_spare_cancelled', 'Cancelled Request -Part Name %s for Booking ID %s ', 'Dear Partner, <br/> <br/> Part Name %s for Booking ID %s request has cancelled. Do not need to send part to Service Center', 'noreply@247around.com', '', 'sachinj@247around.com', '', '1', '2018-02-03 18:26:57');

--21 May Released
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'inform_partner_for_serial_no', 'Serial no %s', 'Dear Partner, <br/> <br/> Please find the attachment. %s', 'noreply@247around.com', '', '', '', '1', '2018-05-23 18:26:57');

--Chhavi 18th May
ALTER TABLE `header_navigation` ADD `entity_type` VARCHAR(128) NOT NULL AFTER `id`;
ALTER TABLE `header_navigation` ADD `title_icon` VARCHAR(256) NULL AFTER `title`;

--Chhavi 19th May
ALTER TABLE `entity_login_table` ADD `groups` VARCHAR(256) NOT NULL AFTER `agent_id`;
ALTER TABLE `entity_login_table` ADD `is_filter_applicable` INT(10) NOT NULL DEFAULT '0' AFTER `groups`;

CREATE TABLE `agent_filters` (
  `id` int(10) NOT NULL,
  `entity_type` varchar(128) NOT NULL,
  `entity_id` int(10) NOT NULL,
  `agent_id` int(10) NOT NULL,
  `state` varchar(256) NOT NULL,
  `is_active` int(10) NOT NULL DEFAULT '1',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agent_filters`
--
ALTER TABLE `agent_filters`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agent_filters`
--
ALTER TABLE `agent_filters`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;COMMIT;

--Abhay 24 May

ALTER TABLE `upcountry_pincode_services_sf_level` ADD `district` VARCHAR(64) NULL DEFAULT NULL AFTER `pincode`;
ALTER TABLE `invoice_details` ADD `inventory_id` INT(11) NULL DEFAULT NULL AFTER `invoice_id`;
ALTER TABLE `invoice_details` ADD `settle_qty` INT NULL DEFAULT '0' AFTER `inventory_id`, ADD `is_settle` INT(1) NULL DEFAULT '0' AFTER `settle_qty`;

--Abhay 25 May
ALTER TABLE `vendor_partner_invoices` ADD `third_party_entity` VARCHAR(28) NULL DEFAULT NULL AFTER `vendor_partner_id`, ADD `third_party_entity_id` INT(11) NULL DEFAULT NULL AFTER `third_party_entity`;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'upcountry_local_template', 'Upcountry File', 'Dear Partner, <br/> <br/> Please find the attachment. ', 'noreply@247around.com', 'abhaya@247around.com', '', 'abhaya@247around.com', '1', '2018-05-25 18:26:57');

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'spare_inventory_invoice', 'Spare Invoice', 'Dear Partner, <br/> <br/> Please find the attachment. ', 'noreply@247around.com', 'abhaya@247around.com', '', '', '1', '2018-05-25 18:26:57');
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'spare_invoice_not_found', 'Spare Invoice', 'Dear Partner, <br/> <br/> Please find the attachment. ', 'noreply@247around.com', 'abhaya@247around.com', '', '', '1', '2018-05-25 18:26:57');


--sachin 30 may 

ALTER TABLE `inventory_model_mapping` ADD UNIQUE( `inventory_id`, `model_number_id`);

--Abhay 29 May
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'inform_to_sf_for_cancellation', 'Pending Booking Cancellation - 247AROUND', 'Dear Partner, <br/> <br/> Booking ID %s has cancelled.<br/><span style="font-weight:bold">Cancellation Reason: </span>%s', 'noreply@247around.com', '', '', '', '1', '2018-05-29 18:26:57');

--sachin 30 may 
ALTER TABLE `inventory_ledger` CHANGE `partner_ack_date` `partner_ack_date` DATETIME NULL DEFAULT NULL;

CREATE TABLE `courier_details` (
  `id` int(11) NOT NULL,
  `sender_entity_id` int(11) NOT NULL,
  `sender_entity_type` varchar(64) NOT NULL,
  `receiver_entity_id` int(11) NOT NULL,
  `receiver_entity_type` varchar(64) NOT NULL,
  `AWB_no` varchar(256) NOT NULL,
  `courier_name` varchar(256) NOT NULL,
  `courier_file` varchar(1024) DEFAULT NULL,
  `shipment_date` datetime DEFAULT NULL,
  `remarks` varchar(1024) DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courier_details`
--
ALTER TABLE `courier_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courier_details`
--
ALTER TABLE `courier_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


--Abhay 31 May
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'courier_invoice_sent', '%s Updated Courier Details for Booking ID %s', 'Please Find Courier Invoice Attachment <br/> Courier Details:- <br/> AWB %s <br/> Courier Name %s <br/> Courier Charge %s <br/> Shipped Date %s <br/>', 'noreply@247around.com', 'sachins@247around.com', 'abhaya@247around.com', '', '1', '2018-05-29 18:26:57');


--Abhay 2 June
ALTER TABLE `partner_serial_no` ADD `invoice_date` DATE NULL DEFAULT NULL AFTER `create_date`, ADD `sku_name` VARCHAR(128) NULL DEFAULT NULL AFTER `invoice_date`, ADD `sku_code` VARCHAR(128) NULL DEFAULT NULL AFTER `sku_name`, ADD `category_name` VARCHAR(128) NULL DEFAULT NULL AFTER `sku_code`, ADD `brand_name` VARCHAR(128) NULL DEFAULT NULL AFTER `category_name`, ADD `model_number` VARCHAR(128) NOT NULL AFTER `brand_name`;
ALTER TABLE `partner_serial_no` ADD `color` VARCHAR(128) NULL DEFAULT NULL AFTER `model_number`, ADD `stock_bin` VARCHAR(128) NULL DEFAULT NULL AFTER `color`;


--Abhay 4 June
ALTER TABLE `inventory_invoice_mapping` CHANGE `incoming_invoice_id` `incoming_invoice_id` VARCHAR(64) NOT NULL;
ALTER TABLE `inventory_invoice_mapping` ADD `inventory_id` INT NULL DEFAULT NULL AFTER `outgoing_invoice_id`;

--Sachin 4 June
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'inventory_details_mapping_not_found', NULL, 'Inventory details mapping not found for the below spare <br> <b> Partner ID : %s </b> <br> <b> Model Number ID : %s </b> <br> <b> Service ID : %s </b> <br> <b> Part Name : %s </b> <br>', 'noreply@247around.com', 'abhaya@247around.com, sachinj@247around.com', '', '', '1', '2016-06-17 00:00:00');

--sachin 5 june

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'file_upload_email', NULL, '', 'noreply@247around.com', '', 'sachinj@247around.com', '', '1', '2016-06-17 00:00:00');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
(NULL, '247Around', 'Inventory Send By Partner To Warehouse', NULL, 'employee/inventory/acknowledge_spares_send_by_partner_by_admin', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-06-05 13:05:52');
--4th June
ALTER TABLE `contact_person` ADD `is_active` INT(10) NOT NULL DEFAULT '1' AFTER `update_date`;
ALTER TABLE `contact_person` ADD `agent_id` INT(10) NOT NULL AFTER `is_active`;
ALTER TABLE `entity_login_table` ADD `contact_person_id` INT(10) NULL AFTER `entity_name`;
ALTER TABLE contact_person AUTO_INCREMENT = 10000;


CREATE TABLE `entity_role` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(128) NOT NULL,
  `department` varchar(256) NOT NULL,
  `role` varchar(256) NOT NULL,
  `is_filter_applicable` int(10) NOT NULL DEFAULT '1',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entity_role`
--

INSERT INTO `entity_role` (`id`, `entity_type`, `department`, `role`, `is_filter_applicable`, `create_date`) VALUES
(1, 'partner', 'Admin', 'poc', 0, '2018-06-11 05:20:44');
INSERT INTO `entity_role` (`id`, `entity_type`, `department`, `role`, `is_filter_applicable`, `create_date`) VALUES
(1, 'partner', 'Management', 'area_sales_manager', 1, '2018-06-11 05:20:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `entity_role`
--
ALTER TABLE `entity_role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `entity_role`
--
ALTER TABLE `entity_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;
ALTER TABLE `contact_person` DROP `department`;
ALTER TABLE `entity_login_table` DROP `groups`;
ALTER TABLE `is_filter_applicable` DROP `groups`;
ALTER TABLE `contact_person` CHANGE `official_contact_number` `official_contact_number` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `contact_person` CHANGE `permanent_address` `permanent_address` VARCHAR(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `contact_person` CHANGE `correspondence_address` `correspondence_address` VARCHAR(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Wybor 1','sh.khan@meplworld.com',NULL,NULL,NULL,NULL,NULL,'4','247010','partner','1','1');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ray Electronics','vinesh9000@hotmail.com',NULL,NULL,NULL,NULL,NULL,'4','247011','partner','1','2');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Wybor-247around','sh.khan@meplworld.com',NULL,NULL,NULL,NULL,NULL,'4','247010','partner','1','3');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Zopper','anoj@zopper.com',NULL,NULL,NULL,NULL,NULL,'4','247012','partner','1','4');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Wybor 2','sh.khan@meplworld.com',NULL,NULL,NULL,NULL,NULL,'4','247010','partner','1','6');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Wybor 3','sh.khan@meplworld.com',NULL,NULL,NULL,NULL,NULL,'4','247010','partner','1','7');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Wybor 4','sh.khan@meplworld.com',NULL,NULL,NULL,NULL,NULL,'4','247010','partner','1','8');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Snapdeal-sts','kaushik.neha@snapdeal.com',NULL,NULL,NULL,NULL,NULL,'4','1','partner','1','978978');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Usha Kalouni','ushakalouni@trigurelectronics.com',NULL,NULL,NULL,NULL,NULL,'4','247016','partner','1','978979');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','navin.kejriwal@futonelectronics.com',NULL,NULL,NULL,NULL,NULL,'4','247017','partner','1','978980');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sandeep Gupta','Mukesh.mb3322@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247018','partner','1','978981');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ICON ENTERPRISES 0','aeshna@krisons.com',NULL,NULL,NULL,NULL,NULL,'4','247019','partner','1','978982');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KRISONS 0','abhishek@krisons.com',NULL,NULL,NULL,NULL,NULL,'4','247020','partner','1','978983');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Santosh','spmelectronic195@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247021','partner','1','978984');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Zone Computer Trade','rohtashsingh90@yahoo.com',NULL,NULL,NULL,NULL,NULL,'4','247023','partner','1','978985');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Paytm Mall','swati.chariya@paytm.com',NULL,NULL,NULL,NULL,NULL,'4','247027','partner','1','978986');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Paytm-sts','ayush.bafna@paytmmall.com',NULL,NULL,NULL,NULL,NULL,'4','3','partner','1','978991');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NU STAR','umassociates64@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247033','partner','1','978992');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dektron','accounts@ultimoenterprises.com',NULL,NULL,NULL,NULL,NULL,'4','247035','partner','1','978993');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S k mishra','kaushik.neha@snapdeal.com',NULL,NULL,NULL,NULL,NULL,'4','1','dealer','1','978994');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Reshav Electronic','',NULL,NULL,NULL,NULL,NULL,'4','2','dealer','1','978995');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SATHYA AGENCIES','ayush.bafna@paytmmall.com',NULL,NULL,NULL,NULL,NULL,'4','3','dealer','1','978996');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('XYZ','NULL',NULL,NULL,NULL,NULL,NULL,'4','4','dealer','1','978997');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHANU PRATAP SINGH','NULL',NULL,NULL,NULL,NULL,NULL,'4','5','dealer','1','978998');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHAKTI ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','6','dealer','1','978999');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHALIMA','NULL',NULL,NULL,NULL,NULL,NULL,'4','7','dealer','1','979000');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','jetinder.raina@akaiindia.in',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','979001');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AZAD','NULL',NULL,NULL,NULL,NULL,NULL,'4','8','dealer','1','979002');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('P00JA ELEC','NULL',NULL,NULL,NULL,NULL,NULL,'4','9','dealer','1','979003');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JB DIGI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','10','dealer','1','979004');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DHARMENDER JI','NULL',NULL,NULL,NULL,NULL,NULL,'4','11','dealer','1','979005');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vishwa Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','12','dealer','1','979006');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('navin electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','13','dealer','1','979007');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KARTIK','NULL',NULL,NULL,NULL,NULL,NULL,'4','14','dealer','1','979008');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PERFECT ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','15','dealer','1','979009');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AJAY ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','16','dealer','1','979010');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('UM ASSOCIATES','NULL',NULL,NULL,NULL,NULL,NULL,'4','17','dealer','1','979011');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sonu Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','18','dealer','1','979012');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHANA ENTERRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','19','dealer','1','979013');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAYANTI HAI','NULL',NULL,NULL,NULL,NULL,NULL,'4','20','dealer','1','979014');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GOMATHI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','21','dealer','1','979015');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANJAY S PATEL','NULL',NULL,NULL,NULL,NULL,NULL,'4','22','dealer','1','979016');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Alam Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','23','dealer','1','979017');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHA T.V','NULL',NULL,NULL,NULL,NULL,NULL,'4','24','dealer','1','979018');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T.V PLACE','NULL',NULL,NULL,NULL,NULL,NULL,'4','25','dealer','1','979019');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MEENA PATEL','NULL',NULL,NULL,NULL,NULL,NULL,'4','26','dealer','1','979020');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KAVITA','NULL',NULL,NULL,NULL,NULL,NULL,'4','27','dealer','1','979021');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prem Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','28','dealer','1','979022');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Susmita Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','29','dealer','1','979023');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Agarwaal traders ','NULL',NULL,NULL,NULL,NULL,NULL,'4','30','dealer','1','979024');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('murphy dealer','NULL',NULL,NULL,NULL,NULL,NULL,'4','31','dealer','1','979025');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manan Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','32','dealer','1','979026');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maser','info@maser-india.com',NULL,NULL,NULL,NULL,NULL,'4','247036','partner','1','979027');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RESHAV electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','33','dealer','1','979028');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AGOAN ELECTRONICS	','NULL',NULL,NULL,NULL,NULL,NULL,'4','34','dealer','1','979029');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rishab electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','35','dealer','1','979030');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ghosh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','36','dealer','1','979031');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GEMS E - CITY ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','37','dealer','1','979032');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bala Jii ','NULL',NULL,NULL,NULL,NULL,NULL,'4','38','dealer','1','979033');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T.V. PALACE','NULL',NULL,NULL,NULL,NULL,NULL,'4','39','dealer','1','979034');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Swati Enterprises  ','NULL',NULL,NULL,NULL,NULL,NULL,'4','40','dealer','1','979035');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Futun electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','41','dealer','1','979036');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JR HOME NETS','NULL',NULL,NULL,NULL,NULL,NULL,'4','42','dealer','1','979037');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CHANDRA TRADERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','43','dealer','1','979038');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maan Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','44','dealer','1','979039');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PATEL','NULL',NULL,NULL,NULL,NULL,NULL,'4','45','dealer','1','979040');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BITTO ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','46','dealer','1','979041');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI NIVASA HOME APPLIANCES','NULL',NULL,NULL,NULL,NULL,NULL,'4','47','dealer','1','979042');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAMA KRISHNA & SONS','NULL',NULL,NULL,NULL,NULL,NULL,'4','48','dealer','1','979043');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('C','NULL',NULL,NULL,NULL,NULL,NULL,'4','49','dealer','1','979044');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('susmita Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','50','dealer','1','979045');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aquagrand Plus','atanu@skyscraperideas.com',NULL,NULL,NULL,NULL,NULL,'4','247038','partner','1','979046');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('puja Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','51','dealer','1','979047');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRATIDIN','NULL',NULL,NULL,NULL,NULL,NULL,'4','52','dealer','1','979048');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TANEJA ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','53','dealer','1','979049');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RATI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','54','dealer','1','979050');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Lakkhi Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','55','dealer','1','979051');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganesh Electronic  ','NULL',NULL,NULL,NULL,NULL,NULL,'4','56','dealer','1','979052');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganj enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','57','dealer','1','979053');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AICH ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','58','dealer','1','979054');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('skyscraper','NULL',NULL,NULL,NULL,NULL,NULL,'4','59','dealer','1','979055');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('R K gift house ','NULL',NULL,NULL,NULL,NULL,NULL,'4','60','dealer','1','979056');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('F R Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','61','dealer','1','979057');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sandeep Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','62','dealer','1','979058');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DADHIWALA ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','63','dealer','1','979059');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mayank Agarwal','NULL',NULL,NULL,NULL,NULL,NULL,'4','64','dealer','1','979060');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEALER','NULL',NULL,NULL,NULL,NULL,NULL,'4','65','dealer','1','979061');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KING HOME APPLIANCES','NULL',NULL,NULL,NULL,NULL,NULL,'4','66','dealer','1','979062');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Electronic Hub','NULL',NULL,NULL,NULL,NULL,NULL,'4','68','dealer','1','979063');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SUBHA SHREE AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','70','dealer','1','979064');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHREE  CHAR BHUJA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','71','dealer','1','979065');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW PANIDAN STORE','NULL',NULL,NULL,NULL,NULL,NULL,'4','73','dealer','1','979066');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI LAXMI TRADING','NULL',NULL,NULL,NULL,NULL,NULL,'4','74','dealer','1','979067');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('U M ASSOCIATES','umassociates64@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','75','dealer','1','979068');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANJAY SISODIYA','NULL',NULL,NULL,NULL,NULL,NULL,'4','76','dealer','1','979069');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ambe Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','77','dealer','1','979070');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('prem Electronic .','NULL',NULL,NULL,NULL,NULL,NULL,'4','79','dealer','1','979071');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('9897028796','NULL',NULL,NULL,NULL,NULL,NULL,'4','80','dealer','1','979072');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chamunda Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','81','dealer','1','979073');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prakirti electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','82','dealer','1','979074');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('om sri ram super store','NULL',NULL,NULL,NULL,NULL,NULL,'4','83','dealer','1','979075');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MANOJ','NULL',NULL,NULL,NULL,NULL,NULL,'4','84','dealer','1','979076');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mann Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','85','dealer','1','979077');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Lalit Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','86','dealer','1','979078');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHREE BALA JI HOME','NULL',NULL,NULL,NULL,NULL,NULL,'4','87','dealer','1','979079');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dyanmic Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','88','dealer','1','979080');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rubi music corner ','NULL',NULL,NULL,NULL,NULL,NULL,'4','89','dealer','1','979081');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEVI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','90','dealer','1','979082');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vikram Company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','91','dealer','1','979083');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHAKTI RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','92','dealer','1','979084');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Duggal Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','93','dealer','1','979085');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amutha&co','NULL',NULL,NULL,NULL,NULL,NULL,'4','94','dealer','1','979086');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MANOHAR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','95','dealer','1','979087');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','96','dealer','1','979088');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ARIHANT ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','97','dealer','1','979089');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Videotex','vinay98716@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247041','partner','1','979090');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ADITYA APLIENCESS','NULL',NULL,NULL,NULL,NULL,NULL,'4','98','dealer','1','979091');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI JI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','99','dealer','1','979092');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAYUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','100','dealer','1','979093');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEALUX MAKETING','NULL',NULL,NULL,NULL,NULL,NULL,'4','101','dealer','1','979094');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KAMAL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','102','dealer','1','979095');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PATEL MUSIC CENTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','103','dealer','1','979096');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GURU KIRPA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','104','dealer','1','979097');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AJAY ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','105','dealer','1','979098');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEX ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','106','dealer','1','979099');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sahi electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','107','dealer','1','979100');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SURYAA TV PALACE','suryaakumar64@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','108','dealer','1','979101');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JHULE LAL COLECTION','NULL',NULL,NULL,NULL,NULL,NULL,'4','109','dealer','1','979102');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DILUX MKT','NULL',NULL,NULL,NULL,NULL,NULL,'4','110','dealer','1','979103');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Navaratann Enterprises','navaratannenterpriises@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','111','dealer','1','979104');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHARAT ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','112','dealer','1','979105');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ LUXMI ENTERP','NULL',NULL,NULL,NULL,NULL,NULL,'4','113','dealer','1','979106');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANIL ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','114','dealer','1','979107');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Panwood','nktitronics@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247042','partner','1','979108');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DHANLAXMI','NULL',NULL,NULL,NULL,NULL,NULL,'4','115','dealer','1','979109');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAMMI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','116','dealer','1','979110');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW PATEL ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','117','dealer','1','979111');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SUBHICHA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','118','dealer','1','979112');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AHUJA WATCH CO','NULL',NULL,NULL,NULL,NULL,NULL,'4','119','dealer','1','979113');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BOMBAY TELEVISION','NULL',NULL,NULL,NULL,NULL,NULL,'4','120','dealer','1','979114');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GUNJAN ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','121','dealer','1','979115');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PAYAL ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','122','dealer','1','979116');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PAYAL ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','123','dealer','1','979117');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AGGERWALL RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','124','dealer','1','979118');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHIV SAGER ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','125','dealer','1','979119');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMER DEEP VISION','NULL',NULL,NULL,NULL,NULL,NULL,'4','126','dealer','1','979120');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Smart Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','127','dealer','1','979121');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANKIT ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','128','dealer','1','979122');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAI RAM ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','129','dealer','1','979123');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('acjay electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','130','dealer','1','979124');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAHAL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','131','dealer','1','979125');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('POJA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','132','dealer','1','979126');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OMKAR ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','133','dealer','1','979127');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DHALIWAL ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','134','dealer','1','979128');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LAGAN ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','135','dealer','1','979129');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DHARADHAM','NULL',NULL,NULL,NULL,NULL,NULL,'4','136','dealer','1','979130');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SATELIGHT ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','138','dealer','1','979131');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('B.M. ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','139','dealer','1','979132');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GULAB ELEC','NULL',NULL,NULL,NULL,NULL,NULL,'4','140','dealer','1','979133');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shop services Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','141','dealer','1','979134');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('WARISH ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','142','dealer','1','979135');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ajnam electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','143','dealer','1','979136');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T V plus ','NULL',NULL,NULL,NULL,NULL,NULL,'4','144','dealer','1','979137');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('popular electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','145','dealer','1','979138');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RISHABH ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','146','dealer','1','979139');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SUNIL ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','147','dealer','1','979140');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHARP SELES','NULL',NULL,NULL,NULL,NULL,NULL,'4','148','dealer','1','979141');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sankri electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','149','dealer','1','979142');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW CENTER PLAZA','NULL',NULL,NULL,NULL,NULL,NULL,'4','150','dealer','1','979143');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sunjoy electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','151','dealer','1','979144');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MOTWANI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','152','dealer','1','979145');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KAILASH ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','153','dealer','1','979146');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHOK WATCH','NULL',NULL,NULL,NULL,NULL,NULL,'4','154','dealer','1','979147');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW MAYUR FERNICTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','155','dealer','1','979148');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BABA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','156','dealer','1','979149');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHIRWAD ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','157','dealer','1','979150');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SUNITA ENTERP','NULL',NULL,NULL,NULL,NULL,NULL,'4','158','dealer','1','979151');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAVI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','159','dealer','1','979152');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHA KAL ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','161','dealer','1','979153');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('XYZ-1','NULL',NULL,NULL,NULL,NULL,NULL,'4','162','dealer','1','979154');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('D K electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','163','dealer','1','979155');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MADHA FARMA FURNICHTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','164','dealer','1','979156');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KKY','Rajnichandok27@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247043','partner','1','979157');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SERVESH ELECT CENTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','165','dealer','1','979158');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NC PLAZA','NULL',NULL,NULL,NULL,NULL,NULL,'4','166','dealer','1','979159');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LAKCHAY ENTERP','NULL',NULL,NULL,NULL,NULL,NULL,'4','167','dealer','1','979160');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHAKTI RADIOS','NULL',NULL,NULL,NULL,NULL,NULL,'4','168','dealer','1','979161');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEVTA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','169','dealer','1','979162');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RIDHI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','170','dealer','1','979163');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VARANI ENTERP','NULL',NULL,NULL,NULL,NULL,NULL,'4','171','dealer','1','979164');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI SHRINIVASAN TRADING CO.','NULL',NULL,NULL,NULL,NULL,NULL,'4','172','dealer','1','979165');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ashwani Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','173','dealer','1','979166');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEEL KANTH ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','174','dealer','1','979167');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharp Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','175','dealer','1','979168');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Harjeet Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','176','dealer','1','979169');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('REVAL TEK AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','177','dealer','1','979170');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tirumalu Electronic sirsilla','NULL',NULL,NULL,NULL,NULL,NULL,'4','178','dealer','1','979171');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAILESH JI','NULL',NULL,NULL,NULL,NULL,NULL,'4','179','dealer','1','979172');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sharpronics Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','180','dealer','1','979173');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('F R Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','181','dealer','1','979174');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAVI VERMA JI','NULL',NULL,NULL,NULL,NULL,NULL,'4','182','dealer','1','979175');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Excellent Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','184','dealer','1','979176');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHARP TRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','185','dealer','1','979177');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VASTAWALL SELES','NULL',NULL,NULL,NULL,NULL,NULL,'4','186','dealer','1','979178');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DYNAMIC ELECTRONIC POINT','NULL',NULL,NULL,NULL,NULL,NULL,'4','187','dealer','1','979179');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Riky Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','188','dealer','1','979180');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Denitoko  ','NULL',NULL,NULL,NULL,NULL,NULL,'4','189','dealer','1','979181');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Bala Jii Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','190','dealer','1','979182');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SURESH KUMAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','191','dealer','1','979183');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMIT KUMAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','192','dealer','1','979184');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GROVER TELECOM','NULL',NULL,NULL,NULL,NULL,NULL,'4','193','dealer','1','979185');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Sound Services ','NULL',NULL,NULL,NULL,NULL,NULL,'4','195','dealer','1','979186');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Digital ','NULL',NULL,NULL,NULL,NULL,NULL,'4','196','dealer','1','979187');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ABC','NULL',NULL,NULL,NULL,NULL,NULL,'4','197','dealer','1','979188');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BADI INTERPRICES','NULL',NULL,NULL,NULL,NULL,NULL,'4','198','dealer','1','979189');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','199','dealer','1','979190');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Starc','starc.ecomm@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247045','partner','1','979191');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vans Genral store','NULL',NULL,NULL,NULL,NULL,NULL,'4','200','dealer','1','979192');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANTOSH SHARMA','NULL',NULL,NULL,NULL,NULL,NULL,'4','201','dealer','1','979193');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VAD PARKASH','NULL',NULL,NULL,NULL,NULL,NULL,'4','202','dealer','1','979194');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sagar Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','203','dealer','1','979195');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JYOTI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','204','dealer','1','979196');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAMART ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','205','dealer','1','979197');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TRINCE ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','210','dealer','1','979198');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('D B Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','212','dealer','1','979199');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAMBO TV CENTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','218','dealer','1','979200');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CHOPRA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','219','dealer','1','979201');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GUPTA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','224','dealer','1','979202');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SARO ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','226','dealer','1','979203');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEERU ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','228','dealer','1','979204');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Sembi electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','229','dealer','1','979205');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAKCHI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','230','dealer','1','979206');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HINDUSTAN AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','231','dealer','1','979207');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMAN ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','232','dealer','1','979208');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' Benit & madurai','NULL',NULL,NULL,NULL,NULL,NULL,'4','234','dealer','1','979209');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MEDIYA PLACE','NULL',NULL,NULL,NULL,NULL,NULL,'4','239','dealer','1','979210');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DESHMESH ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','240','dealer','1','979211');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GADA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','241','dealer','1','979212');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ULTRA SOUND','NULL',NULL,NULL,NULL,NULL,NULL,'4','243','dealer','1','979213');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('N.C PLAZA','NULL',NULL,NULL,NULL,NULL,NULL,'4','244','dealer','1','979214');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','245','dealer','1','979215');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('laxmi','NULL',NULL,NULL,NULL,NULL,NULL,'4','247','dealer','1','979216');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHENDER ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','248','dealer','1','979217');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Boskey International Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','249','dealer','1','979218');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SIFA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','250','dealer','1','979219');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VASUNDHRA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','251','dealer','1','979220');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHIKHAR ENTERP','NULL',NULL,NULL,NULL,NULL,NULL,'4','253','dealer','1','979221');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AJAY COMPUTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','254','dealer','1','979222');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Yash agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','255','dealer','1','979223');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S S ENTERP','NULL',NULL,NULL,NULL,NULL,NULL,'4','256','dealer','1','979224');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GUPTA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','257','dealer','1','979225');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('POOJA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','258','dealer','1','979226');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girija electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','259','dealer','1','979227');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('STAR ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','260','dealer','1','979228');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI MARUTI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','264','dealer','1','979229');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAM AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','265','dealer','1','979230');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LOVI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','266','dealer','1','979231');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HARE KRISHNA ELECT GZB','NULL',NULL,NULL,NULL,NULL,NULL,'4','267','dealer','1','979232');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TARANG ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','268','dealer','1','979233');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','269','dealer','1','979234');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anoop electronics Bareily','NULL',NULL,NULL,NULL,NULL,NULL,'4','270','dealer','1','979235');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Siyal Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','271','dealer','1','979236');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','272','dealer','1','979237');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('tanuja sale kanpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','273','dealer','1','979238');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SIHANI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','274','dealer','1','979239');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHAPURA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','275','dealer','1','979240');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KUMAR SALES TV AGENCY KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','276','dealer','1','979241');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CHADHA TV AGENCIES','NULL',NULL,NULL,NULL,NULL,NULL,'4','277','dealer','1','979242');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rahul Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','283','dealer','1','979243');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kalwant Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','284','dealer','1','979244');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SEETA RAAMA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','285','dealer','1','979245');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM SAI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','289','dealer','1','979246');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SMART ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','291','dealer','1','979247');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHAKTI ENTERPRISES, LUCKNOW','NULL',NULL,NULL,NULL,NULL,NULL,'4','296','dealer','1','979248');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('8500893466','NULL',NULL,NULL,NULL,NULL,NULL,'4','298','dealer','1','979249');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MARUTY ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','301','dealer','1','979250');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KRISHANA FURNITURE IMPORIMENT SALES','NULL',NULL,NULL,NULL,NULL,NULL,'4','302','dealer','1','979251');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SUNIL ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','303','dealer','1','979252');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJU SALES GOVINDPURAM,','NULL',NULL,NULL,NULL,NULL,NULL,'4','306','dealer','1','979253');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHREE GURU AMARNATH AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','307','dealer','1','979254');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI RAM ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','308','dealer','1','979255');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAGUN ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','309','dealer','1','979256');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ram Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','310','dealer','1','979257');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Neeraj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','311','dealer','1','979258');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KHALSHA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','312','dealer','1','979259');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mahesh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','314','dealer','1','979260');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HANU ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','315','dealer','1','979261');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MADHU BROTHERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','316','dealer','1','979262');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SIBBAN RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','317','dealer','1','979263');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TOMAR MOBILES &ELECT.','NULL',NULL,NULL,NULL,NULL,NULL,'4','318','dealer','1','979264');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANJAY ELECT.','NULL',NULL,NULL,NULL,NULL,NULL,'4','319','dealer','1','979265');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAI NATH ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','320','dealer','1','979266');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('King Store ','NULL',NULL,NULL,NULL,NULL,NULL,'4','321','dealer','1','979267');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KHATRI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','322','dealer','1','979268');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHWANI SALES (HARJINDER NAGAR)','NULL',NULL,NULL,NULL,NULL,NULL,'4','324','dealer','1','979269');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW SHRI J.P ELECTRONICS (SAKET NAGAR)','NULL',NULL,NULL,NULL,NULL,NULL,'4','325','dealer','1','979270');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAI TRADERS FUR.','NULL',NULL,NULL,NULL,NULL,NULL,'4','326','dealer','1','979271');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S.K. ELECTRONICS GARHIARANGEEN','NULL',NULL,NULL,NULL,NULL,NULL,'4','327','dealer','1','979272');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MS MIHANI SELES','NULL',NULL,NULL,NULL,NULL,NULL,'4','328','dealer','1','979273');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEELIMA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','329','dealer','1','979274');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANUJ ENTERPRIES','NULL',NULL,NULL,NULL,NULL,NULL,'4','330','dealer','1','979275');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Jammu Jas Center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','331','dealer','1','979276');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BOMBAY TELIVISION','NULL',NULL,NULL,NULL,NULL,NULL,'4','332','dealer','1','979277');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SARIKA ELECTRONIC, KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','334','dealer','1','979279');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('UTTASV ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','337','dealer','1','979280');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJU SALES ELECTRONIC KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','338','dealer','1','979281');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRAKASH ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','339','dealer','1','979282');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('India Tv Electronic  ','NULL',NULL,NULL,NULL,NULL,NULL,'4','340','dealer','1','979283');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TURBO ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','341','dealer','1','979284');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Krishna Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','342','dealer','1','979285');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MASHWARI ELECT,','NULL',NULL,NULL,NULL,NULL,NULL,'4','343','dealer','1','979286');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAI JINENDER ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','344','dealer','1','979287');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Samy','samyinformatics11@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247048','partner','1','979288');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KASHISH ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','346','dealer','1','979289');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TOMER MOBILE','NULL',NULL,NULL,NULL,NULL,NULL,'4','347','dealer','1','979290');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KAMAL ENTERP','NULL',NULL,NULL,NULL,NULL,NULL,'4','348','dealer','1','979291');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEEPAK ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','349','dealer','1','979292');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TEJ RADIO AND FRIZERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','350','dealer','1','979293');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KUMAR TV AGENCY NAUBASTA KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','351','dealer','1','979294');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Beep Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','352','dealer','1','979295');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VISHAL ELECTRONIC GAUNARIA RAMPUR, DEORIA','NULL',NULL,NULL,NULL,NULL,NULL,'4','353','dealer','1','979296');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW PATAL ELECT.','NULL',NULL,NULL,NULL,NULL,NULL,'4','354','dealer','1','979297');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HARSH ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','355','dealer','1','979298');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DIVYANSHI ENTERPRISES JARAULI PHASE 1 KARAHI BARRA','NULL',NULL,NULL,NULL,NULL,NULL,'4','356','dealer','1','979299');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nigam trader juhi','NULL',NULL,NULL,NULL,NULL,NULL,'4','357','dealer','1','979300');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JB visitronics private ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','358','dealer','1','979301');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PREM TALK AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','359','dealer','1','979302');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LAXMI WATCH HOUSE KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','360','dealer','1','979303');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Marchand electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','361','dealer','1','979304');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganeja Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','362','dealer','1','979305');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AHUJA JI RUDERPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','363','dealer','1','979306');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SEVI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','364','dealer','1','979307');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SRI MAA BHAGVATI SALES KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','365','dealer','1','979308');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRA','NULL',NULL,NULL,NULL,NULL,NULL,'4','366','dealer','1','979309');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rupen Marketing ','NULL',NULL,NULL,NULL,NULL,NULL,'4','367','dealer','1','979310');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T.S MARKETING','NULL',NULL,NULL,NULL,NULL,NULL,'4','368','dealer','1','979311');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CLIMATE ZONE PARK ROAD GORAKHPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','369','dealer','1','979312');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anakapally SriSrinivasa','NULL',NULL,NULL,NULL,NULL,NULL,'4','380','dealer','1','979313');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Veenus Radio, Barabanki','NULL',NULL,NULL,NULL,NULL,NULL,'4','381','dealer','1','979314');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Music mahal  Gorakhpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','382','dealer','1','979315');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHARPTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','383','dealer','1','979316');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SRI SRINIVASA CELL CARE','NULL',NULL,NULL,NULL,NULL,NULL,'4','391','dealer','1','979317');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharp Tronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','392','dealer','1','979318');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Versatile Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','393','dealer','1','979319');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sharp tronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','394','dealer','1','979320');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kumar electronics peppeganj','NULL',NULL,NULL,NULL,NULL,NULL,'4','395','dealer','1','979321');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new Apsra Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','396','dealer','1','979322');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('maheswari appliances','NULL',NULL,NULL,NULL,NULL,NULL,'4','397','dealer','1','979323');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jaspal Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','398','dealer','1','979324');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Srinivasa Home Appliance ','NULL',NULL,NULL,NULL,NULL,NULL,'4','399','dealer','1','979325');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raj Sales kanpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','400','dealer','1','979326');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Satyam Enterprises, Kanpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','401','dealer','1','979327');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharp Tronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','402','dealer','1','979328');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Laxmi Sai Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','403','dealer','1','979329');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HVD ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','404','dealer','1','979330');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJPOOT TRADERS KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','405','dealer','1','979331');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SATYAM TRADERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','406','dealer','1','979332');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHIVAM MARKETING KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','407','dealer','1','979333');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('YUVRAJ SALES KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','408','dealer','1','979334');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AAYUSH ELECTRONIC KHANDEPUR NAUBASTA KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','409','dealer','1','979335');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASTHA AGENCY KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','410','dealer','1','979336');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW KUMAR TV AGENCY KIBWAI NAGAR KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','411','dealer','1','979337');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sai Trading Company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','412','dealer','1','979338');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Benit & co','NULL',NULL,NULL,NULL,NULL,NULL,'4','413','dealer','1','979339');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','414','dealer','1','979340');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nuri Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','415','dealer','1','979341');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Deep Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','416','dealer','1','979342');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PREM ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','417','dealer','1','979343');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('om electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','418','dealer','1','979344');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HARISH WATCH','NULL',NULL,NULL,NULL,NULL,NULL,'4','419','dealer','1','979345');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rina Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','421','dealer','1','979346');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Verma Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','422','dealer','1','979347');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MULTI,PERPASE ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','423','dealer','1','979348');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BABA ELECTR2','NULL',NULL,NULL,NULL,NULL,NULL,'4','424','dealer','1','979349');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('prakash and sons (sisamau)','NULL',NULL,NULL,NULL,NULL,NULL,'4','425','dealer','1','979350');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T V House Datia ','NULL',NULL,NULL,NULL,NULL,NULL,'4','426','dealer','1','979351');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRINKA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','427','dealer','1','979352');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TOMER ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','428','dealer','1','979353');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('INDRA SALES RAWATPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','431','dealer','1','979354');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW RAJ SALES KARRAHI','NULL',NULL,NULL,NULL,NULL,NULL,'4','432','dealer','1','979355');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ ELECTRONIC KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','433','dealer','1','979356');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BOMBEY ELECTRONIC PURANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','434','dealer','1','979357');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raj elect panki','NULL',NULL,NULL,NULL,NULL,NULL,'4','435','dealer','1','979358');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ajmera Sales ','NULL',NULL,NULL,NULL,NULL,NULL,'4','436','dealer','1','979359');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Sai Balaji','NULL',NULL,NULL,NULL,NULL,NULL,'4','437','dealer','1','979360');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sonu Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','438','dealer','1','979361');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASWIN AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','439','dealer','1','979362');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW CITY AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','440','dealer','1','979363');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('usha-kings departmental','NULL',NULL,NULL,NULL,NULL,NULL,'4','441','dealer','1','979364');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ ELECTRONIC KUSHINAGAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','442','dealer','1','979365');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAMMI,,ELECT-2','NULL',NULL,NULL,NULL,NULL,NULL,'4','443','dealer','1','979366');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SETHI ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','444','dealer','1','979367');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SARKAR RADIO CORPORATION','NULL',NULL,NULL,NULL,NULL,NULL,'4','445','dealer','1','979368');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Around','anuj@247around.com',NULL,NULL,NULL,NULL,NULL,'4','247001','partner','1','979369');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kishor Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','446','dealer','1','979370');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM KAR ELECT.','NULL',NULL,NULL,NULL,NULL,NULL,'4','447','dealer','1','979371');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Candni Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','448','dealer','1','979372');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kanhaiya Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','449','dealer','1','979373');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mithlesh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','450','dealer','1','979374');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Subhash Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','451','dealer','1','979375');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GIFT GALLERY','NULL',NULL,NULL,NULL,NULL,NULL,'4','452','dealer','1','979376');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','453','dealer','1','979377');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMAN SINGH VISION','NULL',NULL,NULL,NULL,NULL,NULL,'4','454','dealer','1','979378');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANURAG ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','455','dealer','1','979379');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','456','dealer','1','979380');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MEET SALES KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','457','dealer','1','979381');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hari Om Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','458','dealer','1','979382');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('WYBOR','NULL',NULL,NULL,NULL,NULL,NULL,'4','459','dealer','1','979383');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('mahara Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','460','dealer','1','979384');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HARSH ELECTRONIC KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','461','dealer','1','979385');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHARPTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','462','dealer','1','979386');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Feltron','feltronservice@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247049','partner','1','979387');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW PUNJAB ELECTRONIC, KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','463','dealer','1','979388');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('krishna Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','464','dealer','1','979389');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NASEEM ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','465','dealer','1','979390');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('B.K ELEECTRONIC FATEHGANJ,FAIZABAD','NULL',NULL,NULL,NULL,NULL,NULL,'4','466','dealer','1','979391');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHOKA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','467','dealer','1','979392');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KHURANA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','468','dealer','1','979393');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Benit & Co Madurai ','NULL',NULL,NULL,NULL,NULL,NULL,'4','470','dealer','1','979394');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Satguru Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','471','dealer','1','979395');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SatGuru Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','472','dealer','1','979396');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hari Om Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','473','dealer','1','979397');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LUCKNOW AUDIO VISION','NULL',NULL,NULL,NULL,NULL,NULL,'4','474','dealer','1','979398');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('INDIA SPEED KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','475','dealer','1','979399');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S.K RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','476','dealer','1','979400');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRADEEP RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','477','dealer','1','979401');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gift Corner ','NULL',NULL,NULL,NULL,NULL,NULL,'4','478','dealer','1','979402');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gaytri Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','479','dealer','1','979403');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAGANI INTERPRICES','NULL',NULL,NULL,NULL,NULL,NULL,'4','480','dealer','1','979404');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TRIPATI JI','NULL',NULL,NULL,NULL,NULL,NULL,'4','481','dealer','1','979405');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJHARA','NULL',NULL,NULL,NULL,NULL,NULL,'4','482','dealer','1','979406');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AZIJ ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','483','dealer','1','979407');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAI SHARMA RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','484','dealer','1','979408');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAIJINDER ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','485','dealer','1','979409');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Basant Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','486','dealer','1','979410');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NIGAM TV CENTER, MAHARAJGANJ','NULL',NULL,NULL,NULL,NULL,NULL,'4','487','dealer','1','979411');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PANDEY ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','488','dealer','1','979412');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ajanta Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','489','dealer','1','979413');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shri Astha laxmi Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','491','dealer','1','979414');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Arati Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','492','dealer','1','979415');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Semby Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','493','dealer','1','979416');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BAJAJ ELECT, SUPER','NULL',NULL,NULL,NULL,NULL,NULL,'4','494','dealer','1','979417');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Verma Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','495','dealer','1','979418');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sharp Tronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','496','dealer','1','979419');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KUMAR ELECTRONIC,GORAKHNATH MANDIR','NULL',NULL,NULL,NULL,NULL,NULL,'4','497','dealer','1','979420');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shri srinavansa enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','498','dealer','1','979421');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('simran electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','499','dealer','1','979422');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('lokesh','NULL',NULL,NULL,NULL,NULL,NULL,'4','502','dealer','1','979423');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('puja Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','503','dealer','1','979424');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Durga Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','504','dealer','1','979425');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHARP TRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','505','dealer','1','979426');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharp Tronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','506','dealer','1','979427');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rina Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','507','dealer','1','979428');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHREE SAI ELECTRONIC, KUSHINAGAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','508','dealer','1','979429');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHARP TRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','509','dealer','1','979430');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Trimurti Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','510','dealer','1','979431');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Demo_Test','nits@247around.com',NULL,NULL,NULL,NULL,NULL,'4','247050','partner','1','979432');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tv Center Karimnagar','NULL',NULL,NULL,NULL,NULL,NULL,'4','511','dealer','1','979433');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAA ELECTRONIC, GORAKHPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','513','dealer','1','979434');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PARKAS ELECT.','NULL',NULL,NULL,NULL,NULL,NULL,'4','514','dealer','1','979435');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHESH TV HOUSE,','NULL',NULL,NULL,NULL,NULL,NULL,'4','515','dealer','1','979436');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SURESH ELECT.','NULL',NULL,NULL,NULL,NULL,NULL,'4','516','dealer','1','979437');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAA VAISHNO ENTERPRISES SHUKLAGANJ','NULL',NULL,NULL,NULL,NULL,NULL,'4','517','dealer','1','979438');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Janta Radio Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','518','dealer','1','979439');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharp Tronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','519','dealer','1','979440');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madan Machinery Mart ','NULL',NULL,NULL,NULL,NULL,NULL,'4','520','dealer','1','979441');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GADA ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','521','dealer','1','979442');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RADHIKA ELECTRONIC ,FIROZABAD','NULL',NULL,NULL,NULL,NULL,NULL,'4','522','dealer','1','979443');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','523','dealer','1','979444');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gandhi Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','524','dealer','1','979445');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tirupati Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','525','dealer','1','979446');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rakhi Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','526','dealer','1','979447');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Janta Radio Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','527','dealer','1','979448');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','528','dealer','1','979449');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Love Kush Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','529','dealer','1','979450');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T v Center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','531','dealer','1','979451');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Janta Radio Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','533','dealer','1','979452');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SUPER ELECTRONIC,SHAHJHANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','534','dealer','1','979453');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GSM ELEC. SHAJHANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','535','dealer','1','979454');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anil Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','538','dealer','1','979455');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','539','dealer','1','979456');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Poorna electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','540','dealer','1','979457');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ajay Electroworld','NULL',NULL,NULL,NULL,NULL,NULL,'4','541','dealer','1','979458');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sangam Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','542','dealer','1','979459');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mahindra electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','544','dealer','1','979460');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NIKHIL ELECT. KUSHINAGAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','545','dealer','1','979461');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Home Mart electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','546','dealer','1','979462');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ACE-Artha Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','547','dealer','1','979463');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ON TRACK ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','548','dealer','1','979464');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Priya Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','549','dealer','1','979465');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bhumika Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','550','dealer','1','979466');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('On track tv show room ','NULL',NULL,NULL,NULL,NULL,NULL,'4','551','dealer','1','979467');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pinki Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','555','dealer','1','979471');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('janta ','NULL',NULL,NULL,NULL,NULL,NULL,'4','556','dealer','1','979472');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Lalit Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','557','dealer','1','979473');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jatin Brothers,Firozabad','NULL',NULL,NULL,NULL,NULL,NULL,'4','558','dealer','1','979474');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAGHUVAR ELEC. JAFRANA','NULL',NULL,NULL,NULL,NULL,NULL,'4','559','dealer','1','979475');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Priya Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','560','dealer','1','979476');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Janta Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','562','dealer','1','979478');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','surya.malladi@akaiindia.in',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','979479');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shree electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','563','dealer','1','979480');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pinki Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','564','dealer','1','979481');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' baja electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','565','dealer','1','979482');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('bajaj electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','566','dealer','1','979483');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Baja electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','567','dealer','1','979484');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SREE ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','568','dealer','1','979485');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bombay Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','569','dealer','1','979486');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tv center  Karimnagar ','NULL',NULL,NULL,NULL,NULL,NULL,'4','570','dealer','1','979487');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Satguru','NULL',NULL,NULL,NULL,NULL,NULL,'4','571','dealer','1','979488');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri sai enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','572','dealer','1','979489');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rina Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','573','dealer','1','979490');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ambey electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','574','dealer','1','979491');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('FURNITURE REPAIRING CENTRE','NULL',NULL,NULL,NULL,NULL,NULL,'4','575','dealer','1','979492');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ExtendedWarranty','neha@warranty.co.in',NULL,NULL,NULL,NULL,NULL,'4','247053','partner','1','979493');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CorporateWarranty','neha@warranty.co.in',NULL,NULL,NULL,NULL,NULL,'4','247054','partner','1','979494');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Consistent','pradeep.p@consistent.in',NULL,NULL,NULL,NULL,NULL,'4','247052','partner','1','979495');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Santoshi Mata Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','577','dealer','1','979496');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SRI SAI DURGA ENTERPRISES.','NULL',NULL,NULL,NULL,NULL,NULL,'4','578','dealer','1','979497');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BAJAJ ELECTRONICS KOTI','NULL',NULL,NULL,NULL,NULL,NULL,'4','579','dealer','1','979498');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEET SALES','NULL',NULL,NULL,NULL,NULL,NULL,'4','580','dealer','1','979499');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bunty General Store','NULL',NULL,NULL,NULL,NULL,NULL,'4','581','dealer','1','979500');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','582','dealer','1','979501');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Khater Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','583','dealer','1','979502');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('A R REFRIGERATION','NULL',NULL,NULL,NULL,NULL,NULL,'4','584','dealer','1','979503');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('national recording','NULL',NULL,NULL,NULL,NULL,NULL,'4','585','dealer','1','979504');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DIWAKAR ELECTRONICS TANUKU','NULL',NULL,NULL,NULL,NULL,NULL,'4','586','dealer','1','979505');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BAJAJ ELECTRONICS ','NULL',NULL,NULL,NULL,NULL,NULL,'4','587','dealer','1','979506');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ME Electonics','NULL',NULL,NULL,NULL,NULL,NULL,'4','588','dealer','1','979507');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BABA ELEC. SOLAN','NULL',NULL,NULL,NULL,NULL,NULL,'4','589','dealer','1','979508');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nirmal Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','590','dealer','1','979509');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sharp Tronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','591','dealer','1','979510');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Happy TV Center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','592','dealer','1','979511');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Diwakar Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','593','dealer','1','979512');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pinky Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','594','dealer','1','979513');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Thrimurthy Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','595','dealer','1','979514');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Paras Creation','NULL',NULL,NULL,NULL,NULL,NULL,'4','596','dealer','1','979515');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Tirmumala Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','597','dealer','1','979516');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vasavi Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','598','dealer','1','979517');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronics Panjagutta .','NULL',NULL,NULL,NULL,NULL,NULL,'4','599','dealer','1','979518');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maa Pushpa Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','600','dealer','1','979519');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ME ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','601','dealer','1','979520');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JJ ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','602','dealer','1','979521');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('F R Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','603','dealer','1','979522');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('UDAY SAMARAT ELECTRONICS. ','NULL',NULL,NULL,NULL,NULL,NULL,'4','604','dealer','1','979523');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SURYA HOME ','NULL',NULL,NULL,NULL,NULL,NULL,'4','605','dealer','1','979524');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','606','dealer','1','979525');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ALOK JI','NULL',NULL,NULL,NULL,NULL,NULL,'4','607','dealer','1','979526');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Radha enterprise','NULL',NULL,NULL,NULL,NULL,NULL,'4','608','dealer','1','979527');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Trigal Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','609','dealer','1','979528');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('V N Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','610','dealer','1','979529');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Weston','nits@247around.com',NULL,NULL,NULL,NULL,NULL,'4','247055','partner','1','979530');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rina Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','611','dealer','1','979531');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Khadin Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','612','dealer','1','979532');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('H C R Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','613','dealer','1','979533');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kesartha Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','614','dealer','1','979534');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Priya Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','615','dealer','1','979535');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sree Techno Services ','NULL',NULL,NULL,NULL,NULL,NULL,'4','616','dealer','1','979536');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jay Shree Ganesh Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','617','dealer','1','979537');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Surya Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','618','dealer','1','979538');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharp Tronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','619','dealer','1','979539');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('D L N Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','620','dealer','1','979540');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VIJAY ELEC. KHAJANI','NULL',NULL,NULL,NULL,NULL,NULL,'4','621','dealer','1','979541');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chitra Electronic & Communication ','NULL',NULL,NULL,NULL,NULL,NULL,'4','622','dealer','1','979542');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHAK MUSIC CENTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','623','dealer','1','979543');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LAKSHMI GANAPATHI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','624','dealer','1','979544');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJESH ELEC. MAHARAJGANJ','NULL',NULL,NULL,NULL,NULL,NULL,'4','625','dealer','1','979545');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T V plus ','NULL',NULL,NULL,NULL,NULL,NULL,'4','626','dealer','1','979546');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('R R Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','627','dealer','1','979547');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sudha electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','628','dealer','1','979548');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('B.K ELECTRONICS,FAIZABAD','NULL',NULL,NULL,NULL,NULL,NULL,'4','629','dealer','1','979549');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Trimurti Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','630','dealer','1','979550');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DIAMOND ELECTRICALS','NULL',NULL,NULL,NULL,NULL,NULL,'4','631','dealer','1','979551');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Lavniya Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','632','dealer','1','979552');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' kings departmental stores','NULL',NULL,NULL,NULL,NULL,NULL,'4','633','dealer','1','979553');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TV PALACE AND APLIENCES','NULL',NULL,NULL,NULL,NULL,NULL,'4','634','dealer','1','979554');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KULDEEP ENT. AGRA','NULL',NULL,NULL,NULL,NULL,NULL,'4','635','dealer','1','979555');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' radha enterprise dealer','NULL',NULL,NULL,NULL,NULL,NULL,'4','636','dealer','1','979556');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('G N Agency ','NULL',NULL,NULL,NULL,NULL,NULL,'4','637','dealer','1','979557');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','363','engineer','1','979558');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','365','engineer','1','979559');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Batra marketing','NULL',NULL,NULL,NULL,NULL,NULL,'4','638','dealer','1','979560');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Salora','NULL',NULL,NULL,NULL,NULL,NULL,'4','639','dealer','1','979561');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kiran Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','640','dealer','1','979562');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jaggii Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','641','dealer','1','979563');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Lalith Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','642','dealer','1','979564');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HARGOVIND ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','643','dealer','1','979565');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AJAY RADIO PATH','NULL',NULL,NULL,NULL,NULL,NULL,'4','644','dealer','1','979566');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ajay Music Center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','645','dealer','1','979567');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','372','engineer','1','979568');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Top Ten Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','646','dealer','1','979569');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Salora','pramesh.bhatia@salora.com, nits@247around.com,ced-service-ho@salora.com',NULL,NULL,NULL,NULL,NULL,'4','247064','partner','1','979570');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Dinesh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','647','dealer','1','979571');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shubham Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','648','dealer','1','979572');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Videotex-sts','vinay98716@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247041','partner','1','979573');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jiwan Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','649','dealer','1','979574');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BABU','NULL',NULL,NULL,NULL,NULL,NULL,'4','650','dealer','1','979575');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JANATA ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','651','dealer','1','979576');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHYAM TRADERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','652','dealer','1','979577');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Air Cooling Solutions ','NULL',NULL,NULL,NULL,NULL,NULL,'4','653','dealer','1','979578');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW MISHRA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','654','dealer','1','979579');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TARA ENT. GWALIOR','NULL',NULL,NULL,NULL,NULL,NULL,'4','655','dealer','1','979580');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GURU NANAK RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','656','dealer','1','979581');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Surya home Needs ','NULL',NULL,NULL,NULL,NULL,NULL,'4','657','dealer','1','979582');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MODERN ELEC . GHANTAGHAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','658','dealer','1','979583');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('A.R VISION ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','659','dealer','1','979584');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gopi Krishna Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','660','dealer','1','979585');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Indytech','nits@247around.com, indytechtv@gmail.com, vijaya@247around.com',NULL,NULL,NULL,NULL,NULL,'4','247065','partner','1','979586');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Trimurthy Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','661','dealer','1','979587');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BABA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','662','dealer','1','979588');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jaffer Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','663','dealer','1','979589');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI VEDIK ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','664','dealer','1','979590');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HARI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','665','dealer','1','979591');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ARNAV SALES ( RAKESH)','NULL',NULL,NULL,NULL,NULL,NULL,'4','666','dealer','1','979592');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MISHRA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','667','dealer','1','979593');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Divyanshu Refrigeration ','NULL',NULL,NULL,NULL,NULL,NULL,'4','668','dealer','1','979594');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharp Tronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','669','dealer','1','979595');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VANSHIKA ELECTRONIC, SHAMSABAD','NULL',NULL,NULL,NULL,NULL,NULL,'4','670','dealer','1','979596');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SARKAR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','671','dealer','1','979597');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Suren Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','672','dealer','1','979598');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAIN AUDIO VIDEO','NULL',NULL,NULL,NULL,NULL,NULL,'4','673','dealer','1','979599');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('srikanth DJ shopping','NULL',NULL,NULL,NULL,NULL,NULL,'4','674','dealer','1','979600');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shubitra Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','675','dealer','1','979601');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vishal Computer','NULL',NULL,NULL,NULL,NULL,NULL,'4','676','dealer','1','979602');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TV center (9059664555)','NULL',NULL,NULL,NULL,NULL,NULL,'4','677','dealer','1','979603');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('classic electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','678','dealer','1','979604');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Trigal Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','679','dealer','1','979605');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dinesh Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','680','dealer','1','979606');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' S P Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','681','dealer','1','979607');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Vasavi Agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','682','dealer','1','979608');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Cristal Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','683','dealer','1','979609');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Deepak electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','684','dealer','1','979610');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dream Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','685','dealer','1','979611');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEV ELEC.','NULL',NULL,NULL,NULL,NULL,NULL,'4','686','dealer','1','979612');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sri uenkateswara Agency ','NULL',NULL,NULL,NULL,NULL,NULL,'4','687','dealer','1','979613');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('G.S TRADERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','688','dealer','1','979614');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','390','engineer','1','979615');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','391','engineer','1','979616');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('global electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','689','dealer','1','979617');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj electronics chintal','NULL',NULL,NULL,NULL,NULL,NULL,'4','690','dealer','1','979618');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MANGAL DEEP ELEC. ASHOKNAGAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','691','dealer','1','979619');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pinky electronic (Akai)','NULL',NULL,NULL,NULL,NULL,NULL,'4','692','dealer','1','979620');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','392','engineer','1','979621');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','393','engineer','1','979622');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kumaran traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','693','dealer','1','979623');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Benit & Co Madurai ','NULL',NULL,NULL,NULL,NULL,NULL,'4','694','dealer','1','979624');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAXENA ELEC. BAREILLY','NULL',NULL,NULL,NULL,NULL,NULL,'4','695','dealer','1','979625');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SREE SURYA STORE','NULL',NULL,NULL,NULL,NULL,NULL,'4','696','dealer','1','979626');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','398','engineer','1','979627');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJAT ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','697','dealer','1','979628');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S.K ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','698','dealer','1','979629');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Venkateswara Agnecies ','NULL',NULL,NULL,NULL,NULL,NULL,'4','699','dealer','1','979630');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW DEEP AGRA','NULL',NULL,NULL,NULL,NULL,NULL,'4','700','dealer','1','979631');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Trimurti Electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','701','dealer','1','979632');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Goyal enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','702','dealer','1','979633');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Taneja Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','703','dealer','1','979634');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Himalaya Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','704','dealer','1','979635');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pooja','NULL',NULL,NULL,NULL,NULL,NULL,'4','705','dealer','1','979636');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharp Tronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','706','dealer','1','979637');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','404','engineer','1','979638');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Surya home needs','NULL',NULL,NULL,NULL,NULL,NULL,'4','707','dealer','1','979639');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Premnagar electronics Sathupali','NULL',NULL,NULL,NULL,NULL,NULL,'4','708','dealer','1','979640');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('QFX','anshul@mittalsolutions.com',NULL,NULL,NULL,NULL,NULL,'4','247066','partner','1','979641');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEEP ENT. BHATAPARA','NULL',NULL,NULL,NULL,NULL,NULL,'4','709','dealer','1','979642');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Diamond Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','710','dealer','1','979643');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pinky Electronic ( Akai Hyderabad )','NULL',NULL,NULL,NULL,NULL,NULL,'4','711','dealer','1','979644');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','406','engineer','1','979645');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Yash Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','712','dealer','1','979646');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('QFX','anshul@mittalsolutions.com',NULL,NULL,NULL,NULL,NULL,'4','247066','partner','1','979647');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Thirumala Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','713','dealer','1','979648');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MUSIC  AND LIGHT,GORAKHPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','714','dealer','1','979649');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','409','engineer','1','979650');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','410','engineer','1','979651');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HANISH SEHGAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','411','engineer','1','979652');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','412','engineer','1','979653');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','413','engineer','1','979654');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','414','engineer','1','979655');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Parish Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','715','dealer','1','979656');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANCOOL AIRCON, MEERUT','NULL',NULL,NULL,NULL,NULL,NULL,'4','716','dealer','1','979657');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tirupati Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','717','dealer','1','979658');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj electronics  Vijayawada 1','NULL',NULL,NULL,NULL,NULL,NULL,'4','718','dealer','1','979659');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Traders ','NULL',NULL,NULL,NULL,NULL,NULL,'4','719','dealer','1','979660');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SUKHDEV TV CENTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','720','dealer','1','979661');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','418','engineer','1','979662');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RS steel Furniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','721','dealer','1','979663');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TYAGI ASSOCIATE','NULL',NULL,NULL,NULL,NULL,NULL,'4','722','dealer','1','979664');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEVA TRADERS,BARABANKI','NULL',NULL,NULL,NULL,NULL,NULL,'4','723','dealer','1','979665');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('M.K. ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','724','dealer','1','979666');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Detel','yogesh@sgcorp.in, nits@247around.com, coordination@sgcorp.in, munish.jindal@sgcorp.in',NULL,NULL,NULL,NULL,NULL,'4','247068','partner','1','979667');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Classic Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','725','dealer','1','979668');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TELEYUG','NULL',NULL,NULL,NULL,NULL,NULL,'4','726','dealer','1','979669');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hanuman Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','727','dealer','1','979670');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ravi ','NULL',NULL,NULL,NULL,NULL,NULL,'4','728','dealer','1','979671');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','729','dealer','1','979672');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','730','dealer','1','979673');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Baja Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','732','dealer','1','979674');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj electronics karimnagar','NULL',NULL,NULL,NULL,NULL,NULL,'4','733','dealer','1','979675');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sai kinnera electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','734','dealer','1','979676');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('manav elec. mathura','NULL',NULL,NULL,NULL,NULL,NULL,'4','735','dealer','1','979677');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('akhtar hussain ','NULL',NULL,NULL,NULL,NULL,NULL,'4','736','dealer','1','979678');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JANAKI ELEC. BHIND','NULL',NULL,NULL,NULL,NULL,NULL,'4','737','dealer','1','979679');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Yash firniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','738','dealer','1','979680');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Golden Prime','arshi.ansari@goldenprime.co.in',NULL,NULL,NULL,NULL,NULL,'4','247069','partner','1','979681');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','422','engineer','1','979682');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SATYAM ENT. SIDHARTHNAGAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','739','dealer','1','979683');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kuber Trading Company','NULL',NULL,NULL,NULL,NULL,NULL,'4','740','dealer','1','979684');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AKI SATYANARAYANA KIRANA & GENRALS','NULL',NULL,NULL,NULL,NULL,NULL,'4','741','dealer','1','979685');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BEST ENTERPRISES PATIYAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','742','dealer','1','979686');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','743','dealer','1','979687');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ajay electro ward ','NULL',NULL,NULL,NULL,NULL,NULL,'4','744','dealer','1','979688');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shiva Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','745','dealer','1','979689');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('xyz','NULL',NULL,NULL,NULL,NULL,NULL,'4','746','dealer','1','979690');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DOORDARSHAN ELECTONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','747','dealer','1','979691');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Ram Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','748','dealer','1','979692');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Approve Mr Murthi ','NULL',NULL,NULL,NULL,NULL,NULL,'4','749','dealer','1','979693');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JASAN ELECTRONICS PATIALA','NULL',NULL,NULL,NULL,NULL,NULL,'4','750','dealer','1','979694');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','751','dealer','1','979695');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T V Center ( Karim nagar )','NULL',NULL,NULL,NULL,NULL,NULL,'4','752','dealer','1','979696');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAA BHAGWATI ELECTRONICS, MATHURA','NULL',NULL,NULL,NULL,NULL,NULL,'4','753','dealer','1','979697');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Srikant Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','754','dealer','1','979698');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mr Murthi ','NULL',NULL,NULL,NULL,NULL,NULL,'4','755','dealer','1','979699');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jeeves','naveen.n@jeeves.co.in',NULL,NULL,NULL,NULL,NULL,'4','247030','partner','1','979700');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jeeves-sts','naveen.n@jeeves.co.in',NULL,NULL,NULL,NULL,NULL,'4','247030','partner','1','979701');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Markson','service.markson@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247070','partner','1','979703');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NISAR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','756','dealer','1','979704');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bashudev electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','757','dealer','1','979705');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMRIT REDIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','758','dealer','1','979706');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','759','dealer','1','979707');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHUBHAM DESAI','NULL',NULL,NULL,NULL,NULL,NULL,'4','760','dealer','1','979708');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AV ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','761','dealer','1','979709');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ATUL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','762','dealer','1','979710');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','763','dealer','1','979711');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW MAHESHWARI HOME APP.','NULL',NULL,NULL,NULL,NULL,NULL,'4','764','dealer','1','979712');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madhusudhan','NULL',NULL,NULL,NULL,NULL,NULL,'4','765','dealer','1','979713');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JITENDER ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','766','dealer','1','979714');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BANSOD ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','767','dealer','1','979715');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Deep Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','768','dealer','1','979716');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRIME SOLUATIONS NAGPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','769','dealer','1','979717');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Satguru Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','770','dealer','1','979718');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tripurti Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','771','dealer','1','979719');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANUBHAV ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','772','dealer','1','979720');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','773','dealer','1','979721');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganesh Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','774','dealer','1','979722');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gumber Tv Center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','775','dealer','1','979723');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','776','dealer','1','979724');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SIDDHI MULTI TECHNOLOGY PVT LTD','NULL',NULL,NULL,NULL,NULL,NULL,'4','777','dealer','1','979725');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HAPPY','NULL',NULL,NULL,NULL,NULL,NULL,'4','778','dealer','1','979726');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CANTED ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','779','dealer','1','979727');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRALAD KAWALE','NULL',NULL,NULL,NULL,NULL,NULL,'4','780','dealer','1','979728');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','781','dealer','1','979729');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('THRIMURTY ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','782','dealer','1','979730');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GOL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','783','dealer','1','979731');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JYOTI','NULL',NULL,NULL,NULL,NULL,NULL,'4','784','dealer','1','979732');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','430','engineer','1','979733');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','785','dealer','1','979734');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHVAN ARORA','NULL',NULL,NULL,NULL,NULL,NULL,'4','786','dealer','1','979735');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Willett','micky@willettcable.com',NULL,NULL,NULL,NULL,NULL,'4','247071','partner','1','979736');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S S COMMUNICATION','NULL',NULL,NULL,NULL,NULL,NULL,'4','787','dealer','1','979737');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Agency ','NULL',NULL,NULL,NULL,NULL,NULL,'4','788','dealer','1','979738');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','789','dealer','1','979739');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW TIRUPATI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','790','dealer','1','979740');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAI MARKETING','NULL',NULL,NULL,NULL,NULL,NULL,'4','791','dealer','1','979741');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kaykcee Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','792','dealer','1','979742');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJENDRA','NULL',NULL,NULL,NULL,NULL,NULL,'4','793','dealer','1','979743');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MY KITCHEN WORLD','NULL',NULL,NULL,NULL,NULL,NULL,'4','794','dealer','1','979744');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JANATA ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','795','dealer','1','979745');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('K K Bartan Bhandar ','NULL',NULL,NULL,NULL,NULL,NULL,'4','796','dealer','1','979746');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAYA','NULL',NULL,NULL,NULL,NULL,NULL,'4','797','dealer','1','979747');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DIPAK MUKATE','NULL',NULL,NULL,NULL,NULL,NULL,'4','798','dealer','1','979748');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chandu Electronics & Electricals','NULL',NULL,NULL,NULL,NULL,NULL,'4','799','dealer','1','979749');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Laxmi electronic and electric','NULL',NULL,NULL,NULL,NULL,NULL,'4','800','dealer','1','979750');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PUSHPANJALI (GIRISH GUPTA )','NULL',NULL,NULL,NULL,NULL,NULL,'4','801','dealer','1','979751');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KABRA AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','802','dealer','1','979752');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TUMESH ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','803','dealer','1','979753');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('THREE BROTHERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','804','dealer','1','979754');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GAGAN ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','805','dealer','1','979755');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PATEL ELECTRONICS KALAMBOLI','NULL',NULL,NULL,NULL,NULL,NULL,'4','806','dealer','1','979756');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LIMRA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','807','dealer','1','979757');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DIAMOND ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','808','dealer','1','979758');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DHRUV ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','809','dealer','1','979759');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ADITYA VAS AND ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','810','dealer','1','979760');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHARAT ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','811','dealer','1','979761');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KHANDELWAL ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','812','dealer','1','979762');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tip Top Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','813','dealer','1','979763');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRAVIN ELECTRINCALS','NULL',NULL,NULL,NULL,NULL,NULL,'4','814','dealer','1','979764');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ROYAL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','815','dealer','1','979765');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LIMRA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','816','dealer','1','979766');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','817','dealer','1','979767');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','818','dealer','1','979768');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Himanshu Electronics Nagpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','819','dealer','1','979769');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAHA ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','820','dealer','1','979770');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','821','dealer','1','979771');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SATISH','NULL',NULL,NULL,NULL,NULL,NULL,'4','822','dealer','1','979772');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ashok Raj Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','823','dealer','1','979773');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AGRWAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','824','dealer','1','979774');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AKRITI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','825','dealer','1','979775');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AKASHWANI ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','826','dealer','1','979776');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ajay Electro world','NULL',NULL,NULL,NULL,NULL,NULL,'4','827','dealer','1','979777');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANJUM ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','828','dealer','1','979778');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raj Deep Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','829','dealer','1','979779');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Universal Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','830','dealer','1','979780');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ME ELECTRONICS & FURNITURES','NULL',NULL,NULL,NULL,NULL,NULL,'4','831','dealer','1','979781');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KHAN','NULL',NULL,NULL,NULL,NULL,NULL,'4','832','dealer','1','979782');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Khandel Electronic and Computers ','NULL',NULL,NULL,NULL,NULL,NULL,'4','833','dealer','1','979783');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pincky Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','834','dealer','1','979784');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('united Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','835','dealer','1','979785');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prakash Treders Nagpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','836','dealer','1','979786');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Balaji Electronics Wadi','NULL',NULL,NULL,NULL,NULL,NULL,'4','837','dealer','1','979787');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vardaan Enterprises Kalyan','NULL',NULL,NULL,NULL,NULL,NULL,'4','838','dealer','1','979788');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAVINDRA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','839','dealer','1','979789');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRIJI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','840','dealer','1','979790');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ( Vijaywada )','NULL',NULL,NULL,NULL,NULL,NULL,'4','841','dealer','1','979791');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ATTARI TREDARS MODHA','NULL',NULL,NULL,NULL,NULL,NULL,'4','842','dealer','1','979792');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('My Kitchen World','NULL',NULL,NULL,NULL,NULL,NULL,'4','843','dealer','1','979793');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HINDUSTAN ELEC. JALESAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','844','dealer','1','979794');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ELECTRONIC HOUSE','NULL',NULL,NULL,NULL,NULL,NULL,'4','845','dealer','1','979795');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('R K ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','846','dealer','1','979796');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MODERN ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','847','dealer','1','979797');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VISHAL ELECTRONICS KALAMBOLI','NULL',NULL,NULL,NULL,NULL,NULL,'4','848','dealer','1','979798');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KOHINOOR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','849','dealer','1','979799');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHISH ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','850','dealer','1','979800');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Max Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','851','dealer','1','979801');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anmol Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','852','dealer','1','979802');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri IG Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','853','dealer','1','979803');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Homecare Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','854','dealer','1','979804');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Guru interprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','855','dealer','1','979805');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','856','dealer','1','979806');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gujrat Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','857','dealer','1','979807');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pragati Varma Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','858','dealer','1','979808');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Zintex','zintexledtv@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247072','partner','1','979809');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','859','dealer','1','979810');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAI NILKAMAL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','860','dealer','1','979811');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SS MARKETING','NULL',NULL,NULL,NULL,NULL,NULL,'4','861','dealer','1','979812');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gupta Vision','NULL',NULL,NULL,NULL,NULL,NULL,'4','862','dealer','1','979813');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BULBUL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','863','dealer','1','979814');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Veer Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','864','dealer','1','979815');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SACH TRADING COMPANY','NULL',NULL,NULL,NULL,NULL,NULL,'4','865','dealer','1','979816');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ruchita Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','866','dealer','1','979817');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KANHEYA TREDERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','867','dealer','1','979818');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI BALAJI ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','868','dealer','1','979819');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANDEEP ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','869','dealer','1','979820');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','870','dealer','1','979821');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chinmay Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','871','dealer','1','979822');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','872','dealer','1','979823');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AKSHAY SALES','NULL',NULL,NULL,NULL,NULL,NULL,'4','873','dealer','1','979824');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHAVEER ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','874','dealer','1','979825');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MALIK ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','875','dealer','1','979826');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI CHARBHUJA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','876','dealer','1','979827');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PARAS ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','877','dealer','1','979828');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MOTHER ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','878','dealer','1','979829');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI KRISHNA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','879','dealer','1','979830');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Venketash Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','880','dealer','1','979831');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gurukrupa electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','881','dealer','1','979832');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Badrika Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','882','dealer','1','979833');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ROSE ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','883','dealer','1','979834');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('FINE ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','884','dealer','1','979835');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHIVAY AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','885','dealer','1','979836');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jay Electrovision','NULL',NULL,NULL,NULL,NULL,NULL,'4','886','dealer','1','979837');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raju Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','887','dealer','1','979838');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jay Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','888','dealer','1','979839');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','889','dealer','1','979840');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','890','dealer','1','979841');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S D Technology ','NULL',NULL,NULL,NULL,NULL,NULL,'4','891','dealer','1','979842');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Varma Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','892','dealer','1','979843');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','893','dealer','1','979844');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','440','engineer','1','979845');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Duryagamalleswara Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','894','dealer','1','979846');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aradhana Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','895','dealer','1','979847');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SUNSHINE COMPUTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','896','dealer','1','979848');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TAAL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','897','dealer','1','979849');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VINAYAK ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','898','dealer','1','979850');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SNR DEGITAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','899','dealer','1','979851');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T-Series','rakeshkumarsharmaced@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979852');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SATYAM STEEL FINNITURE','NULL',NULL,NULL,NULL,NULL,NULL,'4','900','dealer','1','979853');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','901','dealer','1','979854');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AEON POWER INTERNATIONAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','902','dealer','1','979855');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('J J Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','903','dealer','1','979856');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','446','engineer','1','979857');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BALRAJ ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','904','dealer','1','979858');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Geeta Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','905','dealer','1','979859');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','906','dealer','1','979860');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Adtiya Home Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','907','dealer','1','979861');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('K.C ELEC GWALIOR','NULL',NULL,NULL,NULL,NULL,NULL,'4','908','dealer','1','979862');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('J.K ELEC.SHAHJHANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','909','dealer','1','979863');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI KANKDURGA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','910','dealer','1','979864');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM ELECTRONICS & MOBILES','NULL',NULL,NULL,NULL,NULL,NULL,'4','911','dealer','1','979865');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','912','dealer','1','979866');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI CHARBHUJA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','913','dealer','1','979867');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gandhi Sales Jarod','NULL',NULL,NULL,NULL,NULL,NULL,'4','914','dealer','1','979868');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vijay Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','915','dealer','1','979869');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Getendra Gift Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','916','dealer','1','979870');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Best Vision Shamgrah','NULL',NULL,NULL,NULL,NULL,NULL,'4','917','dealer','1','979871');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','918','dealer','1','979872');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('XYZ','NULL',NULL,NULL,NULL,NULL,NULL,'4','919','dealer','1','979873');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AKIB BROTHERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','920','dealer','1','979874');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','448','engineer','1','979875');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ibrahim Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','921','dealer','1','979876');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jassy electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','922','dealer','1','979877');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','449','engineer','1','979878');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arihant Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','923','dealer','1','979879');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Khanna TV Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','924','dealer','1','979880');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','925','dealer','1','979881');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dewansh Entrprise','NULL',NULL,NULL,NULL,NULL,NULL,'4','926','dealer','1','979882');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Darshan Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','927','dealer','1','979883');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bhgay Shree Eletronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','928','dealer','1','979884');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HINDUSTAN  GIFT HOUSE,','NULL',NULL,NULL,NULL,NULL,NULL,'4','929','dealer','1','979885');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sree Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','930','dealer','1','979886');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','931','dealer','1','979887');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHARAT FIRNITURE ELECTRONIC DEVISION','NULL',NULL,NULL,NULL,NULL,NULL,'4','932','dealer','1','979888');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','933','dealer','1','979889');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nanda Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','934','dealer','1','979890');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RajBig Bazar','NULL',NULL,NULL,NULL,NULL,NULL,'4','935','dealer','1','979891');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tirupati Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','936','dealer','1','979892');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hi-Tech Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','937','dealer','1','979893');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Singh Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','938','dealer','1','979894');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','939','dealer','1','979895');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','940','dealer','1','979896');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Guru Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','941','dealer','1','979897');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('UNION RADIO AND ELECTRICAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','942','dealer','1','979898');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kuswaha electronic firozabad','NULL',NULL,NULL,NULL,NULL,NULL,'4','943','dealer','1','979914');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New ajay radio ,Gorakhpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','944','dealer','1','979915');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vinod kumar','vinodkumar@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979916');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rakesh kumar sharma','rakeshkumarsharma@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979917');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gagan Pathak','gaganpathak@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979918');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Subhash Mehta','tvservice@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979919');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pradeep Roy','pradeeproy@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979920');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Harish','harishced@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979921');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Richa sharma','consumerservice@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979922');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Renu','consumerserviceone@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979923');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manju','electronics@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979924');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rajesh Dutta','rajeshdutta@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979925');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ramesh sharma','jalandhar@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979926');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('B B Dubey','gwalior@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979927');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Suresh sharma','jaipur@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979928');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rupesh kumar','patna@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979929');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tapas','kolkata@tseries.net',NULL,NULL,NULL,NULL,NULL,'4','247073','partner','1','979930');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganesh Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','945','dealer','1','979931');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sachdev laptop and mobile shop','NULL',NULL,NULL,NULL,NULL,NULL,'4','946','dealer','1','979932');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','947','dealer','1','979933');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('suvidha','NULL',NULL,NULL,NULL,NULL,NULL,'4','948','dealer','1','979934');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sabi Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','949','dealer','1','979935');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','950','dealer','1','979936');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI ELECTRONIC RAIPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','951','dealer','1','979937');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ravi','NULL',NULL,NULL,NULL,NULL,NULL,'4','952','dealer','1','979938');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CITIJEN ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','953','dealer','1','979939');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madhura Infotech','NULL',NULL,NULL,NULL,NULL,NULL,'4','954','dealer','1','979940');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','955','dealer','1','979941');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raju Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','956','dealer','1','979942');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mamta Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','957','dealer','1','979943');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','958','dealer','1','979944');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sidharath elec.','NULL',NULL,NULL,NULL,NULL,NULL,'4','959','dealer','1','979945');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','960','dealer','1','979946');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NANDHINI','NULL',NULL,NULL,NULL,NULL,NULL,'4','961','dealer','1','979947');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shignay Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','962','dealer','1','979948');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('FK-Liquidation','anuj@247around.com',NULL,NULL,NULL,NULL,NULL,'4','247074','partner','1','979949');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','453','engineer','1','979950');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','454','engineer','1','979951');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganesh Guest House','NULL',NULL,NULL,NULL,NULL,NULL,'4','963','dealer','1','979952');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ratan enter prises sujangarh','NULL',NULL,NULL,NULL,NULL,NULL,'4','964','dealer','1','979953');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shriti Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','965','dealer','1','979954');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Electronic Rajaji Nagar','NULL',NULL,NULL,NULL,NULL,NULL,'4','966','dealer','1','979955');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Venketash Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','967','dealer','1','979956');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW ANKUR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','968','dealer','1','979957');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sri vedic krishna','NULL',NULL,NULL,NULL,NULL,NULL,'4','969','dealer','1','979958');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','970','dealer','1','979959');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','971','dealer','1','979960');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jayanti sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','972','dealer','1','979961');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','973','dealer','1','979962');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('India Tv Electronic ( Sirsa) ','NULL',NULL,NULL,NULL,NULL,NULL,'4','974','dealer','1','979963');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Laxmi Market Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','975','dealer','1','979964');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Venkatash Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','976','dealer','1','979965');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri shakti Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','977','dealer','1','979966');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sunrays Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','978','dealer','1','979967');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','979','dealer','1','979968');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','980','dealer','1','979969');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SINGH TRADERS HOOLAGANJ,KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','981','dealer','1','979970');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KK SALES AND FRIZERS,GWALIOR','NULL',NULL,NULL,NULL,NULL,NULL,'4','982','dealer','1','979971');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','983','dealer','1','979972');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LAXMI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','984','dealer','1','979973');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAIPUR HOME APPLINCESS','NULL',NULL,NULL,NULL,NULL,NULL,'4','985','dealer','1','979974');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANIL JI KAITHAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','986','dealer','1','979975');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Baba Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','987','dealer','1','979976');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bombay Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','988','dealer','1','979977');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AKN Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','989','dealer','1','979978');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','455','engineer','1','979979');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHIVA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','990','dealer','1','979980');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AGARWAL ELECT','NULL',NULL,NULL,NULL,NULL,NULL,'4','991','dealer','1','979981');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','992','dealer','1','979982');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJMANDIR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','993','dealer','1','979983');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('R K Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','994','dealer','1','979984');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MANGLAM ELEC GORAKHPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','995','dealer','1','979985');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NATIONAL ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','996','dealer','1','979986');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Saheb Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','997','dealer','1','979987');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sun City Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','998','dealer','1','979988');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','999','dealer','1','979989');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Berry Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1000','dealer','1','979990');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Elumalaiyan Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1001','dealer','1','979991');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAI MAA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1002','dealer','1','979992');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('POOJA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1003','dealer','1','979993');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Baba Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1004','dealer','1','979994');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KRISHNA ELEC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1005','dealer','1','979995');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Comfort','NULL',NULL,NULL,NULL,NULL,NULL,'4','1006','dealer','1','979996');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Telecom','NULL',NULL,NULL,NULL,NULL,NULL,'4','1007','dealer','1','979997');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1008','dealer','1','979998');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Bansidhar Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1009','dealer','1','979999');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mahashakti Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1010','dealer','1','980000');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ritesh Jain','NULL',NULL,NULL,NULL,NULL,NULL,'4','1011','dealer','1','980001');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Vibha Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1012','dealer','1','980002');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HARSH ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1013','dealer','1','980003');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','457','engineer','1','980004');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','458','engineer','1','980005');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Paras creations','NULL',NULL,NULL,NULL,NULL,NULL,'4','1014','dealer','1','980006');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Padmavati electronics and firniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','1015','dealer','1','980007');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHIVAM ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1016','dealer','1','980008');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SRI VASANTHAM AGENCIES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1017','dealer','1','980009');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1018','dealer','1','980010');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','459','engineer','1','980011');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jivan Shivnani','NULL',NULL,NULL,NULL,NULL,NULL,'4','1019','dealer','1','980012');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Shri Ayyepa Mobile Communication','NULL',NULL,NULL,NULL,NULL,NULL,'4','1020','dealer','1','980013');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Delight Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1021','dealer','1','980014');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nilkhant Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1022','dealer','1','980015');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Noble Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1023','dealer','1','980016');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1024','dealer','1','980017');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1025','dealer','1','980018');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tarang Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1026','dealer','1','980019');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMRAT RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','1027','dealer','1','980020');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('malpreet singh ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1028','dealer','1','980021');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ELECTRONIC PARADISE','NULL',NULL,NULL,NULL,NULL,NULL,'4','1029','dealer','1','980022');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GIRIAS INV PVT LTD HUBLI ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1030','dealer','1','980023');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nagiya Treders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1031','dealer','1','980024');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tirupati Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1032','dealer','1','980025');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shubh Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1033','dealer','1','980026');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sana Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1034','dealer','1','980027');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vinayak marketing','NULL',NULL,NULL,NULL,NULL,NULL,'4','1035','dealer','1','980028');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Top ten Electronic           ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1036','dealer','1','980029');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tushar Marketing','NULL',NULL,NULL,NULL,NULL,NULL,'4','1037','dealer','1','980030');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KOHINOOR COMPUTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','1038','dealer','1','980031');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rohit Electronics Dombivali East','NULL',NULL,NULL,NULL,NULL,NULL,'4','1039','dealer','1','980032');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HB ELECTRONICS GHADSANA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1040','dealer','1','980033');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arshi','NULL',NULL,NULL,NULL,NULL,NULL,'4','1041','dealer','1','980034');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ROOP SINGH','NULL',NULL,NULL,NULL,NULL,NULL,'4','1042','dealer','1','980035');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ajay TV House','NULL',NULL,NULL,NULL,NULL,NULL,'4','1043','dealer','1','980036');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Vibha Electronics and Firniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','1044','dealer','1','980037');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GANESHAM DOUSA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1045','dealer','1','980038');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1046','dealer','1','980039');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Yashika Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1047','dealer','1','980040');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANJAY','NULL',NULL,NULL,NULL,NULL,NULL,'4','1048','dealer','1','980041');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BINDAS ELEC.NAINBAZAR','NULL',NULL,NULL,NULL,NULL,NULL,'4','1049','dealer','1','980042');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1050','dealer','1','980043');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maya sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1051','dealer','1','980044');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Naveen Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1052','dealer','1','980045');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BALAJI ELECTRONICS  AHMEDABAD','NULL',NULL,NULL,NULL,NULL,NULL,'4','1053','dealer','1','980046');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Patidar Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1054','dealer','1','980047');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SKELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1055','dealer','1','980048');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VIJAY ELECTRONICS CHITTOR','NULL',NULL,NULL,NULL,NULL,NULL,'4','1056','dealer','1','980049');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vaishnav Enterprises Manewada','NULL',NULL,NULL,NULL,NULL,NULL,'4','1057','dealer','1','980050');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kapoor Electronic & Electronical ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1058','dealer','1','980051');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHASHAKTI TRADERS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1059','dealer','1','980052');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KHANDELWAL ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1060','dealer','1','980053');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI BABA VISUANATH','NULL',NULL,NULL,NULL,NULL,NULL,'4','1061','dealer','1','980054');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AASIF','NULL',NULL,NULL,NULL,NULL,NULL,'4','1062','dealer','1','980055');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1063','dealer','1','980056');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1064','dealer','1','980057');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rachhpal Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1065','dealer','1','980058');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','466','engineer','1','980059');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAI ELECTRONCS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1066','dealer','1','980060');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Agresar steel and firniture Dharampeth nagpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','1067','dealer','1','980061');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prime soluation','NULL',NULL,NULL,NULL,NULL,NULL,'4','1068','dealer','1','980062');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Indian Electronics Akola','NULL',NULL,NULL,NULL,NULL,NULL,'4','1069','dealer','1','980063');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shyam Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1070','dealer','1','980064');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PARCH','NULL',NULL,NULL,NULL,NULL,NULL,'4','1071','dealer','1','980065');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SK ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1072','dealer','1','980066');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KRISHNA ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1073','dealer','1','980067');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHIV SALES CORPORATION','NULL',NULL,NULL,NULL,NULL,NULL,'4','1074','dealer','1','980068');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Sai Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1075','dealer','1','980069');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASHOKA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1076','dealer','1','980070');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AARADHANA ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1077','dealer','1','980071');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1078','dealer','1','980072');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kapoor Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1079','dealer','1','980073');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Baba Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1080','dealer','1','980074');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TULASI ELEC BARHAN AGRA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1081','dealer','1','980075');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sairjel Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1082','dealer','1','980076');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHOPAL ELECT.','NULL',NULL,NULL,NULL,NULL,NULL,'4','1083','dealer','1','980077');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1084','dealer','1','980078');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Agency ( DAVANGERE )','NULL',NULL,NULL,NULL,NULL,NULL,'4','1085','dealer','1','980079');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NAGARWAL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1086','dealer','1','980080');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Talasar Electronics Bazar','NULL',NULL,NULL,NULL,NULL,NULL,'4','1087','dealer','1','980081');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PMP ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1088','dealer','1','980082');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','krishna.kumar1@teammas.in',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','980083');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','ankit.sharma@teammas.in',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','980084');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VIDEO CENTRE','NULL',NULL,NULL,NULL,NULL,NULL,'4','1089','dealer','1','980085');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prime soluation','NULL',NULL,NULL,NULL,NULL,NULL,'4','1090','dealer','1','980086');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raman','NULL',NULL,NULL,NULL,NULL,NULL,'4','1091','dealer','1','980087');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Modern Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1092','dealer','1','980088');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','469','engineer','1','980089');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI BAGBAT AGNCI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1093','dealer','1','980090');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bharat Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1094','dealer','1','980091');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharma TV center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1095','dealer','1','980092');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Balasubramannia','NULL',NULL,NULL,NULL,NULL,NULL,'4','1096','dealer','1','980093');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Sai Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1097','dealer','1','980094');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mahendra','NULL',NULL,NULL,NULL,NULL,NULL,'4','1098','dealer','1','980095');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEVDA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1099','dealer','1','980096');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pinky Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1100','dealer','1','980097');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KRISHNA CASSETTES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1101','dealer','1','980098');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1102','dealer','1','980099');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Dehli Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1103','dealer','1','980100');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LAWATI RADIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','1104','dealer','1','980101');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMBAR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1105','dealer','1','980102');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amit sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1106','dealer','1','980103');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('H K ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1107','dealer','1','980104');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHALAXMI STEEL FURNITURE','NULL',NULL,NULL,NULL,NULL,NULL,'4','1108','dealer','1','980105');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mahaveer Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1109','dealer','1','980106');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KANT TV CENTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','1110','dealer','1','980107');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('M M Bartan','NULL',NULL,NULL,NULL,NULL,NULL,'4','1111','dealer','1','980108');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Featherlite','tjentpr@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247075','partner','1','980109');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new ganpati electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1112','dealer','1','980110');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Madesiya Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1113','dealer','1','980111');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PRAKASH BAHI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1114','dealer','1','980112');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ARCHAN ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1115','dealer','1','980113');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ROHIT BHAI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1116','dealer','1','980114');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gagan Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1117','dealer','1','980115');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI BALAJI ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1118','dealer','1','980116');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KRISNA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1119','dealer','1','980117');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Singh Radio Servive','NULL',NULL,NULL,NULL,NULL,NULL,'4','1120','dealer','1','980118');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gulshan','NULL',NULL,NULL,NULL,NULL,NULL,'4','1121','dealer','1','980119');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arjoo Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1122','dealer','1','980120');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMAR GUPTA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1123','dealer','1','980121');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shri thakur electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1124','dealer','1','980122');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('front line electricals','NULL',NULL,NULL,NULL,NULL,NULL,'4','1125','dealer','1','980123');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','471','engineer','1','980124');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ravi enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1126','dealer','1','980125');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('samrat international','NULL',NULL,NULL,NULL,NULL,NULL,'4','1127','dealer','1','980126');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Max Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1128','dealer','1','980127');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pal Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1129','dealer','1','980128');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('asta electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1130','dealer','1','980129');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ajay','NULL',NULL,NULL,NULL,NULL,NULL,'4','1131','dealer','1','980130');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Graduate Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1132','dealer','1','980131');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shiva Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1133','dealer','1','980132');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pankaj enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1134','dealer','1','980133');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bulendkhand Shop And chemical works ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1135','dealer','1','980134');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VPO','NULL',NULL,NULL,NULL,NULL,NULL,'4','1136','dealer','1','980135');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASH ELECTRONICS MALKAJGIRI ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1137','dealer','1','980136');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mumbai television','NULL',NULL,NULL,NULL,NULL,NULL,'4','1138','dealer','1','980137');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sah Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1139','dealer','1','980138');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Indian Refegeriation','NULL',NULL,NULL,NULL,NULL,NULL,'4','1140','dealer','1','980139');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sunrisers Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1141','dealer','1','980140');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('naresh tranding company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1142','dealer','1','980141');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ARJINDER SINGH','NULL',NULL,NULL,NULL,NULL,NULL,'4','1143','dealer','1','980142');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Saksam Palza','NULL',NULL,NULL,NULL,NULL,NULL,'4','1144','dealer','1','980143');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('surender pal singh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1145','dealer','1','980144');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gurupreet singh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1146','dealer','1','980145');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('price Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1147','dealer','1','980146');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amin Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1148','dealer','1','980147');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Radio center sikar ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1149','dealer','1','980148');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('dheeraj sighal','NULL',NULL,NULL,NULL,NULL,NULL,'4','1150','dealer','1','980149');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gayatri Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1151','dealer','1','980150');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kashi Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1152','dealer','1','980151');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madhur Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1153','dealer','1','980152');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Shinath Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1154','dealer','1','980153');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Somil','NULL',NULL,NULL,NULL,NULL,NULL,'4','1155','dealer','1','980154');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mansi Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1156','dealer','1','980155');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sarka electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1157','dealer','1','980156');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sargam India Electronics Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1158','dealer','1','980157');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1159','dealer','1','980158');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANKET INDIA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1160','dealer','1','980159');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mittal Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1161','dealer','1','980160');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jalaram Bartan Bhandaar','NULL',NULL,NULL,NULL,NULL,NULL,'4','1162','dealer','1','980161');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Graduate electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1163','dealer','1','980162');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DINESH ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1164','dealer','1','980163');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('selix enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1165','dealer','1','980164');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Thirupathi Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1166','dealer','1','980165');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1167','dealer','1','980166');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Siddharth n company','NULL',NULL,NULL,NULL,NULL,NULL,'4','1168','dealer','1','980167');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Thakur Brothers Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1169','dealer','1','980168');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Bombay sales Agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','1170','dealer','1','980169');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DV Enteprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1171','dealer','1','980170');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHAGWATI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1172','dealer','1','980171');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kumar & trders ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1173','dealer','1','980172');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mukesh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1174','dealer','1','980173');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KISOR AUDIO VEDIO','NULL',NULL,NULL,NULL,NULL,NULL,'4','1175','dealer','1','980174');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Investment Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1176','dealer','1','980175');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('om sai electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1177','dealer','1','980176');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dwarka Radio Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1178','dealer','1','980177');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1179','dealer','1','980178');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chadha T V Agencies ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1180','dealer','1','980179');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','akaiservice1@akaiindia.in',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','980180');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','shakilsam1986@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','980181');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','rajinnder2004@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','980182');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','sunilvora.akai@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','980183');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akai','vkverma1234@rediffmail.com',NULL,NULL,NULL,NULL,NULL,'4','247034','partner','1','980184');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Kalai Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1181','dealer','1','980185');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nari Electronic ,g','NULL',NULL,NULL,NULL,NULL,NULL,'4','1182','dealer','1','980186');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1183','dealer','1','980187');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LUCKY AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','1184','dealer','1','980188');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('M M Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1185','dealer','1','980189');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Naresh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1186','dealer','1','980190');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MODI ELECTRONICS ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1187','dealer','1','980191');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Ayush Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1188','dealer','1','980192');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TSA Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1189','dealer','1','980193');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sujeet electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1190','dealer','1','980194');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bhardwaj electronics shahjhanpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','1191','dealer','1','980195');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1192','dealer','1','980196');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Halder Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1193','dealer','1','980197');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JEETU BHAI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1194','dealer','1','980198');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sree Sadhana Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1195','dealer','1','980199');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ ADWAANI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1196','dealer','1','980200');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madho marketting','NULL',NULL,NULL,NULL,NULL,NULL,'4','1197','dealer','1','980201');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manisha Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1198','dealer','1','980202');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('POOJA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1199','dealer','1','980203');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amit sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1200','dealer','1','980204');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHAVEER ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1201','dealer','1','980205');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Mateshwari Electronics and Communication','NULL',NULL,NULL,NULL,NULL,NULL,'4','1202','dealer','1','980206');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shri ganesh electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1203','dealer','1','980207');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AP sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1204','dealer','1','980208');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' monika electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1205','dealer','1','980209');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Avishek Enterprise','NULL',NULL,NULL,NULL,NULL,NULL,'4','1206','dealer','1','980210');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHAGWAN DAS SINGLA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1207','dealer','1','980211');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Suprim Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1208','dealer','1','980212');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Paal And Company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1209','dealer','1','980213');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DEP ELE. DATIA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1210','dealer','1','980214');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Khurana Radio Jagdalpur','NULL',NULL,NULL,NULL,NULL,NULL,'4','1211','dealer','1','980215');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prime soluation','NULL',NULL,NULL,NULL,NULL,NULL,'4','1212','dealer','1','980216');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aves payel','NULL',NULL,NULL,NULL,NULL,NULL,'4','1213','dealer','1','980217');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shama Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1214','dealer','1','980218');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' Dealer Number ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1215','dealer','1','980219');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bharat Electricals','NULL',NULL,NULL,NULL,NULL,NULL,'4','1216','dealer','1','980220');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aakash Traders and Furniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','1217','dealer','1','980221');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jain Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1218','dealer','1','980222');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Punjab Trading coming','NULL',NULL,NULL,NULL,NULL,NULL,'4','1219','dealer','1','980223');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kumaran traders ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1220','dealer','1','980224');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rupa Electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1221','dealer','1','980225');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Satguru Mobile Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1222','dealer','1','980226');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Brahm Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1223','dealer','1','980227');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('M Y Marketing','NULL',NULL,NULL,NULL,NULL,NULL,'4','1224','dealer','1','980228');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1225','dealer','1','980229');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('techno tree','NULL',NULL,NULL,NULL,NULL,NULL,'4','1226','dealer','1','980230');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Just Coll','NULL',NULL,NULL,NULL,NULL,NULL,'4','1227','dealer','1','980231');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dayal Brothers','NULL',NULL,NULL,NULL,NULL,NULL,'4','1228','dealer','1','980232');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' B N K G Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1229','dealer','1','980233');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('deepak electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1230','dealer','1','980234');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kruti Electronics Nimkheda','NULL',NULL,NULL,NULL,NULL,NULL,'4','1231','dealer','1','980235');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raj Radio Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1232','dealer','1','980236');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bala jii Electronic Amritsar ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1233','dealer','1','980237');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rohit','NULL',NULL,NULL,NULL,NULL,NULL,'4','1234','dealer','1','980238');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Star Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1235','dealer','1','980239');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sanjay and sant Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1236','dealer','1','980240');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jhankar Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1237','dealer','1','980241');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maa Sharda Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1238','dealer','1','980242');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1239','dealer','1','980243');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raj electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1240','dealer','1','980244');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ambika Radios','NULL',NULL,NULL,NULL,NULL,NULL,'4','1241','dealer','1','980245');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1242','dealer','1','980246');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ZZ Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1243','dealer','1','980247');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mani appliance','NULL',NULL,NULL,NULL,NULL,NULL,'4','1244','dealer','1','980248');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ashu','NULL',NULL,NULL,NULL,NULL,NULL,'4','1245','dealer','1','980249');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('aswariya  electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1246','dealer','1','980250');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GANESH AIRCON','NULL',NULL,NULL,NULL,NULL,NULL,'4','1247','dealer','1','980251');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pawan Steel furniture and electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1248','dealer','1','980252');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kedarnath panjabi','NULL',NULL,NULL,NULL,NULL,NULL,'4','1249','dealer','1','980253');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shivam electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1250','dealer','1','980254');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chinna raj','NULL',NULL,NULL,NULL,NULL,NULL,'4','1251','dealer','1','980255');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tahir','NULL',NULL,NULL,NULL,NULL,NULL,'4','1252','dealer','1','980256');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('navdeep','NULL',NULL,NULL,NULL,NULL,NULL,'4','1253','dealer','1','980257');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tarni','NULL',NULL,NULL,NULL,NULL,NULL,'4','1254','dealer','1','980258');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('elit electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1255','dealer','1','980259');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('star electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1256','dealer','1','980260');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Barnwaal Eletronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1257','dealer','1','980261');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shriram Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1258','dealer','1','980262');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Yashvi Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1259','dealer','1','980263');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Platinum Gift center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1260','dealer','1','980264');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Baksar Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1261','dealer','1','980265');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAGAR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1262','dealer','1','980266');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mr and Mrs Goyal electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1263','dealer','1','980267');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bharti Beauty House  ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1264','dealer','1','980268');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CHANDRESS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1265','dealer','1','980269');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kohinoor Tele Video','NULL',NULL,NULL,NULL,NULL,NULL,'4','1266','dealer','1','980270');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kamal Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1267','dealer','1','980271');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' shubham electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1268','dealer','1','980272');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manglam','NULL',NULL,NULL,NULL,NULL,NULL,'4','1269','dealer','1','980273');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gupta electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1270','dealer','1','980274');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vinay patthak','NULL',NULL,NULL,NULL,NULL,NULL,'4','1271','dealer','1','980275');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anon enterprice','NULL',NULL,NULL,NULL,NULL,NULL,'4','1272','dealer','1','980276');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Riju Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1273','dealer','1','980277');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('bharat electrical','NULL',NULL,NULL,NULL,NULL,NULL,'4','1274','dealer','1','980278');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gautam','NULL',NULL,NULL,NULL,NULL,NULL,'4','1275','dealer','1','980279');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Neetu','NULL',NULL,NULL,NULL,NULL,NULL,'4','1276','dealer','1','980280');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('arora electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1277','dealer','1','980281');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic JP Nagar ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1278','dealer','1','980282');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Naman Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1279','dealer','1','980283');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maya sales ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1280','dealer','1','980284');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vandhana Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1281','dealer','1','980285');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('karmokar electronic and electronicc','NULL',NULL,NULL,NULL,NULL,NULL,'4','1282','dealer','1','980286');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shree Gupta Marketing ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1283','dealer','1','980287');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jalaram Vastu Bhandaar','NULL',NULL,NULL,NULL,NULL,NULL,'4','1284','dealer','1','980288');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sagar Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1285','dealer','1','980289');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madhav Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1286','dealer','1','980290');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tuli Brothers','NULL',NULL,NULL,NULL,NULL,NULL,'4','1287','dealer','1','980291');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Model watcg Radio Complex','NULL',NULL,NULL,NULL,NULL,NULL,'4','1288','dealer','1','980292');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vandana Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1289','dealer','1','980293');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VASTU REFRIGRATION','NULL',NULL,NULL,NULL,NULL,NULL,'4','1290','dealer','1','980294');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('v mak infotecs','NULL',NULL,NULL,NULL,NULL,NULL,'4','1291','dealer','1','980295');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sara sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1292','dealer','1','980296');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VISHAL VISION','NULL',NULL,NULL,NULL,NULL,NULL,'4','1293','dealer','1','980297');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JAI MATI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1294','dealer','1','980298');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pankaj Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1295','dealer','1','980299');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GROVER ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1296','dealer','1','980300');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Balajii electronic sagar ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1297','dealer','1','980301');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sargam Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1298','dealer','1','980302');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NIkita','NULL',NULL,NULL,NULL,NULL,NULL,'4','1299','dealer','1','980303');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amit Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1300','dealer','1','980304');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Suresh jain','NULL',NULL,NULL,NULL,NULL,NULL,'4','1301','dealer','1','980305');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kuldeep','NULL',NULL,NULL,NULL,NULL,NULL,'4','1302','dealer','1','980306');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kodiya Marketing ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1303','dealer','1','980307');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shama Electronic and Gift center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1304','dealer','1','980308');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shobhna Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1305','dealer','1','980309');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tajir Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1306','dealer','1','980310');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1307','dealer','1','980311');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('not shared','NULL',NULL,NULL,NULL,NULL,NULL,'4','1308','dealer','1','980312');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Lucky Agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','1309','dealer','1','980313');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GANGOTRI ENTRIPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1310','dealer','1','980314');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shree Thakur Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1311','dealer','1','980315');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anushka enterprise','NULL',NULL,NULL,NULL,NULL,NULL,'4','1312','dealer','1','980316');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Radhe Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1313','dealer','1','980317');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Azar','NULL',NULL,NULL,NULL,NULL,NULL,'4','1314','dealer','1','980318');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AMAN ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1315','dealer','1','980319');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dimod Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1316','dealer','1','980320');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amazing World International','NULL',NULL,NULL,NULL,NULL,NULL,'4','1317','dealer','1','980321');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Baba Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1318','dealer','1','980322');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maa Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1319','dealer','1','980323');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rajesh electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1320','dealer','1','980324');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pankaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1321','dealer','1','980325');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ASK Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1322','dealer','1','980326');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('chaya ajency yashodanaga','NULL',NULL,NULL,NULL,NULL,NULL,'4','1323','dealer','1','980327');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kumar TV House','NULL',NULL,NULL,NULL,NULL,NULL,'4','1324','dealer','1','980328');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Sai Ajency mohadi','NULL',NULL,NULL,NULL,NULL,NULL,'4','1325','dealer','1','980329');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gobind Furniture ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1326','dealer','1','980330');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shree Sarvodaya Engineering Co','NULL',NULL,NULL,NULL,NULL,NULL,'4','1327','dealer','1','980331');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('CHAYA AGENCY','NULL',NULL,NULL,NULL,NULL,NULL,'4','1328','dealer','1','980332');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' RK Radio and TV Service','NULL',NULL,NULL,NULL,NULL,NULL,'4','1329','dealer','1','980333');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1330','dealer','1','980334');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Indian Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1331','dealer','1','980335');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manisha Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1332','dealer','1','980336');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1333','dealer','1','980337');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('P R Sales Kalamboli','NULL',NULL,NULL,NULL,NULL,NULL,'4','1334','dealer','1','980338');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Zubair bai','NULL',NULL,NULL,NULL,NULL,NULL,'4','1335','dealer','1','980339');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aliya','NULL',NULL,NULL,NULL,NULL,NULL,'4','1336','dealer','1','980340');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ocean Blue','NULL',NULL,NULL,NULL,NULL,NULL,'4','1337','dealer','1','980341');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Hari Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1338','dealer','1','980342');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Television Palace','NULL',NULL,NULL,NULL,NULL,NULL,'4','1339','dealer','1','980343');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manoj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1340','dealer','1','980344');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('abishek enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1341','dealer','1','980345');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S B electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1342','dealer','1','980346');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VIPUL BHAI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1343','dealer','1','980347');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Priyanka Electronics Indore','NULL',NULL,NULL,NULL,NULL,NULL,'4','1344','dealer','1','980348');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('satya agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','1345','dealer','1','980349');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RS ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1346','dealer','1','980350');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bobby Furniture works ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1347','dealer','1','980351');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('hingu electronices','NULL',NULL,NULL,NULL,NULL,NULL,'4','1348','dealer','1','980352');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('bhavya electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1349','dealer','1','980353');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akash Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1350','dealer','1','980354');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tunku','NULL',NULL,NULL,NULL,NULL,NULL,'4','1351','dealer','1','980355');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Business Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1352','dealer','1','980356');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jeet Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1353','dealer','1','980357');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shrikrishna Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1354','dealer','1','980358');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KULDEEP ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1355','dealer','1','980359');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Asif Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1356','dealer','1','980360');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANUBHAV ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1357','dealer','1','980361');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shubham Inverter and Battery House','NULL',NULL,NULL,NULL,NULL,NULL,'4','1358','dealer','1','980362');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganehs Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1359','dealer','1','980363');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('saini electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1360','dealer','1','980364');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ashoka sales ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1361','dealer','1','980365');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Siraj Ahmad','NULL',NULL,NULL,NULL,NULL,NULL,'4','1362','dealer','1','980366');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('batra brother ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1363','dealer','1','980367');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('neha electricians','NULL',NULL,NULL,NULL,NULL,NULL,'4','1364','dealer','1','980368');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Fairdeel Arcade','NULL',NULL,NULL,NULL,NULL,NULL,'4','1365','dealer','1','980369');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sai Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1366','dealer','1','980370');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Multideal corporation','NULL',NULL,NULL,NULL,NULL,NULL,'4','1367','dealer','1','980371');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pratik electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1368','dealer','1','980372');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Deepak chohan','NULL',NULL,NULL,NULL,NULL,NULL,'4','1369','dealer','1','980373');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jaswant singh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1370','dealer','1','980374');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('O M G  sales ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1371','dealer','1','980375');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shambu electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1372','dealer','1','980376');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KRISHNA ELECTRINICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1373','dealer','1','980377');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bharat Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1374','dealer','1','980378');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shahzam Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1375','dealer','1','980379');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BMKD Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1376','dealer','1','980380');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Suncity Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1377','dealer','1','980381');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maa Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1378','dealer','1','980382');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raj Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1379','dealer','1','980383');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('TS Vision','NULL',NULL,NULL,NULL,NULL,NULL,'4','1380','dealer','1','980384');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Lajpat Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1381','dealer','1','980385');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tuli Brothers','NULL',NULL,NULL,NULL,NULL,NULL,'4','1382','dealer','1','980386');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Babji Vasamsetti','NULL',NULL,NULL,NULL,NULL,NULL,'4','1383','dealer','1','980387');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RISHI ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1384','dealer','1','980388');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj elactronics nellore','NULL',NULL,NULL,NULL,NULL,NULL,'4','1385','dealer','1','980389');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajran Treders Vadoda','NULL',NULL,NULL,NULL,NULL,NULL,'4','1386','dealer','1','980390');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hemkund Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1387','dealer','1','980391');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nadeem','NULL',NULL,NULL,NULL,NULL,NULL,'4','1388','dealer','1','980392');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chadda Tv Agency ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1389','dealer','1','980393');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sanjay Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1390','dealer','1','980394');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rohit electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1391','dealer','1','980395');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rishi Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1392','dealer','1','980396');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bhagat Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1393','dealer','1','980397');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jai Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1394','dealer','1','980398');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gopi sales ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1395','dealer','1','980399');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vishal Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1396','dealer','1','980400');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sai infotech','NULL',NULL,NULL,NULL,NULL,NULL,'4','1397','dealer','1','980401');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Irfan mehdi','NULL',NULL,NULL,NULL,NULL,NULL,'4','1398','dealer','1','980402');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('umesh electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1399','dealer','1','980403');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('raj laxmi elecctronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1400','dealer','1','980404');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mannat electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1401','dealer','1','980405');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' jagarnath Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1402','dealer','1','980406');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Applaince Park','NULL',NULL,NULL,NULL,NULL,NULL,'4','1403','dealer','1','980407');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jai Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1404','dealer','1','980408');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jain electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1405','dealer','1','980409');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manat Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1406','dealer','1','980410');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sriram Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1407','dealer','1','980411');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bhagwan Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1408','dealer','1','980412');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Naresh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1409','dealer','1','980413');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rohit Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1410','dealer','1','980414');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ambika electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1411','dealer','1','980415');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MANASI ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1412','dealer','1','980416');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chadha Radio','NULL',NULL,NULL,NULL,NULL,NULL,'4','1413','dealer','1','980417');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arrora Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1414','dealer','1','980418');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raju Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1415','dealer','1','980419');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('B N K D Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1416','dealer','1','980420');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manoj electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1417','dealer','1','980421');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sri sharda elece','NULL',NULL,NULL,NULL,NULL,NULL,'4','1418','dealer','1','980422');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sujeet','NULL',NULL,NULL,NULL,NULL,NULL,'4','1419','dealer','1','980423');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Santosh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1420','dealer','1','980424');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anon Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1421','dealer','1','980425');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','486','engineer','1','980426');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHALAXMI ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1422','dealer','1','980427');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dev Enterprise','NULL',NULL,NULL,NULL,NULL,NULL,'4','1423','dealer','1','980428');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jain Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1424','dealer','1','980429');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Chitra Mala Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1425','dealer','1','980430');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('royal chawdhary electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1426','dealer','1','980431');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gupta electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1427','dealer','1','980432');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('star electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1428','dealer','1','980433');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('paul electrical barini','NULL',NULL,NULL,NULL,NULL,NULL,'4','1429','dealer','1','980434');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Barhm Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1430','dealer','1','980435');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('balaji electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1431','dealer','1','980436');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pawan steel furniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','1432','dealer','1','980437');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KUMAR ELEC GUJAILI  KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','1433','dealer','1','980438');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1434','dealer','1','980439');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Lakshaya electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1435','dealer','1','980440');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anjali Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1436','dealer','1','980441');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bharti music Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1437','dealer','1','980442');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VAISHNAVI IMPORIUM SHAMSABAD','NULL',NULL,NULL,NULL,NULL,NULL,'4','1438','dealer','1','980443');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Firoz khan','NULL',NULL,NULL,NULL,NULL,NULL,'4','1439','dealer','1','980444');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kabir electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1440','dealer','1','980445');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Battery Spot','NULL',NULL,NULL,NULL,NULL,NULL,'4','1441','dealer','1','980446');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Alliance marketing company','NULL',NULL,NULL,NULL,NULL,NULL,'4','1442','dealer','1','980447');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chadha TV Agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','1443','dealer','1','980448');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hanumant Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1444','dealer','1','980449');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anil kumar','NULL',NULL,NULL,NULL,NULL,NULL,'4','1445','dealer','1','980450');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('skumar electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1446','dealer','1','980451');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pal and Company','NULL',NULL,NULL,NULL,NULL,NULL,'4','1447','dealer','1','980452');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Re on Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1448','dealer','1','980453');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hindustan Home Need','NULL',NULL,NULL,NULL,NULL,NULL,'4','1449','dealer','1','980454');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Omkar Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1450','dealer','1','980455');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AONE FURNITURE','NULL',NULL,NULL,NULL,NULL,NULL,'4','1451','dealer','1','980456');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Krishna Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1452','dealer','1','980457');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jai Mata Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1453','dealer','1','980458');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KAMAD GIRI ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1454','dealer','1','980459');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jain Furtinure House','NULL',NULL,NULL,NULL,NULL,NULL,'4','1455','dealer','1','980460');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pinkal mobile','NULL',NULL,NULL,NULL,NULL,NULL,'4','1456','dealer','1','980461');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('arpit','NULL',NULL,NULL,NULL,NULL,NULL,'4','1457','dealer','1','980462');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gandhi Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1458','dealer','1','980463');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('bhagirath electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1459','dealer','1','980464');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1460','dealer','1','980465');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Poornima Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1461','dealer','1','980466');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New charbhuja sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1462','dealer','1','980467');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('prabhart corporation ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1463','dealer','1','980468');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1464','dealer','1','980469');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Grihakin Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1465','dealer','1','980470');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('bajaj electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1466','dealer','1','980471');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madhurvani Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1467','dealer','1','980472');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('modern electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1468','dealer','1','980473');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('High tone TV Centre','NULL',NULL,NULL,NULL,NULL,NULL,'4','1469','dealer','1','980474');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manoj Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1470','dealer','1','980475');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kushwaha enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1471','dealer','1','980476');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anand tv center & electricals ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1472','dealer','1','980477');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('singu electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1473','dealer','1','980478');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('panwar electrics company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1474','dealer','1','980479');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Singh Electonics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1475','dealer','1','980480');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('akash electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1476','dealer','1','980481');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shivshakti electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1477','dealer','1','980482');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jasola Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1478','dealer','1','980483');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('saini electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1479','dealer','1','980484');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MORDAN ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1480','dealer','1','980485');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HARI OM ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1481','dealer','1','980486');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mahadev Electric','NULL',NULL,NULL,NULL,NULL,NULL,'4','1482','dealer','1','980487');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gift Gallery','NULL',NULL,NULL,NULL,NULL,NULL,'4','1483','dealer','1','980488');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PUNAM SALES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1484','dealer','1','980489');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Music Emporium','NULL',NULL,NULL,NULL,NULL,NULL,'4','1485','dealer','1','980490');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Graduate Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1486','dealer','1','980491');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Lucky Radio','NULL',NULL,NULL,NULL,NULL,NULL,'4','1487','dealer','1','980492');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Super Electronics mallapur','NULL',NULL,NULL,NULL,NULL,NULL,'4','1488','dealer','1','980493');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Radhe Krishna electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1489','dealer','1','980494');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manav Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1490','dealer','1','980495');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('subham enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1491','dealer','1','980496');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic Yelahanka ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1492','dealer','1','980497');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI SAI ELECTRONIC PADRAUNA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1493','dealer','1','980498');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JP Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1494','dealer','1','980501');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shakuntala Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1495','dealer','1','980502');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rakhi Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1496','dealer','1','980503');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('aditiya electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1497','dealer','1','980504');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shriji Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1498','dealer','1','980505');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gurunank radio ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1499','dealer','1','980506');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mayank Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1500','dealer','1','980507');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kripa Home Applainces','NULL',NULL,NULL,NULL,NULL,NULL,'4','1501','dealer','1','980508');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ABLE TECHNOMART','NULL',NULL,NULL,NULL,NULL,NULL,'4','1502','dealer','1','980509');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj electronics Panjagutta','NULL',NULL,NULL,NULL,NULL,NULL,'4','1503','dealer','1','980510');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rukmini Agencies','NULL',NULL,NULL,NULL,NULL,NULL,'4','1504','dealer','1','980511');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Subhash Cycle Store','NULL',NULL,NULL,NULL,NULL,NULL,'4','1505','dealer','1','980512');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('M//S Sharma Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1506','dealer','1','980513');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('madhu radio','NULL',NULL,NULL,NULL,NULL,NULL,'4','1507','dealer','1','980514');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Siliguri Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1508','dealer','1','980515');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shivam Sales Corporation','NULL',NULL,NULL,NULL,NULL,NULL,'4','1509','dealer','1','980516');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dheeraj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1510','dealer','1','980517');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jitendra Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1511','dealer','1','980518');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('k c electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1512','dealer','1','980519');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' sadab electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1513','dealer','1','980520');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sai Tech Computers and Distributors','NULL',NULL,NULL,NULL,NULL,NULL,'4','1514','dealer','1','980521');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amit Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1515','dealer','1','980522');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jitendra electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1516','dealer','1','980523');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PARISH Marketing ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1517','dealer','1','980524');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharma Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1518','dealer','1','980525');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Meena','NULL',NULL,NULL,NULL,NULL,NULL,'4','1519','dealer','1','980526');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pampa Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1520','dealer','1','980527');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Thirumal electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1521','dealer','1','980528');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kothari Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1522','dealer','1','980529');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ayush electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1523','dealer','1','980530');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('P K Sadhukhan& co ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1524','dealer','1','980531');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Matrix Shopee','NULL',NULL,NULL,NULL,NULL,NULL,'4','1525','dealer','1','980532');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DB Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1526','dealer','1','980533');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganesh Rodvej  ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1527','dealer','1','980534');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Agarwaal sales ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1528','dealer','1','980535');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shrikrishna Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1529','dealer','1','980536');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ashwani Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1530','dealer','1','980537');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sri varahi electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1531','dealer','1','980538');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mukesh Rai Patel','NULL',NULL,NULL,NULL,NULL,NULL,'4','1532','dealer','1','980539');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Das Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1533','dealer','1','980540');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' anil electronics panna','NULL',NULL,NULL,NULL,NULL,NULL,'4','1534','dealer','1','980541');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SANGEETA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1535','dealer','1','980542');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('abc','NULL',NULL,NULL,NULL,NULL,NULL,'4','1536','dealer','1','980543');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHOLA NATH BHIND ROAD','NULL',NULL,NULL,NULL,NULL,NULL,'4','1537','dealer','1','980544');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Cahndan Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1538','dealer','1','980545');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHALAXMI SALES ETAWAH','NULL',NULL,NULL,NULL,NULL,NULL,'4','1539','dealer','1','980546');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('abhishek enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1540','dealer','1','980547');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S G Agency ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1541','dealer','1','980548');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('bansal electronics and electricals','NULL',NULL,NULL,NULL,NULL,NULL,'4','1542','dealer','1','980549');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jaiswal electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1543','dealer','1','980550');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bala jii Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1544','dealer','1','980551');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arya Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1545','dealer','1','980552');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sainath cycle and Electronics Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1546','dealer','1','980553');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kishor electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1547','dealer','1','980554');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Brothers Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1548','dealer','1','980555');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Manish Appliance ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1549','dealer','1','980556');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vivek Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1550','dealer','1','980557');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anand technologies ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1551','dealer','1','980558');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('fairy land','NULL',NULL,NULL,NULL,NULL,NULL,'4','1552','dealer','1','980559');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('KAMBOJ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1553','dealer','1','980560');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('The elc','NULL',NULL,NULL,NULL,NULL,NULL,'4','1554','dealer','1','980561');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vivek electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1555','dealer','1','980562');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('bohame','NULL',NULL,NULL,NULL,NULL,NULL,'4','1556','dealer','1','980563');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('D.K Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1557','dealer','1','980564');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HAR TEZ ELC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1558','dealer','1','980565');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('mastana electronice sowroom','NULL',NULL,NULL,NULL,NULL,NULL,'4','1559','dealer','1','980566');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shaina mobile galary ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1560','dealer','1','980567');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sakshi Mobile Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1561','dealer','1','980568');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gyan electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1562','dealer','1','980569');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aashyana Music Center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1563','dealer','1','980570');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rakesh electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1564','dealer','1','980571');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GS TRADER','NULL',NULL,NULL,NULL,NULL,NULL,'4','1565','dealer','1','980572');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nitin Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1566','dealer','1','980573');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Puja Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1567','dealer','1','980574');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Saddi Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1568','dealer','1','980575');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kapoor Brothers Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1569','dealer','1','980576');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GURUNANAK ELECTRONICS ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1570','dealer','1','980577');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SKELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1571','dealer','1','980578');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bablu Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1572','dealer','1','980579');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('chanda electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1573','dealer','1','980580');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Viaksh Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1574','dealer','1','980581');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Zobiab Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1575','dealer','1','980582');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MOULIK SALES CORPORATION','NULL',NULL,NULL,NULL,NULL,NULL,'4','1576','dealer','1','980583');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ARIHANT TRADING','NULL',NULL,NULL,NULL,NULL,NULL,'4','1577','dealer','1','980584');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JR Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1578','dealer','1','980585');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JC Sales and Servcie','NULL',NULL,NULL,NULL,NULL,NULL,'4','1579','dealer','1','980586');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('M S Radio Service ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1580','dealer','1','980587');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gurudev furniture house ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1581','dealer','1','980588');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Chaudhari electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1582','dealer','1','980589');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('satyam Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1583','dealer','1','980590');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jay electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1584','dealer','1','980591');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tylole Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1585','dealer','1','980592');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Trivani Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1586','dealer','1','980593');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('APNA ELECTRIC WORK','NULL',NULL,NULL,NULL,NULL,NULL,'4','1587','dealer','1','980594');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Saraswati Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1588','dealer','1','980595');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amit sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1589','dealer','1','980596');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ANURADHA ELECTRICAL PIPRA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1590','dealer','1','980597');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('dipik electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1591','dealer','1','980598');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1592','dealer','1','980599');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Music House','NULL',NULL,NULL,NULL,NULL,NULL,'4','1593','dealer','1','980600');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Baba Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1594','dealer','1','980601');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mandal Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1595','dealer','1','980602');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VEZHAPARAMBIL LUXURY STORES, CHALAKUDY','NULL',NULL,NULL,NULL,NULL,NULL,'4','1596','dealer','1','980603');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kishor Trading Corporation','NULL',NULL,NULL,NULL,NULL,NULL,'4','1597','dealer','1','980604');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sri kumar t.v agencies','NULL',NULL,NULL,NULL,NULL,NULL,'4','1598','dealer','1','980605');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ashish electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1599','dealer','1','980606');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Daya Shankar Rastogi','NULL',NULL,NULL,NULL,NULL,NULL,'4','1600','dealer','1','980607');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('J swami home appliances','NULL',NULL,NULL,NULL,NULL,NULL,'4','1601','dealer','1','980608');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sargam tel','NULL',NULL,NULL,NULL,NULL,NULL,'4','1602','dealer','1','980609');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ruby Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1603','dealer','1','980610');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madhu Agencies Morena ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1604','dealer','1','980611');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('modern gift center kailaras','NULL',NULL,NULL,NULL,NULL,NULL,'4','1605','dealer','1','980612');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bhawana Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1606','dealer','1','980613');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('skir bhai electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1607','dealer','1','980614');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AK ENTERPRISES AGRA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1608','dealer','1','980615');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S S Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1609','dealer','1','980616');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rehan electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1610','dealer','1','980617');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('girias investement  PVT LTD','NULL',NULL,NULL,NULL,NULL,NULL,'4','1611','dealer','1','980618');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MAHLA ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1612','dealer','1','980619');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VYankatesh Enetrprises  & Services','NULL',NULL,NULL,NULL,NULL,NULL,'4','1613','dealer','1','980620');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sound & vision ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1614','dealer','1','980621');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new sk steel center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1615','dealer','1','980622');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NA','NULL',NULL,NULL,NULL,NULL,NULL,'4','1616','dealer','1','980623');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('High Light Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1617','dealer','1','980624');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mohan Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1618','dealer','1','980625');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1619','dealer','1','980626');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PARAS ELECTRONICS, MAINPURI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1620','dealer','1','980627');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shree ji  electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1621','dealer','1','980628');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Spectrum Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1622','dealer','1','980629');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mansi Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1623','dealer','1','980630');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ms aruna enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1624','dealer','1','980631');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('International Marketing Raisen','NULL',NULL,NULL,NULL,NULL,NULL,'4','1625','dealer','1','980632');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kumar electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1626','dealer','1','980633');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MR RAJU','NULL',NULL,NULL,NULL,NULL,NULL,'4','1627','dealer','1','980634');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jai','NULL',NULL,NULL,NULL,NULL,NULL,'4','1628','dealer','1','980635');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sonal electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1629','dealer','1','980636');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Choudhary electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1630','dealer','1','980637');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('patwari brothers','NULL',NULL,NULL,NULL,NULL,NULL,'4','1631','dealer','1','980638');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rathore Watch Mobile Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1632','dealer','1','980639');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Srinivasa Home appliance  ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1633','dealer','1','980640');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GUPTA TRADERSS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1634','dealer','1','980641');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('AS Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1635','dealer','1','980642');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PUSHTI ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1636','dealer','1','980643');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('siraj ahmed','NULL',NULL,NULL,NULL,NULL,NULL,'4','1637','dealer','1','980644');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('N R Packers ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1638','dealer','1','980645');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Computer Home','NULL',NULL,NULL,NULL,NULL,NULL,'4','1639','dealer','1','980646');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vinod electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1640','dealer','1','980647');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Diwan Krishna Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1641','dealer','1','980648');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ajay kumar','NULL',NULL,NULL,NULL,NULL,NULL,'4','1642','dealer','1','980649');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anil soni electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1643','dealer','1','980650');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kushwaha electronics unnao','NULL',NULL,NULL,NULL,NULL,NULL,'4','1644','dealer','1','980651');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('paal & company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1645','dealer','1','980652');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('trishul electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1646','dealer','1','980653');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sai Marketing ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1647','dealer','1','980654');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amit sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1648','dealer','1','980655');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mamta Elecronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1649','dealer','1','980656');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shukla Electronics,','NULL',NULL,NULL,NULL,NULL,NULL,'4','1650','dealer','1','980657');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shalu electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1651','dealer','1','980658');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharma Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1652','dealer','1','980659');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madhraj airzone','NULL',NULL,NULL,NULL,NULL,NULL,'4','1653','dealer','1','980660');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAKTHY APPLIANCES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1654','dealer','1','980661');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sun digital','NULL',NULL,NULL,NULL,NULL,NULL,'4','1655','dealer','1','980662');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GUPTA FURNITURE','NULL',NULL,NULL,NULL,NULL,NULL,'4','1656','dealer','1','980663');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('M/s m.s Uppal & sons Pundri','NULL',NULL,NULL,NULL,NULL,NULL,'4','1657','dealer','1','980664');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('johnny enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1658','dealer','1','980665');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Star Home Appliance ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1659','dealer','1','980666');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sai Tech Computers','NULL',NULL,NULL,NULL,NULL,NULL,'4','1660','dealer','1','980667');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('J P electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1661','dealer','1','980668');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1662','dealer','1','980669');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new ayush elactronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1663','dealer','1','980670');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('agrsen electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1664','dealer','1','980672');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ganesh Electroics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1665','dealer','1','980673');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Omkar Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1666','dealer','1','980674');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('paras jha','NULL',NULL,NULL,NULL,NULL,NULL,'4','1667','dealer','1','980675');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jain electrics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1668','dealer','1','980676');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Monika Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1669','dealer','1','980677');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anabra mattor ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1670','dealer','1','980678');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rewa electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1671','dealer','1','980679');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Preeti Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1672','dealer','1','980680');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gayatri Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1673','dealer','1','980681');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jain  furniture houes','NULL',NULL,NULL,NULL,NULL,NULL,'4','1674','dealer','1','980682');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Krishma Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1675','dealer','1','980683');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1677','dealer','1','980684');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ma kali electronices','NULL',NULL,NULL,NULL,NULL,NULL,'4','1678','dealer','1','980685');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hare Krishna Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1679','dealer','1','980686');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rahul electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1680','dealer','1','980687');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shiv sakti electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1681','dealer','1','980688');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sanjay Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1682','dealer','1','980689');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Digital point ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1683','dealer','1','980690');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Electronics House','NULL',NULL,NULL,NULL,NULL,NULL,'4','1684','dealer','1','980691');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Deepak','NULL',NULL,NULL,NULL,NULL,NULL,'4','1685','dealer','1','980692');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('yaduvanshu marketing ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1686','dealer','1','980693');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Doon Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1687','dealer','1','980694');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('charan singh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1688','dealer','1','980695');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kiran Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1689','dealer','1','980696');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sethi Furnitures','NULL',NULL,NULL,NULL,NULL,NULL,'4','1690','dealer','1','980697');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shiv Shakti Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1691','dealer','1','980698');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nadeem Kham','NULL',NULL,NULL,NULL,NULL,NULL,'4','1692','dealer','1','980699');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Middha Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1693','dealer','1','980700');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Maa Jagadhatri Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1694','dealer','1','980701');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kotriya Marketing Raipur','NULL',NULL,NULL,NULL,NULL,NULL,'4','1695','dealer','1','980702');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ram kishor electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1696','dealer','1','980703');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aditya Vision','NULL',NULL,NULL,NULL,NULL,NULL,'4','1697','dealer','1','980704');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Suvidha Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1698','dealer','1','980705');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('misra electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1699','dealer','1','980706');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new maa vaishno electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1700','dealer','1','980707');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('raghuvanshi electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1701','dealer','1','980708');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gambhir electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1702','dealer','1','980709');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NAGAR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1703','dealer','1','980710');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SRINIVASHA HOME APPLIANCES ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1704','dealer','1','980711');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Saurabha Gupta','NULL',NULL,NULL,NULL,NULL,NULL,'4','1705','dealer','1','980712');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('karuna ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1706','dealer','1','980713');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shiva look galaxy','NULL',NULL,NULL,NULL,NULL,NULL,'4','1707','dealer','1','980714');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new sahu electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1708','dealer','1','980715');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ROY ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1709','dealer','1','980716');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('OM ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1710','dealer','1','980717');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ ELECTRONICS&SERVICE','NULL',NULL,NULL,NULL,NULL,NULL,'4','1711','dealer','1','980718');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VIBGYORNXT','h.luthra@vibgyorelectronics.com',NULL,NULL,NULL,NULL,NULL,'4','247076','partner','1','980719');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sai Shree Home Need','NULL',NULL,NULL,NULL,NULL,NULL,'4','1712','dealer','1','980720');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Krishna store Khurd ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1713','dealer','1','980721');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shyam watch Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1714','dealer','1','980722');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gautam Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1715','dealer','1','980723');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sri nath Agency ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1716','dealer','1','980724');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Factory Graded','NULL',NULL,NULL,NULL,NULL,NULL,'4','1717','dealer','1','980725');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('umesh watch company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1719','dealer','1','980726');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Laxmi Durga Homeneeds ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1720','dealer','1','980727');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shankar electriconic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1721','dealer','1','980728');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('na','NULL',NULL,NULL,NULL,NULL,NULL,'4','1722','dealer','1','980729');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Narayana Swamy','NULL',NULL,NULL,NULL,NULL,NULL,'4','1723','dealer','1','980730');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('r k enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1724','dealer','1','980731');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PUNJAB ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1725','dealer','1','980732');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Worldtech','magicindia111@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247078','partner','1','980733');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('P K Sharma electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1726','dealer','1','980734');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('abhisekh enterpries','NULL',NULL,NULL,NULL,NULL,NULL,'4','1727','dealer','1','980735');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gidriani Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1728','dealer','1','980736');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','500','engineer','1','980737');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('A S P I TV CENTER','NULL',NULL,NULL,NULL,NULL,NULL,'4','1729','dealer','1','980738');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anup kumar gupta','NULL',NULL,NULL,NULL,NULL,NULL,'4','1730','dealer','1','980739');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Megha Television','NULL',NULL,NULL,NULL,NULL,NULL,'4','1731','dealer','1','980740');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Government ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1732','dealer','1','980741');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Crystal Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1733','dealer','1','980742');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shree warhi electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1734','dealer','1','980743');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S K Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1735','dealer','1','980744');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shinde Marketing','NULL',NULL,NULL,NULL,NULL,NULL,'4','1736','dealer','1','980745');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kamla gramophone ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1737','dealer','1','980746');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Laxmi Agencies','NULL',NULL,NULL,NULL,NULL,NULL,'4','1738','dealer','1','980747');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('bk enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1739','dealer','1','980748');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Parchani  Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1740','dealer','1','980749');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sumit electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1741','dealer','1','980750');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ram chandra gupta','NULL',NULL,NULL,NULL,NULL,NULL,'4','1742','dealer','1','980751');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('A1 electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1743','dealer','1','980752');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pankaj chitkara','NULL',NULL,NULL,NULL,NULL,NULL,'4','1744','dealer','1','980753');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('goyal furniture house','NULL',NULL,NULL,NULL,NULL,NULL,'4','1745','dealer','1','980754');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pawan','NULL',NULL,NULL,NULL,NULL,NULL,'4','1746','dealer','1','980755');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gagan elactronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1747','dealer','1','980756');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajrang electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1748','dealer','1','980757');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pavan disty booter','NULL',NULL,NULL,NULL,NULL,NULL,'4','1749','dealer','1','980758');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('m s jhandu bihatti','NULL',NULL,NULL,NULL,NULL,NULL,'4','1750','dealer','1','980759');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('S k Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1751','dealer','1','980760');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sheetal enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1752','dealer','1','980761');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JANGIR ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1753','dealer','1','980762');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arora Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1754','dealer','1','980763');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rajat electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1755','dealer','1','980764');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1756','dealer','1','980765');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kwality electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1757','dealer','1','980766');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('amit dithaniya store ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1758','dealer','1','980767');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('maa durga electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1759','dealer','1','980768');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('raj morya','NULL',NULL,NULL,NULL,NULL,NULL,'4','1760','dealer','1','980769');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pal radioh 53 new markte ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1761','dealer','1','980770');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vision computers','NULL',NULL,NULL,NULL,NULL,NULL,'4','1762','dealer','1','980771');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bablu Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1763','dealer','1','980772');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Deepak','NULL',NULL,NULL,NULL,NULL,NULL,'4','1764','dealer','1','980773');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('nakoda sales ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1765','dealer','1','980774');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('priya electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1766','dealer','1','980775');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prince Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1767','dealer','1','980776');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SS Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1768','dealer','1','980777');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Graduate Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1769','dealer','1','980778');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ankit Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1770','dealer','1','980779');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gaytri Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1771','dealer','1','980780');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAndeep Vajva','NULL',NULL,NULL,NULL,NULL,NULL,'4','1772','dealer','1','980781');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rahul Jain','NULL',NULL,NULL,NULL,NULL,NULL,'4','1773','dealer','1','980782');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Raghavendra T V center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1774','dealer','1','980783');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ankur sharma','NULL',NULL,NULL,NULL,NULL,NULL,'4','1775','dealer','1','980784');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shrirma Multi Electro World Pvt ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1776','dealer','1','980785');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SN Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1777','dealer','1','980786');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rupam Furniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','1778','dealer','1','980787');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('nitin patel','NULL',NULL,NULL,NULL,NULL,NULL,'4','1779','dealer','1','980788');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Kumar TV Agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','1780','dealer','1','980789');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Guaranteed Marketing','NULL',NULL,NULL,NULL,NULL,NULL,'4','1781','dealer','1','980790');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vishaal electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1782','dealer','1','980791');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('raj electro plaza ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1783','dealer','1','980792');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bhagwati Communication','NULL',NULL,NULL,NULL,NULL,NULL,'4','1784','dealer','1','980793');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Ganesh Trading Co','NULL',NULL,NULL,NULL,NULL,NULL,'4','1785','dealer','1','980794');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gagneja electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1786','dealer','1','980795');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sri kalai electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1787','dealer','1','980796');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('mp etectro ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1788','dealer','1','980797');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mehul Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1789','dealer','1','980798');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bunty','NULL',NULL,NULL,NULL,NULL,NULL,'4','1790','dealer','1','980799');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Poonam Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1791','dealer','1','980800');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Capital Enterprises ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1792','dealer','1','980801');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('hazipur','NULL',NULL,NULL,NULL,NULL,NULL,'4','1793','dealer','1','980802');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('mayur','NULL',NULL,NULL,NULL,NULL,NULL,'4','1794','dealer','1','980803');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sony Music Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1795','dealer','1','980804');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Happy Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1796','dealer','1','980805');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('padam electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1797','dealer','1','980806');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('janta electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1798','dealer','1','980807');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Namrdada Traders ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1799','dealer','1','980808');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kamla Furniture Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1800','dealer','1','980809');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Om Eectronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1801','dealer','1','980810');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shiv Shakti Electronics and Furniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','1802','dealer','1','980811');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ekai electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1803','dealer','1','980812');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('The Kuthera Co-oprative Agriculture Society ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1804','dealer','1','980813');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gouri Mata Electric','NULL',NULL,NULL,NULL,NULL,NULL,'4','1805','dealer','1','980814');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Surodhoni','NULL',NULL,NULL,NULL,NULL,NULL,'4','1806','dealer','1','980815');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ma Tara Multimedia','NULL',NULL,NULL,NULL,NULL,NULL,'4','1807','dealer','1','980816');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bappa Furniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','1808','dealer','1','980817');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Matrix Shopee','NULL',NULL,NULL,NULL,NULL,NULL,'4','1809','dealer','1','980818');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('singla enterprise','NULL',NULL,NULL,NULL,NULL,NULL,'4','1810','dealer','1','980819');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('saqlaini electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1811','dealer','1','980820');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJAT ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1812','dealer','1','980821');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rajan Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1813','dealer','1','980822');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('juicl electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1814','dealer','1','980823');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('goyal home apilicetion','NULL',NULL,NULL,NULL,NULL,NULL,'4','1815','dealer','1','980824');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rakesh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1816','dealer','1','980825');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('T S Service ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1817','dealer','1','980826');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Raj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1819','dealer','1','980827');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('chanda  electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1820','dealer','1','980828');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anmol dish care ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1821','dealer','1','980829');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sathya electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1822','dealer','1','980830');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Plus Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1823','dealer','1','980831');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Sahara Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1824','dealer','1','980832');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('atul radio','NULL',NULL,NULL,NULL,NULL,NULL,'4','1825','dealer','1','980833');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pawer house battry ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1826','dealer','1','980834');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Patel Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1827','dealer','1','980835');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sonu electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1828','dealer','1','980836');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('axyz','NULL',NULL,NULL,NULL,NULL,NULL,'4','1829','dealer','1','980837');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('tulshi redio house','NULL',NULL,NULL,NULL,NULL,NULL,'4','1830','dealer','1','980838');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Banwari harjai','NULL',NULL,NULL,NULL,NULL,NULL,'4','1831','dealer','1','980839');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gurpreet singh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1832','dealer','1','980840');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Kalpana','NULL',NULL,NULL,NULL,NULL,NULL,'4','1833','dealer','1','980841');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anand radio company','NULL',NULL,NULL,NULL,NULL,NULL,'4','1834','dealer','1','980842');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gajanand enterprise','NULL',NULL,NULL,NULL,NULL,NULL,'4','1835','dealer','1','980843');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Babu LAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','1836','dealer','1','980844');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Varsha Treders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1837','dealer','1','980845');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Naresh Trading Company','NULL',NULL,NULL,NULL,NULL,NULL,'4','1838','dealer','1','980846');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vimal','NULL',NULL,NULL,NULL,NULL,NULL,'4','1839','dealer','1','980847');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sonu electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1840','dealer','1','980848');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Srinivasa home Appalience','NULL',NULL,NULL,NULL,NULL,NULL,'4','1841','dealer','1','980849');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','502','engineer','1','980850');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('nand kishore rathi','NULL',NULL,NULL,NULL,NULL,NULL,'4','1842','dealer','1','980851');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('mayur patel','NULL',NULL,NULL,NULL,NULL,NULL,'4','1843','dealer','1','980852');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BAJAJ electronics Lakdikapool..','NULL',NULL,NULL,NULL,NULL,NULL,'4','1844','dealer','1','980853');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Satyam TV Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1845','dealer','1','980854');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('amrit redio & electric store','NULL',NULL,NULL,NULL,NULL,NULL,'4','1846','dealer','1','980855');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vishal Infotech','NULL',NULL,NULL,NULL,NULL,NULL,'4','1847','dealer','1','980856');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Triveni Electronics Home Needs ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1848','dealer','1','980857');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('girias investement  PVT LTD','NULL',NULL,NULL,NULL,NULL,NULL,'4','1849','dealer','1','980858');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('UNIVERSALL ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1850','dealer','1','980859');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('wadhwa electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1851','dealer','1','980860');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Star home appliances','NULL',NULL,NULL,NULL,NULL,NULL,'4','1852','dealer','1','980861');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GYATRI ENTERPRIZE','NULL',NULL,NULL,NULL,NULL,NULL,'4','1853','dealer','1','980862');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('lehal tv center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1854','dealer','1','980863');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('lucky sharma','NULL',NULL,NULL,NULL,NULL,NULL,'4','1855','dealer','1','980864');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('NEW VIJAY ELECTRONICS RAEBARELI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1856','dealer','1','980865');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jai shree bala ji electronis','NULL',NULL,NULL,NULL,NULL,NULL,'4','1857','dealer','1','980866');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('anjay electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1858','dealer','1','980867');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias Investement Pvt Ltd','NULL',NULL,NULL,NULL,NULL,NULL,'4','1859','dealer','1','980868');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rajput TV Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1860','dealer','1','980869');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Sharma T V  Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1861','dealer','1','980870');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prasanta Kr. Chanak','prasanta.kc@salora.com',NULL,NULL,NULL,NULL,NULL,'4','247064','partner','1','980871');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arvind Gawande','ced-service-nagpur@salora.com',NULL,NULL,NULL,NULL,NULL,'4','247064','partner','1','980872');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pankaj Singh','oad-service-patna@salora.com',NULL,NULL,NULL,NULL,NULL,'4','247064','partner','1','980873');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pinor radio ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1862','dealer','1','980874');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SAJID','NULL',NULL,NULL,NULL,NULL,NULL,'4','1863','dealer','1','980875');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1864','dealer','1','980876');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SADAB ELECTRONIC KANPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','1865','dealer','1','980877');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ravi electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1866','dealer','1','980878');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('baba electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1867','dealer','1','980879');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Video Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1868','dealer','1','980880');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('jai amber electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1869','dealer','1','980881');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Suraj Traders ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1870','dealer','1','980882');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arihant Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1871','dealer','1','980883');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('meghna','NULL',NULL,NULL,NULL,NULL,NULL,'4','1872','dealer','1','980884');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHIV ELECTRONICS FATEHPUR','NULL',NULL,NULL,NULL,NULL,NULL,'4','1873','dealer','1','980885');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SIDHANT ELECTRONIC','NULL',NULL,NULL,NULL,NULL,NULL,'4','1874','dealer','1','980886');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mohd Ubaid','NULL',NULL,NULL,NULL,NULL,NULL,'4','1875','dealer','1','980887');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sammer','NULL',NULL,NULL,NULL,NULL,NULL,'4','1876','dealer','1','980888');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shani Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1877','dealer','1','980889');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Girias INvestment pvt lts','NULL',NULL,NULL,NULL,NULL,NULL,'4','1878','dealer','1','980890');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Amazon','NULL',NULL,NULL,NULL,NULL,NULL,'4','1879','dealer','1','980891');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mahajan Agency ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1880','dealer','1','980892');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri vankatayswara home needs ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1881','dealer','1','980893');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shri  elc','NULL',NULL,NULL,NULL,NULL,NULL,'4','1882','dealer','1','980894');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pradeep Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1883','dealer','1','980895');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Khusi Enterprises & Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1884','dealer','1','980896');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('saksham plaza','NULL',NULL,NULL,NULL,NULL,NULL,'4','1885','dealer','1','980897');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Syvo Sysetm','NULL',NULL,NULL,NULL,NULL,NULL,'4','1886','dealer','1','980898');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('esquire electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1887','dealer','1','980899');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shrya Variety Store','NULL',NULL,NULL,NULL,NULL,NULL,'4','1888','dealer','1','980900');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shagun Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1889','dealer','1','980901');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kanchan agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','1890','dealer','1','980902');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new hgal traderh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1891','dealer','1','980903');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sajid ali','NULL',NULL,NULL,NULL,NULL,NULL,'4','1892','dealer','1','980904');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Neha electricals','NULL',NULL,NULL,NULL,NULL,NULL,'4','1893','dealer','1','980905');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Yogeshwar Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1894','dealer','1','980906');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('l g trading','NULL',NULL,NULL,NULL,NULL,NULL,'4','1895','dealer','1','980907');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Guddiya nigam','NULL',NULL,NULL,NULL,NULL,NULL,'4','1896','dealer','1','980909');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Activa','visionindia1990@gmail.com',NULL,NULL,NULL,NULL,NULL,'4','247079','partner','1','980910');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vijay','NULL',NULL,NULL,NULL,NULL,NULL,'4','1897','dealer','1','980911');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Dengal Furniture','NULL',NULL,NULL,NULL,NULL,NULL,'4','1898','dealer','1','980912');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('mili electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1899','dealer','1','980913');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Hareesh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1900','dealer','1','980914');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ranga electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1901','dealer','1','980915');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kumar Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1902','dealer','1','980916');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rudiyar Electornics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1903','dealer','1','980917');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('ma Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1904','dealer','1','980918');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Avasthi Radio & Watch Co.','NULL',NULL,NULL,NULL,NULL,NULL,'4','1905','dealer','1','980919');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bharat electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1906','dealer','1','980920');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rajsthan trading center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1907','dealer','1','980921');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new Abasti radio','NULL',NULL,NULL,NULL,NULL,NULL,'4','1908','dealer','1','980922');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('narayni electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1909','dealer','1','980923');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Adhikary Service Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1910','dealer','1','980924');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('lucky Agency','NULL',NULL,NULL,NULL,NULL,NULL,'4','1911','dealer','1','980925');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronics karmanghat','NULL',NULL,NULL,NULL,NULL,NULL,'4','1912','dealer','1','980926');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aline Vision Gallery','NULL',NULL,NULL,NULL,NULL,NULL,'4','1913','dealer','1','980927');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vkm hyppr arekode','NULL',NULL,NULL,NULL,NULL,NULL,'4','1914','dealer','1','980928');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pawan Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1915','dealer','1','980929');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Priyal Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1916','dealer','1','980930');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('R K tale Shop','NULL',NULL,NULL,NULL,NULL,NULL,'4','1917','dealer','1','980931');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('UTSAV','NULL',NULL,NULL,NULL,NULL,NULL,'4','1918','dealer','1','980932');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('chittur electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1919','dealer','1','980933');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('LEMON','ashok.bhardwaj@lemonmobiles.com',NULL,NULL,NULL,NULL,NULL,'4','247080','partner','1','980934');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vedio center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1920','dealer','1','980935');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('nutan interprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1921','dealer','1','980936');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rohit Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1922','dealer','1','980937');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BELL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1923','dealer','1','980938');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Ram Machinery Mart','NULL',NULL,NULL,NULL,NULL,NULL,'4','1924','dealer','1','980939');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Jagdish Brothers','NULL',NULL,NULL,NULL,NULL,NULL,'4','1925','dealer','1','980940');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('PAYTM','NULL',NULL,NULL,NULL,NULL,NULL,'4','1926','dealer','1','980941');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SPENCER','NULL',NULL,NULL,NULL,NULL,NULL,'4','1927','dealer','1','980942');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SNAPDEAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','1928','dealer','1','980943');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shubham Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1929','dealer','1','980944');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VIKASH ELE BARDOLI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1930','dealer','1','980945');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SATGURU ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1931','dealer','1','980946');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vishal electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1932','dealer','1','980947');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pawan Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1933','dealer','1','980948');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Grage Radio Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1934','dealer','1','980949');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sri Sidhivinayak Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1935','dealer','1','980950');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri Meladi Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1936','dealer','1','980951');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('no name ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1937','dealer','1','980952');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('amit electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1938','dealer','1','980953');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sehgal Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1939','dealer','1','980954');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('electronic India ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1940','dealer','1','980955');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rajani Radio and Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1941','dealer','1','980956');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rudra Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1942','dealer','1','980957');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pawan Jain','NULL',NULL,NULL,NULL,NULL,NULL,'4','1943','dealer','1','980958');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rohit galaxy ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1944','dealer','1','980959');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1945','dealer','1','980960');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BHARTBHAI','NULL',NULL,NULL,NULL,NULL,NULL,'4','1946','dealer','1','980961');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ace Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1947','dealer','1','980962');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('reinu electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1948','dealer','1','980963');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('omar gift house ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1949','dealer','1','980964');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Auto World','NULL',NULL,NULL,NULL,NULL,NULL,'4','1950','dealer','1','980965');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anand Technologies ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1951','dealer','1','980966');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shiv Shakti Traders Mandi ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1952','dealer','1','980967');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tech Media Retail','NULL',NULL,NULL,NULL,NULL,NULL,'4','1953','dealer','1','980968');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Sehgal Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','1954','dealer','1','980969');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajrang Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1955','dealer','1','980970');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ahuja Music Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','1956','dealer','1','980971');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Aditi Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1957','dealer','1','980972');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('DINESH ELECTRONICS LALSOT','NULL',NULL,NULL,NULL,NULL,NULL,'4','1958','dealer','1','980973');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Para mohan','NULL',NULL,NULL,NULL,NULL,NULL,'4','1959','dealer','1','980974');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('lalaan General store','NULL',NULL,NULL,NULL,NULL,NULL,'4','1960','dealer','1','980975');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Parchani sales ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1961','dealer','1','980976');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('goel electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1962','dealer','1','980977');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Waves Computer Electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1963','dealer','1','980978');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('M/S Sigma Electronic  ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1964','dealer','1','980979');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pooja sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','1965','dealer','1','980980');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sanjay electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1966','dealer','1','980981');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' Prashant refrigerator','NULL',NULL,NULL,NULL,NULL,NULL,'4','1967','dealer','1','980982');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kushwaha electronic &* furniture ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1968','dealer','1','980983');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('paddy radio and electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1969','dealer','1','980984');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('saudarn radio','NULL',NULL,NULL,NULL,NULL,NULL,'4','1970','dealer','1','980985');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sahni Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1971','dealer','1','980986');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('santi  electrinics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1972','dealer','1','980987');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kiran electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1973','dealer','1','980988');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('nunees electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1974','dealer','1','980989');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sahil','NULL',NULL,NULL,NULL,NULL,NULL,'4','1975','dealer','1','980990');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mr ravi singh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1976','dealer','1','980991');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','511','engineer','1','980992');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','512','engineer','1','980993');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('VMAK iNFOTEH ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1977','dealer','1','980994');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('C L Mehra and sons','NULL',NULL,NULL,NULL,NULL,NULL,'4','1978','dealer','1','980995');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRI SAI ELECTORNICS INDORE','NULL',NULL,NULL,NULL,NULL,NULL,'4','1979','dealer','1','980996');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('tez radio','NULL',NULL,NULL,NULL,NULL,NULL,'4','1980','dealer','1','980997');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('SHRIG ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1981','dealer','1','980998');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JANATA ENTERPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','1982','dealer','1','980999');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shri ji electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1983','dealer','1','981000');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JINDAL ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','1984','dealer','1','981001');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kumar and tradeh','NULL',NULL,NULL,NULL,NULL,NULL,'4','1985','dealer','1','981002');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shivam electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','1986','dealer','1','981003');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raj Electronic Raipur','NULL',NULL,NULL,NULL,NULL,NULL,'4','1987','dealer','1','981004');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('lovely','NULL',NULL,NULL,NULL,NULL,NULL,'4','1988','dealer','1','981005');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Grand Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1989','dealer','1','981006');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('heaven enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','1990','dealer','1','981008');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Raj Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1991','dealer','1','981009');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('HEERA ELE VERAVAL','NULL',NULL,NULL,NULL,NULL,NULL,'4','1992','dealer','1','981010');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jugnu Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1993','dealer','1','981011');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bharat Furnitures','NULL',NULL,NULL,NULL,NULL,NULL,'4','1994','dealer','1','981012');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('MohammedSadiq','NULL',NULL,NULL,NULL,NULL,NULL,'4','1995','dealer','1','981013');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Super Best Company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','1996','dealer','1','981014');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vishwas enterprices','NULL',NULL,NULL,NULL,NULL,NULL,'4','1997','dealer','1','981015');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kumhar Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','1998','dealer','1','981016');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('raj mandir electro plza','NULL',NULL,NULL,NULL,NULL,NULL,'4','1999','dealer','1','981017');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nava Nidhi Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2000','dealer','1','981018');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('mehta electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2001','dealer','1','981019');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Rishab Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2002','dealer','1','981020');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('relices electonics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2003','dealer','1','981021');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Suprime Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2004','dealer','1','981022');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('tanu electricals','NULL',NULL,NULL,NULL,NULL,NULL,'4','2005','dealer','1','981023');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vishesh','NULL',NULL,NULL,NULL,NULL,NULL,'4','2006','dealer','1','981024');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ramesh','NULL',NULL,NULL,NULL,NULL,NULL,'4','2007','dealer','1','981025');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ujjal Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2008','dealer','1','981026');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Ajay Watch And Radio T V Company ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2009','dealer','1','981027');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shri siddhi vinayak Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2010','dealer','1','981028');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GHOSH ENTRPRISES','NULL',NULL,NULL,NULL,NULL,NULL,'4','2011','dealer','1','981029');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('metro traders ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2012','dealer','1','981030');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shiv sakti eletronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2013','dealer','1','981031');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shanti TV Center','NULL',NULL,NULL,NULL,NULL,NULL,'4','2014','dealer','1','981032');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gandhi Sales','NULL',NULL,NULL,NULL,NULL,NULL,'4','2015','dealer','1','981033');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('singhai staioners','NULL',NULL,NULL,NULL,NULL,NULL,'4','2016','dealer','1','981034');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prakash','NULL',NULL,NULL,NULL,NULL,NULL,'4','2017','dealer','1','981035');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nutun Enterprise ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2018','dealer','1','981036');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Giriyas Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2019','dealer','1','981037');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('thakkakuth agency allanalur','NULL',NULL,NULL,NULL,NULL,NULL,'4','2020','dealer','1','981038');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','2021','dealer','1','981039');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('seema electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2022','dealer','1','981040');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('BANGAD CELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','2023','dealer','1','981041');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('GURUDEV ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','2024','dealer','1','981042');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Prakash Traders','NULL',NULL,NULL,NULL,NULL,NULL,'4','2025','dealer','1','981043');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('gurunaid trraders ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2026','dealer','1','981044');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bombay electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','2027','dealer','1','981045');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('giriyas','NULL',NULL,NULL,NULL,NULL,NULL,'4','2028','dealer','1','981046');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('basan  co ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2029','dealer','1','981047');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kishor Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2030','dealer','1','981048');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Akash Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2031','dealer','1','981049');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic Vijaywada ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2032','dealer','1','981050');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kirtesh telecom ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2033','dealer','1','981051');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('aggarwal furniture ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2034','dealer','1','981052');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES (' sankat mochan department store','NULL',NULL,NULL,NULL,NULL,NULL,'4','2035','dealer','1','981053');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Tanvi Kohli Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2036','dealer','1','981054');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RAJ ELECTORNCS','NULL',NULL,NULL,NULL,NULL,NULL,'4','2037','dealer','1','981055');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('p.k enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','2038','dealer','1','981056');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Annapurna Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2039','dealer','1','981057');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jenral Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2040','dealer','1','981058');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Jinius Computer','NULL',NULL,NULL,NULL,NULL,NULL,'4','2041','dealer','1','981059');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('rohit electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2042','dealer','1','981060');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('national tv centre paloda','NULL',NULL,NULL,NULL,NULL,NULL,'4','2043','dealer','1','981061');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('the soni moni  electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2044','dealer','1','981062');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('mayukha home needh','NULL',NULL,NULL,NULL,NULL,NULL,'4','2045','dealer','1','981063');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sun Infosys','NULL',NULL,NULL,NULL,NULL,NULL,'4','2046','dealer','1','981064');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mukesh Music and electricals','NULL',NULL,NULL,NULL,NULL,NULL,'4','2047','dealer','1','981065');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('malina electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2048','dealer','1','981066');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bharti Beauty House','NULL',NULL,NULL,NULL,NULL,NULL,'4','2049','dealer','1','981067');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Nirbhay','NULL',NULL,NULL,NULL,NULL,NULL,'4','2050','dealer','1','981068');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Sharma Sound Service','NULL',NULL,NULL,NULL,NULL,NULL,'4','2051','dealer','1','981069');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Harish Negi','NULL',NULL,NULL,NULL,NULL,NULL,'4','2052','dealer','1','981070');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('zaygan electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2053','dealer','1','981071');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gift Gallary','NULL',NULL,NULL,NULL,NULL,NULL,'4','2054','dealer','1','981072');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Mayur Computer','NULL',NULL,NULL,NULL,NULL,NULL,'4','2055','dealer','1','981073');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Anant Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2056','dealer','1','981074');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Gourav Movi Masti ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2057','dealer','1','981075');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('pooja electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2058','dealer','1','981076');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','525','engineer','1','981077');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','526','engineer','1','981078');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','527','engineer','1','981079');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shweta','NULL',NULL,NULL,NULL,NULL,NULL,'4','2059','dealer','1','981080');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','528','engineer','1','981081');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kartik enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','2060','dealer','1','981082');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kandukuri Enterprises','NULL',NULL,NULL,NULL,NULL,NULL,'4','2061','dealer','1','981083');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('tenjs markiting ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2062','dealer','1','981084');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','529','engineer','1','981085');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Kuldeep Electro care','NULL',NULL,NULL,NULL,NULL,NULL,'4','2063','dealer','1','981086');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Samay Vatika Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2064','dealer','1','981087');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Pk Distibuters','NULL',NULL,NULL,NULL,NULL,NULL,'4','2065','dealer','1','981088');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('shree acrostic electonics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2066','dealer','1','981089');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sushil electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','2067','dealer','1','981090');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('sankit india','NULL',NULL,NULL,NULL,NULL,NULL,'4','2068','dealer','1','981091');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','530','engineer','1','981092');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','531','engineer','1','981093');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('omar electricals ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2069','dealer','1','981094');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New Cable House ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2070','dealer','1','981095');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('vinood electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2071','dealer','1','981096');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Madhulika Enterorises','NULL',NULL,NULL,NULL,NULL,NULL,'4','2072','dealer','1','981097');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('RADHE ELECTRONICS','NULL',NULL,NULL,NULL,NULL,NULL,'4','2073','dealer','1','981098');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('new national electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2074','dealer','1','981099');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('saahara electronic','NULL',NULL,NULL,NULL,NULL,NULL,'4','2075','dealer','1','981100');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('JS furniture and Electronics','NULL',NULL,NULL,NULL,NULL,NULL,'4','2076','dealer','1','981101');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Shivam Steel','NULL',NULL,NULL,NULL,NULL,NULL,'4','2077','dealer','1','981102');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('','NULL',NULL,NULL,NULL,NULL,NULL,'4','532','engineer','1','981103');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('New subhadra Electronics and Electricals','NULL',NULL,NULL,NULL,NULL,NULL,'4','2078','dealer','1','981104');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Arun Computer','NULL',NULL,NULL,NULL,NULL,NULL,'4','2079','dealer','1','981105');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('l h electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2080','dealer','1','981106');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('coonar opar & electric center ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2081','dealer','1','981107');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Vijay Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2082','dealer','1','981108');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('kk moideen and son','NULL',NULL,NULL,NULL,NULL,NULL,'4','2083','dealer','1','981109');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Preeti Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2084','dealer','1','981110');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic Hyderabad ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2085','dealer','1','981111');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('dharmendra electronics ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2086','dealer','1','981112');
INSERT INTO contact_person (name,officail_email,alternate_email,official_contact_number,alternate_contact_number,permanent_address,correspondence_address,role	,entity_id,entity_type,is_active,agent_id) VALUES ('Bajaj Electronic ','NULL',NULL,NULL,NULL,NULL,NULL,'4','2087','dealer','1','981113');
contact_person_id
UPDATE `contact_person` SET `role` =1, `agent_id` =1 WHERE role = 4;
UPDATE `header_navigation` SET `entity_type` = '247Around';
UPDATE `entity_role` SET `entity_type` = 'Partner' WHERE `entity_role`.`id` = 1;


CREATE TABLE `header_navigation` (
  `id` int(30) NOT NULL,
  `entity_type` varchar(128) DEFAULT NULL,
  `title` varchar(512) NOT NULL,
  `title_icon` varchar(256) DEFAULT NULL,
  `link` varchar(1024) DEFAULT NULL,
  `level` int(10) NOT NULL,
  `parent_ids` varchar(30) DEFAULT NULL,
  `groups` varchar(200) NOT NULL,
  `nav_type` varchar(20) NOT NULL DEFAULT 'main_nav',
  `is_active` int(10) NOT NULL DEFAULT '1',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `header_navigation`
--

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
(1, '247Around', 'Find User', NULL, 'employee/user', 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:48:40'),
(2, '247Around', 'Queries', NULL, NULL, 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:48:40'),
(3, '247Around', 'Pending Queries (Pincode Available)', NULL, 'employee/booking/view_queries/FollowUp/p_av', 2, '2', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:48:40'),
(4, '247Around', 'Missed Calls', NULL, 'employee/booking/get_missed_calls_view', 2, '2', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 07:48:40'),
(5, '247Around', 'Pending Queries (Pincode Not Available)', NULL, 'employee/booking/view_queries/FollowUp/p_nav', 2, '2', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:48:40'),
(6, '247Around', 'Cancelled Queries', NULL, 'employee/booking/view_queries/Cancelled/p_all', 2, '2', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:49:39'),
(7, '247Around', 'Bookings', NULL, NULL, 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:50:26'),
(8, '247Around', 'Pending Bookings', NULL, 'employee/booking/view_bookings_by_status/Pending', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:51:18'),
(9, '247Around', 'Spare Parts Bookings', NULL, 'employee/inventory/get_spare_parts', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:52:14'),
(10, '247Around', 'OOW Bookings', NULL, 'employee/booking/get_oow_booking', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 07:52:54'),
(11, '247Around', 'Completed Bookings', NULL, 'employee/booking/view_bookings_by_status/Completed', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:53:49'),
(12, '247Around', 'Cancelled Bookings', NULL, 'employee/booking/view_bookings_by_status/Cancelled', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:54:30'),
(13, '247Around', 'Repair Bookings', NULL, 'employee/booking/get_pending_booking_by_partner_id', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 07:55:07'),
(14, '247Around', 'Assign Vendor', NULL, 'employee/vendor/get_assign_booking_form', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:55:47'),
(15, '247Around', 'Review Bookings', NULL, 'employee/booking/review_bookings', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:56:19'),
(16, '247Around', 'Wall Mount Given', NULL, 'employee/booking/update_not_pay_to_sf_booking', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 07:56:56'),
(17, '247Around', 'Auto Assign Bookings', NULL, 'employee/booking/auto_assigned_booking', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 07:57:37'),
(18, '247Around', 'Waiting to Approve Upcountry Bookings', NULL, 'employee/upcountry/get_waiting_for_approval_upcountry_charges', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 07:58:36'),
(19, '247Around', 'Upcountry Failed Bookings', NULL, 'employee/upcountry/get_upcountry_failed_details', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 08:00:21'),
(20, '247Around', 'Reassign Partner', NULL, 'employee/vendor/get_reassign_partner_form', 2, '7', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 08:01:04'),
(21, '247Around', 'Missed Call Rating', NULL, 'employee/booking/show_missed_call_rating_data', 2, '7', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-28 08:01:41'),
(22, '247Around', 'Advance Search', NULL, 'employee/booking/booking_advance_search', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 08:02:25'),
(23, '247Around', 'Bulk Search', NULL, 'employee/booking/booking_bulk_search', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 08:03:05'),
(24, '247Around', 'Partners', NULL, NULL, 1, NULL, 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 08:03:35'),
(25, '247Around', 'View Partners List', NULL, 'employee/partner/viewpartner', 2, '24', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 08:04:21'),
(26, '247Around', 'Send Mail From Template', NULL, 'employee/vendor/get_mail_to_vendors_form', 2, '24', 'admin,developer', 'main_nav', 1, '2017-12-28 08:05:36'),
(27, '247Around', 'Upload Snapdeal Products - Delivered', NULL, 'employee/bookings_excel', 2, '24', 'admin,closure,developer', 'main_nav', 0, '2017-12-28 08:07:08'),
(28, '247Around', 'Upload Snapdeal Products - Shipped', NULL, 'employee/bookings_excel/upload_shipped_products_excel', 2, '24', 'admin,closure,developer', 'main_nav', 0, '2017-12-28 08:07:53'),
(29, '247Around', 'Upload paytm Bookings', NULL, 'employee/bookings_excel/upload_delivered_products_for_paytm_excel', 2, '24', 'admin,closure,developer', 'main_nav', 0, '2017-12-28 08:08:34'),
(30, '247Around', 'Upload Jeeves Bookings', NULL, 'employee/upload_booking_file/upload_booking_files', 2, '24', 'admin,closure,developer', 'main_nav', 0, '2017-12-28 08:09:14'),
(31, '247Around', 'Upload Satya File', NULL, 'employee/bookings_excel/upload_satya_file', 2, '24', 'admin,closure,developer', 'main_nav', 0, '2017-12-28 08:09:42'),
(32, '247Around', 'Upload Akai File', NULL, 'employee/bookings_excel/upload_akai_file', 2, '24', 'admin,closure,developer', 'main_nav', 0, '2017-12-28 08:10:10'),
(33, '247Around', 'Partner Price List', NULL, 'employee/service_centre_charges/show_partner_service_price', 2, '24', 'admin,developer', 'main_nav', 1, '2017-12-28 08:10:39'),
(34, '247Around', 'View Dealer List', NULL, 'employee/dealers/show_dealer_list', 2, '24', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-28 08:11:23'),
(35, '247Around', 'Add Brackets Data', NULL, 'employee/partner/bracket_allocation', 2, '24', 'admin,developer', 'main_nav', 1, '2017-12-28 08:12:04'),
(36, '247Around', 'Service Centers', NULL, NULL, 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 23:58:17'),
(37, '247Around', 'View Service Centers', NULL, 'employee/vendor/viewvendor', 2, '36', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 23:59:01'),
(38, '247Around', 'Search Service Centers', NULL, 'employee/vendor/vendor_availability_form', 2, '36', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-28 23:59:37'),
(39, '247Around', 'Edit Template', NULL, NULL, 2, '36', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:00:25'),
(40, '247Around', 'SMS Template Grid', NULL, 'employee/vendor/get_sms_template_editable_grid', 3, '39', 'admin,developer', 'main_nav', 1, '2017-12-29 00:01:25'),
(41, '247Around', 'TAX RATES Templates Grid', NULL, 'employee/vendor/get_tax_rates_template_editable_grid', 3, '39', 'admin,developer', 'main_nav', 1, '2017-12-29 00:02:20'),
(42, '247Around', 'Vendor Escalation Policy Template Grid', NULL, 'employee/vendor/get_tax_rates_template_editable_grid', 3, '39', 'admin,developer', 'main_nav', 1, '2017-12-29 00:03:15'),
(43, '247Around', 'Appliance Description Template Grid ', NULL, 'employee/booking/get_appliance_description_editable_grid', 3, '39', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:04:13'),
(44, '247Around', 'Send Broadcast Email', NULL, 'employee/vendor/get_broadcast_mail_to_vendors_form', 2, '36', 'admin,developer', 'main_nav', 1, '2017-12-29 00:05:17'),
(45, '247Around', 'Send Mail from Template', NULL, 'employee/vendor/get_mail_to_vendors_form', 2, '36', 'admin,developer', 'main_nav', 1, '2017-12-29 00:06:19'),
(46, '247Around', 'Download SF List', NULL, 'employee/vendor/download_sf_list_excel', 2, '36', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:07:10'),
(47, '247Around', 'Engineers', NULL, NULL, 2, '36', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:08:01'),
(48, '247Around', 'Add Engineer', NULL, 'employee/vendor/add_engineer', 3, '47', 'admin,callcenter,closure,regionalmanager', 'main_nav', 1, '2017-12-29 00:08:50'),
(49, '247Around', 'View Engineers', NULL, 'employee/vendor/get_engineers', 3, '47', 'admin,callcenter,closure,regionalmanager', 'main_nav', 1, '2017-12-29 00:09:36'),
(50, '247Around', 'Update Pincode Distance', NULL, 'employee/upcountry/get_distance_between_pincodes_form', 2, '36', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:10:18'),
(51, '247Around', 'Bank Details', NULL, 'employee/vendor/show_bank_details', 2, '36', 'admin,developer', 'main_nav', 1, '2017-12-29 00:10:48'),
(52, '247Around', 'Appliances', NULL, NULL, 1, NULL, 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-29 00:11:17'),
(53, '247Around', 'Add New Brands', NULL, 'employee/booking/get_add_new_brand_form', 2, '52', 'admin,callcenter,closure,developer', 'main_nav', 1, '2017-12-29 00:11:55'),
(54, '247Around', 'Partner Appliance Details', NULL, 'employee/service_centre_charges/upload_excel_form', 2, '52', 'admin,developer', 'main_nav', 1, '2017-12-29 00:12:41'),
(55, '247Around', 'Update Zooper Price', NULL, 'employee/inventory/update_part_price_details', 2, '52', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:13:27'),
(56, '247Around', 'Invoices', NULL, NULL, 1, NULL, 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:13:59'),
(57, '247Around', 'Generate Invoices ', NULL, 'employee/invoice/get_invoices_form', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 00:14:47'),
(58, '247Around', 'Add New Transaction', NULL, 'employee/invoice/get_add_new_transaction', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 00:15:22'),
(59, '247Around', 'Add Advance Bank Transaction', NULL, 'employee/invoice/get_advance_bank_transaction', 2, '56', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:15:57'),
(60, '247Around', 'Search Invoice ID', NULL, 'employee/accounting/show_search_invoice_id_view', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 00:16:31'),
(61, '247Around', 'Create Brackets Credit Notes', NULL, 'employee/invoice/show_purchase_brackets_credit_note_form', 2, '56', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:17:14'),
(62, '247Around', 'Search Bank Transaction', NULL, 'employee/accounting/search_bank_transaction', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 00:17:49'),
(63, '247Around', 'Partner', NULL, NULL, 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 00:18:25'),
(64, '247Around', 'Create Partner Invoice', NULL, 'employee/invoice/insert_update_invoice/partner', 3, '63', 'admin,developer', 'main_nav', 1, '2017-12-29 00:19:02'),
(65, '247Around', 'Partner Invoices', NULL, 'employee/invoice/invoice_partner_view', 3, '63', 'admin,developer', 'main_nav', 1, '2017-12-29 00:19:33'),
(66, '247Around', 'Partner Transactions', NULL, 'employee/invoice/show_all_transactions/partner', 3, '63', 'admin,developer', 'main_nav', 1, '2017-12-29 00:20:08'),
(67, '247Around', 'Partner Invoice Check', NULL, 'employee/invoiceDashboard', 3, '63', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:20:41'),
(68, '247Around', 'Partner Invoice Summary', NULL, 'employee/invoiceDashboard/get_invoice_summary_for_partner', 3, '63', 'admin', 'main_nav', 1, '2017-12-29 00:21:15'),
(69, '247Around', 'Service Center', NULL, NULL, 2, '56', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:23:58'),
(70, '247Around', 'Create SF Invoice', NULL, 'employee/invoice/insert_update_invoice/vendor', 3, '69', 'admin,developer', 'main_nav', 1, '2017-12-29 00:25:25'),
(71, '247Around', 'Service Centers Invoices', NULL, 'employee/invoice', 3, '69', 'admin,developer', 'main_nav', 1, '2017-12-29 00:27:20'),
(72, '247Around', 'Service Centers Transactions', NULL, 'employee/invoice/show_all_transactions/vendor', 3, '69', 'admin,developer', 'main_nav', 1, '2017-12-29 00:28:14'),
(73, '247Around', 'SF Invoice Check', NULL, 'employee/invoiceDashboard/service_center_invoice', 3, '69', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:28:49'),
(74, '247Around', 'SF Invoice Summary', NULL, 'employee/invoiceDashboard/get_invoice_summary_for_sf', 3, '69', 'admin,developer', 'main_nav', 1, '2017-12-29 00:29:28'),
(75, '247Around', 'Accounts', NULL, NULL, 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 00:29:55'),
(76, '247Around', 'Upload Challan', NULL, 'employee/accounting/get_challan_upload_form', 3, '75', 'admin', 'main_nav', 1, '2017-12-29 00:30:27'),
(77, '247Around', 'Challan History', NULL, 'employee/accounting/get_challan_details', 3, '75', 'admin', 'main_nav', 1, '2017-12-29 00:31:19'),
(78, '247Around', 'Invoice Summary Report', NULL, 'employee/accounting/accounting_report', 3, '75', 'admin', 'main_nav', 1, '2017-12-29 00:32:11'),
(79, '247Around', 'Search Challan ID', NULL, 'employee/accounting/show_search_challan_id_view', 3, '75', 'admin', 'main_nav', 1, '2017-12-29 00:32:45'),
(80, '247Around', 'Reports', NULL, NULL, 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:33:14'),
(81, '247Around', 'SF Bookings Snapshot', NULL, 'employee/vendor/show_service_center_report', 2, '80', 'admin,closure,developer', 'main_nav', 1, '2017-12-29 00:33:49'),
(82, '247Around', 'Newly Added SF (2 Months)', NULL, 'employee/vendor/new_service_center_report', 2, '80', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:34:33'),
(83, '247Around', 'Download SF Pending Summary', NULL, 'BookingSummary/get_pending_bookings/0', 2, '80', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:35:11'),
(84, '247Around', 'SF Missed Target Reports', NULL, 'BookingSummary/get_sc_crimes/0', 2, '80', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:35:51'),
(85, '247Around', 'RM Crimes Report', NULL, 'BookingSummary/get_rm_crimes/0', 2, '80', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:36:20'),
(86, '247Around', 'RM Performance Stats', NULL, 'BookingSummary/show_reports_chart', 2, '80', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:36:52'),
(87, '247Around', 'New Dashboard', NULL, 'employee/dashboard', 2, '80', 'admin,developer', 'main_nav', 1, '2017-12-29 00:37:34'),
(88, '247Around', 'Download serviceability Report', NULL, 'employee/vendor/get_sms_template_editable_grid', 2, '80', 'admin,closure,developer,regionalmanager', 'main_nav', 0, '2017-12-29 00:38:11'),
(89, '247Around', 'Inventory', NULL, NULL, 1, NULL, 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:38:44'),
(90, '247Around', 'Add Brackets', NULL, 'employee/inventory/get_bracket_add_form', 2, '89', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:39:13'),
(91, '247Around', 'Show Bracket List', NULL, 'employee/inventory/show_brackets_list', 2, '89', 'admin,closure,developer,regionalmanager', 'main_nav', 1, '2017-12-29 00:39:42'),
(92, '247Around', 'Vendor Inventory Details', NULL, 'employee/inventory/get_vendor_inventory_list_form', 2, '89', 'admin,closure,developer', 'main_nav', 0, '2017-12-29 00:40:17'),
(93, '247Around', 'Add Employee', NULL, 'employee/user/add_employee', 1, NULL, 'admin,developer', 'right_nav', 1, '2017-12-29 01:02:05'),
(94, '247Around', 'Employee List', NULL, 'employee/user/show_employee_list', 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'right_nav', 1, '2017-12-29 01:02:37'),
(95, '247Around', 'Holiday List ', NULL, 'employee/user/show_holiday_list', 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'right_nav', 1, '2017-12-29 01:03:10'),
(96, '247Around', 'Edit Profile', NULL, 'employee/user/update_employee', 1, NULL, 'admin,callcenter,closure,developer,regionalmanager', 'right_nav', 1, '2017-12-29 04:32:02'),
(97, '247Around', 'SF Document List', NULL, 'employee/vendor/show_vendor_documents_view', 2, '36', 'admin,developer,regionalmanager', 'main_nav', 1, '2017-12-29 05:03:32'),
(98, '247Around', 'Service Centers Invoices', NULL, 'employee/invoice', 2, '56', 'admin,developer', 'main_nav', 1, '2017-12-29 05:07:02'),
(99, '247Around', 'Service Centers Reports', NULL, 'employee/vendor/show_service_center_report', 2, '80', 'regionalmanager', 'main_nav', 1, '2017-12-29 05:08:28'),
(100, '247Around', 'Dashboard', NULL, 'employee/vendor/show_around_dashboard', 2, '80', 'callcenter,closure,regionalmanager', 'main_nav', 1, '2017-12-29 05:10:25'),
(101, '247Around', 'Bookings', NULL, 'employee/user/get_user_count_view', 2, '80', 'regionalmanager', 'main_nav', 1, '2017-12-29 05:11:41'),
(102, '247Around', 'Users', NULL, 'employee/user/user_count', 2, '80', 'regionalmanager', 'main_nav', 1, '2017-12-29 05:12:21'),
(103, '247Around', 'Buyback Dashboard', NULL, 'employee/dashboard/buyback_dashboard', 2, '80', 'regionalmanager', 'main_nav', 1, '2017-12-29 05:12:59'),
(104, '247Around', 'Partner Leads', NULL, 'employee/booking/get_missed_calls_view', 2, '7', 'closure', 'main_nav', 1, '2017-12-29 05:33:16'),
(105, '247Around', 'No Installation, Only Stand Given', NULL, 'employee/booking/update_not_pay_to_sf_booking', 2, '7', 'closure', 'main_nav', 1, '2017-12-29 05:34:28'),
(106, '247Around', 'Upload File Header Mapping', NULL, 'employee/bookings_excel/file_upload_header_mapping', 2, '24', 'admin,closure,developer', 'main_nav', 1, '2018-01-09 06:52:43'),
(107, '247Around', 'Add Inventory Stocks', NULL, 'employee/inventory/update_inventory_stock', 2, '89', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 0, '2018-01-17 04:50:36'),
(108, '247Around', 'Upload Aguagrand Plus File', NULL, 'employee/bookings_excel/upload_aquagrand_plus_file', 2, '24', 'admin,closure,developer', 'main_nav', 0, '2018-01-25 06:41:21'),
(109, '247Around', 'Show Inventory Stocks', NULL, 'employee/inventory/show_inventory_stock_list', 2, '89', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 0, '2018-01-25 06:41:21'),
(110, '247Around', 'Generate Spare Purchase Invoice', NULL, 'employee/inventory/spare_invoice_list', 10, '56', 'admin,developer', 'main_nav', 1, '2018-01-30 02:04:32'),
(111, '247Around', 'Navigation Management', NULL, 'employee/login/user_role_management', 1, NULL, 'admin,developer', 'right_nav', 1, '2018-02-01 00:58:04'),
(112, '247Around', 'Upload Partner Booking File', NULL, 'employee/do_background_upload_excel/upload_partner_booking_file', 2, '24', 'admin,closure,developer', 'main_nav', 1, '2018-02-17 04:33:42'),
(113, '247Around', 'Add pincode in India Pincode', NULL, 'employee/vendor/insert_pincode_form', 2, '36', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2018-02-20 00:27:39'),
(114, '247Around', 'Get Pincode Distance', NULL, 'partner/uploadpincodefile', 2, '24', 'admin', 'main_nav', 1, '2018-02-22 04:15:45'),
(115, '247Around', 'Engineer Action Review', NULL, 'employee/engineer/review_engineer_action_by_admin', 2, '7', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2018-02-26 08:07:19'),
(116, '247Around', 'Inventory Master List', NULL, 'employee/inventory/inventory_master_list', 2, '89', 'admin,closure,developer,regionalmanager', 'main_nav', 0, '2018-03-13 07:09:26'),
(117, '247Around', 'Customer Invoice', NULL, 'employee/invoice/customer_invoice', 2, '56', 'admin,callcenter,closure,developer,regionalmanager', 'main_nav', 1, '2018-04-12 23:27:13'),
(118, '247Around', 'Upload Inventory Master List', NULL, 'upload_inventory_details_file', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-05-07 07:54:48'),
(119, '247Around', 'Tag Spare Invoice', NULL, 'employee/inventory/tag_spare_invoice_send_by_partner', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-05-30 05:27:04'),
(120, '247Around', 'Upload Model File', NULL, 'employee/inventory/upload_appliance_model_details', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-05-31 00:05:34'),
(121, '247Around', 'Upload Inventory Model Mapping', NULL, 'employee/inventory/upload_bom_file', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-05-31 00:06:35'),
(122, '247Around', 'Inventory Model Details', NULL, 'employee/inventory/appliance_model_list', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-06-04 23:57:42'),
(123, '247Around', 'Inventory Master List', NULL, 'employee/inventory/inventory_master_list', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-06-04 23:58:47'),
(124, '247Around', 'Warehouse Inventory List', NULL, 'employee/inventory/get_wh_inventory_stock_list', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-06-05 00:00:43'),
(125, '247Around', 'Inventory Send By Partner To Warehouse', NULL, 'employee/inventory/acknowledge_spares_send_by_partner_by_admin', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-06-05 02:05:52'),
(126, '247Around', 'Inventory Send By Partner To Warehouse', NULL, 'employee/inventory/acknowledge_spares_send_by_partner_by_admin', 0, '89', 'admin,closure,developer', 'main_nav', 1, '2018-06-05 02:05:52'),
(127, 'Partner', 'Advance Search', 'fa fa-search', 'partner/get_user_form', 1, NULL, 'poc', 'main_nav', 1, '2018-06-11 08:39:19'),
(128, 'Partner', 'Bookings', 'fa fa-book', NULL, 1, NULL, 'poc', 'main_nav', 1, '2018-06-11 08:40:38'),
(129, 'Partner', 'Pending Bookings', NULL, 'partner/home', 2, '128', 'poc', 'main_nav', 1, '2018-06-11 08:41:22'),
(130, 'Partner', 'Completed Bookings', NULL, 'partner/closed_booking/Completed', 2, '128', 'poc', 'main_nav', 1, '2018-06-11 08:42:00'),
(131, 'Partner', 'Cancelled Bookings', NULL, 'partner/closed_booking/Cancelled', 2, '128', 'poc', 'main_nav', 1, '2018-06-11 08:42:50'),
(132, 'Partner', 'Spare Bookings', 'fa fa-truck', NULL, 1, NULL, 'poc', 'main_nav', 1, '2018-06-11 08:43:37'),
(133, 'Partner', 'Pending Spare On Partner', NULL, 'partner/get_spare_parts_booking', 2, '132', 'poc', 'main_nav', 1, '2018-06-11 08:44:57'),
(134, 'Partner', 'Shipped Spare By Partner', NULL, 'partner/get_shipped_parts_list', 2, '132', 'poc', 'main_nav', 1, '2018-06-11 08:45:43'),
(135, 'Partner', 'Pending Spare On SF', NULL, 'partner/get_pending_part_on_sf', 2, '132', 'poc', 'main_nav', 1, '2018-06-11 08:46:51'),
(136, 'Partner', 'Shipped Spare By SF', NULL, 'partner/get_waiting_defective_parts', 2, '132', 'poc', 'main_nav', 1, '2018-06-11 08:47:26'),
(137, 'Partner', 'Received Spare By Partner ', NULL, 'partner/get_approved_defective_parts_booking', 2, '132', 'poc', 'main_nav', 1, '2018-06-11 08:49:29'),
(138, 'Partner', 'Invoices', 'fa fa-inr', NULL, 1, NULL, 'poc', 'main_nav', 1, '2018-06-11 08:50:10'),
(139, 'Partner', 'Invoice', NULL, 'partner/invoices_details', 2, '138', 'poc', 'main_nav', 1, '2018-06-11 08:50:47'),
(140, 'Partner', 'Bank Transactions', NULL, 'partner/banktransaction', 2, '138', 'poc', 'main_nav', 1, '2018-06-11 08:51:26'),
(141, 'Partner', 'Pay', NULL, 'payment/details', 2, '138', 'poc', 'main_nav', 1, '2018-06-11 08:51:58'),
(142, 'Partner', 'Downloads', 'glyphicon glyphicon-download-alt', 'partner/reports', 1, NULL, 'poc', 'main_nav', 1, '2018-06-11 08:52:58'),
(143, 'Partner', 'Contracts', 'fa fa-handshake-o', 'partner/contracts', 1, NULL, 'poc', 'main_nav', 1, '2018-06-11 08:53:48'),
(144, 'Partner', 'Contact Us', 'fa fa-phone', 'partner/contact_us', 2, NULL, 'poc', 'main_nav', 1, '2018-06-11 08:54:58'),
(145, 'Partner', 'Edit Details', 'fa fa-edit pull-right', 'employee/partner/show_partner_edit_details_form', 0, NULL, 'poc', 'right_nav', 1, '2018-06-11 09:01:03'),
(146, 'Partner', 'Reset Password', 'fa fa-key pull-right', 'employee/partner/reset_partner_password', 0, NULL, 'poc', 'right_nav', 1, '2018-06-11 09:02:21'),
(147, 'Partner', 'Log Out', 'fa fa-sign-out pull-right', 'employee/partner/logout', 0, NULL, 'poc', 'right_nav', 1, '2018-06-11 09:03:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `header_navigation`
--
ALTER TABLE `header_navigation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `header_navigation`
--
ALTER TABLE `header_navigation`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;COMMIT;
INSERT INTO `agent_filters` (`id`, `entity_type`, `entity_id`, `contact_person_id`, `agent_id`, `state`, `is_active`, `create_date`) VALUES (NULL, 'Partner', '247034', '1001188', '980180', 'DELHI', '1', CURRENT_TIMESTAMP);
INSERT INTO `agent_filters` (`id`, `entity_type`, `entity_id`, `contact_person_id`, `agent_id`, `state`, `is_active`, `create_date`) VALUES (NULL, 'Partner', '247034', '1001189', '980181', 'UTTAR PRADESH', '1', CURRENT_TIMESTAMP);
INSERT INTO `agent_filters` (`id`, `entity_type`, `entity_id`, `contact_person_id`, `agent_id`, `state`, `is_active`, `create_date`) VALUES (NULL, 'Partner', '247034', '1001190', '980182', 'CHANDIGARH', '1', CURRENT_TIMESTAMP);
UPDATE `contact_person` SET `role` = 2 WHERE id IN ('1001188','1001189','1001190');


--4th June
ALTER TABLE `contact_person` ADD `is_active` INT(10) NOT NULL DEFAULT '1' AFTER `update_date`;
ALTER TABLE `contact_person` ADD `agent_id` INT(10) NOT NULL AFTER `is_active`;
ALTER TABLE `entity_login_table` ADD `contact_person_id` INT(10) NULL AFTER `entity_name`;
ALTER TABLE contact_person AUTO_INCREMENT = 10000;

--Abhay
ALTER TABLE `courier_details` ADD `courier_charge` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `shipment_date`;
ALTER TABLE `inventory_ledger` ADD `vendor_warehouse_invoice_id` VARCHAR(12) NULL DEFAULT NULL AFTER `invoice_id`;
ALTER TABLE `inventory_ledger` ADD `partner_warehouse_invoice_id` VARCHAR(12) NULL DEFAULT NULL AFTER `vendor_warehouse_invoice_id`;

ALTER TABLE `agent_filters` ADD `contact_person_id` INT NULL DEFAULT NULL AFTER `create_date`;
ALTER TABLE `contact_person` ADD `agent_id` INT NULL DEFAULT NULL AFTER `update_date`;


--Abhay 18 June
ALTER TABLE `courier_details` ADD `sender_invoice_id` VARCHAR(64) NULL DEFAULT NULL AFTER `remarks`, ADD `partner_invoice_id` VARCHAR(64) NULL DEFAULT NULL AFTER `sender_invoice_id`;
ALTER TABLE `courier_details` ADD `quantity` INT NOT NULL DEFAULT '0' AFTER `courier_charge`, ADD `booking_id` TEXT NULL DEFAULT NULL AFTER `quantity`;
ALTER TABLE `courier_details` ADD `bill_to_partner` INT NULL DEFAULT NULL AFTER `receiver_entity_type`;
ALTER TABLE `spare_parts_details` ADD `warehouse_courier_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `defective_parts_shippped_courier_pic_by_wh`;

--Abhay 19 June
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'sf_warehouse_invoice', '247around - %s - FOC Invoice for period: %s to %s', 'Dear Partner,Please find attached Warehouse invoice. Please do <strong>Reply All</strong> for raising any query or concern regarding the invoice. <br/><br/>Thanks,<br/>247around Team', 'billing@247around.com', 'abhaya@247around.com', 'abhaya@247around.com', '', '1', '2017-05-29 23:56:58');



-- 20 June
ALTER TABLE `spare_parts_details` ADD `courier_pic_by_partner` VARCHAR(1024) NULL DEFAULT NULL AFTER `courier_price_by_partner`;


--Released 22 June


--sachin 14 june
ALTER TABLE `contact_person` CHANGE `officail_email` `official_email` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

--sachin 22 june

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'spare_send_by_partner_to_wh', 'Spare shipped by %s to %s', 
'Dear Partner,<br><br>

<b>%s</b> shipped below spare to your warehouse.<br><br>
%s
<br>

<b>Courier Details </b><br><br>
%s<br>

Regards,<br>
247around
', 
'noreply@247around.com', '', 'sachins@247around.com', '', '1', '2016-06-17 00:00:00');


INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'spare_received_by_wh_from_partner', 'Spare received by %s send from %s', 
'Dear Partner,<br><br> <b>%s</b> received below spare <br><br> %s <br> Regards,<br> 247around', 
'noreply@247around.com', '', 'sachinj@247around.com', '', '1', '2016-06-17 00:00:00');

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'defective_spare_send_by_wh_to_partner', 'Defective Spare shipped by %s to %s', 
'Dear Partner,<br><br> <b>%s</b> shipped below defective spare to your warehouse.<br><br> %s <br> <b>Courier Details </b><br><br> %s<br> Regards,<br> 247around', 
'noreply@247around.com', '', 'sachins@247around.com', '', '1', '2016-06-17 00:00:00');

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
 VALUES (NULL, 'defective_spare_received_by_partner_from_wh', 'Defective Spare received by %s', 
'Dear Partner,<br><br> <b>%s</b> received below defective spare <br><br> %s <br> Regards,<br> 247around', 
'noreply@247around.com', '', 'sachinj@247around.com', '', '1', '2016-06-17 00:00:00');

INSERT INTO `entity_role` (`id`, `entity_type`, `department`, `role`, `is_filter_applicable`, `create_date`) VALUES
(null, 'partner', 'warehouse', 'wh_incharge', 0, '2018-06-22 11:16:20'),
(null, 'vendor', 'warehouse', 'wh_incharge', 0, '2018-06-22 11:16:20');

-- Chhavi 25th June
CREATE TABLE `booking_internal_conversation` (
  `id` int(10) NOT NULL,
  `booking_id` varchar(100) NOT NULL,
  `subject` text NOT NULL,
  `msg` text NOT NULL,
  `sender_entity_type` varchar(100) NOT NULL,
  `sender_entity_id` int(10) NOT NULL,
  `agent_id` int(10) NOT NULL,
  `email_to` varchar(256) NOT NULL,
  `email_cc` varchar(256) NOT NULL,
  `email_from` varchar(256) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` int(10) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_internal_conversation`
--
ALTER TABLE `booking_internal_conversation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_internal_conversation`
--
ALTER TABLE `booking_internal_conversation`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;COMMIT;



--sachin 25 June
ALTER TABLE `inventory_ledger` ADD `courier_id` INT NULL DEFAULT NULL AFTER `partner_ack_date`;

--Abhay 20 June
ALTER TABLE `blacklist_brand` ADD `blacklist` INT(1) NOT NULL DEFAULT '0' AFTER `brand`, ADD `whitelist` INT(1) NOT NULL DEFAULT '0' AFTER `blacklist`;
ALTER TABLE `vendor_partner_invoices` ADD `reference_invoice_id` VARCHAR(64) NULL DEFAULT NULL AFTER `invoice_id`;


--Abhay 22 June

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES
(NULL, 'booking_misc_charges_details', 'New Miscellaneous Charges Added For Booking ID \n %s', 'Hi,<br/><br/>\nNew Miscellaneous Charges added By %s. Please find the details below: <br/><br/>\n%s<br/><br/>\n\nPlease %s to check these details.\n<br/>Thanks!!;', 'booking@247around.com', 'anuj@247around.com, nits@247around.com', 'abhaya@247around.com', '', '1', '2018-06-21 18:30:00');


--
-- Table structure for table `miscellaneous_charges`
--

CREATE TABLE `miscellaneous_charges` (
  `id` int(11) NOT NULL,
  `description` varchar(128) NOT NULL,
  `booking_id` varchar(64) NOT NULL,
  `vendor_basic_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `vendor_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_rate` int(11) NOT NULL,
  `partner_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `approval_file` varchar(128) DEFAULT NULL,
  `remarks` varchar(128) DEFAULT NULL,
  `product_or_services` varchar(64) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `partner_invoice_id` varchar(64) DEFAULT NULL,
  `vendor_invoice_id` varchar(64) DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `miscellaneous_charges`
--
ALTER TABLE `miscellaneous_charges`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `miscellaneous_charges`
--
ALTER TABLE `miscellaneous_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--sachin 25 June
ALTER TABLE `inventory_ledger` ADD `courier_id` INT NULL DEFAULT NULL AFTER `partner_ack_date`;


--
-- Table structure for table `vendor_partner_varialble_charges`
--

CREATE TABLE `vendor_partner_varialble_charges` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(28) NOT NULL,
  `entity_id` varchar(11) NOT NULL,
  `charges_type` varchar(64) NOT NULL,
  `description` varchar(128) NOT NULL,
  `fixed_charges` decimal(10,0) DEFAULT '0',
  `percentage_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `hsn_code` varchar(64) DEFAULT NULL,
  `gst_rate` decimal(10,0) DEFAULT '0',
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vendor_partner_varialble_charges`
--
ALTER TABLE `vendor_partner_varialble_charges`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vendor_partner_varialble_charges`
--
ALTER TABLE `vendor_partner_varialble_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Released 28 June

--Abhay 28 June
ALTER TABLE `service_center_booking_action` ADD `model_number` VARCHAR(128) NULL DEFAULT NULL AFTER `serial_number`;
ALTER TABLE `booking_unit_details` ADD `sf_model_number` VARCHAR(128) NULL DEFAULT NULL AFTER `model_number`;


--sachin 28 june
UPDATE `email_template` SET `subject` = '247around %s through CRM Payment Gateway' WHERE `email_template`.`tag` = 'payment_transaction_email';

--Abhay 29 June
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'cp_out_standing_email', '%s', 'Dear Partner,<br/><br/> outstanding Amount %s <br/><br/> %s<br/><br/> <br/>Thanks!!;', 'booking@247around.com', '', 'abhaya@247around.com', '', '1', '2018-06-29 00:00:00');
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'cp_outstanding_sms', '%s', '', '1', '2018-06-29 18:59:32');


--Abhay 3 July
ALTER TABLE `service_centres` ADD `cp_credit_limit` DECIMAL NULL DEFAULT '0' AFTER `on_off`;

--Abhay 6 July
ALTER TABLE `booking_details` ADD `upcountry_update_date` DATETIME NULL DEFAULT NULL AFTER `service_center_closed_date`;
--Abhay 3 July
ALTER TABLE `service_centres` ADD `cp_credit_limit` DECIMAL NULL DEFAULT '0' AFTER `on_off`;
--Chhavi 06th July
ALTER TABLE `courier_details` ADD `contact_person_id` INT(10) NOT NULL AFTER `partner_invoice_id`;
ALTER TABLE `courier_details` ADD `document_type` VARCHAR(100) NOT NULL AFTER `receiver_entity_id`;

ALTER TABLE `booking_details` ADD `upcountry_update_date` DATETIME NULL DEFAULT NULL AFTER `service_center_closed_date`;
--Chhavi 4 July
ALTER TABLE `inventory_master_list` ADD `is_local_purchase` INT(1) NOT NULL DEFAULT '0' AFTER `create_date`;
ALTER TABLE `service_centre_charges` ADD `is_local_purchase` INT(1) NOT NULL DEFAULT '0' AFTER `create_date`;

--Namrata 10th July
--
-- Table structure for table `booking_comments`
--

CREATE TABLE `booking_comments` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(28) NOT NULL,
  `booking_id` varchar(128) NOT NULL,
  `agent_id` int(255) NOT NULL,
  `remarks` text NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `spare_parts_details` ADD `entity_type` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `booking_id`;
ALTER TABLE `contact_person` ADD `department` VARCHAR(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `role`;
ALTER TABLE  `request_type` ADD  `create_date` TIMESTAMP NOT NULL;
ALTER TABLE  `service_category_mapping` ADD  `create_date` TIMESTAMP NOT NULL;
ALTER TABLE `booking_details` ADD `isActive` INT(1) NOT NULL DEFAULT '0' AFTER `remarks`;

ALTER TABLE `booking_details` ADD `isActive` INT(1) NOT NULL DEFAULT '0' AFTER `remarks`;

--sachin  11 July
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'escalation_on_booking_from_partner_panel', 
'Booking ID : %s Escalated', '<br>Dear Account Manager,<br><br> Booking ID : 
<strong>%s</strong> is escalated <br> Reason : %s <br> Attend this booking immediately. 
<br><br> Regards,<br> 247around Team', '', '', '', '', '1', '2016-09-26 18:30:00');

UPDATE `email_template` SET `cc` = '' WHERE `email_template`.`tag` = 'escalation_on_booking';

--Abhay 7 July
ALTER TABLE `inventory_stocks` ADD `pending_request_count` INT NULL DEFAULT '0' AFTER `stock`;
UPDATE `email_template` SET `cc` = '' WHERE `email_template`.`tag` = 'escalation_on_booking';

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) 
VALUES (NULL, 'missed_call_confirmed_for_google', 'Thank you for demo confirmation, 
%s Installation & Demo of your %s would be done %s.Installation Powered by 247around.com', 
'SMS sent when customer gives a missed call to confirm demo og google home speaker', '1', '2016-09-22 15:35:25');


UPDATE `sms_template` SET `template` = 'Kudos to you for placing Google Home demo request. 
Check Super Answer Video from Google http://bit.ly/2up6Kwq | http://bit.ly/2s4PzAc | http://bit.ly/2INmjUE - 247around Flipkart Partner' 
WHERE `sms_template`.`tag` = 'flipkart_google_scheduled_sms';

--Abhay 7 July
ALTER TABLE `inventory_stocks` ADD `pending_request_count` INT NULL DEFAULT '0' AFTER `stock`;

ALTER TABLE `spare_parts_details` ADD `invoice_gst_rate` INT(11) NULL DEFAULT '18' AFTER `sell_price`;

--Abhay 11 July
ALTER TABLE `bank_transactions` ADD `payment_txn_id` VARCHAR(1024) NULL DEFAULT NULL AFTER `transaction_id`;

-- sachin 12 July
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
(null, '247Around', 'Search Spare Invoice', NULL, 'employee/inventory/show_spare_details_by_spare_invoice', 0, '89', 'admin,closure,inventory_manager', 'main_nav', 1, '2018-07-12 05:12:36');

--Abhay 12 July
ALTER TABLE `spare_parts_details` ADD `wh_ack_received_part` INT(1) NOT NULL DEFAULT '1' AFTER `inventory_id`;

--Abhay 13 July
ALTER TABLE `partners` ADD `gst_number_file` VARCHAR(1024) NULL DEFAULT NULL AFTER `gst_number`;
ALTER TABLE `trigger_partners` ADD `ALTER TABLE ``partners`` ADD ``gst_number_file`` VARCHAR(1024) NULL` VARCHAR(1024) NULL DEFAULT NULL AFTER `gst_number`;

--Abhay July
ALTER TABLE `spare_parts_details` ADD `date_of_request_from_warehouse` DATETIME NULL DEFAULT NULL AFTER `date_of_request`; 

--Abhay 16 July
ALTER TABLE `vendor_partner_invoices` ADD `invoice_tagged` VARCHAR(64) NULL DEFAULT NULL AFTER `type`;
ALTER TABLE `trigger_vendor_partner_invoices` ADD `invoice_tagged` VARCHAR(64) NULL DEFAULT NULL AFTER `type`

ALTER TABLE `spare_parts_details` ADD `partner_warehouse_courier_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `warehouse_courier_invoice_id`;

--Abhay 19 July
ALTER TABLE `vendor_partner_invoices` ADD `packaging_rate` DECIMAL(10,2) NULL DEFAULT '0' AFTER `courier_charges`, ADD `packaging_quantity` INT(11) NULL DEFAULT '0' AFTER `packaging_rate`;

--Abhay 20 July
ALTER TABLE `spare_parts_details` ADD `partner_courier_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `partner_warehouse_courier_invoice_id`;

--Chhavi 23rd July
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
(NULL, 'Partner', 'Dashboard', 'fa fa-dashboard', 'partner/dashboard', 1, NULL, 'area_sales_manager,poc', 'main_nav', 1, '2018-07-23 10:01:02');

--Abhay 21 July
ALTER TABLE `spare_parts_details` ADD `vendor_courier_invoice_id`  VARCHAR(28) NULL DEFAULT NULL AFTER `partner_courier_invoice_id`;


--sachin 25 July
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'bank_details_verification_email', '%s | Please Verify Your Bank Details', 'Dear Partner<br><br> Your account details could not be verified so request you to send the bank passbook front page or cancelled cheque copy immediately.<br><br> Regards<br><br> Team 247around', '', '', '', '', '1', '2017-08-29 15:06:23');

--Chhavi 23rd July
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
(NULL, 'Partner', 'Dashboard', 'fa fa-dashboard', 'partner/dashboard', 1, NULL, 'area_sales_manager,poc', 'main_nav', 1, '2018-07-23 10:01:02');
--Chhavi 26th July
CREATE TABLE `partner_summary_report_mapping` (
  `id` int(11) NOT NULL,
  `Title` varchar(128) NOT NULL,
  `sub_query` text,
  `is_default` int(11) NOT NULL DEFAULT '0',
  `partner_id` text NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `index_in_report` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `partner_summary_report_mapping`
--

INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES
(1, 'Order ID', ' order_id AS \'Order ID\'', 1, '', 1, 1),
(2, '247BookingID', 'booking_details.booking_id AS \'247BookingID\'', 1, '', 1, 2),
(3, 'Referred Date', ' booking_details.create_date AS \'Referred Date\'', 1, '', 1, 3),
(4, 'Brand', 'ud.appliance_brand AS \'Brand\'', 1, '', 1, 4),
(5, 'Purchase Date', 'ud.purchase_date AS \'Purchase Date\'', 1, '', 1, 5),
(6, 'Model', 'IFNULL(ud.model_number,\'\') AS \'Model\'', 1, '', 1, 6),
(7, 'Serial Number', 'CASE WHEN(ud.serial_number IS NULL OR ud.serial_number = \'\') THEN \'\' ELSE (CONCAT(\'\'\'\', GROUP_CONCAT(ud.serial_number)))  END AS \'Serial Number\'', 1, '', 1, 7),
(8, 'Product', 'services AS \'Product\'', 1, '', 1, 8),
(9, 'Description', 'ud.appliance_description As \'Description\'', 1, '', 1, 9),
(10, 'Customer', 'name As \'Customer\'', 1, '', 1, 10),
(11, 'Address', 'home_address AS \'Address\'', 0, '', 247034, 11),
(12, 'Pincode', 'booking_pincode AS \'Pincode\'', 1, '', 1, 12),
(13, 'City', ' booking_details.city As \'City\'', 1, '', 1, 13),
(14, 'State', 'booking_details.state As \'State\'', 1, '', 1, 14),
(15, 'Phone', 'booking_primary_contact_no AS \'Phone\'', 1, '', 1, 15),
(16, 'Email', 'user_email As \'Email\'', 1, '', 1, 16),
(17, 'Service Type', 'ud.price_tags AS \'Service Type\'', 1, '', 1, 17),
(18, 'Remarks', 'CASE WHEN(current_status = \'Completed\' || current_status = \'Cancelled\') THEN (closing_remarks) ELSE (reschedule_reason) END AS \'Remarks\'', 1, '', 1, 18),
(19, 'Current Booking Date', 'booking_date As \'Current Booking Date\'', 1, '', 1, 19),
(20, 'First Booking Date', 'initial_booking_date As \'First Booking Date\'', 1, '', 1, 20),
(21, 'Timeslot', 'booking_timeslot AS \'Timeslot\'', 1, '', 1, 21),
(22, 'Final Status', 'partner_internal_status AS \'Final Status\'', 1, '', 1, 22),
(23, 'Is Upcountry', 'CASE WHEN (booking_details.is_upcountry = \'0\') THEN \'Local\' ELSE \'Upcountry\' END as \'Is Upcountry\'', 1, '', 1, 23),
(24, 'Completion Date', 'date(booking_details.service_center_closed_date) AS \'Completion Date\'', 1, '', 1, 24),
(25, 'TAT', '(CASE WHEN current_status  = \"Completed\" THEN (CASE WHEN DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,\"%d-%m-%Y\")) < 0 THEN 0 ELSE DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,\"%d-%m-%Y\")) END) ELSE \"\" END) as TAT', 1, '', 1, 25),
(26, 'Ageing', '(CASE WHEN current_status  IN (\"Pending\",\"Rescheduled\",\"FollowUp\") THEN DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,\"%d-%m-%Y\")) ELSE \"\" END) as Ageing', 1, '', 1, 26),
(27, 'Rating', 'booking_details.rating_stars AS \'Rating\'', 1, '', 1, 27),
(28, 'Rating Comments', 'booking_details.rating_comments AS \'Rating Comments\'', 1, '', 1, 28),
(29, 'Requested Part', 'GROUP_CONCAT(spare_parts_details.parts_requested) As \'Requested Part\'', 1, '', 1, 29),
(30, 'Part Requested Date', 'GROUP_CONCAT(spare_parts_details.date_of_request) As \'Part Requested Date\'', 1, '', 1, 30),
(31, 'Shipped Part', 'GROUP_CONCAT(spare_parts_details.parts_shipped) As \'Shipped Part\'', 1, '', 1, 31),
(32, 'Part Shipped Date', 'GROUP_CONCAT(spare_parts_details.shipped_date) As \'Part Shipped Date\'', 1, '', 1, 32),
(33, 'SF Acknowledged Date', 'GROUP_CONCAT(spare_parts_details.acknowledge_date) As \'SF Acknowledged Date\'', 1, '', 1, 33),
(34, 'Shipped Defective Part', 'GROUP_CONCAT(spare_parts_details.defective_part_shipped) As \'Shipped Defective Part\'', 1, '', 1, 34),
(35, 'Defective Part Shipped Date', 'GROUP_CONCAT(spare_parts_details.defective_part_shipped_date) As \'Defective Part Shipped Date\'', 1, '', 1, 35),
(36, 'Dependency', 'api_call_status_updated_on_completed AS Dependency', 0, '247034,247077,247030', 1, 36),
(37, 'Cancellation Remarks', 'booking_details.cancellation_reason AS \'Cancellation Remarks\'', 0, '247077', 1, 37),
(38, 'SF ID', 'booking_details.assigned_vendor_id AS \'SF ID\'', 0, '247064', 1, 38);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `partner_summary_report_mapping`
--
ALTER TABLE `partner_summary_report_mapping`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `partner_summary_report_mapping`
--
ALTER TABLE `partner_summary_report_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;COMMIT;

--- Kalyani 27-07-2018
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
(NULL, '247Around', 'Email Search', 'NULL', 'employee/inventory/seach_by_email', 2, 80, 'admin,developer', 'main_nav', 1, '2018-07-26 15:50:15');

--Abhay 25 July
ALTER TABLE `trigger_service_centres` ADD `gst_status` VARCHAR(28) NULL DEFAULT NULL AFTER `gst_no`, ADD `gst_taxpayer_type` VARCHAR(28) NULL DEFAULT NULL AFTER `gst_status`
ALTER TABLE `trigger_service_centres` ADD `gst_status` VARCHAR(28) NULL DEFAULT NULL AFTER `gst_no`, ADD `gst_taxpayer_type` VARCHAR(28) NULL DEFAULT NULL AFTER `gst_status`

ALTER TABLE `vendor_gst_detail` ADD PRIMARY KEY (`id`);

ALTER TABLE `vendor_gst_detail` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--Kalyani 28-07-2018
ALTER TABLE `booking_comments` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

--- Kalyani 31-07-2018
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES ('70', 'taxpro_api_fail', 'Taxpro GSP Api Fail', '<b>TAXPRO GSP API FAIL </b>\r\n<br/>\r\n<p>%s</p>', 'noreply@247around.com', '', '', '', '1', '', CURRENT_TIMESTAMP);

--Kalyani 28-07-2018
ALTER TABLE `booking_comments` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

--- Kalyani 31-07-2018
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES ('70', 'taxpro_api_fail', 'Taxpro GSP Api Fail', '<b>TAXPRO GSP API FAIL </b>\r\n<br/>\r\n<p>%s</p>', 'noreply@247around.com', '', '', '', '1', '', CURRENT_TIMESTAMP);
--Chhavi
ALTER TABLE `spare_parts_details` ADD `spare_cancelled_date` DATETIME  NULL AFTER `challan_approx_value`;

--- Kalyani 02-08-2018
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES ('71', 'gst_detail_change', '%s GST detail changed', '<b>GST Detail Changed</b>\r\n<br/>\r\n<p><b>Previous Detail</b></p>\r\n GST No => %s </br>\r\n GST Status => %s <br/>\r\n GST Type => %s </br>\r\n GST Cancelled Date => %s </br> \r\n<p>Updated Detail</p>\r\n GST No => %s </br>\r\n GST Status => %s </br>\r\n GST Type => %s </br>\r\n GST Cancelled Date => %s', '', '', '', '', '1', '', '0000-00-00') WHERE `email_template`.`id` = 71 AND `email_template`.`tag` = 'gst_detail_change' AND `email_template`.`subject` = '%s GST detail changed' AND `email_template`.`template` = '<b>GST Detail Changed</b>\r\n<br/>\r\n<p>Previous Detail</p>\r\n <b>GST No</b> => %s\r\n <b>GST Status</b> => %s\r\n <b>GST Type</b> => %s\r\n <b>GST Cancelled Date</b> => %s\r\n<p>Updated Detail</p>\r\n <b>GST No</b> => %s\r\n <b>GST Status</b> => %s\r\n <b>GST Type</b> => %s\r\n <b>GST Cancelled Date</b> => %s', 'noreply@247around.com', '', 'kalyanit@gmail.com', '', '1', '', CURRENT_TIMESTAMP);
ALTER TABLE `service_centres` ADD `gst_cancelled_date` DATE NOT NULL AFTER `gst_status`;

--Abhay 02-08-2018
ALTER TABLE `vendor_partner_invoices` ADD `miscellaneous_charges` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `warehouse_storage_charges`;
ALTER TABLE `trigger_vendor_partner_invoices` ADD `miscellaneous_charges` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `warehouse_storage_charges`

--sachin 02-08-2018
CREATE TABLE `courier_tracking_details` 
( `id` INT(11) NOT NULL AUTO_INCREMENT , 
 `spare_id` INT(11) NOT NULL , 
 `awb_number` VARCHAR(256) NOT NULL ,
 `carrier_code` VARCHAR(256) NOT NULL , 
 `checkpoint_status` VARCHAR(256) NOT NULL , 
 `checkpoint_status_details` VARCHAR(512) NOT NULL , 
 `checkpoint_status_description` VARCHAR(512) NOT NULL , 
 `checkpoint_status_date` DATETIME NOT NULL , 
 `api_id` VARCHAR(512) NOT NULL ,
 `final_status` VARCHAR(64) NOT NULL,
 `checkpoint_item_node` VARCHAR(64) NULL, 
 `remarks` VARCHAR(1024) NOT NULL , 
 `create_date` DATETIME NOT NULL , 
 `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
 PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE courier_tracking_details AUTO_INCREMENT=10000;

ALTER TABLE `courier_tracking_details` ADD UNIQUE( `spare_id`, `awb_number`, `carrier_code`, `checkpoint_status`, `checkpoint_status_details`, `checkpoint_status_description`, `checkpoint_status_date`, `api_id`);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'courier_api_failed_mail', 'TrackingMore Courier Api Failed', 
'Dear Team,<br><br> TrackingMore Courier Api Failed.<br><br> <b>Response From API</b><br><br> %s',
 'noreply@247around.com', '', '', '', '1', '2016-06-17 00:00:00');

CREATE TABLE `courier_services` (
  `id` int(11) NOT NULL,
  `courier_name` varchar(512) NOT NULL,
  `courier_code` varchar(512) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `courier_services` (`id`, `courier_name`, `courier_code`, `create_date`, `update_date`) VALUES
(10000, 'DHL Express', 'dhl', '2018-08-03 00:00:00', '2018-08-03 10:36:25'),
(10001, 'DTDC', 'dtdc', '2018-08-03 00:00:00', '2018-08-03 10:38:29'),
(10002, 'GATI Courier', 'gati-kwe', '2018-08-03 00:00:00', '2018-08-03 10:38:29'),
(10003, ' Trackon Courier', 'trackon', '2018-08-03 00:00:00', '2018-08-03 10:39:32'),
(10004, 'Fedex', 'fedex', '2018-08-03 00:00:00', '2018-08-03 10:39:32'),
(10005, 'First Flight Couriers', 'firstflightme', '2018-08-03 00:00:00', '2018-08-03 10:40:15'),
(10006, 'Bluedart', 'bluedart', '2018-08-03 00:00:00', '2018-08-03 10:40:15'),
(10007, 'Maruti Courier', 'maruti-courier', '2018-08-03 00:00:00', '2018-08-03 10:40:57'),
(10008, ' Delhivery', 'delhivery', '2018-08-03 00:00:00', '2018-08-03 10:40:57'),
(10009, 'Safexpress', 'safexpress', '2018-08-03 00:00:00', '2018-08-03 10:41:50'),
(10010, 'Elegant Express Cargo', 'elegant', '2018-08-03 00:00:00', '2018-08-03 10:41:50'),
(10011, 'Rivigo', 'rivigo', '2018-08-03 00:00:00', '2018-08-03 10:42:20'),
(10012, 'Aramex', 'aramex', '2018-08-03 00:00:00', '2018-08-03 10:42:20'),
(10013, 'India Post', 'india-post', '2018-08-03 00:00:00', '2018-08-03 10:43:00'),
(10014, 'The Professional Couriers (TPC)', 'professional-couriers', '2018-08-03 00:00:00', '2018-08-03 10:43:00'),
(10015, 'Speed Post', 'speed-post', '2018-08-03 00:00:00', '2018-08-03 10:43:21'),
(10017, 'Other', 'other', '2018-08-03 00:00:00', '2018-08-03 10:45:32');


ALTER TABLE `courier_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courier_name` (`courier_name`,`courier_code`);


ALTER TABLE `courier_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10018;


--- Kalyani 31-07-2018
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES ('70', 'taxpro_api_fail', 'Taxpro GSP Api Fail', '<b>TAXPRO GSP API FAIL </b>\r\n<br/>\r\n<p>%s</p>', 'noreply@247around.com', '', '', '', '1', '', CURRENT_TIMESTAMP);


--Chhavi
ALTER TABLE `spare_parts_details` ADD `spare_cancelled_date` DATETIME  NULL AFTER `challan_approx_value`;
--- Kalyani 02-08-2018
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES ('71', 'gst_detail_change', '%s GST detail changed', '<b>GST Detail Changed</b>\r\n<br/>\r\n<p><b>Previous Detail</b></p>\r\n GST No => %s </br>\r\n GST Status => %s <br/>\r\n GST Type => %s </br>\r\n GST Cancelled Date => %s </br> \r\n<p>Updated Detail</p>\r\n GST No => %s </br>\r\n GST Status => %s </br>\r\n GST Type => %s </br>\r\n GST Cancelled Date => %s', '', '', '', '', '1', '', '0000-00-00') WHERE `email_template`.`id` = 71 AND `email_template`.`tag` = 'gst_detail_change' AND `email_template`.`subject` = '%s GST detail changed' AND `email_template`.`template` = '<b>GST Detail Changed</b>\r\n<br/>\r\n<p>Previous Detail</p>\r\n <b>GST No</b> => %s\r\n <b>GST Status</b> => %s\r\n <b>GST Type</b> => %s\r\n <b>GST Cancelled Date</b> => %s\r\n<p>Updated Detail</p>\r\n <b>GST No</b> => %s\r\n <b>GST Status</b> => %s\r\n <b>GST Type</b> => %s\r\n <b>GST Cancelled Date</b> => %s', 'noreply@247around.com', '', 'kalyanit@gmail.com', '', '1', '', CURRENT_TIMESTAMP);
ALTER TABLE `service_centres` ADD `gst_cancelled_date` DATE NOT NULL AFTER `gst_status`;

--Abhay 02-08-2018
ALTER TABLE `vendor_partner_invoices` ADD `miscellaneous_charges` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `warehouse_storage_charges`;
ALTER TABLE `trigger_vendor_partner_invoices` ADD `miscellaneous_charges` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `warehouse_storage_charges`

--Chhavi
CREATE TABLE `booking_tat` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `booking_id` varchar(128) NOT NULL,
  `spare_id` int(11) DEFAULT NULL,
  `is_upcountry` int(1) DEFAULT NULL,
  `leg_1` int(11) DEFAULT NULL,
  `leg_2` int(11) DEFAULT NULL,
  `leg_3` int(11) DEFAULT NULL,
  `leg_4` int(11) DEFAULT NULL,
  `is_leg_1_faulty_for_partner` int(1) DEFAULT NULL,
  `is_leg_2_faulty_for_partner` int(1) DEFAULT NULL,
  `is_leg_3_faulty_for_partner` int(1) DEFAULT NULL,
  `is_leg_4_faulty_for_partner` int(1) DEFAULT NULL,
  `is_leg_1_faulty_for_vendor` int(1) DEFAULT NULL,
  `is_leg_2_faulty_for_vendor` int(1) DEFAULT NULL,
  `is_leg_3_faulty_for_vendor` int(1) DEFAULT NULL,
  `is_leg_4_faulty_for_vendor` int(1) DEFAULT NULL,
  `applicable_on_partner` int(1) NOT NULL,
  `applicable_on_sf` int(1) NOT NULL,
  `create_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_tat`
--
ALTER TABLE `booking_tat`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_tat`
--
ALTER TABLE `booking_tat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;


CREATE TABLE `tat_defactive_booking_criteria` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(11) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `without_repair_upcountry` int(11) DEFAULT NULL,
  `without_repair_non_upcountry` int(11) DEFAULT NULL,
  `with_repair_upcountry_leg_1` int(11) DEFAULT NULL,
  `with_repair_upcountry_leg_2` int(11) DEFAULT NULL,
  `with_repair_upcountry_leg_3` int(11) DEFAULT NULL,
  `with_repair_non_upcountry_leg_1` int(11) DEFAULT NULL,
  `with_repair_non_upcountry_leg_2` int(11) DEFAULT NULL,
  `with_repair_non_upcountry_leg_3` int(11) DEFAULT NULL,
  `is_active` int(1) DEFAULT '1',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tat_defactive_booking_criteria`
--

INSERT INTO `tat_defactive_booking_criteria` (`id`, `entity_type`, `entity_id`, `without_repair_upcountry`, `without_repair_non_upcountry`, `with_repair_upcountry_leg_1`, `with_repair_upcountry_leg_2`, `with_repair_upcountry_leg_3`, `with_repair_non_upcountry_leg_1`, `with_repair_non_upcountry_leg_2`, `with_repair_non_upcountry_leg_3`, `is_active`, `create_date`) VALUES
(2, 'Vendor', NULL, 3, 2, 3, 3, 2, 2, 2, 2, 1, '2018-08-04 05:36:39'),
(4, 'Partner', NULL, 3, 2, 3, 3, 2, 2, 2, 2, 1, '2018-08-06 05:50:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tat_defactive_booking_criteria`
--
ALTER TABLE `tat_defactive_booking_criteria`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tat_defactive_booking_criteria`
--
ALTER TABLE `tat_defactive_booking_criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;COMMIT;

--- Kalyani 02-08-2018
ALTER TABLE `service_centres` ADD `gst_cancelled_date` DATE NOT NULL AFTER `gst_status`;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES ('null', 'gst_detail_change', '%s GST detail changed', '<b>GST Detail Changed</b>\r\n<br/>\r\n<p><b>Previous Detail</b></p>\r\n GST No => %s </br>\r\n GST Status => %s <br/>\r\n GST Type => %s </br>\r\n GST Cancelled Date => %s </br> \r\n<p>Updated Detail</p>\r\n GST No => %s </br>\r\n GST Status => %s </br>\r\n GST Type => %s </br>\r\n GST Cancelled Date => %s', '', '', '', '', '1', '', '0000-00-00');


--Abhay 07 Aug
ALTER TABLE `spare_parts_details` ADD `inventory_invoice_on_booking` INT(1) NOT NULL DEFAULT '0' AFTER `shipped_inventory_id`;

-- Kalyani 23-07-2018

ALTER TABLE `courier_details` ADD COLUMN `notification_email`  VARCHAR(255) AFTER `contact_person_id`;
ALTER TABLE `courier_details` ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `partner_invoice_id`;

--Kalyani 17-08-2018
ALTER TABLE `log_partner_table` ADD `entity_type` VARCHAR(65) NOT NULL DEFAULT 'partner' AFTER `id`;


---Chhavi
ALTER TABLE `tat_defactive_booking_criteria` ADD `with_repair_upcountry_leg_4` INT(11) NULL DEFAULT NULL AFTER `with_repair_upcountry_leg_3`;
ALTER TABLE `tat_defactive_booking_criteria` ADD `with_repair_non_upcountry_leg_4` INT(11) NULL DEFAULT NULL AFTER `with_repair_non_upcountry_leg_3`;
UPDATE `tat_defactive_booking_criteria` SET `with_repair_upcountry_leg_4` = '2' WHERE `tat_defactive_booking_criteria`.`id` = 2;
UPDATE `tat_defactive_booking_criteria` SET `with_repair_upcountry_leg_4` = '2' WHERE `tat_defactive_booking_criteria`.`id` = 4;
UPDATE `tat_defactive_booking_criteria` SET `with_repair_non_upcountry_leg_4` = '2' WHERE `tat_defactive_booking_criteria`.`id` = 2;
UPDATE `tat_defactive_booking_criteria` SET `with_repair_non_upcountry_leg_4` = '2' WHERE `tat_defactive_booking_criteria`.`id` = 4;


--Chhavi
INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES (NULL, 'Dealer Name', 'dealer_details.dealer_name AS \'Dealer Name\'', '0', '247034', '1', '38');
INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES (NULL, 'Dealer Phone', 'dealer_details.dealer_phone_number_1 AS \'Dealer Name\'', '0', '247034', '1','39');
INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES (NULL, 'Dealer Phone', 'dealer_details.dealer_phone_number_1 AS \'Dealer Name\'', '0', '247034', '1','39');INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES (NULL, 'Category', 'ud.appliance_category AS \'Appliance Category\'', '1', '', '1', '8');
INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES (NULL, 'Capacity', 'ud.appliance_capacity AS \'Appliance Capacity\'', '1', '', '1', '8');

-- Abhay 22 Aug
ALTER TABLE `invoice_details` CHANGE `toal_amount` `total_amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00';