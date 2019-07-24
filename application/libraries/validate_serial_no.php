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
        //Check For Duplicate In Non Repeat Booking Case
        $access = $this->MY_CI->partner_model->get_partner_permission(array('partner_id' => $partnerID,'permission_type' => DO_NOT_CHECK_DUPLICATE_SERIAL_NUMBER, 'is_on' => 1));
        if(empty($access)){
             if(!empty($price_tags) && $price_tags != REPEAT_BOOKING_TAG){
                 $v =$this->check_duplicate_serial_number($serialNo, $price_tags, $user_id, $booking_id);
                 if(!empty($v)){
                     $flag = false;
                     return $v;
                 }
             }
        }
        //If booking is repeat then validate serial number with parent booking 
        if($price_tags == REPEAT_BOOKING_TAG){
            $repeatResult =  $this->validate_repeat_booking_serial_number($serialNo,$booking_id);
            if($repeatResult){
                return $repeatResult;
            }
        }
        // Validate with basic rules 
        if($flag){
            $method = $this->getLogicMethod($partnerID);
           
            if(!empty($method)){
                if($method == 'jvc_serialNoValidation'){
                    return $this->$method($partnerID, $serialNo,$applianceID);
                }
                if($method == 'lemon_serialNoValidation'){
                    return $this->$method($partnerID, $serialNo,$modelNumber);
                }

                if($method == 'jeeves_serialNoValidation'){
                    return $this->$method($partnerID, $serialNo, $booking_id);
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
        $logic[JEEVES_ID] = 'jeeves_serialNoValidation';
        $logic[WYBOR_ID] = 'wybor_serialNoValidation';
        $logic[BURLY_ID]='burly_serialNoValidation';
        $logic[VIDEOCON_ID]='videocon_serialNoValidation';
        $logic[KENSTAR_ID]='kenstar_serialNoValidation';

        
	if (isset($logic[$partnerID])) {
            log_message('info', __METHOD__. " Method exist. Partner ID ". $logic[$partnerID]);
	    return $logic[$partnerID];
	} else {
            log_message('info', __METHOD__. " Method is not exist. Partner ID ". $partnerID);
	    return false;
	}
    }
    
    /**
     * @desc This method is used to validate serial number for jeeves partner and micromax brand.
     * Serial number starting with 00.
     * then next 3 digit will be integer.
     * then next 1 digit will be character
     * then next 8 digit will be integer
     * then next last digit will be character
     * and total length is 15.
     * @param String $partnerID
     * @param String $serialNo
     * @param String $booking_id
     * @return Int
     */
    function jeeves_serialNoValidation($partnerID, $serialNo, $booking_id){
        log_message('info', __METHOD__ . " Enterring... Partner ID " . $partnerID . " Srial No " . $serialNo . " Booking Id ". $booking_id);
        $where = array(
                    'booking_id' =>$booking_id, 
                    'appliance_brand' => 'Micromax', 
                    'partner_id' => $partnerID);
        $result = $this->MY_CI->booking_model->get_unit_details($where, FALSE, 'id');
        if(!empty($result)){
            $flag = true;
            $failure_msg = "";
            $digit1to2 = substr($serialNo, 0, 2);
            $digit3to5 = substr($serialNo, 2, 3);
            $digit6 = substr($serialNo, 5, 1);
            $digit7to14 = substr($serialNo, 6, 8);
            $digit15 = substr($serialNo, 14, 1);
            if(strlen($serialNo) != 15){
               log_message('info', __METHOD__ . " Partner ID " . $partnerID . " Srial No " . $serialNo . " not 15 digit number ");
               $failure_msg = JEEVES_SERIAL_NO_VALIDATION_FAILED_MSG;
               $flag = false;
            }
            else if ($digit1to2 != JEEVES_FIRST_TWO_DIGIT) {
                log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " First two digit is not 00 ".$digit1to2);
                $failure_msg = JEEVES_SERIAL_NO_VALIDATION_FAILED_MSG;
                $flag = false;
            } 
            else if (!is_numeric($digit3to5)) {
                $failure_msg = JEEVES_SERIAL_NO_VALIDATION_FAILED_MSG;
                log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " Digit 3 to 5 is not integer ".$digit3to5);
                $flag = false;
            }
            else if(!ctype_alpha($digit6)){
                $failure_msg = JEEVES_SERIAL_NO_VALIDATION_FAILED_MSG;
                log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " Digit 6 is not character ".$digit6);
                $flag = false; 
            }
            else if (!is_numeric($digit7to14)) {
                $failure_msg = JEEVES_SERIAL_NO_VALIDATION_FAILED_MSG;
                log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " Digit 7 to 15 is not integer ".$digit7to14);
                $flag = false;
            }
//            else if(!ctype_alpha($digit15)){
//                $failure_msg = JEEVES_SERIAL_NO_VALIDATION_FAILED_MSG;
//                log_message('info', __METHOD__. " Partner ID ". $partnerID. " Srial No ". $serialNo. " Digit 15 is not character ".$digit15);
//                $flag = false; 
//            }
            if ($flag) {
                return array('code' => SUCCESS_CODE);
            }
            else{
                return array('code' => FAILURE_CODE, "message" => $failure_msg);
            }
            
        } else {
            log_message('info', __METHOD__. 'No need to apply serial no checking....');
            return array('code' => SUCCESS_CODE);
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
     * @desc This is used to validate Burly serial number
     * logic - first two digit are '29',next five digit are numeric only ,next two digits are '11',next two digit shows year , next two digit show month(01 to 12),next 6 digits are numeric
     * @param String $partnerID
     * @param String $serialNo
     * @return Int
     */
    function burly_serialNoValidation($partnerID, $serialNo) {
        log_message('info', __METHOD__ . " Enterring... Partner ID " . $partnerID . " Srial No " . $serialNo);
        $first_two_str = substr($serialNo,0,2);
        $product_code_str=substr($serialNo,2,5);
        $vendor_code_str=substr($serialNo,7,2);
        $year_code_str=substr($serialNo,9,2);
        $month_code_str = substr($serialNo,11,2);
        $month_year=$year_code_str.$month_code_str;
        $current_date=date('y').date('m');
        $serial_code_str=substr($serialNo,13,6);
        $min=01;
        $max=12;
          
        if(!(is_numeric($serialNo) && (strlen($serialNo)== BURLY_SERIALNO_LENGHT)))
        {
           log_message('info', __METHOD__. " Partner ID ". $partnerID. " Serial No ". $serialNo. " Burly Code  is not int ".$first_two_str);
           return array('code' => FAILURE_CODE, "message" => BURLY_SERIAL_NO_VALIDATION_FAILED_MSG); 
        }
     
        if(($first_two_str==BURLY_CODE) && ($min <= $month_code_str) && ($month_code_str <= $max) && ($current_date>=$month_year))
         {            
                return array('code' => SUCCESS_CODE);
         }
        else 
        {
            return array('code' => FAILURE_CODE, "message" => BURLY_SERIAL_NO_VALIDATION_FAILED_MSG);
        } 
      
    }
    /**
     * @desc
     * @param String $serial_number
     * @return boolean
     */
    function check_duplicate_serial_number($serial_number, $price_tags, $user_id, $booking_id){
        $data = $this->MY_CI->booking_model->get_data_for_duplicate_serial_number_check($serial_number,$booking_id);
//        $data = $this->MY_CI->booking_model->get_unit_details(array('serial_number' => $serial_number, 'booking_status != "'._247AROUND_CANCELLED.'"' => NULL,
//            "price_tags != '".REPEAT_BOOKING_TAG."'" => NULL, "booking_id != '".$booking_id."'" => NULL));
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
                          
                                $msg = " You already used in Booking ID - ".$value['booking_id'];
                                $isDuplicate = TRUE;
                                break;
                            }
                        } else {
                            $msg = " You already used in Booking ID - ".$value['booking_id'];
                            $isDuplicate = TRUE;
                            break;
                        }
                    }
               } else {
                   $msg = " You already used in Booking ID - ".$value['booking_id'];
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
    
/**
     * @desc This method is used to validate Wybor serial number, Wybor has 3 serial number pattern (2015,2016,2017)
     * @param String $partnerID
     * @param String $serialNo
     * @param String $applianceID
     * @return Int
     */
    function wybor_serialNoValidation($partnerID, $serialNo){
        log_message('info', __METHOD__. " Enterring... Partner ID ". $partnerID. " Srial No ". $serialNo);
        $serialNo = strtoupper($serialNo);
        //  2015 Pattern Serial NUmber
        $validation_2015 = $this->_wybor2015pattern($serialNo);
        if(!$validation_2015){
            // 2016 Pattern Validation
            $validation_2016 = $this->_wybor2016pattern($serialNo);
            if(!$validation_2016){
                // 2017 Pattern Validation
               $validation_2017 = $this->_wybor2017pattern($serialNo);
               if($validation_2017){
                   return array('code' => SUCCESS_CODE);
               }
            }
            else{
               return array('code' => SUCCESS_CODE);
            }
        }
        else{
           return array('code' => SUCCESS_CODE);
        }
       return array('code' => FAILURE_CODE, "message" => FAILURE_MSG);
    }
    /**
     * @desc This method is used to validate Wybor serial number 2015 pattern
     * Initial 2 digit will be ME
     * Next 2 digit will be Size (It must be numeric)
     * Next 2 digit represents main board , it must be 2 alphabets 
     * Next 3 digit represents panel , it must be 3 alphanumeric char
     * Next 2 digit represent year , it must be last 2 digit of year
     * Next 2 digit represent weeks, there are 52 weeks in an year , so this value must be less then or equal to 52
     * Last 5 digits represents s.no in week , it must be 5 digit numeric number  
     * @param String $serialNo
     * @return boolean
     */
    function _wybor2015pattern($serialNo){
         $stringLength = strlen($serialNo);
         if($stringLength == 18){
             $startDigit = substr($serialNo,0,2);
             $sizeDigit = substr($serialNo,2,2);
             $mainBoardDigit = substr($serialNo,4,2);
             $panelDigit = substr($serialNo,6,3);
             $yearDigit = substr($serialNo,9,2);
             $weekDigit = substr($serialNo,11,2);
             $snWeekDigit = substr($serialNo,13,5);
             //Starting 2 digit must be ME
             if($startDigit != "ME"){
                 return false;
             }
             //Next 2 digit will be Size (It must be numeric)
             if(!is_numeric($sizeDigit)){
                 return false;
             }
             // Next 2 digit represents main board , it must be 2 alphabets 
             if(!ctype_alpha($mainBoardDigit)){
                  return false;
             }
             //Next 3 digit represents panel , it must be 3 alphanumeric char
             if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $panelDigit)){
                 return false;
             }
             //Next 2 digit represent year , it must be last 2 digit of year
             $current_year = date("y");
             if($yearDigit < 15 && $yearDigit > $current_year){
                  return false;
             }
             //Next 2 digit represent weeks, there are 52 weeks in an year , so this value must be less then or equal to 52
             if($weekDigit > 52){
                  return false;
             }
             //Last 5 digits represents s.no in week , it must be 5 digit numeric number  
             if(!is_numeric($snWeekDigit)){
                 return false;
             }
         }
         else{
             return false;
         }
         return true;
    }
    /**
     * @desc This method is used to validate Wybor serial number 2015 pattern
     * Initial 1 digit will be W or E
     * Next 2 digit will be Panel , It must be alphabets 
     * Next 2 digit represents main board , it must be 2 alphabets 
     * Next 3 digit represents Size and model, Initial 2 digit for Size and last 1 digit for model in particular that model , all 3 should be numeric
     * Next 1 digit represent year , it must be alphabet  , 2018 Will be R
     * Next 1 digit represent Month, it must be alphabet  , A Will be Jan and so on
     * Last 5 digits represents s.no in week , it must be 4 digit numeric number  
     * @param String $serialNo
     * @return boolean
     */
    function _wybor2016pattern($serialNo){
         $stringLength = strlen($serialNo);
         if($stringLength == 14){
             $startDigit = substr($serialNo,0,1);
             $panelDigit = substr($serialNo,1,2);
             $mainBoardDigit = substr($serialNo,3,2);
             $sizeModelDigit = substr($serialNo,5,3);
             $yearDigit = substr($serialNo,8,1);
             $monthDigit = substr($serialNo,9,1);
             $snWeekDigit = substr($serialNo,10,4);
             //Initial 1 digit will be W or E
             if(!($startDigit == "W" || $startDigit == "E")){
                 return false;
             }
             //Next 2 digit will be Panel , It must be alphabets 
              if(!ctype_alpha($panelDigit)){
                 return false;
             }
             //Next 2 digit represents main board , it must be 2 alphabets 
              if(!ctype_alpha($mainBoardDigit)){
                  return false;
             }
             //Next 3 digit represents Size and model, Initial 2 digit for Size and last 1 digit for model in particular that model , all 3 should be numeric
             if(!is_numeric($sizeModelDigit)){
                  return false;
             }
             // Next 1 digit represent year , it must be alphabet  , 2016 Will be P
             $yearMAppingYear = array("2016"=>"P","2017"=>"Q","2018"=>"R","2019"=>"S","2020"=>"T","2021"=>"U","2022"=>"V","2023"=>"W","2024"=>"X","2025"=>"Y","2026"=>"Z");
             $current_year = date("Y");
             $currentYearAlph = $yearMAppingYear[$current_year];
             if($yearDigit < "P" || $yearDigit > $currentYearAlph){
                  return false;
             }
             //Next 1 digit represent Month, it must be alphabet  , A Will be Jan and so on
             $expectedMonthValuesArray = explode(",",MONTH_POSIBLE_VALUES_2016);
             if(!in_array($monthDigit, $expectedMonthValuesArray)){
                 return false;
            }
            //Last 5 digits represents s.no in week , it must be 4 digit numeric number 
             if(!is_numeric($snWeekDigit)){
                  return false;
             }
         }
         else{
             return false;
         }
         return true;
    }
    
    /**
     * @desc This method is used to validate Wybor serial number 2015 pattern
     * Initial 1 digit will be 1 or 3 its represent the warranty
     * Next 1 or  digit will be Brand , Expected Values will be O,B,W,E,BL
     * Next 3 digit represents Size and model , Initial 2 will be size and last one will be model 
     * Next 4 digit represents Panel, It will be alphanumeric
     * Next 4 digit represent main board , it must be alphanumeric 
     * Next 1 digit represent year , it must be alphabet  , 2018 Will be R
     * Next 1 digit represent Month, it must be alphabet  , A Will be Jan and so on
     * Next 2 digit represent date  , It must be less then 31
     * Last 3 digits represents s.no , it must be 3 digit numeric number  
     * @param String $serialNo
     * @return boolean
     */
    function _wybor2017pattern($serialNo){
         $stringLength = strlen($serialNo);
         if($stringLength == 20 || $stringLength == 21){
             $startDigit = substr($serialNo,0,1);
             $brandLength = 1;
             if($stringLength == 21){
                $brandLength = 2;
             }
             $brandDigit = substr($serialNo,1,$brandLength);
             $sizeModelDigit = substr($serialNo,($brandLength+1),3);
             $panelDigit = substr($serialNo,($brandLength+4),4);
             $mainBoardDigit = substr($serialNo,($brandLength+8),4);
             $yearDigit = substr($serialNo,($brandLength+12),1);
             $monthDigit = substr($serialNo,($brandLength+13),1);
             $dateDigit = substr($serialNo,($brandLength+14),2);
             $snDigit = substr($serialNo,($brandLength+16),3);
             //Initial 1 digit will be 1 or 3 its represent the warranty
             if(!($startDigit == 1 || $startDigit == 3)){
                 return false;
             }
             //Next 1 or  digit will be Brand , Expected Values will be O,B,W,E,BL
             $expectedBrandValuesArray = explode(",",BRAND_POSIBLE_VALUES);
             if(!in_array($brandDigit, $expectedBrandValuesArray)){
                 return false;
             }
            // Next 3 digit represents Size and model , Initial 2 will be size and last one will be model 
              if(!is_numeric($sizeModelDigit)){
                 return false;
             }
             //Next 4 digit represents Panel, It will be alphanumeric
              if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $panelDigit)){
                 return false;
             }
             //Next 4 digit represent main board , it must be alphanumeric 
              if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $mainBoardDigit)){
                 return false;
             }
             // Next 1 digit represent year , it must be alphabet  , 2018 Will be R
             $yearMAppingYear = array("2016"=>"P","2017"=>"Q","2018"=>"R","2019"=>"S","2020"=>"T","2021"=>"U","2022"=>"V","2023"=>"W","2024"=>"X","2025"=>"Y","2026"=>"Z");
             $current_year = date("Y");
             $currentYearAlph = $yearMAppingYear[$current_year];
             if($yearDigit < "Q" || $yearDigit > $currentYearAlph){
                  return false;
             }
             //Next 1 digit represent Month, it must be alphabet  , A Will be Jan and so on
             $expectedMonthValuesArray = explode(",",MONTH_POSIBLE_VALUES_2016);
             if(!in_array($monthDigit, $expectedMonthValuesArray)){
                 return false;
            }
            //Next 2 digit represent date  , It must be less then 31
            if($dateDigit >32){
                return false;
            }
            //Last 3 digits represents s.no , it must be 3 digit numeric number  
             if(!is_numeric($snDigit)){
                  return false;
             }
         }
         else{
             return false;
         }
         return true;
    }
    function validate_repeat_booking_serial_number($serialNo,$booking_id){
        $parentBookingSerialNumbers = $this->MY_CI->booking_model->get_parent_booking_serial_number($booking_id,1);
        if($parentBookingSerialNumbers){
            foreach($parentBookingSerialNumbers as $sn){
                if(strtoupper($sn['parent_sn']) == $serialNo){
                     return array('code' => SUCCESS_CODE);
                }
            }
            return array('code' => FAILURE_CODE, "message" => REPEAT_BOOKING_FAILURE_MSG);
        }
        else{
            return false;
        }
    }
    
    function videocon_serialNoValidation($partnerID,$serialNo){
        $stringLength = strlen($serialNo);
         if($stringLength == 18){
             $plantLocation = substr($serialNo,0,2);
             $month = substr($serialNo,2,2);
             $year = substr($serialNo,4,2);
             $productCat = substr($serialNo,6,2);
             $Brand = substr($serialNo,8,2);
             $model = substr($serialNo,10,3);
             $serialNumber = substr($serialNo,13,5);
             //First 2 digit represent $plantLocation, it must be alphanumeric 
              if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $plantLocation)){
                 return false;
             }
             //Next 2 digit represent $month, 
             $expectedMonthValuesArray = explode(",",MONTH_POSIBLE_VALUES);
             if(!in_array($month, $expectedMonthValuesArray)){
                 return false;
            }
            //Next 2 digit represents year it should be numeric
             if(!is_numeric($year)){
                  return false;
             }
             //Next 2 digit represent $month, 
             $productCatArray = explode(",",VIDEOCON_PRODUCT_CAT_POSIBLE_VALUES);
             if(!in_array($productCat, $productCatArray)){
                 return false;
            }
             //Next 2 digit represents $productCat , it must be 2 alphabets 
              if(!ctype_alpha($serialNumber)){
                  return false;
             }
         }
         else{
             return false;
         }
         return true;
    }
    function kenstar_serialNoValidation($partnerID,$serialNo){
        $stringLength = strlen($serialNo);
         if($stringLength == 18){
              return array('code' => SUCCESS_CODE);
         }
         else{
             return array('code' => FAILURE_CODE, "message" => "Serial Number Length should be 18");
         }
    }
}

