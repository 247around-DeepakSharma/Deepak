<?php
	$invoice_no=3123;
	$date="05-Apr-2016";
	$invoice_period_from=" 01-02-2016";
	$invoice_period_to="01-03-2016";
	$from="asdjskhsdkfhsdkjfsdfh";
	$bookings="34";
	$from_address="sfsfsfddfsff";
	$logo247around="add_logo";
	$to_address="sdsds";
	$total_amount_collected=0;
	$total_service_charge=0;
	$total_additional_service_charge=0;
	$total_parts_cost=0;
	$total_rating=0;
	$i=0;
	$service_charge_royality=30;
	$additional_service_charge_royality=15;
	$parts_cost_royality=5;
	$beneficiary_name="adsa";
	$beneficiary_account_no=6898;
	$beneficiary_bank_name="SBI";
	$ifsc_code=354354;
	$record= array(array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4),
			array('booking_id' => 'SS-21101602111','service'=>'Washing Machine','booking_date'=>'11-02-2016','closed_date'=>'2016-02-11 04:32:50','service_charge'=>343.5, 'additional_service_charge'=>0,'parts_cost'=>0,'rating'=>4));

?>