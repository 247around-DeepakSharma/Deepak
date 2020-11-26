-- booking_id_is_0_in_sc_action_table
UPDATE `query_report` SET `query1` = 'Select count(*) as count,
GROUP_CONCAT( booking_id SEPARATOR \',\') as booking_id 
FROM (`service_center_booking_action`) \r\n
WHERE (`booking_id` = \'0\' OR `unit_details_id` IS NULL OR `unit_details_id` = \'0\' 
OR `service_center_id` = \'0\' OR `current_status` = \'0\' OR `internal_status` = \'0\') 
AND create_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\n' WHERE `query_report`.`id` = 25;
-- booking_status_is_empty
UPDATE `query_report` SET `query1` = 'Select count(*) as count,
GROUP_CONCAT( booking_details.booking_id SEPARATOR \',\') as booking_id 
FROM (`booking_unit_details`) JOIN `booking_details` ON `booking_details`.`booking_id` = `booking_unit_details`.`booking_id` 
WHERE `booking_status` IN (\'\', NULL) AND `booking_details`.`current_status` IN (\'Completed\') 
AND `booking_details`.`create_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR)' 
WHERE `query_report`.`id` = 24;
-- Tax Rate is 0 
UPDATE `query_report` SET `query1` = 'Select count(*) as count,
GROUP_CONCAT( booking_details.booking_id SEPARATOR \',\') as booking_id 
FROM (`booking_unit_details`) JOIN `booking_details` ON `booking_details`.`booking_id` = `booking_unit_details`.`booking_id` 
WHERE `tax_rate` <= \'0\' AND `booking_details`.`current_status` IN (\'Pending\', \'Completed\', \'Rescheduled\') 
AND `booking_details`.`create_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND `booking_unit_details`.`booking_status` IN (\'Completed\', \'\')' 
WHERE `query_report`.`id` = 23;
-- check_price_tags (NULL)
UPDATE `query_report` SET `query1` = 'select count(booking_unit_details.id) as count,
GROUP_CONCAT( booking_unit_details.booking_id SEPARATOR \',\') as booking_id \r\n
FROM (`booking_unit_details`) JOIN `booking_details` ON `booking_details`.`booking_id` = `booking_unit_details`.`booking_id` \r\n
WHERE `price_tags` IN (\'\', NULL) AND `booking_details`.`current_status` IN (\'Pending\', \'Completed\', \'Rescheduled\') \r\nAND `booking_details`.`create_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND `booking_details`.`booking_id` NOT LIKE \'%Q-%\'\r\n' WHERE `query_report`.`id` = 22;
-- booking_unit_details_has_0_in_column
UPDATE `query_report` SET `query1` = 'select count(id) as count,GROUP_CONCAT( booking_id SEPARATOR \',\') as booking_id FROM (`booking_unit_details`)\r\n WHERE (`booking_id` = \'0\' OR `partner_id` = \'0\' OR `appliance_id` = \'0\') AND `create_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\n' WHERE `query_report`.`id` = 21;
-- paid_amount_repeat_bookings_(Promo SMS)
-- UPDATE `query_report` SET `query1` = 'SELECT sum(b.`amount_paid`) AS count FROM `booking_details` as b, `sms_sent_details` as s \r\nWHERE b.`partner_id` = 247001 AND b.`create_date` >=DATE_SUB(NOW(),INTERVAL 1 YEAR) \r\nAND (s.`sms_tag` = \'completed_promotional_sms_1\' OR s.`sms_tag` = \'completed_promotional_sms_2\') \r\nAND b.user_id = s.type_id AND current_status = \'Completed\' AND amount_paid > 0\r\n' WHERE `query_report`.`id` = 20;
-- paid_completed_repeat_bookings_(Promo SMS)
-- UPDATE `query_report` SET `query1` = 'SELECT count(distinct b.`booking_id`) AS count FROM `booking_details` as b, `sms_sent_details` as s \r\nWHERE b.`partner_id` = 247001 AND b.`create_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\nAND (s.`sms_tag` = \'completed_promotional_sms_1\' OR s.`sms_tag` = \'completed_promotional_sms_2\') \r\nAND b.user_id = s.type_id AND current_status = \'Completed\' AND amount_paid > 0' WHERE `query_report`.`id` = 19;
-- total_completed_repeat_bookings_(Promo SMS)
-- UPDATE `query_report` SET `query1` = 'SELECT count(distinct b.`booking_id`) AS count FROM `booking_details` as b, `sms_sent_details` as s WHERE b.`partner_id` = 247001 AND b.`create_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND (s.`sms_tag` = \'completed_promotional_sms_1\' OR s.`sms_tag` = \'completed_promotional_sms_2\') AND b.user_id = s.type_id AND current_status = \'Completed\'' WHERE `query_report`.`id` = 18;
-- repeat_bookings_(promo_sms)
-- UPDATE `query_report` SET `query1` = 'SELECT count(distinct b.`booking_id`) AS count FROM `booking_details` as b, `sms_sent_details` as s WHERE b.`partner_id` = 247001 AND b.`create_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND (s.`sms_tag` = \'completed_promotional_sms_1\' OR s.`sms_tag` = \'completed_promotional_sms_2\') AND b.user_id = s.type_id' WHERE `query_report`.`id` = 17;
-- snapdeal_leads
-- UPDATE `query_report` SET `query1` = 'SELECT s.services, appliance_brand, bd.`booking_id` , `type` , `current_status` , bd.`city` , bd.`state` , bd.`create_date` \r\nFROM `booking_details` AS bd, `booking_unit_details` AS ud, services AS s\r\nWHERE bd.`partner_id` =1\r\nAND bd.`create_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\nAND bd.booking_id = ud.booking_id\r\nAND bd.service_id = s.id\r\nAND product_or_services != \'Product\'' WHERE `query_report`.`id` = 13;
-- OTG_cancelled_by_Ranju
-- UPDATE `query_report` SET `query1` = 'SELECT COUNT(*) as count FROM booking_state_change as bsc, booking_unit_details as bd WHERE `new_state` LIKE \'Cancelled\' AND `agent_id` = 15 AND bsc.`create_date` > DATE_SUB(NOW(),INTERVAL 1 YEAR) AND bsc.booking_id=bd.booking_id AND bd.service_id=42 AND bsc.remarks = \'Installation Not Required\' AND bd.appliance_description LIKE \'%OTG%\'' WHERE `query_report`.`id` = 11;
-- status_is_empty_for_completed_bookings
UPDATE `query_report` SET `query1` = 'Select count(*) as count, GROUP_CONCAT( bd.booking_id SEPARATOR \',\') as booking_id FROM `booking_details` as bd, booking_unit_details as ud WHERE bd.`closed_date` >= DATE_FORMAT(CURRENT_DATE - INTERVAL 12 MONTH, \'%Y/%m/01\') AND bd.`current_status`=\'Completed\' AND bd.`booking_id`=ud.`booking_id` AND ud.`booking_status`=\'\'\r\n' WHERE `query_report`.`id` = 26;
-- product_OR_services_field_has_empty
UPDATE `query_report` SET `query1` = 'Select count(*) as count,GROUP_CONCAT( booking_id SEPARATOR \',\') as booking_id FROM booking_unit_details as ud WHERE ud.`ud_closed_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND ud.`booking_status`=\'Completed\' AND ud.`product_or_services`=\'\';\r\n' WHERE `query_report`.`id` = 27;
-- prices_has_negative_value 
UPDATE `query_report` SET `query1` = 'Select count(*) as count,GROUP_CONCAT( booking_id SEPARATOR \',\') as booking_id FROM booking_unit_details where (customer_net_payable <0 OR customer_total < 0 OR partner_net_payable <0 OR around_net_payable <0 OR customer_paid_basic_charges< 0 OR partner_paid_basic_charges<0 OR around_paid_basic_charges<0 OR around_comm_basic_charges<0 OR around_st_or_vat_basic_charges<0 OR vendor_basic_charges <0 OR vendor_to_around <0 OR around_to_vendor<0 OR vendor_st_or_vat_basic_charges<0 OR customer_paid_extra_charges< 0 OR around_comm_extra_charges<0 OR around_st_extra_charges<0 OR vendor_extra_charges< 0 OR vendor_st_extra_charges<0 OR customer_paid_parts<0 OR around_comm_parts<0 OR around_st_parts<0 OR vendor_parts<0 OR vendor_st_parts<0) AND create_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\n' WHERE `query_report`.`id` = 28;
-- partner_paid_basic_charge_is_not_correct
UPDATE `query_report` SET `query1` = 'Select count(*) as count,GROUP_CONCAT( booking_id SEPARATOR \',\') as booking_id FROM `booking_unit_details` WHERE `partner_net_payable` >0 AND `partner_paid_basic_charges` != ( partner_net_payable + ( `partner_net_payable` * `tax_rate` ) /100 ) AND create_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND booking_status = \'Completed\'\r\n' WHERE `query_report`.`id` = 29;
-- service_which_was_closed_at_0_prices 
UPDATE `query_report` SET `query1` = 'Select count(*) as count,GROUP_CONCAT( booking_id SEPARATOR \',\') as booking_id FROM `booking_unit_details` WHERE `booking_status` LIKE \'Completed\' AND `customer_net_payable` >0 AND `customer_paid_basic_charges` =0 AND create_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\n' WHERE `query_report`.`id` = 30;
-- stand_is_not_added_in_the_unit_details
UPDATE `query_report` SET `query1` = 'Select count(*) as count,GROUP_CONCAT( b1.booking_id SEPARATOR \',\') as booking_id FROM booking_details AS b1, booking_unit_details AS u1 WHERE u1.appliance_brand IN ( \'Sony\', \'Panasonic\', \'LG\', \'Samsung\' ) AND b1.current_status = \'Completed\' AND b1.closed_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND b1.service_id = \'46\' AND b1.booking_id = u1.booking_id AND b1.current_status != \'Cancelled\' AND b1.partner_id IN ( \'1\', \'3\' ) AND NOT EXISTS ( SELECT * FROM booking_unit_details AS u2 WHERE b1.booking_id = u2.booking_id AND u2.price_tags = \'Wall Mount Stand\' ) ORDER BY `b1`.`create_date` DESC\r\n' WHERE `query_report`.`id` = 31;
-- duplicate_entry_in_unit_details 
UPDATE `query_report` SET `query1` = 'SELECT count(*) as count,GROUP_CONCAT( b1.booking_id SEPARATOR \',\') as booking_id \r\nFROM `booking_unit_details` AS b1, `booking_unit_details` AS b2, booking_details AS b\r\nWHERE b1.`booking_id` = b2.`booking_id` \r\nAND b1.`price_tags` = b2.`price_tags` \r\nAND b1.id != b2.id\r\nAND b1.create_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\nAND (\r\nb1.booking_status != \'Cancelled\'\r\nOR b2.booking_status != \'Cancelled\'\r\n)\r\nAND (\r\nb2.booking_status\r\nIN (\r\n\'Completed\', \'\'\r\n)\r\nOR b1.booking_status\r\nIN (\r\n\'Completed\', \'\'\r\n)\r\n)\r\nAND b.booking_id = b1.booking_id\r\nAND b.booking_id = b2.booking_id\r\nAND b.quantity =1\r\n' WHERE `query_report`.`id` = 32;
-- booking_Id_is_not_exist_in_unit_details
UPDATE `query_report` SET `query1` = 'Select count(*) as count,GROUP_CONCAT( b1.booking_id SEPARATOR \',\') as booking_id FROM booking_details AS b1 WHERE NOT EXISTS ( SELECT booking_id FROM booking_unit_details WHERE b1.booking_id = booking_unit_details.booking_id ) AND create_date >=DATE_SUB(NOW(),INTERVAL 1 YEAR) AND b1.current_status != \'Cancelled\'\r\n' WHERE `query_report`.`id` = 33;
-- customer_total_is_zero
UPDATE `query_report` SET `query1` = 'Select count(*) as count,GROUP_CONCAT( booking_unit_details.booking_id SEPARATOR \',\') as booking_id FROM booking_unit_details, booking_details WHERE \r\ncustomer_total = 0 \r\nAND booking_details.create_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR) \r\nAND booking_details.booking_id = booking_unit_details.booking_id \r\nAND booking_details.current_status IN ( \'Pending\', \'Rescheduled\', \'Completed\' ) \r\nAND booking_status IN ( \'Completed\', \'\') AND price_tags NOT IN (\"Repeat Booking\",\"Spare Parts\")\r\n' WHERE `query_report`.`id` = 34;
-- service_which_was_closed_at_0_prices 
UPDATE `query_report` SET `query1` = 'Select count(*) as count,GROUP_CONCAT( booking_id SEPARATOR \',\') as booking_id FROM `booking_unit_details` WHERE `booking_status` LIKE \'Completed\' AND `customer_net_payable` >0 AND `customer_paid_basic_charges` =0 AND create_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\n' WHERE `query_report`.`id` = 36;
-- Booking Completed But Pending in Unit
UPDATE `query_report` SET `query1` = 'SELECT count(DISTINCT ud.booking_id) as count, GROUP_CONCAT( DISTINCT ud.booking_id SEPARATOR \',\') as booking_id\r\nFROM `booking_unit_details` AS ud, booking_details\r\nWHERE booking_status\r\nIN (\r\n\'Completed\'\r\n)\r\nAND ud.booking_id = booking_details.booking_id\r\nAND current_status IN (\'Pending\', \'Rescheduled\')\r\nAND ud.create_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\n' WHERE `query_report`.`id` = 41;
-- Unit Completed But Pending in Detais
UPDATE `query_report` SET `query1` = 'SELECT count(DISTINCT ud.booking_id) as count, GROUP_CONCAT( DISTINCT ud.booking_id SEPARATOR \',\') as booking_id FROM `booking_unit_details` AS ud, booking_details WHERE booking_status In (\'Pending\', \'FollowUp\') AND ud.booking_id = booking_details.booking_id AND current_status = \'Completed\' AND ud.create_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)\r\n' WHERE `query_report`.`id` = 42;
-- missing_in_booking_tat
UPDATE `query_report` SET `query1` = 'SELECT COUNT(booking_details.booking_id)as count FROM `booking_details` LEFT JOIN booking_tat ON booking_tat.booking_id = booking_details.booking_id WHERE DATE(booking_details.closed_date)>= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND booking_tat.booking_id IS NULL AND booking_details.type != \'Query\'\r\n' WHERE `query_report`.`id` = 49;
-- Bookings Closed By Engineer - Current Month
UPDATE `query_report` SET `query1` = 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND closed_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND internal_status = \"Completed\"\r\n' WHERE `query_report`.`id` = 59;
-- Bookings Closed By Engineer - Current Month
UPDATE `query_report` SET `query2` = 'SELECT count(DISTINCT(booking_id)) as count FROM `engineer_booking_action` WHERE closed_date IS NOT NULL AND closed_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR) AND internal_status = \"Cancelled\" AND engineer_booking_action.booking_id in (select DISTINCT booking_id from engineer_booking_action group by booking_id having count(DISTINCT internal_status)=1)\r\n' WHERE `query_report`.`id` = 59;

