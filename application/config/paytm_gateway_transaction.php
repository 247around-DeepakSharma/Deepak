<?php  
    if ( ! defined('BASEPATH')) {
        exit('No direct script access allowed');
    } 
    
/*

- Use PAYTM_ENVIRONMENT as 'PROD' if you wanted to do transaction in production environment else 'TEST' for doing transaction in testing environment.
- Change the value of PAYTM_MERCHANT_KEY constant with details received from Paytm.
- Change the value of PAYTM_MERCHANT_MID constant with details received from Paytm.
- Change the value of PAYTM_MERCHANT_WEBSITE constant with details received from Paytm.
- Above details will be different for testing and production environment.

*/

if(ENVIRONMENT == 'production'){
	define('PAYTM_GATEWAY_ENVIRONMENT', 'PROD'); // PROD
	define('PAYTM_GATEWAY_MERCHANT_KEY', 'O83YdBrDWmjCrjtG'); //Change this constant's value with Merchant key downloaded from portal
	define('PAYTM_GATEWAY_MERCHANT_MID', '247Aro31364376608092'); //Change this constant's value with MID (Merchant ID) received from Paytm
	define('PAYTM_GATEWAY_MERCHANT_WEBSITE', 'WEBPROD'); //Change this constant's value with Website name received from Paytm
	define('PAYTM_GATEWAY_INDUSTRY_TYPE_ID','Retail109');
	define('PAYTM_GATEWAY_CHANNEL_ID', 'WEB');
}else{
	define('PAYTM_GATEWAY_ENVIRONMENT', 'TEST'); // PROD
	define('PAYTM_GATEWAY_MERCHANT_KEY', '@m8pOe%7LSZw63y2'); //Change this constant's value with Merchant key downloaded from portal
	define('PAYTM_GATEWAY_MERCHANT_MID', '247Aro50898004256928'); //Change this constant's value with MID (Merchant ID) received from Paytm
	define('PAYTM_GATEWAY_MERCHANT_WEBSITE', 'WEBSTAGING'); //Change this constant's value with Website name received from Paytm
	define('PAYTM_GATEWAY_INDUSTRY_TYPE_ID','Retail');
	define('PAYTM_GATEWAY_CHANNEL_ID', 'WEB');
}


/*$PAYTM_DOMAIN = "pguat.paytm.com";
if (PAYTM_ENVIRONMENT == 'PROD') {
	$PAYTM_DOMAIN = 'secure.paytm.in';
}

define('PAYTM_REFUND_URL', 'https://'.$PAYTM_DOMAIN.'/oltp/HANDLER_INTERNAL/REFUND');
define('PAYTM_STATUS_QUERY_URL', 'https://'.$PAYTM_DOMAIN.'/oltp/HANDLER_INTERNAL/TXNSTATUS');
define('PAYTM_STATUS_QUERY_NEW_URL', 'https://'.$PAYTM_DOMAIN.'/oltp/HANDLER_INTERNAL/getTxnStatus');
define('PAYTM_TXN_URL', 'https://'.$PAYTM_DOMAIN.'/oltp-web/processTransaction');*/

//$PAYTM_STATUS_QUERY_NEW_URL='https://securegw-stage.paytm.in/merchant-status/getTxnStatus';


if(ENVIRONMENT == 'production'){
    $PAYTM_GATEWAY_STATUS_QUERY_NEW_URL='https://securegw.paytm.in/merchant-status/getTxnStatus';
    $PAYTM_GATEWAY_TXN_URL='https://securegw.paytm.in/theia/processTransaction';
    $GATEWAY_CALLBACK_URL = 'https://aroundhomzapp.com/payment/response';
}else{
    $PAYTM_GATEWAY_STATUS_QUERY_NEW_URL = 'https://pguat.paytm.com/oltp/HANDLER_INTERNAL/getTxnStatus';
    $PAYTM_GATEWAY_TXN_URL='https://pguat.paytm.com/oltp-web/processTransaction';
    $GATEWAY_CALLBACK_URL = 'http://247newfeaturs.in/payment/response';
    
}

define('PAYTM_GATEWAY_REFUND_URL', '');
define('PAYTM_GATEWAY_STATUS_QUERY_URL', $PAYTM_GATEWAY_STATUS_QUERY_NEW_URL);
define('PAYTM_GATEWAY_STATUS_QUERY_NEW_URL', $PAYTM_GATEWAY_STATUS_QUERY_NEW_URL);
define('PAYTM_GATEWAY_TXN_URL', $PAYTM_GATEWAY_TXN_URL);
//url to send user this page after transaction
define('PAYTM_GATEWAY_CALLBACK_URL',$GATEWAY_CALLBACK_URL);
?>
