ALTER TABLE `booking_details` ADD `dependency_on` INT NOT NULL DEFAULT '0' COMMENT '0 means dependency on Customer 1 means dependency on Around' AFTER `service_promise_date`;