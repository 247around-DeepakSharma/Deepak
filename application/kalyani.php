<?php
//{"status_cd":"1","auth_token":"1ffa1a4819a44566941ee4a6eb7e6286","expiry":"","sek":"w/pYL7+aC743zdAXoByR4uJxwK2S/uZS801h9Mo/O6c="}
/* OTP Request -
   https://api.taxprogsp.co.in/taxpayerapi/dec/v1.0/authenticate?action=OTPREQUEST&aspid=1606680918&password=priya@b30&gstin=07AAFCB1281J1ZQ&username=blackmelon.750

   AUTHTOKEN Request - 
   https://api.taxprogsp.co.in/taxpayerapi/dec/v1.0/authenticate?action=AUTHTOKEN&aspid=1606680918&password=priya@b30&gstin=07AAFCB1281J1ZQ&username=blackmelon.750&OTP=773355
  
   gstr2a Request -
   https://api.taxprogsp.co.in/taxpayerapi/dec/v0.3/returns/gstr2a?action=B2B&aspid=1606680918&password=priya@b30&gstin=07AAFCB1281J1ZQ&username=blackmelon.750&authtoken=1ffa1a4819a44566941ee4a6eb7e6286&ret_period=082017
   
   Search GST Number
   https://api.taxprogsp.co.in/commonapi/v1.1/search?aspid=1606680918&password=priya@b30&action=TP&Gstin=07ALDPK4562B1ZG
  */


/*
247 around GSTIN - 07ALDPK4562B1ZG

Different example of gstin 
29ASDPD0397G1ZS	- GAJANAN ASSOCIATES - Regular - Active
21CSGPM6146R1Z8	- MISHRA SALE AND SERVCES - Regular - Inactive
09APTPV6716J2ZV - POOJA SERVICES - Regular - Cancelled
19ABNFS8916M2Z5	- S L ENTERPRISE - Composition - Active


//test api for otp request
//http://testapi.taxprogsp.co.in/taxpayerapi/dec/v0.2/authenticate?action=OTPREQUEST&aspid=1606680918&password=priya@b30&gstin=27GSPMH0041G1ZZ&username=Chartered.MH.1
Response - {"status_cd":"0","error":{"error_cd":"TEC4001","message":"GSTN Error: OTP generation failed as OTP server is down. Contact Support"}}

//live api for otp request
//https://api.taxprogsp.co.in/taxpayerapi/dec/v1.0/authenticate?action=OTPREQUEST&aspid=1606680918&password=priya@b30&gstin=07ALDPK4562B1ZG    
 * 
UPDATE `booking_state_change` SET `partner_id`='247001' WHERE id = '2145589'
 */
//$url = 'https://api.taxprogsp.co.in/taxpayerapi/dec/v0.3/returns/gstr2a?action=B2B&aspid=1606680918&password=priya@b30&gstin=07AAFCB1281J1ZQ&username=blackmelon.750&authtoken=227824074041429a953ffe5f18a10a32&ret_period=092018';

//SELECT * FROM `vendor_partner_invoices` WHERE create_date >= "2018-10-01" AND create_date <= "2018-12-31";
//

select employee.full_name, penalty_details.criteria, penalty_on_booking.criteria_id from penalty_on_booking join employee_relation on FIND_IN_SET(penalty_on_booking.service_center_id, employee_relation.service_centres_id) join employee on employee.id = employee_relation.agent_id join penalty_details on penalty_details.id = penalty_on_booking.criteria_id group by criteria_id, employee_relation.agent_id ORDER BY `employee`.`full_name` ASC
        
        
        
select employee.full_name, penalty_details.criteria, count(DISTINCT penalty_on_booking.booking_id) as total_booking_id, count(penalty_on_booking.id) as total_penalty_count, SUM(penalty_on_booking.penalty_amount) as penalty_amount from penalty_on_booking join employee_relation on FIND_IN_SET(penalty_on_booking.service_center_id, employee_relation.service_centres_id) join employee on employee.id = employee_relation.agent_id join penalty_details on penalty_details.id = penalty_on_booking.criteria_id group by criteria_id, employee_relation.agent_id ORDER BY `employee`.`full_name` ASC