<?php

class Validate_serial_no {

    public function __construct() {
	$this->MY_CI = & get_instance();
        $this->MY_CI->load->model('partner_model');
    }
    
    function validateSerialNo($partnerID, $serialNo){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID. " Srial No ". $serialNo);
        $method = $this->getLogicMethod($partnerID);
        if(!empty($method)){
            return $this->$method($partnerID, $serialNo);
        } else{
            return false;
        }
    }
    /**
     * @desc In this method, just pass partner id then it will return serial no validation method name.
     * @param String $partnerID
     * @return boolean|string
     */
    function getLogicMethod($partnerID){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID);
	$logic = array();

	$logic[AKAI_ID] = 'akai_serialNoValidation';
        $logic[SALORA_ID] = 'salora_serialNoValidation';
	if (isset($logic[$partnerID])) {
            log_message('info', __METHOD__. " Method not exist. Partner ID ". $logic[$partnerID]);
	    return $logic[$partnerID];
	} else {
            log_message('info', __METHOD__. " Method is not exist. Partner ID ". $partnerID);
	    return false;
	}
    }
    /**
     * @desc This method is used to validate serial number.
     * This is used to check directly from DB 
     * @param String $partnerID
     * @param String $serialNo
     * @return Int
     */
    function akai_serialNoValidation($partnerID, $serialNo){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID. " Srial No ". $serialNo);
        $result = $this->MY_CI->partner_model->getpartner_serialno(array('partner_id' =>$partnerID, 'serial_number' => $serialNo));
        if(!empty($result)){
            log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " code ".SUCCESS_CODE);
            return SUCCESS_CODE;
        } else {
            log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " code ".FAILURE_CODE);
            return FAILURE_CODE;
        }
    }
    /**
     * @desc This is used to validate salora serial number
     * logic - 7th and 8th digit are year and 9th and 10th digits are year
     * @param String $partnerID
     * @param String $serialNo
     * @return Int
     */
    function salora_serialNoValidation($partnerID, $serialNo) {
        log_message('info', __METHOD__ . " Enterring... Partner ID " . $partnerID . " Srial No " . $serialNo);
        $yearString = substr($serialNo, 6, 2);
        $monthString = substr($serialNo, 8, 2);
        $flag = true;
        if (!is_numeric($yearString)) {
            log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " year is not int ".$yearString);
            $flag = false;
        } 
        
        if (!is_numeric($monthString)) {
            log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " month is not int ".$monthString);
            $flag = false;
        }

        if ($flag) {
            $currentString = date('y').date('m');
            $srString = $yearString.$monthString;
            if($srString > $currentString){
                return FAILURE_CODE;
            } else {
                return SUCCESS_CODE;
            } 
        } else {
            return FAILURE_CODE;
        }
    }

}

