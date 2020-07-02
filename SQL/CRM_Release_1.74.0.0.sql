INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'get_unread_email_template', 'Details of unread mails', 'Please find the details of unread emails from the <b>installations@247around.com</b> email. 
<br><br>
%s', 'noreply@247around.com', '', '', '', '1', '2017-10-03 13:05:07');


-- Ankit 02-07-2020
INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES (NULL, 'Covid Zone', 'india_district_coordinates.zone_color AS \'Covid Zone\'', '1', '', '1', '2');