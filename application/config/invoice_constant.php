<?php 
/** vendor invoice constants  **/
define('SERVICE','Service');
define('INSTALLATION_AND_REPAIR', 'Installation & Repair');
define('FOC','FOC');
define('CASH', 'Cash');
define('ADVANCE', 'Advance');
define('SECURITY', 'Security');
define('EXCHANGE', 'Exchange');
define('PREPAID', 'Pre-paid');
define('RECURRING_CHARGES', 'Recurring Charges');
define('CRM', 'CRM');
define('CRM_PERFORMA', 'CRM Performa');
define('COMMISSION', 'Commission');
define('CREDIT_NOTE', 'Credit Note');
define('DEBIT_NOTE', 'Debit Note');
define('SPARES', 'Spares');
define('BRACKETS', 'Brackets');
define('GST_CREDIT_NOTE', 'GST Credit Note');
define('GST_DEBIT_NOTE', 'GST Debit Note');
define('MSL_DEFECTIVE_RETURN', 'MSL Defective Return');
define('MSL_NEW_PART_RETURN', 'MSL New Part Return');
define('OOW_NEW_PART_RETURN', 'OOW New Part Return');
define('SPARE_LOST_PART_RETURN', 'Spare Lost Part Return');
define('OUT_OF_WARRANTY', 'Out-of-Warranty');
define('IN_WARRANTY', 'In-Warranty');
define('CUSTOMER_PAYMENT', 'Customer Payment');
define('PAYTM_GATEWAY', 'paytm_gateway');
define('PRE_PAID_PAYMENT_GATEWAY', 'Pre-paid(PG)');
define('LIQUIDATION', 'Liquidation');
define('MICROWAREHOUSE', 'Micro Warehouse');
define('BUYBACK_VOUCHER','Buyback_Voucher');
define('PARTNER_VOUCHER','Partner_Voucher');
define('VENDOR_VOUCHER','Vendor_Voucher');
define('MSL','MSL');
define("QC_HSN_CODE", 998397);
define("HSN_CODE", "998715");
define("STAND_HSN_CODE", "8302");
define("COMMISION_CHARGE_HSN_CODE", "996111");
//define('ACCOUNTANT_EMAILID',"accounts@247around.com" );
define('ACCOUNTANT_EMAILID',"billing@247around.com" );
//Default Municipal limit for Paytm
define("DEFAULT_PAYTM_MUNICIPAL_LIMIT", 15);
// Default PAYTM UPCOUNTRY DISTRICT
define("DEFAULT_PAYTM_UPCOUNTRY_DISTRICT", "Others");
define('basic_percentage', 0.7);
define('addtitional_percentage', .85);
define('parts_percentage', .95);
define('PART_DELIVERY_PERCENTAGE', .10);
define('SERVICE_TAX_RATE', 0.18);
define('DEFAULT_TAX_RATE', 18);
define('DEFAULT_PARTS_TAX_RATE', 28);
define('DEFAULT_MOBILE_TAX_RATE', 12);
//Define constant for paid royalty to partner
define('ROYALTY', 'Royalty');
define('SALE', 'Sale');
define('BUYBACK_VERTICAL', 'Buyback');
define('REIMBURSEMENT', 'Reimbursement');
define('MSL_SECURITY_AMOUNT', 'MSL Security Amount');
define('DEFECTIVE_PART_LOST','Defective Part Lost');
define('PART_LOST_TAG','part_lost');

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
define('COURIER', 'Courier');
define('ACCESSORIES_TAG','accessories');
define('FNF', 'FNF');
//Default Charges Limit for Courier & Upcountry
define('DEFAULT_CHARGES_LIMIT', 10);
//OPENCELLInvoice 
define('OPENCELL_LEDBAR_CHARGES', 'Open Cell & LED Bar Charges');
//MSL courier large box packaging price
define('LARGE_MSL_BOX_PACKAGING_PRICE', '10');
//MSL courier small box packaging price
define('SMALL_MSL_BOX_PACKAGING_PRICE', '5');
//MSL Packaging charges invoice
define('MSL_PACKAGING_CHARGES', 'MSL Packaging Charges');
//large MSL box type
define('LARGE_MSL_BOX', '1');
//small MSL box type
define('SMALL_MSL_BOX', '2');
//message to display when invoice update time is expired
define('INVOICE_CANNOT_BE_UPDATED_AFTER_DEFINED_TIME', 'Cannot update invoice after 8th of next month.');
//debit penalty
define('DEBIT_PENALTY', 'Debit Penalty');
//Penalty Discount
define('PENALTY_DISCOUNT', 'Penalty Discount');
//days after which we will send negative FOC email to SF after last sent negative FOC email
define('NEGATIVE_FOC_MAIL_PERIOD', 30);
//annual charges for CRM
define('ANNUAL_CHARGES', 'Annual Charges');
//invoice dashboard array, values of constant array -> first field is value, second filed means we have to show row values in invoice table for that or leave that row blank
//1 means show row data, 0 means leave row blank. Third parameter in the array represents whether we want to highlight that cell or not
define("INVOICE_DASHBOARD_SERVICE", array(array("All Sales Nos. Are Without GST", 1, 1), array("Service Revenues", 0, 1), array("OEM / Brands", 1, 2), array("No. Of Billed Calls", 1, 0), array("Average Billing Per Call", 1, 0)
                                  , array("", 0, 0), array("OOW / SFs (Through Customers)", 1, 2), array("No. Of Billed Calls", 1, 0)
                                  , array("% Of Out Of Warranty Calls For Billed In Warranty", 1, 0), array("Average Billing Per Call", 1, 0)
                                  , array("", 0, 0), array("Total Calls", 1, 2),  array("Total Services Revenues", 1, 2)
                                  , array("", 0, 0), array(" Spares", 0, 1), array("OEM / Brands", 1, 2), array("Number of Calls - In Warranty", 1, 0), array("Number of Spares - In Warranty", 1, 0) ,array("Average Booking Price - In Warranty", 1, 0) 
                                  , array("", 0, 0), array("OOW / SFs", 1, 2), array("Number of Calls - OOW", 1, 0), array("Number of Spares - OOW", 1, 0) ,array("Average Booking Price - OOW", 1, 0)
                                  , array("", 0, 0), array("Buyback", 1, 1), array("Number of Orders", 1, 0), array("Average selling price per order", 1, 0)
                                  , array("", 0, 0), array("247ServiceBuddy", 0, 1), array("Number of Customers Paying Annual Fees", 1, 0), array("Old Customers", 1, 0), array("New Customers", 1, 0), array("Annual Fee Per Customer (Old)", 1, 0), array("Annual Fee Per Customer (New)", 1, 0) 
                                  , array("", 0, 0), array("Total CRM Saas Revenues", 1, 2)  ));

define('MSL_PACKAGING_LARGE_TAG', 'msl-packaging-variable-large-box');
define('MSL_PACKAGING_SMALL_TAG', 'msl-packaging-variable-small-box');
define('DEFECTIVE_PACKAGING_SMALL_TAG', 'defective-packaging-variable-small-box');
define('DEFECTIVE_PACKAGING_LARGE_TAG', 'defective-packaging-variable-large-box');
//MSL Debit Note
define('MSL_Debit_Note', 'MSL Debit Note');
//Cash Invoice Amount limit
define('CASH_INVOICE_LIMIT', 10);
//MSL Credit Note
define('MSL_Credit_Note', 'MSL Credit Note');
//Cash Invoice Amount limit
define('BULK_DEBIT_NOTE_TAG', 'BULK_DEBIT_NOTE_TAG');
?>
