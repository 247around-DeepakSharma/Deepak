ALTER TABLE `partner_file_upload_header_mapping` ADD `order_item_id` VARCHAR(64) NULL DEFAULT NULL AFTER `delivery_date`, ADD `spd` VARCHAR(64) NULL DEFAULT NULL AFTER `order_item_id`;
