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
/*
 * URL constatnts
 */
define('CASHBACK_URL','https://trust.paytm.in/wallet-web/refundWalletTxn');
define('CHECK_STATUS_URL','https://trust.paytm.in/wallet-web/checkStatus');
define('QR_CODE_URL','https://wallet.paytm.in/wallet-merchant/v2/createQRCode');

/*
 * Qr Code Constants
 */
define('QR_ALREADY_EXISTS_MSG','ALREADY EXISTS');
define('QR_CREATED_SUCCESSFULLY_MSG','QR GENERATED');
define('QR_CODE_REQUEST_TYPE','QR_ORDER');
define('QR_CODE_S3_FOLDER','qr-codes');
define('QR_CODE_DATABASE_ERROR','Not Saved In Database QR Generated');
define('QR_CODE_FAILURE','Error From Paytm');
define('PAYTM_PAYMENT_METHOD_FOR_QR','Paytm QR Code');
define('QR_CODE_VALIDITY', "30");
define("QR_CHANNEL_JOB_CARD", "JOBCARD");
define("QR_CHANNEL_SMS", "SMS");
define("QR_CHANNEL_APP", "APP");
define('QR_FAILURE_TO','247around_dev@247around.com');
define('QR_FAILURE_CC','247around_dev@247around.com');

/*
 * Cashback Constatnts
 */
define('CASHBACK_DATABASE_ERROR','Cashback transaction Not Saved In Db');
define("PAYTM_CASHBACK_TAG", "Paytm");
define('CASHBACK_TRANSACTION_NOT_FOUND_MSG',"Transaction ID doesn't exist in database");
define('CASHBACK_ALREADY_DONE_FOR_THIS_TRANSACTION_ID',"Cashback already Processes against this transaction ID");
define('CASHBACK_API_version','1.0');
define('REFUND_AMOUNT_GRETER_THEN_TRANSACTION_AMOUNT','Refund amount is greater then transaction amount');
define('CASHBACK_FORM','cashback form');
define('CASHBACK_CRONE','cashback Auto crone');

/*
 * CheckStatus Contants
 */
define('CHECK_STATUS_SUCCESS_CODE','1');
define('CHECK_STATUS_INVALID_ORDER_ID','GE_1009');
define('MID_NOT_AVAILABLE_MSG','MID is not available in header, Authentication Failed');
define('CHECK_STATUS_SUCCESS','SUCCESS');
define('CHECK_STATUS_SUCCESS_MSG','Transaction has been updated in database successfully');
define('TRANSACTION_NOT_HAPPENS_YET','No Transaction');
define('TRANSACTION_NOT_HAPPENS_YET_MSG',"Transaction doesn't happen yet for this order_id");

define('CHECK_STATUS_FAILURE','FAILURE');
define('CHECK_STATUS_FAILURE_MSG','Error From Paytm');
define('TRANSACTION_RESPONSE_FROM_CHECK_STATUS','checkstatus');

/*
 * Callback Constants
 */
define('TRANSACTION_RESPONSE_FROM_CALLBACK','callback');
define('TRANSACTION_SUCCESS_TO','anuj@247around.com');
define('TRANSACTION_SUCCESS_CC','nits@247around.com');
define("DEFAULT_MERCHANT_CONTACT_NO", "8826423424");

define("CASHBACK_REASON_DISCOUNT", "Discount");


/* Engineer incentive constants*/

// Paytm Sub Wallet GUID for engineer incentive
define("INCENTIVE_SUBWALLET_GUID", "846bb113-bf84-40b0-907d-6f167049677c");
//Paytm Merchant id for engineer incentive
define("INCENTIVE_PAYTM_MERCHANT_MID", "Blackm96092355456850");
//Paytm error codes
define("INVALID_BENEFICIARY_MOBILE_OR_EMAILID", "DE_044");
