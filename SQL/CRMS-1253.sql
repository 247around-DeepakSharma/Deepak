CREATE TABLE `sf_miscellaneous_details` (
  `id` int(10) NOT NULL,
  `vendor_id` varchar(100) NOT NULL,
  `stamp_file` varchar(255) NOT NULL,
  `status` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `sf_miscellaneous_details`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `sf_miscellaneous_details`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `sf_miscellaneous_details` 
  RENAME TO sf_stamp_details;
ALTER TABLE sf_stamp_details ADD agent_id int(10), ADD agent_type varchar(255);
