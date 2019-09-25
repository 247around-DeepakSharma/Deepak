--Abhyay 20/5/2019
ALTER TABLE `service_center_booking_action` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0' AFTER `sf_purchase_date`;

ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';

ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';

ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';

----Abhishek -----

UPDATE `email_template` SET `template` = 'SF has marked wrong call area, Please reasign correct SF for booking ID %s, <br/>city is %s,<br/> pincode is %s' WHERE `email_template`.`id` = 124;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'wrong_pincode_enter','Customer Enter Incorrect Pincode %s', 'SF has marked wrong call area, Please reasign correct SF for booking ID %s, <br/>city is %s, <br/> Wrong pincode is %s,<br/>Correct Pincode  is %s', 'noreply@247around.com', '', '', '', '1', '2018-10-30 10:48:05');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer has  Wrong Pincode ', 'vendor', '1');
INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Not Servicable in Your Area', 'vendor', '1');

---ABhishek ----
ALTER TABLE `spare_parts_details` ADD `defective_part_rejected_by_partner` TINYINT(4) NOT NULL DEFAULT '0' AFTER `part_requested_on_approval`;

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
 
--Abhay 25/5/19
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES (NULL, '247001', 'Pending', 'Warehouse acknowledged to receive MSL', 'Booking In Progress', 'Warehouse acknowledged to receive MSL', 'Warehouse', 'Send Spare to SF', '0000-00-00 00:00:00');
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES (NULL, '247001', 'Pending', 'Partner shipped spare to Warehouse', 'Booking In Progress', 'Partner shipped spare to Warehouse', 'Warehouse', 'Acknowledge Spare', '0000-00-00 00:00:00');
 
--Kajal 25-05-2019---

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) 
VALUES ('247Around', 'File Type List', NULL, 'employee/booking/show_file_type_list', '1', '190', 'admin,developer', 'main_nav', '1', CURRENT_TIMESTAMP);
-- Abhishek ----
UPDATE `email_template` SET `template` = 'Dear %s,<br><br> <b> %s </b> Service Franchise is Permanently <b> %s </b> now by %s.<br><br> Thanks<br> 247Around Team' WHERE `email_template`.`id` = 19;

UPDATE `email_template` SET `template` = 'Dear %s,<br><br> <b> %s </b> Service Franchise is Temporarily <b> %s </b> now by %s. <br><br> Thanks<br> 247Around Team' WHERE `email_template`.`id` = 18;

 
 --Kajal 27-05-2019---

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'new_partner_am_notification', 'New AM added for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'update_partner_am_notification', 'AM updated for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP); 
 
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'send_mail_for_insert_update_applaince_by_sf', 'Update Appliance By SF', 'Hi ,<br> Charges Not add fro below category <br> Brand -%s , Category - %s <br> Capacity - %s <br> Service Category - %s . Please add the charges . <br> Thanks<br> 247Around Team', 'booking@247around.com', 'abhisheka@247around.com', 'abhaya@247around.com', 'abhisheka@247around.com', '1', '2016-09-26 18:30:00');
 
 --Kajal 27-05-2019---

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'new_partner_am_notification', 'New AM added for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES 
(NULL, 'update_partner_am_notification', 'AM updated for partner - %s', 'Dear All<br><br>AM details are as follows:- <br><br>%s<br>Looking forward for your best support and services to gain more business and trust from them.<br>\r\nThank you for being a valuable part of our service network!<br><br>Best Regards,<br>Team,<br>247around', 'noreply@247around.com', 'all-emp@247around.com', '', '', '1', CURRENT_TIMESTAMP); 
 
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'send_mail_for_insert_update_applaince_by_sf', 'Update Appliance By SF', 'Hi ,<br> Charges Not add fro below category <br> Brand -%s , Category - %s <br> Capacity - %s <br> Service Category - %s . Please add the charges . <br> Thanks<br> 247Around Team', 'booking@247around.com', 'gurpreets@247around.com', 'abhaya@247around.com', 'abhisheka@247around.com', '1', '2016-09-26 18:30:00');


 UPDATE `email_template` SET `template` = 'Hi ,<br> Charges Not add for below category <br> Brand -%s , <br>Category - %s <br> Capacity - %s <br> Service Category - %s  <br> . Please add the charges . <br> Thanks<br> 247Around Team' WHERE `email_template`.`id` = 158;

-- Ankit 25-May-2019
INSERT INTO `file_type` (`id`, `file_type`, `max_allowed_size`, `allowed_type`, `is_active`, `create_date`) VALUES (NULL, 'SF Purchase Invoice', NULL, NULL, '1', CURRENT_TIMESTAMP);
ALTER TABLE `service_centre_charges` ADD COLUMN invoice_pod tinyint(1) NOT NULL DEFAULT 0 AFTER pod;
ALTER TABLE service_center_booking_action ADD COLUMN sf_purchase_invoice varchar(512) NULL DEFAULT NULL AFTER sf_purchase_date;
--- Ankit 27-05-2019
ALTER TABLE booking_unit_details ADD COLUMN invoice_pod tinyint(1) NOT NULL DEFAULT 0 AFTER pod;
ALTER TABLE booking_unit_details ADD COLUMN sf_purchase_invoice varchar(512) NULL DEFAULT NULL AFTER sf_purchase_date;
 UPDATE `email_template` SET `template` = 'Hi ,<br> Charges Not add for below category <br> Brand -%s , <br>Category - %s <br> Capacity - %s <br> Service Category - %s  <br> . Please add the charges . <br> Thanks<br> 247Around Team' WHERE `email_template`.`id` = 158;
  ---Abhishek ---
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
 
--Kalyani 07-June-2019
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

--Kalyani 07-June-2019
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES (NULL, '247Around', 'Download SF Penalty Summary', '', 'employee/vendor/penalty_summary', '2', '36', 'admin,developer,inventory_manager,regionalmanager', 'main_nav', '1', CURRENT_TIMESTAMP);
ALTER TABLE `invoice_details` ADD `from_gst_number` VARCHAR(25) NULL DEFAULT NULL AFTER `total_amount`, ADD `to_gst_number` VARCHAR(25) NULL DEFAULT NULL AFTER `from_gst_number`;


ALTER TABLE `vendor_partner_invoices` CHANGE `invoice_file_main` `invoice_file_main` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

--Kajal 26/06/2019 starting --
UPDATE `header_navigation` SET `title` = 'Inventory Master List' WHERE `header_navigation`.`title`='Spare Part List';

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('Partner', 'Upload Inventory Master File', NULL, 'partner/inventory/upload_inventory_details_file', 2, '148', 'Primary Contact,Area Sales Manager,Booking Manager,Owner', 'main_nav', 1, CURRENT_TIMESTAMP),
('Partner', 'Upload Serviceable BOM File', NULL, 'partner/inventory/upload_bom_file', 2, '148', 'Primary Contact,Area Sales Manager,Booking Manager,Owner', 'main_nav', 1, CURRENT_TIMESTAMP),
('Partner', 'Upload Alternate Parts', NULL, 'partner/inventory/upload_alternate_spare_parts_file', 2, '148', 'Primary Contact,Area Sales Manager,Booking Manager,Owner', 'main_nav', 1, CURRENT_TIMESTAMP);

ALTER TABLE `file_uploads` ADD `agent_type` VARCHAR(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'employee' AFTER `agent_id`;
--Kajal 26/06/2019 ending --

--- Gorakh 28-06-2019 
INSERT INTO `email_template` (`tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES
('spare_part_shipment_pending', 'Spare Parts Shipment Pending', 'Please find the attachment', NULL, 'noreply@247around.com', '', 'gorakhn@247around.com,gorakhn@247around.com,gorakhn@247around.com', '', '1', '2019-06-28 05:52:56');

--Abhishek----
ALTER TABLE `appliance_updated_by_sf` CHANGE `capacity` `capacity` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;


--Abhay 01/07/2019

ALTER TABLE `spare_parts_details` ADD `partner_warehouse_packaging_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `vendor_courier_invoice_id`;

ALTER TABLE employee_relation ADD COLUMN individual_service_centres_id text NULL DEFAULT NULL AFTER service_centres_id;

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
--Abhay /07/2019

ALTER TABLE `entity_gst_details` ADD `city` VARCHAR(64) NOT NULL AFTER `gst_file`, ADD `pincode` INT(11) NOT NULL AFTER `city`, ADD `address` VARCHAR(256) NOT NULL AFTER `pincode`;

ALTER TABLE `inventory_ledger` ADD `micro_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `invoice_id`;
--- Gorakh 28-06-2019 
INSERT INTO `email_template` (`tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES
('spare_part_shipment_pending', 'OUT OF TAT - UNDELIVERED %s COURIER', 'Please find attached excel in which mentioned undelivered <b>%s</b> courier above 5 days.', NULL, 'noreply@247around.com', '', '', '', '1', '2019-06-28 05:52:56');

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
--Kajal 13/07/2019 --
ALTER TABLE `spare_parts_details` CHANGE `cancellation_reason` `spare_cancellation_reason` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

-- Kajal 19-July-2019 --
ALTER TABLE `inventory_model_mapping` ADD `bom_main_part` INT(1) NOT NULL DEFAULT '1' COMMENT '1 - Main Part, 0 - Alternate Part' AFTER `max_quantity`;

--Ankit 15/07/2019 --
ALTER TABLE service_center_brand_mapping ADD COLUMN service_id int(11) NOT NULL AFTER service_center_id;

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

--Kalyani 03-08-2019
ALTER TABLE `ewaybill_details` ADD `vehicle_number` VARCHAR(255) NOT NULL AFTER `ewaybill_generated_date`;
ALTER TABLE `ewaybill_details` ADD `invoice_id` VARCHAR(255) NOT NULL AFTER `vehicle_number`;


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
=======
 
-- Ankit 13-08-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES (NULL, 'not_delivered_bb_orders', NULL, ' ', 'sunilk@247around.com', 'kmardee@amazon.com,ybhargav@amazon.com', 'sunilk@247around.com', '', '1', '', CURRENT_TIMESTAMP);
 
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

-- Kajal 04-09-2019
UPDATE `email_template` SET `subject` = 'Spare shipped by %s to %s' , `template` = 'Dear Partner,<br><br> <b>%s</b> shipped below spare to your warehouse.<br><br> %s <br> <b>Courier Details </b><br><br> %s<br> Regards,<br> 247around' , `cc` = 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com' WHERE `email_template`.`tag` = 'msl_send_by_wh_to_partner';

-- Kajal 05-09-2019
UPDATE `email_template` SET `from` = 'defective-outward@247around.com', `cc` = 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com' WHERE `email_template`.`tag` = 'defective_spare_send_by_wh_to_partner';

-- Kajal 06-09-2019

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'msl_send_by_microwh_to_wh', 'Spare shipped by %s to %s', 'Dear SF,<br><br> <b>%s</b> shipped below spare from your warehouse.<br><br> %s <br> <b>Courier Details </b><br><br> %s<br> Regards,<br> 247around', NULL, 'defective-outward@247around.com', '', 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com', '', '1', CURRENT_TIMESTAMP);

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'msl_send_by_microwh_to_wh', 'New Spare shipped by %s to %s', 'Dear SF,<br><br> <b>%s</b> shipped below new spare from your warehouse.<br><br> %s <br> <b>Courier Details </b><br><br> %s<br> Regards,<br> 247around', NULL, 'defective-outward@247around.com', '', 'warehouse_noida@247around.com, anuj@247around.com, defective-outward@247around.com', '', '1', CURRENT_TIMESTAMP);

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
>>>>>>> CRM_Release_1.66.0.2

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
 
ALTER TABLE `inventory_model_mapping` ADD `active` TINYINT DEFAULT 1 AFTER  `create_date`;

-- Kajal 20-08-2019
INSERT INTO `internal_status` (`id`, `page`, `status`, `active`, `sf_update_active`, `method_name`, `redirect_url`, `create_date`) VALUES (NULL, 'bill_defective_spare_part_lost', 'Part Sold', '1', '0', NULL, NULL, CURRENT_TIMESTAMP);
UPDATE `invoice_tags` SET `tag` = 'part_lost' WHERE `invoice_tags`.`sub_category` = 'Defective Part Lost';
 
-- Ankit 13-08-2019
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `email_tag`, `create_date`) VALUES (NULL, 'not_delivered_bb_orders', NULL, ' ', 'sunilk@247around.com', 'kmardee@amazon.com,ybhargav@amazon.com', 'sunilk@247around.com', '', '1', '', CURRENT_TIMESTAMP);

---Abhishek--
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES (NULL, '247130', 'Pending', 'NRN Approved By Partner', 'NRN Approved By Partner', 'NRN Approved By Partner', 'Partner', NULL, CURRENT_TIMESTAMP);
  
-- Kajal 22-08-2019
UPDATE `invoice_tags` SET `tag` = 'Out-of-Warranty' WHERE `invoice_tags`.`sub_category` = 'Out-of-Warranty';

-- Prity 26-08-2019
ALTER TABLE warranty_plans add column `plan_depends_on` int(11) NOT NULL DEFAULT 1 COMMENT '1 => Model Specific (Plan Valid on Model Number), 2 => Service Specific (Plan Valid on Product eg : AC, WM)';

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
--Gorakh23-09-2019
RENAME TABLE `boloaaka`.`spare_invoice_details` TO `boloaaka`.`oow_spare_invoice_details`; 
--Ankit 24-09-2019
ALTER TABLE wrong_part_shipped_details ADD COLUMN active tinyint(1) NOT NULL DEFAULT 1



--Pranjal 9/5/2019 --
INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, NUll, 'TAT Between 24 - 48 hrs', NULL, '30', '1');
INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, NUll, 'TAT Between 48 - 72 hrs', NULL, '40', '1');
INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, NUll, 'TAT Greater Than 72 hrs', NULL, '50', '1');

INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, NUll, 'Upcountry SVC TAT Between 48 -72 hrs', NULL, '30', '1');
INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, NUll, 'Upcountry SVC TAT Between 72 -120 hrs', NULL, '40', '1');
INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES (NULL, NULL, NUll, 'Upcountry SVC TAT Greater Than 120 hrs', NULL, '50', '1');

ALTER TABLE wrong_part_shipped_details ADD COLUMN active tinyint(1) NOT NULL DEFAULT 1;
-- Prity Sharma 25-09-2019
ALTER TABLE booking_unit_details CHANGE COLUMN sf_purchase_date sf_purchase_date date NULL DEFAULT NULL;
