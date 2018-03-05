ALTER TABLE `push_notification_logs` ADD `notification_tag` VARCHAR(200) NOT NULL AFTER `request_id`;
ALTER TABLE `push_notification_logs` ADD `status` INT(2) NOT NULL AFTER `subscriber_ids`;
ALTER TABLE `push_notification_logs` ADD `status_msg` TEXT NOT NULL AFTER `status`;
ALTER TABLE `push_notification_subscribers` ADD `is_valid` INT(2) NOT NULL DEFAULT '1' AFTER `subscriber_id`;
ALTER TABLE `push_notification_subscribers` ADD `valid_date` DATETIME NOT NULL AFTER `is_valid`;