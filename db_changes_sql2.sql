--Abhyay 20/5/2019
ALTER TABLE `service_center_booking_action` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0' AFTER `sf_purchase_date`;
ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';


<<<<<<< HEAD
----Abhishek -----

UPDATE `email_template` SET `template` = 'SF has marked wrong call area, Please reasign correct SF for booking ID %s, <br/>city is %s,<br/> pincode is %s' WHERE `email_template`.`id` = 124;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'wrong_pincode_enter','Customer Enter Incorrect Pincode %s', 'SF has marked wrong call area, Please reasign correct SF for booking ID %s, <br/>city is %s, <br/> Wrong pincode is %s,<br/>Correct Pincode  is %s', 'noreply@247around.com', '', '', '', '1', '2018-10-30 10:48:05');

INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Customer has  Wrong Pincode ', 'vendor', '1');
INSERT INTO `booking_cancellation_reasons` (`id`, `reason`, `reason_of`, `show_on_app`) VALUES (NULL, 'Not Servicable in Your Area', 'vendor', '1');

ALTER TABLE `spare_parts_details` ADD `defective_part_rejected_by_partner` TINYINT(4) NOT NULL DEFAULT '0' AFTER `part_requested_on_approval`;

--Kalyani 25-05-2019---

CREATE TABLE `entity_identity_proof` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `identity_proof_type` varchar(255) NOT NULL,
  `identity_proof_number` varchar(255) NOT NULL,
  `identity_proof_pic` varchar(255) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `entity_identity_proof`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `entity_identity_proof`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
=======
--Abhay 25/5/19
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES (NULL, '247001', 'Pending', 'Warehouse acknowledged to receive MSL', 'Booking In Progress', 'Warehouse acknowledged to receive MSL', 'Warehouse', 'Send Spare to SF', '0000-00-00 00:00:00');
INSERT INTO `partner_booking_status_mapping` (`id`, `partner_id`, `247around_current_status`, `247around_internal_status`, `partner_current_status`, `partner_internal_status`, `actor`, `next_action`, `create_date`) VALUES (NULL, '247001', 'Pending', 'Partner shipped spare to Warehouse', 'Booking In Progress', 'Partner shipped spare to Warehouse', 'Warehouse', 'Acknowledge Spare', '0000-00-00 00:00:00');
>>>>>>> 3952d09c4... Link Booking status when MSL send by Partner/ Receive By Warehouse #CRM-3082
