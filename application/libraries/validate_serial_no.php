<?php

class Validate_serial_no {

    public function __construct() {
	$this->MY_CI = & get_instance();
        $this->MY_CI->load->model('partner_model');
        $this->MY_CI->load->model('booking_model');
    }
    
    function validateSerialNo($partnerID, $serialNo, $price_tags = ""){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID. " Srial No ". $serialNo);
        $flag = true;
        
        if(!empty($price_tags) && $price_tags != REPEAT_BOOKING_TAG){
            $v =$this->check_duplicate_serial_number($serialNo);
            if(!empty($v)){
                $flag = false;
                return $v;
            }
        }
        if($flag){
            $method = $this->getLogicMethod($partnerID);
            if(!empty($method)){
                return $this->$method($partnerID, $serialNo);
            } else{
                return false;
            }
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
        $logic[QFX_ID] = 'qfx_serialNoValidation';
        
	if (isset($logic[$partnerID])) {
            log_message('info', __METHOD__. " Method exist. Partner ID ". $logic[$partnerID]);
	    return $logic[$partnerID];
	} else {
            log_message('info', __METHOD__. " Method is not exist. Partner ID ". $partnerID);
	    return false;
	}
    }
    /**
     * @desc This method is used to validate serial number.
     * Serial number should be alpha numeric with 19 character
     * @param String $partnerID
     * @param String $serialNo
     * @return Int
     */
    function akai_serialNoValidation($partnerID, $serialNo){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID. " Srial No ". $serialNo);
        $result = $this->MY_CI->partner_model->getpartner_serialno(array('partner_id' =>$partnerID, 'serial_number' => $serialNo, "active" => 1));
        if(!empty($result)){
            log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " code ".SUCCESS_CODE);
            return array('code' => SUCCESS_CODE);
        } else {
            log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " code ".FAILURE_CODE);
            return array('code' => FAILURE_CODE, "message" => AKAI_SERIAL_NO_VALIDATION_FAILED_MSG);
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
                return array('code' => FAILURE_CODE, "message" => SALORA_SERIAL_NO_VALIDATION_FAILED_MSG);
            } else {
                return array('code' => SUCCESS_CODE);
            } 
        } else {
            return array('code' => FAILURE_CODE, "message" => SALORA_SERIAL_NO_VALIDATION_FAILED_MSG);
        }
    }
    /**
     * @desc Used to validate QFX serial Number
     * Serial Number should be integer and 17 digit.
     * Serial number should not allow to start with zero
     * @param int $partnerID
     * @param String $serialNo
     * @return boolean
     */
    function qfx_serialNoValidation($partnerID, $serialNo){
        log_message('info', __METHOD__ . " Enterring... Partner ID " . $partnerID . " Srial No " . $serialNo);
        if (!is_numeric($serialNo)) {
            
            log_message('info', __METHOD__ . " Partner ID " . $partnerID . " Srial No " . $serialNo . " Not Numeric");
            return array('code' => FAILURE_CODE, "message" => QFX_SERIAL_NO_VALIDATION_FAILED_MSG);
            
        } else if($serialNo == 0){
            
            log_message('info', __METHOD__ . " Partner ID " . $partnerID . " Srial No " . $serialNo . " zero");
            return array('code' => FAILURE_CODE, "message" => QFX_SERIAL_NO_VALIDATION_FAILED_MSG);
            
        } else if (substr($serialNo, 0, 1) == '0') {
            
            log_message('info', __METHOD__ . " Partner ID " . $partnerID . " Srial No " . $serialNo . " Start with zero");
           return array('code' => FAILURE_CODE, "message" => QFX_SERIAL_NO_VALIDATION_FAILED_MSG);
           
        } else if(strlen($serialNo) == 17 && is_numeric($serialNo)){
            
            log_message('info', __METHOD__ . " Partner ID " . $partnerID . " Srial No " . $serialNo . " 17 Digit numeric ");
            return array('code' => SUCCESS_CODE);
        } else {
            
            log_message('info', __METHOD__ . " Partner ID " . $partnerID . " Srial No " . $serialNo . " Return false");
            return array('code' => FAILURE_CODE, "message" => QFX_SERIAL_NO_VALIDATION_FAILED_MSG);
        }
    }
    /**
     * @desc
     * @param String $serial_number
     * @return boolean
     */
    function check_duplicate_serial_number($serial_number){
        $data = $this->MY_CI->booking_model->get_unit_details(array('serial_number' => $serial_number, 'booking_status != "'._247AROUND_CANCELLED.'"' => NULL,
            "price_tags != '".REPEAT_BOOKING_TAG."'" => NULL));
        if(!empty($data)){
            $booking_id = "";
            $isDuplicate = false;
            foreach ($data as $value) {
               if($value['booking_status'] == _247AROUND_COMPLETED){
                   
                    $d = date_diff(date_create($value['ud_closed_date']), date_create('today')); 
                    if($d->days < BOOKING_WARRANTY_DAYS){
                        $booking_id = " You already used in this Booking ID - ".$value['booking_id'];
                        $isDuplicate = TRUE;
                        break;
                    }
               } else {
                   $isDuplicate = TRUE;
                   break;
               }
            }
            
            if($isDuplicate){
               return array('code' => DUPLICATE_SERIAL_NO_CODE, "message" => DUPLICATE_SERIAL_NUMBER_USED." ".$booking_id);
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
}

