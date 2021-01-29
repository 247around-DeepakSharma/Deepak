-- Function To calculate Penalty Percentage --
-- **************************************** --
DELIMITER $$
CREATE FUNCTION `sfPenaltyStatus`(sf_id INT(11), review_status VARCHAR(20), penalty_period INT(11)) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
DECLARE penalty_status DECIMAL(10,2);
DECLARE  total_bookings INT(11);
DECLARE total_penalty_imposed INT(11);
SET total_bookings = 0;
SET total_penalty_imposed = 0;

IF review_status = 'Completed' THEN

select count(*) INTO total_bookings from booking_details where assigned_vendor_id = sf_id AND DATEDIFF(CURDATE(), service_center_closed_date) <= penalty_period AND ((internal_status = 'InProcess_Completed' AND service_center_closed_date IS NOT NULL) OR  current_status = 'Completed');

select SUM(penalty_on_booking.penalty_point) INTO total_penalty_imposed from penalty_on_booking JOIN penalty_details ON (penalty_on_booking.criteria_id = penalty_details.id) WHERE penalty_on_booking.penalty_point <> 0 AND penalty_details.reason_of = 1 AND DATEDIFF(CURDATE(), penalty_on_booking.create_date) <= penalty_period AND service_center_id = sf_id;

ELSE

select count(*) INTO total_bookings from booking_details where assigned_vendor_id = sf_id AND DATEDIFF(CURDATE(), service_center_closed_date) <= penalty_period AND ((internal_status = 'InProcess_Cancelled' AND service_center_closed_date IS NOT NULL) OR  current_status = 'Cancelled');

select SUM(penalty_on_booking.penalty_point) INTO total_penalty_imposed from penalty_on_booking JOIN penalty_details ON (penalty_on_booking.criteria_id = penalty_details.id) WHERE penalty_on_booking.penalty_point <> 0 AND penalty_details.reason_of = 2  AND DATEDIFF(CURDATE(), penalty_on_booking.create_date) <= penalty_period AND service_center_id = sf_id;

END IF;

SET penalty_status = (total_penalty_imposed / (total_penalty_imposed + total_bookings)) * 100;

-- return the penalty status
RETURN (penalty_status);
END$$
DELIMITER ;

-- Function To calculate OW booking Completion percentage ----
-- ******************************************************** --
DELIMITER $$

CREATE FUNCTION sfOWCompletedBookingPercentage(sf_id INT(11), time_period INT(11))
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE total_ow_completed INT(11);
    DECLARE total_completed INT(11);
    declare ow_completed_percentage DECIMAL(10,2);
    SET total_ow_completed = 0;
    SET total_completed = 0;
	
	select count(*) INTO total_ow_completed from booking_details where assigned_vendor_id = sf_id AND DATEDIFF(CURDATE(), service_center_closed_date) <= time_period AND current_status = 'Completed' AND amount_paid > 0;
	
	select count(*) INTO total_completed from booking_details where assigned_vendor_id = sf_id AND DATEDIFF(CURDATE(), service_center_closed_date) <= time_period AND current_status = 'Completed';
	
	SET ow_completed_percentage = (total_ow_completed / total_completed)*100;
	
	-- return the OW completed percentage
	RETURN (ow_completed_percentage);
END$$
DELIMITER ;

-- Function To calculate SF Booking Cancellation Percentage --
-- ******************************************************** --

DELIMITER $$

CREATE FUNCTION sfCancelledBookingPercentage(sf_id INT(11), time_period INT(11))
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE total_cancelled INT(11);
    DECLARE total_bookings INT(11);
    declare cancelled_percentage DECIMAL(10,2);
    SET total_cancelled = 0;
    SET total_bookings = 0;
	
	select count(*) INTO total_cancelled from booking_details where assigned_vendor_id = sf_id AND DATEDIFF(CURDATE(), service_center_closed_date) <= time_period AND current_status = 'Cancelled';
	
	select count(*) INTO total_bookings from booking_details where assigned_vendor_id = sf_id AND DATEDIFF(CURDATE(), service_center_closed_date) <= time_period AND current_status IN ('Completed', 'Cancelled');
	
	SET cancelled_percentage = (total_cancelled / total_bookings)*100;
	
	-- return the cancelled percentage
	RETURN (cancelled_percentage);
END$$
DELIMITER ;