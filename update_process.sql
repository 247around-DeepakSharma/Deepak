
--
-- Table structure for table `assigned_engineer`
--

CREATE TABLE `assigned_engineer` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(250) NOT NULL,
  `service_center_id` int(20) DEFAULT NULL,
  `engineer_id` varchar(20) DEFAULT NULL,
  `current_state` varchar(100) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assigned_engineer`
--
ALTER TABLE `assigned_engineer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assigned_engineer`
--
ALTER TABLE `assigned_engineer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `booking_details` ADD `count_reschedule` INT(10) NULL DEFAULT '0' AFTER `potential_value`, ADD `count_escalation` INT(10) NULL DEFAULT '0' AFTER `count_reschedule`;

ALTER TABLE  `booking_state_change` ADD  `service_center_id` VARCHAR( 20 ) NULL DEFAULT NULL AFTER  `partner_id` ;
ALTER TABLE `vendor_escalation_policy` ADD `entity` VARCHAR(20) NULL DEFAULT NULL AFTER `escalation_reason`;

-

-- --------------------------------------------------------

--
-- Table structure for table `penalty_details`
--

CREATE TABLE `penalty_details` (
  `id` int(11) NOT NULL,
  `partner_id` int(20) DEFAULT NULL,
  `escalation_id` int(20) DEFAULT NULL,
  `criteria` varchar(200) DEFAULT NULL,
  `penalty_amount` varchar(20) DEFAULT NULL,
  `unit_%_rate` int(20) DEFAULT NULL,
  `active` int(10) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `penalty_details`
--

INSERT INTO `penalty_details` (`id`, `partner_id`, `escalation_id`, `criteria`, `penalty_amount`, `unit_%_rate`, `active`) VALUES
(1, 1, NULL, 'Engineer not assign', '50', NULL, 1),
(2, 1, NULL, 'Booking is not updated by service center', '50', NULL, 1),
(3, NULL, 1, NULL, '20', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `penalty_details`
--
ALTER TABLE `penalty_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penalty_details`
--
ALTER TABLE `penalty_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


-- --------------------------------------------------------

--
-- Table structure for table `penalty_on_booking`
--

CREATE TABLE `penalty_on_booking` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(100) NOT NULL,
  `service_center_id` int(20) NOT NULL,
  `criteria_id` int(20) NOT NULL,
  `penalty_amount` int(20) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `penalty_on_booking`
--
ALTER TABLE `penalty_on_booking`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penalty_on_booking`
--
ALTER TABLE `penalty_on_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



--
-- Table structure for table `sc_crimes`
--

CREATE TABLE `sc_crimes` (
  `id` int(11) NOT NULL,
  `service_center_id` int(11) DEFAULT NULL,
  `engineer_not_assigned` int(11) DEFAULT NULL,
  `booking_not_updated` int(11) DEFAULT NULL,
  `total_missed_target` int(11) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sc_crimes`
--
ALTER TABLE `sc_crimes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sc_crimes`
--
ALTER TABLE `sc_crimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


  
ALTER TABLE `internal_status` ADD `sf_update_active` INT NULL DEFAULT '0' AFTER `active`, ADD `method_name` VARCHAR(100) NULL DEFAULT NULL AFTER `sf_update_active`, ADD `redirect_url` VARCHAR(100) NULL DEFAULT NULL AFTER `method_name`;
ALTER TABLE `service_centres` ADD `is_update` INT NULL DEFAULT '0' AFTER `sc_code`, ADD `is_penalty` INT NULL DEFAULT '0' AFTER `is_update`;
ALTER TABLE `service_centres` ADD `penalty_activation_date` DATE NULL DEFAULT NULL AFTER `is_penalty`;

ALTER TABLE `sms_sent_details` ADD `sms_tag` VARCHAR(50) NULL AFTER `booking_id`;




--
-- Table structure for table `spare_parts_details`
--

CREATE TABLE `spare_parts_details` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(100) NOT NULL,
  `partner_id` int(20) NOT NULL,
  `service_center_id` int(20) NOT NULL,
  `model_number` varchar(200) DEFAULT NULL,
  `serial_number` varchar(200) DEFAULT NULL,
  `date_of_purchase` date DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_of_request` date DEFAULT NULL,
  `shipped_date` date DEFAULT NULL,
  `acknowledge_date` date DEFAULT NULL,
  `parts_requested` varchar(200) DEFAULT NULL,
  `parts_shipped` varchar(200) DEFAULT NULL,
  `invoice_pic` varchar(200) DEFAULT NULL,
  `panel_pic` varchar(200) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `remarks_by_sc` varchar(250) DEFAULT NULL,
  `remarks_by_partner` varchar(250) DEFAULT NULL,
  `courier_name_by_partner` varchar(100) DEFAULT NULL,
  `awb_by_partner` varchar(50) DEFAULT NULL,
  `edd` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `spare_parts_details`
--
ALTER TABLE `spare_parts_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `spare_parts_details`
--
ALTER TABLE `spare_parts_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `service_center_booking_action` CHANGE `update_date` `update_date` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `booking_details` CHANGE `update_date` `update_date` DATETIME on update CURRENT_TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE  `booking_details` ADD  `assigned_engineer_id` INT( 20 ) NULL DEFAULT NULL AFTER  `assigned_vendor_id` ;