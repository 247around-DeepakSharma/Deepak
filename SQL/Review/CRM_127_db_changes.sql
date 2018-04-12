CREATE TABLE `payment_transaction` (
  `txn_id` int(11) NOT NULL,
  `order_id` varchar(256) NOT NULL,
  `gw_txn_id` varchar(128) DEFAULT NULL,
  `txn_amount` decimal(10,2) NOT NULL,
  `gw_txn_status` varchar(128) NOT NULL,
  `gw_response_code` varchar(128) NOT NULL,
  `gw_response_msg` varchar(128) NOT NULL,
  `final_txn_status` varchar(128) NOT NULL,
  `final_response_code` varchar(128) NOT NULL,
  `final_response_msg` varchar(128) NOT NULL,
  `bank_txn_id` varchar(128) DEFAULT NULL,
  `payment_mode` varchar(64) DEFAULT NULL,
  `bank_name` varchar(256) DEFAULT NULL,
  `gw_name` varchar(128) DEFAULT NULL,
  `txn_date` datetime DEFAULT NULL,
  `order_details` varchar(512) DEFAULT NULL,
  `contact_number` int(11) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `is_txn_successfull` tinyint(1) NOT NULL DEFAULT '0',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payment_transaction`
--
ALTER TABLE `payment_transaction`
  ADD PRIMARY KEY (`txn_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payment_transaction`
--
ALTER TABLE `payment_transaction`
  MODIFY `txn_id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `gateway_booking_payment_details` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(256) NOT NULL,
  `customer_id` varchar(1024) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `phone_number` varchar(64) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `hash_key` varchar(1024) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = ''settled'', 0 = ''un-settled''',
  `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gateway_booking_payment_details`
--
ALTER TABLE `gateway_booking_payment_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gateway_booking_payment_details`
--
ALTER TABLE `gateway_booking_payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'payment_transaction_email', '%s for Blackmelon Advance Technology Company Private Limited', '', 'billing@247around.com', '', '', '', '1', '2016-06-17 00:00:00');

INSERT INTO `sms_template` (`id`, `tag`, `template`, `comments`, `active`, `create_date`) VALUES (NULL, 'gateway_payment_link_sms', 'Dear Customer, Please click on this link %s to complete the payment of %s for 247around.', '', '1', '2018-04-04 14:36:43');
