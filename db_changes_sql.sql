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