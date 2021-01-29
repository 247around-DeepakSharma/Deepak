ALTER TABLE `hsn_code_details` ADD `status` TINYINT NOT NULL DEFAULT 1 AFTER `service_id`;
ALTER TABLE `hsn_code_details` ADD UNIQUE KEY `unique_hsn_code_service_id` (`hsn_code`,`service_id`);
ALTER TABLE `hsn_code_details` CHANGE `update_date` `update_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;