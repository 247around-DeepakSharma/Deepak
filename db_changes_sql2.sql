--Abhyay 20/5/2019
ALTER TABLE `service_center_booking_action` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0' AFTER `sf_purchase_date`;
ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';


----Abhishek -----

UPDATE `email_template` SET `template` = 'SF has marked wrong call area, Please reasign correct SF for booking ID %s, <br/>city is %s,<br/> pincode is %s' WHERE `email_template`.`id` = 124;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'wrong_pincode_enter','Customer Enter Incorrect Pincode %s', 'SF has marked wrong call area, Please reasign correct SF for booking ID %s, <br/>city is %s, <br/> Wrong pincode is %s,<br/>Correct Pincode  is %s', 'noreply@247around.com', '', '', '', '1', '2018-10-30 10:48:05');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer has  Wrong Pincode ', 'vendor', '1');
INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Not Servicable in Your Area', 'vendor', '1');
