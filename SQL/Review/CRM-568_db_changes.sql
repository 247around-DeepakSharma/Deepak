CREATE TABLE `account_holders_bank_details_trigger` (
  `id` int(11) NOT NULL,
  `entity_id` varchar(20) NOT NULL,
  `entity_type` varchar(20) NOT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_type` varchar(20) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `cancelled_cheque_file` text,
  `beneficiary_name` varchar(50) DEFAULT NULL,
  `is_verified` int(10) NOT NULL DEFAULT '0',
  `agent_id` int(10) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `dateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

CREATE TRIGGER `account_details_trigger` BEFORE UPDATE ON `account_holders_bank_details`
 FOR EACH ROW BEGIN 
INSERT INTO account_holders_bank_details_trigger (SELECT * FROM account_holders_bank_details WHERE id=NEW.id);
END