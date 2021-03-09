INSERT INTO `file_type` (`file_type`, `max_allowed_size`, `allowed_type`, `is_active`, `create_date`) VALUES
('Annual Maintenance Contract(AMC)', NULL, NULL, 1, '2021-02-15 09:29:42');
ALTER TABLE `booking_files` ADD `update_date` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `create_date`;