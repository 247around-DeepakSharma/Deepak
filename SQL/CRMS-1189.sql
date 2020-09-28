ALTER TABLE `inventory_master_list` ADD `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `create_date`;
ALTER TABLE `inventory_master_list` ADD `agent_id` INT NOT NULL AFTER `is_defective_required`;


