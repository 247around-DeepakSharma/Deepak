INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Warranty Checker', NULL, 'employee/warranty', '2', '128', 'Primary Contact,Area Sales Manager,Booking Manager,Call Center,Warehouse Incharge,Owner', 'main_nav', '1', '1', '2020-03-19 05:14:03');
-- Change Sequence as per Live Ids
UPDATE `boloaaka`.`header_navigation` SET `sequence` = '4' WHERE (`id` = '129');
UPDATE `boloaaka`.`header_navigation` SET `sequence` = '3' WHERE (`id` = '130');
UPDATE `boloaaka`.`header_navigation` SET `sequence` = '2' WHERE (`id` = '131');
UPDATE `boloaaka`.`header_navigation` SET `sequence` = '1' WHERE (`id` = '245');
UPDATE `boloaaka`.`header_navigation` SET `sequence` = '0' WHERE (`id` = '277');