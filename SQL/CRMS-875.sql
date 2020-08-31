ALTER TABLE `header_navigation` ADD `sequence` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `is_active`;
UPDATE `header_navigation` SET `sequence` = '2' WHERE `header_navigation`.`id` = 129;
UPDATE `header_navigation` SET `sequence` = '1' WHERE `header_navigation`.`id` = 130;
UPDATE header_navigation set parent_ids = '128' WHERE title = 'bulk warranty checker' AND entity_type = 'partner';
UPDATE header_navigation SET sequence = '3' WHERE header_navigation.id = 129;
UPDATE header_navigation SET sequence = '2' WHERE header_navigation.id = 130;
UPDATE header_navigation SET sequence = '1' WHERE header_navigation.id = 131;
