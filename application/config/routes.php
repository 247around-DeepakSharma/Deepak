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
$route['pass-through-android-app'] = 'api/pass_through_android_app';
$route['pass-through-ac-service'] = 'api/pass_through_ac_service';
$route['service_center'] = 'employee/service_centers';
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
$route['service_center/update_booking_status/(:any)'] = 'employee/service_centers/update_booking_status/$1';
$route['service_center/process_update_booking'] = 'employee/service_centers/process_update_booking';
$route['service_center/acknowledge_delivered_spare_parts/(:any)'] = 'employee/service_centers/acknowledge_delivered_spare_parts/$1';
$route['service_center/get_search_form'] = 'employee/service_centers/get_search_form';
$route['service_center/search'] = 'employee/service_centers/search';
$route['service_center/get_defective_parts_booking'] = 'employee/service_centers/get_defective_parts_booking';
$route['service_center/get_defective_parts_booking/(:any)'] = 'employee/service_centers/get_defective_parts_booking/$1';
$route['service_center/update_defective_parts/(:any)'] = 'employee/service_centers/update_defective_parts/$1';
$route['service_center/process_update_defective_parts/(:any)'] = 'employee/service_centers/process_update_defective_parts/$1';
$route['service_center/get_approved_defective_parts_booking'] = 'employee/service_centers/get_approved_defective_parts_booking';
$route['service_center/get_approved_defective_parts_booking/(:any)'] = 'employee/service_centers/get_approved_defective_parts_booking/$1';
$route['service_center/pending_booking_upcountry_price/(:any)'] = 'employee/service_centers/pending_booking_upcountry_price/$1';


$route['call-customer-status-callback'] = 'employee/booking/call_customer_status_callback';

$route['partner/login'] = 'employee/partner';
$route['partner/home'] = 'employee/partner/partner_default_page';
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
$route['partner/print_all'] = 'employee/partner/print_all';
$route['partner/get_shipped_parts_list'] = 'employee/partner/get_shipped_parts_list';
$route['partner/get_shipped_parts_list/(:any)'] = 'employee/partner/get_shipped_parts_list/$1';
$route['partner/get_waiting_defective_parts'] = 'employee/partner/get_waiting_defective_parts';
$route['partner/get_waiting_defective_parts/(:any)'] = 'employee/partner/get_waiting_defective_parts/$1';
$route['partner/get_waiting_defective_parts/(:any)/(:any)'] = 'employee/partner/get_waiting_defective_parts/$1/$2';
$route['partner/acknowledge_received_defective_parts/(:any)'] = 'employee/partner/acknowledge_received_defective_parts/$1';
$route['partner/reject_defective_part/(:any)/(:any)'] = 'employee/partner/reject_defective_part/$1/$2';
$route['partner/get_approved_defective_parts_booking'] = 'employee/partner/get_approved_defective_parts_booking';
$route['partner/get_approved_defective_parts_booking/(:any)'] = 'employee/partner/get_approved_defective_parts_booking/$1';
$route['partner/get_waiting_for_approval_upcountry_charges'] = 'employee/partner/get_waiting_for_approval_upcountry_charges';
$route['partner/upcountry_charges_approval/(:any)/(:any)'] = 'employee/partner/upcountry_charges_approval/$1/$2';
$route['partner/reject_upcountry_charges/(:any)/(:any)'] = 'employee/partner/reject_upcountry_charges/$1/$2';
$route['partner/download_partner_summary/(:any)'] = 'BookingSummary/send_leads_summary_mail_to_partners/$1';

$route['pass-through-rating-missed-call'] = 'api/pass_through_rating_missed_call';
//$route['api/(:any)'] = 'partner/$2';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
