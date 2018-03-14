<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Status and status msg constants
 */
define('SUCCESS_STATUS','SUCCESS');
define('FAILURE_STATUS','FAILURE');
define('ERR_INVALID_MERCHANT_GUID', 0000);
define('ERR_INVALID_MERCHANT_GUID_MSG', "Authentication Failed - 'Wrong MerchantGuid'");
define('SUCCESS_PAYTM_QR_RESPONSE', "QR_0001");
define('ALREADY_GENERATED_PAYTM_QR_RESPONSE', "QR_1020");
define('MID_NOT_AVAILABLE_MSG','Mid is not available in header, Authentication Failed');
/*
 * URL constatnts
 */
define('CASHBACK_URL','https://trust.paytm.in/wallet-web/refundWalletTxn');
define('CHECK_STATUS_URL','https://trust.paytm.in/wallet-web/checkStatus');
define('QR_CODE_URL','https://wallet.paytm.in/wallet-merchant/v2/createQRCode');
define("USER_DOWNLOAD_WEBSITE_URL", "http://247around.com/downloadQrCode/");

/*
 * Qr Code Constants
 */
define('QR_ALREADY_EXISTS_MSG','ALREADY EXISTS');
define('QR_CREATED_SUCCESSFULLY_MSG','QR GENERATED');
define('QR_CODE_REQUEST_TYPE','QR_ORDER');
define('QR_CODE_S3_FOLDER','qr-codes');
define('QR_CODE_DATABASE_ERROR','Not Saved In Db QR Generated');
define('QR_CODE_FAILURE','Error From Paytm');
define('PAYTM_PAYMENT_METHOD_FOR_QR','Paytm QR Code');
define('QR_CODE_VALIDITY', "30");
define("QR_CHANNEL_JOB_CARD", "JOBCARD");
define("QR_CHANNEL_USER", "USER");
define("QR_CHANNEL_APP", "APP");
define('QR_FAILURE_TO','chhavid@247around.com');
define('QR_FAILURE_CC','abhaya@247around.com');

/*
 * Cashback Constatnts
 */
define('CASHBACK_DATABASE_ERROR','Cashback transaction Not Saved In Db');
define("PAYTM_CASHBACK_TAG", "Paytm");
define('CASHBACK_TRANSACTION_NOT_FOUND_MSG',"Transaction ID does'nt exist in database");
define('CASHBACK_ALREADY_DONE_FOR_THIS_TRANSACTION_ID',"Cashback already Processes against this transaction ID");
define('CASHBACK_API_version','1.0');
define('REFUND_AMOUNT_GRETER_THEN_TRANSACTION_AMOUNT','Refund amount is greater then transaction amount');

/*
 * CheckStatus Contants
 */
define('CHECK_STATUS_SUCCESS_CODE','1');
define('CHECK_STATUS_INVALID_ORDER_ID','GE_1009');
define('CHECK_STATUS_SUCCESS','SUCCESS');
define('CHECK_STATUS_SUCCESS_MSG','Transaction has been updated in database successfully');
define('CHECK_STATUS_FAILURE','FAILURE');
define('CHECK_STATUS_FAILURE_MSG','Error From Paytm');
define('TRANSACTION_NOT_HAPPENS_YET','No Transaction');
define('TRANSACTION_NOT_HAPPENS_YET_MSG',"Transaction does'nt happen yet for this order_id");
define('TRANSACTION_RESPONSE_FROM_CHECK_STATUS','checkstatus');

/*
 * Callback Constants
 */
define('TRANSACTION_RESPONSE_FROM_CALLBACK','callback');
define('TRANSACTION_SUCCESS_TO','anuj@247around.com');
define('TRANSACTION_SUCCESS_CC','nits@247around.com');
define("DEFAULT_MERCHANT_CONTACT_NO", "8826423424");

