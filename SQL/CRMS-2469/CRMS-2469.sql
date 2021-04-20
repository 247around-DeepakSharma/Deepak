UPDATE `header_navigation` SET `title` = 'View Model List' WHERE `header_navigation`.`id` = 201;
UPDATE `header_navigation` SET `title` = 'View Serviceable BOM' WHERE `header_navigation`.`id` = 152;
UPDATE `header_navigation` SET `title` = 'View Serviceable BOM' WHERE `header_navigation`.`id` = 234;
UPDATE `header_navigation` SET `title` = 'View Alternate Parts' WHERE `header_navigation`.`id` = 215;
UPDATE `header_navigation` SET `title` = 'View Part Master' WHERE `header_navigation`.`id` = 151;
UPDATE `header_navigation` SET `title` = 'Download Alternate Part Master' WHERE `header_navigation`.`id` = 243;
UPDATE `header_navigation` SET `title` = 'Download BOM Missing Models' WHERE `header_navigation`.`id` = 235;
UPDATE `header_navigation` SET `title` = 'Upload Model vs part Code Master' WHERE `header_navigation`.`id` = 224;
//Master main_nav
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Master', 'fa fa-database', NULL, '1', NULL, 'primary Contact,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '1', '2021-04-15 12:27:18')
UPDATE `header_navigation` SET `parent_ids` = '422' WHERE `header_navigation`.`id` = 223;
UPDATE `header_navigation` SET `parent_ids` = '422' WHERE `header_navigation`.`id` = 224;
UPDATE `header_navigation` SET `parent_ids` = '422' WHERE `header_navigation`.`id` = 225;
UPDATE `header_navigation` SET `parent_ids` = '422' WHERE `header_navigation`.`id` = 151;
UPDATE `header_navigation` SET `parent_ids` = '422' WHERE `header_navigation`.`id` = 201;
UPDATE `header_navigation` SET `parent_ids` = '422' WHERE `header_navigation`.`id` = 243;
UPDATE `header_navigation` SET `parent_ids` = '422' WHERE `header_navigation`.`id` = 234;
UPDATE `header_navigation` SET `parent_ids` = '422' WHERE `header_navigation`.`id` = 152;
INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Micro Warehouse Stock', NULL, 'partner/inventory/inventory_list/0', '2', '148', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2021-04-15 06:58:29')
UPDATE `header_navigation` SET `title` = 'Central Warehouse Stock' WHERE `header_navigation`.`id` = 149;
UPDATE `header_navigation` SET `link` = 'partner/inventory/inventory_list/1' WHERE `header_navigation`.`id` = 149;