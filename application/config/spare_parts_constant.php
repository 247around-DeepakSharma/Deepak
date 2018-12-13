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
define('SPARE_OOW_EST_MARGIN','0.25');
define('REPAIR_OOW_PARTS_PRICE_TAGS','Spare Parts');
define('REPAIR_OOW_VENDOR_PERCENTAGE','10');
define('REPAIR_OOW_AROUND_PERCENTAGE','0.15');
define('SPARE_PARTS_REQUIRED', 'Spare Parts Required');
define('SPARE_PARTS_REQUESTED', 'Spare Parts Requested');
// Defective Parts pending
define('DEFECTIVE_PARTS_PENDING','Defective Part Pending');
// Defective Parts Shipped by SF
define('DEFECTIVE_PARTS_SHIPPED','Defective Part Shipped By SF');
// Defective Parts Received by SF
define('DEFECTIVE_PARTS_RECEIVED','Defective Part Received By Partner');
// Defective Parts Received by SF
define('DEFECTIVE_PARTS_REJECTED','Defective Part Rejected By Partner');
//Zopper booking- Part Arrange By Same Vendor
define("PART_ARRANGE_BY_SAME_VENDOR", 2);
//Zopper booking- Part Arrange By Different Vendor
define("PART_ARRANGE_BY_DIFF_VENDOR", 1);
//Spare Parts Received By SF
define('SPARE_PARTS_DELIVERED', 'Spare Parts Delivered to SF');
//Spare Parts Shipped By SF
define('SPARE_PARTS_SHIPPED', 'Spare Parts Shipped by Partner');
// Spare Reschedule 
define('SPARE_RESCHEDULED', 'Spare_Rescheduled');

define('SPARE_PART_BOOKING_TAG','Spare Parts');

define('REQUESTED_QUOTE_REJECTED','Requested Quote Rejected');

define("SPARE_PARTS_CANCELLED", "Spare Parts Cancelled");

define("SPARE_DELIVERED_TO_SF", "Delivered");
define("SPARE_SHIPPED_BY_PARTNER", "Shipped");
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
define('SF_SPARE_OOT_DAYS','7');

//for now default warehouse id is 10 (sf = manish ji)
define('DEFAULT_WAREHOUSE_ID',10);

//tracking more production api key
define('TRACKINGMORE_PROD_API_KEY','06ec4a31-6125-4b01-9da9-bf8d420767df');
define('DELIVERY_CONFIRMED_WITH_COURIER','Delivery Confirmed With Courier Company');

define('DEFECTIVE_PARTS_RECEIVED_API_CONFORMATION','Defective Part Received By Partner API Conformation');

define('REQUESTED_SPARED_REMAP','Requested spare remap to');
//Defactive part Delivered date by Courier API
define('DEFACTIVE_PART_DELIVERED_DATE_BY_COURIER_API','defactive_part_received_date_by_courier_api');
//Delivered Spare Status 
define('DELIVERED_SPARE_STATUS','Delivered');
define("MICRO_WAREHOUSE_CHARGES_TYPE", "micro-warehouse-charges");
//Micro Warehouse Description
define("MICRO_WAREHOUSE_CHARGES_DESCRIPTION", "Micro Warehouse");

// Courier details  status
define("COURIER_DETAILS_STATUS", "pick-up");






