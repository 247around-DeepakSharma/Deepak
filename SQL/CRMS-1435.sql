-- ALTER TABLE engineer_table_sign add column `cancellation_reason` int(11) DEFAULT NULL;
ALTER TABLE engineer_table_sign CHANGE COLUMN cancellation_reason cancellation_reason int(11) DEFAULT NULL;
ALTER TABLE engineer_table_sign add column `cancellation_remark` varchar(255) NOT NULL;

UPDATE 
    engineer_table_sign
        JOIN
    engineer_booking_action ON (engineer_table_sign.booking_id = engineer_booking_action.booking_id)
        JOIN
    booking_cancellation_reasons ON (engineer_booking_action.cancellation_reason = booking_cancellation_reasons.reason
        AND booking_cancellation_reasons.reason_of = 'vendor')
set
	engineer_table_sign.cancellation_reason = booking_cancellation_reasons.id,
    engineer_table_sign.cancellation_remark = engineer_booking_action.cancellation_remark
WHERE
    engineer_booking_action.cancellation_reason IS NOT NULL
        AND engineer_booking_action.cancellation_reason <> '';

