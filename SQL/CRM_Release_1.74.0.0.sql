INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'get_unread_email_template', 'Details of unread mails', 'Please find the details of unread emails from the <b>installations@247around.com</b> email. 
<br><br>
%s', 'noreply@247around.com', '', '', '', '1', '2017-10-03 13:05:07');


-- Ankit 02-07-2020
INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES (NULL, 'Covid Zone', 'india_district_coordinates.zone_color AS \'Covid Zone\'', '1', '', '1', '2');

-- Raman
-- 30-06-2020

CREATE TABLE custom_report_queries (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    tag varchar(50) NOT NULL,
    subject text NOT NULL,
    query text NOT NULL,
    create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
);

INSERT INTO `custom_report_queries` (`id`, `tag`, `subject`, `query`, `create_date`) VALUES (NULL, 'partner_contacts_list', '', 'SELECT partners.company_name as \'Company Name\',partners.public_name as \'Public Name\',partners.primary_contact_email as \'Primary Contact Email\',partners.owner_email as \'Owner Email\',partners.owner_alternate_email as \'Alternate Email\',entity_login_table.email as Email FROM partners JOIN entity_login_table ON entity_login_table.entity_id = partners.id;', '2020-07-01 00:15:42');

INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES
('247Around', 'Custom Reports', NULL, 'employee/reports/custom_reports', 2, '80', 'admin,developer,regionalmanager,areasalesmanager, 'main_nav', 1, '2020-07-01 03:20:02');
  

ALTER TABLE `custom_report_queries` ADD `active` TINYINT(1) NOT NULL DEFAULT '0' AFTER `query`;

UPDATE`custom_report_queries` SET `active` = 1;

INSERT INTO `custom_report_queries` (`id`, `tag`, `subject`, `query`, `active`, `create_date`) VALUES (NULL, 'covid_zone_details_sf_wise', '', 'SELECT service_centres.id, service_centres.name as Name, service_centres.company_name as \'Company Name\',service_centres.state as State,service_centres.district as District, india_district_coordinates.zone_color as \'Zone Color\' FROM service_centres JOIN india_district_coordinates ON service_centres.district = india_district_coordinates.district WHERE service_centres.active = 1', '1', CURRENT_TIMESTAMP);