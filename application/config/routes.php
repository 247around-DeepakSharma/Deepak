<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	http://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There area two reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router what URI segments to use if those provided
  | in the URL cannot be matched to a valid route.
  |
 */

$route['default_controller'] = "form";
$route['404_override'] = '';
$route['pass-through'] = 'api/pass_through';
$route['incoming-pass-through'] = 'telephony/pass_through';
$route['pass-through-android-app'] = 'api/pass_through_android_app';
$route['pass-through-ac-service'] = 'api/pass_through_ac_service';
$route['service_center'] = 'employee/service_centers';
$route['service_center/login'] = 'employee/service_centers';
$route['service_center/pending_booking'] = 'employee/service_centers/pending_booking';
$route['service_center/pending_booking/(:any)'] = 'employee/service_centers/pending_booking/$1';
$route['service_center/completed_booking'] = 'employee/service_centers/completed_booking';
$route['service_center/completed_booking/(:any)'] = 'employee/service_centers/completed_booking/$1';
$route['service_center/cancelled_booking'] = 'employee/service_centers/cancelled_booking';
$route['service_center/cancelled_booking/(:any)'] = 'employee/service_centers/cancelled_booking/$1';
$route['service_center/booking_details/(:any)'] = 'employee/service_centers/booking_details/$1';
$route['service_center/cancel_booking_form/(:any)'] = 'employee/service_centers/cancel_booking_form/$1';
$route['service_center/complete_booking_form/(:any)'] = 'employee/service_centers/complete_booking_form/$1';
$route['service_center/add_engineer'] = 'employee/vendor/add_engineer';
$route['service_center/get_engineers'] = 'employee/vendor/get_engineers';
$route['service_center/invoices_details'] = 'employee/service_centers/invoices_details';
$route['service_center/bank_transactions'] = 'employee/service_centers/bank_transactions';
$route['service_center/update_booking_status/(:any)'] = 'employee/service_centers/update_booking_status/$1';
$route['service_center/update_booking_spare_parts_required/(:any)'] = 'employee/service_centers/update_booking_spare_parts_required/$1';
$route['service_center/process_update_booking'] = 'employee/service_centers/process_update_booking';
$route['service_center/acknowledge_delivered_spare_parts/(:any)/(:any)/(:any)/(:any)'] = 'employee/service_centers/acknowledge_delivered_spare_parts/$1/$2/$3/$4';
$route['service_center/get_search_form'] = 'employee/service_centers/get_search_form';
$route['service_center/search'] = 'employee/service_centers/search';
$route['service_center/get_defective_parts_booking'] = 'employee/service_centers/get_defective_parts_booking';
$route['service_center/get_defective_parts_booking/(:any)'] = 'employee/service_centers/get_defective_parts_booking/$1';
$route['service_center/update_defective_parts/(:any)'] = 'employee/service_centers/update_defective_parts/$1';
$route['service_center/process_update_defective_parts/(:any)'] = 'employee/service_centers/process_update_defective_parts/$1';
$route['service_center/get_approved_defective_parts_booking'] = 'employee/service_centers/get_approved_defective_parts_booking';
$route['service_center/get_approved_defective_parts_booking/(:any)'] = 'employee/service_centers/get_approved_defective_parts_booking/$1';

$route['service_center/defective_part_shipped_by_sf'] = 'employee/service_centers/defective_part_shipped_by_sf';

$route['service_center/pending_booking_upcountry_price/(:any)'] = 'employee/service_centers/pending_booking_upcountry_price/$1';
$route['service_center/gst_details'] = 'employee/service_centers/gst_update_form/';
$route['service_center/gst_update_form'] = 'employee/service_centers/gst_update_form/';
$route['service_center/process_gst_update'] = 'employee/service_centers/process_gst_update/';
$route['service_center/review'] = 'employee/engineer/review_engineer_action_form';
$route['service_center/search_docket_number'] = 'employee/service_centers/search_docket_number';

$route['service_center/customer_invoice_details'] = 'employee/service_centers/customer_invoice_details';

$route['service_center/buyback/bb_order_details'] = 'employee/service_centers/view_delivered_bb_order_details';
$route['service_center/buyback/update_order_details/(:any)/(:any)/(:any)/(:any)'] = 'employee/service_centers/update_bb_report_issue_order_details/$1/$2/$3/$4/$5';
$route['process_report_issue_bb_order_details'] = 'employee/service_centers/process_report_issue_bb_order_details';
$route['service_center/buyback/update_received_bb_order/(:any)/(:any)/(:any)'] = 'employee/service_centers/update_received_bb_order/$1/$2/$3/$4';
$route['service_center/buyback/update_not_received_bb_order/(:any)/(:any)/(:any)'] = 'employee/service_centers/update_not_received_bb_order/$1/$2/$3/$4';
$route['service_center/buyback/show_bb_price_list'] = 'employee/service_centers/show_bb_price_list';
$route['service_center/buyback/view_bb_order_details/(:any)'] = 'employee/service_centers/view_bb_order_details/$1';
$route['service_center/buyback/get_bb_order_details_data/(:any)'] = 'employee/service_centers/get_bb_order_details_data/$1';
$route['service_center/buyback/get_bb_order_history_details/(:any)'] = 'employee/service_centers/get_bb_order_history_details/$1';
$route['service_center/buyback/get_bb_order_appliance_details/(:any)'] = 'employee/service_centers/get_bb_order_appliance_details/$1';

$route['service_center/inventory'] = 'employee/service_centers/warehouse_default_page';
$route['service_center/bulkConversion'] = 'employee/spare_parts/bulkConversion';
$route['service_center/bulkConversion_process'] = 'employee/service_centers/bulkConversion_process';


$route['service_center/inventory/inventory_list'] = 'employee/service_centers/inventory_stock_list';
$route['service_center/inventory/alternate_inventory_list/(:any)'] = 'employee/service_centers/alternate_inventory_stock_list/$1/$2/$3';
$route['service_center/inventory/alternate_parts_inventory_list'] = 'employee/service_centers/alternate_parts_inventory_list';
$route['service_center/inventory/inventory_list_by_model/(:any)'] = 'employee/service_centers/get_inventory_by_model/$1';
$route['service_center/inventory/inventory_list_by_model/(:any)'] = 'employee/service_centers/get_inventory_by_model/$1/$2';




$route['service_center/spare_parts'] = 'employee/service_centers/get_spare_parts_booking';
$route['service_center/spare_parts/(:any)'] = 'employee/service_centers/get_spare_parts_booking/$1';
$route['service_center/defective_spare_parts'] = 'employee/service_centers/get_defective_parts_shipped_by_sf';
$route['service_center/defective_spare_parts/(:any)'] = 'employee/service_centers/get_defective_parts_shipped_by_sf/$1';
$route['service_center/update_spare_parts_form/(:any)'] = 'employee/service_centers/update_spare_parts_form/$1';
$route['service_center/process_update_spare_parts/(:any)'] = 'employee/service_centers/process_update_spare_parts/$1';
$route['service_center/reject_defective_part/(:any)/(:any)'] = 'employee/service_centers/reject_defective_part/$1/$2';
$route['service_center/get_shipped_parts_list'] = 'employee/service_centers/get_shipped_parts_list_by_warehouse';
$route['service_center/get_shipped_parts_list/(:any)'] = 'employee/service_centers/get_shipped_parts_list_by_warehouse/$1';
$route['service_center/print_all'] = 'employee/service_centers/print_all';
$route['service_center/acknowledge_received_defective_parts/(:any)/(:any)'] = 'employee/service_centers/acknowledge_received_defective_parts/$1/$2';
$route['service_center/approved_defective_parts_booking_by_warehouse'] = 'employee/service_centers/get_approved_defective_parts_booking_by_warehouse';
$route['service_center/approved_defective_parts_booking_by_warehouse/(:any)'] = 'employee/service_centers/get_approved_defective_parts_booking_by_warehouse/$1';
$route['service_center/download_sf_declaration/(:any)'] = 'employee/service_centers/download_sf_declaration/$1'; 
$route['service_center/acknowledge_spares_send_by_partner'] = 'employee/service_centers/acknowledge_spares_send_by_partner';
$route['service_center/acknowledge_spares_send_by_vendor'] = 'employee/service_centers/acknowledge_spares_send_by_vendor';
$route['service_center/dashboard'] = 'employee/service_centers/sf_dashboard';

    

$route['service_center/inventory/appliance_model_list'] = 'employee/service_centers/appliance_model_list';
$route['service_center/booking_spare_list'] = 'employee/service_centers/booking_spare_list';
$route['service_center/spare_transfer'] = 'employee/service_centers/spare_transfer';
$route['service_center/do_spare_transfer'] = 'employee/service_centers/do_spare_transfer';
//$route['service_center/inventory/inventory_list'] = 'employee/service_centers/inventory_stock_list';






$route['call-customer-status-callback'] = 'employee/booking/call_customer_status_callback';

$route['partner/login'] = 'employee/partner';
$route['partner/spare_shipped_history'] = 'employee/partner/spare_shipped_history';
$route['partner/home'] = 'employee/partner/partner_default_page';
$route['partner/dashboard'] = 'employee/partner/partner_dashboard';
$route['partner/search'] = 'employee/partner/search';
$route['partner/pending_booking'] = 'employee/partner/pending_booking';
$route['partner/pending_booking/(:any)'] = 'employee/partner/pending_booking/$1';
$route['partner/closed_booking/Completed'] = 'employee/partner/closed_booking/Completed';
$route['partner/closed_booking/Completed/(:any)'] = 'employee/partner/closed_booking/Completed/$1';
$route['partner/closed_booking/Cancelled'] = 'employee/partner/closed_booking/Cancelled';
$route['partner/closed_booking/Cancelled/(:any)'] = 'employee/partner/closed_booking/Cancelled/$1';
$route['partner/booking_details/(:any)'] = 'employee/partner/booking_details/$1';
$route['partner/pending_queries'] = 'employee/partner/pending_queries';
$route['partner/pending_queries/(:any)'] = 'employee/partner/pending_queries/$1';
$route['partner/booking_form'] = 'employee/partner/get_addbooking_form';
$route['partner/booking_form/(:any)'] = 'employee/partner/get_addbooking_form/$1';
$route['partner/invoices_details'] = 'employee/partner/invoices_details';
$route['partner/get_user_form'] = 'employee/partner/get_user_form';
$route['partner/invoices_details'] = 'employee/partner/invoices_details';
$route['partner/get_cancel_form/(:any)/(:any)'] = 'employee/partner/get_cancel_form/$1/$2';
$route['partner/get_reschedule_booking_form/(:any)'] = 'employee/partner/get_reschedule_booking_form/$1';
$route['partner/process_reschedule_booking/(:any)'] = 'employee/partner/process_reschedule_booking/$1';
$route['partner/escalation_form/(:any)'] = 'employee/partner/escalation_form/$1';
$route['partner/process_escalation/(:any)'] = 'employee/partner/process_escalation/$1';
$route['partner/update_booking/(:any)'] = 'employee/partner/get_editbooking_form/$1';
$route['partner/process_update_booking/(:any)'] = 'employee/partner/process_editbooking/$1';
$route['partner/get_spare_parts_booking'] = 'employee/partner/get_spare_parts_booking';
$route['partner/get_spare_parts_booking/(:any)'] = 'employee/partner/get_spare_parts_booking/$1';
$route['partner/process_update_spare_parts/(:any)'] = 'employee/partner/process_update_spare_parts/$1';
$route['partner/update_spare_parts_form/(:any)'] = 'employee/partner/update_spare_parts_form/$1';
$route['partner/download_spare_parts'] = 'employee/partner/download_spare_parts';
$route['partner/download_sc_address/(:any)'] = 'employee/partner/download_sc_address/$1';
$route['partner/download_courier_manifest/(:any)'] = 'employee/partner/download_courier_manifest/$1';
$route['partner/get_booking_life_cycle/(:any)'] = 'employee/partner/get_booking_life_cycle/$1';
$route['partner/get_comment_section/(:any)'] = 'employee/partner/get_comment_section/$1/$2';
$route['partner/print_all'] = 'employee/partner/print_all';
$route['partner/get_shipped_parts_list'] = 'employee/partner/get_shipped_parts_list';
$route['partner/get_shipped_parts_list/(:any)'] = 'employee/partner/get_shipped_parts_list/$1';
$route['partner/get_waiting_defective_parts'] = 'employee/partner/get_waiting_defective_parts';
$route['partner/get_waiting_defective_parts/(:any)'] = 'employee/partner/get_waiting_defective_parts/$1';
$route['partner/get_waiting_defective_parts/(:any)/(:any)'] = 'employee/partner/get_waiting_defective_parts/$1/$2';
$route['partner/get_pending_part_on_sf'] = 'employee/partner/get_pending_part_on_sf';
$route['partner/get_pending_part_on_sf/(:any)'] = 'employee/partner/get_pending_part_on_sf/$1';
$route['partner/get_pending_part_on_sf/(:any)/(:any)'] = 'employee/partner/get_pending_part_on_sf/$1/$2';
$route['partner/acknowledge_received_defective_parts/(:any)/(:any)'] = 'employee/partner/acknowledge_received_defective_parts/$1/$2';
$route['partner/reject_defective_part/(:any)/(:any)'] = 'employee/partner/reject_defective_part/$1/$2';
$route['partner/reject_defective_part_sent_by_wh/(:any)/(:any)'] = 'employee/partner/reject_defective_part_sent_by_wh/$1/$2';
$route['partner/get_approved_defective_parts_booking'] = 'employee/partner/get_approved_defective_parts_booking';
$route['partner/get_approved_defective_parts_booking/(:any)'] = 'employee/partner/get_approved_defective_parts_booking/$1';
$route['partner/get_waiting_for_approval_upcountry_charges'] = 'employee/partner/get_waiting_for_approval_upcountry_charges';
$route['partner/get_waiting_for_approval_upcountry_charges/(:any)'] = 'employee/partner/get_waiting_for_approval_upcountry_charges/$1';
$route['partner/upcountry_charges_approval/(:any)/(:any)'] = 'employee/partner/upcountry_charges_approval/$1/$2';
$route['partner/reject_upcountry_charges/(:any)/(:any)'] = 'employee/partner/reject_upcountry_charges/$1/$2';
$route['partner/download_partner_summary/(:any)'] = 'BookingSummary/send_leads_summary_mail_to_partners/$1';
$route['partner/download_sf_list_excel'] = 'employee/partner/download_sf_list_excel';
$route['partner/serviceability_list'] = 'employee/partner/get_serviceability_by_pincode';
$route['partner/banktransaction'] = 'employee/partner/get_bank_transaction';
$route['partner/download_sf_declaration/(:any)'] = 'employee/partner/download_sf_declaration/$1';
$route['partner/inventory/inventory_list'] = 'employee/partner/inventory_stock_list';
$route['partner/inventory/alternate_parts_list'] = 'employee/partner/alternate_parts_list';
$route['partner/inventory/alternate_inventory_list/(:any)'] = 'employee/partner/alternate_inventory_stock_list/$1/$2/$3';
$route['partner/reports'] = 'employee/partner/get_reports';
$route['partner/contracts'] = 'employee/partner/get_contracts';
$route['partner/contact_us'] = 'employee/partner/get_contact_us_page';
$route['partner/upcountry_report'] = 'employee/partner/download_upcountry_report';
$route['partner/download_waiting_defective_parts'] = 'employee/partner/download_waiting_defective_parts';
$route['partner/download_waiting_upcountry_bookings'] = 'employee/partner/download_waiting_upcountry_bookings';
$route['partner/download_spare_part_shipped_by_partner'] = 'employee/partner/download_spare_part_shipped_by_partner';
$route['partner/download_sf_needs_to_send_parts'] = 'employee/partner/download_sf_needs_to_send_parts';
$route['partner/download_received_spare_by_partner'] = 'employee/partner/download_received_spare_by_partner';
$route['partner/inventory/ack_spare_send_by_wh'] = 'employee/partner/ack_spare_send_by_wh';
$route['partner/inventory/show_inventory_details'] = 'employee/partner/show_inventory_master_details';
$route['partner/inventory/show_inventory_appliance_details'] = 'employee/partner/show_appliance_model_list';
$route['partner/inventory/tag_spare_invoice'] = 'employee/partner/tag_spare_invoice';
$route['partner/search_docket_number'] = 'employee/partner/search_docket_number';
$route['partner/review_bookings/(:any)/(:any)'] = 'employee/partner/partner_review_bookings/$1/$2';
$route['partner/contacts'] = 'partner/manage_partner_contacts';
$route['partner/inventory/model_mapping'] = 'employee/partner/show_appliance_model_mapping';
$route['partner/brand_collateral']='employee/partner/brandCollateral';





//$route['service_center/inventory/appliance_model_list'] = 'employee/inventory/appliance_model_list';


// $route['inventory/appliance_model_list']='employee/inventory/appliance_model_list';





$route['pass-through-rating-missed-call'] = 'api/pass_through_rating_missed_call';
$route['pass-through-fake-reschedule-call'] = 'api/pass_through_fake_reschedule_call';
$route['partner/invoice'] = 'employee/partner/inactive_partner_default_page';

$route['dealers'] = 'employee/login/dealer_login_form';
$route['dealer/login'] = 'employee/login/dealer_login_form';
$route['dealers/login'] = 'employee/login/dealer_login_process';
$route['dealers/add_booking'] = 'employee/dealers/add_booking';
$route['dealers/process_addbooking'] = 'employee/dealers/process_addbooking';
$route['login/dealer_logout'] = 'employee/login/dealer_logout';

$route['payment/details'] = 'employee/partner/payment_details';
$route['payment/checkout'] = 'paytm_gateway/process_paytm_transaction';
$route['payment/response'] = 'paytm_gateway/paytm_response';
$route['payment/confirmation'] = 'paytm_gateway/show_payment_confirmation';
$route['payment/verify_booking_payment/(:any)'] = 'paytm_gateway/process_gateway_booking_payment/$1';


$route['upload_file'] = 'file_upload/process_upload_file';
$route['upload_inventory_details_file'] = 'employee/inventory/upload_inventory_details_file';

$route['check_booking_id_exists/(:any)'] = 'employee/inventory/check_booking_id_exists/$1';
$route['check_invoice_id_exists/(:any)'] = 'employee/inventory/check_invoice_id_exists/$1';

$route['service_center/inventory/requested_spare_on_sf'] = 'employee/service_centers/requested_spare_on_sf';
$route['service_center/requested_spare_on_sf/(:any)'] = 'employee/service_centers/get_spare_requested_spare_on_sf/$1';
$route['service_center/send_to_partner_list'] = 'employee/service_centers/warehouse_task_list_tab_send_to_partner';
$route['upload_alternate_spare_parts_file'] = 'employee/spare_parts/upload_alternate_spare_parts_file';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
