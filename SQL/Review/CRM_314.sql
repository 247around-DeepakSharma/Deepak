INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'customer_qr_download', '%s', '', '1', CURRENT_TIMESTAMP);
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'customer_paid_invoice_to_vendor', 'Customer Paid Invoice', 'Please Find Attachment', 'billing@247around.com', 'abhaya@247around.com', 'abhaya@247around.com', '', '1', '2016-06-17 00:00:00');
INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'customer_paid_invoice', '%s', '', '1', '2018-02-27 19:55:03');


