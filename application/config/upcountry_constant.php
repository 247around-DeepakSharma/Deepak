<?php
//Define Upcountry threshold
define('UPCOUNTRY_DISTANCE_THRESHOLD',500);
//Define Upcountry distance Must be grater than 50(UP and DOWN both)
define('UPCOUNTRY_MIN_DISTANCE',50);
//Define NOT UPCOUNTRY BOOKING
define('NOT_UPCOUNTRY_BOOKING','NOT UPCOUNTRY BOOKING');
//Define SF NOT EXIST IN Pincode Table
define('SF_DOES_NOT_EXIST','SF DOES NOT EXIST IN VENDOR PINCODE MAPPING FILE');
//Define UPCOUNTRY BOOKING
define('UPCOUNTRY_BOOKING','UPCOUNTRY BOOKING');
//Define UPCOUNTRY Disatnce Limit Exceed
define('UPCOUNTRY_LIMIT_EXCEED','UPCOUNTRY LIMIT EXCEED');
//Define UPCOUNTRY BOOKING
define('UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE','Upcountry Distance Can Not Be Found');
//Define  UPCOUNTRY_BOOKING_NEED_TO_APPROVAL
define('UPCOUNTRY_BOOKING_NEED_TO_APPROVAL','UPCOUNTRY BOOKING NEED TO APPROVAL');
//Upcountry Cap For Partner
define ('UPCOUNTRY_DISTANCE_CAP', '200');

define("UPCOUNTRY_CHARGES_APPROVED", "Upcountry Charges Approved");

define("UPCOUNTRY_CHARGES_NOT_APPROVED", "Upcountry Charges Not Approved");

define("NOT_UPCOUNTRY_PRICE_TAG", "-1");

define("PARTNER_PROVIDE_UPCOUNTRY_PRICE_TAG", "1");


//Upcountry booking, charges paid by Customer
define("CUSTOMER_PAID_UPCOUNTRY", "Upcountry Booking, Charges To Be Paid By Customer");
//Upcountry booking, charges paid by Partner
define("PARTNER_PAID_UPCOUNTRY", "Upcountry Booking, Free For Customer");

//Reasons which cause a booking to be a non-upcountry booking
//
//If there are more than 1 SF and all of them are non-upcountry
define("MULTIPLE_NON_UPCOUNTRY_VENDOR", "Multiple Non-Upcountry SFs");
//Non-upcountry SF
define("NON_UPCOUNTRY_VENDOR", "SF Not Configured As Upcountry");
//No sub-office for this SF
define("CUSTOMER_DISTRICT_NOT_EXIST_IN_SUB_OFFICE", "Suboffice Not Added for Customer District");
//Pincode missing from India Pincode
define("CUSTOMER_PINCODE_NOT_EXIST_IN_INDIA_PINCODE", "Pincode Not Found In India Pincode List");
//Customer and SF sub-office have same pincode
define("CUSTOMER_AND_SUB_OFFICE_HAS_SAME_PINCODE", "Customer and Sub office Has Same Pincode");
//Upcountry distance is lower than municipal limit
define("DISTANCE_WITHIN_MUNICIPAL_LIMIT", "Distance Within Municipal Limit");
//Customer and partner both do not pay upcountry charges for this request
define("CUSTOMER_AND_PARTNER_BOTH_NOT_PROVIDE_UPCOUNTRY_FOR_THIS_PRICE_TAG", "Upcountry Charges Not Paid by Partner AND Customer");

