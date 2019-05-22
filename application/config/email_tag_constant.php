<?php

//invoice email tag
define('PARTNER_INVOICE_DETAILED_EMAIL_TAG','partner_invoice_detailed');

define('PARTNER_RECEIPT_VOUCHER_EMAIL_TAG','partner_receipt_voucher');

define('CASH_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG','cash_details_invoices_for_vendors');

define("BUYBACK_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG", "buyback_details_invoices_for_vendors");

define('FOC_DETAILS_INVOICE_FOR_VENDORS_EMAIL_TAG','foc_details_invoices_for_vendors');

define('NEGATIVE_FOC_INVOICE_FOR_VENDORS_EMAIL_TAG','negative_foc_invoice_for_vendors');

define('BRACKETS_INVOICE_EMAIL_TAG','send_brackets_invoice_mail');

define('DRAFT_BRACKETS_INVOICE_EMAIL_TAG','send_draft_brackets_invoice_mail');

define('CRM_SETUP_INVOICE_EMAIL_TAG','crm_setup_invoice');
define('CRM_SETUP_PROFORMA_INVOICE_EMAIL_TAG','crm_setup_proforma_invoice');

define('BRACKETS_CREDIT_NOTE_INVOICE_EMAIL_TAG','brackets_credit_note_invoice');

define('ADVANCE_RECEIPT_EMAIL_TAG','advance_receipt');

define('SWEETENER_INVOCIE_EMAIL_TAG','sweetener_invoice');

define('BOOKING_REPORT','booking_report');
define('NEW_SERVICE_CENTERS_REPORT','new_service_centers_report');
define('SERVICE_CENTERS_REPORT','service_centers_report');
define('SC_CRIME_REPORT','sc_crime_report');
define('SC_CRIME_REPORT_FOR_SF','sc_crime_report_for_sf');
define('UN_ASSIGNED_BOOKING','un_assigned_booking');
define('UN_ASSIGNED_BOOKING_TO_SF','un_assigned_booking_to_sf');
define('RM_CRIME_REPORT','rm_crime_report');
define('INCONSISTENT_DATA_TO_DEVELOPER','inconsistent_data_to_developer');
define('SEND_ERROR_FILE','send_error_file');
define('PINCODE_NOT_FOUND','pincode_not_found');
define('SF_NOT_FOUND','sf_not_found');
define('QUERY_UPDATE_FAILED_MISSED_CALL','query_update_failed_after_missed_call');
define('AC_MISSED_CALL','ac_missed_call');
define('SMS_SENDING_FAILED','sms_sending_failed');
define('ERROR_IN_CRONE','error_in_crone_execution');
define('ACL_BALANCE_CREDIT','acl_balance_credit');
define('JEEVES_BOOKING_STATUS_UPDATE','jeeves_booking_status_update');
define('ERROR_IN_CREATING_EMAIL_CONNECTION','error_in_creating_email_connection');
define('BUY_BACK_ORDER_TAG','buy_back_order');
define('BUY_BACK_ORDER_FAILURE','buy_back_order_failure');
define('BUY_BACK_PRICE_SHEET_FAILURE','buy_back_price_sheet_failure');
define('NEW_BRAND_ADDED_TAG','new_brand_added_tag');
define('SERVICE_CENTER_REMINDER_EMAIL','service_center_reminder_email');
define('PAYTM_FILE_UPLOADED','paytm_file_uploaded');
define('PAYTM_FILE_UPLOAD_FAILED','paytm_file_upload_failed');
define('BOOKING_INSERTION_FAILURE_BY_DEALER','booking_insertion_failure_by_dealer');
define('SNAPDEAL_FAILED_FILE_UPLOAD_SHIPPED','snapdeal_shipped_file_upload_failed');
define('SNAPDEAL_FAILED_FILE_UPLOAD_DELIVERED','snapdeal_delivered_file_upload_failed');
define('BOOKING_FILE_VALIDATION_PASS','booking_file_validation_pass');
define('SNAPDEAL_FAILED_FILE','snapdeal_file_upload_failed');
define('DELIVERED_FILE_UPLOADED','delivered_file_uploaded');
define('BOOKING_ID_NOT_UPDATED_FOR_UPLOADED_FILE','booking_id_not_Updated_for_uploaded_file');
define('ATTACHMENT_EXIST_FILE_NOT_FOUND_IN_SYSTEM','attachment_exist_file_not_found_in_system');
define('ATTACHMENT_NOT_FOUND','attachment_not_found');
define('BOOKING_INSERTION_FAILURE','booking_insertion_failure');
define('BOOKING_ESCALATION','booking_escalation');
define('PARTNER_APPROVAL_FAILED','partner_approval_failed');
define('UPCOUNTRY_BOOKING_CANCELLED','upcountry_booking_cancelled');
define('PARTNER_DETAILS_UPDATED','partner_details_updated');
define('COURIER_DETAILS','courier_invoice_sent');
define('GST_FORM_UPDATED','gst_form_updated');
define('SERVICE_PRICE_FILE_FAILED','service_price_file_failed');
define('APPLIANCE_NOT_FOUND','appliance_not_found');
define('FILE_VALIDATION_PASS','file_validation_pass');
define('PAYTM_MALL_FILE_FAILED','paytm_mall_file_failed');
define('FILE_UPLOADED','file_uploaded');
define('SF_ASSIGNED_ACTION_TABLE_NOT_UPDATED','sf_assigned_action_table_not_updated');
define('BROADCAST_EMAIL','broadcast_email');
define('PINCODE_CHANGES','pincode_changes');
define('EMAIL_TO_SPECIFIC_VENDOR','email_to_specific_vendor');
define('UPCOUNTRY_BOOKING_NOT_MARKED','upcountry_booking_not_marked');
define('ERROR_IN_CONVERTING_PDF','error_in_converting_pdf');
define('NO_DATA_FOUND_IN_STATUS_MAPPING_TABLE','no_data_found_in_status_mapping_table');
define('UPCOUNTRY_APPROVAL_TAG','upcountry_approval_tag');
define('BOOKING_CANCELLED_NO_UPCOUNTRY_APPROVAL','booking_cancelled_no_upcountry_approval');
define('UPCOUNTRY_DISTANCE_CAN_NOT_CALCULATE_EMAIL_TAG','upcountry_distance_can_not_calculate');
define('STAG_01_DOWN','stag_01_down');
define('JOB_CARD_NOT_GENERATED','job_card_not_generated');
define('QR_NOT_GENERATED','qr_not_generated');
define('NEW_TRANSACTION_FROM_PAYTM','new_transaction_from_paytm');
define('PAYTM_CASHBACK_PROCESSED','paytm_cashback_processed');
define('PUSH_NOTIFICATION_ERROR','push_notification_error');
define('NEW_PARTNER_ADDED_EMAIL_TAG','new_partner_added');
define('VENDOR_UPDATED','vendor_updated');
define('SEND_LOG_FILE','log_file');
define('INFORM_PARTNER_FOR_NEW_SERIAL_NUMBER','inform_partner_for_serial_no');
define('INVENTORY_INVOICE', 'spare_inventory_invoice');
define('SF_WAREHOUSE_INVOICE_TAG', 'sf_warehouse_invoice');
define('INTERNAL_CONVERSATION_EMAIL', 'internal_conversation_email');
define('MISC_CHARGES_DETAILS_ON_EMAIL', 'booking_misc_charges_details');
define('CP_OUTSTANDING_AMOUNT', 'cp_out_standing_email');
define('COURIER_DOCUMENT', 'courier_documents');
define('TAXPRO_API_FAIL', 'taxpro_api_fail');
define('GST_DETAIL_UPDATED', 'gst_detail_change');
define('BAD_RATING', 'we_get_bad_rating');
define("POSTPAID_PARTNER_ABOVE_DUE_DATE_INVOICE_NOTIFICATION","postpaid_above_due_date_invoice_notification");
define("POSTPAID_PARTNER_WITH_IN_DUE_DATE_INVOICE_NOTIFICATIOIN", "postpaid_with_in_due_date_invoice_notification");
define("MISSED_UPCOUNTRY_BOOKING", "missed_upcountry_booking");
define("CN_AGAINST_GST_DN", "credit_note_against_gst_debit_note");
define("VENDOR_GST_RETURN_WARNING", "vendor_gst_return");
define('SPARE_INVOICE_EMAIL_TAG','spare_invoice_sent');
define('QWIKCILVER_TRANSACTION_DETAIL','qwikcilver_transaction_detail');
define('VALIDITY_EXPIRY_WARNING_FOR_PARTNER', 'validity_expiry_warning_for_partner');
define('WRONG_CALL_AREA_TEMPLATE', 'wrong_call_area');
define('WRONG_PINCODE_TEMPLATE', 'wrong_pincode_enter');
define('MINIMUM_GUARANTEE_MAIL_TEMPLATE', 'minimum_guarantee_mail_template');
define('DEFECTIVE_SPARE_SALE_INVOICE', 'defective_spare_sale_invoice');
define('DEFECTIVE_SPARE_SOLED_NOTIFICATION', 'defective_spare_sold_notification');
define('CREDIT_NOTE_ON_REFUSE_TO_PAY', 'cn_on_refuse_to_pay');
define('DEBIT_NOTE_ON_REFUSE_TO_PAY', 'dn_on_refuse_to_pay');
define('MSL_SEND_BY_WH_TO_PARTNER','msl_send_by_wh_to_partner');
define('MSL_SEND_BY_MICROWH_TO_PARTNER','msl_send_by_microwh_to_partner');
define('NEW_PARTNER_ONBOARD_NOTIFICATION', 'new_partner_onboard_notification');
define('PENALTY_SUMMARY', 'penalty_summary');
define("IFSC_CODE_VALIDATION_API_FAIL", "razorpay_ifsc_code_api_fail");
define('ALL_INVOICE_SUCCESS_MESSAGE', 'invoice_success_message');
define("BUYBACK_REIMBURESE_PO_UPLOADED", "buyback_reimburese_po_uploaded");
define("SF_ADDITION_MAIL_TO_BRAND", "sf_addition_mail_to_brand");
define('BUYBACK_DISPUTED_ORDERS_SUMMARY','buyback_disputed_orders_summary');
define('SEND_MSL_FILE','send_msl_file');
define('CHANGE_PASSWORD','change_password');