ALTER TABLE `spare_parts_details` ADD `model_number_shipped` VARCHAR(256) NULL DEFAULT NULL AFTER `date_of_request`;
ALTER TABLE `partners` ADD `is_wh` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1=\'working as a warehouse model\',0 = \'nor working as a warehouse model\'' AFTER `is_prepaid`;
ALTER TABLE `service_centres` ADD `is_wh` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1 = \'working as a warehouse\', 0 = \'not working as a warehouse\'' AFTER `is_cp`;

DROP TABLE IF EXISTS `contact_person`;
CREATE TABLE `contact_person` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `officail_email` varchar(256) NOT NULL,
  `alternate_email` varchar(256) DEFAULT NULL,
  `official_contact_number` varchar(256) NOT NULL,
  `alternate_contact_number` varchar(256) DEFAULT NULL,
  `permanent_address` varchar(1024) NOT NULL,
  `correspondence_address` varchar(1024) NOT NULL,
  `role` varchar(64) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(128) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_person`
--
ALTER TABLE `contact_person`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_person`

DROP TABLE IF EXISTS `warehouse_details`;
CREATE TABLE `warehouse_details` (
  `id` int(11) NOT NULL,
  `warehouse_address_line1` varchar(512) NOT NULL,
  `warehouse_address_line2` varchar(512) NOT NULL,
  `warehouse_city` varchar(64) NOT NULL,
  `warehouse_region` varchar(64) NOT NULL,
  `warehouse_pincode` int(6) NOT NULL,
  `warehouse_state` varchar(256) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` varchar(256) NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `warehouse_details`
--
ALTER TABLE `warehouse_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `warehouse_details`
--
ALTER TABLE `warehouse_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `warehouse_person_relationship`;
CREATE TABLE `warehouse_person_relationship` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `contact_person_id` int(11) NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `warehouse_person_relationship`
--
ALTER TABLE `warehouse_person_relationship`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_id` (`warehouse_id`),
  ADD KEY `contact_person_id` (`contact_person_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `warehouse_person_relationship`
--
ALTER TABLE `warehouse_person_relationship`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `warehouse_person_relationship`
--
ALTER TABLE `warehouse_person_relationship`
  ADD CONSTRAINT `warehouse_person_relationship_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse_details` (`id`),
  ADD CONSTRAINT `warehouse_person_relationship_ibfk_2` FOREIGN KEY (`contact_person_id`) REFERENCES `contact_person` (`id`);

DROP TABLE IF EXISTS `warehouse_state_relationship`;
CREATE TABLE `warehouse_state_relationship` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `state` varchar(64) NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `warehouse_state_relationship`
--
ALTER TABLE `warehouse_state_relationship`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_id` (`warehouse_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `warehouse_state_relationship`
--
ALTER TABLE `warehouse_state_relationship`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `warehouse_state_relationship`
--
ALTER TABLE `warehouse_state_relationship`
  ADD CONSTRAINT `warehouse_state_relationship_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
