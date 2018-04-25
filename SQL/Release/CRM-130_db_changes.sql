ALTER TABLE `booking_state_change` ADD `actor` VARCHAR(256) NULL AFTER `new_state`;
ALTER TABLE `booking_state_change` ADD `next_action` TEXT NULL AFTER `actor`;
ALTER TABLE `booking_details` ADD `actor` VARCHAR(256) NULL AFTER `partner_internal_status`, ADD `next_action` TEXT NULL AFTER `actor`;
ALTER TABLE `partner_booking_status_mapping` ADD `actor` VARCHAR(256) NULL AFTER `partner_internal_status`, ADD `next_action` TEXT NULL AFTER `actor`;