<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
ini_set('memory_limit', '1256M');
class File_upload extends CI_Controller {
    
    //global variable
    var $Columfailed = "";
    var $dataToInsert = array();
    var $not_exists_model = array();
    var $not_exists_parts = array();
    
    function __Construct() {
        parent::__Construct();
        
        //load library
        $this->load->library('PHPReport');
        $this->load->library('miscelleneous');
        $this->load->library('notify');
        $this->load->library('table');
        $this->load->library('invoice_lib');
        
        //load model
        $this->load->model('inventory_model');
        $this->load->model('partner_model');
        $this->load->model('employee_model');
        $this->load->model('invoices_model');
        $this->load->model('accounting_model');

        $this->load->helper(array('form', 'url', 'file', 'array'));
    }

    
    /** @desc: This function is used to process the upload file
     * @param: void
     * @return JSON
     */
    public function process_upload_file(){ 
        log_message('info', __FUNCTION__ . "=> File Upload Process Begin ". print_r($_POST,true));
        //get file extension and file tmp name
         
        $file_status = $this->get_upload_file_type();
        $redirect_to = $this->input->post('redirect_url');
        if ($file_status['status']) { 
            
            //get file header
            $data = $this->read_upload_file_header($file_status);
            
            $data['post_data'] = $this->input->post();
            
            if(!empty( $data['post_data']['partner_id'])){
               $data['post_data']['entity_type'] = "partner";
               $data['post_data']['entity_id'] = $data['post_data']['partner_id'];
            }
            else if(!empty( $data['post_data']['vendor_id'])){
                $data['post_data']['entity_type'] = "vendor";
                $data['post_data']['entity_id'] =$data['post_data']['vendor_id'];
            }
            else{
                $data['post_data']['entity_type'] = "";
                $data['post_data']['entity_id'] = "";
            }

            //check all required header and file type 
            if ($data['status']) { 
                $response = array();
                //process upload file
                switch ($data['post_data']['file_type']){ 
                    case PARTNER_INVENTORY_DETAILS_FILE:
                        //process inventory file upload
                        $response = $this->process_inventory_upload_file($data);
                        break;
                    case _247AROUND_PARTNER_APPLIANCE_DETAILS:
                        //process partner appliance file upload
                        $response = $this->process_partner_appliance_upload_file($data);
                        break;
                    case PARTNER_APPLIANCE_MODEL_FILe:
                        //process partner appliance model file upload
                        $response = $this->process_partner_appliance_model_details_file($data);
                        break;
                    case PARTNER_BOM_FILE:
                        //process partner bom file upload
                        $response = $this->process_partner_bom_file_upload($data);
                        break;
                    default :
                        log_message("info"," upload file type not found");
                        $response['status'] = FALSE;
                        $response['message'] = 'Something Went wrong!!!';
                }
                
                //save file into database send send response based on file upload status
                if($response['status']){
                    
                    //save file and upload on s3
                    $this->miscelleneous->update_file_uploads($data['file_name'],TMP_FOLDER.$data['file_name'], $data['post_data']['file_type'],FILE_UPLOAD_SUCCESS_STATUS, "", $data['post_data']['entity_type'], $data['post_data']['entity_id']);
                    
                }else{
                    //save file and upload on s3
                    $this->miscelleneous->update_file_uploads($data['file_name'],TMP_FOLDER.$data['file_name'], $data['post_data']['file_type'],FILE_UPLOAD_FAILED_STATUS, "", $data['post_data']['entity_type'], $data['post_data']['entity_id']);
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
            redirect(base_url() . $redirect_to);
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
    private function read_upload_file_header($file) {
        log_message('info', __FUNCTION__ . "=> getting upload file header");
        try {
            $objReader = PHPExcel_IOFactory::createReader($file['file_ext']);
            $objPHPExcel = $objReader->load($file['file_tmp_name']);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($file['file_tmp_name'], PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        $file_name = $_FILES["file"]["name"];
        move_uploaded_file($file['file_tmp_name'], TMP_FOLDER . $file_name);
        chmod(TMP_FOLDER . $file_name, 0777);
        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $response['status'] = TRUE;
        //Validation for Empty File
        if ($highestRow <= 1) {
            log_message('info', __FUNCTION__ . ' Empty File Uploaded');
            $response['status'] = False;
        }

        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);
        $headings_new = array();
        foreach ($headings as $heading) {
            $heading = str_replace(array("/", "(", ")", "."), "", $heading);
            array_push($headings_new, str_replace(array(" "), "_", $heading));
        }

        $headings_new1 = array_map('strtolower', $headings_new[0]);

        $response['file_name'] = $file_name;
        $response['header_data'] = $headings_new1;
        $response['sheet'] = $sheet;
        $response['highest_row'] = $highestRow;
        $response['highest_column'] = $highestColumn;
        return $response;
    }

    /**
     * @desc: This function is used to process the inventory data 
     * @param $data array  //consist file temporary name, file extension and status(file type is correct or not) and post data from upload form
     * @param $response array  response message and status
     */
    function process_inventory_upload_file($data) {
        log_message('info', __FUNCTION__ . " => process upload inventory file");
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $sheetUniqueRowData = array();
        //$file_appliance_arr = array();
        //column which must be present in the  upload inventory file
        $header_column_need_to_be_present = array('part_name', 'part_number', 'part_type', 'basic_price', 'hsn_code', 'gst_rate', 'around_margin', 'vendor_margin');
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);

        if ($check_header['status']) {
            $invalid_data = array();
            $flag = 1;
            $msg = "";
            //get file data to process
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim', $rowData_array[0]);

                if (!empty(array_filter($sanitizes_row_data))) {
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
               
                    $where['hsn_code'] = $rowData['hsn_code'];
                    $hsncode_data = $this->invoices_model->get_hsncode_details('id,hsn_code,gst_rate', $where);
                                        
                    if(!empty($service_id) && !empty($rowData['part_type'])){
                        $parts_type_details = $this->inventory_model->get_inventory_parts_type_details('*', array('service_id' => $service_id,'part_type'=> ucwords($rowData['part_type'])),false);
                        if(empty($parts_type_details)){
                            if($rowData['around_margin'] > 0 && $rowData['vendor_margin'] > 0){
                                $parts_data['service_id'] = $service_id;
                                $parts_data['part_type'] = ucwords($rowData['part_type']);
                                $parts_data['oow_around_percentage'] = $rowData['around_margin'];
                                $parts_data['oow_vendor_percentage'] = $rowData['vendor_margin'];
                                $parts_data['hsn_code_details_id'] = $hsncode_data[0]['id'];                            
                                if(!empty($parts_data)){
                                    $this->inventory_model->insert_inventory_parts_type($parts_data);
                                }
                            } else {
                                $flag = 0;
                                $msg = "Around & Vendor Margin % should be greater than zero";
                                break;
                            } 
                        }
                    }

                    if ($rowData['gst_rate'] != $hsncode_data[0]['gst_rate']) {
                        $flag = 0;
                        $msg = "GST Rate of HSN Code (" . $rowData['hsn_code'] . ") should be " . $hsncode_data[0]['gst_rate'];
                        break;
                    }                                          
                    if ($flag == 1) {
                        $rowData['service_id'] = $service_id;
                        //array_push($file_appliance_arr, $rowData['appliance']);
                        /*                         * check if part_number value is present or not
                         * if its value is not presnet then create new part number
                         * based on partner_id,service_id and unique number
                         */

                        if (!empty($rowData['hsn_code']) && !empty($rowData['basic_price'])) {
                            if (empty($rowData['part_number'])) {
                                $new_part_number = $this->create_inventory_part_number($partner_id, $service_id, $rowData);
                                $rowData['part_number'] = $new_part_number;
                            }

                            $subArray = $this->get_sub_array($rowData, array('appliance', 'service_id', 'part_name', 'part_number'));
                            array_push($sheetUniqueRowData, implode('_join_', $subArray));
                            $this->sanitize_inventory_data_to_insert($rowData);
                        } else {
                            array_push($invalid_data, array('part_name' => $rowData['part_name'], 'part_number' => $rowData['part_number'], 'hsn_code' => $rowData['hsn_code'], 'basic_price' => $rowData['basic_price']));
                        }
                    }
                }
            }

            if ($flag == 1) {
                $is_file_contains_unique_data = $this->check_unique_in_array_data($sheetUniqueRowData);

                if ($is_file_contains_unique_data['status']) {

                    $insert_id = $this->inventory_model->insert_batch_inventory_master_list_data($this->dataToInsert);
                    if ($insert_id) {
                        log_message("info", __METHOD__ . " inventory file data inserted succcessfully");
                        $response['status'] = TRUE;

                        $message = "Details inserted successfully.";

                        if (!empty($invalid_data)) {
                            $template = array(
                                'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                            );

                            $this->table->set_template($template);

                            $this->table->set_heading(array('Part Name', 'Part Number', 'HSN Code', 'Basic Price'));
                            foreach ($invalid_data as $value) {
                                $this->table->add_row($value['part_name'], $value['part_number'], $value['hsn_code'], $value['basic_price']);
                            }

                            $message .= " Below parts have invalid hsn code or price. Please modify these and upload only below data again: <br>";
                            $message .= $this->table->generate();
                        }

                        $response['message'] = $message;
                    }
                } else {
                    log_message("info", __METHOD__ . $is_file_contains_unique_data['message']);
                    $response['status'] = FALSE;
                    $response['message'] = $is_file_contains_unique_data['message'];
                }
            } else {
                $response['status'] = FALSE;
                $response['message'] = $msg;
            }
        } else {
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
        $tmp_serial_number = $data['part_name'].'-'.$partner_id . "-" . $service_id . "-";
        $old_part_number = $this->inventory_model->get_inventory_master_list_data('part_number', array('part_name' => $data['part_name'], 'entity_id' => $partner_id, 'entity_type' => _247AROUND_PARTNER_STRING));
        
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
                $new_part_number = $tmp_serial_number.($new_serial_num[0] + 1);
                
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
        $tmp_data['serial_number'] = (isset($data['serial_number']) && !empty($data['serial_number'])) ? trim($data['serial_number']):null;
        $tmp_data['type'] = (isset($data['part_type']) && !empty($data['part_type'])) ? trim($data['part_type']):null;
        $tmp_data['size'] = (isset($data['size']) && !empty($data['size'])) ? trim($data['size']):null;
        $tmp_data['price'] = (isset($data['basic_price']) && !empty($data['basic_price'])) ? trim($data['basic_price']):null;
        $tmp_data['hsn_code'] = (isset($data['hsn_code']) && !empty($data['hsn_code'])) ? trim($data['hsn_code']):null;
        $tmp_data['gst_rate'] = (isset($data['gst_rate']) && !empty($data['gst_rate'])) ? trim($data['gst_rate']):null;
        $tmp_data['entity_id'] = $this->input->post('partner_id');
        $tmp_data['entity_type'] = _247AROUND_PARTNER_STRING;
        
        array_push($this->dataToInsert, $tmp_data);
        
    }
    
     /**
     * @desc: This function is used to check duplicate values in the file and remove them 
     * @param $data array() 
     * @param $unique_arr array()
     * @return $response
     */
    function check_unique_in_array_data($data,$unique_arr = null){
        //get unique 
        if(!empty($unique_arr)){
            $is_unique_appliance = count(array_unique($unique_arr));
        }else{
            $is_unique_appliance = 1;
        }
        
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
            $response['message'] = "File contains invalid data. Please check file and upload again.";
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
        log_message('info', __METHOD__);
        $am_email = "";
        if ($this->input->post('partner_id')) {
            $get_partner_am_id = $this->partner_model->getpartner_details('account_manager_id', array('partners.id' => $this->input->post('partner_id')));
            if (!empty($get_partner_am_id[0]['account_manager_id'])) {
                $am_email = $this->employee_model->getemployeefromid($get_partner_am_id[0]['account_manager_id'])[0]['official_email'];
            }
        }
        
        $to = $this->session->userdata('official_email').",".$am_email;
        $agent_name = !empty($this->session->userdata('emp_name')) ? $this->session->userdata('emp_name') : _247AROUND_DEFAULT_AGENT_NAME;

        if ($response['status']) {
            $subject = str_replace('-', ' ', $data['post_data']['file_type']) . " File uploaded by " . $agent_name." successfully.";
        } else {
            $subject = "Failed!!! " . str_replace('-', '', $data['post_data']['file_type']) . " File uploaded by " . $agent_name;
        }
        
        //Getting template from Database
        $template = $this->booking_model->get_booking_email_template("file_upload_email");
        $attachment = TMP_FOLDER.$data['file_name'];
        if (!empty($template)) {
            $body = $response['message'];
            $body .= "<br> <b>File Name</b> " . $data['file_name'];
            
            $sendmail = $this->notify->sendEmail($template[2], $to, $template[3], "", $subject, $body, "", 'inventory_not_found');
            
            if ($sendmail) {
                log_message('info', __FUNCTION__ . 'Mail Send successfully');
            } else {
                log_message('info', __FUNCTION__ . 'Error in Sending Mail');
            }
        }
        if(file_exists($attachment)){
            unlink($attachment);
        }
        
        
    }
    
    /**
     * @desc: This function is used to update model in partner_appliance_details table
     * @param $data array  //consist file temporary name, file extension and status(file type is correct or not) and post data from upload form
     * @param $response array  response message and status
     */
    function process_partner_appliance_upload_file($data) {
        log_message('info', __FUNCTION__ . " => process upload partner appliance file");
        $sheetUniqueRowData = array();
        //column which must be present in the  upload inventory file
        $header_column_need_to_be_present = array('brand', 'category', 'capacity', 'model');
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
        if ($check_header['status']) {

            //get file data to process
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim', $rowData_array[0]);
               
                if (!empty(array_filter($sanitizes_row_data))) {
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    if (!empty($this->input->post('partner_id')) && !empty($this->input->post('service_id')) && !empty($rowData['model'])) { 
                        $aplliance_model_where = array(
                            'service_id' => $this->input->post('service_id'),
                            'model_number' => $rowData['model'],
                            'entity_type' => 'partner',
                            'entity_id' => $this->input->post('partner_id')
                        );
                        $model_detail = $this->inventory_model->get_appliance_model_details("id", $aplliance_model_where);
                        if(empty($model_detail)){
                            $appliance_model_id = $this->inventory_model->insert_appliance_model_data($aplliance_model_where); 
                        }
                        else{
                            $appliance_model_id = $model_detail[0]['id'];
                        }

                        $partner_model_where = array(
                            "partner_id" => $this->input->post('partner_id'),
                            "service_id" => $this->input->post('service_id'),
                            "brand" => $rowData['brand'],
                            "category" => $rowData['category'],
                            "capacity" => $rowData['capacity'],
                            "model" => $appliance_model_id
                        );
                        $partner_model_details = $this->partner_model->get_partner_appliance_details($partner_model_where, 'id');
                        if(empty($partner_model_details)){ 
                            unset($partner_model_where["model"]); 
                            $partner_model_where["(model IS NULL OR model != '".$appliance_model_id."')"] = NULL;
                            $partner_model_details = $this->partner_model->get_partner_appliance_details($partner_model_where, 'id, model');
                            if(!empty($partner_model_details)){
                                if($partner_model_details[0]['model'] == NULL){
                                   $partner_appliance_id = $this->partner_model->update_partner_appliance_details(array("id"=>$partner_model_details[0]['id']), array("model"=>$appliance_model_id));
                                }
                                else if($partner_model_details[0]['model'] != $appliance_model_id){
                                    unset($partner_model_where["(model IS NULL OR model != '".$appliance_model_id."')"]);
                                    $partner_model_where['model'] = $appliance_model_id;
                                    $partner_appliance_id = $this->partner_model->insert_partner_appliance_detail($partner_model_where);
                                }
                            }
                            else{
                                unset($partner_model_where["(model IS NULL OR model != '".$appliance_model_id."')"]);
                                $partner_model_where['model'] = $appliance_model_id;
                                $partner_appliance_id = $this->partner_model->insert_partner_appliance_detail($partner_model_where);
                            }
                        }
                        else{
                           $partner_appliance_id =  $partner_model_details[0]['id'];
                        }
                        
                        if ($partner_appliance_id) {
                            log_message("info", __METHOD__ . " partner appliance file data inserted succcessfully");
                            //check brand_name and service_id is exist in appliance_brand table or not
                            $not_exist_data = $this->booking_model->get_not_exist_appliance_brand_data();
                            if ($not_exist_data) {
                                $this->booking_model->insert_not_exist_appliance_brand_data($not_exist_data);
                                log_message('info', __FUNCTION__ . 'Not exist brand name and service id added into the table appliance_brand');
                            }
                            $response['status'] = TRUE;
                            $response['message'] = "Details inserted successfully.";
                        } else {
                            log_message("info", __METHOD__ . " error in inserting partner appliance file data");
                            $response['status'] = FALSE;
                            $response['message'] = "Something went wrong in inserting data.";
                        }
                    }
                    else {
                        log_message("info", __METHOD__ . " Partner/service/model can not be empty");
                        $response['status'] = FALSE;
                        $response['message'] = "Please Select Partner And Appliance Both OR Model Number can not be empty";
                    }
                }
                else {
                    log_message("info", __METHOD__ . " Partner/service/model can not be empty");
                    $response['status'] = FALSE;
                    $response['message'] = "Please Select Partner And Appliance Both OR Model Number can not be empty";
                }
            }
        } else {
            $response['status'] = FALSE;
            $response['message'] = $check_header['message'];
        }

        return $response;
    }

    /**
     * @desc: This function is used to sanitize partner appliance file data and make final data to insert
     * @param $data array() 
     * @return void
     */
    function sanitize_partner_appliance_data_to_insert($data){
        
        $tmp_data['partner_id'] = $data['partnerid'];
        $tmp_data['service_id'] = trim($data['serviceid']);
        $tmp_data['brand'] = trim($data['brand']);
        $tmp_data['category'] = trim($data['category']);
        $tmp_data['capacity'] = trim($data['capacity']);
        $tmp_data['model'] = trim($data['model']);
        $tmp_data['create_date'] = date('Y-m-d H:i:s');
        
        array_push($this->dataToInsert, $tmp_data);
        
    }
    
    /**
     * @desc: This function is used to update model in appliance_model_details table
     * @param $data array  //consist file temporary name, file extension and status(file type is correct or not) and post data from upload form
     * @param $response array  response message and status
     */
    function process_partner_appliance_model_details_file($data) {
        log_message('info', __FUNCTION__ . " => process upload appliance model file");
        $flag = true;
        $row_number = "";
        $sheet_unique_row_data = array();
        //column which must be present in the  upload inventory file
        $header_column_need_to_be_present = array('appliance', 'model_number');
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
        
        if ($check_header['status']) {
            $services_list = $this->booking_model->selectservice();
            $services = array();
            foreach ($services_list as $value) {
                $services[$value->services] = $value->id;
            }
            //get file data to process
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $row_data_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim', $row_data_array[0]);
                if (!empty(array_filter($sanitizes_row_data))) {
                    $rowData = array_combine($data['header_data'], $row_data_array[0]);
                    $subArray = $this->get_sub_array($rowData, array('appliance', 'model_number'));
                    if(isset($services[trim($rowData['appliance'])]) && !empty($services[trim($rowData['appliance'])])){
                        $rowData['service_id'] = $services[trim($rowData['appliance'])];
                        $rowData['partner_id'] = $this->input->post('partner_id');
                        array_push($sheet_unique_row_data, implode('_join_', $subArray));
                        $this->sanitize_partner_appliance_model_data_to_insert($rowData);
                    }else{
                        $flag = FALSE;
                        $row_number = $row;
                        break;
                    }
                    
                }
            }
            
            if($flag){
                //check file contains unique data
                $is_file_contains_unique_data = $this->check_unique_in_array_data($sheet_unique_row_data);
                if ($is_file_contains_unique_data['status']) {
                    $insert_id = $this->inventory_model->insert_appliance_model_details_batch($this->dataToInsert);
    
                    if ($insert_id) {
                        log_message("info", __METHOD__ . " partner appliance model details file data inserted succcessfully");
                        $response['status'] = TRUE;
                        $response['message'] = "Details inserted successfully.";
                    } else {
                        log_message("info", __METHOD__ . " error in inserting partner appliance file data");
                        $response['status'] = FALSE;
                        $response['message'] = "Something went wrong in inserting data.";
                    }
                } else {
                    log_message("info", __METHOD__ . " " . $is_file_contains_unique_data['message']);
                    $response['status'] = FALSE;
                    $response['message'] = $is_file_contains_unique_data['message'];
                }
            }else{
                log_message("info", __METHOD__ . " " . "Uploaded  file dose not contains right appliance name");
                $response['status'] = FALSE;
                $response['message'] = "Uploaded  file dose not contains right appliance name. Please check spelling in the file at line $row_number";
            }
            
        } else {
            $response['status'] = $check_header['status'];
            $response['message'] = $check_header['message'];
        }
        
        return $response;
    }

    /**
     * @desc: This function is used to sanitize upload file data and make final data to insert in appliance_model_details
     * @param $data array() 
     * @return void
     */
    function sanitize_partner_appliance_model_data_to_insert($data){
        
        $tmp_data['entity_id'] = trim($data['partner_id']);
        $tmp_data['entity_type'] = _247AROUND_PARTNER_STRING;
        $tmp_data['service_id'] = trim($data['service_id']);
        $tmp_data['model_number'] = trim($data['model_number']);
        $tmp_data['create_date'] = date('Y-m-d H:i:s');
        
        array_push($this->dataToInsert, $tmp_data);
    }
    
    
    /**
     * @desc: This function is used to do the inventory and model mapping from file upload
     * @param $data array() 
     * @return $response array()
     */
    function process_partner_bom_file_upload($data) {
        log_message("info", __METHOD__);
        
        $response = array();
        $partner_id = trim($this->input->post('partner_id'));
        if($partner_id){
            $model = $this->inventory_model->get_appliance_model_details('id,model_number',array('entity_id' => trim($this->input->post('partner_id')),'entity_type' => _247AROUND_PARTNER_STRING));
            $part_number = $this->inventory_model->get_inventory_master_list_data('inventory_id,part_number', array('entity_id' => $partner_id, 'entity_type' => _247AROUND_PARTNER_STRING));
            if(!empty($model) && !empty($part_number)){

                $model_arr = array_column($model,'id','model_number');
                $part_number_arr = array_column($part_number,'inventory_id','part_number');
                
                //get file data to process
                for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                    $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                    $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                    //check if model number exist in our database
                    if (!empty(array_filter($sanitizes_row_data))) {
                        
                        if(array_key_exists($sanitizes_row_data[0], $model_arr)){
                            //check part exist in our database
                            $response = $this->process_bom_mapping($model_arr[$sanitizes_row_data[0]], $part_number_arr, $sanitizes_row_data);
                        }else{
                            array_push($this->not_exists_model, $sanitizes_row_data[0]);
                        }
                    }
                }
                
                $not_exist_data_msg = '';
                
                $template = array(
                    'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                );
                
                $this->table->set_template($template);
                //generate not exists model table to send in email
                if (!empty($this->not_exists_model)) {
                    $this->table->set_heading(array('Model Number'));
                    foreach (array_unique($this->not_exists_model) as $value) {
                        $this->table->add_row($value);
                    }

                    $not_exist_data_msg .= " Below models does not exists in our record: <br>";
                    $not_exist_data_msg .= $this->table->generate();
                }
                //generate not exists parts table to send in email
                if (!empty($this->not_exists_parts)) {
                    $this->table->set_heading(array('Part Number'));
                    foreach (array_unique($this->not_exists_parts) as $value) {
                        $this->table->add_row($value);
                    }
                    $not_exist_data_msg .= "<br> Below part number does not exists in our record: <br>";
                    $not_exist_data_msg .= $this->table->generate();
                }
                
                if(!empty($this->dataToInsert)){
                    $insert_data = $this->inventory_model->insert_batch_inventory_model_mapping($this->dataToInsert);
                    if ($insert_data) {
                        log_message("info", __METHOD__ . count($this->dataToInsert). " mapping created succcessfully");
                        $response['status'] = TRUE;
                        $message = "<b>".count($this->dataToInsert)."</b> mapping created successfully.";
                        $response['message'] = $message.' '.$not_exist_data_msg;
                    } else {
                        log_message("info", __METHOD__ . " error in creating mapping.");
                        $response['status'] = FALSE;
                        $response['message'] = "Either mapping already exists or something gone wrong. Please contact 247around developer.";
                    }
                }else{
                    $response['status'] = True;
                    $response['message'] = "File has been uploaded successfully. No New Mapping Created. $not_exist_data_msg";
                }
            }else{
                $response['status'] = FALSE;
                $response['message'] = 'Model and Parts details not found for the selected partner.';
            }
        }else{
            $response['status'] = FALSE;
            $response['message'] = 'Please select correct partner';
        }
        
        return $response;
        
    }
    
    
    /**
     * @desc: This function is used to make the data to do the correct mapping between inventory and model_number 
     * @param $model_number_id integer
     * @param $part_number_arr array() // our database array
     * @param $uploaded_file_parts array()  //uploaded file parts details 
     * @return void
     */
    function process_bom_mapping($model_number_id,$part_number_arr,$uploaded_file_parts){
        //get only parts details from the uploaded file array. remove model number from first index of the array.
        //here we assume that first index of the file is always model number
        unset($uploaded_file_parts[0]);
        foreach ($uploaded_file_parts as $value){
            //check if uploaded part exists in our database
            if (!empty($value)) {
                if(array_key_exists(str_replace(array('"',"'"), "", $value), $part_number_arr)){
                    $tmp = array();
                    $tmp['inventory_id'] = $part_number_arr[str_replace(array('"',"'"), "", $value)];
                    $tmp['model_number_id'] = $model_number_id;
                    array_push($this->dataToInsert, $tmp);
                }else{
                    array_push($this->not_exists_parts, $value);
                }   
            }
        }
    }
    /**
     * @desc This function is used to upload partner serial no file
     */
    function process_upload_serial_number(){
        $partner_id = trim($this->input->post('partner_id'));
        if(!empty($partner_id)){
            $file_status = $this->get_upload_file_type();
            if ($file_status['status']) {
                $data = $this->read_upload_file_header($file_status);
                if ($data['status']) {
                    log_message('info', __METHOD__. " ". print_r($data['header_data'], TRUE));
                    $data['post_data']['file_type'] = $partner_id."_".PARTNER_SERIAL_NUMBER_FILE_TYPE;
                    
                    //column which must be present in the  upload inventory file
                    $header_column_need_to_be_present = array('invoicedate','skuname','skucode','productcategoryname',
                        'brandname', 'modelname', 'colorname', 'stockbin', 'serialnumber');
                    //check if required column is present in upload file header
                    $check_header = $this->check_column_exist($header_column_need_to_be_present,$data['header_data']);
                    if ($check_header['status']) {
                        $existingData = array();
                        $emptyarray = 0;
                        $validData = array();
                        $template = array(
                            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                        );

                        $this->table->set_template($template);

                        $this->table->set_heading(array('Serial Number'));
                       
                        //get file data to process
                        for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                            $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                            $sanitizes_row_data = array_map('trim',$rowData_array[0]);
                            if(!empty(array_filter($sanitizes_row_data))){
                                $rowData = array_combine($data['header_data'], $rowData_array[0]);

                                if(!empty($rowData['serialnumber'])){

                                    $result = $this->partner_model->getpartner_serialno(array('partner_id' =>$partner_id, 'serial_number' => $rowData['serialnumber'], "active" => 1));
                                    if(empty($result)){
                                        $invoiceData = NULL;
                                        if(!empty($rowData['invoicedate'])){
                                            $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData['invoicedate']);
                                            $invoiceData =  $dateObj2->format('Y-m-d');
                                        }
                                        $array = array('partner_id' =>$partner_id,
                                           'serial_number' => $rowData['serialnumber'],
                                           'invoice_date' => $invoiceData,
                                           'sku_name' => $rowData['skuname'],
                                           'sku_code' => $rowData['skucode'],
                                           'category_name' => $rowData['productcategoryname'],
                                           'brand_name' => $rowData['brandname'],
                                           'model_number' => $rowData['modelname'], 
                                           'added_by' => "247around", 
                                           'color' => $rowData['colorname'], 
                                           'stock_bin' => $rowData['stockbin']);
                                       
                                       array_push($validData, $array);
                                    } else {
                                        $this->table->add_row($rowData['serialnumber']);
                                        array_push($existingData, $rowData['serialnumber']);
                                    }
                                } else {
                                    $emptyarray ++;
                                }
                            }
                        }
                       
                        $file_upload_status = FILE_UPLOAD_FAILED_STATUS;
                        if(!empty($validData)){
                            $status =$this->partner_model->insert_partner_serial_number_in_batch($validData);
                            if($status){
                                $response['status'] = TRUE;
                                $response['message'] = "File Successfully uploaded.";
                                $file_upload_status = FILE_UPLOAD_SUCCESS_STATUS;
                                $message = "File Successfully uploaded.";
                                
                            } else {
                                $response['status'] = FALSE;
                                $response['message'] = "File upload Failed.";
                                $message = "File upload Failed. ";
                                
                            }
                        } else {
                            $response['status'] = FALSE;
                            $response['message'] = "File upload Failed. ";
                            $message = "File upload Failed. ";
                        }
                    } else {
                        $response['status'] = FALSE;
                        $response['message'] = "File upload Failed. ".$check_header['message'];
                        $message = "File upload Failed. ".$check_header['message'];
                        
                        
                    }
                } else {
                    $response['status'] = FALSE;
                    $response['message'] = "File upload Failed. Empty file has been uploaded";
                    $message = "File upload Failed. Empty file has been uploaded";
                   
                }
                $this->miscelleneous->update_file_uploads($data['file_name'],TMP_FOLDER.$data['file_name'], 
                    $data['post_data']['file_type'], $file_upload_status, "", "partner", $partner_id);
            
                $this->send_email($data,$response);
            } else {
                
                $message = "File upload Failed. Empty file has been uploaded";
            }
            
        } else {
            
            $message = "Unable to find Partner, Please refresh and try again";
            
        }
        
        echo $message;
    }
    
    /**
     * @desc This function is used to upload docket number file only has two columns awb_number and courier_charges
     */
    function process_docket_number_file_upload(){ 
            $data = array();
            $redirect_to = $this->input->post('redirect_url');
            $file_upload_status = FILE_UPLOAD_FAILED_STATUS;
            $file_status = $this->get_upload_file_type();
            if ($file_status['status']) {
                $data = $this->read_upload_file_header($file_status);
                if ($data['status']) {
                    $data['post_data']['file_type'] = DOCKET_NUMBER_FILE_TYPE;
                    //column which must be present in the  upload inventory file
                    $header_column_need_to_be_present = array('awb_number', 'courier_charges', 'invoice_id', 'courier_name', 'billable_weight', 'actual_weight');
                    
                    //check if required column is present in upload file header
                    $check_header = $this->check_column_exist($header_column_need_to_be_present,$data['header_data']);
                    if ($check_header['status']) {
                        $inValidData = FALSE;
                        $notfoundData = array();
                        $template = array(
                            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                        );
                        $this->table->set_template($template);
                        $this->table->set_heading(array('AWB Number'));
                        //get file data to process
                        for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                            $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                            $sanitizes_row_data = array_map('trim',$rowData_array[0]);
                            if(!empty(array_filter($sanitizes_row_data))){
                                $rowData = array_combine($data['header_data'], $rowData_array[0]);
                                if(!empty($rowData['awb_number']) && !empty($rowData['courier_charges']) && !empty($rowData['invoice_id']) && !empty($rowData['billable_weight']) && !empty($rowData['actual_weight'])){
                                    $courier_company_detail = $this->inventory_model->update_docket_price($rowData);
                                    if(!empty($courier_company_detail['inValidData'])){
                                        $inValidData = TRUE;
                                        $this->table->add_row($courier_company_detail['inValidData']);
                                    }
                                    if(!empty($courier_company_detail['notfoundData'])){
                                        $notfoundData[] = $courier_company_detail['notfoundData'];
                                    }
                                }
                                else{
                                    //log_message('info', __METHOD__. "data not found ". print_r($data['header_data'], TRUE));
                                }
                            }
                        }
                        if($inValidData){
                            $response['status'] = TRUE;
                            $email_message = "Duplicate entry found for below AWB number. Please check : <br>";
                            $email_message .= $this->table->generate();
                            $response['message'] = $email_message;
                            $file_upload_status = FILE_UPLOAD_SUCCESS_STATUS;
                            $returnData['status'] = TRUE;
                            $returnData['message'] = "File Successfully Uploaded.";
                        } 
                        else {
                            $response['status'] = TRUE;
                            $file_upload_status = FILE_UPLOAD_SUCCESS_STATUS;
                            $response['message'] = "Docket Number Successfully Uploaded. ";
                            $returnData['status'] = TRUE;
                            $returnData['message'] = "File Successfully Uploaded.";
                        }
                    } else {
                        $response['status'] = FALSE;
                        $response['message'] = "File upload Failed. ".$check_header['message'];
                        $returnData['status'] = FALSE;
                        $returnData['message'] = "File upload Failed. ".$check_header['message'];
                    }
                } else {
                    $response['status'] = FALSE;
                    $response['message'] = "File upload Failed. Empty file has been uploaded";
                    $returnData['status'] = FALSE;
                    $returnData['message'] = "File upload Failed. Empty file has been uploaded";
                }
                $this->miscelleneous->update_file_uploads($data['file_name'],TMP_FOLDER.$data['file_name'], $data['post_data']['file_type'], $file_upload_status);
                $this->send_email($data,$response);
                if(!empty($notfoundData)){
                    $this->table->set_template($template);
                    $this->table->set_heading(array('AWB Number'));
                    foreach ($notfoundData as $value) {
                        $this->table->add_row($value);
                    }
                    $response['status'] = TRUE;
                    $email_message = "Below AWB number not found. Please check and upload only below data again: <br>";
                    $email_message .= $this->table->generate();
                    $response['message'] = $email_message;
                    $this->send_email($data,$response);
                }
            } else {
                $returnData['status'] = FALSE;
                $returnData['message'] = "File upload Failed. Empty file has been uploaded";
            }
        echo json_encode($returnData);
    }

    /**
     * @desc load upload payment file view
     */
    function upload_payment_file(){
        $this->miscelleneous->load_nav_header();
	$this->load->view('employee/upload_payment_summary_invoice');
    }
    /**
     * @desc: this function used to process sf payment.
     * We will upload custom payment file.
     */
    function process_invoice_payment_file_upload() {
        log_message("info", __METHOD__ . "starting...");
        $file_status = $this->get_upload_file_type();
        $file_upload_status = FILE_UPLOAD_FAILED_STATUS;
        if ($file_status['status']) {
            $sheetRowData = array();
            $invalid_data = array();
            $data = $this->read_upload_file_header($file_status);
            if ($data['status']) {

                $data['post_data']['file_type'] = INVOICE_PAYMENT_FILE_TYPE;
                log_message('info', __METHOD__ . " HEADER " . print_r($data['header_data'], TRUE));

                //column which must be present in the  upload inventory file
                $header_column_need_to_be_present = array('vendor_partner_id', 'vendor_id', 'vendor_name', 'invoice_id',
                    'invoice_date', 'from_date', 'to_date', 'amount_due', 'amount_paid', 'invoice_remarks', 'bank_transaction_id');
                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
                if ($check_header['status']) {

                    for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                        $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                        $sanitizes_row_data = array_map('trim', $rowData_array[0]);

                        if (!empty(array_filter($sanitizes_row_data))) {
                            $rowData = array_combine($data['header_data'], $rowData_array[0]);
                            array_push($sheetRowData, $rowData);
                        } else {
                            $invalid_data['message'] = "Invalid Row Sheet";
                        }
                    }
                    
                    $total_amount = (array_sum(array_column($sheetRowData, 'amount_paid')));
                    $p_amount_paid = $this->input->post("total_amount_paid");
                    if(abs(round($total_amount,0)) == round($p_amount_paid,0) ){
                        $main_data = $this->get_service_center_filtered_data($sheetRowData);
                        log_message('info', __METHOD__. print_r($main_data, true));
                        if($main_data['status']){
                            if(!empty($main_data['data'])){
                                foreach ($main_data['data'] as$value) {
                                    $this->invoice_lib->process_add_new_transaction($value);
                                }
                                
                                $response['status'] = TRUE;
                                $response['message'] = "File Successfully uploaded.";
                                $file_upload_status = FILE_UPLOAD_SUCCESS_STATUS;
                                $message = "File Successfully uploaded.";
                                
                            } else {
                                $response['status'] = FALSE;
                                $response['message'] = "Data Filtered - Empty ";
                                $message = "Payment file upload Failed"; 
                            }
                        } else {
                             $response['status'] = FALSE;
                             if(isset($main_data['message'])){
                                 $response['message'] = $main_data['message'];
                             } else {
                                 $response['message'] = "File upload failed due to row data";
                             }
                             
                             $message = "Payment file upload Failed"; 
                        }

                    } else {
                        $response['status'] = FALSE;
                        $response['message'] = "Total Amount Paid amount is not matching.";
                        $message = "Payment file upload Failed - Total Amount Paid amount is not matching."; 
                    }
                
                } else {
                    $response['status'] = FALSE;
                    $response['message'] = "File upload Failed. ".$check_header['message'];
                    $message = "Payment file upload Failed. ".$check_header['message']; 
                }
                
                $this->miscelleneous->update_file_uploads($data['file_name'],TMP_FOLDER.$data['file_name'], $data['post_data']['file_type'], $file_upload_status);
                $this->send_email($data,$response);
                
            } else {
                $response['status'] = FALSE;
                $response['message'] = "File upload Failed. Empty file has been uploaded";
                $message = "Payment file upload Failed. Empty file has been uploaded";
                
            }
            
            
        } else {
            $message = "Unable to find Partner, Please refresh and try again";
        }
        
        echo $message;
    }
    /**
     * @desc this is used to customized all excel data.
     * All data grouped accordingly SC
     * @param Array $data
     * @return Array
     */
    function get_service_center_filtered_data($data) {
        log_message("info", __METHOD__ . " starting..");
        $main_data = array();
        $v = array();
        $v['status'] = true;
        foreach ($data as $value) {
            if (round(abs($value['amount_due']), 0) >= round($value['amount_paid'], 0)) {
                if($value['amount_paid'] == 0){
                    if(!empty($value['invoice_remarks'])){
                        $this->invoices_model->update_partner_invoices(array('invoice_id' => $value['invoice_id']), array('remarks' => $value['invoice_remarks']));
                    }
                } else {
                    $main_data[$value['vendor_id']]['invoice_id'][$value['invoice_id']]['invoice_id'] = $value['invoice_id'];
                    $main_data[$value['vendor_id']]['invoice_id'][$value['invoice_id']]['pre_credit_amount'] = abs($value['amount_paid']);
                    $main_data[$value['vendor_id']]['invoice_id'][$value['invoice_id']]['credit_debit_amount'] = abs($value['amount_paid']);
                    if ($value['amount_due'] > 0) {
                        $main_data[$value['vendor_id']]['invoice_id'][$value['invoice_id']]['credit_debit'] = 'Credit';
                    } else {
                        $main_data[$value['vendor_id']]['invoice_id'][$value['invoice_id']]['credit_debit'] = 'Debit';
                    }

                    $main_data[$value['vendor_id']]['bankname'] = "";
                    $main_data[$value['vendor_id']]['tdate'] = $this->input->post('transaction_date');
                    $main_data[$value['vendor_id']]['transaction_id'] = $value['bank_transaction_id'];
                    $main_data[$value['vendor_id']]['transaction_mode'] = "Transfer";
                    $main_data[$value['vendor_id']]['agent_id'] = $this->session->userdata('id');
                    $main_data[$value['vendor_id']]['bank_txn_id'] = "";
                    $main_data[$value['vendor_id']]['partner_vendor'] = "vendor";
                    $main_data[$value['vendor_id']]['partner_vendor_id'] = $value['vendor_id'];
                    if (!empty($value['description'])) {
                        $main_data[$value['vendor_id']]['description'] = $value['description'];
                    }
                }
                
            } else {
                $v['status'] = false;
                $v['message'] = array('invoice_id' => "Amount Paid greater than amount due. Invoice ID: - ". $value['invoice_id']);
               
                break;
            }
        }

        log_message('info', __METHOD__ . " DATA " . print_r($main_data, true));
        $v['data'] = $main_data;
        return $v;
    }

}