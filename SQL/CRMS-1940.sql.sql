ALTER TABLE `service_centres` ADD COLUMN `is_approved` INT(1) NULL DEFAULT 0;
ALTER TABLE `service_centres` ADD COLUMN `approved_by` TINYINT NULL ;
Insert INTO header_navigation (entity_type,title,title_icon,link,level,parent_ids,groups,nav_type,is_active) values ('247Around','Unapproved Service Centers','','employee/vendor/unapprovered_service_centers',2,36,'admin,developer,regionalmanager','main_nav',1);
