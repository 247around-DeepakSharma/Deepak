ALTER TABLE `spare_parts_details` ADD `courier_rto_by_api` INT NULL DEFAULT NULL AFTER `rejected_defective_part_pic_by_wh`;
ALTER TABLE `courier_company_invoice_details` ADD `tracking_status` VARCHAR(250) NULL DEFAULT NULL AFTER `rto_file`;

INSERT INTO `partner_booking_status_mapping` (`partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES
(247001, 'Pending', 'Parts not received', 'Parts not received by SF', 'Parts not received by SF', '247Around', 'To validate (Parts not received by SF)', CURRENT_TIMESTAMP());

ALTER TABLE `spare_parts_details` ADD `part_not_delivered_marked_by_sf_count` TINYINT NULL DEFAULT NULL AFTER `courier_rto_by_api`;
ALTER TABLE `courier_company_invoice_details` ADD `courier_lost` TINYINT(4) NULL DEFAULT NULL AFTER `tracking_status`;

ALTER TABLE `courier_company_invoice_details` ADD `courier_lost_file` VARCHAR(250) NULL DEFAULT NULL AFTER `courier_lost`;