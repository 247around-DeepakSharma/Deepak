ALTER TABLE `boloaaka`.`service_centres` ADD COLUMN `is_approved` INT(1) NULL DEFAULT 0 AFTER `auth_certificate_validate_year`;
Insert INTO boloaaka.header_navigation (entity_type,title,title_icon,link,level,parent_ids,groups,nav_type,is_active) values('247Around','Unapproved Service Centers','','employee/vendor/unapprovered_service_centers',2,36,'admin,developer,regionalmanager','main_nav',1);
