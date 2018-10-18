<?php

/* OTP Request -
   https://api.taxprogsp.co.in/taxpayerapi/dec/v1.0/authenticate?action=OTPREQUEST&aspid=1606680918&password=priya@b30&gstin=07AAFCB1281J1ZQ&username=blackmelon.750

   AUTHTOKEN Request - 
   https://api.taxprogsp.co.in/taxpayerapi/dec/v1.0/authenticate?action=AUTHTOKEN&aspid=1606680918&password=priya@b30&gstin=07AAFCB1281J1ZQ&username=blackmelon.750&OTP=773355
  
   gstr2a Request -
   https://api.taxprogsp.co.in/taxpayerapi/dec/v0.3/returns/gstr2a?action=B2B&aspid=1606680918&password=priya@b30&gstin=07AAFCB1281J1ZQ&username=blackmelon.750&authtoken=3811dfe98afa4e0ea62e1a0021d0a25d&ret_period=082017
   
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

