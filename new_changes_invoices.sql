ALTER TABLE `booking_details` ADD `request_type` VARCHAR(1024) NULL AFTER `order_id`;
ALTER TABLE `booking_details` ADD  `reference_date` DATETIME NULL AFTER `partner_source`;
ALTER TABLE `booking_details` ADD `estimated_delivery_date` DATETIME NULL AFTER `reference_date`, 
ADD `shipped_date` DATETIME NULL AFTER `estimated_delivery_date`, 
ADD `delivery_date` DATETIME NULL AFTER `shipped_date`;
ALTER TABLE `booking_details` CHANGE `partner_source` `partner_source` VARCHAR(1024) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'source for this partner booking - excel file / email / api call / callcentre call / etc';
ALTER TABLE  `booking_details` ADD  `booking_landmark` VARCHAR( 1024 ) NOT NULL AFTER  `booking_location` ;
ALTER TABLE `booking_details` CHANGE `partner_source` `partner_source` VARCHAR(1024) 
CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'source for this partner booking - excel file / email / api call / callcentre call / etc';



-- ALTER TABLE `booking_details` DROP `booking_picture_file`;
-- ALTER TABLE `booking_details` DROP `discount_coupon`;
-- ALTER TABLE `booking_details` DROP `discount_amount`;
-- ALTER TABLE `booking_details` DROP `appliance_brand`;
-- ALTER TABLE `booking_details` DROP `appliance_category`;
-- ALTER TABLE `booking_details` DROP `appliance_capacity`;
-- ALTER TABLE `booking_details` DROP `items_selected`;
-- ALTER TABLE `booking_details` DROP `total_price`;
-- ALTER TABLE `booking_details` DROP `appliance_tags`;
-- ALTER TABLE `booking_details` DROP `service_charge`;
-- ALTER TABLE `booking_details` DROP `service_charge_collected_by`;
-- ALTER TABLE `booking_details` DROP `additional_service_charge`;
-- ALTER TABLE `booking_details` DROP `additional_service_charge_collected_by`;
-- ALTER TABLE `booking_details` DROP `parts_cost`;
-- ALTER TABLE `booking_details` DROP `parts_cost_collected_by`;
-- ALTER TABLE `booking_details` DROP `appliance_id`;
ALTER TABLE `booking_unit_details` ADD `partner_id` int(11) DEFAULT NULL AFTER `booking_id`;
ALTER TABLE `booking_unit_details` ADD `service_id` int(11) DEFAULT NULL AFTER `partner_id`;
ALTER TABLE `booking_unit_details` ADD `appliance_id` int(11) DEFAULT NULL AFTER `service_id`;
ALTER TABLE `booking_unit_details` ADD `appliance_size` VARCHAR(128) DEFAULT NULL AFTER `appliance_capacity`;
ALTER TABLE `booking_unit_details` ADD `appliance_serial_no` VARCHAR(1024) DEFAULT NULL AFTER `appliance_size`;
ALTER TABLE `booking_unit_details` ADD `appliance_description` VARCHAR(1024) DEFAULT NULL AFTER `appliance_serial_no`;
ALTER TABLE `booking_unit_details` ADD `booking_status` VARCHAR(1024) DEFAULT NULL AFTER `price_tags`;
ALTER TABLE `booking_unit_details` ADD `product_or_services` VARCHAR(1024) DEFAULT NULL AFTER `booking_status`;
ALTER TABLE `booking_unit_details` ADD `tax_rate` DECIMAL(10,3) AFTER `product_or_services`;
ALTER TABLE `booking_unit_details` ADD `customer_total` DECIMAL(10,2) AFTER `tax_rate`;
ALTER TABLE `booking_unit_details` ADD `customer_net_payable` DECIMAL(10,2) AFTER `customer_total`;
ALTER TABLE `booking_unit_details` ADD `partner_net_payable` DECIMAL(10,2) AFTER `customer_net_payable`;
ALTER TABLE `booking_unit_details` ADD `around_net_payable` DECIMAL(10,2) AFTER `partner_net_payable`;
ALTER TABLE `booking_unit_details` ADD `customer_paid_basic_charges` DECIMAL(10,2) AFTER `around_net_payable`;
ALTER TABLE `booking_unit_details` ADD `partner_paid_basic_charges` DECIMAL(10,2) AFTER `customer_paid_basic_charges`;
ALTER TABLE `booking_unit_details` ADD `around_paid_basic_charges` DECIMAL(10,2) AFTER `partner_paid_basic_charges`;
ALTER TABLE `booking_unit_details` ADD `around_comm_basic_charges` DECIMAL(10,2) AFTER `around_paid_basic_charges`;
ALTER TABLE `booking_unit_details` ADD `around_st_or_vat_basic_charges` DECIMAL(10,2) AFTER `around_comm_basic_charges`;
ALTER TABLE `booking_unit_details` ADD `vendor_basic_charges` DECIMAL(10,2) AFTER `around_st_or_vat_basic_charges`;
ALTER TABLE `booking_unit_details` ADD `vendor_to_around` DECIMAL(10,2) AFTER `vendor_basic_charges`;
ALTER TABLE `booking_unit_details` ADD `around_to_vendor` DECIMAL(10,2) AFTER `vendor_to_around`;
ALTER TABLE `booking_unit_details` ADD `vendor_st_or_vat_basic_charges` DECIMAL(10,2) AFTER `around_to_vendor`;
ALTER TABLE `booking_unit_details` ADD `customer_paid_extra_charges` DECIMAL(10,2) AFTER `vendor_st_or_vat_basic_charges`;
ALTER TABLE `booking_unit_details` ADD `around_comm_extra_charges` DECIMAL(10,2) AFTER `customer_paid_extra_charges`;
ALTER TABLE `booking_unit_details` ADD `around_st_extra_charges` DECIMAL(10,2) AFTER `around_comm_extra_charges`;
ALTER TABLE `booking_unit_details` ADD `vendor_extra_charges` DECIMAL(10,2) AFTER `around_st_extra_charges`;
ALTER TABLE `booking_unit_details` ADD `vendor_st_extra_charges` DECIMAL(10,2) AFTER `vendor_extra_charges`;
ALTER TABLE `booking_unit_details` ADD `customer_paid_parts` DECIMAL(10,2) AFTER `vendor_st_extra_charges`;
ALTER TABLE `booking_unit_details` ADD `around_comm_parts` DECIMAL(10,2) AFTER `customer_paid_parts`;
ALTER TABLE `booking_unit_details` ADD `around_st_parts` DECIMAL(10,2) AFTER `around_comm_parts`;
ALTER TABLE `booking_unit_details` ADD `vendor_parts` DECIMAL(10,2) AFTER `around_st_parts`;
ALTER TABLE `booking_unit_details` ADD `vendor_st_parts` DECIMAL(10,2) AFTER `vendor_parts`;


CREATE TABLE IF NOT EXISTS `service_centre_charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `state` varchar(50) NOT NULL,
  `service_id` varchar(10) NOT NULL,
  `category` varchar(1024) NOT NULL,
  `capacity` varchar(1024) DEFAULT NULL,
  `service_category` varchar(1024) NOT NULL,
  `product_or_services` varchar(1024) NOT NULL,
  `product_type` varchar(1024) DEFAULT NULL,
  `tax_code` varchar(10) NOT NULL,
  `active` varchar(2) NOT NULL,
  `check_box` varchar(2) NOT NULL,
  `vendor_basic_charges` decimal(10,2) NOT NULL,
  `vendor_tax_basic_charges` decimal(10,2) NOT NULL,
  `vendor_total` decimal(10,2) NOT NULL,
  `around_basic_charges` decimal(10,2) NOT NULL,
  `around_tax_basic_charges` decimal(10,2) NOT NULL,
  `around_total` decimal(10,2) NOT NULL,
  `customer_total` decimal(10,2) NOT NULL,
  `partner_payable_basic` decimal(10,2) NOT NULL,
  `partner_payable_tax` decimal(10,2) NOT NULL,
  `partner_net_payable` decimal(10,2) NOT NULL,
  `customer_net_payable` decimal(10,2) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



ALTER TABLE `partners` ADD `company_address` VARCHAR(1024) NOT NULL AFTER `public_name`;
ALTER TABLE `partners` ADD `partner_code` INT NOT NULL COMMENT 'This is the Partner ID which is used in Booking Sources table.' AFTER `company_address`;
ALTER TABLE  `service_center_booking_action` ADD  `unit_details_id` INT( 25 ) NULL DEFAULT NULL AFTER  `booking_id` ;
ALTER TABLE  `booking_unit_details` ADD  `purchase_month` VARCHAR( 25 ) NOT NULL AFTER  `appliance_tag` ;


--
-- Table structure for table `tax_rates`
--

CREATE TABLE IF NOT EXISTS `tax_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_code` varchar(50) NOT NULL COMMENT 'ST (service tax), VAT, CST etc',
  `state` varchar(1024) NOT NULL,
  `product_type` varchar(1024) DEFAULT NULL COMMENT 'product category',
  `rate` decimal(4,2) NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `active` varchar(1) NOT NULL DEFAULT '1',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `partner_callback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(25) NOT NULL,
  `callback_string` varchar(1024) NOT NULL,
  `active` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


--

ALTER TABLE  `booking_unit_details` CHANGE  `appliance_brand`  `appliance_brand` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `booking_unit_details` CHANGE  `appliance_category`  `appliance_category` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `booking_unit_details` CHANGE  `appliance_capacity`  `appliance_capacity` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `booking_unit_details` CHANGE  `model_number`  `model_number` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `booking_unit_details` CHANGE  `serial_number`  `serial_number` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `booking_unit_details` CHANGE  `price_tags`  `price_tags` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `booking_unit_details` CHANGE  `appliance_tag`  `appliance_tag` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `booking_unit_details` CHANGE  `booking_picture_file`  `booking_picture_file` VARCHAR( 1024 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;


