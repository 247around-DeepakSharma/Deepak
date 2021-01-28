ALTER TABLE `hsn_code_details` ADD `status` TINYINT NOT NULL DEFAULT 1 AFTER `service_id`;
ALTER TABLE hsn_code_details ADD UNIQUE INDEX unique_hsn_code_service_id(hsn_code, service_id);