<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('INT_STATUS_CUSTOMER_NOT_REACHABLE', 'Customer Not Reachable');
define('DEFAULT_SEARCH_PAGE', 'employee/user');
define('short_url_key', 'AIzaSyBPTsxWtCYUBfq_GqcRisN-MsWU8dT2HeI');
define('short_api_url', 'https://www.googleapis.com/urlshortener/v1/url');

define('basic_percentage', 0.7);
define('addtitional_percentage', .85);
define('parts_percentage', .95);
define('SERVICE_TAX_RATE', 0.15);
define('DEFAULT_TAX_RATE', 15);

//Agent ID used when Partner inserts a Booking by calling our API
define('DEFAULT_PARTNER_AGENT', 978978);

define('_247AROUND',247001);
define('_247AROUND2',247002);
define('_247AROUND3',247003);
define('_247AROUND99',247999);

//constant to define Pending state
define('_247AROUND_PENDING','Pending');
//constant to define New Booking state
define('_247AROUND_NEW_BOOKING','New_Booking');
//constant to define Follow up state
define('_247AROUND_FOLLOWUP','FollowUp');
//constant to define New Query state
define('_247AROUND_NEW_QUERY','New_Query');
//constant to define Cancelled state
define('_247AROUND_CANCELLED','Cancelled');
//constant to define Rescheduled state
define('_247AROUND_RESCHEDULED','Rescheduled');
//constant to define Completed state
define('_247AROUND_COMPLETED','Completed');
//Only Pincode available Queries
define('PINCODE_AVAILABLE','p_av');
//Only Pincode not available Queies
define('PINCODE_NOT_AVAILABLE','p_nav');
//Pincode not available OR Pincode Avaliable Queies
define('PINCODE_ALL_AVAILABLE','p_all');
// Vendor NOT Assign
define('SC_NOT_ASSIGN','SC_not_assign');
// Assigned Vendor
define('ASSIGNED_VENDOR','Assigned_vendor');
//Re-Assigned Vendor
define('RE_ASSIGNED_VENDOR','Re-Assigned_vendor');
//Engineer Assigned
define('ENGG_ASSIGNED', "Engg_Assigned");
//Re-Assigned Engineer
define('RE_ASSIGNED_ENGINEER', "Re-Assigned_Engineer");
define('UPDATED_SC', "upadted_sc");
//Engineer Not Assign
define('ENGG_NOT_ASSIGN', 'Engineer not assigned');
//Engineer Not Assign on Time
define('ENGG_LATE_ASSIGN', 'Engineer Late assigned');
// Booking not updated by SF
define('BOOKING_NOT_UPDATED_BY_SERVICE_CENTER', 'Booking is not updated by service center');
//Spare Parts Received By SF
define('SPARE_PARTS_DELIVERED', 'Spare Parts Delivered to SF');
//Spare Parts Shipped By SF
define('SPARE_PARTS_SHIPPED', 'Spare Parts Shipped by Partner');
// Spare Reschedule 
define('SPARE_RESCHEDULED', 'Spare_Rescheduled');

//constant to define Login 
define('_247AROUND_LOGIN',1);
//constant to define Completed state
define('_247AROUND_LOGOUT',0);
//constant to define 247around access for login panel
define('_247AROUND_ACCESS','247Access');
//constant to define 247around state for brackets requested 
define('_247AROUND_BRACKETS_REQUESTED','Brackets_Requested');
//constant to define 247around state for brackets shipped 
define('_247AROUND_BRACKETS_SHIPPED','Brackets_Shipped');
//constant to define 247around state for brackets requested 
define('_247AROUND_BRACKETS_RECEIVED','Brackets_Received');
//constant to define 247around state for brackets pending 
define('_247AROUND_BRACKETS_PENDING','Brackets_Pending');
//constant to define 247around brackets charge for 19-24 inch
define('_247AROUND_BRACKETS_19_24_UNIT_PRICE',240);
//constant to define 247around brackets charge for 26-32 inch
define('_247AROUND_BRACKETS_26_32_UNIT_PRICE',350);
//constant to define 247around brackets charge for 36-42 inch
define('_247AROUND_BRACKETS_36_42_UNIT_PRICE',485);
//constant to define State for making Vendor Activate
define('_247AROUND_VENDOR_ACTIVATED','Vendor_Activated');
//constant to define State for making Vendor De-activate
define('_247AROUND_VENDOR_DEACTIVATED','Vendor_Deactivated');
//constant to define State for making Vendor DELETED
define('_247AROUND_VENDOR_DELETED','Vendor_Deleted');
//constant to define State for making Partner Activate
define('_247AROUND_PARTNER_ACTIVATED','Partner_Activated');
//constant to define State for making Partner De-activate
define('_247AROUND_PARTNER_DEACTIVATED','Partner_Deactivated');
//constant to define State for making Partner Suspended
define('_247AROUND_VENDOR_SUSPENDED','Vendor Suspended');
//constant to define State for making Partner Non Suspended
define('_247AROUND_VENDOR_NON_SUSPENDED','Vendor Non_Suspended');

define("INSERT_NEW_BOOKING", "INSERT_NEW_BOOKING");


define('SPARE_PARTS_REQUIRED', 'Spare Parts Required');

define('SPARE_PARTS_REQUESTED', 'Spare Parts Requested');
//
define('Max_TIME_TO_BE_ASSIGNED_ENGINEER', 12);

define('Max_TIME_WITH_IN_ASSIGNED_ENGINEER', 3);

define('PRODUCT_NOT_DELIVERED_TO_CUSTOMER','Product not delivered to customer');

define('CUSTOMER_ASK_TO_RESCHEDULE','Customer asked to reschedule');

define('CUSTOMER_NOT_REACHABLE','Customer not reachable');
//constant to define default brackets order id
define('_247_AROUND_DEFAULT_BRACKETS_ORDER_ID',201611000001);





/* End of file constants.php */
/* Location: ./application/config/constants.php */