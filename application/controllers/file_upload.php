<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class File_upload extends CI_Controller {
    
    function __Construct() {
        parent::__Construct();
        
        //load library
        $this->load->library('PHPReport');
        $this->load->library('form_validation');

        $this->load->helper(array('form', 'url', 'file', 'array'));
    }

    
    /** @desc: This function is used to process the upload file
     * @param: void
     * @return JSON
     */
    public function process_upload_file(){
        log_message('info', __FUNCTION__ . "=> File Upload Process Begin");
        
        //get file extension and file tmp name
        $file_status = $this->get_upload_file_type();
        
        if ($file_status['status']) {
            //get file header
            $header_data = $this->read_upload_file_header($file_status);
            
            //check all required header and file type 
            if ($header_data['status']) {
                //process upload file
                
            }else{
                //redirect to upload page
            }
            
        }else{
            //redirect to upload page
        }
    }
    
    
    /**
     * @desc: This function is used to get the file type
     * @param void
     * @param $response array   //consist file temporary name, file extension and status(file type is correct or not)
     */
    private function get_upload_file_type(){
        
    }
    
    /**
     * @desc: This function is used to get the file header
     * @param $file array  //consist file temporary name, file extension and status(file type is correct or not)
     * @param $response array  //consist file name,sheet name(in case of excel),header details,sheet highest row and highest column
     */
    private function read_upload_file_header($file){
        try {
            //read file
        } catch (Exception $e) {
            //capture error
        }
    }
}