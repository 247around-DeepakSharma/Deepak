ALTER TABLE `header_navigation` ADD `priority` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `is_active`;
UPDATE `header_navigation` SET `priority` = '2' WHERE `header_navigation`.`id` = 129;
UPDATE `header_navigation` SET `priority` = '1' WHERE `header_navigation`.`id` = 130;
