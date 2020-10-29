/* add main menu Master*/


INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Master', 'fa fa-database', NULL, '1', NULL, 'primary Contact,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '1', '2018-06-21 12:27:18');

/* Add sub menu of Master*/

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'View Alternate Parts', NULL, 'partner/inventory/alternate_parts_list', '2', '300', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2018-06-21 06:58:29');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'View Serviceable BOM', NULL, 'partner/inventory/download_serviceable_bom', '2', '300', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2018-06-21 07:00:18');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Download Alternate Part Master', NULL, 'partner/inventory/download_alternate_parts', '2', '300', 'Area Sales Manager,Booking Manager,Call Center,Owner,primary Contact', 'main_nav', '1', '0', '2019-08-06 18:03:26');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'View Part Master', NULL, 'partner/inventory/show_inventory_details', '2', '300', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2018-06-21 12:30:18');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Download BOM Missing Models', NULL, 'partner/inventory/download_missing_serviceable_bom', '2', '300', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2018-06-21 07:00:18');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Upload Part Master', NULL, 'partner/inventory/upload_inventory_details_file', '2', '300', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2019-07-02 16:14:03');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Upload Alternate Master', NULL, 'partner/inventory/upload_alternate_spare_parts_file', '2', '300', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2019-07-02 16:14:03');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'Upload Model vs part Code Master', NULL, 'partner/inventory/upload_bom_file', '2', '300', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2019-07-02 16:14:03');

INSERT INTO `header_navigation` (`id`, `entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `sequence`, `create_date`) VALUES (NULL, 'Partner', 'View Model List', 'partner/inventory/appliance_model_mapping', 'partner/inventory/model_mapping', '2', '300', 'primary Contact,Area Sales Manager,Warehouse Incharge,Booking Manager,Owner', 'main_nav', '1', '0', '2019-02-28 15:54:39');

/*Deactivate sub menues of inventory main menu*/

UPDATE header_navigation SET is_active = '0' WHERE id in (201, 234, 215, 151, 243, 235, 223, 225, 224);

/* Arrange menu as requirement */
UPDATE `header_navigation` SET `sequence` = '1' WHERE `header_navigation`.`id` in (161,128,143,127,142);
