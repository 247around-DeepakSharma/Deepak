ALTER TABLE `email_attachment_parser` ADD `send_file_back` TINYINT(4) NULL DEFAULT NULL AFTER `email_send_to`;
ALTER TABLE `email_attachment_parser` ADD `order_id_read_column` VARCHAR(5) NULL DEFAULT NULL AFTER `send_file_back`;
ALTER TABLE `email_attachment_parser` ADD `booking_id_write_column` VARCHAR(5) NULL DEFAULT NULL AFTER `order_id_read_column`;
ALTER TABLE `email_attachment_parser` ADD `revert_file_to_email` VARCHAR(256) NULL DEFAULT NULL AFTER `booking_id_write_column`;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'revert_upload_file_to_partner', 'Updated Booking Id ', 'Please find the attached file with updated booking id', 'noreply@247around.com', '', '', '', '1', '2018-02-09 10:31:44');
