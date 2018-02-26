CREATE TABLE `paytm_transaction_callback` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `paytm_order_id` varchar(100) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `txn_id` varchar(200) NOT NULL,
  `paid_amount` varchar(20) DEFAULT NULL,
  `user_guid` varchar(200) NOT NULL,
  `refund_amount` varchar(20) NOT NULL,
  `is_cashback` int(2) NOT NULL DEFAULT '0',
  `cash_back_status` varchar(100) DEFAULT NULL,
  `cash_back_message` text,
  `create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `paytm_transaction_callback`
--
ALTER TABLE `paytm_transaction_callback`
  ADD PRIMARY KEY (`id`,`user_guid`,`refund_amount`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `paytm_transaction_callback`
--
ALTER TABLE `paytm_transaction_callback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;
