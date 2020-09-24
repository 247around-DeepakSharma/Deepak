/*
-- Query: select * from cron_config
LIMIT 0, 50000

-- Date: 2020-09-11 10:43
*/
CREATE TABLE `cron_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(64) NOT NULL,
  `column` varchar(64) NOT NULL,
  `date` datetime NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `cron_config` (`table`,`column`) VALUES ('service_center_booking_action','sf_purchase_invoice');
INSERT INTO `cron_config` (`table`,`column`) VALUES ('booking_files','file_name');
INSERT INTO `cron_config` (`table`,`column`) VALUES ('engineer_booking_action','purchase_invoice');
INSERT INTO `cron_config` (`table`,`column`) VALUES ('spare_parts_details','invoice_pic');

