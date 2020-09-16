ALTER TABLE penalty_details ADD COLUMN `penalty_point` int(11) NOT NULL DEFAULT 1;
ALTER TABLE penalty_details ADD COLUMN `reason_of` int(11) NOT NULL comment '1 => Completion, 2 => Cancellation';
ALTER TABLE penalty_details ADD COLUMN `agent_id` int(11) NOT NULL DEFAULT 1;
ALTER TABLE penalty_details ADD COLUMN `create_date` timestamp NOT NULL DEFAULT current_timestamp();
ALTER TABLE penalty_details ADD COLUMN `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp();
ALTER TABLE penalty_details ADD CONSTRAINT `fk_penalty_details_employee` FOREIGN KEY (`agent_id`) REFERENCES `employee` (`id`);

INSERT INTO `penalty_details` (`criteria`, `penalty_point`, `reason_of`, `agent_id`) VALUES ('Wrong Amount Entered', '1', '1', '1');
INSERT INTO `penalty_details` (`criteria`, `penalty_point`, `reason_of`, `agent_id`) VALUES ('Wrong Serial Number/Serial Number Image', '1', '1', '1');
INSERT INTO `penalty_details` (`criteria`, `penalty_point`, `reason_of`, `agent_id`) VALUES ('Wrong Invoice/DOP', '1', '1', '1');
INSERT INTO `penalty_details` (`criteria`, `penalty_point`, `reason_of`, `agent_id`) VALUES ('Fake Completion', '1', '1', '1');
INSERT INTO `penalty_details` (`criteria`, `penalty_point`, `reason_of`, `agent_id`) VALUES ('Customer is Waiting for Service', '1', '2', '1');
INSERT INTO `penalty_details` (`criteria`, `penalty_point`, `reason_of`, `agent_id`) VALUES ('Customer has Paid Charges', '1', '2', '1');

CREATE TABLE `cancellation_rejection_penalty_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `rejection_reason` int(11) NOT NULL,
  `cancellation_reason` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_rejection_reason_penalty_details` (`rejection_reason`),
  KEY `fk_cancellation_reason_booking_cancellation_reasons` (`cancellation_reason`),  
  CONSTRAINT `fk_rejection_reason_penalty_details` FOREIGN KEY (`rejection_reason`) REFERENCES `penalty_details` (`id`),
  CONSTRAINT `fk_cancellation_reason_booking_cancellation_reasons` FOREIGN KEY (`cancellation_reason`) REFERENCES `booking_cancellation_reasons` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE penalty_on_booking ADD COLUMN `penalty_point` int(11) NOT NULL;