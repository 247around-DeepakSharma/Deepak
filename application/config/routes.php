<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
$route['vendor-extn'] = 'api/vendor_extn';
$route['get-vendor-phone'] = 'api/getVendorPhoneFromExtn';
$route['service_center'] = 'employee/service_centers';
$route['service_center/pending_booking'] = 'employee/service_centers/pending_booking';
$route['service_center/booking_details/(:any)'] = 'employee/service_centers/booking_details/$1';
$route['service_center/cancel_booking_form/(:any)'] = 'employee/service_centers/cancel_booking_form/$1';
$route['service_center/complete_booking_form/(:any)'] = 'employee/service_centers/complete_booking_form/$1';
$route['call-customer-status-callback'] = 'employee/booking/call_customer_status_callback';

//$route['api/(:any)'] = 'partner/$2';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
