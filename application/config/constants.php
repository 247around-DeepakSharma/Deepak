<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include('instance_load.php');
include('third_party_credentials.php');
include('email_template_constant.php');
include('spare_parts_constant.php');
include('push_notifications_constant.php');
include('upcountry_constant.php');
include('email_tag_constant.php');
include('paytm_payment_constant.php');
include('booking_status_actor_next_action_constant.php');
include('paytm_gateway_transaction.php');
include('partners_id_constant.php');
include('serial_number_failed_msg_constant.php');
include('warehouse_constant.php');
include('telephony_constant.php');
include('taxpro_api_constant.php');
include('invoice_constant.php');
include('buyback_constant.php');
include('sms_constant.php');
include('partner_permission_constant.php');
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

define('FOPEN_READ','rb');
define('FOPEN_READ_WRITE','r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE','wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE','w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE','ab');
define('FOPEN_READ_WRITE_CREATE','a+b');
define('FOPEN_WRITE_CREATE_STRICT','xb');
define('FOPEN_READ_WRITE_CREATE_STRICT','x+b');

define('INT_STATUS_CUSTOMER_NOT_REACHABLE', 'Customer not reachable / Customer not picked phone');
define('DEFAULT_SEARCH_PAGE', 'employee/user');

//Agent ID used when Partner inserts a Booking by calling our API
//define('DEFAULT_PARTNER_AGENT', 978978);

//define('_247AROUND2',247002);
//define('_247AROUND3',247003);
//define('_247AROUND99',247999);
define('_247AROUND_DEFAULT_AGENT', $db->default_agent_id);
define('_247AROUND_DEFAULT_AGENT_NAME', $db->crm_title);

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
define('_247AROUND_BRACKETS_26_32_UNIT_PRICE',190);
//constant to define 247around brackets charge for 36-42 inch
define('_247AROUND_BRACKETS_36_42_UNIT_PRICE',190);
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
define('_247AROUND_ACCOUNTANT','accountant');
define('_247AROUND_AM','accountmanager');

//
define('Max_TIME_TO_BE_ASSIGNED_ENGINEER', 12);

define('Max_TIME_WITH_IN_ASSIGNED_ENGINEER', 3);

define('PRODUCT_NOT_DELIVERED_TO_CUSTOMER','Product not delivered to customer');

define('CUSTOMER_ASK_TO_RESCHEDULE','Customer asked to reschedule');

define('CUSTOMER_NOT_REACHABLE','Customer not reachable / Customer not picked phone');

define('CALLBACK_SCHEDULED', 'Callback Scheduled');
//constant to define default brackets order id
define('_247_AROUND_DEFAULT_BRACKETS_ORDER_ID',201611000001);
//Snapdeal ID
define('SNAPDEAL_ID',1);
//Snapdeal host
define('SNAPDEAL_HOST','snapdeal.com');
//Define Email ID to send system health emails
define('SYS_HEALTH_NAME','247around Health Monitor');
define('SYS_HEALTH_EMAIL','health@247around.com');
//Define New CRM Contstant for SF
define('NEW_SF_CRM',"New SF CRM");
//Define Ols CRM Contstant for SF
define('OLD_SF_CRM',"Old SF CRM");
//Define New Partner Lead
define('_247AROUND_NEW_PARTNER_LEAD','New_Lead');
//Define Constant for Snapdeal Delivered Excel File Type
define('_247AROUND_SNAPDEAL_DELIVERED','Snapdeal-Delivered');
//Define Constant for Snapdeal Shipped Excel File Type
define('_247AROUND_SNAPDEAL_SHIPPED','Snapdeal-Shipped');
//Define Constant for Paytm Shipped Excel File Type
define('_247AROUND_PAYTM_DELIVERED','Paytm-Delivered');
//Define Constant for Satya File 
define('_247AROUND_SATYA_DELIVERED','Wybor-Delivered');
//Define Constant for Akai File 
define('_247AROUND_AKAI_DELIVERED','Akai-Delivered');
//Define Constant for Aquagrand File 
define('_247AROUND_AQUAGRAND_PLUS_DELIVERED','Aquagrand-Plus-Delivered');
//Define Constant for Vendor Pincode Excel File Type
define('_247AROUND_VENDOR_PINCODE','Vendor-Pincode');
//Define Constant for Vendor Pincode Excel File Type
define('_247AROUND_SF_PRICE_LIST','SF-Price-List');
//Define Constant for Partner Appliance Details
define('_247AROUND_PARTNER_APPLIANCE_DETAILS','Partner-Appliance-Details');
//Define Partner Type
define('OEM', "OEM");
define('EXTWARRANTYPROVIDERTYPE', "EXT_WARRANTY_PROVIDER");
define('BUYBACKTYPE', "BUYBACK");
define('INTERNALTYPE', "INTERNAL");
define('ECOMMERCETYPE', "ECOMMERCE");
//Define New SF Creation
define('NEW_SF_ADDED','New SF Added');
define('NEW_SF_DOCUMENTS','New SF Documents Added');
define('NEW_SF_BRANDS','New SF Brands Added');
define('NEW_SF_CONTACTS','New SF Contacts Added');
define('NEW_SF_BANK_DETAILS','New SF Bank Details Added');
//Define Edit SF State
define('SF_UPDATED','SF Updated');
//Define New Partner Creation
define('NEW_PARTNER_ADDED','New Partner Added');
//Define Edit SF State
define('PARTNER_UPDATED','Partner Updated');
define('IS_DEFAULT_ENGINEER', TRUE);
define('DEFAULT_ENGINEER', 24700001);

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
define('PARTNERS_MISSED_CALLED_NUMBER','01143092150');
//Define snapdeal new missed call number
define('PARTNERS_MISSED_CALLED_NUMBER_2','01143092150');
//Define snapdeal new missed call number
define('SNAPDEAL_MISSED_CALLED_NUMBER','01143078384');
//Define Android App new missed call number
define('ANDROID_APP_MISSED_CALLED_NUMBER','01139585684');

//Define new missed call number for AC installation
define('AC_SERVICE_MISSED_CALLED_NUMBER','01139595450');

define('HOME_THEATER_REPAIR_SERVICE_TAG', 'Repair - In Warranty (Service Center Visit)');
define('HOME_THEATER_REPAIR_SERVICE_TAG_OUT_OF_WARRANTY', 'Repair - Out Of Warranty (Service Center Visit)');

define('REPAIR_STRING', 'Repair');
define('IN_WARRANTY_STRING', 'In');

define("CAP_ON_PENALTY_AMOUNT", "100");
define("PAYTM", "3");
define("PENALTY_ON_COMPLETED_BOOKING", TRUE);
define("PENALTY_ON_CANCELLED_BOOKING", TRUE);

define ('SF_UNAVAILABLE_SMS_NOT_SENT', 'SMS Not Sent To Customer For Installation');
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
define('FAILURE_CODE', -247);
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
define('ERR_INVALID_SERVICE_AREA_CODE', -1021);
define('ERR_BOOKING_NOT_ASSIGNED_CODE', -1022);
define('ERR_SPD_DATE_MANDATORY_CODE', -1023);
define('ERR_INVALID_SPD_DATE_CODE', -1024);
define('ERR_CALL_ALREADY_CANCELLED_CODE', -1024);

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
define('ERR_INVALID_SERVICE_AREA_MSG', 'Non Serviceable Area');
define('ERR_BOOKING_NOT_ASSIGNED_MSG', 'Engineer Not Assigned');
define('ERR_SPD_DATE_MANDATORY_MSG', 'Service Promise Date is Mandatory');
define('ERR_INVALID_SPD_DATE_MSG', 'Invalid Service Promise Date');
define('ERR_CALL_ALREADY_CANCELLED_MSG', 'Call is already cancelled');
//ICICI Bank name constant
define('ICICI_BANK_NAME', 'ICICI Bank Ltd');

//Default email id for sending internal emails
define('NOREPLY_EMAIL_ID', 'noreply@247around.com');

//ADIL EMIL ID
define("ADIL_EMAIL_ID", "adila@247around.com");

//rating new state
define('RATING_NEW_STATE','Completed_With_Rating');

//Regional Managers Email ID
define("RM_EMAIL", "souvikg@247around.com, suresh@247around.com, oza@247around.com, arunk@247around.com");

//Email IDs when SF is not found in Vendor Pincode Mapping file
define("SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_TO", "adila@247around.com");
define("SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_CC", "");

//promotional sms constant
define('COMPLETED_PROMOTINAL_SMS_1','completed_promotional_sms_1');
define('COMPLETED_PROMOTINAL_SMS_2','completed_promotional_sms_2');
define('CANCELLED_PROMOTINAL_SMS_1','cancelled_promotional_sms_1');
define('CANCELLED_PROMOTINAL_SMS_2','cancelled_promotional_sms_2');
define('CANCELLED_QUERY_PROMOTINAL_SMS_1','cancelled_query_promotional_sms_1');
define('CANCELLED_QUERY_PROMOTINAL_SMS_2','cancelled_query_promotional_sms_2');
define('BOOKING_NOT_EXIST_PROMOTINAL_SMS_1','booking_not_exist_promotional_sms_1');
define('BOOKING_NOT_EXIST_PROMOTINAL_SMS_2','booking_not_exist_promotional_sms_2');


//miss call rating sms
define('MISSED_CALL_RATING_SMS','missed_call_rating_sms');
define('GOOD_MISSED_CALL_RATING_NUMBER','01140849145');
define('POOR_MISSED_CALL_RATING_NUMBER','01140849146');
define('MISSED_CALL_DEFAULT_RATING', '5');


define('CUSTOMER_NOT_VISTED_TO_SERVICE_CENTER','Customer not visited to service center');

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
define('SMS_DEACTIVATION_MAIL_SERVER','{imap.gmail.com:993/imap/ssl/novalidate-cert}');
define('SMS_DEACTIVATION_EMAIL','chhavid@247around.com');
define('SMS_DEACTIVATION_PASSWORD','chhavid247');
define('SMS_DEACTIVATION_SCRIPT_RUNNING_DAYS','-1 day');
define('SMS_DEACTIVATION_EMAIL_SUBJECT','has requested for opt-out!');
define('SMS_DEACTIVATION_NO_NEW_REQUEST_MSG','There is not any new request');
define('PREPAID_DEACTIVATED_MSG_FOR_PARTNER','Your Account is De-Activated');
define('PREPAID_LOW_AMOUNT_MSG_FOR_PARTNER','Low Balance, Please Recharge Your Account Immediately');
define('POSTPAID_PARTNER_UNPAID_INVOICE_MESSAGE','Low Balance, Please Recharge Your Account Immediately');
define('PREPAID_LOW_AMOUNT_MSG_FOR_ADMIN','Low Balance, Please Inform Brand To Recharge Account Immediately');
define('PREPAID_LOW_AMOUNT_MSG_FOR_DEALER','Low Balance, Please Ask Brand To Recharge Account Immediately');

define("PARTNER_ADVANCE_DESCRIPTION", "Advance Payment");
define("PARTNER_INVOICE_BUTTON", "CRM Setup Invoice");
define("CT_INVOICE_BUTTON", "Sweetener Invoice");
define("QC_INVOICE_DESCRIPTION", "Service Charges for QC");
define("CRM_SETUP_INVOICE_DESCRIPTION", "Annual Setup Charges");
define("TAT_BREACH_DAYS", "-45 days");

// QC Balance Read Email Constant
define('TV_BALANCE_EMAIL_SUBJECT', "Amazon_Ext_buyback SVC Balance '7014851010000071' Notification");
define('LA_BALANCE_EMAIL_SUBJECT', "Amazon_Ext_buyback SVC Balance '7014851010000029' Notification");
define('MOBILE_BALANCE_EMAIL_SUBJECT', "Amazon_Ext_buyback SVC Balance '7014851010000091' Notification");

define('FILE_UPLOAD_SUCCESS_STATUS', 'Success');
define('FILE_UPLOAD_FAILED_STATUS', 'Failed');

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
//audio system service id
define("_247AROUND_AUDIO_SYSTEM_SERVICE_ID",'45');
//Chimney service id
define("_247AROUND_CHIMNEY_SERVICE_ID",'44');
// We will display Not received button after 3 days of Delivery date
define("NO_OF_DAYS_NOT_SHOW_NOT_RECEIVED_BUTTON", -3);

define("AUTO_ACK_ADMIN_REMARKS", "Auto Acknowledge");

//missing_pincode_email
define("MISSING_PINCODE_EMAIL_TO",NITS_ANUJ_EMAIL_ID);

//sales email_id
define('_247AROUND_SALES_EMAIL','sales@247around.com');

//vijaya email_id
define('_247AROUND_VIJAYA_EMAIL','vijaya@247around.com');

//brackets string
define('BRACKET_STRING','Bracket');
//less than 32" bracket part number
define('LESS_THAN_32_BRACKETS_PART_NUMBER','B-24732');
//greater than 32" bracket part number
define('GREATER_THAN_32_BRACKETS_PART_NUMBER','B-24733');
//wall mount price tag
define('_247AROUND_WALL_MOUNT__PRICE_TAG','Wall Mount Stand');
//247around employee string
define('_247AROUND_EMPLOYEE_STRING','247around');
//247around partner string
define('_247AROUND_PARTNER_STRING','partner');
//247around sf string
define('_247AROUND_SF_STRING','vendor');
//247around cp string
define('_247AROUND_CP_STRING','CP');

define('ENGINEER_COMPLETE_STATUS',"InProcess_Engg_Completed" );
define('ENGINEER_CANCELLED_STATUS',"InProcess_Engg_Cancelled" );

define('PINCODE_FILE_PASSSWORD',"247around" );
//Chavi
//define('PUSH_NOTIFICATION_API_KEY','3d15cad629128ac894c9a73a0ff5852b');
//Around
//define('PUSH_NOTIFICATION_API_KEY','5e80dc70981389335ae38d969ca075be');
//define('PUSH_NOTIFICATION_SUBSCRIBER_LIST_SEND_NOTIFICATION_URL','https://pushcrew.com/api/v1/send/list');

define('BUYBACK','buyback');
//Collateral 
define('LEARNING_DOCUMENT','Brand_Collateral');
define('CONTRACT','Contract');
define("BOOKING_NOT_UPDATED_PENALTY_CRITERIA", 2);
//Same penalty can not be levied within 120 minute
define('AROUND_PENALTY_NOT_APPLIED_WITH_IN','120');
define('PARTNER_PENALTY_NOT_APPLIED_WITH_IN','120');
define("MAKE_CUTOMER_PAYMENT_INVOICE_GREATER_THAN", 5);


//tv SVC
define('TV_SVC', "7014851010000071");
//LA SVC
define('LA_SVC', "7014851010000029");
//Mobile SVC
define('MOBILE_SVC', "7014851010000091");


define("DEPENDENCY_ON_AROUND", "SPD breach due to 247Around dependency");
define("DEPENDENCY_ON_CUSTOMER", "SPD breach due to customer dependency");
define("S3_WEBSITE_URL", "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/");

//mobile service id
define("_247AROUND_MOBILE_SERVICE_ID",'54');

//247around call center number
define('_247AROUND_CALLCENTER_NUMBER',9555000247);
//jeeves call center number
define('JEEVES_CALLCENTER_NUMBER',180042525252);
//Sharp call center number
define('SHARP_CALLCENTER_NUMBER',18004254322);
//jeeves websitelink
define('JEEVES_WEBSITE','www.jeeves.co.in');

define('CUSTOMER_INVOICE_TAG','CUSTOMER_INVOICE_TAG');

define('VINESH_FLIPKART_EMAIL', 'vinesh.poojari@flipkart.com,bsdflipkart@jeeves.co.in,naveen.n@jeeves.co.in,sathis.s@mnw.co.in,radha.c@jeeves.co.in');
define('THRESHOLD_LIMIT_TO_CANCEL_NOT_FOUND_SF_QUERIES', '5');
define('SF_NOT_FOUND_BOOKING_CANCELLED_REASON', 'Services not started in this region yet');
define('SF_NOT_FOUND_BOOKING_CANCELLED_REASON_TEXT', 'Service Not Available in this region');


define("_247Around_Rejected_SF_Update", "247Around Rejected SF Update");

define('SF_BOOKING_COMPLETE_STATUS', 'InProcess_Completed');
define('SF_BOOKING_CANCELLED_STATUS', 'InProcess_Cancelled');

define('JEEVES_CUSTOMER_RESCHEDULED', 'Customer rescheduled');
define('JEEVES_CUSTOMER_NO_RESPONSE','Customer No Response above 2 Days');
define('JEEVES_PRODUCT_NOT_DELIVERED','Set Not Delivered above 2 Days');
define('JEEVES_BOOKING_DELAY_BY_AROUND','Allotted/ Closed delay by SF / Jeeves');
define('JEEVES_CALL_COMPLETED_WITH_IN_TAT', "call completed with in TAT");

//Define Constant for Partner Inventory File 
define('PARTNER_INVENTORY_DETAILS_FILE','Inventory-file');

define('RESCHEDULE_FOR_UPCOUNTRY', "Upcountry Booking");
define("ENGINEER_ON_ROUTE", "Engineer on route");
define("INVOCIE_TAG_FOR_INVENTORY", "inventory");


//Define Constant for Partner Appliance model
define('PARTNER_APPLIANCE_MODEL_FILe','Appliance-model');

define("PARTNER_CALL_CENTER_USER_GROUP", "Call Center");

//Define Constant for Partner BOM file upload
define('PARTNER_BOM_FILE','Partner-BOM');

//Define Constant for Alternate Spare Parts Mapping file upload
define('ALTERNATE_SPARE_PARTS_MAPPING','Alternate Spare Part Mapping');

//Partner Serial no file type
define('PARTNER_SERIAL_NUMBER_FILE_TYPE','partner_serial_number');

//warehouse incharge constant
define('WAREHOUSE_INCHARCGE_CONSTANT','Warehouse Incharge');    
define('NEW_CHARGES_ADDED','New Charges Added');
define('MISC_CHARGES_REMOVED','Miscellaneous Charges Removed');
//Partner Login
define('PARTNER_POC_ROLE_ID','1');


//INventory 
define('INVENTORY_HANDLER_ID', '10030');

//Annual Charge Invoice Tagging
define("ANNUAL_CHARGE_INVOICE_TAGGING", "Annual Charge");
//PARTNER_SWEETNER_CHARGE
define("PARTNER_SWEETENER_CHARGE_INVOICE_TAGGING", "Partner Sweetener Charge");

// Region Constatnts
define('EAST_RM', 'Souvik Ganguly');
define('SOUTH_RM', 'Suresh K');
define('WEST_RM', 'Rajendra Oza');
define('NORTH_RM', 'Arun Kaushik');
define('TOTAL', 'Total');

//do not allow cancel from partner panel after this time interval
define('BOOKING_CAN_NOT_BE_CANCEL_AFTER_THIS_TIME_INTERVAL',10);
//show this text when partner cancel booking before time interval
define('TEXT_FOR_BOOKING_CAN_NOT_BE_CANCEL_AFTER_THIS_TIME_INTERVAL','Booking can not be cancelled, Please contact to 247Around Team');
define('REVIEW_NOTIFICATION_TO_PARTNER_DAYS','2');
define("REVIEW_LIMIT_BEFORE", "BEFORE");
define("REVIEW_LIMIT_AFTER", "AFTER");
//Define Constant for upload docket file 
define('DOCKET_NUMBER_FILE_TYPE','upload_docket_number');
define('DOCKET_NUMBER_FILE','upload_docket_number');
//Partner invoice payment file type
define('INVOICE_PAYMENT_FILE_TYPE','invoice_payment_file_type');
// 247around service string for invoices
define('_247AROUND_SERVICE_STRING','Service');
// 247around installation and repair string for invoices
define('_247AROUND_INSTALLATION_AND_REPAIR_STRING','Installation & Repair');
// 247around GST Debit Note string for invoices
define('_247AROUND_GST_DEBIT_NOTE_STRING','GST Debit Note');
// it is use to check entity-type Warehouse 

define('WAREHOUSE','Warehouse');  
//constant to define New Query state
define('_247AROUND_QUERY','Query');
//Sf Select Wrong Call Area
define('CANCELLATION_REASON_WRONG_AREA', 'Wrong call - Not in our area.');
//send broadcast sms to all vendors
define('BROADCAST_SMS_TO_VENDOR', 'broadcast_sms_to_vendor');

define("_247AROUND_WEBSITE", "247around-Website");

//Repeat Booking
define("_247AROUND_REPEAT_BOOKING_ALLOWED_DAYS", 90);
define("_PARTNER_REPEAT_BOOKING_ALLOWED_DAYS", 30);
define("_NO_REPEAT_BOOKING_FLAG", 2);
define("_ONE_REPEAT_BOOKING_FLAG", 1);
define("_MULTIPLE_REPEAT_BOOKING_FLAG", 3);
define("IS_SN_CORRECT",0);
define("NOT_DEFINE_SN",2);

//Define Constant for booking upload file type for updating partner Royalty
define('PARTNER_ROYALTY_FILE_TYPE','partner_royalty_file');
define('SPARE_REQUEST_SMS_TAG', 'spare_request_sms_tag');
define('CAN_NOT_ALLOW_RE_COMPLETE_BOOKING_TEXT', "Don't have permission to re-complete booking.");
define('PAYMENT_SUCCESS_NOTICATION_TYPE', '5');
define('PAYMENT_HOLD_NOTICATION_TYPE', '6');

//Define constant for Proforma Invoices
define("CRM_SETUP_PROFORMA_INVOICE_DESCRIPTION", "Annual Setup Charges");

//Define constant for razorpay api for bank ifsc code varification
define("IFSC_CODE_VALIDATION_API_URL", "https://ifsc.razorpay.com/");

define('INVENTORY_USER_GROUP', 'inventory_manager');

define('_247AROUND_INVOICE_TEMPLATE_DESCRIPTION', 'Book Appliance Service from Qualified Engineers on "247AROUND" App / On Phone - 9555000247 / On Website - www.247around.com');
define('_247AROUND_TAX_INVOICE_LOGO_CELL', 'B3');
define('_247AROUND_TAX_INVOICE_SEAL_CELL', 'G26');
define('_247AROUND_TAX_INVOICE_SIGN_CELL', 'K26');
//constant to define Admin Approved Completed bookings
define('_247AROUND_COMPLETED_APPROVED','Completed_Approved');
//constant to define Admin Approved Completed bookings
define('_247AROUND_COMPLETED_REJECTED','Completed_Rejected');
define('_247AROUND_WRONG_PINCODE_CANCEL_REASON', 'The Custoner has given wrong pincode');
//constant to define Admin Rejected cancelled bookings
define('_247AROUND_CANCELED_REJECTED','Cancelled_Rejected');
//constant to define Admin Approved canceled bookings
define('_247AROUND_CANCELED_APPROVED','Cancelled_Approved');
//247around engineer string
define('_247AROUND_ENGINEER_STRING','engineer');

define('SF_PURCHASE_INVOICE_FILE_TYPE', '4');
define('FREE_INSTALLATION_REQUEST', 'Installation & Demo (Free)');
define('BOOKING_PURCHASE_INVOICE_FILE_TYPE', '1');

define('_247AROUND_TRANSFERED_TO_WAREHOUSE', 'Spare transfer from one warehouse to another warehouse');  //

define('_247AROUND_TRANSFERED_TO_NEXT_ACTION', 'Spare Part to be shipped');
define('SF_UPDATE_FORM_VALIDATION_TEXT', 'sf_update');

define('_247AROUND_TRANSFERED_TO_VENDOR', 'Spare transfer from  partner to vendor');
define('_247AROUND_TRANSFERED_TO_PARTNER', 'Spare transfer from  warehouse to partner');

define('SYMPTOM_DEFECT_SOLUTION_MAPPING_FILE', 'Symptom_Defect_Solution_Mapping_File');

// CRM Training menu Link
define('_247AROUND_CRM_TRAINING', 'https://drive.google.com/drive/folders/1Ri7PZJCuCaSSWt-Bv1InrHiQgtb79Hlp');

//send broadcast sms to all vendors
define('ENGINEER_LOGIN_SMS_TEMPLATE', 'engineer_login_sms_template');
define('SERIAL_NUMBER_PIC_DIR', 'serial_number_pic');

define('PARTNER_APPLIANCE_MAPPING', 'Partner_Appliance_Mapping_File');
//warranty related constants
define('IN_WARRANTY_STATUS', 1);
define('EXTENDED_WARRANTY_STATUS', 2);
define('PARTNER_DEFAULT_CONTACT_PERSON_ID', '1002706');

//tv hsn code
define("_247AROUND_TV_HSN_CODE","84159000");
//washing_machine hsn code
define("_247AROUND_WASHING_MACHINE_HSN_CODE","85014090");
//microwave hsn code
define("_247AROUND_MICROWAVE_HSN_CODE","85299090");
//water_purifier hsn code
define("_247AROUND_WATER_PURIFIER_HSN_CODE","85299090");
//ac hsn code
define("_247AROUND_AC_HSN_CODE","84159000");
//refrigerator hsn code
define("_247AROUND_REFRIGERATOR_HSN_CODE","85014090");
//geyser hsn code
define("_247AROUND_GEYSER_HSN_CODE","85444920");
//audio system hsn code
define("_247AROUND_AUDIO_SYSTEM_HSN_CODE","85299090");
//Chimney hsn code
define("_247AROUND_CHIMNEY_HSN_CODE","85299090");

//tv gst rate
define("_247AROUND_TV_GST_RATE",18);
//washing_machine gst rate
define("_247AROUND_WASHING_MACHINE_GST_RATE",18);
//microwave gst rate
define("_247AROUND_MICROWAVE_GST_RATE",18);
//water_purifier gst rate
define("_247AROUND_WATER_PURIFIER_GST_RATE",18);
//ac gst rate
define("_247AROUND_AC_GST_RATE",18);
//refrigerator gst rate
define("_247AROUND_REFRIGERATOR_GST_RATE",18);
//geyser gst rate
define("_247AROUND_GEYSER_GST_RATE",18);
//audio system gst rate
define("_247AROUND_AUDIO_SYSTEM_GST_RATE",18);
//Chimney gst rate
define("_247AROUND_CHIMNEY_GST_RATE",18);
define('REJECTED_FROM_REVIEW_STATUS','Rejected From Review');

define('WARRANTY_TYPE_AMC', 'AMC (Annual Maintenance Contract)');
/* End of file constants.php */
/* Location: ./application/config/constants.php */
