ALTER TABLE `email_attachment_parser` ADD `email_host` VARCHAR(256) NOT NULL AFTER `partner_id`;
ALTER TABLE `email_attachment_parser` DROP `email_received_from`;

UPDATE email_attachment_parser SET email_subject_text = ''

INSERT INTO `email_attachment_parser` (`id`, `partner_id`, `email_host`, `email_subject_text`, `email_function_name`, `file_type`, `email_remarks`, `email_send_to`, `qc_svc`, `active`, `create_date`) VALUES (NULL, '247024', 'amazon.com', NULL, 'buyback/upload_buyback_process/process_upload_order', NULL, NULL, NULL, NULL, '1', CURRENT_TIMESTAMP);

INSERT INTO `email_attachment_parser` (`id`, `partner_id`, `email_host`, `email_subject_text`, `email_function_name`, `file_type`, `email_remarks`, `email_send_to`, `qc_svc`, `active`, `create_date`) VALUES (NULL, '1', 'snapdeal.com', 'around', 'employee/do_background_upload_excel/process_upload_file', 'Snapdeal-Delivered', NULL, NULL, NULL, '1', CURRENT_TIMESTAMP),(NULL, '3', 'paytm.com', NULL, 'employee/do_background_upload_excel/process_upload_file', 'Paytm-Delivered', NULL, NULL, NULL, '1', CURRENT_TIMESTAMP),(NULL, '3', 'paytmmall.com', NULL, 'employee/do_background_upload_excel/process_upload_file', 'Paytm-Delivered', NULL, NULL, NULL, '1', CURRENT_TIMESTAMP),(NULL, '247010', 'sathyaindia.com', NULL, 'employee/do_background_upload_excel/process_upload_file', 'Satya-Delivered', NULL, NULL, NULL, '1', CURRENT_TIMESTAMP),(NULL, '247010', 'sathya.email', NULL, 'employee/do_background_upload_excel/process_upload_file', 'Satya-Delivered', NULL, NULL, NULL, '1', CURRENT_TIMESTAMP);
