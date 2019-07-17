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
--Abhay /07/2019

ALTER TABLE `entity_gst_details` ADD `city` VARCHAR(64) NOT NULL AFTER `gst_file`, ADD `pincode` INT(11) NOT NULL AFTER `city`, ADD `address` VARCHAR(256) NOT NULL AFTER `pincode`;

ALTER TABLE `inventory_ledger` ADD `micro_invoice_id` VARCHAR(128) NULL DEFAULT NULL AFTER `invoice_id`;
--- Gorakh 28-06-2019 
INSERT INTO `email_template` (`tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES
('spare_part_shipment_pending', 'OUT OF TAT - UNDELIVERED %s COURIER', 'Please find attached excel in which mentioned undelivered <b>%s</b> courier above 5 days.', NULL, 'noreply@247around.com', '', '', '', '1', '2019-06-28 05:52:56');

--Kajal 12/07/2019 --
ALTER TABLE `spare_parts_details` ADD `cancellation_reason` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `shipped_quantity`;

--Kajal 13/07/2019 --
ALTER TABLE `spare_parts_details` CHANGE `cancellation_reason` `spare_cancellation_reason` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

-- Kajal 19-July-2019 --
ALTER TABLE `inventory_model_mapping` ADD `bom_main_part` INT(1) NOT NULL DEFAULT '1' COMMENT '1 - Main Part, 0 - Alternate Part' AFTER `max_quantity`;

--Ankit 15/07/2019 --
ALTER TABLE service_center_brand_mapping ADD COLUMN service_id int(11) NOT NULL AFTER service_center_id;

-- Ankit 17-July-2019
ALTER TABLE service_center_brand_mapping ADD COLUMN created_by varchar(150) NOT NULL AFTER create_date;
