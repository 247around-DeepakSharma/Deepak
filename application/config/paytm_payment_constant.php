<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('PAYTM_MERCHANT_KEY', 'bD_idSGGkQwb8dV1'); 
define('SUCCESS_STATUS','SUCCESS');
define('FAILURE_STATUS','FAILURE');
define('QR_ALREADY_EXISTS_MSG','ALREADY EXISTS');
define('QR_CREATED_SUCCESSFULLY_MSG','QR GENERATED');
define('QR_CODE_REQUEST_TYPE','QR_ORDER');
define('MERCHANT_GUID','dafba0a8-97da-4636-92d6-0e3e13c7bbb0');
define('MID','BATCPL33992620706070');
define('QR_CODE_URL','https://trust-uat.paytm.in/wallet-merchant/v2/createQRCode');
define('QR_CODE_S3_FOLDER','qr-codes');
define('QR_CODE_DATABASE_ERROR','Not Saved In Db QR Generated');
define('QR_CODE_FAILURE','Error From Paytm');
define('AMOUNT_ZERO_ERROR','Amount should not be 0');
define('MERCHANT_CONTACT','8826186751');
define('PAYTM_PAYMENT_METHOD_FOR_QR','Paytm QR Code');
define("PAYTM_CASHBACK_TAG", "Paytm");

