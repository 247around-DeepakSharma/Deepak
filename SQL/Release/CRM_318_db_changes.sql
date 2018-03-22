CREATE TABLE `paytm_payment_qr_code` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `amount` varchar(20) DEFAULT NULL,
  `order_id` varchar(40) NOT NULL,
  `channel` varchar(100) NOT NULL,
  `qr_data` text NOT NULL,
  `qr_path` longtext NOT NULL,
  `qr_image_url` text NOT NULL,
  `qr_image_name` text NOT NULL,
  `txn_response_contact` varchar(28) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `paytm_payment_qr_code`
--
ALTER TABLE `paytm_payment_qr_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `paytm_payment_qr_code`
--
ALTER TABLE `paytm_payment_qr_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;