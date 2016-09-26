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
define('PARTNER_API_CALL',978978);
define('_247AROUND',247010);

/* End of file constants.php */
/* Location: ./application/config/constants.php */