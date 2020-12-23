INSERT INTO `third_party_api_credentials` (`company_name`, `secret_key`, `constant_tag`, `active`, `create_date`, `update_date`) VALUES
('Rapid Courier API', 'ebe9b6b60fmshb43757ccd4fd149p1fb41djsn615a8e4523a9', 'RAPID_TRACKING_API_KEY', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

--
-- Table structure for table `courier_tracking_details`
--

CREATE TABLE `courier_tracking_details` (
  `id` int(11) NOT NULL,
  `awb_number` varchar(256) NOT NULL,
  `carrier_code` varchar(256) NOT NULL,
  `checkpoint_status` varchar(256) DEFAULT NULL,
  `checkpoint_status_details` varchar(512) NOT NULL,
  `checkpoint_status_description` varchar(512) NOT NULL,
  `checkpoint_status_date` datetime NOT NULL,
  `api_id` varchar(512) NOT NULL,
  `final_status` varchar(64) NOT NULL,
  `checkpoint_item_node` varchar(64) DEFAULT NULL,
  `remarks` varchar(1024) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Indexes for table `courier_tracking_details`
--
ALTER TABLE `courier_tracking_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `spare_id_2` (`awb_number`(128),`checkpoint_status_date`,`checkpoint_status`(128),`checkpoint_status_details`(128),`checkpoint_status_description`(128),`api_id`(128)),
  ADD KEY `awb_number` (`awb_number`);


--
-- AUTO_INCREMENT for table `courier_tracking_details`
--
ALTER TABLE `courier_tracking_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- ALTER  table `courier_tracking_details` ADD NEW COLOUM
--

ALTER TABLE `courier_tracking_details` ADD `substatus` VARCHAR(250) NULL DEFAULT NULL AFTER `api_id`;

--
-- INSERT  table `email_template` ADD NEW TEMPATE FOR RAPIDAPI
--
INSERT INTO `email_template` (`tag`, `subject`, `template`, `booking_id`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES
(1'rapidapi_courier_api_failed_mail', 'RapidAPI Courier Tracking Failed', 'Dear Team,<br><br>  RapidAPI Courier Tracking Failed.<br><br> <b>Response From API</b><br><br> %s', NULL, 'noreply@247around.com', '', '', '', '1', '2020-10-09 18:30:00');
