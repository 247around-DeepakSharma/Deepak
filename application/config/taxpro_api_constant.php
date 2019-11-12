<?php 
//taxpayer api credentials
define('ASP_ID', '1606680918');
define('ASP_PASSWORD', 'priya@b30');
define('_247_AROUND_GSTIN', '07AAFCB1281J1ZQ');
define('USER_NAME_GSTIN', 'blackmelon.750');

//taxpro OTP request URL
define('TAXPRO_OTP_REQUEST_URL', 'https://api.taxprogsp.co.in/taxpayerapi/dec/v1.0/authenticate?action=OTPREQUEST&aspid='.ASP_ID.'&password='.ASP_PASSWORD.'&gstin='._247_AROUND_GSTIN.'&username='.USER_NAME_GSTIN);

//taxpro Auth Token request URL
define('TAXPRO_AUTH_TOKEN_REQUEST_URL', 'https://api.taxprogsp.co.in/taxpayerapi/dec/v1.0/authenticate?action=AUTHTOKEN&aspid='.ASP_ID.'&password='.ASP_PASSWORD.'&gstin='._247_AROUND_GSTIN.'&username='.USER_NAME_GSTIN.'&OTP=');

//taxpro GSTR2a data
define('TAXPRO__FEATCH_GSTR2A_URL', 'https://api.taxprogsp.co.in/taxpayerapi/dec/v0.3/returns/gstr2a?action=B2B&aspid='.ASP_ID.'&password='.ASP_PASSWORD.'&gstin='._247_AROUND_GSTIN.'&username='.USER_NAME_GSTIN.'&authtoken=');

//taxpayer api error codes
define('INVALID_GSTIN', 'GSP050D');
define('INVALID_GSTIN_MSG', 'You have entered invalid GST number');
define('INVALID_LENGHT_GSTIN', 'GSP001GA');
define('INVALID_LENGHT_GSTIN_MSG', 'Please enter valid 15 digit length for GST number');

?>