<?php
/*
 * This class use to check authentication using header for coming request 
 */

class around_generic_lib {
   
    var $My_CI;

    function __Construct() {
    $this->My_CI = & get_instance();

    $this->My_CI->load->library('PHPReport');
    $this->My_CI->load->library('email');
    $this->My_CI->load->library('s3');
    $this->My_CI->load->library('form_validation');
    $this->My_CI->load->library("miscelleneous");
    $this->My_CI->load->library("notify");
    $this->My_CI->load->helper('download');
    $this->My_CI->load->helper(array('form', 'url'));
    $this->My_CI->load->model('employee_model');
    $this->My_CI->load->model('booking_model');
    $this->My_CI->load->model('reporting_utils');
    $this->My_CI->load->model('booking_request_model');
    $this->My_CI->load->model('warranty_model');
    $this->My_CI->load->model('vendor_model');
    $this->My_CI->load->model('indiapincode_model');
    $this->My_CI->load->library('paytm_payment_lib');
    }



    /**
     *  @desc : This function is to get all states.
     *
     *  All the distinct states of India in Ascending order From Table state_code
     *
     *  @param : void
     *  @return : array of states
     *  @author : Abhishek Awasthi
     */


    function  getAllStates(){
        $result = array();
        $response  = $this->My_CI->vendor_model->get_allstates();
        if(!empty($response)){
            $result['data'] = $response;
            $result['message'] = STATES_FOUND_MSG; 
            $result['code'] = STATES_FOUND_MSG_CODE;
        }else{
            $result['data'] = '';
            $result['message'] = STATES_FOUND_MSG_ERR;
            $result['code'] = STATES_FOUND_MSG_ERR_CODE; 
        }
        return $result;

    }




    /**
     *  @desc : This function is to get all cities of state.
     *
     *  
     *
     *  @param : void
     *  @return : array of cities
     *  @author : Abhishek Awasthi
     */


    function getStateCities($state_code){
        $result = array();
        $response  = $this->My_CI->indiapincode_model->getStateCities($state_code);
        if(!empty($response)){
            $result['data'] = $response;
            $result['message'] = CITIES_FOUND_MSG; 
            $result['code'] = SUCCESS_CODE;
        }else{
            $result['data'] = array();
            $result['message'] = CITIES_FOUND_MSG_ERR;
            $result['code'] = CITIES_FOUND_MSG_ERR_CODE; 
        }
        return $result;

    }





}
