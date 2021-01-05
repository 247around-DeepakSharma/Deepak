INSERT INTO `partner_summary_report_mapping` (`id`, `Title`, `sub_query`, `is_default`, `partner_id`, `is_active`, `index_in_report`) VALUES ('', 'Agent Name', '(CASE WHEN booking_details.created_by_agent_type IN (\'partner\', \'dealers\') then entity_login_table.agent_name WHEN booking_details.created_by_agent_type = \'website\' THEN \'website\' ELSE employee.full_name END) as \'Agent Name\'', '1', '', '1', '3');
