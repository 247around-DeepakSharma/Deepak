INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `is_exception_for_length`) VALUES (NULL, 'booking_creation_otp', 'Dear Customer,\r\n\r\nYour one time password for booking creation is %s.', '', '1', '0');
ALTER TABLE sms_sent_details change column booking_id booking_id varchar(50) NULL DEFAULT NULL;
ALTER TABLE services add column walk_in tinyint NOT NULL DEFAULT 0;