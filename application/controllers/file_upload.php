<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
ini_set('memory_limit', -1);

class File_upload extends CI_Controller {

    //global variable
    var $Columfailed = "";
    var $dataToInsert = array();
    var $not_exists_model = array();
    var $not_exists_parts = array();
    var $remap_bom_array = array();

    function __Construct() {
        parent::__Construct();

        //load library
        $this->load->library('PHPReport');
        $this->load->library('miscelleneous');
        $this->load->library('notify');
        $this->load->library('table');
        $this->load->library('invoice_lib');
        $this->load->library('booking_utilities');

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
    public function process_upload_file() {

        log_message('info', __FUNCTION__ . "=> File Upload Process Begin " . print_r($_POST, true));
        //get file extension and file tmp name
        $file_status = $this->get_upload_file_type();
        $redirect_to = $this->input->post('redirect_url');
        if ($file_status['file_name_lenth']) {
            if ($file_status['status']) {
                //get file header
                $data = $this->read_upload_file_header($file_status);
                $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
                $data['post_data'] = $this->input->post();
                if (!empty($data['post_data']['partner_id'])) {
                    $data['post_data']['entity_type'] = "partner";
                    $data['post_data']['entity_id'] = $data['post_data']['partner_id'];
                } else if (!empty($data['post_data']['vendor_id'])) {
                    $data['post_data']['entity_type'] = "vendor";
                    $data['post_data']['entity_id'] = $data['post_data']['vendor_id'];
                } else {
                    $data['post_data']['entity_type'] = "";
                    $data['post_data']['entity_id'] = "";
                }
                //check all required header and file type 
                if ($data['status']) {
                    $response = array();
                    //process upload file
                    switch ($data['post_data']['file_type']) {
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
                        case ALTERNATE_SPARE_PARTS_MAPPING:
                            //process Alternate Spare Parts 
                            $response = $this->process_upload_alternate_spare_parts($data);
                            break;
                        case MSL_TRANSFERED_BY_PARTNER_BY_EXCEL:
                            //process msl excel  
                            $response = $this->process_msl_upload_file($data);
                            break;
                         case UPLOAD_MSL_EXCEL_FILE:
                            //process upload msl excel  
                            $response = $this->process_upload_msl_file($data);
                            break;
                        case _247AROUND_ENGINEER_NOTIFICATIONS:
                            //process msl excel  
                            $response = $this->process_engg_notification_upload_file($data);
                            break;
                         case UPLOAD_COURIER_SERVICEABLE_AREA_EXCEL_FILE:
                            //process upload courier serviceable area excel  
                            $response = $this->process_upload_courier_serviceable_area_file($data);
                            break;
                        default :
                            log_message("info", " upload file type not found");
                            $response['status'] = FALSE;
                            $response['message'] = 'Something Went wrong!!!';
                    }
                   
                    if ($this->input->post("transfered_by") == MSL_TRANSFERED_BY_WAREHOUSE) {
                        $file_type = $data['post_data']['file_type'] . " by warehouse";
                    } else {
                        $file_type = $data['post_data']['file_type'];
                    }
                    //save file into database send send response based on file upload status  
                    if (isset($response['status']) && ($response['status'])) {
                        
                        //save file and upload on s3
                        $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $file_type, FILE_UPLOAD_SUCCESS_STATUS, "default", $data['post_data']['entity_type'], $data['post_data']['entity_id']);
                        
                        $this->session->set_userdata('file_success', $response['message']);
                    } else {
                        //save file and upload on s3
                        $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $file_type, FILE_UPLOAD_FAILED_STATUS, "", $data['post_data']['entity_type'], $data['post_data']['entity_id']);
                        if ($data['post_data']['file_type'] == UPLOAD_MSL_EXCEL_FILE) {
                            $this->session->set_userdata('fail', $response['message']);
                        } else {
                            if ($data['post_data']['file_type'] == UPLOAD_COURIER_SERVICEABLE_AREA_EXCEL_FILE) {
                                $this->session->set_userdata('fail', $response['message']);
                            } else {
                                $this->session->set_flashdata('file_error', $response['message']);
                            }
                        }
                    }

                     //send email
                    $this->send_email($data, $response);
                    $redirect_to = $response['redirect_to'];
                    if (isset($response['status']) && ($response['status'])) {
                        
                        if ($this->input->post("transfered_by") == MSL_TRANSFERED_BY_WAREHOUSE) {
                            redirect(base_url() . $redirect_to);
                        } else {
                            $this->session->set_userdata('details', $response['message']);
                            if(!empty($redirect_to)){
                                redirect(base_url() . $redirect_to);
                            }
                        }
                    } else {
                      redirect(base_url() . $redirect_to);  
                    }
                } else {
                    //redirect to upload page
                    //$this->session->set_flashdata('file_error', 'Empty file has been uploaded');
                    $this->session->set_userdata('fail', '<h5 style="color:red; margin-left:10px;">Empty file cannot be uploaded.</h5>');
                    redirect(base_url() . $redirect_to);
                }
            } else {
                //redirect to upload page
                $this->session->set_flashdata('file_error', $file_status['message']);
                //redirect(base_url() . $redirect_to);
            }
        } else {
            $this->session->set_flashdata('file_error', $file_status['message']);
            //redirect(base_url() . $redirect_to);
        }
    }

    /**
     * @desc: This function is used to get the file type
     * @param void
     * @param $response array   //consist file temporary name, file extension and status(file type is correct or not)
     */
    private function get_upload_file_type() {
        log_message('info', __FUNCTION__ . "=> getting upload file type");
        if (!empty($_FILES['file']['name']) && strlen($_FILES['file']['name']) > 0 && strlen($_FILES['file']['name']) <= 44) {
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
                if (!empty($response['file_ext'])) {
                    $response['status'] = True;
                    $response['file_name_lenth'] = True;
                    $response['message'] = 'File has been uploaded successfully. ';
                } else {
                    $response['status'] = False;
                    $response['file_name_lenth'] = false;
                    $response['message'] = 'File type is not supported. Allowed extentions are xls or xlsx. ';
                }
            } else {
                log_message('info', __FUNCTION__ . ' Empty File Uploaded');
                $response['status'] = False;
                $response['file_name_lenth'] = True;
                $response['message'] = 'File upload Failed. Empty file has been uploaded';
            }
        } else if (!empty($_FILES['file']['name']) && strlen($_FILES['file']['name']) > 44) {
            log_message('info', __FUNCTION__ . 'File Name Length Is Long');
            $response['status'] = False;
            $response['file_name_lenth'] = false;
            $response['message'] = 'File upload Failed. File name length is long.';
        } else {
            log_message('info', __FUNCTION__ . 'No File Selected!! ');
            $response['status'] = False;
            $response['file_name_lenth'] = True;
            $response['message'] = 'File upload Failed. No File Selected!! ';
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
     * @desc: This function is used to process the engg notifications 
     * @param $data array  //consist file temporary name, file extension and status(file type is correct or not) and post data from upload form
     * @param $response array  response message and status
     */
    function process_engg_notification_upload_file($data) {
        log_message('info', __FUNCTION__ . " => process upload engg notify file");
        $sheetUniqueRowData = array();
        $response = array();
        //column which must be present in the  upload engg file
        $header_column_need_to_be_present = array('phone', 'message');
        $coloumn_need_count = count($header_column_need_to_be_present);
        $excel_col_count = count(array_filter($data['header_data']));
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present, array_filter($data['header_data']));

        if ($check_header['status'] && ($excel_col_count == $coloumn_need_count)) {
            $invalid_data = array();
            $flag = 1;
            $valid_flage = 1;
            $msg = "";
            $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $data['post_data']['file_type'], FILE_UPLOAD_SUCCESS_STATUS, "default", $data['post_data']['entity_type'], $data['post_data']['entity_id']);
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);

                $sanitizes_row_data = array_map('trim', $rowData_array[0]);

                if (!empty(array_filter($sanitizes_row_data))) {
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    // $rowData['agent_id'] = $this->session->userdata('agent_id');;
                }


                $engg_details = $this->engineer_model->get_engineers_details(array('phone' => $rowData['phone']), "*");
                if (!empty($engg_details[0]['device_firebase_token'])) {
                    log_message('info', __FUNCTION__ . 'Notification Data inserted');

                    $url = base_url() . "employee/engineer/send_notication/" . $engg_details[0]['device_firebase_token'];
                    $requestData_post = array(
                        'text' => trim($rowData['message']),
                        'phone' => $rowData['phone']
                    );
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestData_post));
                    $curl_response = curl_exec($ch);
                    curl_close($ch);
                }
            }
            echo 'success';
        } else {
            $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $data['post_data']['file_type'], FILE_UPLOAD_FAILED_STATUS, "", $data['post_data']['entity_type'], $data['post_data']['entity_id']);
            echo 'error';
        }

        exit;

        //  return $response;
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
        $response = array();
        if ($data['saas_module'] == 1) {
            $around_margin = 'partner_margin';
        } else {
            $around_margin = 'around_margin';
        }

        //$file_appliance_arr = array();
        //column which must be present in the  upload inventory file
        $header_column_need_to_be_present = array('part_name', 'part_number', 'part_type', 'basic_price', 'hsn_code', 'gst_rate', $around_margin, 'vendor_margin', 'is_defective_required');
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);

        if ($check_header['status']) {
            $invalid_data = array();
            $flag = 1;
            $valid_flage = 1;
            $msg = "";
            //get file data to process
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim', $rowData_array[0]);

                if (!empty(array_filter($sanitizes_row_data))) {
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    if ($data['saas_module'] == 1) {
                        $margin = $rowData['partner_margin'];
                    } else {
                        $margin = $rowData['around_margin'];
                    }

                    if (!empty($rowData['appliance']) && !empty($rowData['part_name']) && !empty($rowData['part_number']) &&
                            !empty($rowData['part_type']) && !empty($rowData['basic_price']) && ($rowData['basic_price'] > 0) &&
                            (!is_null($margin) && !empty($rowData['is_defective_required']) && ((isset($data['saas_module']) && ($data['saas_module'] == 1)) ? ($margin >= 0) : ($margin > 0)) && $margin <= 30 ) &&
                            (!is_null($rowData['vendor_margin']) && ((isset($data['saas_module']) && ($data['saas_module'] == 1)) ? ($rowData['vendor_margin'] >= 0) : ($rowData['vendor_margin'] > 0)) && $rowData['vendor_margin'] <= 15 ) &&
                            ((isset($data['saas_module']) && ($data['saas_module'] == 1)) ? ($margin >= $rowData['vendor_margin'] || ($margin <= $rowData['vendor_margin'])) : ($margin >= $rowData['vendor_margin']))) {

                        $where['hsn_code'] = trim($rowData['hsn_code']);

                        $hsncode_data = $this->invoices_model->get_hsncode_details('id,hsn_code,gst_rate', $where);

                        if (empty($hsncode_data)) {
                            $hsn_data['hsn_code'] = $rowData['hsn_code'];
                            $hsn_data['gst_rate'] = $rowData['gst_rate'];
                            if ($this->session->userdata("userType") == _247AROUND_PARTNER_STRING) {
                                $hsn_data['agent_id'] = $this->session->userdata('agent_id');
                            } else {
                                $hsn_data['agent_id'] = $this->session->userdata('id');
                            }

                            $hsn_code_details_id = $this->inventory_model->insert_hsn_code_details($hsn_data);
                        } else {
                            $hsn_code_details_id = $hsncode_data[0]['id'];
                        }

                        if (!empty($service_id) && !empty($rowData['part_type'])) {
                            $parts_type_details = $this->inventory_model->get_inventory_parts_type_details('*', array('inventory_parts_type.service_id' => $service_id, 'part_type' => strtoupper($rowData['part_type'])), false);
                            if (empty($parts_type_details)) {
                                $parts_data['service_id'] = $service_id;
                                $parts_data['part_type'] = strtoupper($rowData['part_type']);
                                $parts_data['hsn_code_details_id'] = $hsn_code_details_id;
                                if (!empty($parts_data)) {
                                    $this->inventory_model->insert_inventory_parts_type($parts_data);
                                }
                            }
                            /*
                              if ($rowData['gst_rate'] != $hsncode_data[0]['gst_rate']) {
                              $flag = 0;
                              $msg = "GST Rate of HSN Code (" . $rowData['hsn_code'] . ") should be " . $hsncode_data[0]['gst_rate'];
                              break;
                              } */
                        } else {
                            $flag = 0;
                            $msg = "Around & Vendor Margin % should be greater than zero";
                            break;
                        }

                        if ($flag == 1) {
                            $rowData['service_id'] = $service_id;
                            //array_push($file_appliance_arr, $rowData['appliance']);
                            /*                             * check if part_number value is present or not
                             * if its value is not presnet then create new part number
                             * based on partner_id,service_id and unique number
                             */

                            if (!empty($rowData['hsn_code']) && !empty($rowData['basic_price']) && ((isset($data['saas_module']) && ($data['saas_module'] == 1)) ? ($rowData['around_margin'] >= 0) : ($rowData['around_margin'] > 0)) && ((isset($data['saas_module']) && ($data['saas_module'] == 1)) ? ($rowData['vendor_margin'] >= 0) : ($rowData['vendor_margin'] > 0))) {
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
                    } else {
                        $valid_flage = 0;
                        break;
                    }
                }
            }


            if ($flag == 1) {

                if ($valid_flage == 1) {
                    $is_file_contains_unique_data = $this->check_unique_in_array_data($sheetUniqueRowData);

                    if ($is_file_contains_unique_data['status']) {
                        if (!empty($this->dataToInsert)) {
                            foreach ($this->dataToInsert as $key => $val) {
                                $part_type_return_val = null;
                                $is_part_type_exists = false;

                                if (isset($val['is_return'])) {
                                    $is_part_type_exists = true;
                                    $part_type_return_val = $val['is_return'];
                                    unset($val['is_return']);
                                }

                                $where = array('inventory_master_list.entity_id' => $partner_id, 'inventory_master_list.entity_type' => _247AROUND_PARTNER_STRING, 'part_number' => $val['part_number']); //, 'service_id' => $val['service_id']
                                $select = 'inventory_master_list.type, inventory_master_list.service_id, inventory_master_list.part_name, inventory_master_list.part_number';
                                $inventory_details = $this->inventory_model->get_inventory_master_list_data($select, $where);

                                if (empty($inventory_details)) {
                                    $insert_id = $this->inventory_model->insert_inventory_master_list_data($val);
                                    log_message("info", __METHOD__ . " safdsf " . $insert_id);
                                    if ($insert_id) {
                                        log_message("info", __METHOD__ . " inventory file data inserted succcessfully");
                                        if ($is_part_type_exists) {
                                            log_message("info", __METHOD__ . " adding part type return mapping for the part type ", $val['type']);

                                            $part_type_mapping_data = array();
                                            $tmp_mapping_data = array();

                                            $tmp_mapping_data['partner_id'] = $partner_id;
                                            $tmp_mapping_data['appliance_id'] = $val['service_id'];
                                            $tmp_mapping_data['inventory_id'] = $insert_id;
                                            $tmp_mapping_data['part_type'] = $val['type'];
                                            $tmp_mapping_data['is_return'] = $part_type_return_val;

                                            array_push($part_type_mapping_data, $tmp_mapping_data);

                                            $part_type_mapping_id = $this->inventory_model->insert_part_type_mapping_batch($part_type_mapping_data);

                                            if ($part_type_mapping_id) {
                                                log_message("info", __METHOD__ . " part type mapping added succcessfully for " . $val['type'] . " with mapping id " . $part_type_mapping_id);
                                            } else {
                                                log_message("info", __METHOD__ . " part type mapping not added succcessfully for " . $val['type'] . " with mapping id " . $part_type_mapping_id);
                                            }
                                        } else {
                                            log_message("info", __METHOD__ . " no value provided for part type return for part type " . $val['type']);
                                        }
                                    }
                                } else {
                                    $rows_affected = 0;
                                    if (($inventory_details[0]['service_id'] === $val['service_id'])) {

                                        $inventory_data = array("type" => $val['type'], 'description' => $val['description'], 'hsn_code' => $val['hsn_code'],
                                            'gst_rate' => $val['gst_rate'], 'oow_around_margin' => $val['oow_around_margin'], 'oow_vendor_margin' => $val['oow_vendor_margin']);
                                        if ($data['saas_module'] == 1) {
                                            $inventory_data['price'] = $val['price'];
                                        }
                                        if ($this->session->userdata("userType") == _247AROUND_PARTNER_STRING) {
                                            $inventory_data['agent_id'] = $this->session->userdata('agent_id');
                                        } else {
                                            $inventory_data['agent_id'] = $this->session->userdata('id');
                                        }
                                        $rows_affected = $this->inventory_model->update_inventory_master_list_data($where, $inventory_data);
                                    }
                                    if ($rows_affected > 0) {
                                        log_message("info", __METHOD__ . " Inventory Master List Type updated from " . $inventory_details[0]['type'] . " to " . $val['type'] . " of Part Number -> " . $val['part_number'] . " & Service ID -> " . $val['service_id'] . " .");
                                    }
                                }
                            }
                        }
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
                    } else {
                        log_message("info", __METHOD__ . $is_file_contains_unique_data['message']);
                        $response['status'] = FALSE;
                        $response['message'] = $is_file_contains_unique_data['message'];
                    }
                } else {
                    $response['status'] = false;
                    $response['message'] = 'Excel file details is incorrect.';
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

     * */
    function process_msl_upload_file($data) {
        log_message('info', __FUNCTION__ . " => process upload msl file");
        //  $partner_id = $this->input->post('partner_id');
        //  $service_id = $this->input->post('service_id');
        $action_entity_id = "";
        $action_agent_id = "";
        if ($this->session->userdata('service_center_id')) {
            $agent_id = $this->session->userdata('service_center_id');
            $action_agent_id = $this->session->userdata('id');
            $action_entity_id = $this->session->userdata('service_center_id');
            ;
            $agent_type = _247AROUND_SF_STRING;
        } else if ($this->session->userdata('id')) {
            $agent_id = $this->session->userdata('id');
            $action_agent_id = $this->session->userdata('id');
            $action_entity_id = _247AROUND;
            $agent_type = _247AROUND_EMPLOYEE_STRING;
        } else {
            $agent_id = $this->session->userdata('partner_id');
            $action_agent_id = $this->session->userdata('agent_id');
            $action_entity_id = $this->session->userdata('partner_id');
            $agent_type = _247AROUND_PARTNER_STRING;
        }
        $sheetUniqueRowData = array();
        //$file_appliance_arr = array();
        //column which must be present in the  upload inventory file
        $header_column_need_to_be_present = array('sap_vendor_id', 'part_code', 'vendor_id', 'quantity', 'basic_price', 'hsn_code', 'invoice_id', 'gst_rate', 'from_gst', 'to_gst');
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
        if ($check_header['status']) {
            $invalid_data = array();
            $flag = 1;
            $valid_flage = 1;
            $msg = "";
            $template1 = array(
                'table_open' => '<table border="1" class="table" cellpadding="2" cellspacing="0" class="mytable">'
            );

            $this->table->set_template($template1);
            $this->table->set_heading(array('Part Number', 'Invoice Id', 'HSN Code', 'Error Type'));
            //get file data to process
            $post_data = array();
            $error_type = "";
            $reciver_entity_id = 0;
            $error_array = array();
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                if (!empty(array_filter($sanitizes_row_data))) {
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    if (!empty($rowData['sap_vendor_id']) && !empty($rowData['part_code']) && !empty($rowData['quantity']) && !empty($rowData['basic_price']) && !empty($rowData['hsn_code']) && !empty($rowData['invoice_id']) && !empty($rowData['gst_rate']) && !empty($rowData['from_gst']) && !empty($rowData['to_gst'])) {
                        $select = '*';
                        $where_part = array('part_number' => $rowData['part_code']);
                        $where_in_parts = array();

                        $part_details = $this->inventory_model->get_inventory_master_list_data($select, $where_part, $where_in_parts);
                        if (empty($part_details)) {
                            $error_type = "Part not found in inventory";
                            $error_array[] = $error_type;
                            $this->table->add_row($rowData['part_code'], $rowData['invoice_id'], $rowData['hsn_code'], $error_type);
                        }
                        $from_gst_data = $this->inventory_model->get_entity_gst_data('*', $where = array('gst_number' => $rowData['from_gst']));
                        if (empty($from_gst_data)) {
                            $error_type = "From gst details not found";
                            $error_array[] = $error_type;
                            $this->table->add_row($rowData['part_code'], $rowData['invoice_id'], $rowData['hsn_code'], $error_type);
                        }
                        $to_gst_data = $this->inventory_model->get_entity_gst_data('*', $where = array('gst_number' => $rowData['to_gst']));
                        if (empty($to_gst_data)) {
                            $error_type = "To gst details not found";
                            $error_array[] = $error_type;
                            $this->table->add_row($rowData['part_code'], $rowData['invoice_id'], $rowData['hsn_code'], $error_type);
                        }
                        $wh_details = $this->vendor_model->getVendorContact(trim($rowData['sap_vendor_id']));
                        if (empty($wh_details)) {
                            $error_type = "Warehouse details not found";
                            $error_array[] = $error_type;
                            $this->table->add_row($rowData['part_code'], $rowData['invoice_id'], $rowData['hsn_code'], $error_type);
                        }


                        $invoice_exist = $this->check_invoice_id_exists($rowData['invoice_id']);
                        if ($invoice_exist) {
                            $error_type = "Duplicate Invoice details  found";
                            $error_array[] = $error_type;
                            $this->table->add_row($rowData['part_code'], $rowData['invoice_id'], $rowData['hsn_code'], $error_type);
                        }

                        $vendors_array[] = $rowData['sap_vendor_id'];

                        if (!empty($part_details) && !empty($from_gst_data) && !empty($to_gst_data) && !empty($wh_details) && !empty($part_details)) {

                            $reciver_entity_id = $wh_details[0]['id'];
                            $is_wh_micro = 0;
                            if ($wh_details[0]['is_micro_wh'] == 1) {
                                $is_wh_micro = 2;
                            } else if ($wh_details[0]['is_wh'] == 1) {
                                $is_wh_micro = 1;
                            }

                            $invoice_price = 0;

                            if (isset($post_data[$rowData['invoice_id']])) {

                                $invoice_price = round($invoice_price + $part_details[0]['price'], 2);
                                $part = array(
                                    'shippingStatus' => 1,
                                    'service_id' => $part_details[0]['service_id'],
                                    'part_name' => $part_details[0]['part_name'],
                                    'part_number' => $part_details[0]['part_number'],
                                    'booking_id' => '',
                                    'quantity' => $rowData['quantity'],
                                    'part_total_price' => round($part_details[0]['price'], 2),
                                    'hsn_code' => $rowData['hsn_code'],
                                    'gst_rate' => $rowData['gst_rate'],
                                    'inventory_id' => $part_details[0]['inventory_id'],
                                );
                                if ($rowData['sap_vendor_id'] != $post_data[$rowData['invoice_id']]['wh_id']) {
                                    $error_type = "Duplicate Invoice ID  found for Different Vendors";
                                    $error_array[] = $error_type;
                                    $this->table->add_row($rowData['part_code'], $rowData['invoice_id'], $rowData['hsn_code'], $error_type);
                                }
                                array_push($post_data[$rowData['invoice_id']]['part'], $part);
                            } else {

                                $part = array(
                                    'shippingStatus' => 1,
                                    'service_id' => $part_details[0]['service_id'],
                                    'part_name' => $part_details[0]['part_name'],
                                    'part_number' => $part_details[0]['part_number'],
                                    'booking_id' => '',
                                    'quantity' => $rowData['quantity'],
                                    'part_total_price' => $part_details[0]['price'],
                                    'hsn_code' => $rowData['hsn_code'],
                                    'gst_rate' => $rowData['gst_rate'],
                                    'inventory_id' => $part_details[0]['inventory_id'],
                                );
                                $parts_array[] = $part;
                                $invoice_price = $invoice_price + $part_details[0]['price'];
                                $post_data[$rowData['invoice_id']]['is_wh_micro'] = $is_wh_micro;
                                $post_data[$rowData['invoice_id']]['dated'] = date('Y-m-d H:i:s');
                                $post_data[$rowData['invoice_id']]['invoice_id'] = $rowData['invoice_id'];
                                $post_data[$rowData['invoice_id']]['invoice_amount'] = $invoice_price + (($invoice_price * $rowData['gst_rate']) / 100);
                                $post_data[$rowData['invoice_id']]['courier_name'] = $rowData['courier_name'];
                                $post_data[$rowData['invoice_id']]['awb_number'] = $rowData['awb_number'];
                                $post_data[$rowData['invoice_id']]['courier_shipment_date'] = $rowData['courier_shipment_date'];
                                $post_data[$rowData['invoice_id']]['from_gst_number'] = $from_gst_data[0]['id'];
                                $post_data[$rowData['invoice_id']]['to_gst_number'] = $to_gst_data[0]['id'];
                                $post_data[$rowData['invoice_id']]['wh_id'] = $wh_details[0]['id'];
                                $post_data[$rowData['invoice_id']]['partner_id'] = _247AROUND;
                                $post_data[$rowData['invoice_id']]['partner_name'] = $is_wh_micro;
                                $post_data[$rowData['invoice_id']]['wh_name'] = $wh_details[0]['company_name'];
                                $post_data[$rowData['invoice_id']]['invoice_tag'] = 'MSL';
                                $post_data[$rowData['invoice_id']]['invoice_file'] = false;
                                $post_data[$rowData['invoice_id']]['transfered_by'] = MSL_TRANSFERED_BY_PARTNER;
                                $post_data[$rowData['invoice_id']]['is_defective_part_return_wh'] = 1;
                                $post_data[$rowData['invoice_id']]['part'] = array();
                                array_push($post_data[$rowData['invoice_id']]['part'], $part);
                            }
                        }
                    } else {
                        $error_type = "Error in header";
                        $error_array[] = $error_type;
                        $this->table->add_row($rowData['part_code'], $rowData['invoice_id'], $rowData['hsn_code'], $error_type);
                    }
                }
            }
        } else {
            $this->table->add_row("-", "-", "-", "Excel header is Incorrect");
        }

        $err_msg = $this->table->generate();

        if (empty($error_array)) {

            foreach ($post_data as $post) {

                $post_json = json_encode($post, true);
                $url = base_url() . 'employee/inventory/process_msl_upload_excel';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                $response1 = curl_exec($ch);
                curl_close($ch);
            }


            $response['status'] = TRUE;
            $response['message'] = $err_msg;
            $response['bulk_msl'] = TRUE;
            $response['redirect_to'] = 'inventory/msl_excel_upload';
        } else {
            $response['status'] = FALSE;
            $response['message'] = $err_msg;
            $response['redirect_to'] = 'inventory/msl_excel_upload';
            // $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $data['post_data']['file_type'], FILE_UPLOAD_FAILED_STATUS, "", $data['post_data']['entity_type'], $data['post_data']['entity_id']);
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/msl_excel_upload_errors', $response);
        }


        return $response;
    }

    function check_invoice_id_exists($invoice_id_temp) {
        $res = array();
        if ($invoice_id_temp) {
            $invoice_id = str_replace("/", "-", $invoice_id_temp);
            $count = $this->invoices_model->get_invoices_details(array('invoice_id' => $invoice_id), 'count(invoice_id) as count');
            if (!empty($count[0]['count'])) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
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

     * @desc: This function is used to validate upload file header
     * @param $actual_header array this is actual header. It contains all the required column
     * @param $upload_file_header array this is upload file header. It contains all column from the upload file header
     * @param $return_data array

     * */
function process_upload_msl_file($data) {
        log_message('info', __FUNCTION__ . " => process upload msl file");
        //  $partner_id = $this->input->post('partner_id');
        //  $service_id = $this->input->post('service_id');
        $action_entity_id = "";
        $action_agent_id = "";
        if ($this->session->userdata('service_center_id')) {
            $agent_id = $this->session->userdata('service_center_id');
            $action_agent_id = $this->session->userdata('id');
            $action_entity_id = $this->session->userdata('service_center_id');
            $agent_type = _247AROUND_SF_STRING;
        } else if ($this->session->userdata('id')) {
            $agent_id = $this->session->userdata('id');
            $action_agent_id = $this->session->userdata('id');
            $action_entity_id = _247AROUND;
            $agent_type = _247AROUND_EMPLOYEE_STRING;
        } else {
            $agent_id = $this->session->userdata('partner_id');
            $action_agent_id = $this->session->userdata('agent_id');
            $action_entity_id = $this->session->userdata('partner_id');
            $agent_type = _247AROUND_PARTNER_STRING;
        }
        
        $file_data = array();
        $file_data['invoice_file'] = array(
            'name' => $_FILES['invoice_file']['name'],
            'type' => $_FILES['invoice_file']['type'],
            'tmp_name' => $_FILES['invoice_file']['tmp_name'],
            'error' => $_FILES['invoice_file']['error'],
            'size' => $_FILES['invoice_file']['size']
        );

        $file_data['courier_file'] = array(
            'name' => $_FILES['courier_file']['name'],
            'type' => $_FILES['courier_file']['type'],
            'tmp_name' => $_FILES['courier_file']['tmp_name'],
            'error' => $_FILES['courier_file']['error'],
            'size' => $_FILES['courier_file']['size']
        );

     
        $sheetUniqueRowData = array();
        //$file_appliance_arr = array();
        //column which must be present in the  upload inventory file
        $header_column_need_to_be_present = array('appliance', 'part_code', 'quantity', 'basic_price', 'hsn_code', 'gst_rate');
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
        if ($check_header['status']) {
            $invalid_data = array();
            $flag = 1;
            $valid_flage = 1;
            $msg = "";
            $template1 = array(
                'table_open' => '<table border="1" class="table" cellpadding="2" cellspacing="0" class="mytable">'
            );

            $this->table->set_template($template1);
            $this->table->set_heading(array('Appliance', 'Part Number', 'HSN Code', 'Error Type'));
            //get file data to process
            $post_data = array();
            $error_type = "";
            $reciver_entity_id = 0;
            $error_array = array();
            $invoice_price = 0;
            $invoice_price_with_gst = 0;
            $uniqu_part_number_arr = array();
            $parts_list = array();  
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                if (!empty(array_filter($sanitizes_row_data))) {
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    
                    if (!in_array($rowData['part_code'], $uniqu_part_number_arr)) {
                        array_push($uniqu_part_number_arr, $rowData['part_code']);

                        if (!empty($rowData['appliance']) && !empty($rowData['part_code']) && !empty($rowData['quantity']) && !empty($rowData['basic_price']) && !empty($rowData['hsn_code']) && !empty($rowData['gst_rate'])) {

                            $select = 'services.services, inventory_master_list.inventory_id, inventory_master_list.service_id, inventory_master_list.part_number, inventory_master_list.part_name, inventory_master_list.description, inventory_master_list.size, inventory_master_list.price, inventory_master_list.type, inventory_master_list.oow_vendor_margin, inventory_master_list.oow_around_margin, inventory_master_list.entity_id, inventory_master_list.entity_type, inventory_master_list.hsn_code, inventory_master_list.gst_rate';

                            $part_details = $this->inventory_model->get_inventory_without_model_mapping_data($select, array('inventory_master_list.part_number' => $rowData['part_code'], 'inventory_master_list.entity_id' => $this->input->post("partner_id"), 'inventory_master_list.entity_type' => _247AROUND_PARTNER_STRING), array());
                           
                            if (empty($part_details)) {
                                $error_type = "Part not found in inventory";
                                $error_array[] = $error_type;
                                $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                            }
                            
                            if (!empty($part_details) && strcasecmp($part_details[0]['services'], $rowData['appliance']) != 0) {
                                $error_type = "Appliance name mismatch as our system";
                                $error_array[] = $error_type;
                                $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                            }
                                                       
                            if (!empty($part_details) && $part_details[0]['price'] != $rowData['basic_price']) {
                                $error_type = "Basic price details mismatch";
                                $error_array[] = $error_type;
                                $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                            }

                            if (!empty($part_details) && $part_details[0]['gst_rate'] != $rowData['gst_rate']) {
                                $error_type = "GST details mismatch";
                                $error_array[] = $error_type;
                                $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                            }

                            if (!empty($part_details) && trim($part_details[0]['hsn_code']) != trim($rowData['hsn_code'])) {
                                $error_type = "HSN details mismatch";
                                $error_array[] = $error_type;
                                $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                            }
                            if (!empty($part_details) && $this->input->post("transfered_by") == MSL_TRANSFERED_BY_WAREHOUSE && !empty($rowData['quantity'])) {
                                $this->input->post("sender_entity_type");
                                $this->input->post("sender_entity_id");
                                $quantitytobeship = $rowData['quantity'];
                                $stockavialable = $this->inventory_model->get_inventory_stock_count_details('*', array('inventory_stocks.entity_id' => $this->input->post("sender_entity_id"), 'inventory_id' => $part_details[0]['inventory_id']));
                                if (!empty($stockavialable)) {
                                    $stock_quantity = $stockavialable[0]['stock'];
                                    if ($rowData['quantity'] > $stock_quantity) {
                                        $error_type = "Ship quantity is more than available quantity.";
                                        $error_array[] = $error_type;
                                        $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                                    }
                                } else {
                                    $error_type = "Stock not available.";
                                    $error_array[] = $error_type;
                                    $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                                }
                            }

                            if (!empty($part_details)) {

                                $parts_list[] = array(
                                    'shippingStatus' => 1,
                                    'service_id' => $part_details[0]['service_id'],
                                    'part_name' => $part_details[0]['part_name'],
                                    'part_number' => $part_details[0]['part_number'],
                                    'booking_id' => '',
                                    'quantity' => $rowData['quantity'],
                                    'part_total_price' => round(($part_details[0]['price'] * $rowData['quantity']), 2),
                                    'hsn_code' => $rowData['hsn_code'],
                                    'gst_rate' => $rowData['gst_rate'],
                                    'inventory_id' => $part_details[0]['inventory_id'],
                                );
                                
                               $invoice_price = round(($part_details[0]['price'] * $rowData['quantity']), 2);
                               $invoice_price_with_gst = ($invoice_price_with_gst + ($invoice_price + (($invoice_price * $rowData['gst_rate']) / 100)));
                            }
                        }
                        } else {
                            $error_type = "Error in header Or excel value should not be null.";
                            $error_array[] = $error_type;
                            $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                        }
                    } else {
                        $error_type = "Duplicate part number not allowed ".$rowData['part_code'];
                        $error_array[] = $error_type;
                        $this->table->add_row($rowData['appliance'], $rowData['part_code'], $rowData['hsn_code'], $error_type);
                    }                   
                    
                }
                
            $post_data['appliance']['is_wh_micro'] = $this->input->post("is_wh_micro");
            $post_data['appliance']['dated'] = date('Y-m-d H:i:s');
            $post_data['appliance']['invoice_id'] = $this->input->post("invoice_id");
            
            if(!empty($this->input->post('tcs_rate'))){
              $tcs_rate = $this->input->post('tcs_rate');  
            }else{
             $tcs_rate = 0;    
            }
            
            $tcs_rate_value = (round(($invoice_price_with_gst), 2) * $tcs_rate / 100);
            $invoice_value = (round(($invoice_price_with_gst), 2) + $tcs_rate_value);
            $post_data['appliance']['invoice_amount'] = round(($invoice_value), 2);
            $post_data['appliance']['courier_name'] = $this->input->post("courier_name");
            $post_data['appliance']['awb_number'] = $this->input->post("awb_number");
            $post_data['appliance']['courier_shipment_date'] = $this->input->post("courier_shipment_date");
            $post_data['appliance']['from_gst_number'] = $this->input->post("from_gst_number");
            $post_data['appliance']['to_gst_number'] = $this->input->post("to_gst_number");
            $post_data['appliance']['wh_id'] = $this->input->post("wh_id");
            $post_data['appliance']['partner_id'] = $this->input->post("partner_id");
            $post_data['appliance']['partner_name'] = $this->input->post("partner_name");
            $post_data['appliance']['wh_name'] = $this->input->post("wh_name");
            $post_data['appliance']['invoice_tag'] = 'MSL';
            $post_data['appliance']['tcs_rate'] = $this->input->post('tcs_rate');
            if ($this->input->post("transfered_by") == MSL_TRANSFERED_BY_WAREHOUSE) {
                $post_data['appliance']['transfered_by'] = MSL_TRANSFERED_BY_WAREHOUSE;
                $post_data['appliance']['sender_entity_type'] = $this->input->post("sender_entity_type");
                $post_data['appliance']['sender_entity_id'] = $this->input->post("sender_entity_id");
            } else {
                $post_data['appliance']['transfered_by'] = MSL_TRANSFERED_BY_PARTNER;
            }

            $post_data['appliance']['is_defective_part_return_wh'] = 1;
            $post_data['appliance']['files'] = $file_data;
            $post_data['appliance']['part'] = $parts_list;
            $session = array();
            if ($this->session->userdata('service_center_id')) {
                $session['session_id'] = $this->session->userdata("session_id");
                $session['ip_address'] = $this->session->userdata("ip_address");
                $session['user_agent'] = $this->session->userdata("user_agent");
                $session['last_activity'] = $this->session->userdata("last_activity");
                $session['user_data'] = $this->session->userdata("user_data");
                $session['service_center_id'] = $this->session->userdata("service_center_id");
                $session['service_center_name'] = $this->session->userdata("service_center_name");
                $session['service_center_agent_id'] = $this->session->userdata("service_center_agent_id");
                $session['is_upcountry'] = $this->session->userdata("is_upcountry");
                $session['is_update'] = $this->session->userdata("is_update");
                $session['is_engineer_app'] = $this->session->userdata("is_engineer_app");
                $session['sess_expiration'] = $this->session->userdata("sess_expiration");
                $session['loggedIn'] = $this->session->userdata("loggedIn");
                $session['userType'] = $this->session->userdata("userType");
                $session['is_sf'] = $this->session->userdata("is_sf");
                $session['is_cp'] = $this->session->userdata("is_cp");
                $session['is_wh'] = $this->session->userdata("is_wh");
                $session['wh_name'] = $this->session->userdata("wh_name");
                $session['is_gst_exist'] = $this->session->userdata("is_gst_exist");
                $session['is_micro_wh'] = $this->session->userdata("is_micro_wh");
                $session['poc_email'] = $this->session->userdata("poc_email");
                $session['agent_name'] = $this->session->userdata("agent_name");
                $session['covid_popup'] = $this->session->userdata("covid_popup");
            } else {
                $session['session_id'] = $this->session->userdata("session_id");
                $session['ip_address'] = $this->session->userdata("ip_address");
                $session['user_agent'] = $this->session->userdata("user_agent");
                $session['last_activity'] = $this->session->userdata("last_activity");
                $session['user_data'] = $this->session->userdata("user_data");
                $session['employee_id'] = $this->session->userdata("employee_id");
                $session['id'] = $this->session->userdata("id");
                $session['phone'] = $this->session->userdata("phone");
                $session['sess_expiration'] = $this->session->userdata("sess_expiration");
                $session['loggedIn'] = $this->session->userdata("loggedIn");
                $session['userType'] = $this->session->userdata("userType");
                $session['user_group'] = $this->session->userdata("user_group");
                $session['official_email'] = $this->session->userdata("official_email");
                $session['emp_name'] = $this->session->userdata("emp_name");
                $session['is_am'] = $this->session->userdata("is_am");
                $session['user_source'] = $this->session->userdata("user_source");
                $session['warehouse_id'] = $this->session->userdata("warehouse_id");
            }
            
            $post_data['appliance']['session'] = $session;
            
        } else {
            $this->table->add_row("-", "-", "-", "Excel header is Incorrect");
        }
        
        $err_msg = $this->table->generate();
        
        if (empty($error_array)) {
            if (!empty($post_data)) {
                $post_json = json_encode($post_data['appliance'], true);
                $url = base_url() . 'employee/inventory/process_msl_upload_excel';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                $response1 = curl_exec($ch);
                curl_close($ch);
            }
            
            echo $response1;
           
            $response['status'] = TRUE;
            $response['message'] = $err_msg;
            $response['bulk_msl'] = TRUE;
            if ($this->input->post("transfered_by") == MSL_TRANSFERED_BY_WAREHOUSE) {
                $response['redirect_to'] = 'service_center/upload_msl_excel_file';
                //redirect(base_url() . "service_center/upload_msl_excel_file");
            } else {
                $response['redirect_to'] = 'employee/inventory/upload_msl_excel_file';
            }
            
        } else {
            $response['status'] = FALSE;
            $response['message'] = $err_msg;
            if ($this->input->post("transfered_by") == MSL_TRANSFERED_BY_WAREHOUSE) {
                $response['redirect_to'] = 'service_center/upload_msl_excel_file';
            } else {
                $response['redirect_to'] = 'employee/inventory/upload_msl_excel_file';
            }
            // $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $data['post_data']['file_type'], FILE_UPLOAD_FAILED_STATUS, "", $data['post_data']['entity_type'], $data['post_data']['entity_id']);
            //$this->miscelleneous->load_nav_header();
//            if ($this->input->post("transfered_by") == MSL_TRANSFERED_BY_WAREHOUSE) {
//                $this->load->view('employee/msl_excel_upload_errors', $response); 
//                //$this->load->view('service_center/msl_excel_upload_errors', $response);
//            } else {
//               $this->load->view('employee/msl_excel_upload_errors', $response); 
//            }
        }

        return $response;
    }
    
       
    /**

     * @desc: This function is used to validate upload file header, courier serviceable area.
     * @param $actual_header array this is actual header. It contains all the required column
     * @param $upload_file_header array this is upload file header. It contains all column from the upload file header
     * @param $return_data array

     * */
    function process_upload_courier_serviceable_area_file($data) {
        log_message('info', __FUNCTION__ . " => process upload msl file");

        if ($this->session->userdata('service_center_id')) {
            $agent_id = $this->session->userdata('service_center_id');
            $action_agent_id = $this->session->userdata('id');
            $action_entity_id = $this->session->userdata('service_center_id');
            $agent_type = _247AROUND_SF_STRING;
        } else if ($this->session->userdata('id')) {
            $agent_id = $this->session->userdata('id');
            $action_agent_id = $this->session->userdata('id');
            $action_entity_id = _247AROUND;
            $agent_type = _247AROUND_EMPLOYEE_STRING;
        } else {
            $agent_id = $this->session->userdata('partner_id');
            $action_agent_id = $this->session->userdata('agent_id');
            $action_entity_id = $this->session->userdata('partner_id');
            $agent_type = _247AROUND_PARTNER_STRING;
        }

        $sheetUniqueRowData = array();
        $header_column_need_to_be_present = array('courier_company_name', 'pincode');
        //check if required column is present in upload file header
        $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
       
        if ($check_header['status']) {
            
            $flag = 1;
            $valid_flage = 1;
            $msg = "";
            $template1 = array(
                'table_open' => '<table border="1" class="table" cellpadding="2" cellspacing="0" class="mytable">'
            );

            $this->table->set_template($template1);
            $this->table->set_heading(array('Courier Name', 'Pincode', 'Error Type'));
            //get file data to process
            $error_type = "";
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                if (!empty($sanitizes_row_data)) {
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    if (!empty($rowData['courier_company_name']) && !empty($rowData['pincode'])) {
                        $select = 'courier_serviceable_area.id, courier_serviceable_area.courier_company_name, courier_serviceable_area.pincode';
                        $courier_details = $this->inventory_model->get_generic_table_details('courier_serviceable_area', $select, array('courier_serviceable_area.courier_company_name' => trim($rowData['courier_company_name']), 'courier_serviceable_area.pincode' => trim($rowData['pincode'])), '');
                        if (!empty($courier_details)) {
                            $error_type = "Already Exists In Our System.";
                            $this->table->add_row($rowData['courier_company_name'], $rowData['pincode'], $error_type);
                        } else {
                            
                            if(preg_match("/[^a-zA-Z0-9 ]+/", trim($rowData['courier_company_name']))){
                                 $error_type = "Courier company name should not be special character.";
                                 $this->table->add_row($rowData['courier_company_name'], $rowData['pincode'], $error_type);
                            } 
                            
                            if(strlen(trim($rowData['courier_company_name'])) > 20){
                               $error_type = "Courier company name should not be more than 20 characters.";
                                 $this->table->add_row($rowData['courier_company_name'], $rowData['pincode'], $error_type); 
                            }
                            
                            if(!preg_match('/^[0-9]{6}$/', $rowData['pincode']) ){
                                $error_type = " Pincode should be of 6 digit number.";
                                $this->table->add_row($rowData['courier_company_name'], $rowData['pincode'], $error_type);
                            } 
                            $tmp_data['courier_company_name'] = trim($rowData['courier_company_name']);
                            $tmp_data['pincode'] = trim($rowData['pincode']);
                            array_push($this->dataToInsert, $tmp_data);
                        }
                    } else {
                        $error_type = "Error in header Or excel value should not be blank.";
                        $this->table->add_row($rowData['courier_company_name'], $rowData['pincode'], $error_type);
                    }
                }
            }
        } else {
            $error_type = "Error in header Or excel value should not be blank.";
            $this->table->add_row(" ", " ", " ", $error_type);
        }
        $err_msg = $this->table->generate();
                
        if (empty($error_type)) {
            if (!empty($this->dataToInsert)){
            $this->inventory_model->insert_courier_serviceable_area_details_batch($this->dataToInsert);
        }
            $response['status'] = TRUE;
            $response['message'] = 'Courier serviceable area file uploaded successfully';
            $response['redirect_to'] = 'employee/inventory/upload_courier_serviceable_area_file';
        } else {
            $response['status'] = FALSE;
            $response['message'] = $err_msg;
            $response['redirect_to'] = 'employee/inventory/upload_courier_serviceable_area_file';
        }

        return $response;
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
        $tmp_serial_number = $data['part_name'] . '-' . $partner_id . "-" . $service_id . "-";
        $old_part_number = $this->inventory_model->get_inventory_master_list_data('part_number', array('part_name' => $data['part_name'], 'entity_id' => $partner_id, 'entity_type' => _247AROUND_PARTNER_STRING));

        if (!empty($old_part_number)) {
            $part_number_arr = array_values(array_column($old_part_number, 'part_number'));
            /*             * check if our custom part number present in the array or not
             * if custom part number present then increase number by one else create new part number
             */
            $is_tmp_serial_number_exists = array_filter($part_number_arr, function($value) use ($tmp_serial_number) {
                return stripos($value, $tmp_serial_number) !== false;
            });
            if (!empty($is_tmp_serial_number_exists)) {

                $new_serial_num = array();
                foreach ($is_tmp_serial_number_exists as $key => $value) {
                    $old_serial_num = explode($tmp_serial_number, $part_number_arr[$key])[1];
                    array_push($new_serial_num, $old_serial_num);
                }

                rsort($new_serial_num);
                $new_part_number = $tmp_serial_number . ($new_serial_num[0] + 1);
            } else {
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
    function get_sub_array(array $parentArray, array $subsetArrayToGet) {
        return array_intersect_key($parentArray, array_flip($subsetArrayToGet));
    }

    /**
     * @desc: This function is used to sanitize file data and make final data to insert
     * @param $data array() 
     * @return void
     */
    function sanitize_inventory_data_to_insert($data) {

        $tmp_data['service_id'] = $data['service_id'];
        $tmp_data['part_name'] = trim(str_replace(array('"', "'"), "", $data['part_name']));
        $tmp_data['part_number'] = trim(str_replace(array('"', "'"), "", $data['part_number']));
        $tmp_data['description'] = trim(str_replace(array('"', "'"), "", $data['part_description']));
//        $tmp_data['serial_number'] = (isset($data['serial_number']) && !empty($data['serial_number'])) ? trim($data['serial_number']):null;
        $tmp_data['type'] = (isset($data['part_type']) && !empty($data['part_type'])) ? trim($data['part_type']) : null;
        $tmp_data['size'] = (isset($data['size']) && !empty($data['size'])) ? trim($data['size']) : null;
        $tmp_data['price'] = (isset($data['basic_price']) && !empty($data['basic_price'])) ? trim($data['basic_price']) : null;
        $tmp_data['hsn_code'] = (isset($data['hsn_code']) && !empty($data['hsn_code'])) ? trim($data['hsn_code']) : null;
        $tmp_data['gst_rate'] = (isset($data['gst_rate']) && !empty($data['gst_rate'])) ? trim($data['gst_rate']) : null;
        
        if ($this->session->userdata('userType') == _247AROUND_PARTNER_STRING && $this->session->userdata('partner_id')) {
            if ($this->session->userdata('partner_id') == VIDEOCON_ID) {
                $tmp_data['oow_vendor_margin'] = 10;
                $tmp_data['oow_around_margin'] = 15;
            } else {
                $tmp_data['oow_vendor_margin'] = 15;
                $tmp_data['oow_around_margin'] = 15;
            }
        } else {
            $tmp_data['oow_vendor_margin'] = (isset($data['vendor_margin']) && !is_null($data['vendor_margin'])) ? trim($data['vendor_margin']) : REPAIR_OOW_VENDOR_PERCENTAGE;
            $tmp_data['oow_around_margin'] = (isset($data['around_margin']) && !is_null($data['around_margin'])) ? trim($data['around_margin']) : (REPAIR_OOW_AROUND_PERCENTAGE * 100);
        }

        $tmp_data['entity_id'] = $this->input->post('partner_id');
        $tmp_data['entity_type'] = _247AROUND_PARTNER_STRING;

        if (isset($data['is_part_return'])) {
            $tmp_data['is_return'] = (strtolower($data['is_part_return']) == PART_TYPE_RETURN_MAPPING_FILE_VALUE) ? 1 : 0;
        }

        if(strtolower($data['is_defective_required']) == 'yes') {
            $tmp_data['is_defective_required'] = 1;
        } else {
            $tmp_data['is_defective_required'] = 0;
        }
        
        if ($this->session->userdata("userType") == _247AROUND_PARTNER_STRING) {
            $tmp_data['agent_id'] = $this->session->userdata('agent_id');
        } else {
            $tmp_data['agent_id'] = $this->session->userdata('id');
        }
        
        array_push($this->dataToInsert, $tmp_data);
    }

    /**
     * @desc: This function is used to check duplicate values in the file and remove them 
     * @param $data array() 
     * @param $unique_arr array()
     * @return $response
     */
    function check_unique_in_array_data($data, $unique_arr = null) {
        //get unique 
        if (!empty($unique_arr)) {
            $is_unique_appliance = count(array_unique($unique_arr));
        } else {
            $is_unique_appliance = 1;
        }

        //get unique file data
        $arr_duplicates = array_diff_assoc($data, array_unique($data));

        //if appliance is not unique return message with duplicate appliance name
        //else if file has unique appliance then check file has unique combination else remove duplicate data from the final data
        if ($is_unique_appliance == 1) {
            if (empty($arr_duplicates)) {
                $response['status'] = TRUE;
                $response['message'] = "";
            } else {
                foreach ($arr_duplicates as $key => $value) {
                    unset($this->dataToInsert[$key]);
                }
                $response['status'] = TRUE;
                $response['message'] = "";
            }
        } else {
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
            //$get_partner_am_id = $this->partner_model->getpartner_details('account_manager_id', array('partners.id' => $this->input->post('partner_id')));
            $get_partner_am_id = $this->partner_model->getpartner_data("group_concat(distinct agent_filters.agent_id) as account_manager_id",
                    array('partners.id' => $this->input->post('partner_id')), "", 0, 1, 1, "partners.id");
            if (!empty($get_partner_am_id[0]['account_manager_id'])) {
                //$am_email = $this->employee_model->getemployeefromid($get_partner_am_id[0]['account_manager_id'])[0]['official_email'];
                $am_email = $this->employee_model->getemployeeMailFromID($get_partner_am_id[0]['account_manager_id'])[0]['official_email'];
            }
        }

        $to = $this->session->userdata('official_email') . "," . $am_email;
        $agent_name = !empty($this->session->userdata('emp_name')) ? $this->session->userdata('emp_name') : _247AROUND_DEFAULT_AGENT_NAME;

        if ($response['status']) {
            $subject = str_replace('-', ' ', $data['post_data']['file_type']) . " File uploaded by " . $agent_name . " successfully.";
        } else {
            $subject = "Failed!!! " . str_replace('-', '', $data['post_data']['file_type']) . " File uploaded by " . $agent_name;
        }

        //Getting template from Database
        $template = $this->booking_model->get_booking_email_template("file_upload_email");
        $attachment = TMP_FOLDER . $data['file_name'];
        if (!empty($template)) {
            $body = $response['message'];
            if (!empty($response['data'])) {
                $body .= "<br> " . $response['data'];
            }
            $body .= "<br> <b>File Name</b> " . $data['file_name'];

            $sendmail = $this->notify->sendEmail($template[2], $to, $template[3], "", $subject, $body, $attachment, 'inventory_not_found');

            if ($sendmail) {
                log_message('info', __FUNCTION__ . 'Mail Send successfully');
            } else {
                log_message('info', __FUNCTION__ . 'Error in Sending Mail');
            }
        }
        if (file_exists($attachment)) {
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
            $nonValidData = array();
            //get file data to process
            for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                if (!empty(array_filter($sanitizes_row_data))) {
                    $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    if (!empty($this->input->post('partner_id')) && !empty($this->input->post('service_id')) && !empty($rowData['model'])) {
                        $validate_model_number = true;
                        if (strpos($rowData['model'], "'")) {
                            $validate_model_number = false;
                        } else if (strpos($rowData['model'], '"')) {
                            $validate_model_number = false;
                        }else if (strpos($rowData['model'], '”')) {
                            $validate_model_number = false;
                        }else if (strpos($rowData['model'], "’")) {
                            $validate_model_number = false;
                        }
                        if ($validate_model_number) {
                            $partner_model_id = "";
                            $model_description = "";
                            $partner_brand_id = "";
                            if (isset($rowData['partner_model_id'])) {
                                $partner_model_id = $rowData['partner_model_id'];
                            }

                            if (isset($rowData['model_description'])) {
                                $model_description = $rowData['model_description'];
                            }

                            if (isset($rowData['partner_brand_id'])) {
                                $partner_brand_id = $rowData['partner_brand_id'];
                            }

                            $aplliance_model_where = array(
                                'service_id' => $this->input->post('service_id'),
                                'model_number' => $rowData['model'],
                                'entity_type' => 'partner',
                                'entity_id' => $this->input->post('partner_id')
                            );
                            $model_detail = $this->inventory_model->get_appliance_model_details("id", $aplliance_model_where);
                            if (empty($model_detail)) {
                                $aplliance_model_where["partner_model_id"] = $partner_model_id;
                                $aplliance_model_where["model_description"] = $model_description;
                                $appliance_model_id = $this->inventory_model->insert_appliance_model_data($aplliance_model_where);
                            } else {
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
                            if (empty($partner_model_details)) {
                                // ---------------------------------------------------------------------
                                // Check case if model exists with some other capacity/category
                                // update data in this case
                                // Added by Prity Sharma on 01-07-2019
                                $partner_model_where_without_category_capacity = array(
                                    "partner_id" => $this->input->post('partner_id'),
                                    "service_id" => $this->input->post('service_id'),
                                    "brand" => $rowData['brand'],
                                    "model" => $appliance_model_id
                                );
                                $partner_model_details = $this->partner_model->get_partner_appliance_details($partner_model_where_without_category_capacity, 'id, model');
                                if (!empty($partner_model_details)) {
                                    $partner_appliance_id = $this->partner_model->update_partner_appliance_details(array("id" => $partner_model_details[0]['id']), array("category" => $partner_model_where["category"], 'capacity' => $partner_model_where["capacity"]));
                                } else {
                                    // ----------------------------------------------------------------------
                                    unset($partner_model_where["model"]);
                                    $partner_model_where["(model IS NULL OR model != '" . $appliance_model_id . "')"] = NULL;
                                    $partner_model_details = $this->partner_model->get_partner_appliance_details($partner_model_where, 'id, model');
                                    if (!empty($partner_model_details)) {
                                        if ($partner_model_details[0]['model'] == NULL) {
                                            $partner_appliance_id = $this->partner_model->update_partner_appliance_details(array("id" => $partner_model_details[0]['id']), array("model" => $appliance_model_id));
                                        } else if ($partner_model_details[0]['model'] != $appliance_model_id) {
                                            unset($partner_model_where["(model IS NULL OR model != '" . $appliance_model_id . "')"]);
                                            $partner_model_where['model'] = $appliance_model_id;
                                            $partner_model_where['partner_brand_id'] = $partner_brand_id;
                                            $partner_appliance_id = $this->partner_model->insert_partner_appliance_detail($partner_model_where);
                                        }
                                    } else {
                                        unset($partner_model_where["(model IS NULL OR model != '" . $appliance_model_id . "')"]);
                                        $partner_model_where['model'] = $appliance_model_id;
                                        $partner_model_where['partner_brand_id'] = $partner_brand_id;
                                        $partner_appliance_id = $this->partner_model->insert_partner_appliance_detail($partner_model_where);
                                    }
                                }
                            } else {
                                $partner_appliance_id = $partner_model_details[0]['id'];
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
                        } else {
                            array_push($nonValidData, $rowData['model']);
                        }
                    } else {
                        log_message("info", __METHOD__ . " Partner/service/model can not be empty");
                        $response['status'] = FALSE;
                        $response['message'] = "Please Select Partner And Appliance Both OR Model Number can not be empty";
                    }
                } else {
                    log_message("info", __METHOD__ . " Partner/service/model can not be empty");
                    $response['status'] = FALSE;
                    $response['message'] = "Please Select Partner And Appliance Both OR Model Number can not be empty";
                }
            }
            if (!empty($nonValidData)) {
                $model_string = "";
                foreach ($nonValidData as $model_number) {
                    $model_string = $model_number . ", " . $model_string;
                }
                $response['status'] = FALSE;
                $response['message'] = "Model Number has quotes, Please remove and upload again - " . rtrim($model_string, ' ,');
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
    function sanitize_partner_appliance_data_to_insert($data) {

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
                    if (isset($services[trim($rowData['appliance'])]) && !empty($services[trim($rowData['appliance'])])) {
                        $rowData['service_id'] = $services[trim($rowData['appliance'])];
                        $rowData['partner_id'] = $this->input->post('partner_id');
                        array_push($sheet_unique_row_data, implode('_join_', $subArray));
                        $this->sanitize_partner_appliance_model_data_to_insert($rowData);
                    } else {
                        $flag = FALSE;
                        $row_number = $row;
                        break;
                    }
                }
            }

            if ($flag) {
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
            } else {
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
    function sanitize_partner_appliance_model_data_to_insert($data) {

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
        $flag = false;
        if ($partner_id) {
            $model_details = $this->inventory_model->get_appliance_model_details('id,model_number', array('entity_id' => trim($this->input->post('partner_id')), 'entity_type' => _247AROUND_PARTNER_STRING, "appliance_model_details.active" => 1));
            $part_number_details = $this->inventory_model->get_inventory_master_list_data('inventory_id,part_number', array('entity_id' => $partner_id, 'entity_type' => _247AROUND_PARTNER_STRING));

            $model = array();
            foreach ($model_details as $value) {
                $trim_data = array_map('trim', $value);
                $model[] = array_map('strtolower', $value);
            }

            $part_number = array();
            foreach ($part_number_details as $value) {
                $trim_data = array_map('trim', $value);
                $part_number[] = array_map('strtolower', $value);
            }

            if (!empty($model) && !empty($part_number)) {
                $model_arr = array_column($model, 'id', 'model_number');
                $part_number_arr = array_column($part_number, 'inventory_id', 'part_number');

                //get file data to process
                for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                    $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                    $sanitizes_row_data = array_map('strtolower', array_map('trim', $rowData_array[0]));


                    //check if model number exist in our database
                    if (!empty(array_filter($sanitizes_row_data))) {

                        if (array_key_exists($sanitizes_row_data[0], $model_arr)) {
                            //check part exist in our database
                            //print_r($sanitizes_row_data[0]);
                            $response = $this->process_bom_mapping($model_arr[$sanitizes_row_data[0]], $part_number_arr, $sanitizes_row_data, $rowData_array[0][0]);
                            //array_push($remap_bom_array, end($this->dataToInsert));
                        } else {
                            array_push($this->not_exists_model, $sanitizes_row_data[0]);
                            $flag = true;
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

                if (!empty($this->dataToInsert)) {
                    $insert_data = $this->inventory_model->insert_batch_inventory_model_mapping($this->dataToInsert);
                    if ($insert_data) {
                        $this->remap_in_bom_map($this->remap_bom_array);
                        log_message("info", __METHOD__ . count($this->dataToInsert) . " mapping created succcessfully");
                        $response['status'] = TRUE;
                        $message = "<b>" . count($this->dataToInsert) . "</b> mapping created successfully.";
                        $response['message'] = $message . ' ' . $not_exist_data_msg;
                    } else {
                        log_message("info", __METHOD__ . " error in creating mapping.");
                        $response['status'] = FALSE;
                        if (!empty($data['saas_module'])) {
                            $response['message'] = "Either mapping already exists or something gone wrong. Please contact to backoffice team.";
                        } else {
                            $response['message'] = "Either mapping already exists or something gone wrong. Please contact backoffice team.";
                        }
                    }
                } else {
                    $response['status'] = True;
                    $response['message'] = "File has been uploaded successfully. No New Mapping Created. $not_exist_data_msg";
                }
            } else {
                $response['status'] = FALSE;
                $response['message'] = 'Model and Parts details not found for the selected partner.';
            }
        } else {
            $response['status'] = FALSE;
            $response['message'] = 'Please select correct partner';
        }

        if (!empty($flag)) {
            $response['status'] = FALSE;
            $response['message'] = "Models number does not exists in our record. $not_exist_data_msg";
        }

        return $response;
    }

    function remap_in_bom_map($remap_bom_array) {

        //$agentid = '';
        if ($this->session->userdata('userType') == 'employee') {
            $agentid = $this->session->userdata('id');
            $agent_name = $this->session->userdata('emp_name');
            $login_partner_id = _247AROUND;
            $login_service_center_id = NULL;
        } else if ($this->session->userdata('userType') == 'service_center') {
            $agentid = $this->session->userdata('agent_id');
            $agent_name = $this->session->userdata('service_center_name');
            $login_service_center_id = $this->session->userdata('service_center_id');
            $login_partner_id = NULL;
        } else if ($this->session->userdata('userType') == 'partner') {
            $agentid = $this->session->userdata('agent_id');
            $agent_name = $this->session->userdata('partner_name');
            $login_service_center_id = $this->session->userdata('partner_id');
            $login_partner_id = NULL;
        }else{
            $agentid = $this->session->userdata('id');
        }

        foreach ($remap_bom_array as $rowkey => $rowvalue) {

            $where = array(
                'spare_parts_details.status' => SPARE_PARTS_REQUESTED,
                'spare_parts_details.entity_type' => _247AROUND_PARTNER_STRING,
                'spare_parts_details.requested_inventory_id' => $rowvalue['inventory_id'],
                'spare_parts_details.model_number' => trim($rowvalue['model_name'])
            );
            $select = "spare_parts_details.id,spare_parts_details.quantity,spare_parts_details.booking_id,spare_parts_details.model_number,spare_parts_details.entity_type, booking_details.state,spare_parts_details.service_center_id,inventory_master_list.part_number, spare_parts_details.partner_id, booking_details.partner_id as booking_partner_id,"
                    . " requested_inventory_id";
            $post['is_inventory'] = true;
            $spares = $this->partner_model->get_spare_parts_by_any($select, $where, TRUE, FALSE, false, $post);
            $this->miscelleneous->spareTransfer($spares, $agentid, $agent_name, $login_partner_id, $login_service_center_id, "");
        }
    }

    /**
     * @desc: This function is used to do the inventory and model mapping from file upload
     * @param $data array() 
     * @return $response array()
     */
    function process_upload_alternate_spare_parts($data) {
        log_message("info", __METHOD__);
        $response = array();
        $insert_data = array();
        $partner_id = trim($this->input->post('partner_id'));

        $agentid = '';
        if ($this->session->userdata('userType') == 'employee') {
            $agentid = $this->session->userdata('id');
            $agent_name = $this->session->userdata('emp_name');
            $login_partner_id = _247AROUND;
            $login_service_center_id = NULL;
        } else if ($this->session->userdata('userType') == 'service_center') {
            $agentid = $this->session->userdata('agent_id');
            $agent_name = $this->session->userdata('service_center_name');
            $login_service_center_id = $this->session->userdata('service_center_id');
            $login_partner_id = NULL;
        } else if ($this->session->userdata('userType') == _247AROUND_PARTNER_STRING) {
            $agentid = $this->session->userdata('agent_id');
            $agent_name = $this->session->userdata('partner_name');
            $login_partner_id = $this->session->userdata('partner_id');
            $login_service_center_id = NULL;
        }

        if ($partner_id) {
            //column which must be present in the  upload inventory file
            $header_column_need_to_be_present = array('part_code', 'alt_part_code', 'model_number');
            //check if required column is present in upload file header
            $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);

            if ($check_header['status']) {
                //get file data to process
                $table_flag = false;
                $not_exist_data_msg = '';
                for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                    $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                    $sanitizes_row_data = array_map('trim', $rowData_array[0]);

                    if (!empty(array_filter($sanitizes_row_data))) {
                        $rowData = array_combine($data['header_data'], $rowData_array[0]);
                    }
                    if (!empty($rowData['model_number'])) {
                        if ($rowData['part_code'] != $rowData['alt_part_code']) {

                            $where = array('inventory_master_list.entity_id' => $partner_id, 'inventory_master_list.entity_type' => _247AROUND_PARTNER_STRING);
                            $where_in = array(trim($rowData['part_code']), trim($rowData['alt_part_code']));
                            $select = 'inventory_master_list.inventory_id, inventory_master_list.part_number';
                            $inventory_id_details = $this->inventory_model->get_inventory_master_list_data($select, $where, $where_in);
                            $model = $this->inventory_model->get_appliance_model_details("*", array('model_number' => trim($rowData['model_number']), "entity_id" => $partner_id));
                            if (!empty($model)) {
                                if (!empty($inventory_id_details) && count($inventory_id_details) > 1) {
                                    $tmp_arr = array();
                                    $tmp_arr['inventory_id'] = $inventory_id_details[0]['inventory_id'];
                                    $tmp_arr['alt_inventory_id'] = $inventory_id_details[1]['inventory_id'];
                                    $tmp_arr['part_code'] = $inventory_id_details[0]['part_number'];
                                    $tmp_arr['alt_part_code'] = $inventory_id_details[1]['part_number'];
                                    $tmp_arr['model_id'] = $model[0]['id'];
                                    $tmp_arr['model_number'] = trim($rowData['model_number']);
                                    array_push($this->dataToInsert, $tmp_arr);
                                } else {
                                    $template = array(
                                        'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                                    );
                                    $this->table->set_template($template);
                                    $this->table->set_heading(array('Part Code', 'Alt Part Code'));
                                    $this->table->add_row($rowData['part_code'], $rowData['alt_part_code']);
                                    $table_flag = true;
                                }
                            } else {
                                log_message("info", __METHOD__ . " error in creating mapping.");
                                $response['status'] = FALSE;
                                $response['message'] = "Model Number " . $rowData['model_number'] . " Not Exist";
                            }
                        } else {
                            log_message("info", __METHOD__ . " error in creating mapping.");
                            $response['status'] = FALSE;
                            $response['message'] = "Spare Parts Code And Alternate Spare Parts Code Is Same.";
                        }
                    } else {
                        log_message("info", __METHOD__ . " error in creating mapping.");
                        $response['status'] = FALSE;
                        $response['message'] = "Model Number can not be empty";
                    }
                }

                if (!empty($table_flag)) {
                    $not_exist_data_msg .= "<br> Below part number does not exists in our record: <br>";
                    $not_exist_data_msg .= $this->table->generate();
                }

                $count = 0;
                $insertUpdateFlag = false;
                $notInserted = $insertArr = array();
                if (!empty($this->dataToInsert)) {

                    foreach ($this->dataToInsert as $key => $val) {
                        $insertArr[$key]['inventory_id'] = $val['inventory_id'];
                        $insertArr[$key]['alt_inventory_id'] = $val['alt_inventory_id'];
                        $insertArr[$key]['model_id'] = $val['model_id'];
                    }

                    $insert_data = $this->inventory_model->insert_alternate_spare_parts($insertArr);
                    foreach ($this->dataToInsert as $val) {
                        $inventory_group_id_list = $this->inventory_model->get_generic_table_details('alternate_inventory_set', 'alternate_inventory_set.id,alternate_inventory_set.inventory_id, alternate_inventory_set.group_id', array('model_id' => $val['model_id']), array(trim($val['inventory_id']), trim($val['alt_inventory_id'])));

                        if (!empty($inventory_group_id_list)) {

                            if (count($inventory_group_id_list) > 1) {
                                $min_group_id = min(array_column($inventory_group_id_list, 'group_id'));
                                $max_group_id = max(array_column($inventory_group_id_list, 'group_id'));
                                if ($max_group_id !== $min_group_id) {
                                    foreach ($inventory_group_id_list as $value) {
                                        if ($value['group_id'] === $max_group_id) {
                                            $insertUpdateFlag = $this->inventory_model->update_group_wise_inventory_id(array('alternate_inventory_set.group_id' => $min_group_id), array('alternate_inventory_set.id' => $value['id']));
                                            (!empty($insertUpdateFlag) ? ++$count : array_push($notInserted, $val));
                                        } else {
                                            array_push($notInserted, $val);
                                        }
                                    }
                                } else {
                                    array_push($notInserted, $val);
                                }
                            } else if (count($inventory_group_id_list) == 1) {
                                $inventory_id = $inventory_group_id_list[0]['inventory_id'];
                                if ($val['inventory_id'] != $inventory_id) {
                                    $inventory_group_data = array('group_id' => $inventory_group_id_list[0]['group_id'], 'inventory_id' => $val['inventory_id'], 'model_id' => $val['model_id']);
                                } elseif ($val['alt_inventory_id'] != $inventory_id) {
                                    $inventory_group_data = array('group_id' => $inventory_group_id_list[0]['group_id'], 'inventory_id' => $val['alt_inventory_id'], 'model_id' => $val['model_id']);
                                }
                                $insertUpdateFlag = $this->inventory_model->insert_group_wise_inventory_id($inventory_group_data);
                                (!empty($insertUpdateFlag) ? ++$count : array_push($notInserted, $val));
                            }
                        } else {
                            $max_group_id_details = $this->inventory_model->get_generic_table_details('alternate_inventory_set', 'MAX(alternate_inventory_set.group_id) as max_group_id', array(), array());
                            $group_id = ($max_group_id_details[0]['max_group_id'] + 1);
                            $inventory_group_data = array('group_id' => $group_id, 'inventory_id' => $val['alt_inventory_id'], 'model_id' => $val['model_id']);
                            $insertUpdateFlag = $this->inventory_model->insert_group_wise_inventory_id($inventory_group_data);
                            (!empty($insertUpdateFlag) ? ++$count : array_push($notInserted, $val));

                            $inventory_group = array('group_id' => $group_id, 'inventory_id' => $val['inventory_id'], 'model_id' => $val['model_id']);
                            $insertUpdateFlag = $this->inventory_model->insert_group_wise_inventory_id($inventory_group);
                            (!empty($insertUpdateFlag) ? ++$count : array_push($notInserted, $val));
                        }

                        $insert_inventory = $this->insert_Inventory_Model_Data(trim($val['inventory_id']), trim($val['model_id']), 1);
                        $insert_alt_inventory = $this->insert_Inventory_Model_Data(trim($val['alt_inventory_id']), trim($val['model_id']), 0);
                        if ($insert_inventory && $insert_alt_inventory) {
                            log_message("info", __METHOD__ . " inventory model mapping created succcessfully");
                            $response['status'] = TRUE;
                            $response['message'] = "Details inserted successfully.";
                        } else {
                            log_message("info", __METHOD__ . " Inventory Model Mapping already created.");
                            $response['status'] = TRUE;
                            $response['message'] = "Inventory Model Mapping already created.";
                        }

                        $where = array(
                            'spare_parts_details.status' => SPARE_PARTS_REQUESTED,
                            'spare_parts_details.entity_type' => _247AROUND_PARTNER_STRING,
                            'spare_parts_details.requested_inventory_id IS NOT NULL ' => NULL
                        );
                        $select = "spare_parts_details.id,spare_parts_details.booking_id, spare_parts_details.entity_type,spare_parts_details.quantity, booking_details.state,spare_parts_details.service_center_id,inventory_master_list.part_number, spare_parts_details.partner_id, booking_details.partner_id as booking_partner_id,"
                                . " requested_inventory_id";
                        $post['where_in'] = array('spare_parts_details.requested_inventory_id' => array(trim($val['inventory_id']), trim($val['alt_inventory_id'])), 'model_number' => $val['model_number']);
                        $post['is_inventory'] = true;
                        $bookings_spare = $this->partner_model->get_spare_parts_by_any($select, $where, TRUE, FALSE, false, $post);

                        if (!empty($bookings_spare)) {
                            $this->miscelleneous->spareTransfer($bookings_spare, $agentid, $agent_name, $login_partner_id, $login_service_center_id);
                        }
                    }
                }

                if ($count > 0) {
                    log_message("info", __METHOD__ . $count . " mapping created succcessfully");
                    $response['status'] = TRUE;
                    $message = "<b>" . $count . "</b> mapping created successfully."; //count($this->dataToInsert)
                    $response['message'] = $message . ' ' . $not_exist_data_msg;
                } else {
                    log_message("info", __METHOD__ . " error in creating mapping.");
                    $response['status'] = FALSE;
                    $response['message'] = "Either mapping already exists or something gone wrong. Please contact 247around developer.";
                }

                if (!empty($notInserted)) {
                    $template = array(
                        'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                    );
                    $this->table->set_template($template);
                    $this->table->set_heading(array('Part Code', 'Alt Part Code'));
                    foreach ($notInserted as $val) {
                        $this->table->add_row($val['part_code'], $val['alt_part_code']);
                    }
                    $response['data'] = "<br><b> Error in creating mapping.<br> " . $this->table->generate();
                }
            } else {
                $response['status'] = $check_header['status'];
                $response['message'] = $check_header['message'];
            }
        } else {
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
    function process_bom_mapping($model_number_id, $part_number_arr, $uploaded_file_parts, $model_name) {
        //get only parts details from the uploaded file array. remove model number from first index of the array.
        //here we assume that first index of the file is always model number 
        // $model = $uploaded_file_parts[0];
        /// print_r($remap_bom_array);      
        unset($uploaded_file_parts[0]);
        foreach ($uploaded_file_parts as $value) {
            //check if uploaded part exists in our database
            if (!empty($value)) {
                if (array_key_exists(str_replace(array('"', "'"), "", $value), $part_number_arr)) {
                    $tmp = array();
                    $inventory_id = $part_number_arr[str_replace(array('"', "'"), "", $value)];
                    
                    /* check model number and part number belongs to same appliance
                     * If yes then push record in insert array.
                     * @modifiedBy Ankit Rajvanshi
                     */
                    if(!empty($this->inventory_model->check_appliance_of_model_and_part($model_number_id, $inventory_id))) {
                        $tmp['inventory_id'] = $inventory_id;
                        $tmp['model_number_id'] = $model_number_id;
                        $tmp2 = array();
                        $tmp2['inventory_id'] = $inventory_id;
                        $tmp2['model_number_id'] = $model_number_id;
                        $tmp2['model_name'] = $model_name;

                        array_push($this->dataToInsert, $tmp);
                        array_push($this->remap_bom_array, $tmp2);
                    }
                } else {
                    array_push($this->not_exists_parts, $value);
                }
            }
        }
    }

    /**
     * @desc This function is used to upload partner serial no file
     */
    function process_upload_serial_number() {
        $partner_id = trim($this->input->post('partner_id'));
        if (!empty($partner_id)) {
            $file_status = $this->get_upload_file_type();
            if ($file_status['status']) {
                $data = $this->read_upload_file_header($file_status);
                if ($data['status']) {
                    log_message('info', __METHOD__ . " " . print_r($data['header_data'], TRUE));
                    $data['post_data']['file_type'] = $partner_id . "_" . PARTNER_SERIAL_NUMBER_FILE_TYPE;

                    //column which must be present in the  upload inventory file
                    $header_column_need_to_be_present = array('invoicedate', 'skuname', 'skucode', 'productcategoryname',
                        'brandname', 'modelname', 'colorname', 'stockbin', 'serialnumber');
                    //check if required column is present in upload file header
                    $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
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
                            $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                            if (!empty(array_filter($sanitizes_row_data))) {
                                $rowData = array_combine($data['header_data'], $rowData_array[0]);

                                if (!empty($rowData['serialnumber'])) {

                                    $result = $this->partner_model->getpartner_serialno(array('partner_id' => $partner_id, 'serial_number' => $rowData['serialnumber'], "active" => 1));
                                    if (empty($result)) {
                                        $invoiceData = NULL;
                                        if (!empty($rowData['invoicedate'])) {
                                            $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData['invoicedate']);
                                            $invoiceData = $dateObj2->format('Y-m-d');
                                        }
                                        $array = array('partner_id' => $partner_id,
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
                                    $emptyarray++;
                                }
                            }
                        }

                        $file_upload_status = FILE_UPLOAD_FAILED_STATUS;
                        if (!empty($validData)) {
                            $status = $this->partner_model->insert_partner_serial_number_in_batch($validData);
                            if ($status) {
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
                        $response['message'] = "File upload Failed. " . $check_header['message'];
                        $message = "File upload Failed. " . $check_header['message'];
                    }
                } else {
                    $response['status'] = FALSE;
                    $response['message'] = "File upload Failed. Empty file has been uploaded";
                    $message = "File upload Failed. Empty file has been uploaded";
                }
                $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'],
                        $data['post_data']['file_type'], $file_upload_status, "", "partner", $partner_id);

                $this->send_email($data, $response);
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
    function process_docket_number_file_upload() {
        $data = array();
        //$redirect_to = $this->input->post('redirect_url');
        $file_upload_status = FILE_UPLOAD_FAILED_STATUS;
        $file_status = $this->get_upload_file_type();
        if ($file_status['status']) {
            $data = $this->read_upload_file_header($file_status);
            if ($data['status']) {
                $data['post_data']['file_type'] = DOCKET_NUMBER_FILE_TYPE;
                //column which must be present in the  upload inventory file
                $header_column_need_to_be_present = array('awb_number', 'courier_charges', 'invoice_id', 'courier_name', 'billable_weight', 'actual_weight');

                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
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
                        $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                        if (!empty(array_filter($sanitizes_row_data))) {
                            $rowData = array_combine($data['header_data'], $rowData_array[0]);
                            if (!empty($rowData['awb_number']) && !empty($rowData['courier_charges']) && !empty($rowData['invoice_id']) && !empty($rowData['billable_weight']) && !empty($rowData['actual_weight'])) {
                                $courier_company_detail = $this->inventory_model->update_docket_price($rowData);
                                if (!empty($courier_company_detail['inValidData'])) {
                                    $inValidData = TRUE;
                                    $this->table->add_row($courier_company_detail['inValidData']);
                                }
                                if (!empty($courier_company_detail['notfoundData'])) {
                                    $notfoundData[] = $courier_company_detail['notfoundData'];
                                }
                            } else {
                                //log_message('info', __METHOD__. "data not found ". print_r($data['header_data'], TRUE));
                            }
                        }
                    }
                    if ($inValidData) {
                        $response['status'] = TRUE;
                        $email_message = "Duplicate entry found for below AWB number. Please check : <br>";
                        $email_message .= $this->table->generate();
                        $response['message'] = $email_message;
                        $file_upload_status = FILE_UPLOAD_SUCCESS_STATUS;
                        $returnData['status'] = TRUE;
                        $returnData['message'] = "File Successfully Uploaded.";
                    } else {
                        $response['status'] = TRUE;
                        $file_upload_status = FILE_UPLOAD_SUCCESS_STATUS;
                        $response['message'] = "Docket Number Successfully Uploaded. ";
                        $returnData['status'] = TRUE;
                        $returnData['message'] = "File Successfully Uploaded.";
                    }
                } else {
                    $response['status'] = FALSE;
                    $response['message'] = "File upload Failed. " . $check_header['message'];
                    $returnData['status'] = FALSE;
                    $returnData['message'] = "File upload Failed. " . $check_header['message'];
                }
            } else {
                $response['status'] = FALSE;
                $response['message'] = "File upload Failed. Empty file has been uploaded";
                $returnData['status'] = FALSE;
                $returnData['message'] = "File upload Failed. Empty file has been uploaded";
            }
            $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $data['post_data']['file_type'], $file_upload_status);
            $this->send_email($data, $response);
            if (!empty($notfoundData)) {
                $this->table->set_template($template);
                $this->table->set_heading(array('AWB Number'));
                foreach ($notfoundData as $value) {
                    $this->table->add_row($value);
                }
                $response['status'] = TRUE;
                $email_message = "Below AWB number not found. Please check and upload only below data again: <br>";
                $email_message .= $this->table->generate();
                $response['message'] = $email_message;
                $this->send_email($data, $response);
            }
        } else {
            $returnData['status'] = FALSE;
            $returnData['message'] = $file_status['message'];
        }
        echo json_encode($returnData);
    }

    /**
     * @desc load upload payment file view
     */
    function upload_payment_file() {
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
                    if (abs(round($total_amount, 0)) == round($p_amount_paid, 0)) {
                        $main_data = $this->get_service_center_filtered_data($sheetRowData);
                        if ($main_data['status']) {
                            if (!empty($main_data['data'])) {
                                foreach ($main_data['data'] as $value) {

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
                            if (isset($main_data['message'])) {
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
                    $response['message'] = "File upload Failed. " . $check_header['message'];
                    $message = "Payment file upload Failed. " . $check_header['message'];
                }

                $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $data['post_data']['file_type'], $file_upload_status, "", "", "", $this->input->post("total_amount_paid"));
                $this->send_email($data, $response);
            } else {
                $response['status'] = FALSE;
                $response['message'] = "File upload Failed. Empty file has been uploaded";
                $message = "Payment file upload Failed. Empty file has been uploaded";
            }
        } else {
            $response['status'] = FALSE;
            $message = $file_status['message']; //"Unable to find Partner, Please refresh and try again";
        }

        echo $response['status'] . "~~" . $message;
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
        $notifications = array();
        foreach ($data as $value) {
            if (round(abs($value['amount_due']), 0) >= round($value['amount_paid'], 0)) {
                if ($value['amount_paid'] == 0) {
                    if (!empty($value['invoice_remarks'])) {
                        $this->invoices_model->update_partner_invoices(array('invoice_id' => $value['invoice_id']), array('remarks' => $value['invoice_remarks']));
                        $notifications['failure'][$value['vendor_id']] = $value['invoice_remarks'];
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
                    if (!empty($value['invoice_remarks'])) {
                        $notifications['success'][$value['vendor_id']] = $value['invoice_remarks'];
                    }
                }
            } else {
                $v['status'] = false;
                $v['message'] = array('invoice_id' => "Amount Paid greater than amount due. Invoice ID: - " . $value['invoice_id']);

                break;
            }
        }

        if (!empty($notifications)) {
            $this->send_dashboard_notification_to_vendor($notifications);
        }

        log_message('info', __METHOD__ . " DATA " . print_r($main_data, true));
        $v['data'] = $main_data;
        return $v;
    }

    /*     * Desc - This function is used to show payment status of vendor * */

    function send_dashboard_notification_to_vendor($notifications) {
        $data = array();
        $notification_type = "";
        foreach ($notifications as $key => $value) {
            if ($key == "success") {
                $notification_type = PAYMENT_SUCCESS_NOTICATION_TYPE;
            } else {
                $notification_type = PAYMENT_HOLD_NOTICATION_TYPE;
            }

            foreach ($value as $vid => $remarks) {
                $rowData = array();
                $rowData['entity_type'] = _247AROUND_SF_STRING;
                $rowData['entity_id'] = $vid;
                $rowData['notification_type'] = $notification_type;
                $rowData['message'] = $remarks;
                $rowData['marquee'] = 1;
                $rowData['start_date'] = date("Y-m-d 00:00:00");
                $rowData['end_date'] = date('Y-m-d 00:00:00', strtotime("+2 day", strtotime(date("Y-m-d 00:00:00"))));
                $rowData['create_date'] = date("Y-m-d H:i:s");
                array_push($data, $rowData);
            }
        }
        $this->dashboard_model->insert_dashboard_notification($data);
    }

    /**
     * @desc - This function is used to update partner royalty on specified bookings
     * @param Array $data (excel data)
     * @return Array
     */
    function process_partner_royalty_file_upload() {
        $data = array();
        $file_upload_status = FILE_UPLOAD_FAILED_STATUS;
        $file_status = $this->get_upload_file_type();
        if ($file_status['status']) {
            $data = $this->read_upload_file_header($file_status);
            if ($data['status']) {
                $data['post_data']['file_type'] = PARTNER_ROYALTY_FILE_TYPE;
                //column which must be present in the  upload inventory file
                $header_column_need_to_be_present = array('partner_royalty_charge');
                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
                if ($check_header['status']) {
                    $file_upload_status = FILE_UPLOAD_SUCCESS_STATUS;
                    //get file data to process
                    for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                        $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                        $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                        if (!empty(array_filter($sanitizes_row_data))) {
                            $rowData = array_combine($data['header_data'], $rowData_array[0]);
                            $invoice_tax_amount = (($rowData['partner_royalty_charge'] * 18) / 100);
                            $invoice_basic_amount = $rowData['partner_royalty_charge'] - $invoice_tax_amount;
                            $royalty_invoice_data = array(
                                'entity_type' => _247AROUND_PARTNER_STRING,
                                'entity_id' => $this->input->post('partner_id'),
                                'booking_id' => $rowData['booking_id'],
                                'booking_unit_id' => $rowData['booking_unit_id'],
                                'invoice_type' => ROYALTY,
                                'invoice_id' => $this->input->post('invoice_id'),
                                'booking_basic_amount' => $invoice_basic_amount,
                                'booking_tax_amount' => $invoice_tax_amount,
                            );
                            $this->invoices_model->insert_into_booking_debit_credit_detils($royalty_invoice_data);
                            $this->reusable_model->update_table_where_in('booking_unit_details', array("royalty_invoice" => $this->input->post('invoice_id')), array('id' => $rowData['booking_unit_id']));
                            $returnData['status'] = TRUE;
                            $returnData['message'] = "File Successfully Uploaded";
                        }
                    }

                    $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], $data['post_data']['file_type'], $file_upload_status);
                } else {
                    $returnData['status'] = FALSE;
                    $returnData['message'] = "File upload Failed. " . $check_header['message'];
                }
            } else {
                $returnData['status'] = FALSE;
                $returnData['message'] = "File upload Failed. Empty file has been uploaded";
            }
        } else {
            $returnData['status'] = FALSE;
            $returnData['message'] = "File upload Failed. Empty file has been uploaded";
        }
        echo json_encode($returnData);
    }

    /**
     * @desc - This function is used to insert model mapping data for uploaded alternate parts
     * @param $inventory_id, $alt_inventory_id
     * @return $insert_id
     */
    function insert_Inventory_Model_Data($inventory_id, $model_id, $bom_main) {
        $data_model_mapping = array();
        $inventory_details = $this->inventory_model->get_inventory_model_data("*", array('inventory_id' => $inventory_id, 'model_number_id' => $model_id));
        if (empty($inventory_details)) {
            $tmp = array();
            $tmp['model_number_id'] = $model_id;
            $tmp['inventory_id'] = trim($inventory_id);
            $tmp['bom_main_part'] = $bom_main;
            array_push($data_model_mapping, $tmp);

            return $this->inventory_model->insert_batch_inventory_model_mapping($data_model_mapping);
        } else {
            return TRUE;
        }
    }

    /** @desc: This function is used to upload the partner mapping with service configuration
     * @param: void
     * @return void
     */
    function import_partner_appliance_configuration() {

        log_message('info', __FUNCTION__ . ' Function Start For Mapping Partner Appliance Configuration By ' . $this->session->userdata('id'));
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/import_partner_appliance_configuration');
    }

    function upload_partner_appliance_list() {
        $file_status = $this->get_upload_file_type();
        $partner_id = $this->input->post('partner_id');
        $returnMsg = [];
        $errorMsg = "";

        if ($file_status['file_name_lenth']) {
            if ($file_status['status']) {

                //get file header
                $data = $this->read_upload_file_header($file_status);
                $data['post_data'] = $this->input->post();
                $is_data_validated = true;

                //column which must be present in the  upload inventory file
                $header_column_need_to_be_present = array('product', 'category', 'capacity');
                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, array_filter($data['header_data']));
                $arr_mismatch = array_diff_assoc($header_column_need_to_be_present, $data['header_data']);

                if (!empty($arr_mismatch)) {
                    // Invalid file format
                    $errorMsg = 'Uploaded File format not Matches with requested format';
                    $this->session->set_flashdata('file_error', $errorMsg);
                    redirect(base_url() . 'file_upload/upload_partner_appliance_list');
                }

                if ($data['highest_row'] <= 1) {
                    // Empty file
                    $errorMsg = 'Empty file uploaded';
                    $this->session->set_flashdata('file_error', $errorMsg);
                    redirect(base_url() . 'file_upload/upload_partner_appliance_list');
                }

                if ($check_header['status']) {

                    // apply loop for validation.
                    for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                        $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                        $sanitizes_row_data = $rowData_array[0];

                        if (!empty(array_filter($sanitizes_row_data))) {
//                            $returnMsg[$row][0] = $sanitizes_row_data[0]; // Partner
                            $returnMsg[$row][0] = $sanitizes_row_data[0]; // Product
                            $returnMsg[$row][1] = $sanitizes_row_data[1]; // Category
                            $returnMsg[$row][2] = $sanitizes_row_data[2]; // Capacity  

                            $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                            $sanitizes_row_data = array_map('strtoupper', $rowData_array[0]);

                            $data['header_data'] = array_map('trim', $data['header_data']);
                            $data['header_data'] = array_map('strtoupper', $data['header_data']);
                            $rowData = array_combine($data['header_data'], $rowData_array[0]);
                            $returnMsg[$row][3] = "";

                            // check empty data.
                            if (empty($rowData['PRODUCT']) || empty($rowData['CATEGORY']) || empty($rowData['CAPACITY'])) {
                                // Insert Status of Record
                                $returnMsg[$row][3] .= 'Insufficient Data<br/>';
                                $is_data_validated = false;
                                continue;
                            }

                            // Check For Product
                            $arr_service = $this->reusable_model->get_search_result_data('services', 'id', ['UPPER(TRIM(services))' => $rowData['PRODUCT']], NULL, NULL, NULL, NULL, NULL);
                            if (empty($arr_service)) {
                                $returnMsg[$row][3] .= 'Product Not Found<br/>`';
                                $is_data_validated = false;
                                continue;
                            } else {
                                $service_id = $arr_service[0]['id'];
                            }

                            // Check For Category
                            $category = strtoupper(preg_replace("/[^a-zA-Z0-9.-]/", "", $rowData['CATEGORY']));
                            $arr_category = $this->reusable_model->get_search_result_data('category', 'id', ['private_key' => $category], NULL, NULL, NULL, NULL, NULL);
                            if (empty($arr_category) && $is_data_validated) {
                                // insert category.
                                $category_data['private_key'] = $category;
                                $category_data['name'] = $rowData['CATEGORY'];
                                $category_id = $this->reusable_model->insert_into_table('category', $category_data);
                                $returnMsg[$row][3] .= 'Inserted Category Data<br/>';
                            } else {
                                $category_id = $arr_category[0]['id'];
                            }

                            // Check For Capacity
                            $capacity = strtoupper(preg_replace("/[^a-zA-Z0-9.-]/", "", $rowData['CAPACITY']));
                            $arr_capacity = $this->reusable_model->get_search_result_data('capacity', 'id', ['private_key' => $capacity], NULL, NULL, NULL, NULL, NULL);
                            if (empty($arr_capacity) && $is_data_validated) {
                                // insert capacity.
                                $capacity_data['private_key'] = $capacity;
                                $capacity_data['name'] = $rowData['CAPACITY'];
                                $capacity_id = $this->reusable_model->insert_into_table('capacity', $capacity_data);
                                $returnMsg[$row][3] .= 'Inserted Capacity Data<br/>';
                            } else {
                                $capacity_id = $arr_capacity[0]['id'];
                            }

                            // check for service-category-capacity mapping
                            $service_category_mapping_data = $this->reusable_model->get_search_result_data('service_category_mapping', 'id', ['service_id' => $service_id, 'category_id' => $category_id, 'capacity_id' => $capacity_id], NULL, NULL, NULL, NULL, NULL);
                            if (empty($service_category_mapping_data) && $is_data_validated) {
                                // insert service-category-capacity mapping data.
                                $service_category_mapping_data['service_id'] = $service_id;
                                $service_category_mapping_data['category_id'] = $category_id;
                                $service_category_mapping_data['capacity_id'] = $capacity_id;
                                $appliance_configuration_id = $this->reusable_model->insert_into_table('service_category_mapping', $service_category_mapping_data);
                                $returnMsg[$row][3] .= 'Inserted Record for Service-Category-Mapping';
                            } else {
                                $appliance_configuration_id = $service_category_mapping_data[0]['id'];
                            }

                            // check for partner-appliance mapping
                            $partner_appliance_mapping_data = $this->reusable_model->get_search_result_data('partner_appliance_mapping', 'id', ['partner_id' => $partner_id, 'appliance_configuration_id' => $appliance_configuration_id], NULL, NULL, NULL, NULL, NULL);
                            if (empty($partner_appliance_mapping_data) && $is_data_validated) {
                                // insert partner-appliance mapping data.
                                $partner_appliance_mapping_data['partner_id'] = $partner_id;
                                $partner_appliance_mapping_data['appliance_configuration_id'] = $appliance_configuration_id;
                                $partner_appliance_mapping_id = $this->reusable_model->insert_into_table('partner_appliance_mapping', $partner_appliance_mapping_data);
                                $returnMsg[$row][3] .= 'Success';
                            } else {
                                $returnMsg[$row][3] .= 'Mapping already exists';
                                $partner_appliance_mapping_id = $partner_appliance_mapping_data[0]['id'];
                            }
                        }
                    }
                }
            }
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/import_partner_appliance_configuration', ['partner_id' => $partner_id, 'data' => $returnMsg]);
    }

    /**
     * @desc This function is used to load view.
     * We will upload excel file to make debit note in bulk
     */
    function create_bulk_debit_note(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_bulk_debit_note');
    }
    /**
     * @desc This function is used to process bulk upload
     */
    function process_bulk_debit_note() {
        $file_status = $this->get_upload_file_type();

        if ($file_status['status']) {
            $sheetRowData = array();
            $invalid_data = array();
            $data = $this->read_upload_file_header($file_status);
            if ($data['status']) {

                $data['post_data']['file_type'] = BULK_DEBIT_NOTE_TAG;

                $header_column_need_to_be_present = array('vendor_id', 'basic_amount', 'gst_rate', 'description');

                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, $data['header_data']);
                $vendor_not_found = array();
                if ($check_header['status']) {
                    $c_data = array();
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


                    foreach ($sheetRowData as $value) {
                        $entity_details = array();
                        if (!isset($c_data[$value['vendor_id']])) {
                            $k = 0;
                            $entity_details = $this->vendor_model->getVendorDetails("id,gst_no as gst_number, sc_code,"
                                    . "state,address as company_address,company_name,district, pincode, owner_phone_1, primary_contact_email, owner_email", array("service_centres.id" => trim($value['vendor_id'])));
                            if (!empty($entity_details)) {

                                $c_data[$value['vendor_id']][$k]['service_center_id'] = $entity_details[0]['id'];
                                $c_data[$value['vendor_id']][$k]['company_name'] = $entity_details[0]['company_name'];
                                $c_data[$value['vendor_id']][$k]['company_address'] = $entity_details[0]['company_address'];
                                $c_data[$value['vendor_id']][$k]['district'] = $entity_details[0]['district'];
                                $c_data[$value['vendor_id']][$k]['pincode'] = $entity_details[0]['pincode'];
                                $c_data[$value['vendor_id']][$k]['state'] = $entity_details[0]['state'];
                                $c_data[$value['vendor_id']][$k]['gst_number'] = $entity_details[0]['gst_number'];
                            } else {
                                array_push($vendor_not_found, $value['vendor_id']);
                            }
                        } else {
                            $k = count($c_data[$value['vendor_id']]);
                        }

                        if (empty($vendor_not_found)) {
                            if (isset($c_data[$value['vendor_id']][0]['service_center_id'])) {
                                $c_data[$value['vendor_id']][$k]['description'] = $value['description'];
                                $c_data[$value['vendor_id']][$k]['rate'] = sprintf("%.2f", $value['basic_amount']);
                                $c_data[$value['vendor_id']][$k]['taxable_value'] = sprintf("%.2f", $value['basic_amount']);
                                $c_data[$value['vendor_id']][$k]['product_or_services'] = 'Service';
                                $c_data[$value['vendor_id']][$k]['gst_rate'] = $value['gst_rate'];
                                $c_data[$value['vendor_id']][$k]['qty'] = 1;
                                $c_data[$value['vendor_id']][$k]['hsn_code'] = HSN_CODE;
                            }
                        }
                    }

                    if (empty($vendor_not_found)) {
                        $invoice_date = date("Y-m-d");
                        foreach ($c_data as $vendor_id => $val) {
                            $response = $this->invoices_model->_set_partner_excel_invoice_data($val, $invoice_date, $invoice_date, "Debit Note", $invoice_date);
                            $invoice_id = $this->invoice_lib->create_invoice_id('ARD-DN');
                            $response['meta']['invoice_id'] = $invoice_id;

                            $response['meta']['accounting'] = 1;
                            $response['meta']["vertical"] = SERVICE;
                            $response['meta']["category"] = INSTALLATION_AND_REPAIR;
                            $response['meta']["sub_category"] = DEBIT_NOTE;

                            $response['meta']['due_date'] = $response['meta']['invoice_date'];
                            $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
                            if ($status) {
                                $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
                                $output_pdf_file_name = $convert['main_pdf_file_name'];
                                $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                                $response['meta']['copy_file'] = $convert['copy_file'];
                                $response['meta']['invoice_file_excel'] = $invoice_id . ".xlsx";
                                $response['meta']['invoice_detailed_excel'] = NULL;

                                $response['meta']['due_date'] = $response['meta']['invoice_date'];

                                $this->invoice_lib->insert_invoice_breackup($response);
                                $invoice_details = $this->invoice_lib->insert_vendor_partner_main_invoice($response, "A", "Debit Note", _247AROUND_SF_STRING, $vendor_id, $convert, $this->session->userdata('id'), HSN_CODE);
                                $this->invoices_model->insert_new_invoice($invoice_details);

                                $this->invoice_lib->upload_invoice_to_S3($invoice_id, false);
                                unlink(TMP_FOLDER.$output_pdf_file_name);
                                unlink(TMP_FOLDER."copy_".$output_pdf_file_name);
                                unlink(TMP_FOLDER.$invoice_id.".xlsx");
                                unlink(TMP_FOLDER."copy_".$invoice_id.".xlsx");
                            }

                        }
                        $response['status'] = True;
                        $response['message'] = "Debit Note Generated Successfully";

                        $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], BULK_DEBIT_NOTE_TAG, FILE_UPLOAD_SUCCESS_STATUS, "default", _247AROUND_EMPLOYEE_STRING, _247AROUND);
                    } else {
                        $this->miscelleneous->update_file_uploads($data['file_name'], TMP_FOLDER . $data['file_name'], BULK_DEBIT_NOTE_TAG, FILE_UPLOAD_FAILED_STATUS, "default", _247AROUND_EMPLOYEE_STRING, _247AROUND);
                        $response['status'] = FALSE;
                        $response['message'] = "Incorrect Vendor ID - " . implode(", ", $vendor_not_found);
                        //Vendor Not found
                    }
                } else {
                    $returnData['status'] = FALSE;
                    $returnData['message'] = "File upload Failed. " . $check_header['message'];
                }
            } else {
                $returnData['status'] = FALSE;
                $returnData['message'] = "File upload Failed. Empty file has been uploaded";
            }
        } else {
            $response['status'] = FALSE;
            $response['message'] = "Failed - File Corrupted.";
        }

        echo json_encode($response, true);
    }
}
