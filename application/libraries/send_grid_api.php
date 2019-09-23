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

    function __Construct() {
        $this->url = 'https://api.sendgrid.com/v3/';
    }

    /*
     * This function used to send curl request to sendgrid with required data and headers
     */

    function send_email($params) {
        $request = $this->url . 'mail/send';
        $session = curl_init($request);
        curl_setopt_array($session, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . SENDGRID_API_KEY,
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $params
        ));
        $response = curl_exec($session);

        return $response;
    }

    /*
     * This function is used to convert string of to,cc,bcc into a sendgrid request format if key is blank then it will return an empty array
     */

    function get_formated_array_to_send($key, $emailBasicDataArray) {
        $finalArray = array();
        if (array_key_exists($key, $emailBasicDataArray)) {
            $tempArray = explode(",", $emailBasicDataArray[$key]);
            foreach ($tempArray as $value) {
                $finalArray[] = array('email' => $value);
            }
        }
        return $finalArray;
    }

    /*
     * This function is used to convert substitute parameter aaray into sendgrid required format
     */

    function get_substitute_parameter_in_format($dynamic_variables) {
        $finalDynamicVariableArray = array();
        foreach ($dynamic_variables as $key => $value) {
            $finalDynamicVariableArray["-" . $key . "-"] = $value;
        }
        return $finalDynamicVariableArray;
    }

    /*
     * This function is used to send Email using SendGrid API V3
     * @input - 1) $emailBasicDataArray - It will contain basic info to send email Like - to,cc,bcc,from,fromName,subject (Required)(Note- Multiple to,cc,bcc Should be comma seprated)
     * @input - 2) $emailTemplateDataArray - It will contain template info  Like - templateID and optionalParameters  (Optional)
     * @input - 3) $emailAttachmentDataArray - It will contain attachment info  Like - attachmentType,filename, filePath  (Optional)
     */

    function send_email_using_send_grid_templates($emailBasicDataArray, $emailTemplateDataArray = array(), $emailAttachmentDataArray = array()) {
        log_message('info', __FUNCTION__ . ' => Entering: ');

        //convert all string input for to,cc,bcc into formated array
        $toArray = $this->get_formated_array_to_send("to", $emailBasicDataArray);
        $ccArray = $this->get_formated_array_to_send("cc", $emailBasicDataArray);
        $bccArray = $this->get_formated_array_to_send("bcc", $emailBasicDataArray);
        //convert substitute parameter into desired format
        $dynamicParamsArray = array();
        if (array_key_exists('dynamicParams', $emailTemplateDataArray)) {
            $dynamicParamsArray = $this->get_substitute_parameter_in_format($emailTemplateDataArray['dynamicParams']);
        }
        //start creating requested array
        //personalizations will contains info about to,cc,bcc,optional tags and subject of email
        if (!empty($toArray)) {
            $personalizationsArray['to'] = $toArray;
        }
        if (!empty($ccArray)) {
            $personalizationsArray['cc'] = $ccArray;
        }
        if (!empty($bccArray)) {
            $personalizationsArray['bcc'] = $bccArray;
        }
        if (!empty($dynamicParamsArray)) {
            $personalizationsArray['substitutions'] = $dynamicParamsArray;
        }
        if (array_key_exists('subject', $emailBasicDataArray)) {
            $personalizationsArray['subject'] = $emailBasicDataArray['subject'];
        }
        $data['personalizations'] = array($personalizationsArray);
        //From will contain from name and email
        $data['from'] = array("email" => $emailBasicDataArray['from'], "name" => $emailBasicDataArray['fromName']);
        //Content will contain information about you want to send a plain text email or html email
        $data['content'][] = array("type" => "text/html charset=utf-8", "value" => "Hello, World!");
        // template_id will contains id of your sendgrid  transactional template 
        if (array_key_exists('templateId', $emailTemplateDataArray)) {
            $data['template_id'] = $emailTemplateDataArray['templateId'];
        }
        //attachments tag contain info about attachment like file content, file name, file type
        if (!empty($emailAttachmentDataArray)) {
            $data['attachments'][] = array("content" => base64_encode(file_get_contents($emailAttachmentDataArray['filePath'])),
                "filename" => $emailAttachmentDataArray['fileName'] . "." . $emailAttachmentDataArray['type'],
                "name" => $emailAttachmentDataArray['fileName'],
                "type" => $emailAttachmentDataArray['type']);
        }
        log_message('info', __FUNCTION__ . ' => Success: ' . print_r($data, 1));
        // convert array into json and send a curl request to sendgrid
        $response = $this->send_email(json_encode($data));
        if ($response == '') {
            log_message('info', __FUNCTION__ . ' => Success: ' . print_r($response, 1));
            return 'success';
        } else {
            log_message('info', __FUNCTION__ . ' => Fail: ' . print_r($response, 1));
            return 'failure';
        }
    }

}
