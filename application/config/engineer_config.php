<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
///  Making new constants for responses
/* ENV = "test" for test and "production" for prod"  */
define("ENV","production");
define("APP_VERSION", "2.29"); /* Engineer App Current Version */
define("FORCE_UPGRADE", "force_upgrade");
define("SEND_WHATSAPP", "send_whatsapp");
define("ACCESS_FROM_SPLASH_SCREEN","This Request Come From Splash Screen Without Permissions");
/*  NEw constants for S3 URL */
define("COLLATERAL_S3_PATH_LIVE","https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/");
define("COLLATERAL_S3_PATH_TEST","https://s3.amazonaws.com/bookings-collateral-test/vendor-partner-docs/");