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

define('INT_STATUS_CUSTOMER_NOT_REACHABLE', 'Customer not reachable / Customer not picked phone');
define('DEFAULT_SEARCH_PAGE', 'employee/user');
define('short_url_key', 'AIzaSyBPTsxWtCYUBfq_GqcRisN-MsWU8dT2HeI');
define('short_api_url', 'https://www.googleapis.com/urlshortener/v1/url');

define('basic_percentage', 0.7);
define('addtitional_percentage', .85);
define('parts_percentage', .95);
define('PART_DELIVERY_PERCENTAGE', .10);
define('SERVICE_TAX_RATE', 0.18);
define('DEFAULT_TAX_RATE', 18);
define('DEFAULT_PARTS_TAX_RATE', 28);

//Agent ID used when Partner inserts a Booking by calling our API
define('DEFAULT_PARTNER_AGENT', 978978);

define('_247AROUND',247001);
//define('_247AROUND2',247002);
//define('_247AROUND3',247003);
//define('_247AROUND99',247999);
define('_247AROUND_DEFAULT_AGENT',1);
define('_247AROUND_DEFAULT_AGENT_NAME',"247Around");

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
//constant to define Completed Bookings with Ratings
define('_247AROUND_RATING_COMPLETED','Completed With Rating');
//constant to define Customer Not Available
define('_247AROUND__Customer_Not_Available','Customer_Not_Available');
//constant to define SCHEDULED
define('_247AROUND__SCHEDULED','SCHEDULED');
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
//define('_247AROUND_BRACKETS_19_24_UNIT_PRICE',120);
//constant to define 247around brackets charge for 26-32 inch
define('_247AROUND_BRACKETS_26_32_UNIT_PRICE',100);
//constant to define 247around brackets charge for 36-42 inch
define('_247AROUND_BRACKETS_36_42_UNIT_PRICE',165);
//constant to define 247around brackets charge for greater than 43 inch
//define('_247AROUND_BRACKETS_43_UNIT_PRICE',425);
//constant to define State for making Vendor Activate
define('_247AROUND_VENDOR_ACTIVATED','Vendor Activated');
//constant to define State for making Vendor De-activate
define('_247AROUND_VENDOR_DEACTIVATED','Vendor Deactivated');
//constant to define State for making Vendor DELETED
define('_247AROUND_VENDOR_DELETED','Vendor_Deleted');
//constant to define State for making Partner Activate
define('_247AROUND_PARTNER_ACTIVATED','Partner_Activated');
//constant to define State for making Partner De-activate
define('_247AROUND_PARTNER_DEACTIVATED','Partner_Deactivated');
//constant to define State for making Partner Suspended
define('_247AROUND_VENDOR_SUSPENDED','Vendor Temporary OFF');
//constant to define State for making Partner Non Suspended
define('_247AROUND_VENDOR_NON_SUSPENDED','Vendor Temporary ON');

define("INSERT_NEW_BOOKING", "INSERT_NEW_BOOKING");

//Constant to define groups
define('_247AROUND_ADMIN','admin');
define('_247AROUND_CALLCENTER','callcenter');
define('_247AROUND_CLOSURE','closure');
define('_247AROUND_RM','regionalmanager');
define('_247AROUND_DEVELOPER','developer');

define('SPARE_PARTS_REQUIRED', 'Spare Parts Required');

define('SPARE_PARTS_REQUESTED', 'Spare Parts Requested');
//
define('Max_TIME_TO_BE_ASSIGNED_ENGINEER', 12);

define('Max_TIME_WITH_IN_ASSIGNED_ENGINEER', 3);

define('PRODUCT_NOT_DELIVERED_TO_CUSTOMER','Product not delivered to customer');

define('CUSTOMER_ASK_TO_RESCHEDULE','Customer asked to reschedule');

define('CUSTOMER_NOT_REACHABLE','Customer not reachable / Customer not picked phone');
//constant to define default brackets order id
define('_247_AROUND_DEFAULT_BRACKETS_ORDER_ID',201611000001);
// Defective Parts pending
define('DEFECTIVE_PARTS_PENDING','Defective Part Pending');
// Defective Parts Shipped by SF
define('DEFECTIVE_PARTS_SHIPPED','Defective Part Shipped By SF');
// Defective Parts Received by SF
define('DEFECTIVE_PARTS_RECEIVED','Defective Part Received By Partner');
// Defective Parts Received by SF
define('DEFECTIVE_PARTS_REJECTED','Defective Part Rejected By Partner');
//Snapdeal ID
define('SNAPDEAL_ID',1);
//Jeeves ID
define("JEEEVES_ID", 247030);
//Wybor ID
define("WYBOR_ID", 247010);
//Define Developer Email id
define('DEVELOPER_EMAIL','anuj@247around.com, abhaya@247around.com, sachinj@247around.com, chhavid@247around.com');
//Define Email ID to send system health emails
define('SYS_HEALTH_NAME','247around Health Monitor');
define('SYS_HEALTH_EMAIL','health@247around.com');
//Define New CRM Contstant for SF
define('NEW_SF_CRM',"New SF CRM");
//Define Ols CRM Contstant for SF
define('OLD_SF_CRM',"Old SF CRM");
//Define Upcountry threshold
define('UPCOUNTRY_DISTANCE_THRESHOLD',500);
//Define New Partner Lead
define('_247AROUND_NEW_PARTNER_LEAD','New_Lead');
//Define Constant for Snapdeal Delivered Excel File Type
define('_247AROUND_SNAPDEAL_DELIVERED','Snapdeal-Delivered');
//Define Constant for Snapdeal Shipped Excel File Type
define('_247AROUND_SNAPDEAL_SHIPPED','Snapdeal-Shipped');
//Define Constant for Paytm Shipped Excel File Type
define('_247AROUND_PAYTM_DELIVERED','Paytm-Delivered');
//Define Constant for Satya File 
define('_247AROUND_SATYA_DELIVERED','Satya-Delivered');
//Define Constant for Vendor Pincode Excel File Type
define('_247AROUND_VENDOR_PINCODE','Vendor-Pincode');
//Define Constant for Vendor Pincode Excel File Type
define('_247AROUND_SF_PRICE_LIST','SF-Price-List');
//Define Constant for Partner Appliance Details
define('_247AROUND_PARTNER_APPLIANCE_DETAILS','Partner-Appliance-Details');
//Define Upcountry distance Must be grater than 50(UP and DOWN both)
define('UPCOUNTRY_MIN_DISTANCE',50);
define('OEM', "OEM");
//Define New SF Creation
define('NEW_SF_ADDED','New SF Added');
//Define Edit SF State
define('SF_UPDATED','SF Updated');
//Define New Partner Creation
define('NEW_PARTNER_ADDED','New Partner Added');
//Define Edit SF State
define('PARTNER_UPDATED','Partner Updated');
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
define('IS_DEFAULT_ENGINEER', TRUE);
define('DEFAULT_ENGINEER', 24700001);
define("UPCOUNTRY_CHARGES_APPROVED", "Upcountry Charges Approved");
define("UPCOUNTRY_CHARGES_NOT_APPROVED", "Upcountry Charges Not Approved");

//Customer will pay upcountry charges as per the below rate
define("DEFAULT_UPCOUNTRY_RATE", 3);
//Define Incentive Cut
define('INCENTIVE_CUT',12);
//Define Penalty - Fake Complete customer want installation
define('PENALTY_FAKE_COMPLETE_CUSTOMER_WANT_INSTALLATION',14);
//Define Penalty - Fake Complete customer not want installation
define('PENALTY_FAKE_COMPLETE_CUSTOMER_NOT_WANT_INSTALLATION',15);
//Define Escalation Panalty id
define('ESCALATION_PENALTY',1);
define('PENALTY_FAKE_CANCEL',13);
//Define error code for order id not found for partner_sd_sb.php file
define('ERR_ORDER_ID_NOT_FOUND_CODE', -1007);
//Define error code for order id not found for partner_sd_sb.php file
define('ERR_ORDER_ID_NOT_FOUND_MSG', 'Order ID Does Not Exist');
//Define snapdeal new missed call number
define('PARTNERS_MISSED_CALLED_NUMBER','01130017601');
//Define snapdeal new missed call number
define('SNAPDEAL_MISSED_CALLED_NUMBER','01139595247');
//Define Android App new missed call number
define('ANDROID_APP_MISSED_CALLED_NUMBER','01139585684');

//Define new missed call number for AC installation
define('AC_SERVICE_MISSED_CALLED_NUMBER','01139595450');

define('HOME_THEATER_REPAIR_SERVICE_TAG', 'Repair - In Warranty (Service Center Visit)');
define('HOME_THEATER_REPAIR_SERVICE_TAG_OUT_OF_WARRANTY', 'Repair - Out Of Warranty (Service Center Visit)');

define("CAP_ON_PENALTY_AMOUNT", "100");
define("PAYTM", "3");
define("PENALTY_ON_COMPLETED_BOOKING", TRUE);
define("PENALTY_ON_CANCELLED_BOOKING", TRUE);

define ('SF_UNAVAILABLE_SMS_NOT_SENT', 'SMS Not Sent To Customer For Installation');
//Upcountry Cap For Partner
define ('UPCOUNTRY_DISTANCE_CAP', '200');
// Booking is not updated by service center
define("BOOKING_IS_NOT_UPDATED_BY_SERVICE_CENTER_ID", 2);
// Incentive Cut - Reschedule without reason
define("INCENTIVE_CUT_RESCHEDULED_WITHOUT_REASON_ID", 8);
// Booking Rescheduled But Customer Not Informed
define("BOOKING_RESCHEDULED_WITHOUT_REASON_ID", 12);
//Penalty - Fake Complete - Customer Want Installati...
define("PENALTY_FAKE_COMPLETED_CUSTOMER_WANT_INSTALLATION_ID", 10);
//Penalty - Fake Complete - Customer DOES NOT Want Installation
define("PENALTY_FAKE_COMPLETED_CUSTOMER_DOES_NOT_WANT", 11);
//Engineer has not contacted with customer.
define("ENGINEER_HAS_NOT_CONTACTED_WITH_CUSTOMER", 3);


define('SUCCESS_CODE', 247);
define('SUCCESS_MSG', 'Success');
define('ERR_BOOKING_NOT_INSERTED', -24700);
define('ERR_GENERIC_ERROR_CODE', -1000);
define('ERR_INVALID_AUTH_TOKEN_CODE', -1001);
define('ERR_MOBILE_NUM_MISSING_CODE', -1002);
define('ERR_ORDER_ID_EXISTS_CODE', -1003);
define('ERR_MANDATORY_PARAMETER_MISSING_CODE', -1004);
define('ERR_INVALID_PRODUCT_CODE', -1005);
define('ERR_INVALID_REQUEST_TYPE_CODE', -1006);
//define('ERR_ORDER_ID_NOT_FOUND_CODE', -1007);
define('ERR_INVALID_BOOKING_ID_CODE', -1008);
define('ERR_REQUEST_ALREADY_COMPLETED_CODE', -1009);
define('ERR_REQUEST_ALREADY_CANCELLED_CODE', -1010);
define('ERR_REQUEST_BEYOND_CUTOFF_TIME_CODE', -1011);
define('ERR_INVALID_DATE_FORMAT_CODE', -1012);
define('ERR_INVALID_TIMESLOT_FORMAT_CODE', -1013);
define('ERR_INVALID_INSTALLATION_TIMESLOT_CODE', -1014);
define('ERR_INVALID_PARTNER_NAME_CODE', -1015);
define('ERR_INVALID_JSON_INPUT_CODE', -1016);
define('ERR_INVALID_PRODUCT_TYPE_CODE', -1017);

define('ERR_BOOKING_NOT_INSERTED_MSG', 'Booking Insertion Failed');
define('ERR_GENERIC_ERROR_MSG', 'Unknown Error');
define('ERR_INVALID_AUTH_TOKEN_MSG', 'Invalid Auth Token');
define('ERR_MOBILE_NUM_MISSING_MSG', 'Mobile Number Missing');
define('ERR_ORDER_ID_EXISTS_MSG', 'Order ID Exists');
define('ERR_MANDATORY_PARAMETER_MISSING_MSG', 'Mandatory Parameter is Missing');
define('ERR_INVALID_PRODUCT_MSG', 'Invalid Product');
define('ERR_INVALID_REQUEST_TYPE_MSG', 'Invalid Request Type');
//define('ERR_ORDER_ID_NOT_FOUND_MSG', 'Order ID Not Found');
define('ERR_INVALID_BOOKING_ID_MSG', 'Invalid Booking ID');
define('ERR_REQUEST_ALREADY_COMPLETED_MSG', 'Request is Already Completed');
define('ERR_REQUEST_ALREADY_CANCELLED_MSG', 'Request is Already Cancelled');
define('ERR_REQUEST_BEYOND_CUTOFF_TIME_MSG', 'Request Beyond Cutoff Time');
define('ERR_INVALID_DATE_FORMAT_MSG', 'Invalid Date Format');
define('ERR_INVALID_TIMESLOT_FORMAT_MSG', 'Invalid Timeslot Format');
define('ERR_INVALID_INSTALLATION_TIMESLOT_MSG', 'Invalid Installation Timeslot');
define('ERR_INVALID_PARTNER_NAME_MSG', 'Invalid Partner Name');
define('ERR_INVALID_JSON_INPUT_MSG', 'Invalid JSON Input');
define('ERR_INVALID_PRODUCT_TYPE_MSG', 'DENIED BY VENDOR');

//ICICI Bank name constant
define('ICICI_BANK_NAME', 'ICICI Bank Ltd');

//Default email id for sending internal emails
define('NOREPLY_EMAIL_ID', 'noreply@247around.com');

//ADIL EMIL ID
define("ADIL_EMAIL_ID", "adila@247around.com");

//rating new state
define('RATING_NEW_STATE','Completed_With_Rating');

//Regional Managers Email ID
define("RM_EMAIL", "nits@247around.com, suresh@247around.com, oza@247around.com, nilanjan@247around.com, arunk@247around.com");

//Email IDs when SF is not found in Vendor Pincode Mapping file
define("SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_TO", "adila@247around.com");
define("SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_CC", "anuj@247around.com");

//promotional sms constant
define('COMPLETED_PROMOTINAL_SMS_1','completed_promotional_sms_1');
define('COMPLETED_PROMOTINAL_SMS_2','completed_promotional_sms_2');
define('CANCELLED_PROMOTINAL_SMS_1','cancelled_promotional_sms_1');
define('CANCELLED_PROMOTINAL_SMS_2','cancelled_promotional_sms_2');
define('CANCELLED_QUERY_PROMOTINAL_SMS_1','cancelled_query_promotional_sms_1');
define('CANCELLED_QUERY_PROMOTINAL_SMS_2','cancelled_query_promotional_sms_2');
define('BOOKING_NOT_EXIST_PROMOTINAL_SMS_1','booking_not_exist_promotional_sms_1');
define('BOOKING_NOT_EXIST_PROMOTINAL_SMS_2','booking_not_exist_promotional_sms_2');

//invoice email tag
define('PARTNER_INVOICE_DETAILED_EMAIL_TAG','partner_invoice_detailed');
define('CASH_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG','cash_details_invoices_for_vendors');
define("BUYBACK_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG", "buyback_details_invoices_for_vendors");
define('FOC_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG','foc_details_invoices_for_vendors');
define('BRACKETS_INVOICE_EMAIL_TAG','send_brackets_invoice_mail');
define('DRAFT_BRACKETS_INVOICE_EMAIL_TAG','send_draft_brackets_invoice_mail');
define('CRM_SETUP_INVOICE_EMAIL_TAG','crm_setup_invoice');
define('BRACKETS_CREDIT_NOTE_INVOICE_EMAIL_TAG','brackets_credit_note_invoice');

//miss call rating sms
define('MISSED_CALL_RATING_SMS','missed_call_rating_sms');
define('GOOD_MISSED_CALL_RATING_NUMBER','01139588220');
define('POOR_MISSED_CALL_RATING_NUMBER','01139588224');
define('MISSED_CALL_DEFAULT_RATING', '5');

//Buyback constant
define('_247AROUND_BB_PRICE_LIST','BB-Price-List');
define('_247AROUND_BB_ORDER_LIST','BB-Order-List');
define('_247AROUND_BB_ORDER_ID_IMAGE_TAG','order_id_image');
define('_247AROUND_BB_DAMAGED_ORDER_IMAGE_TAG','damaged_order_image');
define('_247AROUND_BB_DELIVERED','Delivered');
define('_247AROUND_BB_IN_PROCESS','InProcess');
define('_247AROUND_BB_ORDER_COMPLETED_CURRENT_STATUS','Completed');
define('_247AROUND_BB_REPORT_ISSUE_INTERNAL_STATUS','Claimed Raised By CP');
define('_247AROUND_BB_ORDER_COMPLETED_INTERNAL_STATUS','Auto Approve Spec. Match');
define('_247AROUND_BB_ORDER_NOT_RECEIVED_INTERNAL_STATUS','To Be Claimed Not Delivered');
define('CLAIM_SUBMITTED','Claim Submitted');
define('TO_BE_CLAIMED','To Be Claimed');
define('CLAIM_SETTLED_BY_AMAZON','Claim Settled by Amazon');
define('_247AROUND_BB_REPORT_ISSUE_IN_PROCESS','InProcess_Claimed_Raised_By_CP');
define('_247AROUND_BB_NOT_DELIVERED_IN_PROCESS','InProcess_Not_delivered');
define('_247AROUND_BB_247APPROVED_STATUS','Refunded');
define('_247AROUND_BB_Damaged_STATUS','Damaged');
define('_247AROUND_BB_NOT_DELIVERED','Not Delivered');
define('_247AROUND_BB_TO_BE_CLAIMED','To Be Claimed');
define('_247AROUND_BB_ORDER_MISMATCH','Product Mismatch');
define('_247AROUND_BB_ORDER_REJECTED','Rejected');
define('_247AROUND_BB_ORDER_TAT_BREACH','Tat Breach');

//new shop address added
define('NEW_SHOP_ADDRESS_ADDED','New Shop Address Added');
define('SHOP_ADDRESS_DEACTIVATED','Shop Address De-Activated');
define('SHOP_ADDRESS_ACTIVATED','Shop Address Activated');
define('BB_CP_ADDRESS','CP_ADDRESS');

define('CUSTOMER_NOT_VISTED_TO_SERVICE_CENTER','Customer not visited to service center');
define("HSN_CODE", "998715");
define("STAND_HSN_CODE", "8302");


define('_247AROUND_BB_TAG_CLAIMED_SUBMITTED_BROKEN', 'Broken');
define('CLAIM_APPROVED','Claim Approved');
define('CLAIM_REJECTED','Claim Rejected');
define('CLAIM_SETTLED','Claim Settled');

define('BUYBACK_VOUCHER','Buyback_Voucher');
define('PARTNER_VOUCHER','Partner_Voucher');

define('CLAIM_DEBIT_NOTE_RAISED','Claim Debit Note Raised');

define('SF_NOTIFICATION_MSG','Urgent - Update your GST number by clicking on the GST link above IMMEDIATELY. Your CRM will get deactivated if GST is not updated soon.');

//Define error code for Type is not found for partner_sd_sb.php file
define('ERR_UPDATE_TYPE_NOT_FOUND_CODE', -1018);
define('ERR_UPDATE_TYPE_NOT_FOUND_MSG', 'UpdateType Value Does Not Exist');

//Define error code for Booking Already Schedule
define('ERR_BOOKING_ALREADY_SCHEDULED_CODE', -1019);
//Define error code for Booking Already Schedule
define('ERR_BOOKING_ALREADY_SCHEDULED_MSG', 'Booking Already Scheduled');

//Define error code for Booking Already Schedule
define('ERR_STATUS_EMPTY_CODE', -1020);
//Define error code for Booking Already Schedule
define('ERR_STATUS_EMPTY_MSG', 'Status Should Not Be Empty');

define('SUCCESS_UPDATED_MSG', 'Order is Updated Successfully');
define('PRODUCT_DELIVERED', 'PRODUCT_DELIVERED');

//SMS deactivation constant
define('SMS_DEACTIVATION_MAIL_SERVER','{md-in-13.webhostbox.net}');
define('SMS_DEACTIVATION_EMAIL','chhavid@247around.com');
define('SMS_DEACTIVATION_PASSWORD','chhavid247');
define('SMS_DEACTIVATION_SCRIPT_RUNNING_DAYS','-1 day');
define('SMS_DEACTIVATION_EMAIL_SUBJECT','has requested for opt-out!');
define('SMS_DEACTIVATION_NO_NEW_REQUEST_MSG','There is not any new request');

define('PREPAID_LOW_AMOUNT_MSG_FOR_PARTNER','Your Credit is low. Add credit');
define('PREPAID_LOW_AMOUNT_MSG_FOR_ADMIN','Your Credit is low. Add credit');
define('PREPAID_LOW_AMOUNT_MSG_FOR_DEALER','Your Credit is low. Add credit');

define("PARTNER_ADVANCE_DESCRIPTION", "Advance Payment");
define("PARTNER_INVOICE_BUTTON", "CRM Setup Invoice");
define("CT_INVOICE_BUTTON", "Sweetener Invoice");
define("QC_INVOICE_DESCRIPTION", "Service Charges for QC");
define("CRM_SETUP_INVOICE_DESCRIPTION", "Annual Setup Charges");
define("QC_HSN_CODE", 998397);

define("TAT_BREACH_DAYS", "-45 days");

// QC Balance Read Email Constant
define('QC_BALANCE_READ_EMAIL','sachinj@247around.com');
define('QC_BALANCE_READ_EMAIL_PASSWORD','sachinj');
define('TV_BALANCE_EMAIL_SUBJECT', "Amazon_Ext_buyback SVC Balance '7014851010000071' Notification");
define('LA_BALANCE_EMAIL_SUBJECT', "Amazon_Ext_buyback SVC Balance '7014851010000029' Notification");


define('SPARE_OOW_EST_REQUESTED','Spare Estimate Cost Required');

define('SPARE_OOW_EST_GIVEN','Spare Estimate Cost Given');
define('SPARE_OOW_EST_MARGIN','0.25');
define('REPAIR_OOW_PARTS_PRICE_TAGS','Spare Parts');
define('REPAIR_OOW_VENDOR_PERCENTAGE','10');

define('FILE_UPLOAD_SUCCESS_STATUS', 'Success');
define('FILE_UPLOAD_FAILED_STATUS', 'Failed');
define("REPAIR_OOW_TAG", "Repair - Out Of Warranty");
define("NOT_UPCOUNTRY_PRICE_TAG", "-1");
define("PARTNER_PROVIDE_UPCOUNTRY_PRICE_TAG", "1");

define("REPEAT_BOOKING_TAG", "Repeat Booking");

define("_247AROUND_PRODUCT_TAG", "Product");


//tv service id
define("_247AROUND_TV_SERVICE_ID",'46');
//washing_machine service id
define("_247AROUND_WASHING_MACHINE_SERVICE_ID",'28');
//microwave service id
define("_247AROUND_MICROWAVE_SERVICE_ID",'42');
//water_purifier service id
define("_247AROUND_WATER_PURIFIER_SERVICE_ID",'38');
//ac service id
define("_247AROUND_AC_SERVICE_ID",'50');
//refrigerator service id
define("_247AROUND_REFRIGERATOR_SERVICE_ID",'37');
//geyser service id
define("_247AROUND_GEYSER_SERVICE_ID",'32');



define("SPARE_PART_RADIO_BUTTON_NOT_REQUIRED", "0"); 
define("ESTIMATE_APPROVED_BY_CUSTOMER", "Spare Estimate Approved By Customer");
/* End of file constants.php */
/* Location: ./application/config/constants.php */
