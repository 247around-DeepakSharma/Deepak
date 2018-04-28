<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class File_upload extends CI_Controller {
    
    //global variable
    var $Columfailed = "";
    var $dataToInsert = array();
    
    function __Construct() {
        parent::__Construct();
        
        //load library
        $this->load->library('PHPReport');
        $this->load->library('miscelleneous');
        $this->load->library('notify');
        
        //load model
        $this->load->model('inventory_model');
        $this->load->model('partner_model');
        $this->load->model('employee_model');

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
        $redirect_to = $this->input->post('redirect_url');
        
        if ($file_status['status']) {
            //get file header
            $data = $this->read_upload_file_header($file_status);
            $data['post_data'] = $this->input->post();
            
            //check all required header and file type 
            if ($data['status']) {
                //process upload file
                switch ($data['post_data']['file_type']){
                    case PARTNER_INVENTORY_DETAILS_FILE:
                        //process inventory file upload
                        $response = $this->process_inventory_upload_file($data);
                        break;
                    default :
                        log_message("info"," upload file type not found");
                        $response['status'] = FALSE;
                        $response['message'] = 'Something Went wrong!!!';
                }
                
                //save file into database send send response based on file upload status
                if($response['status']){
                    
                    //save file and upload on s3
                    $this->miscelleneous->update_file_uploads($data['file_name'],TMP_FOLDER.$data['file_name'], $data['post_data']['file_type'],FILE_UPLOAD_SUCCESS_STATUS);
                    $this->session->set_flashdata('file_success', $response['message']);
                }else{
                    //save file and upload on s3
                    $this->miscelleneous->update_file_uploads($data['file_name'],TMP_FOLDER.$data['file_name'], $data['post_data']['file_type'],FILE_UPLOAD_FAILED_STATUS);
                    $this->session->set_flashdata('file_error', $response['message']);
                    
                }
                
                //send email
                $this->send_email($data,$response);
                
                redirect(base_url() . $redirect_to);
                

            }else{
                //redirect to upload page
                $this->session->set_flashdata('file_error', 'Empty file has been uploaded');
                redirect(base_url() . $redirect_to);
            }
            
        }else{
            //redirect to upload page
            $this->session->set_flashdata('file_error', 'Empty file has been uploaded');
            redirect(base_url() . "employee/booking_excel/$redirect_to");
        }
    }
    
    
    /**
     * @desc: This function is used to get the file type
     * @param void
     * @param $response array   //consist file temporary name, file extension and status(file type is correct or not)
     */
    private function get_upload_file_type(){
        log_message('info', __FUNCTION__ . "=> getting upload file type");
        if (!empty($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);

            switch ($pathinfo['extension']) {
                case 'xlsx':
                    $response['file_tmp_name'] = $_FILES['file']['tmp_name'];
                    $response['file_ext'] = 'Excel2007';
                    break;
                case 'xls':
                    $response['file_tmp_name'] = $_FILES['file']['tmp_name'];
                    $response['file_ext'] = 'Excel5';
                    break;
            }
            
            $response['status'] =  True;
        } else {
            log_message('info', __FUNCTION__ . ' Empty File Uploaded');
            $response['status'] =  False;
        }
        
        return $response;
    }
    
    /**
     * @desc: This function is used to get the file header
     * @param $file array  //consist file temporary name, file extension and status(file type is correct or not)
     * @param $response array  //consist file name,sheet name(in case of excel),header details,sheet highest row and highest column
     */
    private function read_upload_file_header($file){
        log_message('info', __FUNCTION__ . "=> getting upload file header");
        try {
            $objReader = PHPExcel_IOFactory::createReader($file['file_ext']);
            $objPHPExcel = $objReader->load($file['file_tmp_name']);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($file['file_tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $file_name = $_FILES["file"]["name"];
        move_uploaded_file($file['file_tmp_name'],TMP_FOLDER.$file_name);
        $res1 = 0;
        system("chmod 777" . TMP_FOLDER . $file_name, $res1);

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $response['status'] =  TRUE;
        //Validation for Empty File
        if ($highestRow <= 1) {
            log_message('info', __FUNCTION__ . ' Empty File Uploaded');
            $response['status'] =  False;
        }

        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
        $headings_new = array();
        foreach ($headings as $heading) {
            $heading = str_replace(array("/", "(", ")", "."), "", $heading);
            array_push($headings_new, str_replace(array(" "), "_", $heading));
        }
        
        $headings_new1 = array_map('strtolower', $headings_new[0]);
        
        $response['file_name'] =  $file_name;
        $response['header_data'] =  $headings_new1;
        $response['sheet'] =  $sheet;
        $response['highest_row'] =  $highestRow;
        $response['highest_column'] =  $highestColumn;
        
        return $response;
    }
    
     /**
     * @desc: This function is used to process the inventory data 
     * @param $data array  //consist file temporary name, file extension and status(file type is correct or not) and post data from upload form
     * @param $response array  response message and status
     */
    function process_inventory_upload_file($data){
        log_message('info', __FUNCTION__ . " => process upload inventory file");
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $sheetUniqueRowData = array();
        //$file_appliance_arr = array();
        //column which must be present in the  upload inventory file
        $header_column_need_to_be_present = array('appliance','part_name','part_number','part_description','model_number','price');
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present,$data['header_data']);

        if($check_header['status']){
            
            //get file data to process
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim',$rowData_array[0]);
                
                if(!empty(array_filter($sanitizes_row_data))){
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    $rowData['service_id'] = $service_id;
                    //array_push($file_appliance_arr, $rowData['appliance']);
                    /**check if part_number value is present or not
                     * if its value is not presnet then create new part number
                     * based on partner_id,service_id and unique number
                    */
                    if(empty($rowData['part_number'])){
                        $new_part_number = $this->create_inventory_part_number($partner_id,$service_id,$rowData);
                        $rowData['part_number'] = $new_part_number;
                    }
                    
                    $subArray = $this->get_sub_array($rowData, array('appliance','service_id', 'part_name', 'part_number', 'model_number'));
                    array_push($sheetUniqueRowData, implode('_join_', $subArray));
                    $this->sanitize_inventory_data_to_insert($rowData);
                }
            }
            
            
            $is_file_contains_unique_data = $this->check_unique_inventory_in_array_data($sheetUniqueRowData);
            
            if($is_file_contains_unique_data['status']){
                
                $insert_id = $this->inventory_model->insert_batch_inventory_master_list_data($this->dataToInsert);
            
                if($insert_id){
                    log_message("info", __METHOD__." inventory file data inserted succcessfully");
                    $response['status'] = TRUE;
                    $response['message'] = "Details inserted successfully.";
                }else{
                    log_message("info", __METHOD__." error in inserting inventory file data");
                    $response['status'] = FALSE;
                    $response['message'] = "Something went wrong in inserting data.";
                }
            }else{
                log_message("info", __METHOD__ . $is_file_contains_unique_data['message']);
                $response['status'] = FALSE;
                $response['message'] = $is_file_contains_unique_data['message'];
            }
            
        }else{
            $response['status'] = $check_header['status'];
            $response['message'] = $check_header['message'];
        }
        
        return $response;
    }
    
    /**
     * @desc: This function is used to validate upload file header
     * @param $actual_header array this is actual header. It contains all the required column
     * @param $upload_file_header array this is upload file header. It contains all column from the upload file header
     * @param $return_data array
     */
    function check_column_exist($actual_header, $upload_file_header) {

        $is_all_header_present = array_diff($actual_header, $upload_file_header);
        if (empty($is_all_header_present)) {
            $return_data['status'] = TRUE;
            $return_data['message'] = '';
        } else {
            $this->Columfailed = "<b>" . implode($is_all_header_present, ',') . " </b> column does not exist.Please correct these and upload again. <br><br><b> For reference,Please use previous successfully upload file from CRM</b>";
            $return_data['status'] = FALSE;
            $return_data['message'] = $this->Columfailed;
        }
        
        return $return_data;
    }
    
    
    /**
     * @desc: This function is used to create new part number based on partner_id,service_id and part description
     * @param $partner_id integer partner_id 
     * @param $service_id integer service_id
     * @param $rowData array part description
     * @param $return_data string newly created part number
     */
    function create_inventory_part_number($partner_id, $service_id, $data) {

        $new_part_number = "";
        $tmp_serial_number = $partner_id . "-" . $service_id . "-";
        $old_part_number = $this->inventory_model->get_inventory_master_list_data('part_number', array('model_number' => $data['model_number'], 'part_name' => $data['part_name'], 'entity_id' => $partner_id, 'entity_type' => _247AROUND_PARTNER_STRING));
        
        if (!empty($old_part_number)) {
            $part_number_arr = array_values(array_column($old_part_number, 'part_number'));
            /**check if our custom part number present in the array or not
             * if custom part number present then increase number by one else create new part number
            */
            $is_tmp_serial_number_exists = array_filter($part_number_arr, function($value) use ($tmp_serial_number) { return stripos($value, $tmp_serial_number) !== false;});
            if (!empty($is_tmp_serial_number_exists)) {
                
                $new_serial_num = array();
                foreach ($is_tmp_serial_number_exists as $key => $value) {
                    $old_serial_num = explode($tmp_serial_number,$part_number_arr[$key])[1];
                    array_push($new_serial_num, $old_serial_num);
                }
                
                rsort($new_serial_num);
                $new_part_number = $tmp_serial_number.$new_serial_num[0];
                
            }else{
                $new_part_number = $tmp_serial_number . "1";
            }
        } else {
            $new_part_number = $tmp_serial_number . "1";
        }
        return $new_part_number;
    }
    
    /**
     * @desc: This function is used to get required array from the given array
     * @param $parentArray array() 
     * @param $subsetArrayToGet array() 
     * @return array()
     */
    function get_sub_array(array $parentArray, array $subsetArrayToGet)
    {
        return array_intersect_key($parentArray, array_flip($subsetArrayToGet));
    }
    
    
    /**
     * @desc: This function is used to sanitize file data and make final data to insert
     * @param $data array() 
     * @return void
     */
    function sanitize_inventory_data_to_insert($data){
        
        $tmp_data['service_id'] = $data['service_id'];
        $tmp_data['part_name'] = trim(str_replace(array('"',"'"), "", $data['part_name']));
        $tmp_data['part_number'] = trim(str_replace(array('"',"'"), "", $data['part_number']));
        $tmp_data['description'] = trim(str_replace(array('"',"'"), "", $data['part_description']));
        $tmp_data['model_number'] = trim(str_replace(array('"',"'"), "", $data['model_number']));
        $tmp_data['serial_number'] = (isset($data['serial_number']) && !empty($data['serial_number'])) ? trim($data['serial_number']):null;
        $tmp_data['type'] = (isset($data['part_type']) && !empty($data['part_type'])) ? trim($data['part_type']):null;
        $tmp_data['size'] = (isset($data['size']) && !empty($data['size'])) ? trim($data['size']):null;
        $tmp_data['price'] = (isset($data['price']) && !empty($data['price'])) ? trim($data['price']):null;
        $tmp_data['hsn_code'] = (isset($data['hsn_code']) && !empty($data['hsn_code'])) ? trim($data['hsn_code']):null;
        $tmp_data['entity_id'] = $this->input->post('partner_id');
        $tmp_data['entity_type'] = _247AROUND_PARTNER_STRING;
        
        array_push($this->dataToInsert, $tmp_data);
        
    }
    
     /**
     * @desc: This function is used to check duplicate values in the file and remove them 
     * @param $data array() 
     * @param $file_appliance_arr array()
     * @return $response
     */
    function check_unique_inventory_in_array_data($data){
        //get unique appliance 
        $is_unique_appliance = 1;//count(array_unique($file_appliance_arr));
        //get unique file data
        $arr_duplicates = array_diff_assoc($data, array_unique($data));
        
        //if appliance is not unique return message with duplicate appliance name
        //else if file has unique appliance then check file has unique combination else remove duplicate data from the final data
        if($is_unique_appliance == 1){
            if(empty($arr_duplicates)){
                $response['status'] = TRUE;
                $response['message'] = "";
            }else{
                foreach($arr_duplicates as $key => $value){
                    unset($this->dataToInsert[$key]);
                }
                $response['status'] = TRUE;
                $response['message'] = "";
            }
            
        }else{
            $response['status'] = FALSE;
            $response['message'] = "File Contains Wrong Appliance,Please Select Appliance only from appliance list Or Check the Spelling";
        }
        
        return $response;
    }
    
    /**
     * @desc: This function is used to send email on file upload
     * @param $data array() 
     * @param $response array()
     * @return void
     */
    function send_email($data, $response) {

        if (empty($this->session->userdata('official_email'))) {
            if ($this->input->post('partner_id')) {
                $get_partner_am_id = $this->partner_model->getpartner_details('account_manager_id', array('partners.id' => $this->input->post('partner_id')));
                if (!empty($get_partner_am_id[0]['account_manager_id'])) {
                    $to = $this->employee_model->getemployeefromid($get_partner_am_id[0]['account_manager_id'])[0]['official_email'];
                } else {
                    $to = NITS_ANUJ_EMAIL_ID;
                }
            } else {
                $to = NITS_ANUJ_EMAIL_ID;
            }
        } else {
            $to = $this->session->userdata('official_email');
        }

        $agent_name = !empty($this->session->userdata('emp_name')) ? $this->session->userdata('emp_name') : _247AROUND_DEFAULT_AGENT_NAME;

        if ($response['status']) {
            $subject = $data['post_data']['file_type'] . " File uploaded by " . $agent_name." successfully.";
        } else {
            $subject = "Failed!!! " . $data['post_data']['file_type'] . " File uploaded by " . $agent_name;
        }


        $cc = NITS_ANUJ_EMAIL_ID;
        $body = $response['message'];
        $body .= "<br> <b>File Name</b> " . $data['file_name'];
        $attachment = TMP_FOLDER.$data['file_name'];
        $sendmail = $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $body, $attachment, $data['post_data']['file_type']);

        if ($sendmail) {
            log_message('info', __FUNCTION__ . 'Mail Send successfully');
        } else {
            log_message('info', __FUNCTION__ . 'Error in Sending Mail');
        }
        
        unlink($attachment);
    }

}