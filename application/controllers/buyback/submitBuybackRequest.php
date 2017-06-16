+<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * REST APIs for Partners to insert New orders in our CRM
 *
 * @author Abhay Anand
 */
class SubmitBuybackRequest extends CI_Controller {

    Private $partner = NULL;
    Private $ApiData = array();
    Private $jsonRequestData = NULL;

    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
    }

    function index() {
        log_message('info', "Entering: " . __METHOD__);
        $this->requestUrl = __METHOD__;

        //Default values
        $this->jsonResponseString['response'] = NULL;
        $this->jsonResponseString['code'] = ERR_GENERIC_ERROR_CODE;
        $this->jsonResponseString['result'] = ERR_GENERIC_ERROR_MSG;

        //Save header / ip address in DB
        $h = $this->getallheaders();
        if ($h === FALSE) {
            $this->header = json_encode($h);
            $this->sendJsonResponse(array(ERR_GENERIC_ERROR_CODE, ERR_GENERIC_ERROR_MSG));
        } else {
            $this->header = json_encode($h);
            $this->token = $h['Authorization'];

            //Validate token
            $this->partner = $this->partner_model->validate_partner($this->token);
            //Token validated
            $input_d = file_get_contents('php://input');

            // store API data into ApiData
            $this->ApiData = json_decode($input_d, TRUE);
            if (!(json_last_error() === JSON_ERROR_NONE)) {
                log_message('info', __METHOD__ . ":: Invalid JSON");

                //Invalid json
                $this->jsonRequestData = $input_d;
                $this->sendJsonResponse(array(ERR_INVALID_JSON_INPUT_CODE, ERR_INVALID_JSON_INPUT_MSG));
            } else {
                 $this->jsonRequestData = $input_d;
                 echo "<pre>"; print_r($this->ApiData);
                 
            }
        }
    }

}
