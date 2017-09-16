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

As you are aware, the introduction of Goods and Services Tax (“GST”) will be implemented on 1st July 2017.

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
