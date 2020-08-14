UPDATE query_report SET query1 = 'SELECT count(DISTINCT(booking_id)) as count FROM engineer_booking_action WHERE closed_date IS NOT NULL AND  closed_date >= DATE_FORMAT(CURRENT_DATE - INTERVAL 0 MONTH, "%Y/%m/01") AND internal_status = "Completed";' WHERE query_report.id = 59;
UPDATE query_report SET query2 = 'SELECT count(DISTINCT(booking_id)) as count FROM engineer_booking_action WHERE closed_date IS NOT NULL AND  closed_date >= DATE_FORMAT(CURRENT_DATE - INTERVAL 0 MONTH, "%Y/%m/01") AND internal_status = "Cancelled";' WHERE query_report.id = 59;
UPDATE query_report SET query1 = 'SELECT count(DISTINCT(booking_id)) as count FROM engineer_booking_action WHERE closed_date IS NOT NULL AND  closed_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, "%Y/%m/01" ) AND closed_date < DATE_FORMAT( CURRENT_DATE, "%Y/%m/01" ) AND internal_status = "Completed";' WHERE query_report.id = 60;
UPDATE query_report SET query2 = 'SELECT count(DISTINCT(booking_id)) as count FROM engineer_booking_action WHERE closed_date IS NOT NULL AND  closed_date >= DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, "%Y/%m/01" ) AND closed_date < DATE_FORMAT( CURRENT_DATE, "%Y/%m/01" ) AND internal_status = "Cancelled";' WHERE query_report.id = 60;



