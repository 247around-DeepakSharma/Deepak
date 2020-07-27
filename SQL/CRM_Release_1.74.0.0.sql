
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'get_unread_email_template', 'Details of unread mails', 'Please find the details of unread emails from the <b>installations@247around.com</b> email. 
<br><br>
%s', 'noreply@247around.com', '', '', '', '1', '2017-10-03 13:05:07');


-- Ankit 02-07-2020
INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES (NULL, 'Covid Zone', 'india_district_coordinates.zone_color AS \'Covid Zone\'', '1', '', '1', '2');


UPDATE `email_template` SET `from` = 'ar@247around.com' WHERE `email_template`.`id` = 44;

-- Ankit
ALTER TABLE `inventory_ledger` ADD `spare_id` int(11) NULL DEFAULT NULL;

 

CREATE TABLE custom_report_queries (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    tag varchar(50) NOT NULL,
    subject text NOT NULL,
    query text NOT NULL,
    create_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
);

INSERT INTO `custom_report_queries` (`id`, `tag`, `subject`, `query`, `create_date`) VALUES (NULL, 'partner_contacts_list', '', 'SELECT partners.company_name as \'Company Name\',partners.public_name as \'Public Name\',partners.primary_contact_email as \'Primary Contact Email\',partners.owner_email as \'Owner Email\',partners.owner_alternate_email as \'Alternate Email\',entity_login_table.email as Email FROM partners JOIN entity_login_table ON entity_login_table.entity_id = partners.id;', '2020-07-01 00:15:42');

INSERT INTO `custom_report_queries` (`id`, `tag`, `subject`, `query`, `create_date`) VALUES (NULL, 'partner_contacts_list', '', 'SELECT partners.company_name as \'Company Name\',partners.public_name as \'Public Name\',partners.primary_contact_email as \'Primary Contact Email\',partners.owner_email as \'Owner Email\',partners.owner_alternate_email as \'Alternate Email\',entity_login_table.email as Email FROM partners JOIN entity_login_table ON entity_login_table.entity_id = partners.id;', '2020-07-01 00:15:42');


INSERT INTO `header_navigation` (`entity_type`, `title`, `title_icon`, `link`, `level`, `parent_ids`, `groups`, `nav_type`, `is_active`, `create_date`) VALUES('247Around', 'Custom Reports', NULL, 'employee/reports/custom_reports', 2, '80', 'admin,developer,regionalmanager,areasalesmanager', 'main_nav', 1, '2020-07-01 03:20:02'); 

-- Raman 74
-- 12 June

ALTER TABLE `custom_report_queries` ADD `active` TINYINT(1) NOT NULL DEFAULT '0' AFTER `query`;

UPDATE`custom_report_queries` SET `active` = 1;
<<<<<<< HEAD

INSERT INTO `custom_report_queries` (`id`, `tag`, `subject`, `query`, `active`, `create_date`) VALUES (NULL, 'covid_zone_details_sf_wise', '', 'SELECT service_centres.id, service_centres.name as Name, service_centres.company_name as \'Company Name\',service_centres.state as State,service_centres.district as District, india_district_coordinates.zone_color as \'Zone Color\' FROM service_centres JOIN india_district_coordinates ON service_centres.district = india_district_coordinates.district WHERE service_centres.active = 1', '1', CURRENT_TIMESTAMP);
ALTER TABLE `custom_report_queries` ADD `active` TINYINT(1) NOT NULL DEFAULT '0' AFTER `query`;


UPDATE`custom_report_queries` SET `active` = 1;
=======
 
---27-july--Abhishek--
ALTER TABLE `engineer_table_sign` ADD `device_info` TEXT NULL DEFAULT NULL AFTER `mismatch_pincode`;
 
>>>>>>> 18fa5404e... CRMS-965	 Update Device Info While Complete Booking from APP in engineer_booking_sign_table

INSERT INTO `custom_report_queries` (`id`, `tag`, `subject`, `query`, `active`, `create_date`) VALUES (NULL, 'covid_zone_details_sf_wise', '', 'SELECT service_centres.id, service_centres.name as Name, service_centres.company_name as \'Company Name\',service_centres.state as State,service_centres.district as District, india_district_coordinates.zone_color as \'Zone Color\' FROM service_centres JOIN india_district_coordinates ON service_centres.district = india_district_coordinates.district WHERE service_centres.active = 1', '1', CURRENT_TIMESTAMP);


---27-july--Abhishek--
ALTER TABLE `engineer_table_sign` ADD `device_info` TEXT NULL DEFAULT NULL AFTER `mismatch_pincode`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_partner_real_time_summary_report` AS select `booking_details`.`id` AS `id`,`booking_details`.`order_id` AS `Brand Reference ID`,`booking_details`.`partner_id` AS `partner_id`,`booking_details`.`origin_partner_id` AS `origin_partner_id`,`booking_details`.`booking_id` AS `247around Booking ID`,`service_centres`.`name` AS `Service Center`,cast(`booking_details`.`create_date` as date) AS `Create Date`,`ud`.`product_or_services` AS `product_or_services`,`ud`.`appliance_brand` AS `Brand`,`ud`.`purchase_date` AS `Date of Purchase`,(case when (isnull(`service_center_booking_action`.`model_number`) or (`service_center_booking_action`.`model_number` = '')) then `ud`.`sf_model_number` else `service_center_booking_action`.`model_number` end) AS `Model`,(case when (isnull(`ud`.`serial_number`) or (`ud`.`serial_number` = '')) then '' else concat('\'',group_concat(`ud`.`serial_number` separator ',')) end) AS `Product Serial Number`,`services`.`services` AS `Product`,`ud`.`appliance_category` AS `Category`,`ud`.`appliance_capacity` AS `Capacity`,`ud`.`appliance_description` AS `Description`,`users`.`name` AS `Customer Name`,`booking_details`.`booking_pincode` AS `Pincode`,`booking_details`.`city` AS `City`,`booking_details`.`state` AS `State`,`booking_details`.`booking_primary_contact_no` AS `Phone`,`users`.`user_email` AS `Email`,`booking_details`.`request_type` AS `Service Type`,`booking_details`.`booking_remarks` AS `Customer Remarks`,`booking_details`.`reschedule_reason` AS `Reschedule Remarks`,(case when ((`booking_details`.`current_status` = 'Completed') or (`booking_details`.`current_status` = 'Cancelled')) then `booking_details`.`closing_remarks` else '' end) AS `Closing Remarks`,(case when (`booking_details`.`current_status` = 'Cancelled') then `b_cr`.`reason` else group_concat(`ssba_cr`.`reason` separator ',') end) AS `Cancellation Remarks`,date_format(str_to_date(`booking_details`.`booking_date`,'%Y-%m-%d'),'%d/%c/%Y') AS `Current Booking Date`,date_format(str_to_date(`booking_details`.`initial_booking_date`,'%Y-%m-%d'),'%d/%c/%Y') AS `First Booking Date`,`booking_details`.`booking_timeslot` AS `Timeslot`,`booking_details`.`partner_internal_status` AS `Final Status Level 2`,`booking_details`.`current_status` AS `Final Status Level 1`,(case when (`booking_details`.`is_upcountry` = '0') then 'Local' else 'Upcountry' end) AS `Is Upcountry`,(case when ((group_concat(distinct `spare_parts_details`.`status` separator ',') <> 'Cancelled') and (`spare_parts_details`.`parts_requested` is not null)) then 'Yes' else 'No' end) AS `Is Part Involve`,`booking_details`.`actor` AS `Dependency On`,date_format(`booking_details`.`service_center_closed_date`,'%d/%c/%Y') AS `Completion Date`,(case when (`booking_details`.`service_center_closed_date` is not null) then (case when ((to_days(cast(`booking_details`.`service_center_closed_date` as date)) - to_days(str_to_date(`booking_details`.`initial_booking_date`,'%Y-%m-%d'))) < 0) then 0 else (to_days(cast(`booking_details`.`service_center_closed_date` as date)) - to_days(str_to_date(`booking_details`.`initial_booking_date`,'%Y-%m-%d'))) end) else '' end) AS `TAT`,(case when ((`booking_details`.`current_status` in ('Pending','Rescheduled','FollowUp')) and isnull(`booking_details`.`service_center_closed_date`)) then (to_days(curdate()) - to_days(str_to_date(`booking_details`.`initial_booking_date`,'%Y-%m-%d'))) else '' end) AS `Ageing`,`booking_details`.`rating_stars` AS `Rating`,`booking_details`.`rating_comments` AS `Rating Comments`,group_concat(distinct `i`.`part_number` separator ' | ') AS `Requested Part Code`,group_concat(distinct `spare_parts_details`.`parts_requested` separator ' | ') AS `Requested Part`,group_concat(date_format(`spare_parts_details`.`date_of_request`,'%d/%c/%Y') separator ' | ') AS `Part Requested Date`,group_concat(distinct `im`.`part_number` separator ' | ') AS `Shipped Part Code`,group_concat(distinct `spare_parts_details`.`parts_shipped` separator ' | ') AS `Shipped Part`,group_concat(date_format(`spare_parts_details`.`shipped_date`,'%d/%c/%Y') separator ' | ') AS `Part Shipped Date`,group_concat(date_format(`spare_parts_details`.`acknowledge_date`,'%d/%c/%Y') separator ' | ') AS `SF Acknowledged Date`,(case when (`spare_parts_details`.`defective_part_shipped` is not null) then group_concat(distinct `im`.`part_number` separator ' | ') else '' end) AS `Shipped Defective Part Code`,group_concat(`spare_parts_details`.`defective_part_shipped` separator ' | ') AS `Shipped Defective Part`,group_concat(date_format(`spare_parts_details`.`defective_part_shipped_date`,'%d/%c/%Y') separator ' | ') AS `Defective Part Shipped Date`,`engineer_details`.`name` AS `engineer_name`,(case when (`creation_symptom`.`symptom` is not null) then `creation_symptom`.`symptom` else 'Default' end) AS `Booking Symptom`,`completion_symptom`.`symptom` AS `Completion Symptom`,`defect`.`defect` AS `Defect`,`symptom_completion_solution`.`technical_solution` AS `Solution` from ((((((((((((((((((`booking_details` join `booking_unit_details` `ud` on((`booking_details`.`booking_id` = `ud`.`booking_id`))) join `services` on((`booking_details`.`service_id` = `services`.`id`))) join `users` on((`booking_details`.`user_id` = `users`.`user_id`))) left join `booking_comments` on((`booking_comments`.`booking_id` = `booking_details`.`booking_id`))) left join `dealer_details` on((`dealer_details`.`dealer_id` = `booking_details`.`dealer_id`))) left join `spare_parts_details` on((`spare_parts_details`.`booking_id` = `booking_details`.`booking_id`))) left join `inventory_master_list` `i` on((`i`.`inventory_id` = `spare_parts_details`.`requested_inventory_id`))) left join `inventory_master_list` `im` on((`im`.`inventory_id` = `spare_parts_details`.`shipped_inventory_id`))) left join `service_center_booking_action` on((`service_center_booking_action`.`booking_id` = `booking_details`.`booking_id`))) left join `service_centres` on((`service_center_booking_action`.`service_center_id` = `service_centres`.`id`))) left join `booking_cancellation_reasons` `b_cr` on((`booking_details`.`cancellation_reason` = `b_cr`.`id`))) left join `booking_cancellation_reasons` `ssba_cr` on((`service_center_booking_action`.`cancellation_reason` = `ssba_cr`.`id`))) left join `booking_symptom_defect_details` on((`booking_details`.`booking_id` = `booking_symptom_defect_details`.`booking_id`))) left join `symptom` `creation_symptom` on((`booking_symptom_defect_details`.`symptom_id_booking_creation_time` = `creation_symptom`.`id`))) left join `symptom` `completion_symptom` on((`booking_symptom_defect_details`.`symptom_id_booking_completion_time` = `completion_symptom`.`id`))) left join `defect` on((`booking_symptom_defect_details`.`defect_id_completion` = `defect`.`id`))) left join `symptom_completion_solution` on((`booking_symptom_defect_details`.`solution_id` = `symptom_completion_solution`.`id`))) left join `engineer_details` on((`engineer_details`.`id` = `booking_details`.`assigned_engineer_id`))) where ((`ud`.`product_or_services` <> 'Product') and ((`booking_details`.`create_date` > (curdate() - interval 3 month)) or (`booking_details`.`current_status` not in ('Cancelled','Completed')))) group by `ud`.`booking_id`
---Abhishek---End Query ----

