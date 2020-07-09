---Abhishek---
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertNewEngineer`(IN `name` VARCHAR(250), IN `phone` VARCHAR(20), IN `alternate_phone` VARCHAR(20), IN `service_center_id` INT(10) UNSIGNED, IN `active` INT(5), IN `create_date` DATETIME, OUT `last_id` INT(11) UNSIGNED)
    COMMENT 'insert engineer first time from SF CRM'
BEGIN
		INSERT INTO engineer_details(name,phone,alternate_phone,service_center_id,active,create_date) VALUES(name,phone,alternate_phone,service_center_id,active,create_date);
        SET last_id = LAST_INSERT_ID();
	END$$
DELIMITER ;
