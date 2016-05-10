<!--  --Abhay 9/4/16-->

ALTER TABLE  `bookings_sources` ADD  `partner_email_for_to` VARCHAR( 100 ) NOT NULL AFTER  `partner_id` ,
ADD  `partner_email_for_cc` VARCHAR( 100 ) NOT NULL AFTER  `patner_email_for_to` ;

UPDATE  `247around_test`.`bookings_sources` SET  `partner_email_for_cc` =  'heyrajcool@gmail.com,abhaya@247around' WHERE  `bookings_sources`.`id` =8;
