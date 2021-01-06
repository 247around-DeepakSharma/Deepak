<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define("SPARE_HSN_CODE", "85299090");
define('SEND_DEFECTIVE_SPARE_PARTS_NOTIFICATION',7 );
define("SPARE_PART_RADIO_BUTTON_NOT_REQUIRED", "0"); 
define("ESTIMATE_APPROVED_BY_CUSTOMER", "Spare Estimate Approved By Customer");
define("REPAIR_OOW_TAG", "Repair - Out Of Warranty (Home Visit)");
define("REPAIR_IN_WARRANTY_TAG", "Repair - In Warranty (Home Visit)");
define('SPARE_OOW_EST_REQUESTED','Request Quote for Spare Part');
define('SPARE_OOW_EST_GIVEN','Spare Estimate Cost Given');
define('SPARE_OOW_SHIPPED','Out Of Warranty Part Shipped By Partner');
define('SPARE_OOW_EST_UPDATED','Spare Estimate Cost Updated');
define('SPARE_OOW_EST_MARGIN','0.30');
define('REPAIR_OOW_PARTS_PRICE_TAGS','Spare Parts');
define('REPAIR_OOW_VENDOR_PERCENTAGE','15');
define('REPAIR_OOW_AROUND_PERCENTAGE','0.15');
define('SPARE_PARTS_REQUIRED', 'Spare Parts Required');
define('SPARE_PARTS_REQUESTED', 'Spare Parts Requested');
// Defective Parts pending
define('DEFECTIVE_PARTS_PENDING','Defective Part To Be Shipped By SF');
// Defective Parts Shipped by SF
define('DEFECTIVE_PARTS_SHIPPED','Defective Part Shipped By SF');
// Defective Parts Received by Partner
define('DEFECTIVE_PARTS_RECEIVED','Defective Part Received By Partner');
// Defective Parts Received by Warehouse
define('DEFECTIVE_PARTS_RECEIVED_BY_WAREHOUSE','Defective Part Received By Warehouse');
// Defective Parts Rejected by Partner
define('DEFECTIVE_PARTS_REJECTED','Defective Part Rejected By Partner');
// Defective Parts Received by Warehouse
define('DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE','Defective Part Rejected By Warehouse');
//Zopper booking- Part Arrange By Same Vendor
define("PART_ARRANGE_BY_SAME_VENDOR", 2);
//Zopper booking- Part Arrange By Different Vendor
define("PART_ARRANGE_BY_DIFF_VENDOR", 1);
//Spare Parts Shipped By SF
define('SPARE_PARTS_SHIPPED', 'Spare Parts Shipped by Partner');
//SPARE paarts shipoped By Warehouse
define('SPARE_PARTS_SHIPPED_BY_WAREHOUSE', 'Spare Parts Shipped by Warehouse');
// Spare Reschedule 
define('SPARE_RESCHEDULED', 'Spare_Rescheduled');

define('SPARE_PART_BOOKING_TAG','Spare Parts');

define('REQUESTED_QUOTE_REJECTED','Requested Quote Rejected');

define("SPARE_PARTS_CANCELLED", "Spare Parts Cancelled");
//Spare Parts Received By SF
define("SPARE_DELIVERED_TO_SF", "Spare Parts Delivered to SF");
define("SPARE_SHIPPED_BY_PARTNER", "Spare Parts Shipped By Partner");
define("SPARE_PARTS_NOT_DELIVERED_TO_SF", "Spare parts not received");

define('DEFECTIVE_PARTS_SEND_TO_PARTNER_BY_WH','Defective parts send by warehouse to partner');
define('PARTNER_ACK_DEFECTIVE_PARTS_SEND_BY_WH','Partner acknowledge defective parts send by warehouse');
define('SPARE_SHIPPED_TO_WAREHOUSE','Partner shipped spare to Warehouse');

//MSG- Sf can not complete a booking if spare part request is pending on partner
define("UNABLE_COMPLETE_BOOKING_SPARE_MSG", "Please wait, your requested part is pending for shipment.");
define("CANCEL_PAGE_SPARE_NOT_SHIPPED", "Please wait, requested part is pending for shipment.");
define("CANCEL_PAGE_SPARE_NOT_SHIPPED_FOR_PARTNER", "Spare Requested for this booking. Please Reject Spare First");
define("CANCEL_PAGE_SPARE_SHIPPED", "This booking can not be cancelled since spare part has already been shipped to SF.");

define('PARTNER_SPARE_OOT_DAYS','30');
define('SF_SPARE_OOT_DAYS','15');

//for now default warehouse id is 10 (sf = manish ji)
define('DEFAULT_WAREHOUSE_ID',15);

define('DELIVERY_CONFIRMED_WITH_COURIER','Delivery Confirmed With Courier Company');

define('DEFECTIVE_PARTS_RECEIVED_API_CONFORMATION','Defective Part Received By Partner API Conformation');

define('REQUESTED_SPARED_REMAP','Requested Spare Re-map To Partner');
//Defactive part Delivered date by Courier API
define('DEFACTIVE_PART_DELIVERED_DATE_BY_COURIER_API','defactive_part_received_date_by_courier_api');
//Delivered Spare Status 
define('DELIVERED_SPARE_STATUS','Delivered');
define("MICRO_WAREHOUSE_CHARGES_TYPE", "micro-warehouse-charges");
//led bar spare parts charge type
define("LEDBAR_SPARE_PARTS_CHARGES_TYPE", "ledbar-charges-fixed");
//open cell spare parts charge type
define("OPENCELL_SPARE_PARTS_CHARGES_TYPE", "opencell-charges-fixed");
//Micro Warehouse Description
define("MICRO_WAREHOUSE_CHARGES_DESCRIPTION", "Micro Warehouse");

// Courier details  status
define("COURIER_DETAILS_STATUS", "pick-up");


// Partner will send new parts
define("PARTNER_WILL_SEND_NEW_PARTS", "Partner will send new parts");

// Courier Lost 
define("COURIER_LOST", "Courier Lost");
//Spare to be billed to partner, partner did not ship new part to sf
define('BILL_TO_PARTNER_NOT_SHIP_PART_DAYS', 60);

// Spare Part On Approval
define("SPARE_PART_ON_APPROVAL", "Spare Part On Approval");

// Part Approved By Admin 
define("PART_APPROVED_BY_ADMIN", "Part Approved By Admin");
// Spare Part Updated
define("SPARE_PART_UPDATED", "Spare Part Updated");
//constant to define 'bill defective spare part to vendor' internal status
define("BIll_DEFECTIVE_SPARE_PART_TO_VENDOR", "Bill_Defective_Spare_Part_To_Vendor");


// spare parts in warranty 
define("SPARE_PART_IN_WARRANTY_STATUS", 1);
//spare parts in out-of warranty
define("SPARE_PART_IN_OUT_OF_WARRANTY_STATUS", 2);

//internal status page constant
define("BILL_DEFECTIVE_OOW_SPARE_PART_PAGE", "bill_defective_oow_spare_part");
define("BILL_DEFECTIVE_SPARE_PART_LOST_PAGE", "bill_defective_spare_part_lost");

//Defective Part Pending OOT day
define('DEFECTIVE_PART_PENDING_OOT_DAYS', 15);
//sf shipped defective parts after tat breach 
define('SHIPPED_DEFECTIVE_PARTS_AFTER_TAT_BREACH', 15);
//Defective parts shipped 
define('DEFECTIVE_PART_SHIPPED_OOT_DAYS', 15);

//show message when partner escalate booiking but it is pending on partner to ship new part
define('NOT_ESCALATE_BOOKING_DUE_SPARE_PENDING', 'You cannot escalate booking because it is pending to ship new part');
define('NOT_ESCALATE_BOOKING_DUE_SPARE_NOT_DELIVERED', 'You cannot escalate booking because it is pending to ship new part');
// Courier in pickup request 
define("COURIER_PICKUP_REQUEST", 2);
//Courier in pickup schedule
define("COURIER_PICKUP_SCHEDULE", 3);
//Auto spare delivered to sf after 14 days 
define('AUTO_ACKNOWLEDGE_SPARE_DELIVERED_TO_SF', 7);
define("EXTENDED_WARRANTY_TAG", "Extended Warranty");
//Pre Sale Repair Tag
define('PRESALE_REPAIR_TAG', 'Presale Repair');
define('WAREHOUSE_ACKNOWLEDGED_TO_RECEIVE_PARTS', "Warehouse acknowledged to receive MSL");

define('MSL_TRANSFERED_BY_PARTNER', "1");
define('MSL_TRANSFERED_BY_WAREHOUSE', "2");

define('GAS_RECHARGE_IN_WARRANTY', 'Gas Recharge - In Warranty');
define('GAS_RECHARGE_OUT_OF_WARRANTY', 'Gas Recharge - Out of Warranty');
define('AMC_PRICE_TAGS', 'AMC (Annual Maintenance Contract)');
define('MSL_TRANSFERED_BY_PARTNER_BY_EXCEL', 'Msl send by partner by excel upload');

define('UPLOAD_MSL_EXCEL_FILE', 'Upload msl excel file');
// Upload courier serviceable area excel file
define('UPLOAD_COURIER_SERVICEABLE_AREA_EXCEL_FILE', 'Upload courier serviceable area excel file');
 
define('NRN_APPROVED_BY_PARTNER', 'NRN Approved By Partner');
define('NRN_REVERSE_BY_PARTNER', 'NRN Reverse by Partner');
define('NRN_TO_BE_APPROVED_BY_PARTNER', 'NRN To Be Approved By Partner');
define('NRN_TO_BE_SHIPPED_BY_PARTNER', 'NRN To Be Shipped By Partner');
 
define('UNABLE_TO_COMPLETE_BOOKING_INVOICE_GENERATED_MSG', 'Invoice has been generated so booking can not be updated.');
 

define('COURIER_STATUS_FILE_MSG', 'Auto Acknowledged BY Courier Status File');
define('COURIER_STATUS_FILE_STATUS_DESCRIPTION', 'Delivered at destination city');
define('SPARE_RECIEVED_NOT_USED', 'Spare recieved but not used');
//MWH auto deliver consumption remove
define('REMOVE_PART_CONSUMPTION', 'Consumed MSL Part Removed');

define('DEFECTIVE_PARTS_SHIPPED_PENDING', 'All Defective Parts Not Shipped');


// spare consumption status tags 
define('PART_CONSUMED_TAG', 'part_consumed');
define('PART_NOT_RECEIVED_COURIER_LOST_TAG', 'part_not_received_courier_lost');
define('DAMAGE_BROKEN_PART_RECEIVED_TAG', 'damage_broken_part_received');
define('WRONG_PART_RECEIVED_TAG', 'wrong_part_received');
define('PART_SHIPPED_BUT_NOT_USED_TAG', 'ok_part_received_but_not_used');
define('PART_CANCELLED_STATUS_TAG', 'part_cancelled');
define('PART_NRN_APPROVED_STATUS_TAG', 'nrn_approved');
define('PART_NOT_RECEIVED_TAG', 'part_not_received');

define('OK_PART_TO_BE_SHIPPED', 'Ok Part To Be Shipped By SF');
define('DAMAGE_PART_TO_BE_SHIPPED', 'Damage Part To Be Shipped');

define('OK_PARTS_SHIPPED','Ok Part Shipped By SF');
define('CONSUMED_WRONG_PART_STATUS_ID', '4');
define('OK_PART_BUT_NOT_USED_CONSUMPTION_STATUS_ID', '5');
define('DAMAGE_PARTS_SHIPPED', 'Damage Part Shipped By SF');

define('COURIER_LOST_APPROVED_STATUS', 'Courier Lost Approved');
define('PART_CONSUMED_STATUS_ID', '1');
/* Alternate spare part Picked */
define('ALTERNATE_PART_PICKED', 'Alternate Part Picked');
// Ok Parts Received by Warehouse
define('Ok_PARTS_RECEIVED_BY_WAREHOUSE','Ok Part Received By Warehouse');
// Ok Parts Received by Partner
define('Ok_PARTS_RECEIVED','Ok Part Received By Partner');
// Ok Parts Rejected by Partner
define('OK_PARTS_REJECTED','Ok Part Rejected By Partner');
// Ok Parts Rejected by Warehouse
define('OK_PARTS_REJECTED_BY_WAREHOUSE','Ok Part Rejected By Warehouse');
// Ok parts send by warehouse to partner
define('OK_PARTS_SEND_TO_PARTNER_BY_WH','Ok parts send by warehouse to partner');
// Defective Parts pending OOT
define('DEFECTIVE_PARTS_PENDING_OOT','Defective Part To Be Shipped By SF Out Of TAT');
// InProcess_CourierLost
define('InProcess_Courier_Lost','Courier Lost Approval Pending');
//LED BAR part type
define('LED_BAR', 'LED BAR');
//Open cell Part Type
define('OPEN_CELL_PART_TYPE', 'OPEN CELL');
// Totala Parts shipped to SF
define('TOTAL_PARTS_SHIPPED_TO_SF','Total Parts Shipped To SF');
// spare parts out of tat days
define('SPARE_PARTS_OOT_DAYS', '45');
// spare acknowledge team
define('SPARE_ACKNOWLEDGE_TEAM', 'Defective/Ok Acknowledge Team');
// courier audit team
define('COURIER_AUDIT_TEAM', 'Courier Audit Team');
// part to be billed string
define('PART_TO_BE_BILLED', 'Part Return Warning -<br>To Be Billed');

// allow awb number updation for days
define('UPDATE_AWB_NUMBER_DAYS', 7);
// message to show awb number used more than UPDATE_AWB_NUMBER_DAYS ago. 
define('UPDATE_AWB_NUMBER_DAYS_MESSAGE', "Entered AWB Number has been already used more than 7 days ago.");
// Auto Acknowledged From API
define('AUTO_ACKNOWLEDGED_FROM_API', 1);
// Auto Acknowledged To Cron
define('AUTO_ACKNOWLEDGED_TO_CRON', 2);

