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
define('UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE','UPCOUNTRY DISTANCE CAN NOT CALCULATE');
//Define  UPCOUNTRY_BOOKING_NEED_TO_APPROVAL
define('UPCOUNTRY_BOOKING_NEED_TO_APPROVAL','UPCOUNTRY BOOKING NEED TO APPROVAL');
//Upcountry Cap For Partner
define ('UPCOUNTRY_DISTANCE_CAP', '200');

define("MULTIPLE_NON_UPCOUNTRY_VENDOR", "Multiple Non Upcountry Vendor");

define("NON_UPCOUNTRY_VENDOR", "Non Upcountry Vendor");

define("CUSTOMER_DISTRICT_NOT_EXIST_IN_SUB_OFFICE", "Vendor does not work as upcountry");

define("CUSTOMER_PINCODE_NOT_EXIST_IN_INDIA_PINCODE", "Customer pincode does not exist in India Pincode");

define("CUSTOMER_AND_SUB_OFFICE_HAS_SAME_PINCODE", "Customer and Sub office has same pincode");

define("DISTANCE_MINIMUM_FROM_MUNICIPAL_LIMIT", "DISTANCE is minimum from municipal limit");

define("CUSTOMER_PAID_UPCOUNTRY", "Customer will pay upcountry charges");

define("CUSTOMER_AND_PARTNER_BOTH_NOT_PROVIDE_UPCOUNTRY_FOR_THIS_PRICE_TAG", "Customer and partner both do not pay upcountry charges for this request");

define("PARTNER_WILL_PAY_UPCOUNTRY", "PARTNER_WILL_PAY_UPCOUNTRY");

define("UPCOUNTRY_LOGIC", "First of all we will check how many sf work on this pin code(customer pincode ). 

    <br/><br/>If at least one sf available here who does not work as a upcountry then we will not consider booking as a upcountry. 

    <br/><br/>If here is more than one sf available who does not work as upcountry then we are not assign booking to sf. 

    <br/><br/>If here is more than one sf and all sf work as a upcountry then we will assign those sf whose distance is lowest from sf sub office pincode. 

    <br/><br/>If customer district is not exist in the sub office district list of available sf then we will not mark booking as a upcountry. 

    <br/><br/>For the upcountry,  Customer District should be available in the sub office District List of available sf. 

    <br/><br/>If customer district is exist in the sub office list of available sf then we will get distance between customer pincode and its sub office district pincode from google map. 

    <br/><br/>This distance is one way.  To calculate upcountry distance, we will minus from sf municipal limit then multiply by 2. ");

