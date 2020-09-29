UPDATE 
    engineer_table_sign
    JOIN engineer_booking_action ON (engineer_table_sign.booking_id = engineer_booking_action.booking_id)
SET 
    engineer_table_sign.amount_paid = engineer_booking_action.amount_paid
WHERE 
    (engineer_table_sign.amount_paid = "" OR engineer_table_sign.amount_paid IS NULL);
