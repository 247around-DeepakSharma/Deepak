<?php

class Validate_serial_no {

    public function __construct() {
	$this->MY_CI = & get_instance();
        $this->MY_CI->load->model('partner_model');
        $this->MY_CI->load->model('booking_model');
    }
    
    function validateSerialNo($partnerID, $serialNo, $price_tags, $user_id, $booking_id,$applianceID,$modelNumber = NULL){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID. " Srial No ". $serialNo);
        $flag = true;
        
        if(!empty($price_tags) && $price_tags != REPEAT_BOOKING_TAG){
            $v =$this->check_duplicate_serial_number($serialNo, $price_tags, $user_id, $booking_id);
            if(!empty($v)){
                $flag = false;
                return $v;
            }
        }
        if($flag){
            $method = $this->getLogicMethod($partnerID);
            if(!empty($method)){
                if($method == 'jvc_serialNoValidation'){
                    return $this->$method($partnerID, $serialNo,$applianceID);
                }
                 if($method == 'lemon_serialNoValidation'){
                    return $this->$method($partnerID, $serialNo,$modelNumber);
                }
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
        $logic[JVC_ID] = 'jvc_serialNoValidation';
        $logic[LEMON_ID] = 'lemon_serialNoValidation';
        
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
    function check_duplicate_serial_number($serial_number, $price_tags, $user_id, $booking_id){
        $data = $this->MY_CI->booking_model->get_unit_details(array('serial_number' => $serial_number, 'booking_status != "'._247AROUND_CANCELLED.'"' => NULL,
            "price_tags != '".REPEAT_BOOKING_TAG."'" => NULL, "booking_id != '".$booking_id."'" => NULL));
       
        if(!empty($data)){
            $msg = "";
            $isDuplicate = false;
            foreach ($data as $key =>$value) {
               
               if($value['booking_status'] == _247AROUND_COMPLETED){

                    $d = date_diff(date_create($value['ud_closed_date']), date_create('today')); 
                    if($d->days < BOOKING_WARRANTY_DAYS){
                      
                        $booking_details = $this->MY_CI->booking_model->get_bookings_count_by_any('user_id', array('booking_id' => $value['booking_id']));
                      
                        if($booking_details[0]['user_id'] == $user_id){
                        
                            if($price_tags == $value['price_tags']){
                          
                                $msg = " You already used in this Booking ID - ".$value['booking_id'];
                                $isDuplicate = TRUE;
                                break;
                            }
                        } else {
                            $msg = " You already used in this Booking ID - ".$value['booking_id'];
                            $isDuplicate = TRUE;
                            break;
                        }
                    }
               } else {
                   $isDuplicate = TRUE;
                   break;
               }
            }
            if($isDuplicate){
               return array('code' => DUPLICATE_SERIAL_NO_CODE, "message" => DUPLICATE_SERIAL_NUMBER_USED." ".$msg);
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
         /**
     * @desc This method is used to validate JVC serial number.
     * @param String $partnerID
     * @param String $serialNo
     * @param String $applianceID
     * @return Int
     */
    function jvc_serialNoValidation($partnerID, $serialNo, $applianceID){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID. " Srial No ". $serialNo);
        switch ($applianceID) {
            case _247AROUND_TV_SERVICE_ID:
                return $this->jvc_television_serial_number_validation($serialNo);
            case _247AROUND_WASHING_MACHINE_SERVICE_ID:
                return $this->jvc_WM_serial_number_validation($serialNo);
            default:
        }
    }
     /**
     * @desc This method is used to validate JVC Television (service_id 46) serial number.
     * @param String $partnerID
     * @param String $serialNo
     * @param String $applianceID
     * @return Int
     */
    function jvc_television_serial_number_validation($serialNo){
        $stringLength = strlen($serialNo);
        $serialNo = strtoupper($serialNo);
        $firstString = strtoupper(substr($serialNo,0,5));
        if ($firstString == 'SHG32') {
            $secondString = substr($serialNo,5,2);
            if (!(preg_match("/^[a-zA-Z]$/", $secondString[0]) && preg_match("/^[a-zA-Z]$/", $secondString[1]))) {
                //return array('code' => FAILURE_CODE, "message" => JVC_TV_SERIAL_NO_VALIDATION_SHG_FAILED_MSG);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            return array('code' => SUCCESS_CODE);
        }
        else if($stringLength>15 && $stringLength<20){
            $startDigitLength = 4;
            if($stringLength == 16 || $stringLength == 17){
                $startDigitLength = 2;
            }
            //Serial Number Should start with pre Defined Values
            $start = substr($serialNo,0,$startDigitLength);
            $expectedStartValuesArray = explode(",",JVC_TV_SN_START_POSIBLE_VALUES);
            if(!in_array($start, $expectedStartValuesArray)){
                //return array('code' => FAILURE_CODE, "message" => JVC_TV_SERIAL_NO_VALIDATION_START_FAILED_MSG .JVC_TV_SN_START_POSIBLE_VALUES);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            //Open call Panel,MainBoardCoding,Factory model should consider 2 letter/Digit
            $factoryModel = substr($serialNo,$startDigitLength,5);
            $openCellPanelCoding = substr($serialNo,$startDigitLength+5,2);
            $mainBoardCodingLength = 1;
            if($stringLength == 17 || $stringLength == 19){
                $mainBoardCodingLength = 2;
            }
            $mainBoardCoding = substr($serialNo,$startDigitLength+7,$mainBoardCodingLength);
            $nextIndex = $startDigitLength+7+$mainBoardCodingLength;
            if(!(ctype_alnum($openCellPanelCoding) && ctype_alnum($mainBoardCoding) && ctype_alnum($factoryModel))) {
                //return array('code' => FAILURE_CODE, "message" => JVC_TV_SERIAL_NO_VALIDATION_ALPHANUMARIC_FAILED_MSG);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            // Month should be alphabetic and Year should be a number , date should not be greater then today
            $yearValidation =  $this->_jvc_year_month_validation(substr($serialNo,$nextIndex,1),substr($serialNo,$nextIndex+1,1),1);
            if(!$yearValidation){
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            //First Letter Should be alphabetic and other 3 should be numaric
            $srNumberValidation = $this->_jvc_sr_number_validation(substr($serialNo,$nextIndex+2,4));
            if(!$srNumberValidation){
                //return array('code' => FAILURE_CODE, "message" => JVC_TV_SERIAL_NO_VALIDATION_SR_FAILED_MSG);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            return array('code' => SUCCESS_CODE);
        }
        else{
            return array('code' => FAILURE_CODE, "message" => JVC_TV_SERIAL_NO_VALIDATION_LENGTH_FAILED_MSG);
        }
    }
    function jvc_WM_serial_number_validation($serialNo){
        $serialNo = strtoupper($serialNo);
        $stringLength = strlen($serialNo);
        if($stringLength == 18){
            // Color Coding Validation 
            $colorCodingArray = explode(",",JVC_WM_SN_COLOR_CODE_POSIBLE_VALUES);
            if(!in_array(substr($serialNo,$stringLength-9,2),$colorCodingArray)){
                //return array('code' => FAILURE_CODE, "message" => "Index 10,11 Should have following Values ".JVC_WM_SN_COLOR_CODE_POSIBLE_VALUES);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            $factoryModelArray = explode(",",JVC_WM_SN_FACTORY_MODEL_POSIBLE_VALUES);
            if(!in_array(substr($serialNo,$stringLength-11,2),$factoryModelArray)){
                //return array('code' => FAILURE_CODE, "message" => "Index 8,9 Should have following Values ".JVC_WM_SN_FACTORY_MODEL_POSIBLE_VALUES);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            $productArray = explode(",",JVC_WM_SN_PRODUCT_POSIBLE_VALUES);
            if(!in_array(substr($serialNo,$stringLength-13,2),$productArray)){
                //return array('code' => FAILURE_CODE, "message" => "Index 6,7 Should have following Values ".JVC_WM_SN_PRODUCT_POSIBLE_VALUES);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            $vendorArray = explode(",",JVC_WM_SN_VENDOR_POSIBLE_VALUES);
            if(!in_array(substr($serialNo,$stringLength-15,1),$vendorArray)){
               //return array('code' => FAILURE_CODE, "message" => "Index 5 Should have following Values ".JVC_WM_SN_VENDOR_POSIBLE_VALUES);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            $brandsArray = explode(",",JVC_WM_SN_BRAND_POSIBLE_VALUES);
            if(!in_array(substr($serialNo,$stringLength-16,2),$brandsArray)){
               //return array('code' => FAILURE_CODE, "message" => "Index 3,4 Should have following Values ".JVC_WM_SN_BRAND_POSIBLE_VALUES);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            $startArray = explode(",",JVC_WM_SN_START_POSIBLE_VALUES);
            if(!in_array(substr($serialNo,$stringLength-18,2),$startArray)){
               //return array('code' => FAILURE_CODE, "message" => "Index 1,2 Should have following Values ".JVC_WM_SN_START_POSIBLE_VALUES);
               return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            // Month should be alphabetic and Year should be a number , date should not be greater then today
            $yearValidation =  $this->_jvc_year_month_validation(substr($serialNo,$stringLength-7,1),substr($serialNo,$stringLength-6,2),2);
            if(!$yearValidation){
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            //First Letter Should be alphabetic and other 3 should be numaric
            $srNumberValidation = $this->_jvc_sr_number_validation(substr($serialNo,$stringLength-4,4));
            if(!$srNumberValidation){
                //return array('code' => FAILURE_CODE, "message" => JVC_TV_SERIAL_NO_VALIDATION_SR_FAILED_MSG);
                return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
            }
            return array('code' => SUCCESS_CODE);
        }
        else{
            return array('code' => FAILURE_CODE, "message" => JVC_SERIAL_NO_VALIDATION_FAILED_MSG);
        }
    }
    function _jvc_year_month_validation($month,$year,$yearDigit = 1){
        $currentYear = substr(date("Y"),3,1);
        if($yearDigit>1){
           $currentYear = substr(date("Y"),2,2); 
        }
        $currentMonth = date("m");
        $monthNumberMappingArray = array("A"=>1,"B"=>2,"C"=>3,"D"=>4,"E"=>5,"F"=>6,"G"=>7,"H"=>8,"I"=>9,"J"=>10,"K"=>11,"L"=>12);
        if(array_key_exists($month, $monthNumberMappingArray)){
            $letterMonth = $monthNumberMappingArray[$month];
        }
        else{
            return FALSE;
        }
        //$month Should be alphabetic 
        if (!preg_match("/^[a-zA-Z]$/", $month)) {
            return FALSE;
        }
        //year Should be Numaric 
        else if(!is_numeric($year)) {
            return FALSE;
        }
        //Year Should not be greater then current year
        else if($year>$currentYear){
            return FALSE;
        }
        else if($year == $currentYear && $letterMonth > $currentMonth){
            return FALSE;
        }
        else{
            return TRUE;
        }
    }
    function _jvc_sr_number_validation($sr_number){
        $length = strlen($sr_number);
        if($length != 4){
            return FALSE;
        }
        else if(!preg_match("/^[a-zA-Z]$/", $sr_number[0])) {
            return FALSE;
        }
        else if(!(is_numeric($sr_number[1]) && is_numeric($sr_number[2]) && is_numeric($sr_number[3]))){
            return FALSE;
        }
        return TRUE;
    }
             /**
     * @desc This method is used to validate JVC serial number.
     * @param String $partnerID
     * @param String $serialNo
     * @param String $applianceID
     * @return Int
     */
    function lemon_serialNoValidation($partnerID, $serialNo,$modelNumber){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID. " Srial No ". $serialNo);
        $serialNo = strtoupper($serialNo);
        $stringLength = strlen($serialNo);
        if($stringLength  == 15){
            // Lemon  start Values
            $colorCodingArray = explode(",",LEMON_SN_START_POSIBLE_VALUES);
            if(!in_array(substr($serialNo,0,1),$colorCodingArray)){
               // return array('code' => FAILURE_CODE, "message" => "Index 0 Should have following Values ".LEMON_SERIAL_NO_ALL_VALIDATION_FAILED_MSG);
                 return array('code' => FAILURE_CODE, "message" => LEMON_SERIAL_NO_ALL_VALIDATION_FAILED_MSG);
            }
            // Month should be alphabetic and Year should be a number , date should not be greater then today
            $yearValidation =  $this->_jvc_year_month_validation(substr($serialNo,5,1),substr($serialNo,1,2),2);
            if(!$yearValidation){
                return array('code' => FAILURE_CODE, "message" => LEMON_SERIAL_NO_ALL_VALIDATION_FAILED_MSG);
            }
            //Vendor Value Should be only in expected values
            $vendorArray = explode(",",LEMON_SN_VENDOR_POSIBLE_VALUES);
            if(!in_array(substr($serialNo,3,2),$vendorArray)){
                return array('code' => FAILURE_CODE, "message" => LEMON_SERIAL_NO_ALL_VALIDATION_FAILED_MSG);
            }
            //Model Code Values
            if($modelNumber){
                $modelCode = substr($serialNo,6,4);
                $modelNumberValidation = $this->_lemonModelCodeValidation($modelNumber,$modelCode);
                if(!$modelNumberValidation){
                    return array('code' => FAILURE_CODE, "message" => LEMON_SERIAL_NO_ALL_VALIDATION_FAILED_MSG);
                }
            }
            $siNumber = substr($serialNo,10,5);
            if (!is_numeric($siNumber)) {
                return array('code' => FAILURE_CODE, "message" => LEMON_SERIAL_NO_ALL_VALIDATION_FAILED_MSG);
            }
        }
        else{
            return array('code' => FAILURE_CODE, "message" => LEMON_SERIAL_NO_LENGTH_VALIDATION_FAILED_MSG);
        }
    }
    function _lemonModelCodeValidation($modelNumber,$modelCode){
        $expected_model_code = substr($modelNumber,0,4);
        if($expected_model_code != $modelCode){
            return FALSE;
        }
        return TRUE;
    }
}

