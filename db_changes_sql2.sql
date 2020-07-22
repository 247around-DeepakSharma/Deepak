--Abhyay 20/5/2019
ALTER TABLE `service_center_booking_action` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0' AFTER `sf_purchase_date`;

ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';


--Abhay 25/5/19
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES (NULL, '247001', 'Pending', 'Warehouse acknowledged to receive MSL', 'Booking In Progress', 'Warehouse acknowledged to receive MSL', 'Warehouse', 'Send Spare to SF', '0000-00-00 00:00:00');
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES (NULL, '247001', 'Pending', 'Partner shipped spare to Warehouse', 'Booking In Progress', 'Partner shipped spare to Warehouse', 'Warehouse', 'Acknowledge Spare', '0000-00-00 00:00:00');


ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';

ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';


----Abhishek -----

UPDATE `email_template` SET `template` = 'SF has marked wrong call area, Please reasign correct SF for booking ID %s, <br/>city is %s,<br/> pincode is %s' WHERE `email_template`.`id` = 124;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'wrong_pincode_enter','Customer Enter Incorrect Pincode %s', 'SF has marked wrong call area, Please reasign correct SF for booking ID %s, <br/>city is %s, <br/> Wrong pincode is %s,<br/>Correct Pincode  is %s', 'noreply@247around.com', '', '', '', '1', '2018-10-30 10:48:05');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer has  Wrong Pincode ', 'vendor', '1');
INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Not Servicable in Your Area', 'vendor', '1');
 
---ABhishek ----
ALTER TABLE `spare_parts_details` ADD `defective_part_rejected_by_partner` TINYINT(4) NOT NULL DEFAULT '0' AFTER `part_requested_on_approval`;
 
--Kajal 23/5/2019 starting --
insert into `partner_permission`(partner_id,permission_type,is_on,create_date,update_date) 
values(247001, 'partner_on_state_appliance',0,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);

insert into agent_filters(entity_type,entity_id,contact_person_id,agent_id,state) 
SELECT '247around',id, 0,account_manager_id,state FROM `partners` where account_manager_id is not NULL;
--Kajal 23/5/2019 ending --
---ABhishek ----
ALTER TABLE `spare_parts_details` ADD `defective_part_rejected_by_partner` TINYINT(4) NOT NULL DEFAULT '0' AFTER `part_requested_on_approval`;

--Kalyani 25-05-2019---

CREATE TABLE `entity_identity_proof` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `identity_proof_type` varchar(255) NOT NULL,
  `identity_proof_number` varchar(255) NOT NULL,
  `identity_proof_pic` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `entity_identity_proof`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `entity_identity_proof`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

--Kajal 25-05-2019---

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) 
VALUES ('247Around', 'File Type List', NULL, 'employee/booking/show_file_type_list', '1', '190', 'admin,developer', 'main_nav', '1', CURRENT_TIMESTAMP);
-- Abhishek ----
UPDATE `email_template` SET `template` = 'Dear %s,<br><br> <b> %s </b> Service Franchise is Permanently <b> %s </b> now by %s.<br><br> Thanks<br> 247Around Team' WHERE `email_template`.`id` = 19;


UPDATE `email_template` SET `template` = 'Dear %s,<br><br> <b> %s </b> Service Franchise is Temporarily <b> %s </b> now by %s. <br><br> Thanks<br> 247Around Team' WHERE `email_template`.`id` = 18;

 
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'send_mail_for_insert_update_applaince_by_sf', 'Update Appliance By SF', 'Hi ,<br> Charges Not add fro below category <br> Brand -%s , Category - %s <br> Capacity - %s <br> Service Category - %s . Please add the charges . <br> Thanks<br> 247Around Team', 'booking@247around.com', 'abhisheka@247around.com', 'abhaya@247around.com', 'abhisheka@247around.com', '1', '2016-09-26 18:30:00');

 
 

-- Ankit 25-May-2019
INSERT INTO `file_type` (`id`, `file_type`, `max_allowed_size`, `allowed_type`, `is_active`, `create_date`) VALUES (NULL, 'SF Purchase Invoice', NULL, NULL, '1', CURRENT_TIMESTAMP);
ALTER TABLE `service_centre_charges` ADD COLUMN invoice_pod tinyint(1) NOT NULL DEFAULT 0 AFTER pod;
ALTER TABLE service_center_booking_action ADD COLUMN sf_purchase_invoice varchar(512) NULL DEFAULT NULL AFTER sf_purchase_date;
--- Ankit 27-05-2019
ALTER TABLE booking_unit_details ADD COLUMN invoice_pod tinyint(1) NOT NULL DEFAULT 0 AFTER pod;

--Kajal 27-05-2019---

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'new_partner_am_notification', 'New AM added for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'update_partner_am_notification', 'AM updated for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP);

 
 --Kajal 27-05-2019---

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'new_partner_am_notification', 'New AM added for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'update_partner_am_notification', 'AM updated for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP); 

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'send_mail_for_insert_update_applaince_by_sf', 'Update Appliance By SF', 'Hi ,<br> Charges Not add fro below category <br> Brand -%s , Category - %s <br> Capacity - %s <br> Service Category - %s . Please add the charges . <br> Thanks<br> 247Around Team', 'booking@247around.com', 'gurpreets@247around.com', 'abhaya@247around.com', 'abhisheka@247around.com', '1', '2016-09-26 18:30:00');

 --Kajal 27-05-2019---

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'new_partner_am_notification', 'New AM added for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'update_partner_am_notification', 'AM updated for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP); 
 
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'send_mail_for_insert_update_applaince_by_sf', 'Update Appliance By SF', 'Hi ,<br> Charges Not add fro below category <br> Brand -%s , Category - %s <br> Capacity - %s <br> Service Category - %s . Please add the charges . <br> Thanks<br> 247Around Team', 'booking@247around.com', 'gurpreets@247around.com', 'abhaya@247around.com', 'abhisheka@247around.com', '1', '2016-09-26 18:30:00');
 
 UPDATE `email_template` SET `template` = 'Hi ,<br> Charges Not add for below category <br> Brand -%s , <br>Category - %s <br> Capacity - %s <br> Service Category - %s  <br> . Please add the charges . <br> Thanks<br> 247Around Team' WHERE `email_template`.`id` = 158;
----Abhishek ----
--
-- Table structure for table `entity_gst_details`
--

CREATE TABLE `entity_gst_details` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(15) NOT NULL,
  `gst_number` varchar(16) NOT NULL,
  `gst_file` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `entity_gst_details`
--
ALTER TABLE `entity_gst_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `entity_gst_details`
--
ALTER TABLE `entity_gst_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
 

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-----  Abhishek End ------
 ---Abhishek ---

 
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES (NULL, 'spare_not_transfer_from_wh_to_wh', 'Spare for booking ID - %s not transferred', 'Spare not transferred due to no available of stock ,Booking ID is %s, <br/>Inventory ID is %s . <br>\r\n', 'noreply@247around.com', '247around_dev@247around.com', 'abhisheka@247around.com', 'abhaya@247around.com', '1', '', '2018-10-30 10:48:05');
 
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES (NULL, 'spare_not_transfer_from_wh_to_wh', 'Spare for booking ID - %s not transferred', 'Spare not transferred due to no available of stock ,Booking ID is %s, <br/>Inventory ID is %s . <br>\r\n', 'noreply@247around.com', '247around_dev@247around.com', 'abhisheka@247around.com', 'abhaya@247around.com', '1', '', '2018-10-30 10:48:05');
--Chhavi
CREATE TABLE `fake_cancellation_missed_call_log` (
  `id` int(11) NOT NULL,
  `callSid` varchar(40) NOT NULL,
  `from_number` int(11) NOT NULL,
  `to_number` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fake_cancellation_missed_call_log`
--
ALTER TABLE `fake_cancellation_missed_call_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fake_cancellation_missed_call_log`
--
ALTER TABLE `fake_cancellation_missed_call_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
 

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, '247Around', 'Download SF Penalty Summary', '', 'employee/vendor/penalty_summary', '2', '36', 'admin,developer,inventory_manager,regionalmanager', 'main_nav', '1', CURRENT_TIMESTAMP);--Chhavi 12-June-2019
CREATE TABLE `booking_request_type_state_change` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(256) NOT NULL,
  `old_request_type` varchar(256) NOT NULL,
  `new_request_type` varchar(256) NOT NULL,
  `old_price_tag` text NOT NULL,
  `new_price_tag` text NOT NULL,
  `entity_type` varchar(164) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_request_type_state_change`
--
ALTER TABLE `booking_request_type_state_change`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_request_type_state_change`
--
ALTER TABLE `booking_request_type_state_change`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

UPDATE `email_template` SET `template` = 'Spare not transferred due to no available of stock ,Details are - %s,', `to` = 'abhisheka@247around.com', `bcc` = 'abhisheka@247around.com' WHERE `email_template`.`id` = 159;
--Kalyani 07-June-2019
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, '247Around', 'Download SF Penalty Summary', '', 'employee/vendor/penalty_summary', '2', '36', 'admin,developer,inventory_manager,regionalmanager', 'main_nav', '1', CURRENT_TIMESTAMP);
ALTER TABLE `invoice_details` ADD `from_gst_number` VARCHAR(25) NULL DEFAULT NULL AFTER `total_amount`, ADD `to_gst_number` VARCHAR(25) NULL DEFAULT NULL AFTER `from_gst_number`;



ALTER TABLE `vendor_partner_invoices` CHANGE `invoice_file_main` `invoice_file_main` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

-- Prity Bhardwaj 26-June-2019
CREATE TABLE `service_center_brand_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_center_id` int(11) NOT NULL,
  `brand_id` int(11) NULL DEFAULT NULL,
  `brand_name` varchar(25) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `isActive` int(1) DEFAULT 1,
  PRIMARY KEY (`id`),  
  KEY fk_scbm_service_center_id_service_centers_id (service_center_id),
  KEY fk_scbm_brand_id_appliance_brands_id (brand_id),
  CONSTRAINT fk_scbm_service_center_id_service_centers_id FOREIGN KEY (service_center_id) REFERENCES service_centres (id),
  CONSTRAINT fk_scbm_brand_id_appliance_brands_id FOREIGN KEY (brand_id) REFERENCES appliance_brands (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--- Gorakh 28-06-2019 
INSERT INTO `email_template` (`tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES
('spare_part_shipment_pending', 'Spare Parts Shipment Pending', 'Please find the attachment', NULL, 'noreply@247around.com', '', 'gorakhn@247around.com,gorakhn@247around.com,gorakhn@247around.com', '', '1', '2019-06-28 05:52:56');
--Kajal 26/06/2019 starting --
UPDATE `header_navigation` SET `title` = 'Inventory Master List' WHERE `header_navigation`.`title`='Spare Part List';

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('Partner', 'Upload Inventory Master File', NULL, 'partner/inventory/upload_inventory_details_file', 2, '148', 'Primary Contact,Area Sales Manager,Booking Manager,Owner', 'main_nav', 1, CURRENT_TIMESTAMP),
('Partner', 'Upload Serviceable BOM File', NULL, 'partner/inventory/upload_bom_file', 2, '148', 'Primary Contact,Area Sales Manager,Booking Manager,Owner', 'main_nav', 1, CURRENT_TIMESTAMP),
('Partner', 'Upload Alternate Parts', NULL, 'partner/inventory/upload_alternate_spare_parts_file', 2, '148', 'Primary Contact,Area Sales Manager,Booking Manager,Owner', 'main_nav', 1, CURRENT_TIMESTAMP);

ALTER TABLE `file_uploads` ADD `agent_type` VARCHAR(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'employee' AFTER `agent_id`;
--Kajal 26/06/2019 ending --

--Abhishek----
ALTER TABLE `appliance_updated_by_sf` CHANGE `capacity` `capacity` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;


--Abhay 01/07/2019

ALTER TABLE `spare_parts_details` ADD `partner_warehouse_packaging_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `vendor_courier_invoice_id`;
--Abhay /07/2019

ALTER TABLE `entity_gst_details` ADD `city` VARCHAR(64) NOT NULL AFTER `gst_file`, ADD `pincode` INT(11) NOT NULL AFTER `city`, ADD `address` VARCHAR(256) NOT NULL AFTER `pincode`;

ALTER TABLE `inventory_ledger` ADD `micro_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `invoice_id`;

--Kalyani 04-July-2019
ALTER TABLE `engineer_booking_action` ADD `model_number` VARCHAR(255) NULL DEFAULT NULL AFTER `cancellation_reason`;
ALTER TABLE `engineer_booking_action` ADD `sf_purchase_date` DATETIME NULL DEFAULT NULL AFTER `model_number`;
ALTER TABLE `engineer_booking_action` ADD `closing_remark` VARCHAR(500) NULL DEFAULT NULL COMMENT 'engineer remark on booking completion' AFTER `sf_purchase_date`;
ALTER TABLE `engineer_booking_action` ADD `symptom` INT NULL DEFAULT NULL AFTER `closing_remark`, ADD `defect` INT NULL DEFAULT NULL AFTER `symptom`, ADD `solution` INT NULL DEFAULT NULL AFTER `defect`;
ALTER TABLE `engineer_booking_action` ADD `service_charge` INT NOT NULL DEFAULT '0' AFTER `cancellation_reason`;
ALTER TABLE `engineer_booking_action` ADD `additional_service_charge` INT NOT NULL DEFAULT '0' AFTER `service_charge`;
ALTER TABLE `engineer_booking_action` ADD `parts_cost` INT NOT NULL DEFAULT '0' AFTER `additional_service_charge`;
ALTER TABLE `engineer_booking_action` ADD `amount_paid` INT NULL AFTER `cancellation_reason`;

ALTER TABLE employee_relation ADD COLUMN individual_service_centres_id text NULL DEFAULT NULL AFTER service_centres_id;

--Kajal 12/07/2019 --
ALTER TABLE `spare_parts_details` ADD `cancellation_reason` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `shipped_quantity`;

-- Prity 12-July-2019
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `private_key` varchar(200) NOT NULL COMMENT 'here we store trimed, uppercased, filtered (remove all special characters instead of hyphen(-) and dot(.)) value of name for unique constraints',
  `name` varchar(200) NOT NULL COMMENT 'this is the name of the category, this value will be replaced each time user changes the appearance of name.(eg: double spaces))',
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `last_updated_by` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_private_key` (`private_key`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `capacity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `private_key` varchar(200) NOT NULL COMMENT 'here we store trimed, uppercased, filtered (remove all special characters instead of hyphen(-) and dot(.)) value of name for unique constraints',
  `name` varchar(200) NOT NULL COMMENT 'this is the name of the category, this value will be replaced each time user changes the appearance of name.(eg: double spaces))',
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `last_updated_by` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_private_key` (`private_key`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-----------------------------    Script for Data Insertion     ------------------------------------------------------
-------------------------------- CATEGORY --------------------------------------------------------------------------
-- select distinct concat("('",(REPLACE(UPPER(category), " ", "")),"','",category,"',1,'1'),") as category from service_category_mapping where category <> "";
-- INSERT IGNORE category (private_key,name,active,last_updated_by) values 
-------------------------------- CAPACITY ---------------------------------------------------------------------------
-- select distinct concat("('",(REPLACE(UPPER(capacity), " ", "")),"','",capacity,"',1,'1'),") from service_category_mapping where capacity <> "";
-- INSERT IGNORE INTO capacity (private_key, name, active, last_updated_by) values 

--Kajal 13/07/2019 --
ALTER TABLE `spare_parts_details` CHANGE `cancellation_reason` `spare_cancellation_reason` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

--Ankit 15/07/2019 --
ALTER TABLE service_center_brand_mapping ADD COLUMN service_id int(11) NOT NULL AFTER service_center_id;

-- Prity 15-July-2019
-- USE EITHER PART 1 OR PART 2 QUERIES
-- PART 1 START HERE -----------------------------------------------
ALTER TABLE service_category_mapping add column category_id int(11) NOT NULL after category;
ALTER TABLE service_category_mapping add column capacity_id int(11) NULL DEFAULT NULL after capacity;

UPDATE service_category_mapping INNER JOIN category 
ON REPLACE(UPPER(service_category_mapping.category), " ", "") = category.private_key
SET service_category_mapping.category_id = category.id;

UPDATE service_category_mapping INNER JOIN capacity 
ON REPLACE(UPPER(service_category_mapping.capacity), " ", "") = capacity.private_key
SET service_category_mapping.capacity_id = capacity.id;

-- ---------------------------------------------------------------------------------------------
-- Add data in Category/Capacity Table if some mapping is missing from above queries.
-- ---------------------------------------------------------------------------------------------
ALTER TABLE service_category_mapping drop key uniq;
ALTER TABLE service_category_mapping drop column category;
ALTER TABLE service_category_mapping drop column capacity;
-- PART 1 ENDS HERE -----------------------------------------------
-- PART 2 STARTS HERE -------------------------------------------------
ALTER TABLE service_category_mapping change column category category_id int(11) NOT NULL;
ALTER TABLE service_category_mapping change column capacity capacity_id int(11) NULL DEFAULT NULL;

UPDATE service_category_mapping INNER JOIN category 
ON REPLACE(UPPER(service_category_mapping.category), " ", "") = category.private_key
SET service_category_mapping.category = category.id;

UPDATE service_category_mapping INNER JOIN capacity 
ON REPLACE(UPPER(service_category_mapping.capacity), " ", "") = capacity.private_key
SET service_category_mapping.capacity = capacity.id;

-- ---------------------------------------------------------------------------------------------
-- Add data in Category/Capacity Table if some mapping is missing from above queries.
-- ---------------------------------------------------------------------------------------------

ALTER TABLE service_category_mapping change column category category_id int(11) NOT NULL;
ALTER TABLE service_category_mapping change column capacity capacity_id int(11) NULL DEFAULT NULL;
UPDATE service_category_mapping set capacity_id = NULL WHERE capacity_id = 0;
ALTER TABLE service_category_mapping drop key uniq;
-- PART 2 ENDS HERE -------------------------------------------------
ALTER TABLE service_category_mapping ADD CONSTRAINT `fk_scm_category` FOREIGN KEY(`category_id`) REFERENCES category(id);
ALTER TABLE service_category_mapping ADD CONSTRAINT `fk_scm_capacity` FOREIGN KEY(`capacity_id`) REFERENCES capacity(id);
ALTER TABLE service_category_mapping ADD CONSTRAINT `uk_scm_service_category_capacity` UNIQUE (service_id, category_id, capacity_id);
-----------------------------------------------------------------------------------------------
ALTER TABLE `engineer_booking_action` ADD `booking_status` INT NOT NULL AFTER `solution`;
-- Prity 16-July-2019
ALTER TABLE warranty_plan_state_mapping ADD CONSTRAINT uk_state_plan UNIQUE (plan_id, state_code);
-- Ankit 17-July-2019
ALTER TABLE service_center_brand_mapping ADD COLUMN created_by varchar(150) NOT NULL AFTER create_date;
-- Prity 18-July-2019
CREATE TABLE `partner_appliance_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `appliance_configuration_id` int(11) NULL DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `isActive` int(1) DEFAULT 1,
  PRIMARY KEY (`id`),  
  UNIQUE KEY `uk_partner_appliance_configuration` (partner_id, appliance_configuration_id),
  KEY fk_pam_partner_id_partners_id (partner_id),
  KEY fk_pam_aci_service_category_mapping_id (appliance_configuration_id),
  CONSTRAINT fk_pam_partner_id_partners_id FOREIGN KEY (partner_id) REFERENCES partners (id),
  CONSTRAINT fk_pam_aci_service_category_mapping_id FOREIGN KEY (appliance_configuration_id) REFERENCES service_category_mapping (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Kajal 19-July-2019 --
ALTER TABLE `inventory_model_mapping` ADD `bom_main_part` INT(1) NOT NULL DEFAULT '1' COMMENT '1 - Main Part, 0 - Alternate Part' AFTER `max_quantity`;

--Kalyani 19-July-2019
INSERT INTO `sms_template` (`tag`, `template`, `comments`, `active`, `is_exception_for_length`, `create_date`) VALUES ('engineer_login_sms_template', 'Hi %S\r\n\r\nYour Engineer Login is created.\r\nUser Id - %s\r\nPassword - %s\r\n\r\n247around', NULL, '1', '0', CURRENT_TIMESTAMP);

--Kalyani 24-July-2019
ALTER TABLE `engineer_details` ADD `varified` BOOLEAN NOT NULL DEFAULT FALSE AFTER `alternate_phone`;

--Gorakh - 24 -july - 2019
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Download serviceable BOM', NULL, 'employee/inventory/download_serviceable_bom', 2, '89', 'accountmanager,admin,closure,developer,inventory_manager', 'main_nav', 1, '2018-06-05 05:27:42');

ALTER TABLE warranty_plans add column service_id int(11) NULL DEFAULT NULL after partner_id;
ALTER TABLE warranty_plans ADD CONSTRAINT `fk_warranty_plans_services` FOREIGN KEY(`service_id`) REFERENCES services(id);
ALTER TABLE warranty_plans add column `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE warranty_plans add column `created_by` varchar(25) NOT NULL;
ALTER TABLE warranty_plan_state_mapping add column `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE warranty_plan_state_mapping add column `created_by` varchar(25) NOT NULL;
ALTER TABLE warranty_plan_part_type_mapping add column `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE warranty_plan_part_type_mapping add column `created_by` varchar(25) NOT NULL;
ALTER TABLE warranty_plan_model_mapping add column `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE warranty_plan_model_mapping add column `created_by` varchar(25) NOT NULL;
ALTER TABLE warranty_plan_part_type_mapping ADD CONSTRAINT uk_part_plan UNIQUE (plan_id, part_type_id);
ALTER TABLE warranty_plan_model_mapping ADD CONSTRAINT uk_model_plan UNIQUE (plan_id, model_id);

--Kalyani 01-08-2019
ALTER TABLE `engineer_booking_action` ADD `cancellation_remark` VARCHAR(255) NOT NULL AFTER `cancellation_reason`;
--Kalyani 03-08-2019
ALTER TABLE `ewaybill_details` ADD `vehicle_number` VARCHAR(255) NOT NULL AFTER `ewaybill_generated_date`;
ALTER TABLE `ewaybill_details` ADD `invoice_id` VARCHAR(255) NOT NULL AFTER `vehicle_number`;

-- Prity 02-08-2019
-- Query to add data in partner_appliance_mapping Table
SELECT DISTINCT
    concat("INSERT IGNORE INTO partner_appliance_mapping (partner_id, appliance_configuration_id) VALUES (",partner_appliance_details.partner_id,",",service_category_mapping.id,");") as query,
    partner_appliance_details.partner_id,
    partner_appliance_details.service_id,
    partner_appliance_details.category,
    category.id as category_id,
    partner_appliance_details.capacity,
    capacity.id as capacity_id,
    service_category_mapping.id as appliance_configuration_id
FROM
    partner_appliance_details
        LEFT JOIN
    category ON (REPLACE(UPPER(partner_appliance_details.category),' ','') = category.private_key)
        LEFT JOIN
    capacity ON (REPLACE(UPPER(partner_appliance_details.capacity),' ','') = capacity.private_key)
        LEFT JOIN 
    service_category_mapping ON (partner_appliance_details.service_id = service_category_mapping.service_id AND category.id = service_category_mapping.category_id AND capacity.id = service_category_mapping.capacity_id)    
;

--Kajal 02/08/2019 Start --
UPDATE `spare_parts_details` JOIN `booking_cancellation_reasons` ON spare_parts_details.spare_cancellation_reason=booking_cancellation_reasons.reason SET spare_parts_details.spare_cancellation_reason=booking_cancellation_reasons.id WHERE spare_parts_details.spare_cancellation_reason<>'' ;

ALTER TABLE `spare_parts_details` CHANGE `spare_cancellation_reason` `spare_cancellation_reason` INT(11) NULL DEFAULT NULL;

--Kajal 02/08/2019 End  --

---Ankit 05/08/2019 -- partners default contact person logins.
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','1','Snapdeal','1002706','247around','247around_18712',md5(18712),'1','18712',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','3','Paytm','1002706','247around','247around_22435',md5(22435),'1','22435',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247001','247around','1002706','247around','247around_34848',md5(34848),'1','34848',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247002','247around-Android','1002706','247around','247around_37587',md5(37587),'1','37587',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247003','247around-CallCenter','1002706','247around','247around_16928',md5(16928),'1','16928',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247010','Wybor','1002706','247around','247around_13466',md5(13466),'1','13466',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247011','Ray','1002706','247around','247around_74119',md5(74119),'1','74119',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247012','Zopper','1002706','247around','247around_87887',md5(87887),'1','87887',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247013','Nacson (OLD)','1002706','247around','247around_26543',md5(26543),'1','26543',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247014','Bosch&Delon','1002706','247around','247around_57580',md5(57580),'1','57580',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247016','Trigur','1002706','247around','247around_44616',md5(44616),'1','44616',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247017','Daewoo','1002706','247around','247around_23051',md5(23051),'1','23051',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247018','Murphy','1002706','247around','247around_20403',md5(20403),'1','20403',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247019','ICON','1002706','247around','247around_47468',md5(47468),'1','47468',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247020','KRISONS','1002706','247around','247around_79580',md5(79580),'1','79580',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247021','Santosh','1002706','247around','247around_22891',md5(22891),'1','22891',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247023','New Zone Computer Trade','1002706','247around','247around_96005',md5(96005),'1','96005',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247024','Amazon','1002706','247around','247around_58158',md5(58158),'1','58158',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247025','QWIKCILVER','1002706','247around','247around_30308',md5(30308),'1','30308',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247026','Overcart','1002706','247around','247around_12848',md5(12848),'1','12848',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247027','Paytm Mall','1002706','247around','247around_81344',md5(81344),'1','81344',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247029','Cloudtail-LA','1002706','247around','247around_66560',md5(66560),'1','66560',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247030','Jeeves','1002706','247around','247around_19885',md5(19885),'1','19885',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247032','Housejoy','1002706','247around','247around_86949',md5(86949),'1','86949',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247033','NU STAR','1002706','247around','247around_87337',md5(87337),'1','87337',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247034','Akai','1002706','247around','247around_49547',md5(49547),'1','49547',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247035','Dektron','1002706','247around','247around_76415',md5(76415),'1','76415',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247036','Maser','1002706','247around','247around_47339',md5(47339),'1','47339',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247037','Exotel','1002706','247around','247around_93277',md5(93277),'1','93277',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247038','Aquagrand Plus','1002706','247around','247around_64643',md5(64643),'1','64643',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247039','ADI MEDIA','1002706','247around','247around_42184',md5(42184),'1','42184',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247040','Greendust','1002706','247around','247around_53337',md5(53337),'1','53337',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247041','Videotex','1002706','247around','247around_59885',md5(59885),'1','59885',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247042','Panwood','1002706','247around','247around_66550',md5(66550),'1','66550',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247043','KKY','1002706','247around','247around_10934',md5(10934),'1','10934',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247044','Idfy','1002706','247around','247around_46097',md5(46097),'1','46097',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247045','Starc','1002706','247around','247around_30510',md5(30510),'1','30510',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247046','Ashok Sehgal and Associates','1002706','247around','247around_62205',md5(62205),'1','62205',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247047','MSG91','1002706','247around','247around_64086',md5(64086),'1','64086',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247048','Samy','1002706','247around','247around_43271',md5(43271),'1','43271',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247049','Feltron','1002706','247around','247around_34754',md5(34754),'1','34754',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247050','Demo_Test','1002706','247around','247around_56833',md5(56833),'1','56833',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247051','Adsun Impex','1002706','247around','247around_18115',md5(18115),'1','18115',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247052','Consistent','1002706','247around','247around_21766',md5(21766),'1','21766',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247053','ExtendedWarranty','1002706','247around','247around_10384',md5(10384),'1','10384',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247054','CorporateWarranty','1002706','247around','247around_53541',md5(53541),'1','53541',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247055','Weston','1002706','247around','247around_39535',md5(39535),'1','39535',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247056','QC-LA','1002706','247around','247around_65701',md5(65701),'1','65701',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247057','QC-TV','1002706','247around','247around_48785',md5(48785),'1','48785',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247058','QC-MD','1002706','247around','247around_79427',md5(79427),'1','79427',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247059','Cloudtail-MD','1002706','247around','247around_70992',md5(70992),'1','70992',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247060','Cloudtail-TV','1002706','247around','247around_20944',md5(20944),'1','20944',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247061','WS Retail','1002706','247around','247around_56394',md5(56394),'1','56394',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247064','Salora','1002706','247around','247around_81849',md5(81849),'1','81849',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247065','Indytech','1002706','247around','247around_31813',md5(31813),'1','31813',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247066','QFX (Old)','1002706','247around','247around_87257',md5(87257),'1','87257',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247068','Detel','1002706','247around','247around_89569',md5(89569),'1','89569',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247069','Golden Prime','1002706','247around','247around_19564',md5(19564),'1','19564',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247070','Markson','1002706','247around','247around_29016',md5(29016),'1','29016',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247071','Willett','1002706','247around','247around_53991',md5(53991),'1','53991',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247072','Zintex','1002706','247around','247around_63165',md5(63165),'1','63165',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247073','T-Series','1002706','247around','247around_34758',md5(34758),'1','34758',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247074','FK-Liquidation','1002706','247around','247around_60281',md5(60281),'1','60281',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247075','Noritec','1002706','247around','247around_95522',md5(95522),'1','95522',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247076','VIBGYORNXT','1002706','247around','247around_63742',md5(63742),'1','63742',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247077','Flipkart','1002706','247around','247around_43650',md5(43650),'1','43650',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247078','Worldtech','1002706','247around','247around_18667',md5(18667),'1','18667',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247079','Activa','1002706','247around','247around_28184',md5(28184),'1','28184',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247080','LEMON','1002706','247around','247around_36741',md5(36741),'1','36741',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247081','Delhivery','1002706','247around','247around_35318',md5(35318),'1','35318',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247082','FedEx','1002706','247around','247around_69946',md5(69946),'1','69946',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247083','TGL','1002706','247around','247around_91301',md5(91301),'1','91301',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247084','VIEWEX','1002706','247around','247around_63272',md5(63272),'1','63272',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247085','OSCAR','1002706','247around','247around_70658',md5(70658),'1','70658',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247086','BOAT','1002706','247around','247around_17384',md5(17384),'1','17384',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247087','Champion','1002706','247around','247around_81137',md5(81137),'1','81137',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247088','Welltek','1002706','247around','247around_76179',md5(76179),'1','76179',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247089','FOXSKY','1002706','247around','247around_75994',md5(75994),'1','75994',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247090','BIGTRON','1002706','247around','247around_35465',md5(35465),'1','35465',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247091','Gati Courier','1002706','247around','247around_58038',md5(58038),'1','58038',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247092','JVC','1002706','247around','247around_46975',md5(46975),'1','46975',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247093','Texla','1002706','247around','247around_68733',md5(68733),'1','68733',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247094','COSCO','1002706','247around','247around_13666',md5(13666),'1','13666',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247095','Hybon','1002706','247around','247around_23883',md5(23883),'1','23883',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247096','ENDROID','1002706','247around','247around_34914',md5(34914),'1','34914',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247097','BTL','1002706','247around','247around_85702',md5(85702),'1','85702',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247098','SIGNORACARE','1002706','247around','247around_53681',md5(53681),'1','53681',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247099','HIGHtron','1002706','247around','247around_69052',md5(69052),'1','69052',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247100','FAME','1002706','247around','247around_56979',md5(56979),'1','56979',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247101','Cloudtail Liquidation','1002706','247around','247around_38296',md5(38296),'1','38296',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247102','VEWTRON','1002706','247around','247around_68701',md5(68701),'1','68701',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247103','VSOONI','1002706','247around','247around_45558',md5(45558),'1','45558',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247104','GOELA','1002706','247around','247around_82889',md5(82889),'1','82889',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247105','DTDC Courier (Rajouri)','1002706','247around','247around_22431',md5(22431),'1','22431',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247106','BMS Lifestyle','1002706','247around','247around_31308',md5(31308),'1','31308',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247107','TELVICA','1002706','247around','247around_85758',md5(85758),'1','85758',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247108','BELL BERRY','1002706','247around','247around_42373',md5(42373),'1','42373',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247109','LIVING ARTS','1002706','247around','247around_95436',md5(95436),'1','95436',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247110','NACSON','1002706','247around','247around_77365',md5(77365),'1','77365',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247111','ELARA','1002706','247around','247around_84070',md5(84070),'1','84070',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247112','MV VENTURES','1002706','247around','247around_82893',md5(82893),'1','82893',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247113','THOMPSONS / FOXSTAR / ITH / PRANIT','1002706','247around','247around_56475',md5(56475),'1','56475',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247114','KORTEK','1002706','247around','247around_36837',md5(36837),'1','36837',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247115','ACOOSTA','1002706','247around','247around_12531',md5(12531),'1','12531',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247116','AKIVA','1002706','247around','247around_94753',md5(94753),'1','94753',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247117','KECHAODA','1002706','247around','247around_11879',md5(11879),'1','11879',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247118','QFX','1002706','247around','247around_38954',md5(38954),'1','38954',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247122','GEM','1002706','247around','247around_59699',md5(59699),'1','59699',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247123','Burly','1002706','247around','247around_37128',md5(37128),'1','37128',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247124','Adsun','1002706','247around','247around_31838',md5(31838),'1','31838',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247125','INSAK HOMES','1002706','247around','247around_68299',md5(68299),'1','68299',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247126','UPTRON','1002706','247around','247around_56383',md5(56383),'1','56383',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247127','STARSHINE','1002706','247around','247around_32905',md5(32905),'1','32905',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247128','INDO WORLD','1002706','247around','247around_20328',md5(20328),'1','20328',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247129','INDOWORLD DIGITECH PRIVATE LIMITED','1002706','247around','247around_50491',md5(50491),'1','50491',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247130','Videocon','1002706','247around','247around_41020',md5(41020),'1','41020',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247131','SHARP','1002706','247around','247around_12440',md5(12440),'1','12440',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247132','EILISH','1002706','247around','247around_25829',md5(25829),'1','25829',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247133','LIFELONG','1002706','247around','247around_80957',md5(80957),'1','80957',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247136','OSCAR - WB','1002706','247around','247around_22109',md5(22109),'1','22109',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247137','ORGANIC','1002706','247around','247around_86343',md5(86343),'1','86343',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247138','HUIDI','1002706','247around','247around_36453',md5(36453),'1','36453',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247139','i-smart','1002706','247around','247around_67963',md5(67963),'1','67963',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247140','KAZAKI','1002706','247around','247around_65064',md5(65064),'1','65064',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247141','INOYO','1002706','247around','247around_45219',md5(45219),'1','45219',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247142','Amazon Home Services','1002706','247around','247around_69398',md5(69398),'1','69398',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247143','PIXELS','1002706','247around','247around_61554',md5(61554),'1','61554',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247144','Jack Martin','1002706','247around','247around_66077',md5(66077),'1','66077',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247145','CloudWalker','1002706','247around','247around_39935',md5(39935),'1','39935',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247147','FENDA','1002706','247around','247around_51332',md5(51332),'1','51332',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247148','NHANCENOW','1002706','247around','247around_43344',md5(43344),'1','43344',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247149','Dacs','1002706','247around','247around_76150',md5(76150),'1','76150',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247150','Harrison','1002706','247around','247around_99429',md5(99429),'1','99429',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247151','SCAT','1002706','247around','247around_83247',md5(83247),'1','83247',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247152','Kenstar','1002706','247around','247around_48195',md5(48195),'1','48195',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247153','Videocon(Darling)','1002706','247around','247around_54320',md5(54320),'1','54320',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247154','Remson-Prime','1002706','247around','247around_74334',md5(74334),'1','74334',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247155','Viewme','1002706','247around','247around_60618',md5(60618),'1','60618',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247156','RGL','1002706','247around','247around_77747',md5(77747),'1','77747',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247157','Pureflames','1002706','247around','247around_20278',md5(20278),'1','20278',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247158','Cika','1002706','247around','247around_83756',md5(83756),'1','83756',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247159','Powerpye','1002706','247around','247around_27263',md5(27263),'1','27263',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247160','VYOM','1002706','247around','247around_17483',md5(17483),'1','17483',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247161','KENVA','1002706','247around','247around_93089',md5(93089),'1','93089',now(),now());
INSERT INTO entity_login_table (entity, entity_id, entity_name, contact_person_id, agent_name, user_id, password, active, clear_password, create_date, update_date) VALUES ('partner','247162','HIT','1002706','247around','247around_79455',md5(79455),'1','79455',now(),now());

--Kalyani 06-08-2019
UPDATE `sms_template` SET `template` = 'Hi %S,Your Engineer Login is created.User Id - %s,Password - %s. download engineer app from https://urlzs.com/zoUkF.247around' WHERE `sms_template`.`tag` = 'engineer_login_sms_template';
--Gorakh 08-08-2019
ALTER TABLE `inventory_model_mapping` ADD `active` TINYINT DEFAULT 1 AFTER  `create_date`;

-- Menus for category/capacity
INSERT INTO `header_navigation` ( `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Partner Category Capacity Mapping', NULL, 'employee/service_centre_charges/show_partner_appliances', 2, '52', 'admin,developer', 'main_nav', 1, '2019-08-06 09:13:09'),
( '247Around', 'Capacity', NULL, 'capacity', 2, '52', 'admin,developer', 'main_nav', 1, '2019-08-06 09:11:02'),
( '247Around', 'Category', NULL, 'category', 1, '52', 'admin,developer', 'main_nav', 1, '2019-08-06 09:07:06');

-- Add Foriegn key Constraints on Warranty Tables
ALTER TABLE warranty_plan_model_mapping ADD CONSTRAINT `fk_warranty_plan_model_mapping_services` FOREIGN KEY(`service_id`) REFERENCES services(id);
ALTER TABLE warranty_plan_model_mapping ADD CONSTRAINT `fk_warranty_plan_model_mapping_plans` FOREIGN KEY(`plan_id`) REFERENCES warranty_plans(plan_id);
ALTER TABLE warranty_plan_model_mapping ADD CONSTRAINT `fk_warranty_plan_model_mapping_model` FOREIGN KEY(`model_id`) REFERENCES appliance_model_details(id);
ALTER TABLE warranty_plans  ADD CONSTRAINT `fk_warranty_plan_partner_id_partners_id` FOREIGN KEY (partner_id) REFERENCES partners (id);
ALTER TABLE warranty_plan_state_mapping ADD CONSTRAINT fk_warranty_plan_state_mapping_plan_id_warranty_plan_plan_id FOREIGN KEY (plan_id) REFERENCES warranty_plans (plan_id);
ALTER TABLE warranty_plan_state_mapping ADD CONSTRAINT fk_warranty_plan_state_mapping_state_code_state_state_code FOREIGN KEY (state_code) REFERENCES state_code (state_code);
ALTER TABLE warranty_plan_part_type_mapping ADD CONSTRAINT fk_wpptm_part_type_inventory_parts_type_id FOREIGN KEY (part_type_id) REFERENCES inventory_parts_type (id);
ALTER TABLE warranty_plan_part_type_mapping ADD CONSTRAINT fk_wpptm_plan_id_warranty_plan_plan_id FOREIGN KEY (plan_id) REFERENCES warranty_plans (plan_id);

--- Abhishek -----

CREATE TABLE spare_nrn_approval ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `booking_id` VARCHAR(50) NOT NULL ,  `email_to` VARCHAR(100) NULL DEFAULT NULL ,  `remark` TEXT NOT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;

ALTER TABLE `spare_nrn_approval` ADD `approval_file` TEXT NULL DEFAULT NULL AFTER `email_to`;
ALTER TABLE `spare_parts_details` ADD `nrn_approv_by_partner` INT(5) NOT NULL DEFAULT '0' AFTER `spare_cancellation_reason`;

 
-- Ankit 13-08-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES (NULL, 'not_delivered_bb_orders', NULL, ' ', 'sunilk@247around.com', 'kmardee@amazon.com,ybhargav@amazon.com', 'sunilk@247around.com', '', '1', '', CURRENT_TIMESTAMP);

 
---Abhishek--
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES (NULL, '247130', 'Pending', 'NRN Approved By Partner', 'NRN Approved By Partner', 'NRN Approved By Partner', 'Partner', NULL, CURRENT_TIMESTAMP);
 
-- Kajal 20-08-2019
INSERT INTO `internal_status` (`id`, `page`, `status`, `active`, `sf_update_active`, `method_name`, `redirect_url`, `create_date`) VALUES (NULL, 'bill_defective_spare_part_lost', 'Part Sold', '1', '0', NULL, NULL, CURRENT_TIMESTAMP);
UPDATE `invoice_tags` SET `tag` = 'part_lost' WHERE `invoice_tags`.`sub_category` = 'Defective Part Lost';

--sachin 13-08-2019

CREATE TABLE `boloaaka`.`courier_file_upload_header_mapping` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `docket_number` VARCHAR(64) NULL DEFAULT NULL , `booking_station` VARCHAR(128) NULL DEFAULT NULL , `delivery_station` VARCHAR(128) NULL DEFAULT NULL , `consignee_name` VARCHAR(256) NULL DEFAULT NULL , `courier_booking_date` VARCHAR(64) NULL DEFAULT NULL , `assured_delivery_date` VARCHAR(64) NULL DEFAULT NULL , `delivery_date` VARCHAR(64) NULL DEFAULT NULL , `docket_status` VARCHAR(64) NULL DEFAULT NULL , `docket_current_status` VARCHAR(64) NULL DEFAULT NULL , `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `courier_file_upload_header_mapping` ADD `courier_partner_id` INT(11) NOT NULL AFTER `id`;

CREATE TABLE `boloaaka`.`courier_status_file_details` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `docket_no` VARCHAR(64) NOT NULL , `booking_stn` VARCHAR(64) NOT NULL , `delivery_stn` VARCHAR(64) NOT NULL , `bkg_dt` DATETIME NOT NULL , `assured_dly_dt` DATETIME NOT NULL , `delivery_date` DATETIME NOT NULL , `docket_status` VARCHAR(128) NOT NULL , `docket_curr_status` VARCHAR(128) NOT NULL , `consignee_name` VARCHAR(64) NOT NULL , `order_no` VARCHAR(64) NOT NULL , `remarks` VARCHAR(256) NOT NULL , `deps` VARCHAR(64) NOT NULL , `no_of_pkgs` VARCHAR(32) NOT NULL , `actual_wt` DECIMAL(10,2) NOT NULL , `charged_wt` DECIMAL(10,2) NOT NULL , `only_booking_amt` DECIMAL(10,2) NOT NULL , `cargo_value` DECIMAL(10,2) NOT NULL , `freight_amt` DECIMAL(10,2) NOT NULL , `cust_remarks` VARCHAR(256) NOT NULL , `bkg_zone` VARCHAR(64) NOT NULL , `dly_zone` VARCHAR(64) NOT NULL , `prod_service_name` VARCHAR(128) NOT NULL , `create_date` TIMESTAMP NOT NULL , `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `courier_status_file_details` CHANGE `delivery_date` `delivery_date` DATETIME NULL DEFAULT NULL;
ALTER TABLE `courier_status_file_details` CHANGE `create_date` `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `courier_status_file_details` CHANGE `bkg_dt` `bkg_dt` DATE NOT NULL;
ALTER TABLE `courier_status_file_details` CHANGE `assured_dly_dt` `assured_dly_dt` DATE NOT NULL;
ALTER TABLE `courier_status_file_details` CHANGE `delivery_date` `delivery_date` DATE NULL DEFAULT NULL;

INSERT INTO `courier_file_upload_header_mapping` (`id`, `courier_partner_id`, `docket_number`, `booking_station`, `delivery_station`, `consignee_name`, `courier_booking_date`, `assured_delivery_date`, `delivery_date`, `docket_status`, `docket_current_status`, `create_date`, `update_date`) VALUES ('10000', '10002', 'docket_no', 'booking_stn', 'delivery_stn', 'consignee_name', 'bkg_dt', 'assured_dly_dt', 'delivery_date', 'docket_status', 'docket_curr_status', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

--Kalyani 22-08-2019
ALTER TABLE `spare_parts_details` ADD `part_requested_by_engineer` BOOLEAN NOT NULL DEFAULT FALSE AFTER `nrn_approv_by_partner`;

-- Kajal 22-08-2019
UPDATE `invoice_tags` SET `tag` = 'Out-of-Warranty' WHERE `invoice_tags`.`sub_category` = 'Out-of-Warranty';

-- Prity 26-08-2019
ALTER TABLE warranty_plans add column `plan_depends_on` int(11) NOT NULL DEFAULT 1 COMMENT '1 => Model Specific (Plan Valid on Model Number), 2 => Service Specific (Plan Valid on Product eg : AC, WM)';
--Gorakh 26-08-2019
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Master', NULL, NULL, 1, NULL, 'accountmanager,admin,closure,developer,inventory_manager,regionalmanager', 'main_nav', 1, '2017-12-29 06:08:44');

UPDATE `header_navigation` SET `parent_ids` = '247',title ='Upload Model Master' WHERE `header_navigation`.`id` = 120;
UPDATE `header_navigation` SET `parent_ids` = '247',title='Upload BOM Master'  WHERE `header_navigation`.`id` = 116;
UPDATE `header_navigation` SET `parent_ids` = '247', title ='Upload Alternate Master' WHERE `header_navigation`.`id` = 225;
UPDATE `header_navigation` SET `parent_ids` = '247', title='Upload Model vs Part Code Master' WHERE `header_navigation`.`id` = 121;
UPDATE `header_navigation` SET `parent_ids` = '247',title='Create Part Type' WHERE `header_navigation`.`id` = 197;
UPDATE `header_navigation` SET `parent_ids` = '190',title='Upload Symptom Master' WHERE `header_navigation`.`id` = 190;

UPDATE `header_navigation` SET `parent_ids` = '247',title='Download Serviceable BOM By Appliance' WHERE `header_navigation`.`id` = 233;

UPDATE `header_navigation` SET `is_active` = '0' WHERE `header_navigation`.`id` = 239;

UPDATE `header_navigation` SET `parent_ids` = '247',title='Download Serviceable BOM By Model' WHERE `header_navigation`.`id` = 152;

UPDATE `header_navigation` SET `parent_ids` = '247', WHERE `header_navigation`.`id` = 240;

UPDATE `header_navigation` SET `parent_ids` = '247', WHERE `header_navigation`.`id` = 236;

UPDATE `header_navigation` SET `parent_ids` = '247',title='Download Alternate Part Master' WHERE `header_navigation`.`id` = 238;
-- Ankit 29-08-2019
CREATE TABLE spare_consumption_status (
	id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	consumed_status varchar(255) NOT NULL,
	is_consumed tinyint(0) NOT NULL DEFAULT 0,
	create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	update_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `spare_consumption_status` (`id`, `consumed_status`, `is_consumed`, `create_date`, `update_date`) VALUES (NULL, 'Product consumed', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), (NULL, 'Product not delivered', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), (NULL, 'Damage/Broken part received', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), (NULL, 'Wrong part received', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), (NULL, 'Part shipped but not used', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), (NULL, 'Part cancelled', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), (NULL, 'Part not NRN approved', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

ALTER TABLE spare_parts_details ADD COLUMN consumed_part_status_id int(11) NULL DEFAULT NULL AFTER old_status;

-- Kajal 29-08-2019
ALTER TABLE `inventory_master_list` ADD `is_invoice` INT(1) NOT NULL DEFAULT '0' AFTER `part_image`;


--Pranjal 30-8-2019 - for adding link for RM Mapping
insert into `header_navigation`(`entity_type`,`title`,`link`,`level`,`groups`,`nav_type`,`is_active`)
values ('247Around','RM Mapping','employee/user/rm_state_mapping',1,'admin,developer','right_nav','1')
-- Gorakh 31-08-2019
ALTER TABLE `hsn_code_details` ADD `service_id` INT NULL DEFAULT NULL AFTER `agent_id`;
CREATE TABLE `spare_invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `spare_id` int(11) NOT NULL,
  `invoice_date` datetime NOT NULL,
  `hsn_code` varchar(255) NOT NULL,
  `gst_rate` varchar(255) NOT NULL,
  `invoice_amount` varchar(255) NOT NULL,
  `invoice_pdf` varchar(500) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `spare_invoice_details`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `spare_invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =11081200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =15050020;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =27111900;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =28044090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =29012300;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =29012910;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =29033919;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =29037100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =29291010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =29291090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =32041916;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =32041990;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =32061900;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =32064900;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =32081010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =32082090;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =32091010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =32149090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =34039900;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =35061000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =35069910;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =38021000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =38101010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =38101090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =38140010;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =38240000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =38249090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39011010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39021000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39031100;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39031900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39033000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39039010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39069000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39071000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39072010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39073090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39093090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39129020;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =39152000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39159090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39171010;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39172390;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39173100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39173290;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39191000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39199010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39199090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39201019;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39202010;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =39202090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39203090;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39204300;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39206190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39206919;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39210000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39211200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39211390;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39211900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39219010;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39231010;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39231090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =39232100;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39232910;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39232990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39239090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39241010;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =39249000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39269010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39269099;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =40091100;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =40091200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =4010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40101990;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40103190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40103999;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40161000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =40169320;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169330;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169340;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169350;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =40169390;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169950;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169960;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =40169990;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =48040000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48081000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48089000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48114100;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =4819;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48191010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48191090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =48192010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =48192090;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =48200000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =48211010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48211020;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48219090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48239090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =49011010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =49011020;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =49019900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =49111090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =52083320;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =54075210;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =62052000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =70031290;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =70040000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =70071900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =70200090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =72101190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =72104900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =72107000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =72124000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =72170000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =72286012;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73070000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =7318;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73181110;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73181400;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73181500;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73181600;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73182100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73182200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73182900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73182990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73199000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73201011;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73209090;
UPDATE hsn_code_details SET service_id ='59' WHERE hsn_code =73219000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73251000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =73259999;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =7326;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73261100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73269099;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =74111000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =74112900;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =74120000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =74199930;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =76011090;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =76071190;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =76169990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =82051000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =82057000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =83013000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =83022000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =83052000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =83081019;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =83100090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =83113090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =83119000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84130000;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =84137010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84143000;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84145190;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84145990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84148011;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84149011;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84151010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84159000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84180000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84181090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84186990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84189010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84189900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84189990;
UPDATE hsn_code_details SET service_id ='38' WHERE hsn_code =84212190;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84212900;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84213990;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84219900;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84219990;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84439990;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =8450;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84501200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84509000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84509010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84509090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84663020;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =84718000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84733030;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =84799090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84807900;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84811000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84818090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84828000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =8483;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84831099;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84834000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84835090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84849000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =8501;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85010000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =85011011;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85011019;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85011020;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85012000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85013119;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85014090;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =85015190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85030090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85041090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85043100;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85045010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85045090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85066000;
UPDATE hsn_code_details SET service_id ='55' WHERE hsn_code =85099000;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =85159000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85161000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85162900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85168000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85169000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85172900;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85177090;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =8518;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85181000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85182900;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85183000;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85185000;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85219090;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85229000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85258090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85281200;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85287213;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85287218;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85287219;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =8529;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85299090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =8532;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =85321000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85322200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85322500;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85322900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85322990;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85331000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85333190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85333990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85334030;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85334090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85340000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85361010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85361060;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85361090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85363000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85365090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85366910;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85366990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85369090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85371000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85392200;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85393190;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85401190;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =85407100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85411000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85412900;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85413010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85414020;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85414090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85415000;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85416000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85423100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85429000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85437099;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =8544;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85440000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =85441110;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85441920;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85441990;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85442010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85444220;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85444920;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85444999;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90049090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90065990;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90138010;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90139010;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90139090;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =90318000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =90321010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =90328990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =91070000;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =94032010;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =94039000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =94051090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =94054090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =96121090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =99000000;

--Abhishek--2-sep-2019
ALTER TABLE `spare_parts_details` ADD `shipped_to_partner_qty` INT(11) NOT NULL DEFAULT '1' AFTER `shipped_quantity`;

-- Kajal 02-09-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'sf_invoice_summary', 'SF Invoice Summary for period: %s to %s', 
'Dear SF, Invoice Summary are as follows:- <br><br>%s<br>
<br/>Thanks,<br/>247around Team', 'billing@247around.com', 'accounts@247around.com', 'abhaya@247around.com', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'partner_invoice_summary', 'Partner Invoice Summary for period: %s to %s', 
'Dear Partner, Invoice Summary are as follows:- <br><br>%s<br>

<br/>Thanks,<br/>247around Team', 'billing@247around.com', 'accounts@247around.com', 'abhaya@247around.com', '', '1', CURRENT_TIMESTAMP);
 
--Abhay 03 Sept
ALTER TABLE `inventory_alternate_spare_parts_mapping` ADD `model_id` INT(11) NULL DEFAULT NULL AFTER `alt_inventory_id`;
ALTER TABLE `alternate_inventory_set` ADD `model_id` INT(11) NULL DEFAULT NULL AFTER `inventory_id`;
-- Ankit 03-09-2019
ALTER TABLE spare_consumption_status ADD COLUMN status_description text NULL DEFAULT NULL AFTER consumed_status; 

-- Kajal 04-09-2019
UPDATE `email_template` SET `subject` = 'Spare shipped by %s to %s' , `template` = 'Dear Partner,<br><br> <b>%s</b> shipped below spare to your warehouse.<br><br> %s <br> <b>Courier Details </b><br><br> %s<br> Regards,<br> 247around' , `cc` = 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com' WHERE `email_template`.`tag` = 'msl_send_by_wh_to_partner';

-- Kajal 05-09-2019
UPDATE `email_template` SET `from` = 'defective-outward@247around.com', `cc` = 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com' WHERE `email_template`.`tag` = 'defective_spare_send_by_wh_to_partner';

ALTER TABLE spare_consumption_status ADD COLUMN status_description text NULL DEFAULT NULL AFTER consumed_status; 

--Kalyani 04-Aug-2019
ALTER TABLE `entity_gst_details` ADD `state_stamp_picture` VARCHAR(256) NULL AFTER `phone_number`;

-- Kajal 06-09-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'msl_send_by_microwh_to_wh', 'New Spare shipped by %s to %s', 'Dear SF,<br><br> <b>%s</b> shipped below new spare from your warehouse.<br><br> %s <br> <b>Courier Details </b><br><br> %s<br> Regards,<br> 247around', NULL, 'defective-outward@247around.com', '', 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com', '', '1', CURRENT_TIMESTAMP);
 

 --Abhishek---
 CREATE TABLE `spare_qty_mgmt` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `spare_id` INT(11) NOT NULL ,  `booking_id` VARCHAR(60) NOT NULL ,  `qty` INT(11) NOT NULL DEFAULT '1' ,  `sf_id` INT NOT NULL ,  `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;
 ALTER TABLE `spare_qty_mgmt` ADD `awb_by_sf_defective` VARCHAR(50) NOT NULL AFTER `created_on`, ADD `def_courier_price_by_sf` INT(11) NOT NULL AFTER `awb_by_sf_defective`, ADD `def_courier_name` VARCHAR(50) NOT NULL AFTER `def_courier_price_by_sf`;

 ALTER TABLE `spare_qty_mgmt` ADD `qty_status` INT(11) NOT NULL DEFAULT '1' AFTER `qty`;
 ALTER TABLE `spare_qty_mgmt` ADD `is_defective_qty` VARCHAR(100) NOT NULL AFTER `qty_status`;
 ALTER TABLE `spare_qty_mgmt` CHANGE `awb_by_sf_defective` `awb_by_sf_defective` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL, CHANGE `def_courier_price_by_sf` `def_courier_price_by_sf` DECIMAL(11) NULL DEFAULT NULL, CHANGE `def_courier_name` `def_courier_name` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
 ALTER TABLE `spare_qty_mgmt` CHANGE `qty_status` `qty_status` VARCHAR(100) NULL DEFAULT NULL;

 ALTER TABLE `spare_qty_mgmt` CHANGE `is_defective_qty` `is_defective_qty` SMALLINT(5) NOT NULL DEFAULT '1';
ALTER TABLE `courier_company_invoice_details` ADD `courier_invoice_file` VARCHAR(100) NULL DEFAULT NULL AFTER `box_count`;
ALTER TABLE `courier_company_invoice_details` ADD `shippment_date` DATE NULL DEFAULT NULL AFTER `courier_invoice_file`;
ALTER TABLE `courier_company_invoice_details` ADD `created_by` VARCHAR(50) NULL DEFAULT NULL AFTER `shippment_date`, ADD `is_exist` TINYINT(5) NOT NULL DEFAULT '0' AFTER `created_by`;
 
-- Ankit 09-09-2019
CREATE TABLE wrong_part_shipped_details (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    spare_id int(11) NOT NULL,
    part_name varchar(255) NOT NULL,
    inventory_id int(11) NOT NULL,
    remarks text NULL DEFAULT NULL
);

--Kalyani 10-09-2019
UPDATE `sms_template` SET `template` = 'Get 5 Percent Cashback On Your %s Booking. Download QR Code from %s Or Engineer Job Card & Pay On Paytm App. Use Paytm even if technician refuses and asks for Cash. 5 Percent Discount ONLY available on Payments made through Paytm. %s 247around' WHERE `sms_template`.`tag` = "customer_qr_download";

--Kalyani 11-09-2019
INSERT INTO `query_report` (`id`, `main_description`, `query1_description`, `query2_description`, `query1`, `query2`, `role`, `priority`, `type`, `active`, `result`, `create_date`) VALUES (NULL, 'Total Bookings Closed By Engineer', 'Completed', 'Cancelled', 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND closed_date >= \"2019-08-01\" AND internal_status = \"Completed\"', 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND closed_date >= \"2019-08-01\" AND internal_status = \"Cancelled\"', 'developer', '1', 'service', '1', NULL, CURRENT_TIMESTAMP);
INSERT INTO `query_report` (`id`, `main_description`, `query1_description`, `query2_description`, `query1`, `query2`, `role`, `priority`, `type`, `active`, `result`, `create_date`) VALUES (NULL, 'Todays Bookings Closed By Engineer', 'Completed', 'Cancelled', 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND DATE(closed_date) = CURDATE() AND internal_status = \"Completed\"', 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND DATE(closed_date) = CURDATE() AND internal_status = \"Cancelled\"', 'developer', '1', 'service', '1', NULL, CURRENT_TIMESTAMP);

--sachin 11-09-2019

CREATE TABLE `boloaaka`.`part_type_return_mapping` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `partner_id` INT(11) NOT NULL , `appliance_id` INT(11) NOT NULL , `part_type` VARCHAR(128) NOT NULL , `is_return` BOOLEAN NULL DEFAULT NULL , `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `part_type_return_mapping` ADD `inventory_id` INT(11) NOT NULL AFTER `appliance_id`;

--Kalyani 12-09-2019
ALTER TABLE `engineer_booking_action` ADD `purchase_invoice` VARCHAR(255) NULL DEFAULT NULL AFTER `serial_number_pic`;

-- Ankit 12-SEP-2019
ALTER TABLE spare_consumption_status ADD COLUMN tag varchar(100) NULL DEFAULT NULL AFTER id;

UPDATE `spare_consumption_status` SET `tag` = 'part_consumed' WHERE `spare_consumption_status`.`id` = 1;
UPDATE `spare_consumption_status` SET `tag` = 'part_not_received_courier_lost' WHERE `spare_consumption_status`.`id` = 2;
UPDATE `spare_consumption_status` SET `tag` = 'damage_broken_part_received' WHERE `spare_consumption_status`.`id` = 3;
UPDATE `spare_consumption_status` SET `tag` = 'wrong_part_received' WHERE `spare_consumption_status`.`id` = 4;
UPDATE `spare_consumption_status` SET `tag` = 'ok_part_received_but_not_used' WHERE `spare_consumption_status`.`id` = 5;
UPDATE `spare_consumption_status` SET `tag` = 'part_cancelled' WHERE `spare_consumption_status`.`id` = 6;
UPDATE `spare_consumption_status` SET `tag` = 'nrn_approved' WHERE `spare_consumption_status`.`id` = 7;

ALTER TABLE wrong_part_shipped_details ADD COLUMN create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ;


--Pranjal 30-8-2019 - for adding link for RM Mapping
insert into `header_navigation`(`entity_type`,`title`,`link`,`level`,`groups`,`nav_type`,`is_active`)
values ('247Around','RM Mapping','employee/user/rm_state_mapping',1,'admin,developer','right_nav','1')
-- Gorakh 31-08-2019
ALTER TABLE `hsn_code_details` ADD `service_id` INT NULL DEFAULT NULL AFTER `agent_id`;
CREATE TABLE `spare_invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `spare_id` int(11) NOT NULL,
  `invoice_date` datetime NOT NULL,
  `hsn_code` varchar(255) NOT NULL,
  `gst_rate` varchar(255) NOT NULL,
  `invoice_amount` varchar(255) NOT NULL,
  `invoice_pdf` varchar(500) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `spare_invoice_details`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `spare_invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =11081200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =15050020;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =27111900;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =28044090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =29012300;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =29012910;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =29033919;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =29037100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =29291010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =29291090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =32041916;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =32041990;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =32061900;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =32064900;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =32081010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =32082090;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =32091010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =32149090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =34039900;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =35061000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =35069910;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =38021000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =38101010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =38101090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =38140010;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =38240000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =38249090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39011010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39021000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39031100;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39031900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39033000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39039010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39069000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39071000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39072010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39073090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39093090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39129020;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =39152000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39159090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39171010;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39172390;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39173100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39173290;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39191000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39199010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39199090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39201019;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39202010;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =39202090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39203090;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39204300;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39206190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39206919;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39210000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39211200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39211390;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39211900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39219010;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39231010;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =39231090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =39232100;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39232910;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39232990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39239090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =39241010;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =39249000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39269010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =39269099;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =40091100;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =40091200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =4010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40101990;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40103190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40103999;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40161000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =40169320;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169330;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169340;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169350;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =40169390;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169950;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =40169960;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =40169990;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =48040000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48081000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48089000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48114100;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =4819;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48191010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48191090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =48192010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =48192090;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =48200000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =48211010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48211020;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48219090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =48239090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =49011010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =49011020;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =49019900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =49111090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =52083320;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =54075210;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =62052000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =70031290;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =70040000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =70071900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =70200090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =72101190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =72104900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =72107000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =72124000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =72170000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =72286012;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73070000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =7318;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73181110;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73181400;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73181500;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73181600;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73182100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73182200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73182900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73182990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73199000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73201011;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73209090;
UPDATE hsn_code_details SET service_id ='59' WHERE hsn_code =73219000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73251000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =73259999;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =7326;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =73261100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =73269099;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =74111000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =74112900;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =74120000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =74199930;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =76011090;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =76071190;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =76169990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =82051000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =82057000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =83013000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =83022000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =83052000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =83081019;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =83100090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =83113090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =83119000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84130000;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =84137010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84143000;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84145190;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84145990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84148011;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84149011;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84151010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84159000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84180000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84181090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84186990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84189010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84189900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84189990;
UPDATE hsn_code_details SET service_id ='38' WHERE hsn_code =84212190;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84212900;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84213990;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84219900;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84219990;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =84439990;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =8450;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84501200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84509000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84509010;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84509090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84663020;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =84718000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84733030;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =84799090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84807900;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84811000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =84818090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84828000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =8483;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84831099;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84834000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =84835090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =84849000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =8501;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85010000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =85011011;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85011019;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85011020;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85012000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85013119;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85014090;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =85015190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85030090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85041090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85043100;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85045010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85045090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85066000;
UPDATE hsn_code_details SET service_id ='55' WHERE hsn_code =85099000;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =85159000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85161000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85162900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85168000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85169000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85172900;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85177090;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =8518;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85181000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85182900;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85183000;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85185000;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85219090;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85229000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85258090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85281200;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85287213;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85287218;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85287219;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =8529;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85299090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =8532;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =85321000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85322200;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85322500;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85322900;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85322990;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85331000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85333190;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85333990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85334030;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85334090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85340000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85361010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85361060;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85361090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85363000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85365090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85366910;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85366990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85369090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85371000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85392200;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85393190;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85401190;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =85407100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85411000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85412900;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85413010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85414020;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85414090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85415000;
UPDATE hsn_code_details SET service_id ='45' WHERE hsn_code =85416000;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85423100;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =85429000;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85437099;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =8544;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85440000;
UPDATE hsn_code_details SET service_id ='50' WHERE hsn_code =85441110;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85441920;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85441990;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =85442010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85444220;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85444920;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =85444999;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90049090;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90065990;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90138010;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90139010;
UPDATE hsn_code_details SET service_id ='46' WHERE hsn_code =90139090;
UPDATE hsn_code_details SET service_id ='58' WHERE hsn_code =90318000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =90321010;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =90328990;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =91070000;
UPDATE hsn_code_details SET service_id ='42' WHERE hsn_code =94032010;
UPDATE hsn_code_details SET service_id ='53' WHERE hsn_code =94039000;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =94051090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =94054090;
UPDATE hsn_code_details SET service_id ='28' WHERE hsn_code =96121090;
UPDATE hsn_code_details SET service_id ='37' WHERE hsn_code =99000000; 
-- Kajal 02-09-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'sf_invoice_summary', 'SF Invoice Summary for period: %s to %s', 
'Dear SF, Invoice Summary are as follows:- <br><br>%s<br>
<br/>Thanks,<br/>247around Team', 'billing@247around.com', 'accounts@247around.com', 'abhaya@247around.com', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) 
VALUES (NULL, 'partner_invoice_summary', 'Partner Invoice Summary for period: %s to %s', 
'Dear Partner, Invoice Summary are as follows:- <br><br>%s<br>

<br/>Thanks,<br/>247around Team', 'billing@247around.com', 'accounts@247around.com', 'abhaya@247around.com', '', '1', CURRENT_TIMESTAMP);
 
 
--Pranjal 30-8-2019 - for adding link for RM Mapping
insert into `header_navigation`(`entity_type`,`title`,`link`,`level`,`groups`,`nav_type`,`is_active`)
values ('247Around','RM Mapping','employee/user/rm_state_mapping',1,'admin,developer','right_nav','1')
--Abhishek--2-sep-2019
ALTER TABLE `spare_parts_details` ADD `shipped_to_partner_qty` INT(11) NOT NULL DEFAULT '1' AFTER `shipped_quantity`;
 
--Abhay 03 Sept
ALTER TABLE `inventory_alternate_spare_parts_mapping` ADD `model_id` INT(11) NULL DEFAULT NULL AFTER `alt_inventory_id`;
ALTER TABLE `alternate_inventory_set` ADD `model_id` INT(11) NULL DEFAULT NULL AFTER `inventory_id`;
 
-- Ankit 03-09-2019
ALTER TABLE spare_consumption_status ADD COLUMN status_description text NULL DEFAULT NULL AFTER consumed_status; 

-- Kajal 04-09-2019
UPDATE `email_template` SET `subject` = 'Spare shipped by %s to %s' , `template` = 'Dear Partner,<br><br> <b>%s</b> shipped below spare to your warehouse.<br><br> %s <br> <b>Courier Details </b><br><br> %s<br> Regards,<br> 247around' , `cc` = 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com' WHERE `email_template`.`tag` = 'msl_send_by_wh_to_partner';

-- Kajal 05-09-2019
UPDATE `email_template` SET `from` = 'defective-outward@247around.com', `cc` = 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com' WHERE `email_template`.`tag` = 'defective_spare_send_by_wh_to_partner';

-- Kajal 06-09-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'msl_send_by_microwh_to_wh', 'New Spare shipped by %s to %s', 'Dear SF,<br><br> <b>%s</b> shipped below new spare from your warehouse.<br><br> %s <br> <b>Courier Details </b><br><br> %s<br> Regards,<br> 247around', NULL, 'defective-outward@247around.com', '', 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com', '', '1', CURRENT_TIMESTAMP);

-- Ankit 09-09-2019
CREATE TABLE wrong_part_shipped_details (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    spare_id int(11) NOT NULL,
    part_name varchar(255) NOT NULL,
    inventory_id int(11) NOT NULL,
    remarks text NULL DEFAULT NULL
);
 
 --Abhishek---
 CREATE TABLE spare_qty_mgmt ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `spare_id` INT(11) NOT NULL ,  `booking_id` VARCHAR(60) NOT NULL ,  `qty` INT(11) NOT NULL DEFAULT '1' ,  `sf_id` INT NOT NULL ,  `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;
 ALTER TABLE `spare_qty_mgmt` ADD `awb_by_sf_defective` VARCHAR(50) NOT NULL AFTER `created_on`, ADD `def_courier_price_by_sf` INT(11) NOT NULL AFTER `awb_by_sf_defective`, ADD `def_courier_name` VARCHAR(50) NOT NULL AFTER `def_courier_price_by_sf`;

 ALTER TABLE `spare_qty_mgmt` ADD `qty_status` INT(11) NOT NULL DEFAULT '1' AFTER `qty`;
 ALTER TABLE `spare_qty_mgmt` ADD `is_defective_qty` VARCHAR(100) NOT NULL AFTER `qty_status`;
 ALTER TABLE `spare_qty_mgmt` CHANGE `awb_by_sf_defective` `awb_by_sf_defective` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL, CHANGE `def_courier_price_by_sf` `def_courier_price_by_sf` DECIMAL(11) NULL DEFAULT NULL, CHANGE `def_courier_name` `def_courier_name` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
 ALTER TABLE `spare_qty_mgmt` CHANGE `qty_status` `qty_status` VARCHAR(100) NULL DEFAULT NULL;

 ALTER TABLE `spare_qty_mgmt` CHANGE `is_defective_qty` `is_defective_qty` SMALLINT(5) NOT NULL DEFAULT '1';
ALTER TABLE `courier_company_invoice_details` ADD `courier_invoice_file` VARCHAR(100) NULL DEFAULT NULL AFTER `box_count`;
ALTER TABLE `courier_company_invoice_details` ADD `shippment_date` DATE NULL DEFAULT NULL AFTER `courier_invoice_file`;
ALTER TABLE `courier_company_invoice_details` ADD `created_by` VARCHAR(50) NULL DEFAULT NULL AFTER `shippment_date`, ADD `is_exist` TINYINT(5) NOT NULL DEFAULT '0' AFTER `created_by`;
 
 
--Kalyani 10-09-2019
UPDATE `sms_template` SET `template` = 'Get 5 Percent Cashback On Your %s Booking. Download QR Code from %s Or Engineer Job Card & Pay On Paytm App. Use Paytm even if technician refuses and asks for Cash. 5 Percent Discount ONLY available on Payments made through Paytm. %s 247around' WHERE `sms_template`.`tag` = "customer_qr_download";

--Kalyani 11-09-2019
INSERT INTO `query_report` (`id`, `main_description`, `query1_description`, `query2_description`, `query1`, `query2`, `role`, `priority`, `type`, `active`, `result`, `create_date`) VALUES (NULL, 'Total Bookings Closed By Engineer', 'Completed', 'Cancelled', 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND closed_date >= \"2019-08-01\" AND internal_status = \"Completed\"', 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND closed_date >= \"2019-08-01\" AND internal_status = \"Cancelled\"', 'developer', '1', 'service', '1', NULL, CURRENT_TIMESTAMP);
INSERT INTO `query_report` (`id`, `main_description`, `query1_description`, `query2_description`, `query1`, `query2`, `role`, `priority`, `type`, `active`, `result`, `create_date`) VALUES (NULL, 'Todays Bookings Closed By Engineer', 'Completed', 'Cancelled', 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND DATE(closed_date) = CURDATE() AND internal_status = \"Completed\"', 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND DATE(closed_date) = CURDATE() AND internal_status = \"Cancelled\"', 'developer', '1', 'service', '1', NULL, CURRENT_TIMESTAMP);

 -- Ankit 12-SEP-2019
ALTER TABLE spare_consumption_status ADD COLUMN tag varchar(100) NULL DEFAULT NULL AFTER id;

UPDATE `spare_consumption_status` SET `tag` = 'part_consumed' WHERE `spare_consumption_status`.`id` = 1;
UPDATE `spare_consumption_status` SET `tag` = 'part_not_received_courier_lost' WHERE `spare_consumption_status`.`id` = 2;
UPDATE `spare_consumption_status` SET `tag` = 'damage_broken_part_received' WHERE `spare_consumption_status`.`id` = 3;
UPDATE `spare_consumption_status` SET `tag` = 'wrong_part_received' WHERE `spare_consumption_status`.`id` = 4;
UPDATE `spare_consumption_status` SET `tag` = 'ok_part_received_but_not_used' WHERE `spare_consumption_status`.`id` = 5;
UPDATE `spare_consumption_status` SET `tag` = 'part_cancelled' WHERE `spare_consumption_status`.`id` = 6;
UPDATE `spare_consumption_status` SET `tag` = 'nrn_approved' WHERE `spare_consumption_status`.`id` = 7;

ALTER TABLE wrong_part_shipped_details ADD COLUMN create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ;



--sachin 13-08-2019

CREATE TABLE `boloaaka`.`courier_file_upload_header_mapping` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `docket_number` VARCHAR(64) NULL DEFAULT NULL , `booking_station` VARCHAR(128) NULL DEFAULT NULL , `delivery_station` VARCHAR(128) NULL DEFAULT NULL , `consignee_name` VARCHAR(256) NULL DEFAULT NULL , `courier_booking_date` VARCHAR(64) NULL DEFAULT NULL , `assured_delivery_date` VARCHAR(64) NULL DEFAULT NULL , `delivery_date` VARCHAR(64) NULL DEFAULT NULL , `docket_status` VARCHAR(64) NULL DEFAULT NULL , `docket_current_status` VARCHAR(64) NULL DEFAULT NULL , `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `courier_file_upload_header_mapping` ADD `courier_partner_id` INT(11) NOT NULL AFTER `id`;

CREATE TABLE `boloaaka`.`courier_status_file_details` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `docket_no` VARCHAR(64) NOT NULL , `booking_stn` VARCHAR(64) NOT NULL , `delivery_stn` VARCHAR(64) NOT NULL , `bkg_dt` DATETIME NOT NULL , `assured_dly_dt` DATETIME NOT NULL , `delivery_date` DATETIME NOT NULL , `docket_status` VARCHAR(128) NOT NULL , `docket_curr_status` VARCHAR(128) NOT NULL , `consignee_name` VARCHAR(64) NOT NULL , `order_no` VARCHAR(64) NOT NULL , `remarks` VARCHAR(256) NOT NULL , `deps` VARCHAR(64) NOT NULL , `no_of_pkgs` VARCHAR(32) NOT NULL , `actual_wt` DECIMAL(10,2) NOT NULL , `charged_wt` DECIMAL(10,2) NOT NULL , `only_booking_amt` DECIMAL(10,2) NOT NULL , `cargo_value` DECIMAL(10,2) NOT NULL , `freight_amt` DECIMAL(10,2) NOT NULL , `cust_remarks` VARCHAR(256) NOT NULL , `bkg_zone` VARCHAR(64) NOT NULL , `dly_zone` VARCHAR(64) NOT NULL , `prod_service_name` VARCHAR(128) NOT NULL , `create_date` TIMESTAMP NOT NULL , `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `courier_status_file_details` CHANGE `delivery_date` `delivery_date` DATETIME NULL DEFAULT NULL;
ALTER TABLE `courier_status_file_details` CHANGE `create_date` `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `courier_status_file_details` CHANGE `bkg_dt` `bkg_dt` DATE NOT NULL;
ALTER TABLE `courier_status_file_details` CHANGE `assured_dly_dt` `assured_dly_dt` DATE NOT NULL;
ALTER TABLE `courier_status_file_details` CHANGE `delivery_date` `delivery_date` DATE NULL DEFAULT NULL;

INSERT INTO `courier_file_upload_header_mapping` (`id`, `courier_partner_id`, `docket_number`, `booking_station`, `delivery_station`, `consignee_name`, `courier_booking_date`, `assured_delivery_date`, `delivery_date`, `docket_status`, `docket_current_status`, `create_date`, `update_date`) VALUES ('10000', '10002', 'docket_no', 'booking_stn', 'delivery_stn', 'consignee_name', 'bkg_dt', 'assured_dly_dt', 'delivery_date', 'docket_status', 'docket_curr_status', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--sachin 02-09-2019

CREATE TABLE `boloaaka`.`part_type_return_mapping` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `partner_id` INT(11) NOT NULL , `appliance_id` INT(11) NOT NULL , `part_type` VARCHAR(128) NOT NULL , `is_return` BOOLEAN NULL DEFAULT NULL , `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `part_type_return_mapping` ADD `inventory_id` INT(11) NOT NULL AFTER `appliance_id`;
--Gorakh 23-09-2019
RENAME TABLE `boloaaka`.`spare_invoice_details` TO `boloaaka`.`oow_spare_invoice_details`; 
--Ankit 24-09-2019
ALTER TABLE wrong_part_shipped_details ADD COLUMN active tinyint(1) NOT NULL DEFAULT 1;
-- Prity Sharma 25-09-2019
ALTER TABLE booking_unit_details CHANGE COLUMN sf_purchase_date sf_purchase_date date NULL DEFAULT NULL;
-- Ankit 27-09-2019
ALTER TABLE partners ADD COLUMN is_booking_close_by_app_only tinyint(1) NOT NULL DEFAULT 0 AFTER auth_token;
ALTER TABLE service_centres ADD COLUMN is_booking_close_by_app_only tinyint(1) NOT NULL DEFAULT 0 AFTER is_wh;
--Gorakh 28-09-2019
ALTER TABLE `courier_company_invoice_details` CHANGE `billable_weight` `billable_weight` VARCHAR(20) NOT NULL;
ALTER TABLE wrong_part_shipped_details ADD COLUMN active tinyint(1) NOT NULL DEFAULT 1
 --Gorakh 20-09-2019
ALTER TABLE `spare_parts_details` ADD `wh_challan_number` VARCHAR(128) NULL DEFAULT NULL AFTER `sf_challan_number`;
ALTER TABLE `spare_parts_details` ADD `wh_challan_file` VARCHAR(128) NULL DEFAULT NULL AFTER `sf_challan_file`;

--Kajal 01-10-2019
UPDATE `entity_gst_details` SET `state_stamp_picture` = 'seal_07.jpg' WHERE `entity_gst_details`.`id` = 2;
UPDATE `entity_gst_details` SET `state_stamp_picture` = 'seal_09.jpg' WHERE `entity_gst_details`.`id` = 7;
UPDATE `entity_gst_details` SET `state_stamp_picture` = 'seal_27.jpg' WHERE `entity_gst_details`.`id` = 6;

-- Ankit 10-04-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES (NULL, 'courier_lost_spare_parts', NULL, ' ', 'noreply@247around.com', 'ankitr@247around.com', '', '', '1', '', CURRENT_TIMESTAMP);

--Kalyani 11-10-2019
INSERT INTO `partner_summary_report_mapping` (`Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES ('Engineer Name', 'engineer_details.name AS engineer_name', '1', '', '1', '50');

--Kajal 11-10-2019
ALTER TABLE `file_uploads` ADD `amount_paid` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `result`;

--Kalyani 18-10-2019
CREATE TABLE `engineer_consumed_spare_details` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(255) NOT NULL,
  `spare_id` int(11) NOT NULL,
  `consumed_part_status_id` int(11) DEFAULT NULL,
  `part_name` varchar(255) DEFAULT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `engineer_consumed_spare_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `engineer_consumed_spare_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

--Kalyani 31-10-2019 
UPDATE query_report SET query2 = 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND closed_date >= "2019-08-01" AND internal_status = "Cancelled" AND engineer_booking_action.booking_id in (select DISTINCT booking_id from engineer_booking_action group by booking_id having count(DISTINCT internal_status)=1)' WHERE id = '59';
UPDATE query_report SET query2 = 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND DATE(closed_date) = CURDATE() AND internal_status = "Cancelled" AND engineer_booking_action.booking_id in (select DISTINCT booking_id from engineer_booking_action group by booking_id having count(DISTINCT internal_status)=1)' WHERE id = '58'

--Gorakh 24-10-2019
ALTER TABLE `spare_parts_details` ADD `wh_to_partner_defective_shipped_date` TIMESTAMP NULL DEFAULT NULL AFTER `shipped_quantity`;

--Kalyani 01-11-2019
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `is_exception_for_length`, `create_date`) VALUES (NULL, 'appliance_installation_video_link', 'Hi %s,\r\nClick on the link to watch Installation demo video of %s link - %s\r\n247around', NULL, '1', '0', CURRENT_TIMESTAMP);
ALTER TABLE `booking_details` ADD `nrn_approved` INT(2) NOT NULL DEFAULT '0' AFTER `technical_solution`;


-- Ankit 04-11-2019
CREATE TABLE en_vendor_brand_mapping (
	id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	service_center_id int(11) NOT NULL,
	partner_id int(11) NOT NULL,
	active tinyint(1) NOT NULL DEFAULT 0,
	create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	update_date datetime NULL DEFAULT NULL
);

CREATE UNIQUE INDEX uni_partner_sf
ON en_vendor_brand_mapping (service_center_id, partner_id);

ALTER TABLE `service_centres` DROP `is_booking_close_by_app_only`;
ALTER TABLE `partners` DROP `is_booking_close_by_app_only`;
ALTER TABLE query_report add column ownership varchar(100) NULL DEFAULT NULL ;

--Kalyani 12-11-2019
CREATE TABLE `engineer_incentive_details` (
  `id` int(11) NOT NULL,
  `booking_details_id` int(11) NOT NULL,
  `partner_incentive` int(11) NOT NULL DEFAULT '0',
  `247around_incentive` int(11) NOT NULL DEFAULT '0',
  `is_active` int(11) NOT NULL DEFAULT '1',
  `is_paid` int(11) NOT NULL DEFAULT '0',
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `engineer_incentive_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `engineer_incentive_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

--Ankit 15-11-2019
ALTER TABLE spare_consumption_status ADD COLUMN active tinyint(1) NOT NULL DEFAULT 1 AFTER update_date;

--Kalyani 27-11-2019
INSERT INTO `collateral_type` (`id`, `collateral_tag`, `collateral_type`, `document_type`) VALUES (NULL, 'Brand_Collateral', 'Software', 'software');

-- Kalyani 02-12-2019
CREATE TABLE `callback_api_booking_details` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(255) NOT NULL,
  `api_status` tinyint(1) NOT NULL DEFAULT '0',
  `api_call_count` int(11) NOT NULL,
  `update_date` int(11) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `callback_api_booking_details`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `callback_api_booking_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
--Gorakh 03-12-2019
ALTER TABLE `courier_services` ADD `status` TINYINT NOT NULL DEFAULT '1' AFTER `courier_code`;

--Ankit 06-12-2019
ALTER TABLE spare_consumption_status ADD COLUMN reason_text text NULL DEFAULT NULL AFTER consumed_status;  
UPDATE spare_consumption_status SET reason_text = 'Defective Part Received' where id = 1;
UPDATE spare_consumption_status SET reason_text = 'Part not Received' where id = 2;
UPDATE spare_consumption_status SET reason_text = 'Ok/Damage Part Received' where id = 3;
UPDATE spare_consumption_status SET reason_text = 'Ok/Wrong Part Received' where id = 4;
UPDATE spare_consumption_status SET reason_text = 'Ok Part Received' where id = 5;

-- Ankit 13-06-2019
CREATE TABLE courier_lost_spare_status (
	id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
	spare_id int(11) NOT NULL,
	pod text NULL DEFAULT NULL,
	remarks text NULL DEFAULT NULL,
	status varchar(100) NOT NULL,
	agent_id int(11) NOT NULL,
	create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	update_date datetime NULL DEFAULT NULL
);

--Kalyani 09-12-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'insufficient_balance_paytm_wallet', 'Paytm wallet has insufficient balance', 'Dear Sir,<br>Paytm wallet has insufficient balance for engineer incentive amount transfer.\r\n<br/>Thanks,<br/>247around Team', 'noreply@247around.com', 'kalyanit@247around.com', 'kalyanit@247around.com', '', '1', CURRENT_TIMESTAMP);
--Gorakh 16-12-2019
ALTER TABLE `spare_parts_details` ADD `defective_part_received_by_wh` TINYINT NULL DEFAULT '0' AFTER `wh_to_partner_defective_shipped_date`, ADD `defective_part_received_date_by_wh` DATETIME NULL DEFAULT NULL AFTER `defective_part_received_by_wh`;
ALTER TABLE `spare_parts_details` ADD `remarks_defective_part_by_wh` VARCHAR(260) NULL DEFAULT NULL AFTER `defective_part_received_date_by_wh`;
ALTER TABLE `spare_parts_details` ADD `defective_part_rejected_by_wh` TINYINT(4) NOT NULL DEFAULT '0' AFTER `remarks_defective_part_by_wh`;


--Kalyani 19-12-2019
ALTER TABLE `engineer_incentive_details` ADD UNIQUE(`booking_details_id`);

/*   VIEW     */

CREATE OR REPLACE VIEW `sf_brand_wise_tat_report` AS select `service_centres`.`name` AS `sf_name`,`service_centres`.`state` AS `state`,`service_centres`.`district` AS `district`,`service_centres`.`id` AS `id`,`partners`.`id` AS `partner_id`,`partners`.`public_name` AS `partner_name`,sum(`spare_parts_details`.`shipped_quantity`) AS `parts_count_to_shipped`,sum(`spare_parts_details`.`challan_approx_value`) AS `parts_charge` from (((`spare_parts_details` join `booking_details` on((`booking_details`.`booking_id` = `spare_parts_details`.`booking_id`))) join `partners` on((`partners`.`id` = `booking_details`.`partner_id`))) join `service_centres` on((`service_centres`.`id` = `booking_details`.`assigned_vendor_id`))) where ((`spare_parts_details`.`status` not in ('Cancelled','Completed','Defective parts send by warehouse to partner','Partner acknowledge defective parts send by warehouse','Defective Part Shipped By SF','Defective Part Received By Partner','Damage Part Shipped By SF','Ok Part Shipped By SF')) and ((to_days(now()) - to_days(str_to_date(`spare_parts_details`.`shipped_date`,'%Y-%m-%d'))) >= 45)) group by `booking_details`.`partner_id`,`booking_details`.`assigned_vendor_id` order by service_centres.district,service_centres.state,service_centres.name ASC
 
/*****  Abhishek   ***/
ALTER TABLE `partners` ADD `spare_approval_by_partner` BOOLEAN NOT NULL DEFAULT FALSE AFTER `oot_spare_to_be_shipped`;
ALTER TABLE `spare_parts_details` ADD `spare_approval_date` DATE NOT NULL AFTER `wh_to_partner_defective_shipped_date`, ADD `approval_agent_id` INT(11) NOT NULL DEFAULT '247001' AFTER `spare_approval_date`, ADD `approval_entity_type` VARCHAR(15) NOT NULL DEFAULT 'vendor' AFTER `approval_agent_id`;
 
--Gorakh 20-12-2019
ALTER TABLE `spare_parts_details`  ADD `received_defective_part_pic_by_wh` VARCHAR(200) NULL DEFAULT NULL  AFTER `defective_part_rejected_by_wh`,  ADD `rejected_defective_part_pic_by_wh` VARCHAR(200) NULL DEFAULT NULL  AFTER `received_defective_part_pic_by_wh`;
 --Gorakh 27-12-2019
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Micro Warehouse MSL Details', NULL, 'employee/inventory/mwh_msl_details', 2, '190', 'accountant,accountmanager,admin,developer,inventory_manager', 'main_nav', 1, '2018-12-13 05:13:48'),
('247Around', 'Add Courier Service', NULL, 'employee/courier/add_courier_service', 2, '190', 'accountant,accountmanager,admin,developer,inventory_manager', 'main_nav', 1, '2018-12-13 05:13:48');

-- Kajal 27-12-2019
CREATE TABLE `pincode_district_mapping` ( 
    `id` INT(11) NOT NULL AUTO_INCREMENT , 
    `pincode` VARCHAR(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , 
    `district` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , 
    `state` VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

INSERT into `pincode_district_mapping` (pincode, district, state) SELECT distinct pincode, district, state FROM `india_pincode` where length(pincode)=6 group by pincode;

-- Prity 31-12-2019
Alter table service_centres add column rm_id int(11) NULL DEFAULT NULL;
Alter table service_centres add column asm_id int(11) NULL DEFAULT NULL;
update service_centres set rm_id = 36 where id IN (8,16,23,24,25,189,261,262,264,266,321,333,334,370,392,444,445,447,466,469,559,565,573,581,671,676,679,680,701,712,715,718,720,721,749,769,783,842,843,872,911,1030,1031,1051,1118,1221,1253,1254,1284,1299,7,9,10,11,12,14,18,19,20,22,48,85,156,169,291,338,395,491,567,571,572,623,624,647,651,706,750,771,792,807,818,825,826,850,855,856,857,891,894,920,963,964,1027,1065,1086,1148,1164,1189,1192,1193,1202,1207,1217,1240,1246,1256,122,123,124,125,126,142,146,147,280,312,332,335,369,373,374,378,400,402,413,429,431,434,436,440,457,474,550,566,663,678,683,734,754,755,759,761,770,777,782,812,840,845,848,849,854,884,885,896,902,924,934,967,1002,1037,1084,1085,1136,1156,1166,1172,1243,2,3,4,6,15,21,38,216,227,228,230,231,241,257,313,336,337,339,340,342,346,347,350,358,363,364,367,368,372,375,377,406,407,409,411,422,427,435,464,465,475,479,486,503,504,506,507,508,514,516,533,535,536,544,551,569,577,582,595,605,606,611,612,625,627,628,639,640,641,649,664,673,677,694,714,727,729,731,743,762,763,764,780,781,786,787,789,790,801,806,810,811,813,814,815,816,817,821,822,823,824,827,832,838,844,853,861,863,864,866,867,868,869,871,877,879,883,895,897,898,905,909,928,943,947,949,951,953,955,966,971,978,997,998,1003,1024,1036,1059,1060,1063,1075,1076,1083,1091,1095,1096,1098,1132,1140,1147,1152,1163,1167,1170,1198,1213,1215,1227,1228,1234,1235,1249,1283,1286,1287,1294,1302,212,278,311,386,387,393,433,472,473,485,986,1064,494,668,737,794,808,828,829,831,890,899,908,910,925,1053,1133,470,480,489,518,519,562,578,585,592,751,839,913,985,1184,1199,128,131,136,137,138,217,242,302,348,349,351,352,354,356,365,455,463,482,493,495,509,543,558,599,630,672,744,870,903,904,965,973,1045,1054,1141,1168,1248,1046,1277,452,1,739,1176,962,576,385,736,5,211,212,278,311,386,387,393,433,472,473,485,986,1064,17,1206,851,1066,13,1123,570,403,344,632);
update service_centres set rm_id = 24 where id IN (26,27,28,29,30,31,32,34,44,45,46,47,64,66,67,68,86,96,98,104,110,112,116,117,148,271,276,308,408,441,442,608,619,621,667,686,758,768,907,977,982,992,1000,1001,1022,1121,51,52,59,180,183,210,215,223,250,256,355,388,397,524,669,746,35,36,53,61,62,78,155,172,174,176,186,187,193,194,195,197,199,200,201,206,225,232,236,243,246,254,404,537,564,596,603,656,688,692,804,846,918,931,932,950,981,987,996,1005,1010,1016,1017,1020,1033,1039,1042,1062,1077,1087,1103,1105,1119,1131,1134,1171,1194,1205,1210,1218,1219,952,1212,1043,1236,1237,942,1239,1247,666,1258,54,60,184,214,221,222,233,239,248,255,260,283,319,390,483,512,525,561,574,575,583,587,588,634,665,670,682,704,724,730,732,760,765,776,784,785,788,791,798,841,889,930,970,990,1158,1186,1224,1225,1230,1153,326,341,1250,1187,1267,705,1272,1114,190,1195,1292,1293,1122,1292,1293,1294,1295,1296,1297,263,275,282,305,316,318,325,361,471,661,690,733,847,852,921,969,994,1026,65,89,218,253,376,401,526,580,629,635,654,689,702,726,797,862,865,873,874,878,881,900,914,916,922,939,940,954,972,983,1048,1079,1124,1165,1196,1222,1252,1275,1295,1301,360,1307,941,687,1011,886,1203,1061,1080,638,989,1312,633,1285,258,800,1078,1216,58,300,893,213,1032,1040,1151,882,1014,560,767,1111,937,938,584,976,323,1126,1197,1204,304,980,219,389,642,191,958,961,1050,1185,1262,1266,522,659,1130,979,91,772,859,880,875,984,1125,707,1023,1191,948,265,589,710,968,202,1183,1308,476,272,1317,1316,748,1190,259,1006,1320,1146,1106,1055,1300,833,1021,1117,741,1319,1265,1324,487,69,1296,331,1325);
update service_centres set rm_id = 10146 where id IN (55,56,273,286,289,417,423,424,432,450,114,115,120,127,130,132,135,139,141,143,144,149,150,152,153,154,158,159,160,161,162,163,165,166,167,168,170,171,173,175,177,178,185,192,245,247,249,296,306,343,353,357,380,399,520,521,541,545,547,548,598,622,703,711,713,716,738,766,774,799,960,974,988,991,993,999,1009,1019,1028,1034,1038,1049,1056,1068,1100,1101,1102,1115,1157,1161,1162,1188,1200,1201,39,40,41,42,43,49,57,63,70,71,72,88,92,93,94,95,97,99,100,101,102,103,106,107,108,109,111,113,118,157,164,203,220,251,252,267,269,293,315,327,329,359,383,398,410,438,439,446,448,467,527,528,532,553,579,614,615,617,618,637,658,660,674,684,698,700,709,717,719,722,742,747,793,805,820,860,876,892,901,906,923,926,935,944,957,1007,1012,1015,1035,1041,1052,1067,1069,1071,1072,1073,1074,1082,1088,1089,1090,1099,1107,1109,1112,1116,1129,1135,1139,1143,1144,1145,1160,1169,1174,1179,1208,1214,1220,1223,1226,1229,1231,1233,1238,1081,1097,1241,1245,1251,274,292,301,371,366,415,421,426,428,430,420,416,425,362,449,288,481,501,502,419,529,542,554,555,556,557,616,644,645,418,675,685,691,834,835,836,1094,1128,1138,1155,1177,1180,1181,819,933,268,837,655,1025,1104,1244,1257,1259,1261,1264,1268,773,412,1271,1232,459,1270,134,1260,1274,1276,778,1278,1108,936,281,151,1288,1289,1290,1209,1279,1281,1292,1293,1298,87,1303,1304,1305,1306,384,129,995,119,237,1309,1310,1113,1313,414,1092,1013,1018,1314,1070,145,956,324,244,1315,1311,1323);
update service_centres set rm_id = 38 where id IN (208,235,90,234,307,84,229,443,515,299,488,226,179,609,626,298,382,657,602,462,696,697,699,604,723,779,795,796,809,224,915,500,1093,610,488,1149,1150,1173,610,643,1263,531,381,1211,1057,1008,620,284,648,277,204,205,207,294,297,320,322,394,396,405,437,451,453,454,456,458,460,461,492,496,497,499,510,511,517,530,549,552,586,590,631,646,725,740,756,803,887,888,917,927,1004,1110,1175,1255,1273,1280,1291,209,238,240,270,285,287,295,303,345,379,391,477,523,540,594,662,728,946,1029,1058,1142,681,73,74,75,76,77,79,80,81,82,83,105,140,181,182,188,196,198,290,310,314,317,330,468,484,490,498,505,534,539,546,568,597,607,613,650,652,653,693,695,708,745,752,753,757,775,830,858,912,919,929,1047,1127,1154,1178,1269,1297,945,1044,478,279,959,975,735,1318,563,1120,309,593,1137,1242,1282,513,1182,802);
update service_centres set asm_id = 10105 where id IN (114,115,120,127,130,132,135,139,141,143,144,149,150,152,153,154,158,159,160,161,162,163,165,166,167,168,170,171,173,175,177,178,185,192,245,247,249,296,306,343,353,357,380,399,520,521,541,545,547,548,598,622,703,711,713,716,738,766,774,799,960,974,988,991,993,999,1009,1019,1028,1034,1038,1049,1056,1068,1100,1101,1102,1115,1157,1161,1162,1188,1200,1201,1220,1223,1229,1231,1245,1251,1244,1257,1259,1264,1268,773,1271,134,1260,1274,778,151,1279,1281,1292,1293,384,129,995,119,237,414,1314,1070,145,956,324,244);
update service_centres set asm_id = 10118 where id IN (8,16,23,24,25,189,261,262,264,266,321,334,370,392,444,445,447,466,469,559,565,573,581,671,676,679,680,701,712,715,718,720,721,749,769,842,843,872,911,1030,1031,1051,1118,1221,1254,1284,1299,9,10,11,12,14,18,19,20,22,48,85,156,169,291,338,395,491,567,571,572,623,624,651,706,750,771,792,807,818,825,826,850,856,857,891,894,920,964,1027,1065,1086,1148,1164,1189,1192,1193,1202,1207,1217,1240,1246,1256,739,1176,962,576,385,736,855,1,5,7,963,783,452,647,1253,333,17,1206,851,1066,13,1123,570,344,632);
update service_centres set asm_id = 10125 where id IN (35,36,53,61,62,78,155,172,174,176,186,187,193,194,195,197,199,200,201,206,225,232,236,243,246,254,404,537,564,596,603,656,688,692,804,846,918,931,932,950,981,987,996,1005,1010,1016,1017,1020,1033,1039,1042,1062,1077,1087,1103,1105,1119,1131,1134,1171,1194,1205,1210,1218,1219,1212,1043,1236,1237,1239,1187,1272,1114,190,1195,1122,1301,941,687,886,1203,1080,989,258,800,1078,1216,213,1032,1014,937,938,191,958,961,1050,1185,1262,1266,91,772,859,880,875,984,202,1183,1308,1317,259,1146,1300,833,1021,1117,1265,69,331,1325);
update service_centres set asm_id = 10133 where id IN (39,40,41,42,43,49,57,63,70,71,72,88,92,93,94,95,97,99,100,101,102,103,106,107,108,109,111,113,118,157,164,203,220,251,252,267,269,293,315,327,329,359,383,398,410,438,439,446,448,467,527,528,532,553,579,614,615,617,618,637,658,660,674,684,698,700,709,717,719,722,742,747,793,805,820,860,876,892,901,906,923,926,935,944,957,1007,1012,1015,1035,1041,1052,1067,1069,1071,1072,1073,1074,1082,1088,1089,1090,1099,1107,1109,1112,1116,1129,1135,1139,1143,1144,1145,1160,1169,1174,1179,1208,1214,1226,1233,1238,1081,1097,1241,819,933,268,655,1025,1104,1261,412,1232,459,1276,1278,1108,936,281,1288,1289,1290,1209,1298,87,1303,1304,1305,1309,1113,1313,1092,1013,1018,1315);
update service_centres set asm_id = 10140 where id IN (122,123,124,125,126,142,146,147,280,312,332,335,369,373,374,378,400,402,413,429,431,434,436,440,457,474,550,566,663,678,683,734,754,755,759,761,770,777,782,812,840,845,848,849,854,884,885,896,902,924,934,967,1002,1037,1084,1085,1136,1156,1166,1172,1243);
update service_centres set asm_id = 10143 where id IN (494,668,737,794,808,828,829,831,890,899,908,910,925,1053,470,480,489,518,519,562,578,585,592,751,839,913,985,1184,1199,128,131,136,137,138,217,242,302,348,349,351,352,354,356,365,455,463,482,493,495,509,543,558,599,630,672,744,870,903,904,965,973,1045,1054,1141,1168,1248,1046,1277,1133,403);
update service_centres set asm_id = 10141 where id IN (3,4,6,21,38,216,241,336,340,342,358,372,375,377,406,407,409,411,422,464,465,479,486,503,506,508,514,516,535,569,577,595,628,677,727,729,731,743,762,810,813,815,816,817,821,822,838,853,863,864,867,868,871,879,883,928,997,998,1059,1060,1075,1083,1098,1152,1215,1227,1294,1302,211,212,278,311,386,387,393,433,472,473,485,986,1064);
update service_centres set asm_id = 10156 where id IN (274,292,301,371,366,415,421,426,428,430,420,416,425,362,449,288,481,501,502,419,529,542,554,555,556,557,616,644,645,418,675,685,691,834,835,836,1094,1128,1138,1155,1177,1180,1181,837,1270,1306,1311);
update service_centres set asm_id = 10160 where id IN (54,60,184,214,221,222,233,239,248,255,260,283,319,390,483,512,525,561,574,575,583,587,588,634,665,670,682,704,724,730,732,760,765,776,784,785,788,791,798,841,889,930,970,990,1158,1186,1224,1225,1230,1153,326,341,1250,1307,1285,58,300,893,560,767,584,976,323,304,522);
update service_centres set asm_id = 10170 where id IN (1292,1293,1294,1295,1297,263,275,282,305,316,318,325,361,471,661,690,733,847,852,921,969,994,1026,65,89,218,253,376,401,526,580,629,635,654,689,702,726,797,862,865,873,874,878,881,900,914,916,922,939,940,954,972,983,1048,1079,1124,1165,1196,1222,1252,1275,1295,633,1040,1151,882,1111,1126,1197,1204,980,219,389,642,659,1130,979,1125,707,1023,1191,948,476,272,1316,1006,1106);
update service_centres set asm_id = 10178 where id IN (227,228,230,231,257,337,339,346,347,350,363,364,367,368,427,435,475,504,507,533,536,544,551,582,611,612,625,627,639,640,641,649,664,673,694,714,763,764,780,781,786,787,789,790,801,806,811,814,823,824,827,832,844,861,866,869,877,895,897,898,905,909,943,947,949,951,953,955,966,971,978,1003,1024,1036,1063,1076,1091,1095,1096,1147,1163,1167,1170,1198,1213,1228,1234,1235,1249,1283,1286,1287,1132);
update service_centres set asm_id = 10181 where id IN (204,205,207,294,297,320,322,394,396,405,437,451,453,454,456,458,460,461,492,496,497,499,510,511,517,530,549,552,586,590,631,646,725,740,756,803,887,888,917,927,1004,1110,1175,1255,1273,1280,1291,209,238,240,270,285,287,295,303,345,379,391,477,523,540,594,662,728,946,1029,1058,1142,681,1044,563);

--Abhay 06-01-2020
ALTER TABLE `paytm_transaction_callback` ADD `engineer_id` INT(11) NULL DEFAULT NULL AFTER `response_api`;


---Abhishek 10-01-2019
ALTER TABLE `engineer_details` ADD `device_firebase_token` TEXT NULL DEFAULT NULL AFTER `update_date`;
CREATE TABLE `engineer_notification_detail` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `phone` varchar(20) DEFAULT NULL,
 `message` text CHARACTER SET utf8 COLLATE utf8_bin,
 `notified` int(5) NOT NULL DEFAULT '1',
 `fire_base_response` text,
 `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1


ALTER TABLE `entity_login_table` ADD `device_firebase_token` TEXT NULL DEFAULT NULL AFTER `device_id`;

--Ankit 15-01-2019
ALTER TABLE spare_parts_details ADD COLUMN consumption_remarks text NULL DEFAULT NULL AFTER consumed_part_status_id;
ALTER TABLE `engg_notification_detail` DROP `notified`;
ALTER TABLE `engg_notification_detail`  ADD `notified` INT(5) NOT NULL DEFAULT '1'  AFTER `message`,  ADD `fire_base_response` TEXT NULL DEFAULT NULL  AFTER `notified`;

--Ankit Bhatt 2020-01-21
 insert into header_navigation(entity_type, title, link, level, parent_ids, groups, nav_type, is_active, create_date)
values('247Around', 'Warranty Plan List', 'employee/warranty/warranty_plan_list', 2, 52, 'admin,developer', 'main_nav', 1, now());

--Gorakh Nath 16-01-2020
CREATE TABLE `spare_state_change_tracker` (
  `id` int(11) NOT NULL,
  `spare_id` int(11) NOT NULL,
  `action` varchar(300) DEFAULT NULL,
  `remarks` varchar(400) DEFAULT NULL,
  `agent_id` int(11) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `entity_type` varchar(35) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `spare_state_change_tracker`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `spare_state_change_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `booking_state_change`  ADD `spare_id` INT(11) NULL DEFAULT NULL  AFTER `create_date`;
 
--Ankit Bhatt 2020-01-21
 insert into header_navigation(entity_type, title, link, level, parent_ids, groups, nav_type, is_active, create_date)
values('247Around', 'Warranty Plan List', 'employee/warranty/warranty_plan_list', 2, 52, 'admin,developer', 'main_nav', 1, now());


--Ankit Bhatt 2020-01-22
insert into `email_template`(tag, subject, template, from, to, cc, bcc, active)
values('parts_received_by_warehouse','Parts Received By Warehouse', 'Parts Received By Warehouse:<br>Booking Id : %s <br>SF: %s <br>Receive Date : %s <br>Shipped By : %s <br>Part Name : %s <br>Part Number : %s <br>Quantity : %s <br>Consumption Reason : %s <br>Warehouse : %s <br>Image Link : %s <br>Thanks!!', 'ankitb@247around.com', 'ankitb@247around.com', '', '', 1);

--Ankit Bhatt 2020-01-27
ALTER TABLE taxpro_gstr2a_data ADD COLUMN state_gstin varchar(30);
update taxpro_gstr2a_data set state_gstin = '07AAFCB1281J1ZQ';


-- Prity Sharma 27-01-2020
CREATE TABLE `rm_region_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region` varchar(25) NOT NULL,
  `rm_id` int(10) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY `fk_rm` (`rm_id`) REFERENCES employee(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO `rm_region_mapping` (`region`, `rm_id`) VALUES ('North', '36');
INSERT INTO `rm_region_mapping` (`region`, `rm_id`) VALUES ('South', '10146');
INSERT INTO `rm_region_mapping` (`region`, `rm_id`) VALUES ('East', '38');
INSERT INTO `rm_region_mapping` (`region`, `rm_id`) VALUES ('West', '24');

INSERT INTO `entity_role` (`entity_type`, `department`, `role`, `is_filter_applicable`, `create_date`) VALUES ('247Around', 'Operations', 'areasalesmanager', '0', now());

SELECT * FROM `employee` WHERE full_name like '%arun%'; -- 36
SELECT * FROM `employee` WHERE full_name like '%rajendra%'; -- 24
SELECT * FROM `employee` WHERE full_name like '%souvik%'; -- 38
SELECT * FROM `employee` WHERE full_name like '%sankara%'; -- 10146

update `employee` set groups = 'areasalesmanager' where groups = 'regionalmanager';
update `employee` set groups = 'regionalmanager' where id IN  (36,24,38,10146);

--Ankit Bhatt 2020-01-27
ALTER TABLE taxpro_gstr2a_data ADD COLUMN state_gstin varchar(30);
update taxpro_gstr2a_data set state_gstin = '07AAFCB1281J1ZQ' where state_gstin ='';

--Abhay 07-01-2020
ALTER TABLE `courier_company_invoice_details` ADD `is_delivered` INT(1) NOT NULL DEFAULT '0' AFTER `shippment_date`, ADD `delivered_date` DATETIME NULL DEFAULT NULL AFTER `is_delivered`;


---Release Date 08-01-2020

--Abhay 24-01-2020
SELECT awb_by_sf, sum(courier_charges_by_sf) as courier_charges_by_sf, defective_part_shipped_date, received_defective_part_date, status FROM `spare_parts_details` WHERE spare_parts_details.awb_by_sf IS NOT NULL GROUP by awb_by_sf


--Abhay Feb 03
CREATE TABLE `billed_docket` (
  `id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  `entity_type` varchar(64) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `invoice_id` varchar(128) NOT NULL,
  `basic_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--Ankit Bhatt 2020-02-03
update `email_template` set template='<table border="1" cellspacing="0" cellpadding="0"><tr><td colspan="10">Parts Received By Warehouse</td></tr><tr><th>Booking Id</th><th>SF</th><th>Receive Date</th><th>Shipped By</th><th>Part Name</th><th>Part Number</th><th>Quantity</th><th>Consumption Reason</th><th>Warehouse</th><th>Image Link</th></tr><tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr><tr><td colspan="10">Thanks!!</td></tr></table>' where tag= 'parts_received_by_warehouse';

-- Kajal 13-02-2020
ALTER TABLE `booking_details` MODIFY `sf_upcountry_rate` DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE `booking_details` MODIFY `partner_upcountry_rate` DECIMAL(10,2) NULL DEFAULT NULL;
--Ankit 15-01-2019
ALTER TABLE spare_parts_details ADD COLUMN consumption_remarks text NULL DEFAULT NULL AFTER consumed_part_status_id;
--Ankit Rajvanshi 05-02-2020
INSERT INTO `spare_consumption_status` (`tag`, `consumed_status`, `reason_text`, `status_description`, `is_consumed`, `create_date`, `update_date`, `active`) VALUES ('part_not_received', 'Part not Received', 'Part not Received', 'For any reason part not received to you', '0', '2019-08-29 11:43:40', '2019-08-29 11:43:40', '1');

---Abhishek 13-02-2020 --
CREATE TABLE `247around`.`engineer_configs` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `configuration_type` VARCHAR(255) NOT NULL ,  `config_value` VARCHAR(255) NULL ,  `description` VARCHAR(255) NULL DEFAULT NULL ,  `groups` VARCHAR(255) NULL DEFAULT NULL ,  `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,  `create_date` DATETIME NOT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;

ALTER TABLE `engineer_configs` ADD `app_version` VARCHAR(10) NULL DEFAULT NULL AFTER `description`;
--Gorakh 04-02-2020
ALTER TABLE `spare_state_change_tracker` CHANGE `partner_id` `entity_id` INT(11) NULL DEFAULT NULL, CHANGE `service_center_id` `entity_type` VARCHAR(35) NULL DEFAULT NULL;
--Gorakh 15-02-2020
ALTER TABLE `booking_state_change`  ADD `spare_id` INT NULL DEFAULT NULL  AFTER `service_center_id`;
-- Kajal 14-02-2020
ALTER TABLE `miscellaneous_charges` ADD `purchase_invoice_file` VARCHAR(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `approval_file`;

--Ankit Bhatt 2020-02-12
UPDATE account_holders_bank_details SET ifsc_code = UPPER(ifsc_code);
-- Kajal 13-02-2020
ALTER TABLE `booking_details` MODIFY `sf_upcountry_rate` DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE `booking_details` MODIFY `partner_upcountry_rate` DECIMAL(10,2) NULL DEFAULT NULL;

-- Ankit Rajvanshi 17-02-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Detailed Summary Report', NULL, 'employee/booking/get_detailed_summary_report', 2, '80', 'admin,developer,regionalmanager', 'main_nav', 1, '2019-08-02 05:42:02');
  
-- Prity 18-02-2020
ALTER table booking_details ADD column created_by_agent_id int NOT NULL;
ALTER table booking_details ADD column created_source int NOT NULL;
--Ankit Bhatt 2020-02-06
delete FROM `taxpro_gstr2a_data` WHERE id in(1909,1910,1911,1912,1913,1914,1915,1916,1917,1918,1919,1920,1921,1922,1923,1924,1925,1926,1927,1928,1929,1894,1895,1896,1897,1900,1901,1907,1930,1931,1932,2078,2063,2064,2079,2080
,2160,2161,2162,2135,1514,1515,1516,1519,1526,1527,1528,1530,1534,1522,1523,1531,1532,1533,1535,1827,1828,1829,1830,1832,1833,1834,1835,2065,2066,2067,2068,2069,2133,2134
,2165,2166,2253,2254,2255,2256,2257,2261,2262,2263,2264,2265,2266,2267,2330,2331,2332,2333,2334,2335,2336,2337,2268,2269,2270,2271,2272
,1507,1508,1509,1510,1511,1512,1838,1839,1840,1841,1842,1843,1521)

delete FROM `taxpro_gstr2a_data` WHERE id >=2273 and id <=2303;
delete FROM `taxpro_gstr2a_data` WHERE id >=1933 and id <=2036;
delete FROM `taxpro_gstr2a_data` WHERE id >=2081 and id <=2126;
delete FROM `taxpro_gstr2a_data` WHERE id >=2038 and id <=2047;
delete FROM `taxpro_gstr2a_data` WHERE id >=2136 and id <=2158;
delete FROM `taxpro_gstr2a_data` WHERE id >=2169 and id <=2196;
delete FROM `taxpro_gstr2a_data` WHERE id >=2199 and id <=2249;
delete FROM `taxpro_gstr2a_data` WHERE id >=1782 and id <=1791;
delete FROM `taxpro_gstr2a_data` WHERE id >=1846 and id <=1854;
delete FROM `taxpro_gstr2a_data` WHERE id >=1885 and id <=1891;

ALTER TABLE `taxpro_gstr2a_data` ADD UNIQUE( checksum(255),gst_no, invoice_number, invoice_amount, gst_rate, taxable_value, invoice_date);


--Ankit Bhatt 2020-02-18
insert into header_navigation(entity_type, title, title_icon, link, level, parent_ids, groups, nav_type, is_active, create_date)
values('247Around', 'Part Sold Reverse Spare Sale Invoice ', null, 'employee/invoice/spare_sale_invoice_list', 3, 57, 'admin,developer', 'main_nav', 1, now());
--Ankit Rajvanshi 05-02-2020
INSERT INTO `spare_consumption_status` (`tag`, `consumed_status`, `reason_text`, `status_description`, `is_consumed`, `create_date`, `update_date`, `active`) VALUES ('part_not_received', 'Part not Received', 'Part not Received', 'For any reason part not received to you', '0', '2019-08-29 11:43:40', '2019-08-29 11:43:40', '1');
 
---Abhishek 13-02-2020 --
CREATE TABLE `247around`.`engineer_configs` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `configuration_type` VARCHAR(255) NOT NULL ,  `config_value` VARCHAR(255) NULL ,  `description` VARCHAR(255) NULL DEFAULT NULL ,  `groups` VARCHAR(255) NULL DEFAULT NULL ,  `update_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,  `create_date` DATETIME NOT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;

ALTER TABLE `engineer_configs` ADD `app_version` VARCHAR(10) NULL DEFAULT NULL AFTER `description`;

-- Kajal 13-02-2020
ALTER TABLE `booking_details` MODIFY `sf_upcountry_rate` DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE `booking_details` MODIFY `partner_upcountry_rate` DECIMAL(10,2) NULL DEFAULT NULL;

-- Kajal 14-02-2020
ALTER TABLE `miscellaneous_charges` ADD `purchase_invoice_file` VARCHAR(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `approval_file`;

Add above Lines after this line :
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10186,28);

-- Prity 14-02-2020
CREATE TABLE `agent_state_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `state_code` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_state_agent` (`agent_id`,`state_code`),
  KEY `fk_agent_id` (`agent_id`),
  KEY `fk_state_code` (`state_code`),
  CONSTRAINT `agent_state_mapping_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `employee` (`id`),
  CONSTRAINT `agent_state_mapping_ibfk_2` FOREIGN KEY (`state_code`) REFERENCES `state_code` (`state_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (36,5),(36,6),(36,7),(36,8),(36,1),(36,2),(36,3),(36,4),(36,9);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (24,30),(24,27),(24,24),(24,22),(24,23);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10146,32),(10146,33),(10146,29),(10146,34),(10146,36),(10146,28);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (38,17),(38,21),(38,14),(38,12),(38,18),(38,16),(38,13),(38,15),(38,10),(38,20),(38,19);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10105,36);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10118,6),(10118,7);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10125,27);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10133,33),(10133,32);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10140,8);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10143,1),(10143,2),(10143,3),(10143,4);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10141,5),(10141,9);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10156,29);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10160,24);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10170,22),(10170,23);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10178,5),(10178,9);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10181,10),(10181,20);
INSERT INTO agent_state_mapping (agent_id,state_code) VALUES (10186,28);

 -- ghanshyam 17-02-2020----------------------------------------
values('247Around', 'Warranty Plan List', 'employee/warranty/warranty_plan_list', 2, 52, 'admin,developer', 'main_nav', 1, now());


-- ghanshyam 17-02-2020----------------------------------------
 CREATE TABLE `accessories_product_description` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `appliance` int(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `basic_charge` float(10,2) NOT NULL,
  `hsn_code` varchar(50) NOT NULL,
  `tax_rate` float(10,2) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `accessories_product_description`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_name` (`product_name`),
  ADD KEY `FK_appliance` (`appliance`);

  ALTER TABLE `accessories_product_description`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


-- Ankit Rajvanshi 17-02-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Detailed Summary Report', NULL, 'employee/booking/get_detailed_summary_report', 2, '80', 'admin,developer,regionalmanager', 'main_nav', 1, '2019-08-02 05:42:02');
  
-- Prity 18-02-2020
ALTER table booking_details ADD column created_by_agent_id int NOT NULL;
ALTER table booking_details ADD column created_source int NOT NULL;
ALTER TABLE booking_details ADD COLUMN created_by_agent_type varchar(50) NULL DEFAULT NULL AFTER created_by_agent_id;

--Ankit Bhatt 2020-02-18
insert into header_navigation(entity_type, title, title_icon, link, level, parent_ids, groups, nav_type, is_active, create_date)
values('247Around', 'Part Sold Reverse Spare Sale Invoice ', null, 'employee/invoice/spare_sale_invoice_list', 3, 57, 'admin,developer', 'main_nav', 1, now());

-- Kajal 18-02-2020
INSERT INTO `invoice_tags` (`id`, `vertical`, `category`, `sub_category`, `accounting`, `remarks`, `tag`) VALUES (NULL, 'Service', 'Spares', 'Accessories', '1', 'SF Accessories Invoice', 'accessories');

-- Kajal 19-02-2020
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'sf_accessories_invoice', 'SF Accessories Invoice', 'Dear SF, <br/><br/> Please find Accessories Invoice attached for your reference.  <br/><br/> With Regards, <br>247around Team', 'billing@247around.com', '', '', '', '1', CURRENT_TIMESTAMP);

-- Ghanshyam 20_02_2020

ALTER TABLE accessories_product_description Change created_by agent_id int(10);
ALTER TABLE accessories_product_description Change appliance service_id int(10);
ALTER TABLE accessories_product_description Change created_date create_date timestamp NOT NULL DEFAULT current_timestamp();
ALTER TABLE accessories_product_description ADD update_date datetime DEFAULT NULL AFTER create_date;

 -- ghanshyam 24-02-2020----------------------------------------

CREATE TABLE IF NOT EXISTS `sf_payment_hold_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_center_id` int(11) DEFAULT NULL,
  `payment_hold_reason` text NOT NULL,
  `agent_id` int(10) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
  
ALTER TABLE `sf_payment_hold_reason`
  ADD CONSTRAINT `FK_Service_center_id` FOREIGN KEY (`service_center_id`) REFERENCES `service_centres` (`id`);

---------------------------------------------

--Ankit Bhatt 2020-02-20
update `account_holders_bank_details` set ifsc_code = upper(ifsc_code);
--Gorakh 24-02-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES ('Partner', 'Search WH Stock By Part Number', NULL, 'partner/inventory/inventory_stocks_on_warhouse', '2', '148', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '2018-06-21 12:28:29');
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, '247Around', 'Search WH Stock By Part Number', NULL, 'employee/inventory/search_inventory_stock_by_part_number_on_wh', '0', '89', 'accountmanager,admin,closure,developer,inventory_manager,regionalmanager', 'main_nav', '1', '2018-06-05 11:00:43');

-- Kajal 25-02-2020
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, '247Around', 'SF Accessories Invoice', NULL, 'employee/accessories/sf_accessories_invoice', '3', '69', 'accountant,accountmanager,admin,closure,developer', 'main_nav', '1', CURRENT_TIMESTAMP);
--- Whatsapp Seeting --
INSERT INTO `engineer_configs` (`id`, `configuration_type`, `config_value`, `description`, `app_version`, `groups`, `update_date`, `create_date`) VALUES (NULL, 'send_whatsapp', '1', 'This is set 1 if want to send whatsaap', NULL, NULL, '2020-02-13 13:33:33', '2020-02-12 05:12:09');
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `is_exception_for_length`, `create_date`) VALUES (NULL, 'send_complete_whatsapp_number_tag', 'Your %s %s completed (%s). Enjoyed Service? Yes, miss call on 01140849145. If not, 01140849146. 247Around, %s Service Partner', NULL, '1', '1', '2019-04-02 04:51:44');
ALTER TABLE `engineer_details` ADD `installed` INT(4) NOT NULL DEFAULT '0' AFTER `device_firebase_token`;

--Ankit Bhatt 2020-02-24
INSERT INTO `variable_charges_type` (`type`, `description`, `hsn_code`, `gst_rate`, `is_fixed`, `updated_date`, `created_date`) VALUES ('opencell-ledbar-charges-fixed', 'Open Cell & Led Bar Charges', '998715', '18', '0', '2018-12-03 00:00:00', '2018-12-03 00:00:00');

insert into vendor_partner_variable_charges(entity_type, entity_id, charges_type, fixed_charges, percentage_charge, validity_in_month, status, create_date, update_date) values('partner', 247130, 4, 10, 0, 0, 1, now(), now());

-- Kajal 25-02-2020
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, '247Around', 'SF Accessories Invoice', NULL, 'employee/accessories/sf_accessories_invoice', '3', '69', 'accountant,accountmanager,admin,developer', 'main_nav', '1', CURRENT_TIMESTAMP);

ALTER TABLE `entity_login_table` ADD `device_firebase_token` TEXT NULL DEFAULT NULL AFTER `device_id`;

--Ankit 15-01-2019
ALTER TABLE spare_parts_details ADD COLUMN consumption_remarks text NULL DEFAULT NULL AFTER consumed_part_status_id;


--Ankit Bhatt 2020-02-28
CREATE TABLE IF NOT EXISTS `bill_to_partner_opencell` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spare_id` int(11) NOT NULL,
  `invoice_id` varchar(128) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2),
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ;
		
		
ALTER TABLE bill_to_partner_opencell
ADD CONSTRAINT unique_open_cell UNIQUE (spare_id,invoice_id);

--- Whatsapp Seeting --
INSERT INTO `engineer_configs` (`id`, `configuration_type`, `config_value`, `description`, `app_version`, `groups`, `update_date`, `create_date`) VALUES (NULL, 'send_whatsapp', '1', 'This is set 1 if want to send whatsaap', NULL, NULL, '2020-02-13 13:33:33', '2020-02-12 05:12:09');
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `is_exception_for_length`, `create_date`) VALUES (NULL, 'send_complete_whatsapp_number_tag', 'Your %s %s completed (%s). Enjoyed Service? Yes, miss call on 01140849145. If not, 01140849146. 247Around, %s Service Partner', NULL, '1', '1', '2019-04-02 04:51:44');
ALTER TABLE `engineer_details` ADD `installed` INT(4) NOT NULL DEFAULT '0' AFTER `device_firebase_token`;

-- Kajal 27-02-2020
ALTER TABLE `courier_company_invoice_details` ADD `sender_city` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `courier_charge`, ADD `sender_state` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `sender_city`, ADD `receiver_city` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `sender_state`, ADD `receiver_state` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `receiver_city`;

-- Prity 02-03-2020
update header_navigation set groups = REPLACE(groups, 'regionalmanager', 'regionalmanager,areasalesmanager');
ALTER TABLE `courier_company_invoice_details` ADD `sender_city` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `courier_charge`, ADD `sender_state` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `sender_city`, ADD `receiver_city` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `sender_state`, ADD `receiver_state` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `receiver_city`;-- Prity 02-03-2020

-- Prity 02-03-2020
update header_navigation set groups = REPLACE(groups, 'regionalmanager', 'regionalmanager,areasalesmanager');


-- Ankit Rajvanshi 17-02-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Detailed Summary Report', NULL, 'employee/booking/get_detailed_summary_report', 2, '80', 'admin,developer,regionalmanager', 'main_nav', 1, '2019-08-02 05:42:02');
  

  --- Abhishek -- 11-03-2020
  INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `is_exception_for_length`, `create_date`) VALUES (NULL, 'send_notification_on_engg_assign', ' Hi %S, is assigned to you . ', 'This is used to send to notification to engg when booking is assigned to him', '1', '0', '2019-08-06 15:14:24');
---Gorakh 11-03-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Download Courier Invoice', NULL, 'employee/inventory/download_courier_invoice', 2, '249', 'accountant,accountmanager,admin,callcenter,closure,developer,inventory_manager,regionalmanager', 'main_nav', 1, '2019-08-06 12:57:33');

  --- Abhishek -- 11-03-2020
  INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `is_exception_for_length`, `create_date`) VALUES (NULL, 'send_notification_on_engg_assign', ' Hi %s, Booking ID- %s is assigned to you . ', 'This is used to send to notification to engg when booking is assigned to him', '1', '0', '2019-08-06 15:14:24');
 
 
---Gorakh 11-03-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Download Courier Invoice', NULL, 'employee/inventory/download_courier_invoice', 2, '249', 'accountant,accountmanager,admin,callcenter,closure,developer,inventory_manager,regionalmanager', 'main_nav', 1, '2019-08-06 12:57:33');
 
--Abhishek 13-03-2020
CREATE TABLE `cron_logs` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `url` TEXT NULL DEFAULT NULL ,  `start_time` VARCHAR(25) NULL DEFAULT NULL ,  `end_time` VARCHAR(25) NULL DEFAULT NULL ,  `remark` TEXT NULL DEFAULT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;
 ALTER TABLE `cron_logs` CHANGE `url` `cron_url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;


--Ghanshyam 17-03-2020-------------------------------------

CREATE TABLE IF NOT EXISTS `district_state_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_code` int(11) NOT NULL,
  `district` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

ALTER TABLE `district_state_mapping` ADD UNIQUE unique_state_district(state_code,district);

insert into district_state_mapping(district,state_code) select distinct india_pincode.district,state_code.state_code from india_pincode inner join state_code on india_pincode.state = state_code.state where india_pincode.district is not null and india_pincode.district!='';

ALTER TABLE `agent_state_mapping`  ADD `district_id` INT NOT NULL DEFAULT '0'  AFTER `state_code`;


ALTER TABLE agent_state_mapping DROP INDEX uk_state_agent;

-------------------------------------------------------------

-- Ankit Rajvanshi 18-03-2020
ALTER TABLE inventory_master_list ADD COLUMN is_defective_required tinyint(1) NOT NULL DEFAULT 0;


--Ghanshyam 19-03-2020---- Bulk Warranty checker partner panel Navigation

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('Partner', 'Bulk Warranty Checker', NULL, 'partner/inventory/bulk_warranty_checker', 2, '148', 'Primary Contact,Area Sales Manager,Booking Manager,Call Center,Warehouse Incharge,Owner', 'main_nav', 1, '2020-03-19 05:14:03');;

---------------------------------------------------------
--Gorakh 24-03-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Upload MSL File', NULL, 'employee/inventory/upload_msl_excel_file', 3, '228', 'accountmanager,admin,closure,developer', 'main_nav', 1, '2020-03-19 07:54:48');

--Ankit Bhatt 2020-03-23
CREATE TABLE `invoice_category` (
  `id` int(11) NOT NULL,
  `category` varchar(64) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `invoice_category` (`id`, `category`, `create_date`, `update_date`) VALUES
(1, 'Service', '2020-03-23 07:46:31', '2020-03-23 07:46:31'),
(2, 'Product', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(3, 'Upcountry', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(4, 'Debit Penalty', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(5, 'Courier', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(6, 'Misc Charge', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(7, 'Packaging Charges', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(9, 'Warehouse Charges', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(10, 'Call Center Charges', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(11, 'Penalty Discount', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(13, 'Credit Penalty', '2020-03-23 07:47:37', '2020-03-23 07:47:37'),
(14, 'Micro Warehouse', '2020-03-23 07:47:37', '2020-03-23 07:47:37');

ALTER TABLE `invoice_category`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `invoice_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `vendor_partner_invoices`  ADD `micro_warehouse_charges` decimal(10,2) NOT NULL DEFAULT 0.00  AFTER `miscellaneous_charges`;
ALTER TABLE `vendor_partner_invoices`  ADD `call_center_charges` decimal(10,2) NOT NULL DEFAULT 0.00  AFTER `micro_warehouse_charges`;

--Ankit Bhatt 2020-03-26
INSERT INTO `invoice_category` (`category`, `create_date`, `update_date`) VALUES
('OPEN CELL', '2020-03-23 07:46:31', CURRENT_TIMESTAMP),
('Annual Charges', '2020-03-23 07:47:37', CURRENT_TIMESTAMP);
 

--Ankit Bhatt 2020-03-27
ALTER TABLE `courier_company_invoice_details`  ADD `small_box_count` int(11) NOT NULL DEFAULT 0  AFTER `box_count`;

CREATE TABLE IF NOT EXISTS `billed_msl_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courier_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `entity_type` varchar(64) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `invoice_id` varchar(64) NOT NULL,
  `rate` decimal(10,2) NOT NULL default 0.00,
  `box_count` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ;
--Abhishek 28-mar-2020--
ALTER TABLE `dealer_details` ADD `device_firebase_token` TEXT NULL DEFAULT NULL AFTER `active`;
ALTER TABLE `dealer_details` ADD `installed` TINYINT(4) NOT NULL DEFAULT '0' AFTER `device_firebase_token`;

INSERT INTO `engineer_configs` (`id`, `configuration_type`, `config_value`, `description`, `app_version`, `groups`, `update_date`, `create_date`) VALUES (NULL, 'dealer_force_upgrade', '0', 'Dealer App upgrade hard or soft', NULL, NULL, '2020-02-13 13:33:33', '2020-02-12 05:12:09');
ALTER TABLE `engineer_configs` ADD UNIQUE(`configuration_type`);


--Ankit 30-Mar-2020
INSERT INTO booking_cancellation_reasons (id, reason, reason_of, show_on_app, create_date) VALUES (NULL, 'RTO Case', 'spare_parts', '0', CURRENT_TIMESTAMP);

---Abhishek ----01-04-2020
CREATE TABLE `boloaaka`.`whatsapp_logs` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `source` VARCHAR(15) NOT NULL ,  `destination` VARCHAR(15) NOT NULL ,  `channel` VARCHAR(50) NOT NULL ,  `direction` VARCHAR(50) NOT NULL ,  `content` TEXT NOT NULL ,  `content_type` VARCHAR(50) NOT NULL ,  `source_profile` VARCHAR(50) NOT NULL ,  `status` VARCHAR(50) NOT NULL ,  `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;
ALTER TABLE `whatsapp_logs` ADD `type` VARCHAR(50) NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `whatsapp_logs` CHANGE `source` `source` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `destination` `destination` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `channel` `channel` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `direction` `direction` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `content` `content` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `content_type` `content_type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `source_profile` `source_profile` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `status` `status` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `whatsapp_logs` ADD `json_response` TEXT NULL DEFAULT NULL AFTER `status`;

ALTER TABLE `whatsapp_logs` ADD `message_type` VARCHAR(50) NULL DEFAULT NULL AFTER `created_on`, ADD `total_cost` VARCHAR(10) NULL DEFAULT NULL AFTER `message_type`, ADD `update_on` VARCHAR(50) NULL DEFAULT NULL AFTER `total_cost`;
ALTER TABLE `whatsapp_logs` CHANGE `update_on` `update_on` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;
 
--Ankit 01-04-2020
ALTER TABLE `courier_company_invoice_details` ADD column is_rto tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `courier_company_invoice_details` ADD column rto_file varchar(255) NULL DEFAULT NULL;
--Ankit Rajvanshi 03-04-2020
INSERT INTO booking_cancellation_reasons (id, reason, reason_of, show_on_app, create_date) VALUES (NULL, 'Part Not Consumed', 'spare_parts', '0', CURRENT_TIMESTAMP);

-- Prity 03-04-2020
-- Other data correction queries are attached in Task : CRM-5926
ALTER TABLE warranty_plan_model_mapping
ADD CONSTRAINT uk_plan_model UNIQUE (plan_id,model_id);

update warranty_plans set service_id = 46 where service_id = 0 and plan_description like '%led%';
update warranty_plans set service_id = 37 where service_id = 0 and plan_description like '%ref%';
update warranty_plans set service_id = 50 where service_id = 0 and plan_description like '% AC%';
update warranty_plans set service_id = 28 where service_id = 0 and plan_description like '%WM%';
update warranty_plans set service_id = 46 where service_id = 0 and plan_description like '%CTV%';
update warranty_plans set service_id = 28 where service_id = 0 and plan_description like '%Motor%';
update warranty_plans set service_id = 37 where service_id = 0 and plan_description like '%comp%';
UPDATE `warranty_plans` SET `service_id` = '37' WHERE (`plan_id` = '195');
UPDATE `warranty_plans` SET `service_id` = '28' WHERE (`plan_id` = '196');
UPDATE `warranty_plans` SET `service_id` = '50' WHERE (`plan_id` = '197');
UPDATE `warranty_plans` SET `service_id` = '46' WHERE (`plan_id` = '70');

ALTER TABLE warranty_plans
ADD CONSTRAINT uk_plan_partner_service UNIQUE (plan_name,plan_description,service_id,partner_id);

-- Ankit Rajvanshi 06-04-2020
INSERT INTO `partner_booking_status_mapping` (`partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES
(247034, 'Pending', 'Spare Parts Shipped by Warehouse', 'Booking In Progress', 'Spare Parts Shipped by Warehouse', 'Vendor', 'Acknowledge the Received Part', '0000-00-00 00:00:00'),
(247001, 'Pending', 'Spare Parts Shipped by Warehouse', 'Booking In Progress', 'Spare Parts Shipped by Warehouse', 'Vendor', 'Acknowledge the Received Part', '0000-00-00 00:00:00');
 

-- Prity Sharma 06-04-2020
-- 73 Branch
CREATE TABLE `zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zone` varchar(25) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO `zones` (`zone`) VALUES ('North');
INSERT INTO `zones` (`zone`) VALUES ('South');
INSERT INTO `zones` (`zone`) VALUES ('East');
INSERT INTO `zones` (`zone`) VALUES ('West');

ALTER TABLE state_code add column zone_id int NOT NULL;

update state_code set zone_id = '1' where state_code = '1';
update state_code set zone_id = '1' where state_code = '2';
update state_code set zone_id = '1' where state_code = '3';
update state_code set zone_id = '1' where state_code = '4';
update state_code set zone_id = '1' where state_code = '6';
update state_code set zone_id = '1' where state_code = '7';
update state_code set zone_id = '1' where state_code = '8';
update state_code set zone_id = '1' where state_code = '9';
update state_code set zone_id = '3' where state_code = '10';
update state_code set zone_id = '3' where state_code = '11';
update state_code set zone_id = '3' where state_code = '12';
update state_code set zone_id = '3' where state_code = '13';
update state_code set zone_id = '3' where state_code = '14';
update state_code set zone_id = '3' where state_code = '15';
update state_code set zone_id = '3' where state_code = '16';
update state_code set zone_id = '3' where state_code = '17';
update state_code set zone_id = '3' where state_code = '18';
update state_code set zone_id = '3' where state_code = '19';
update state_code set zone_id = '3' where state_code = '20';
update state_code set zone_id = '4' where state_code = '22';
update state_code set zone_id = '4' where state_code = '23';
update state_code set zone_id = '4' where state_code = '24';
update state_code set zone_id = '4' where state_code = '25';
update state_code set zone_id = '4' where state_code = '26';
update state_code set zone_id = '4' where state_code = '27';
update state_code set zone_id = '2' where state_code = '28';
update state_code set zone_id = '2' where state_code = '29';
update state_code set zone_id = '4' where state_code = '30';
update state_code set zone_id = '2' where state_code = '31';
update state_code set zone_id = '2' where state_code = '32';
update state_code set zone_id = '2' where state_code = '33';
update state_code set zone_id = '2' where state_code = '34';
update state_code set zone_id = '3' where state_code = '35';
update state_code set zone_id = '3' where state_code = '21';
update state_code set zone_id = '1' where state_code = '5';
update state_code set zone_id = '2' where state_code = '36';

--Ankit Rajvanshi 07-04-2020
UPDATE `header_navigation` SET `title` = 'Shipped Spare By Warehouse' WHERE `header_navigation`.`id` = 136;

-- Prity Sharma 08-04-2020
-- 73 Branch
UPDATE `rm_region_mapping` set region = 1 WHERE rm_id = '36';
UPDATE `rm_region_mapping` set region = 2 WHERE rm_id = '10146';
UPDATE `rm_region_mapping` set region = 3 WHERE rm_id = '38';
UPDATE `rm_region_mapping` set region = 4 WHERE rm_id = '24';
ALTER TABLE  rm_region_mapping change column region zone_id int NOT NULL ;
RENAME TABLE rm_region_mapping TO rm_zone_mapping;
ALTER TABLE `rm_zone_mapping` ADD CONSTRAINT `FK_rm_zone_mapping_zone` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`);

--Ankit Bhatt 2020-04-08
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'OOW Invoice By Partner', NULL, 'employee/invoice/partner_oow_invoice', 3, '63', 'accountmanager,admin,closure,developer', 'main_nav', 1, CURRENT_TIMESTAMP);


--Abhishek --08-04-2020
CREATE TABLE `247around`.`reassign_bookings` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `booking_details_id` INT(11) NOT NULL ,  `reason` INT(11) NULL DEFAULT NULL ,  `remark` VARCHAR(500) NULL DEFAULT NULL ,  `old_sf` INT(11) NULL DEFAULT NULL ,  `new_sf` INT(11) NULL DEFAULT NULL ,  `rm_flag` INT(4) NULL DEFAULT '0' ,  `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,  `updated_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;
 ALTER TABLE `reassign_bookings` CHANGE `rm_flag` `rm_responsible_flag` INT(4) NULL DEFAULT '0';
 ALTER TABLE `reassign_bookings` CHANGE `created_on` `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
--Gorakh 10-04-2020

CREATE TABLE `courier_serviceable_area` (
  `id` int(11) NOT NULL,
  `courier_company_name` varchar(255) NOT NULL,
  `pincode` varchar(255) NOT NULL,
  `status` TINYINT  NOT NULL DEFAULT 1,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `courier_serviceable_area`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `courier_serviceable_area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
 
---Gorakh 01 Apr 2020
UPDATE `header_navigation` SET `link` = '' WHERE `header_navigation`.`id` = 119;

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Upload MSL File', NULL, 'employee/inventory/upload_msl_excel_file', 2, '119', 'accountant,accountmanager,admin,developer,inventory_manager,regionalmanager', 'main_nav', 1, '2018-10-04 12:08:07');

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Send MSL Via Form', NULL, 'employee/inventory/tag_spare_invoice_send_by_partner', 2, '119', 'accountant,accountmanager,admin,developer,inventory_manager,regionalmanager', 'main_nav', 1, '2018-10-04 12:08:07');

ALTER TABLE `spare_parts_details` ADD `defect_pic` VARCHAR(200) NULL DEFAULT NULL AFTER `approval_entity_type`;

--Ankit Rajvanshi 13-04-2020
INSERT INTO `email_template` (`tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES
('part_to_be_billed', NULL, ' ', NULL, 'ankitr@247around.com', 'ankitr@247around.com', 'ankitr@247around.com', '', '1', '2020-04-13 10:01:27');
--Ankit Bhatt 2020-04-10
ALTER TABLE service_centres ADD COLUMN last_foc_mail_send_date timestamp;

---Ghanshyam 2020-04-13

INSERT INTO `partner_booking_status_mapping` ( `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES ('247001', 'Pending', 'NRN Reverse by Partner', 'NRN Reverse by Partner', 'NRN Reverse by Partner', 'vendor', 'Visit to Customer', CURRENT_TIMESTAMP);


---Abhishek -- 15-04-2020
ALTER TABLE `engineer_details` ADD `edu_qualification` VARCHAR(255) NULL DEFAULT NULL AFTER `bank_holder_name`, ADD `pro_qualification` VARCHAR(255) NULL DEFAULT NULL AFTER `edu_qualification`, ADD `overall_exp` VARCHAR(15) NULL DEFAULT NULL AFTER `pro_qualification`, ADD `around_exp` VARCHAR(15) NULL DEFAULT NULL AFTER `overall_exp`;
 

---Ghanshyam 2020-04-15
ALTER TABLE `courier_company_invoice_details` ADD `courier_pod_file` VARCHAR(255) NULL DEFAULT NULL AFTER `delivered_date`;

---Abhishek -- 15-04-2020
ALTER TABLE `engineer_details` ADD `edu_qualification` VARCHAR(255) NULL DEFAULT NULL AFTER `bank_holder_name`, ADD `pro_qualification` VARCHAR(255) NULL DEFAULT NULL AFTER `edu_qualification`, ADD `overall_exp` VARCHAR(15) NULL DEFAULT NULL AFTER `pro_qualification`, ADD `around_exp` VARCHAR(15) NULL DEFAULT NULL AFTER `overall_exp`;

---Ghanshyam 2020-04-15
ALTER TABLE `courier_company_invoice_details` ADD `courier_pod_file` VARCHAR(255) NULL DEFAULT NULL AFTER `delivered_date`;
--Ankit Bhatt 2020-04-15
insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('partner_service_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category='Cash' and amount_collected_paid > 0 and vendor_partner='partner' ;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category='Cash' and amount_collected_paid > 0 and vendor_partner='partner';", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('partner_buyback_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and type='Buyback' and sub_category = 'Sale' and vendor_partner='partner' ;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and type='Buyback' and sub_category = 'Sale' and vendor_partner='partner';", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('partner_spare_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category in('MSL New Part Return', 'OOW New Part Return', 'MSL Defective Return') and amount_collected_paid > 0 and vendor_partner='partner' ;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category in('MSL New Part Return', 'OOW New Part Return', 'MSL Defective Return') and amount_collected_paid > 0 and vendor_partner='partner';", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('partner_other_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category = 'CRM' and vendor_partner='partner' ;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category = 'CRM' and vendor_partner='partner';", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('SF_service_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category='Commission' and amount_collected_paid > 0 and vendor_partner='vendor' ;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category='Commission' and amount_collected_paid > 0 and vendor_partner='vendor';", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('SF_buyback_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and type='Buyback' and sub_category = 'Sale' and vendor_partner='vendor' ;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and type='Buyback' and sub_category = 'Sale' and vendor_partner='vendor';", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('SF_spare_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category = 'Out-of-Warranty' and amount_collected_paid > 0 and vendor_partner='vendor' ;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category = 'Out-of-Warranty' and amount_collected_paid > 0 and vendor_partner='vendor';", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('SF_other_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category = 'CRM' and vendor_partner='vendor' ;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and sub_category = 'CRM' and vendor_partner='vendor';", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('partner_CN_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and vendor_partner='partner' and (type = 'Credit Note' or type = 'CreditNote');", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and vendor_partner='partner' and (type = 'Credit Note' or type = 'CreditNote');", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('SF_CN_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and vendor_partner='vendor' and (type = 'Credit Note' or type = 'CreditNote');", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and vendor_partner='vendor' and (type = 'Credit Note' or type = 'CreditNote');", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('partner_DN_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and vendor_partner='partner' and (type = 'Debit Note' or type = 'DebitNote');", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and vendor_partner='partner' and (type = 'Debit Note' or type = 'DebitNote');", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('SF_DN_invoice_this_month', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and vendor_partner='vendor' and (type = 'Debit Note' or type = 'DebitNote');", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01') and vendor_partner='vendor' and (type = 'Debit Note' or type = 'DebitNote');", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('payment_through_bank_today', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `bank_transactions` WHERE transaction_date >= CURDATE() and credit_amount is not null ;", "SELECT IFNULL(sum(credit_amount), 0) as count FROM `bank_transactions` WHERE transaction_date >= CURDATE() and credit_amount is not null;", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('payment_through_paytm_today', 'No of payments', 'Total Amount',"SELECT count(id) as count FROM `vendor_partner_invoices` WHERE invoice_date >= CURDATE()  and sub_category = 'Pre-paid(PG)' and amount_collected_paid > 0;", "SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` WHERE invoice_date >= CURDATE() and sub_category = 'Pre-paid(PG)' and amount_collected_paid > 0;", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('Total_GST_Credit_Hold_Amount_This_Month', 'Total Amount', '',"SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` where sub_category = 'GST Credit Note' and invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01');", "", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);

insert into query_report(main_description, query1_description, query2_description, query1, query2, role, priority, type, active, create_date)
values('Total_GST_Debit_Hold_Amount_This_Month', 'Total Amount', '',"SELECT IFNULL(sum(total_amount_collected - cgst_tax_amount - sgst_tax_amount - igst_tax_amount), 0) as count FROM `vendor_partner_invoices` where sub_category = 'GST Debit Note' and invoice_date >= DATE_FORMAT(CURRENT_TIMESTAMP ,'%Y-%m-01');", "", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);


-- Warehouse menu on admin crm Ankit Rajvanshi 20-04-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Warehouse', NULL, NULL, 1, NULL, 'inventory_manager', 'main_nav', 1, '2017-12-29 06:08:44');
	
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Warehouse Task', NULL, 'service_center/inventory', 1, NULL, 'inventory_manager', 'main_nav', 1, '2019-02-28 12:06:20');	

ALTER TABLE employee ADD COLUMN warehouse_id int(11) NULL DEFAULT NULL;
---Gorakh 20-04-02020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('Partner', 'Search Docket Number', NULL, 'partner/search_docket_number', 2, '148', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', 1, '2018-06-21 06:58:29');

ALTER TABLE employee ADD COLUMN warehouse_id int(11) NULL DEFAULT 15;

-- Prity 17-04-2020 (73 Branch)
CREATE TABLE review_questionare (
  q_id int(11) NOT NULL AUTO_INCREMENT,
  question varchar(500) NOT NULL,
  form int NOT NULL COMMENT '1 => booking cancellation, 2 => booking completion',
  panel int NOT NULL COMMENT '1 => Admin, 2 => Partner',
  sequence int(11) NOT NULL DEFAULT 1,
  active tinyint(1) NOT NULL DEFAULT 1,
  create_date timestamp NOT NULL DEFAULT current_timestamp(),
  created_by int NOT NULL,
  PRIMARY KEY (q_id))
  ENGINE = InnoDB;  
						
CREATE TABLE review_request_type_mapping (
  id int(11) NOT NULL AUTO_INCREMENT,
  q_id int(11) NOT NULL,
  request_type_id int(11) NOT NULL,
  active tinyint(1) NOT NULL DEFAULT 1,
  create_date timestamp NOT NULL DEFAULT current_timestamp(),
  created_by int NOT NULL,
  PRIMARY KEY (id),
  KEY fk_ques_review_mapping (q_id),
  KEY fk_request_type_review_mapping (request_type_id),
  CONSTRAINT fk_request_type_question_mapping FOREIGN KEY (q_id) REFERENCES review_questionare (q_id),
  CONSTRAINT fk_request_type_request_mapping FOREIGN KEY (request_type_id) REFERENCES request_type (id))
  ENGINE=InnoDB AUTO_INCREMENT=1;	

  
CREATE TABLE review_questionare_checklist (
  checklist_id int(11) NOT NULL AUTO_INCREMENT,
  q_id int(11) NOT NULL,
  answer varchar(500) NOT NULL,
  active tinyint(1) NOT NULL DEFAULT 1,
  create_date timestamp NOT NULL DEFAULT current_timestamp(),
  created_by int NOT NULL,
  PRIMARY KEY (checklist_id),
  KEY fk_ques_checklist_mapping (q_id),
  CONSTRAINT fk_ques_checklist_mapping FOREIGN KEY (q_id) REFERENCES review_questionare (q_id))
  ENGINE=InnoDB AUTO_INCREMENT=1;  

CREATE TABLE review_booking_checklist (
  id int(11) NOT NULL AUTO_INCREMENT,
  booking_id int(11) NOT NULL,
  q_id int(11) NOT NULL,
  checklist_id int(11) NULL DEFAULT NULL,
  remarks varchar(500) NULL DEFAULT NULL,
  active tinyint(1) NOT NULL DEFAULT 1,
  create_date timestamp NOT NULL DEFAULT current_timestamp(),
  created_by int NOT NULL,
  PRIMARY KEY (id),
  KEY fk_booking_checklist_mapping (booking_id),
  KEY fk_booking_ques_checklist_mapping (q_id),
  KEY fk_booking_checklist_checklist_mapping (checklist_id),  
  CONSTRAINT fk_booking_checklist_mapping FOREIGN KEY (booking_id) REFERENCES booking_details (id),
  CONSTRAINT fk_booking_ques_checklist_mapping FOREIGN KEY (q_id) REFERENCES review_questionare (q_id),
  CONSTRAINT fk_booking_checklist_checklist_mapping FOREIGN KEY (checklist_id) REFERENCES review_questionare_checklist (checklist_id))
  ENGINE=InnoDB AUTO_INCREMENT=1; 
ALTER TABLE service_centres ADD COLUMN last_foc_mail_send_date timestamp;



ALTER TABLE `spare_parts_details` ADD `defect_pic` VARCHAR(200) NULL DEFAULT NULL AFTER `approval_entity_type`, ADD `symptom` INT(11) NULL DEFAULT NULL AFTER `defect_pic`;
 
---Abhishek -- 15-04-2020
ALTER TABLE `engineer_details` ADD `edu_qualification` VARCHAR(255) NULL DEFAULT NULL AFTER `bank_holder_name`, ADD `pro_qualification` VARCHAR(255) NULL DEFAULT NULL AFTER `edu_qualification`, ADD `overall_exp` VARCHAR(15) NULL DEFAULT NULL AFTER `pro_qualification`, ADD `around_exp` VARCHAR(15) NULL DEFAULT NULL AFTER `overall_exp`;

-- Ankit Rajvanshi 20-04-2020
CREATE TABLE non_inventory_partners_part_type (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    partner_id int(11) NOT NULL,
    service_id int(11) NOT NULL,
    inventory_part_type_id int(11) NOT NULL,
    is_defective_required tinyint(1) NOT NULL DEFAULT 0,
    create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    update_date datetime NULL DEFAULT NULL,

    CONSTRAINT fk_non_inventory_partners FOREIGN KEY (partner_id) REFERENCES partners(id),
    CONSTRAINT fk_non_inventory_services FOREIGN KEY (service_id) REFERENCES services(id),
    CONSTRAINT fk_non_inventory_parts_type FOREIGN KEY (inventory_part_type_id) REFERENCES inventory_parts_type(id)
);


---Ghanshyam 2020-04-13
INSERT INTO `partner_booking_status_mapping` ( `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES ('247001', 'Pending', 'NRN Reverse', 'NRN Reverse', 'NRN Reverse', 'vendor', 'Visit to Customer', CURRENT_TIMESTAMP);


-- Prity 21-04-2020
-- 73 Branch
CREATE TABLE `customer_dissatisfactory_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL,
  `active` TINYINT  NOT NULL DEFAULT 1,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE booking_details add column customer_dissatisfactory_reason int NULL DEFAULT NULL AFTER rating_comments; 
-- Prity 23-04-2020
-- 73
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Delay in Engineer Visit');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Delay in Part Supply');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Engineer Not Skilled');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Engineer Behaviour Not good');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('High Repair Charges');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Others');
-- Prity 15-04-2020 (73 Branch)
ALTER TABLE booking_details change column booking_date booking_date_old varchar(100) NOT NULL; 
ALTER TABLE booking_details add column booking_date date NOT NULL AFTER booking_date_old; 
update booking_details set booking_date = DATE_FORMAT(STR_TO_DATE(booking_date_old,'%d-%m-%Y'), '%Y-%m-%d');
ALTER TABLE booking_details change column initial_booking_date initial_booking_date_old varchar(100) NOT NULL; 
ALTER TABLE booking_details add column initial_booking_date date NOT NULL AFTER initial_booking_date_old; 
update booking_details set initial_booking_date = DATE_FORMAT(STR_TO_DATE(initial_booking_date_old,'%d-%m-%Y'), '%Y-%m-%d');

-- Prity 22-04-2020
-- 73 Branch
UPDATE email_template SET template = 'Dear Partner,<br><br>\nGreetings from 247around !!!<br><br>\nPlease provide your bank details (Cheque / Passbook Front Page) to your Area Sales Manager so that invoice payment can happen on time.<br><br>\nRegards,<br>\nTeam 247around' WHERE email_template.id = 37;
-- Ankit Rajvanshi 22-04-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'MSL Security Amount', NULL, 'employee/invoice/get_msl_security_amount_list', 3, '69', 'admin,developer', 'main_nav', 1, CURRENT_TIMESTAMP);

-- Prity 29-04-2020
-- 73
ALTER TABLE sf_not_exist_booking_details ADD COLUMN asm_id INT NULL DEFAULT NULL AFTER rm_id;
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Delay in Engineer Visit');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Delay in Part Supply');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Engineer Not Skilled');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Engineer Behaviour Not good');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('High Repair Charges');
INSERT INTO `customer_dissatisfactory_reasons` (`reason`) VALUES ('Others');

-- Ankit Rajvanshi 23-04-2020
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `is_exception_for_length`, `create_date`) VALUES (NULL, 'booking_cancel_otp_sms', 'Dear Customer,\r\n\r\nYour one time password for booking cancellation is %s.', NULL, '1', '0', CURRENT_TIMESTAMP), (NULL, 'booking_reschedule_otp_sms', 'Dear Customer,\r\n\r\nYour one time password for booking reschedule is %s.', NULL, '1', '0', CURRENT_TIMESTAMP);

ALTER TABLE `service_centre_charges` ADD `partner_spare_extra_charge` INT(11) NOT NULL DEFAULT '0' AFTER `partner_net_payable`;
ALTER TABLE `booking_unit_details` ADD `partner_spare_extra_charge` DECIMAL(2) NOT NULL DEFAULT '0' AFTER `partner_paid_basic_charges`;
--Gorakh 01-04-2020
ALTER TABLE `spare_parts_details` ADD `defactive_part_return_to_partner_from_wh_date_by_courier_api` DATETIME NULL DEFAULT NULL AFTER `symptom`;
---Ghanshyam 2020-04-15
ALTER TABLE `courier_company_invoice_details` ADD `courier_pod_file` VARCHAR(255) NULL DEFAULT NULL AFTER `delivered_date`;
--Ankit Bhatt 2020-04-27
update header_navigation set groups = concat(groups, ',regionalmanager') where id = 69;

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'FNF Amount Payment List', NULL, 'employee/invoice/get_security_amount_list', 3, '69', 'admin,developer,regionalmanager', 'main_nav', 1, CURRENT_TIMESTAMP);

-- Prity 29-04-2020
-- 73
ALTER TABLE sf_not_exist_booking_details ADD COLUMN asm_id INT NULL DEFAULT NULL AFTER rm_id;

-- Ankit Rajvanshi 01-05-2020
ALTER TABLE collateral ADD COLUMN youtube_link text NULL DEFAULT NULL;

-- Ankit Rajvanshi 30-04-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('Partner', 'Received Spare By Warehouse ', NULL, 'partner/received_parts_by_wh', 2, '132', 'Primary Contact,Area Sales Manager,Booking Manager,Owner, Warehouse Incharge', 'main_nav', 1, '2018-06-11 03:19:29');

-- Prity 04-05-2020
-- 73
CREATE TABLE `booking_amount_differences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `total_amount_by_sf` decimal(10,2) NOT NULL,
  `total_amount_actual` decimal(10,2) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_booking_amount_differences` (`booking_id`),
  CONSTRAINT `fk_booking_amount_differences` FOREIGN KEY (`booking_id`) REFERENCES `booking_details` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;



    --Sarvendra 04-05-2020 - CRM-6175
    --74
    ALTER TABLE `boloaaka`.`service_centres` 
    ADD COLUMN `is_approved` INT(1) NULL DEFAULT 0 AFTER `auth_certificate_validate_year`;

    Insert INTO boloaaka.header_navigation (entity_type,title,title_icon,link,level,parent_ids,groups,nav_type,is_active)
    values('247Around','Unapproved Service Centers','','employee/vendor/unapprovered_service_centers',
    2,36,'admin,developer,regionalmanager',
    'main_nav',1);

values('Total_GST_Hold_Amount', 'Total Amount', '',"SELECT IFNULL(sum(cgst_tax_amount + sgst_tax_amount + igst_tax_amount), 0) as count FROM `vendor_partner_invoices` ;", "", 'accountant', 1, 'service', 1, CURRENT_TIMESTAMP);


---Ghanshyam 2020-05-07
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES ('partner', 'Search Docket', NULL, 'partner/search_docket', '2', '148', 'Primary Contact,Area Sales Manager,Booking Manager,Call Center,Warehouse Incharge,Owner', 'main_nav', '1', CURRENT_TIMESTAMP);
---Ankit Bhatt ---
CREATE TABLE `challan_item_details` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(64) NOT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `settle_qty` int(11) DEFAULT '0',
  `is_settle` int(1) DEFAULT '0',
  `spare_id` int(11) NOT NULL,
  `description` varchar(128) NOT NULL,
  `product_or_services` varchar(28) DEFAULT NULL,
  `hsn_code` varchar(28) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT '0',
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `taxable_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cgst_tax_rate` decimal(10,2) DEFAULT NULL,
  `cgst_tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst_tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst_tax_rate` decimal(10,2) DEFAULT NULL,
  `igst_tax_amount` decimal(10,2) DEFAULT '0.00',
  `igst_tax_rate` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `from_gst_id` int(11) DEFAULT NULL,
  `to_gst_id` int(11) DEFAULT NULL,
  `is_invoice_generated` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - invoice not generated,1 - invoice_generated, 2 - challan rejected',
  `partner_id` int(11) NOT NULL,
  `challan_no` varchar(24) NOT NULL,
  `state_code` int(11) DEFAULT NULL,
  `from_address` varchar(512) DEFAULT NULL,
  `to_address` varchar(512) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `challan_item_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_id` (`partner_id`),
  ADD KEY `spare_id` (`spare_id`),
  ADD KEY `challan_no` (`challan_no`);
  ALTER TABLE `challan_item_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Sarvendra 05-05-2020 - CRM-5471 - 74
ALTER TABLE `boloaaka`.`service_centres` 
ADD COLUMN `agreement_email_sent` INT(1) NOT NULL DEFAULT 0 AFTER `is_approved`,
ADD COLUMN `agreement_secret_code` VARCHAR(10) NULL DEFAULT NULL AFTER `agreement_email_sent`,
ADD COLUMN `agreement_ip_address` VARCHAR(20) NULL DEFAULT NULL AFTER `agreement_secret_code`,
ADD COLUMN `agreement_sign_datetime` TIMESTAMP NULL DEFAULT '0000-00-00 00:00:00' AFTER `agreement_ip_address`,
ADD COLUMN `agreement_email_sent_date` DATE NOT NULL DEFAULT '0000-00-00' AFTER `agreement_sign_datetime`,
ADD COLUMN `agreement_email_reminder_date` DATE NOT NULL DEFAULT '0000-00-00' AFTER `agreement_email_sent_date`,
ADD COLUMN `agreement_file_name` TEXT NULL DEFAULT NULL AFTER `agreement_email_reminder_date`,
ADD COLUMN `is_sf_agreement_signed` INT(1) NOT NULL DEFAULT 0 AFTER `agreement_file_name`;

insert into boloaaka.email_template (tag,subject,template,booking_id,`from`,`to`,cc,bcc,active)
values('agreement_email_template','',
'','','booking@247around.com','','','accounts@247around.com',1);

ALTER TABLE `inventory_invoice_mapping` ADD `invoice_or_challan` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = invoice, 0 = challan' AFTER `settle_qty`;
  
ALTER TABLE `india_pincode` ADD `latitude` VARCHAR(20) NULL DEFAULT NULL AFTER `state`, ADD `longitude` VARCHAR(20) NULL DEFAULT NULL AFTER `latitude`;

-- Prity 15-05-2020
-- 73Branch
UPDATE `partner_summary_report_mapping` SET `sub_query` = '(CASE WHEN (booking_details.current_status = \'Cancelled\') THEN b_cr.reason ELSE GROUP_CONCAT(ssba_cr.reason) END) AS \'Cancellation Remarks\'' WHERE (`id` = '37');

-- Sarvendra CRM-5967
--73 Branch
ALTER TABLE `boloaaka`.`247around_nrn_details` 
ADD COLUMN `service_id` INT(11) NOT NULL AFTER `vendor_reversal_category`;

ALTER TABLE `boloaaka`.`247around_nrn_details` 
ADD COLUMN `brand` VARCHAR(255) NOT NULL AFTER `service_id`;

ALTER TABLE `boloaaka`.`247around_nrn_details` 
ADD COLUMN `partner_type` VARCHAR(255) NOT NULL AFTER `brand`;

ALTER TABLE `boloaaka`.`247around_nrn_details` 
ADD COLUMN `partner_id` VARCHAR(255) NOT NULL AFTER `partner_type`;

ALTER TABLE `boloaaka`.`247around_nrn_details` 
CHANGE COLUMN `booking_date` `booking_date` DATE NULL DEFAULT NULL COMMENT '	' ,
CHANGE COLUMN `tr_reporting_date` `tr_reporting_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `purchase_date` `purchase_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `make` `make` TEXT NULL DEFAULT NULL ,
CHANGE COLUMN `customer_name` `customer_name` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `state` `state` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `branch` `branch` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `approval_rejection_date` `approval_rejection_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `hdpl_invoice_no` `hdpl_invoice_no` VARCHAR(45) NULL DEFAULT NULL ,
CHANGE COLUMN `hdpl_point` `hdpl_point` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `vendor_warranty_expire_month` `vendor_warranty_expire_month` VARCHAR(10) NULL DEFAULT NULL ,
CHANGE COLUMN `action_plan` `action_plan` ENUM('Customer + Sub-dealer', 'Distributor') NULL DEFAULT NULL ,
CHANGE COLUMN `asf_distributor_pincode` `asf_distributor_pincode` VARCHAR(6) NULL DEFAULT NULL ,
CHANGE COLUMN `control_no` `control_no` VARCHAR(45) NULL DEFAULT NULL ,
CHANGE COLUMN `replacement_status` `replacement_status` ENUM('Dispatched', 'Pending', 'NA') NULL DEFAULT NULL ,
CHANGE COLUMN `replacement_with_accessory` `replacement_with_accessory` ENUM('Yes', 'No', 'NA') NULL DEFAULT NULL ,
CHANGE COLUMN `defective_receiving_date` `defective_receiving_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `tr_status` `tr_status` ENUM('Open', 'Close') NULL DEFAULT NULL ,
CHANGE COLUMN `replacement_awb_no` `replacement_awb_no` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `replacement_courier_name` `replacement_courier_name` VARCHAR(255) NULL DEFAULT NULL ,
CHANGE COLUMN `replacement_dispatch_date` `replacement_dispatch_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `replacement_delivery_date` `replacement_delivery_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `category_after_inspection_date` `category_after_inspection_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `final_pdi_category_after_inspection_date` `final_pdi_category_after_inspection_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `final_defective_status_date` `final_defective_status_date` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `vendor_reversal_date` `vendor_reversal_date` DATE NULL DEFAULT NULL ;

-- Sarvendra CRM-3450
INSERT INTO boloaaka.email_template (`tag`,`subject`,`template`,`booking_id`,`from`,`to`,`cc`,`bcc`,`active`)
values('sf_permanent_on_off_is_micro_wh','',
'Dear Inventory Team/ Accounts Team/ %s,<br><br> <b> %s </b> Service Franchise is Permanently <b> %s </b> now by %s.<br><br> Thanks<br> 247Around Team',
'','booking@247around.com','warehouse_noida@247around.com,accounts@247around.com','','',1);

-- Prity
-- 73 Release
ALTER TABLE booking_details CHANGE COLUMN cancellation_reason cancellation_reason_old varchar(100) DEFAULT NULL;
ALTER TABLE booking_details ADD COLUMN `cancellation_reason` int(11) DEFAULT NULL AFTER cancellation_reason_old;
ALTER TABLE booking_details ADD CONSTRAINT `fk_bd_bcr` FOREIGN KEY (`cancellation_reason`) REFERENCES `booking_cancellation_reasons` (`id`);
UPDATE booking_details JOIN booking_cancellation_reasons ON (booking_details.cancellation_reason_old = booking_cancellation_reasons.reason) set booking_details.cancellation_reason = booking_cancellation_reasons.id;  
ALTER TABLE service_center_booking_action CHANGE COLUMN cancellation_reason cancellation_reason_old varchar(100) DEFAULT NULL;
ALTER TABLE service_center_booking_action ADD COLUMN `cancellation_reason` int(11) DEFAULT NULL AFTER cancellation_reason_old;
ALTER TABLE service_center_booking_action ADD CONSTRAINT `fk_scba_bcr` FOREIGN KEY (`cancellation_reason`) REFERENCES `booking_cancellation_reasons` (`id`);
UPDATE service_center_booking_action JOIN booking_cancellation_reasons ON (service_center_booking_action.cancellation_reason_old = booking_cancellation_reasons.reason) set service_center_booking_action.cancellation_reason = booking_cancellation_reasons.id;  

-- Sarvendra CRM-6281
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) 
VALUES ('247Around', 'SF Authorization Certificate', NULL, 'employee/SF_authorization_certificate', '1', '', 'accountant,accountmanager,admin,callcenter,closure,developer,inventory_manager,regionalmanager,areasalesmanager', 'main_nav', '1', CURRENT_TIMESTAMP);

    --Sarvendra CRM-6107
    CREATE TABLE `boloaaka`.`sf_auth_certificate_setting` (
      `id` INT NOT NULL AUTO_INCREMENT,
      `letter_pad_img_name` TEXT NULL,
      `stamp_img_name` TEXT NULL,
      `sign_img_name` VARCHAR(45) NULL,
      `s3_directory_name` TEXT NULL,
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      `modified_at` TIMESTAMP NULL,
      PRIMARY KEY (`id`));

    INSERT INTO `boloaaka`.`sf_auth_certificate_setting` (`letter_pad_img_name`, `stamp_img_name`, `sign_img_name`, `s3_directory_name`) VALUES ('247_letter_head_sample.jpg', 'stamp_sample.png', 'anujsign_sample.jpg', 'authorization_certificate');


-- Raman
-- 22-May-2020 CRM-6286
UPDATE `partner_summary_report_mapping` SET `sub_query` = 'DATE_FORMAT(STR_TO_DATE(booking_details.booking_date, \"%Y-%m-%d\"), \"%d/%c/%Y\") As \"Current Booking Date\"' WHERE `partner_summary_report_mapping`.`id` = 19;

UPDATE `partner_summary_report_mapping` SET `sub_query` = 'DATE_FORMAT(STR_TO_DATE(booking_details.initial_booking_date, \"%Y-%m-%d\"), \"%d/%c/%Y\") As \"First Booking Date\"' WHERE `partner_summary_report_mapping`.`id` = 20;

-- Ankit Rajvanshi 73 branch
INSERT INTO `partner_summary_report_mapping` (`Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES
('Symptom', 'creation_symptom.symptom as \'Booking Symptom\'', 1, '', 1, 51),
('SF Symptom', 'completion_symptom.symptom as \'Completion Symptom\'', 1, '', 1, 52),
('Defect', 'defect.defect AS \'Defect\'', 1, '', 1, 53),
('Solution', 'symptom_completion_solution.technical_solution AS \'Solution\'', 1, '', 1, 54);

ALTER TABLE collateral ADD COLUMN youtube_link text NULL DEFAULT NULL;

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('Partner', 'Received Spare By Warehouse ', NULL, 'partner/received_parts_by_wh', 2, '132', 'Primary Contact,Area Sales Manager,Booking Manager,Owner, Warehouse Incharge', 'main_nav', 1, '2018-06-11 03:19:29');

CREATE TABLE booking_unit_details_invoice_process (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    booking_unit_details_id int(11) NOT NULL,
    dashboard_section_id varchar(50) NOT NULL,
    is_processed tinyint(1) NOT NULL DEFAULT 0,
    create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
);

ALTER TABLE booking_details change column booking_date booking_date date NULL DEFAULT NULL;
UPDATE booking_details set booking_date = NULL where booking_date = '0000-00-00';

-- Raman 73 
 -- 28 May
UPDATE `partner_summary_report_mapping` SET `sub_query` = '(CASE WHEN booking_details.current_status = \"Completed\" THEN (CASE WHEN DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,\"%Y-%m-%d\")) < 0 THEN 0 ELSE DATEDIFF(date(booking_details.service_center_closed_date),STR_TO_DATE(booking_details.initial_booking_date,\"%Y-%m-%d\")) END) ELSE \"\" END) as TAT' WHERE `partner_summary_report_mapping`.`id` = 25;


UPDATE `partner_summary_report_mapping` SET `sub_query` = '(CASE WHEN booking_details.current_status IN (\"Pending\",\"Rescheduled\",\"FollowUp\") THEN DATEDIFF(CURDATE(),STR_TO_DATE(booking_details.initial_booking_date,\"%Y-%m-%d\")) ELSE \"\" END) as Ageing' WHERE `partner_summary_report_mapping`.`id` = 26;

--Gorakh 10-06-2020
ALTER TABLE `courier_tracking_details` CHANGE `checkpoint_status` `checkpoint_status` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;


----Gorakh 07-07-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Upload Courier Serviceable Area File', NULL, 'employee/inventory/upload_courier_serviceable_area_file', 3, '228', 'accountmanager,admin,closure,developer', 'main_nav', 1, '2020-03-19 02:24:48');


-- Raman 74
-- 12-06-2020

UPDATE `partner_summary_report_mapping` SET `sub_query` = 'if(booking_details.booking_date != \'0000-00-00\', DATE_FORMAT(STR_TO_DATE(booking_details.booking_date, \"%Y-%m-%d\"), \"%d/%c/%Y\"),null) As \"Current Booking Date\"' WHERE `partner_summary_report_mapping`.`id` = 19;

UPDATE `partner_summary_report_mapping` SET `sub_query` = 'if(booking_details.initial_booking_date != \'0000-00-00\', DATE_FORMAT(STR_TO_DATE(booking_details.initial_booking_date, \"%Y-%m-%d\"), \"%d/%c/%Y\"),null) As \"First Booking Date\"\n' WHERE `partner_summary_report_mapping`.`id` = 20;
--Gorakh 10-06-2020
ALTER TABLE `courier_tracking_details` CHANGE `checkpoint_status` `checkpoint_status` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

--Gorakh Nath 06-05-2020
CREATE TABLE `personal_used_spare_parts` (
  `id` int(11) NOT NULL,
 `warehouse_id` int(11) NOT NULL,
 `quantity` int(11) DEFAULT NULL,
 `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `personal_used_spare_parts` ADD PRIMARY KEY (`id`);
ALTER TABLE `personal_used_spare_parts`
 MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `personal_used_spare_parts` ADD `inventory_id` INT NOT NULL AFTER `warehouse_id`;
--Gorakh 21-07-2020
INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Add Courier Serviceable area', NULL, 'employee/courier/add_courier_serviceable_area', 2, '172', 'accountant,accountmanager,admin,developer,inventory_manager', 'main_nav', 1, '2018-12-13 05:13:48');

