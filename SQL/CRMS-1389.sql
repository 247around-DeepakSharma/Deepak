ALTER TABLE `hsn_code_details` CHANGE `hsn_code` `hsn_code` INT NULL DEFAULT NULL;
ALTER TABLE `hsn_code_details` CHANGE `gst_rate` `gst_rate` DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE `hsn_code_details` CHANGE `update_date` `update_date` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
