CREATE TABLE `paytm_cashback_details` (
  `id` int(11) NOT NULL,
  `cashback_txn_id` varchar(64) NOT NULL,
  `cashback_txn_id_paytm` varchar(64) NOT NULL,
  `cashback_from` varchar(64) NOT NULL,
  `cash_back_status` varchar(64) NOT NULL,
  `cash_back_message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `paytm_cashback_details`
--
ALTER TABLE `paytm_cashback_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `paytm_cashback_details`
--
ALTER TABLE `paytm_cashback_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;
ALTER TABLE `paytm_cashback_details` ADD `transaction_id` VARCHAR(64) NOT NULL AFTER `id`;
ALTER TABLE `paytm_cashback_details` ADD `order_id` VARCHAR(64) NOT NULL AFTER `transaction_id`;

ALTER TABLE `paytm_transaction_callback`
  DROP `cashback_amount`,
  DROP `cashback_txn_id`,
  DROP `cashback_txn_id_paytm`,
  DROP `cashback_from`,
  DROP `cash_back_status`,
  DROP `cash_back_message`,
  DROP `cashback_date`;
ALTER TABLE `paytm_cashback_details` ADD `booking_id` VARCHAR(64) NOT NULL AFTER `id`;
ALTER TABLE `paytm_cashback_details` ADD `cashback_amount` VARCHAR(64) NOT NULL AFTER `order_id`;