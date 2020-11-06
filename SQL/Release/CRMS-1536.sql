ALTER TABLE `vendor_partner_invoices` ADD `tcs_rate` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `call_center_charges`, ADD `tcs_amount` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `tcs_rate`;

ALTER TABLE trigger_vendor_partner_invoices ADD `tcs_rate` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `call_center_charges`, ADD `tcs_amount` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `tcs_rate`;
