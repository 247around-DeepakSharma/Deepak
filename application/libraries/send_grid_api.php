<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Notify library to send Mails and SMSs
 *
 * @author anujaggarwal
 */
class send_grid_api {
var $url;
var $user;
var $pass;
    function __Construct() {
        $this->url = 'https://api.sendgrid.com/';
        $this->user = 'anujagg';
        $this->pass = 'w2KeG23@ve3@UEPn';
    }
    /*
     * This function used to sen
     */
    function send_email($params){
        $request =  $this->url.'api/mail.send.json';
        $session = curl_init($request);
        curl_setopt ($session, CURLOPT_POST, true);
        curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($session);
        curl_close($session);
        return $response;
    }
    function send_email_using_send_grid_templates($to,$to_name,$cc,$cc_name,$bcc,$bcc_name,$subject,$from,$from_name,$template_id,$dynamic_variables,$attachmentPath,$attachmentFileName){
        $finalDynamicVariableArray = array();
        foreach($dynamic_variables as $key=>$value){
            $finalDynamicVariableArray["-".$key."-"] = array($value,$value,$value);
        }
       $xSmtpApiArray =  array (
  'filters' => array ('templates' => array ('settings' => array ('enable' => 1,'template_id' => $template_id,),),),
  'sub' => $finalDynamicVariableArray
           );
       $xSmtpApiJson = json_encode($xSmtpApiArray);
        $params = array(
            'api_user'  => $this->user,
            'api_key'   => $this->pass,
            'to' => $to,
            'toname'=>$to_name,
            'cc'=>$cc,
            'cc_name'=>$cc_name,
            'bcc'=>$bcc,
            'bcc_name'=>$bcc_name,
            'subject'=>$subject,
            'html'=> '<p></p>',
            'text'=> '',
            'from'=> $from,
            'fromname'=> $from_name,
            'x-smtpapi'=> $xSmtpApiJson,
            'files['.$attachmentFileName.']' =>file_get_contents($attachmentPath)
            );
        $response = $this->send_email($params);
        $responseArray = json_decode($response,TRUE);
        return $responseArray['message'];
    }
}
