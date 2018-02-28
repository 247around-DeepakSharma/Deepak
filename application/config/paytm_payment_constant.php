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
define('CASHBACK_DATABASE_ERROR','Cashback transaction Not Saved In Db');
define('QR_CODE_FAILURE','Error From Paytm');
define('AMOUNT_ZERO_ERROR','Amount should not be 0');
define('MERCHANT_CONTACT','8826186751');
define('PAYTM_PAYMENT_METHOD_FOR_QR','Paytm QR Code');
define("PAYTM_CASHBACK_TAG", "Paytm");
define('CASHBACK_TRANSACTION_NOT_FOUND_MSG',"Transaction ID does'nt exist in database");
define('CASHBACK_API_version','1.0');
define('CASHBACK_URL','https://trust-uat.paytm.in/wallet-web/refundWalletTxn');
define('ERR_INVALID_MERCHANT_GUID', 0000);
define('ERR_INVALID_MERCHANT_GUID_MSG', "Authentication Failed - 'Wrong MerchantGuid'");
define('QR_CODE_VALIDITY', "30");
define('SUCCESS_PAYTM_QR_RESPONSE', "QR_0001");
define('ALREADY_GENERATED_PAYTM_QR_RESPONSE', "QR_1020");
define('CHECK_STATUS_URL','https://trust-uat.paytm.in/wallet-web/checkStatus');
define('CHECK_STATUS_SUCCESS_CODE','1');
define('CHECK_STATUS_INVALID_ORDER_ID','GE_1009');
define('MID_NOT_AVAILABLE_MSG','Mid is not available in header, Authentication Failed');
define('CHECK_STATUS_SUCCESS','SUCCESS');
define('CHECK_STATUS_SUCCESS_MSG','Transaction has been updated in database successfully');
define('TRANSACTION_NOT_HAPPENS_YET','No Transaction');
define('TRANSACTION_NOT_HAPPENS_YET_MSG',"Transaction does'nt happen yet for this order_id");
define('CHECK_STATUS_FAILURE','FAILURE');
define('CHECK_STATUS_FAILURE_MSG','Error From Paytm');
define("QR_CHANNEL_JOB_CARD", "JOBCARD");
define("QR_CHANNEL_USER", "USER");
define("USER_DOWNLOAD_WEBSITE_URL", "http://247around.com/downloadQrCode/");
define('REFUND_AMOUNT_GRETER_THEN_TRANSACTION_AMOUNT','Refund amount is greater then transaction amount');

