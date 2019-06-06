<?php

define("QFX_SERIAL_NO_VALIDATION_FAILED_MSG", "Serial No should be 17 Digit Interger");
define("AKAI_SERIAL_NO_VALIDATION_FAILED_MSG", "Entered serial number is wrong");
define("SALORA_SERIAL_NO_VALIDATION_FAILED_MSG", "Serial No should be alphanumeric. 6-7 character should be year and 8-9 character should be month");
define("DUPLICATE_SERIAL_NUMBER_USED", "Please insert valid Serial No as entered serial no is duplicate.");
define("BOOKING_WARRANTY_DAYS", 30);
define("DUPLICATE_SERIAL_NO_CODE", 1001);
//JVC
define("JVC_SERIAL_NO_VALIDATION_FAILED_MSG", "Please update valid serial number");
define("JVC_TV_SERIAL_NO_VALIDATION_LENGTH_FAILED_MSG", "Serial Number Length Should be greater then 16 and less then 19 OR  it should be Start With SHG32");
define("JVC_TV_SERIAL_NO_VALIDATION_START_FAILED_MSG", "Serial Number Should be Start With Following Values ");
define("JVC_TV_SERIAL_NO_VALIDATION_ALPHANUMARIC_FAILED_MSG", "Only Alphanumaric Characters are allowed");
define("JVC_TV_SERIAL_NO_VALIDATION_SR_FAILED_MSG", "Last 4 character Should be In A123 Pattern ");
define("JVC_TV_SERIAL_NO_VALIDATION_SHG_FAILED_MSG", "In SHG32 Pattern 6,7 Character Should be alphabet");
define("JVC_WM_SERIAL_NO_VALIDATION_COLOR_FAILED_MSG", "");
define("JVC_WM_SERIAL_NO_VALIDATION_FM_FAILED_MSG", "In SHG32 Pattern 6,7 Character Should be alphabet");
define("JVC_WM_SERIAL_NO_VALIDATION_PRODUCT_FAILED_MSG", "In SHG32 Pattern 6,7 Character Should be alphabet");
define("JVC_WM_SERIAL_NO_VALIDATION_VENDOR_FAILED_MSG", "In SHG32 Pattern 6,7 Character Should be alphabet");
define("JVC_WM_SERIAL_NO_VALIDATION_BRAND_FAILED_MSG", "In SHG32 Pattern 6,7 Character Should be alphabet");
define("JVC_WM_SERIAL_NO_VALIDATION_START_FAILED_MSG", "In SHG32 Pattern 6,7 Character Should be alphabet");

//LEMON
define('LEMON_SERIAL_NO_LENGTH_VALIDATION_FAILED_MSG','Length Should be 15, PLease Enter Valid Serial Number');
define('LEMON_SERIAL_NO_ALL_VALIDATION_FAILED_MSG','Please Entered a valid Serial Number');

// JVC Serial number Posible values String
define('JVC_TV_SN_START_POSIBLE_VALUES','C1,C2,C3,F1,F2,F3,J1,J2,J3,H1,No,I1,HS,HSNI');
define('JVC_WM_SN_COLOR_CODE_POSIBLE_VALUES','RE,BR,BL,GR');
define('JVC_WM_SN_FACTORY_MODEL_POSIBLE_VALUES','65,68,90,92,9S');
define('JVC_WM_SN_PRODUCT_POSIBLE_VALUES','WM,WS');
define('JVC_WM_SN_VENDOR_POSIBLE_VALUES','A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z');
define('JVC_WM_SN_BRAND_POSIBLE_VALUES','CN,CW,NB');
define('JVC_WM_SN_START_POSIBLE_VALUES','H2,H1');

// LEMON Serial number Posible values String
define('LEMON_SN_START_POSIBLE_VALUES','I,C');
define('LEMON_SN_VENDOR_POSIBLE_VALUES','VE,AD');
define('LEMON_SN_MODEL_POSIBLE_VALUES','24LL,24LS,32LL,32LS,40LL,40LS');


//JEEVES(MICROMAX) Serial number possible values
define('JEEVES_FIRST_TWO_DIGIT', 00);
define('JEEVES_SERIAL_NO_VALIDATION_FAILED_MSG', 'Serial No length should be 15 character, starting with 00.');

//Wybor 
define('MONTH_POSIBLE_VALUES_2016', 'A,B,C,D,E,F,G,H,I,J,K,L');
define('BRAND_POSIBLE_VALUES', 'W,O,B,E,BL');
define('FAILURE_MSG', 'Please Enter Correct Serial Number');

//Repeat Booking Failure Massege
define('REPEAT_BOOKING_FAILURE_MSG', 'For Repeat Booking Serial Number Should be Similar to Parent Booking. Booking Will not be Complete Until Repeat Booking Will not have correcct Serial Number');

//Borly SErial Number Possible Constant
define('BURLY_CODE','29');
define('BURLY_SERIALNO_LENGHT','19');
define('BURLY_SERIAL_NO_VALIDATION_FAILED_MSG','Serial No Should Be 19 Digit numeric code.First Two digits are 29 ,8th and 9th digit shows vendor code,10th and 11th digit shows year,12th and 13th digit shows month only');
define('MONTH_POSIBLE_VALUES','1,2,3,4,5,6,7,8,9,10,11,12');
define('VIDEOCON_PRODUCT_CAT_POSIBLE_VALUES','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,01,32,33,34,35,36,37,38,39,40,44,48,49,51,52,53,54,58,74,78,82,86,90');