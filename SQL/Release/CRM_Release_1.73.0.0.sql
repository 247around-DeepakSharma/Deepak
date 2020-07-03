ALTER TABLE `paytm_transaction_callback` ADD `vendor_invoice_id` VARCHAR(64) NULL DEFAULT NULL AFTER `txn_id`;
ALTER TABLE `customer_invoice` ADD `from_date` DATE NULL DEFAULT NULL AFTER `invoice_date`, ADD `to_date` DATE NULL DEFAULT NULL AFTER `from_date`, ADD `due_date` DATE NULL DEFAULT NULL AFTER `to_date`;
INSERT INTO `email_template` (`id`, `tag`, `subject`, `template`, `from`, `to`, `cc`, `bcc`, `active`, `create_date`) VALUES (NULL, 'paytm_payment_voucher', 'Invoice for Booking - %s', 'Please find attached invoice from 247around for your completed booking with us.', 'billing@247around.com', '', '', 'abhaya@247around.com', '1', '2016-06-17 00:00:00');



--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(64) NOT NULL,
  `description` varchar(128) NOT NULL,
  `hsn_code` decimal(10,2) DEFAULT '0.00',
  `qty` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `taxable_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cgst_tax_rate` decimal(10,2) NOT NULL,
  `cgst_tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst_tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst_tax_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igst_tax_amount` decimal(10,2) DEFAULT '0.00',
  `igst_tax_rate` int(11) NOT NULL,
  `toal_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `create_date` datetime NOT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `bill_to_party` int(11) NOT NULL,
  `entity_to` varchar(56) NOT NULL,
  `bill_from_party` int(11) NOT NULL,
  `entity_from` varchar(56) NOT NULL,
  `main_invoice_file` varchar(128) NOT NULL,
  `duplicate_file` varchar(128) DEFAULT NULL,
  `triplicate_file` varchar(128) DEFAULT NULL,
  `invoice_excel` varchar(128) DEFAULT NULL,
  `booking_id` varchar(128) NOT NULL,
  `invoice_date` date NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `total_basic_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_cgst_tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_sgst_tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_igst_tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_invoice_amount` decimal(10,2) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `remarks` varchar(128) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



---Abhay
INSERT INTO `vendor_partner_variable_charges` (`id`, `entity_type`, `entity_id`, `charges_type`, `fixed_charges`, `percentage_charge`, `validity_in_month`, `status`, `create_date`, `update_date`, `active`) VALUES
(NULL, 'partner', '247130', 63, '45', '0.00', 0, 1, '2020-05-20 16:23:59', '2020-05-20 10:53:59', 1),
(NULL, 'partner', '247130', 62, '45', '0.00', 0, 1, '2020-05-20 16:23:37', '2020-05-20 10:53:37', 1);

INSERT INTO `variable_charges_type` (`id`, `name`, `type`, `description`, `hsn_code`, `gst_rate`, `is_fixed`, `updated_date`, `created_date`) VALUES
(NULL, 'Defective Return Packaging Charges( Large Box)', 'defective-packaging-variable-large-box', 'Defective Return Packaging( Large Box)', 998715, 18, 0, '2020-04-30 00:00:00', '2020-04-30 00:00:00'),
(NULL, 'Defective Return Packaging Charges( Small Box)', 'defective-packaging-variable-small-box', 'Defective Return Packaging( Small Box)', 998715, 18, 0, '2020-04-30 00:00:00', '2020-04-30 00:00:00');
ALTER TABLE `billed_msl_package` ADD `booking_id` TEXT NULL DEFAULT NULL AFTER `box_count`;
