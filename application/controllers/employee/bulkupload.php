<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bulkupload extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('reusable_model');
        $this->load->model('warranty_model');
        $this->load->library("session");
        $this->load->library('miscelleneous');
    }

    /** @desc: This function is used to upload the partner mapping with service configuration
     * @param: void
     * @return void
     */
    function check_warranty() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/bulk_upload_check_warranty');
    }

    function check_warranty_data() {
        ini_set('display_errors', '1');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 36000);

        $file_status = $this->get_upload_file_type();
        $post_data = $this->input->post();
        $redirect_to = $this->input->post('redirect_url');
        $returnMsg = [];

        if ($file_status['file_name_lenth']) {
            if ($file_status['status']) {

                //get file header
                $data = $this->read_upload_file_header($file_status);
                $data['post_data'] = $this->input->post();

                //column which must be present in the  upload inventory file
                $header_column_need_to_be_present = array('product', 'bookingdate', 'bookingid', 'dop');
                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, array_filter($data['header_data']));

                if ($check_header['status']) {

                    // apply loop for validation.
                    for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                        $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                        $sanitizes_row_data = $rowData_array[0];
                        $is_data_validated = true;

                        if (!empty(array_filter($sanitizes_row_data))) {

                            $returnMsg[$row][0] = $sanitizes_row_data[0]; // Product
                            $returnMsg[$row][1] = $sanitizes_row_data[1]; // BookingDate
                            $returnMsg[$row][2] = $sanitizes_row_data[2]; // Model
                            $returnMsg[$row][3] = $sanitizes_row_data[3]; // DOP            
                            $returnMsg[$row][4] = "";
                            $returnMsg[$row][5] = "";

                            $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                            $sanitizes_row_data = array_map('strtoupper', $rowData_array[0]);

                            $data['header_data'] = array_map('trim', $data['header_data']);
                            $data['header_data'] = array_map('strtoupper', $data['header_data']);
                            $rowData = array_combine($data['header_data'], $rowData_array[0]);

                            // check empty data.
                            if (empty($rowData['PRODUCT']) || empty($rowData['BOOKINGDATE']) || empty($rowData['BOOKINGID']) || empty($rowData['DOP'])) {
                                // Insert Status of Record
                                $returnMsg[$row][5] .= 'Insufficient Data<br/>';
                                $is_data_validated = false;
                                continue;
                            }

                            // Check For Product
                            $arr_service = $this->reusable_model->get_search_result_data('services', 'id', ['UPPER(TRIM(services))' => $rowData['PRODUCT']], NULL, NULL, NULL, NULL, NULL);
                            if (empty($arr_service)) {
                                $returnMsg[$row][5] .= 'Product Not Found<br/>`';
                                $is_data_validated = false;
                                continue; // Continue to next record.
                            } else {
                                $service_id = $arr_service[0]['id'];
                            }

                            if (!is_numeric($sanitizes_row_data[3])) {
                                $returnMsg[$row][5] .= 'DOP not valid<br/>`';
                                $is_data_validated = false;
                                continue; // Continue to next record.
                            }

                            if (!is_numeric($sanitizes_row_data[1])) {
                                $returnMsg[$row][5] .= 'Booking Date not valid<br/>`';
                                $is_data_validated = false;
                                continue; // Continue to next record.
                            }

                            $unix_date = ($sanitizes_row_data[3] - 25569) * 86400;
                            $excel_date = (25569) + ($unix_date / 86400);
                            $unix_date = ($excel_date - 25569) * 86400;
                            $dop = date('d-m-Y', $unix_date);

                            $unix_date1 = ($sanitizes_row_data[1] - 25569) * 86400;
                            $excel_date1 = (25569) + ($unix_date1 / 86400);
                            $unix_date1 = ($excel_date1 - 25569) * 86400;
                            $bd = date('d-m-Y', $unix_date1);

                            $returnMsg[$row][1] = $bd; // BookingDate
                            $returnMsg[$row][3] = $dop; // DOP
                            // Check For Model
                            $join = array("appliance_model_details" => "appliance_model_details.model_number=booking_unit_details.sf_model_number");
                            $arr_model = $this->reusable_model->get_search_result_data('booking_unit_details', 'appliance_model_details.id', ['UPPER(TRIM(booking_id))' => $rowData['BOOKINGID'], 'booking_unit_details.service_id' => $service_id], $join, NULL, NULL, NULL, NULL);

                            if (empty($arr_model)) {
                                $returnMsg[$row][5] .= 'Model Not Found<br/>';
                                $is_data_validated = false;
                            } else {
                                $model_id = $arr_model[0]['id'];
                            }

                            if (!$is_data_validated) {
                                continue; // Continue to next record.
                            }

                            //set data
                            $arr_data['partner'] = '247130';
                            $arr_data['service_id'] = $service_id;
                            $arr_data['brand'] = 'Videocon';
                            $arr_data['model'] = $model_id;
                            $arr_data['purchase_date'] = $dop;
                            $arr_data['booking_date'] = $bd;
                            $arr_warranty_data = $this->warranty_model->check_warranty_for_bulk_data($arr_data);
                            $returnMsg[$row][4] = 'OW';

                            if (empty($arr_warranty_data)):
                                $in_warranty_end_period = strtotime(date("Y-m-d", strtotime($arr_data['purchase_date'])) . " +1 year");
                                $in_warranty_end_period = strtotime(date("Y-m-d", $in_warranty_end_period) . " -1 day");
                                if (strtotime($arr_data['booking_date']) <= $in_warranty_end_period) :
                                    $returnMsg[$row][4] = 'IW';
                                    $returnMsg[$row][5] = 'Warranty will end on ' . date("d-M-Y", $in_warranty_end_period);
                                endif;
                            else:
                                $warranty_months = $arr_warranty_data[0]['warranty_period'] + 12;
                                $strWarrantyType = "extended";
                                if($arr_warranty_data[0]['warranty_type'] == 1)
                                {
                                    $strWarrantyType = "";
                                    $warranty_months = $arr_warranty_data[0]['warranty_period'];
                                } 
                                $warranty_end_period = strtotime(date("Y-m-d", strtotime($arr_data['purchase_date'])) . " +" . $warranty_months . " months");
                                $warranty_end_period = strtotime(date("Y-m-d", $warranty_end_period) . " -1 day");
                                if (strtotime($arr_data['booking_date']) <= $warranty_end_period) :
                                    $returnMsg[$row][4] = 'EW';
                                    $returnMsg[$row][5] = 'Product lies in '.$strWarrantyType.' warranty of ' . $arr_warranty_data[0]['warranty_period'] . ' months. Warranty will end on ' . date("d-m-Y", $warranty_end_period);
                                endif;
                            endif;
                        }
                    }
                }
            }
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/bulk_upload_check_warranty', ['data' => $returnMsg]);
    }

    /* --------------- Query for fetching data -------------------------------------------------------------------
      SELECT
      booking_details.booking_id,
      booking_details.service_id,
      services.services,
      appliance_model_details.id,
      booking_unit_details.sf_model_number,
      spare_parts_details.date_of_purchase,
      booking_details.booking_date,
      booking_details.request_type
      FROM
      booking_details
      LEFT JOIN
      services ON (services.id = booking_details.service_id)
      LEFT JOIN
      booking_unit_details ON (booking_unit_details.booking_id = booking_details.booking_id)
      LEFT JOIN
      appliance_model_details ON (appliance_model_details.model_number = booking_unit_details.sf_model_number)
      LEFT JOIN
      spare_parts_details ON (spare_parts_details.booking_id = booking_details.booking_id)
      WHERE
      booking_details.booking_id IN ()
      AND booking_unit_details.sf_model_number IS NOT NULL AND booking_unit_details.sf_model_number <> '' and spare_parts_details.date_of_purchase IS NOT NULL
      GROUP BY booking_details.booking_id;
     */

    function check_warranty_by_ids() {
        ini_set('display_errors', '1');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 36000);

        $file_status = $this->get_upload_file_type();
        $post_data = $this->input->post();
        $redirect_to = $this->input->post('redirect_url');
        $returnMsg = [];

        if ($file_status['file_name_lenth']) {
            if ($file_status['status']) {

                //get file header
                $data = $this->read_upload_file_header($file_status);
                $data['post_data'] = $this->input->post();

                //column which must be present in the  upload inventory file
                $header_column_need_to_be_present = array('booking_id', 'booking_create_date', 'product', 'booking_request_type', 'part_warranty_status', 'product_id', 'model_id', 'dop');
                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, array_filter($data['header_data']));

                if ($check_header['status']) {

                    // apply loop for validation.
                    for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                        $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                        $sanitizes_row_data = $rowData_array[0];
                        $is_data_validated = true;

                        if (!empty(array_filter($sanitizes_row_data))) {

                            $returnMsg[$row][0] = $sanitizes_row_data[0]; // BookingID
                            $returnMsg[$row][1] = $sanitizes_row_data[1]; // ServiceId
                            $returnMsg[$row][2] = $sanitizes_row_data[2]; // Service
                            $returnMsg[$row][3] = $sanitizes_row_data[3]; // ModelID
                            $returnMsg[$row][4] = $sanitizes_row_data[4]; // Model
                            $returnMsg[$row][5] = $sanitizes_row_data[5]; // DOP
                            $returnMsg[$row][6] = $sanitizes_row_data[6]; // Booking Date
                            $returnMsg[$row][7] = $sanitizes_row_data[7]; // Request Type
                            $returnMsg[$row][8] = ""; // warranty Status
                            $returnMsg[$row][9] = ""; // Remarks

                            $sanitizes_row_data = array_map('trim', $rowData_array[0]);
                            $sanitizes_row_data = array_map('strtoupper', $rowData_array[0]);

                            $data['header_data'] = array_map('trim', $data['header_data']);
                            $data['header_data'] = array_map('strtoupper', $data['header_data']);
                            $rowData = array_combine($data['header_data'], $rowData_array[0]);

                            // check empty data.
                            if (empty($rowData['PRODUCT_ID']) || empty($rowData['MODEL_ID']) || empty($rowData['DOP']) || empty($rowData['BOOKING_CREATE_DATE'])) {
                                // Insert Status of Record
                                $returnMsg[$row][9] .= 'Insufficient Data<br/>';
                                $is_data_validated = false;
                                continue;
                            }

                            if (!is_numeric($sanitizes_row_data[5])) {
                                $returnMsg[$row][9] .= 'DOP not valid<br/>`';
                                $is_data_validated = false;
                                continue; // Continue to next record.
                            }

                            if (!is_numeric($sanitizes_row_data[6])) {
                                $returnMsg[$row][6] .= 'Booking Date not valid<br/>`';
                                $is_data_validated = false;
                                continue; // Continue to next record.
                            }

                            $service_id = $rowData['PRODUCT_ID'];
                            $model_id = $rowData['MODEL_ID'];

                            $unix_date = ($sanitizes_row_data[5] - 25569) * 86400;
                            $excel_date = (25569) + ($unix_date / 86400);
                            $unix_date = ($excel_date - 25569) * 86400;
                            $dop = date('d-m-Y', $unix_date);

                            $unix_date1 = ($sanitizes_row_data[6] - 25569) * 86400;
                            $excel_date1 = (25569) + ($unix_date1 / 86400);
                            $unix_date1 = ($excel_date1 - 25569) * 86400;
                            $bd = date('d-m-Y', $unix_date1);

                            $returnMsg[$row][6] = $bd; // BookingDate
                            $returnMsg[$row][5] = $dop; // DOP
                            //set data
                            $arr_data['partner'] = '247130';
                            $arr_data['service_id'] = $service_id;
                            $arr_data['brand'] = 'Videocon';
                            $arr_data['model'] = $model_id;
                            $arr_data['purchase_date'] = $dop;
                            $arr_data['booking_date'] = $bd;

                            $arr_warranty_data = $this->warranty_model->check_warranty_for_bulk_data($arr_data);
                            
                            $returnMsg[$row][8] = 'OW';
                            if (empty($arr_warranty_data)):
                                $in_warranty_end_period = strtotime(date("Y-m-d", strtotime($arr_data['purchase_date'])) . " +1 year");
                                $in_warranty_end_period = strtotime(date("Y-m-d", $in_warranty_end_period) . " -1 day");
                                if (strtotime($arr_data['booking_date']) <= $in_warranty_end_period) :
                                    $returnMsg[$row][8] = 'IW';
                                    $returnMsg[$row][9] = 'Warranty will end on ' . date("d-M-Y", $in_warranty_end_period);
                                endif;
                            else:
                                $warranty_months = $arr_warranty_data[0]['warranty_period'] + 12;
                                $strWarrantyType = "extended";
                                if($arr_warranty_data[0]['warranty_type'] == 1)
                                {
                                    $strWarrantyType = "";
                                    $warranty_months = $arr_warranty_data[0]['warranty_period'];
                                }                                
                                $warranty_end_period = strtotime(date("Y-m-d", strtotime($arr_data['purchase_date'])) . " +" . $warranty_months . " months");
                                $warranty_end_period = strtotime(date("Y-m-d", $warranty_end_period) . " -1 day");
                                if (strtotime($arr_data['booking_date']) <= $warranty_end_period) :
                                    $returnMsg[$row][8] = 'EW';
                                    $returnMsg[$row][9] = 'Product lies in '.$strWarrantyType.' warranty of ' . $arr_warranty_data[0]['warranty_period'] . ' months. Warranty will end on ' . date("d-m-Y", $warranty_end_period);
                                endif;
                            endif;
                        }
                    }
                }
            }
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/bulk_upload_check_warranty', ['data' => $returnMsg]);
    }

    /**
     * @desc: This function is used to get the file type
     * @param void
     * @param $response array   //consist file temporary name, file extension and status(file type is correct or not)
     */
    private function get_upload_file_type() {
        log_message('info', __FUNCTION__ . "=> getting upload file type");
        if (!empty($_FILES['file']['name']) && strlen($_FILES['file']['name']) <= 44) {
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

                $response['status'] = True;
                $response['file_name_lenth'] = True;
            } else {
                log_message('info', __FUNCTION__ . ' Empty File Uploaded');
                $response['status'] = False;
                $response['file_name_lenth'] = True;
            }
        } else {
            log_message('info', __FUNCTION__ . 'File Name Length Is Long');
            $response['status'] = False;
            $response['file_name_lenth'] = false;
        }

        return $response;
    }

    /**
     * @desc: This function is used to get the file header
     * @param $file array  //consist file temporary name, file extension and status(file type is correct or not)
     * @param $response array  //consist file name,sheet name(in case of excel),header details,sheet highest row and highest column
     */
    private function read_upload_file_header($file, $sheet_no = 0) {
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
        $sheet = $objPHPExcel->getSheet($sheet_no);
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

    /** @desc: This function is used to upload the partner mapping with service configuration
     * @param: void
     * @return void
     */
    function add_warranty() {
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/bulk_upload_add_warranty');
    }

    function add_warranty_data() {
        ini_set('display_errors', '1');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $file_status = $this->get_upload_file_type();
        $post_data = $this->input->post();
        $redirect_to = $this->input->post('redirect_url');
        $returnMsg = [];

        if ($file_status['file_name_lenth'] && !empty($post_data['partner_id'])) {
            if ($file_status['status']) {
                // ********************* UPLOAD PLAN, STATE, PART TYPE DATA   ***********************************************************************                 
                //get file header
                $data = $this->read_upload_file_header($file_status, 0);
                $data['post_data'] = $this->input->post();

                //column which must be present in the  upload inventory file
                $header_column_need_to_be_present = array('plan_name', 'plan_desc', 'dop_start_date', 'dop_end_date', 'svc_charge', 'gas_charge', 'transport_charge', 'tenure', 'tenure_grace', 'branch', 'part_category', 'active');
                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, array_filter($data['header_data']));

                if ($check_header['status']) {
                    // Store all States data in Array
                    $arr_states = $this->reusable_model->get_search_result_data('state_code', 'state_code,state', NULL, NULL, NULL, NULL, NULL, NULL);
                    $arr_states = array_column($arr_states, 'state', 'state_code');
                    $arr_states = array_map(function($state) {
                        $state = strtoupper(str_replace(" ", "", $state));
                        return $state;
                    }, $arr_states);
                    $arr_states = array_flip($arr_states);

                    // Store all Part Types data in Array
                    $arr_parts = $this->reusable_model->get_search_result_data('inventory_parts_type', 'part_type,id', NULL, NULL, NULL, NULL, NULL, NULL);
                    $arr_parts = array_column($arr_parts, 'part_type', 'id');
                    $arr_parts = array_map(function($part) {
                        $part = strtoupper(str_replace(" ", "", $part));
                        return $part;
                    }, $arr_parts);
                    $arr_parts = array_flip($arr_parts);

                    // Store all Products data in Array
                    $arr_services = ["W/M" => "28", "REF" => "37", "LCDP" => "46", "A/C" => "50", "LCD" => "46", "CTV" => "46", "MO" => "54"];

                    // apply loop for validation.
                    for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                        $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                        $sanitizes_row_data = $rowData_array[0];
                        $is_data_validated = true;

                        // Check If Product exists or not
                        $returnMsg[$row][4] = "";
                        if (empty($arr_services[$sanitizes_row_data[4]])) {
                            $returnMsg[$row][4] = "Product Not Found";
                            continue;
                        }
                        $service_id = $arr_services[$sanitizes_row_data[4]];

                        // Calculate Plan Start End Date
                        $date_period_start = date('Y-m-d 00:00:00', strtotime($sanitizes_row_data[2]));
                        $date_period_end = date('Y-m-d 23:59:59', strtotime($sanitizes_row_data[3]));

                        if (gettype($sanitizes_row_data[2]) == 'double'):
                            $unix_date = ($sanitizes_row_data[2] - 25569) * 86400;
                            $excel_date = (25569) + ($unix_date / 86400);
                            $unix_date = ($excel_date - 25569) * 86400;
                            $date_period_start = date('Y-m-d 00:00:00', $unix_date);
                        endif;

                        if (gettype($sanitizes_row_data[3]) == 'double'):
                            $unix_date1 = ($sanitizes_row_data[3] - 25569) * 86400;
                            $excel_date1 = (25569) + ($unix_date1 / 86400);
                            $unix_date1 = ($excel_date1 - 25569) * 86400;
                            $date_period_end = date('Y-m-d 23:59:59', $unix_date1);
                        endif;

                        if (!empty(array_filter($sanitizes_row_data))) {
                            $arr_data = [];
                            $returnMsg[$row][0] = $arr_data['plan_name'] = $sanitizes_row_data[0];
                            $returnMsg[$row][1] = $arr_data['plan_description'] = $sanitizes_row_data[1];
                            $returnMsg[$row][2] = date("d-M-Y", strtotime($date_period_start));
                            $returnMsg[$row][3] = date("d-M-Y", strtotime($date_period_end));
                            $arr_data['period_start'] = $date_period_start;
                            $arr_data['period_end'] = $date_period_end;
                            $arr_data['warranty_type'] = 2;
                            $arr_data['partner_id'] = $post_data['partner_id'];
                            $arr_data['service_id'] = $service_id;
                            $arr_data['inclusive_svc_charge'] = (trim(strtoupper($sanitizes_row_data[5])) == 'YES') ? 1 : 0;
                            $arr_data['inclusive_gas_charge'] = (trim(strtoupper($sanitizes_row_data[6])) == 'YES') ? 1 : 0;
                            $arr_data['inclusive_transport_charge'] = (trim(strtoupper($sanitizes_row_data[7])) == 'YES') ? 1 : 0;
                            $arr_data['warranty_period'] = "CONVERT('$sanitizes_row_data[8]', SIGNED)";
                            $arr_data['warranty_grace_period'] = (int) $sanitizes_row_data[9];
                            $arr_data['is_active'] = (trim(strtoupper($sanitizes_row_data[12])) == 'YES') ? 1 : 0;
                            $arr_data['create_date'] = date('Y-m-d H:i:s');
                            $arr_data['created_by'] = $this->session->userdata('employee_id');
                            $plan_id = $this->reusable_model->insert_into_table('warranty_plans', $arr_data);

                            if (empty($plan_id)) {
                                $returnMsg[$row][4] = "Invalid Data";
                                continue;
                            }

                            // Insert Data in Warranty Plan State Mapping
                            $arr_state_mapping_result = [];
                            $str_plan_states = $sanitizes_row_data[10];
                            $arr_plan_states = explode(',', $str_plan_states);
                            $arr_plan_states = array_filter($arr_plan_states);
                            $arr_plan_states = array_map(function($plan_state) {
                                $plan_state = strtoupper(str_replace(" ", "", $plan_state));
                                return $plan_state;
                            }, $arr_plan_states);

                            foreach ($arr_plan_states as $rec_plan_state) {
                                if (!empty($arr_states[$rec_plan_state])) {
                                    $state_data = [];
                                    $state_data['state_code'] = $arr_states[$rec_plan_state];
                                    $state_data['plan_id'] = $plan_id;
                                    $state_data['create_date'] = date('Y-m-d H:i:s');
                                    $state_data['created_by'] = $this->session->userdata('employee_id');
                                    $plan_state_mapping_id = $this->reusable_model->insert_into_table('warranty_plan_state_mapping', $state_data);
                                    $arr_state_mapping_result[$row] = "Success";
                                    if(empty($plan_state_mapping_id))
                                    {
                                        $arr_state_mapping_result[$row] = "Fail";
                                    }
                                }
                            }

                            // Insert Data in Warranty Plan Part Type Mapping
                            $arr_part_mapping_result = [];
                            $str_plan_parts = $sanitizes_row_data[11];
                            $arr_plan_parts = explode(',', $str_plan_parts);
                            $arr_plan_parts = explode(',', $str_plan_parts);
                            $arr_plan_parts = array_filter($arr_plan_parts);
                            $arr_plan_parts = array_map(function($plan_part) use ($arr_services) {
                                $plan_part = strtoupper(str_replace(" ", "", $plan_part));
                                // Replace all prefixes from part types into blank
                                foreach ($arr_services as $key => $service) {
                                    $plan_part = str_replace($key . "-", "", $plan_part);
                                }
                                return $plan_part;
                            }, $arr_plan_parts);
                            foreach ($arr_plan_parts as $rec_plan_part) {
                                if (!empty($arr_parts[$rec_plan_part])) {
                                    $plan_data = [];
                                    $plan_data['part_type_id'] = $arr_parts[$rec_plan_part];
                                    $plan_data['plan_id'] = $plan_id;
                                    $plan_data['create_date'] = date('Y-m-d H:i:s');
                                    $plan_data['created_by'] = $this->session->userdata('employee_id');
                                    $plan_part_mapping_id = $this->reusable_model->insert_into_table('warranty_plan_part_type_mapping', $plan_data);
                                    $arr_part_mapping_result[$row] = "Success";
                                    if(empty($plan_part_mapping_id))
                                    {
                                        $arr_part_mapping_result[$row] = "Fail";
                                    }
                                }
                            }
                        }
                    }
                }
                // ********************* UPLOAD MODEL DATA   ***********************************************************************                // 
                //get file header
                $data = [];
                $arr_model_mapping_result = [];
                $data = $this->read_upload_file_header($file_status, 1);
                $data['post_data'] = $this->input->post();

                //column which must be present in the  upload inventory file
                $header_column_need_to_be_present = array('product', 'plan_name', 'model_list');
                //check if required column is present in upload file header
                $check_header = $this->check_column_exist($header_column_need_to_be_present, array_filter($data['header_data']));

                if ($check_header['status']) {
                    // Get Plans Data
                    $arr_service_wise_warranty_plans = [];
                    $arr_plans = $this->reusable_model->get_search_result_data('warranty_plans', '*', NULL, NULL, NULL, NULL, NULL, NULL);
                    foreach ($arr_plans as $arr_plan) {
                        $arr_service_wise_warranty_plans[$arr_plan['service_id']][$arr_plan['plan_name']] = $arr_plan['plan_id'];
                    }

                    // Get Models Data
                    $arr_service_wise_models = [];
                    $arr_models = $this->reusable_model->get_search_result_data('appliance_model_details', '*', NULL, NULL, NULL, NULL, NULL, NULL);
                    foreach ($arr_models as $arr_model) {
                        $arr_service_wise_models[$arr_model['service_id']][$arr_model['model_number']] = $arr_model['id'];
                    }

                    // apply loop for validation.
                    for ($row = 2, $i = 0; $row <= $data['highest_row']; $row++, $i++) {
                        $rowData_array = $data['sheet']->rangeToArray('A' . $row . ':' . $data['highest_column'] . $row, NULL, TRUE, FALSE);
                        $sanitizes_row_data = $rowData_array[0];
                        $is_data_validated = true;

                        if (!empty(array_filter($sanitizes_row_data))) {

                            // Check if Product Exists
                            $service_name = trim($sanitizes_row_data[0]);
                            if (empty($arr_services[$service_name])):
                                $arr_model_mapping_result[$row] = "Product Not Found";
                                continue;
                            endif;
                            $service_id = $arr_services[$service_name];

                            // Check if Plan Exists
                            $plan_name = trim($sanitizes_row_data[1]);
                            if (empty($arr_service_wise_warranty_plans[$service_id][$plan_name])):
                                $arr_model_mapping_result[$row] = "Plan Not Found";
                                continue;
                            endif;
                            $plan_id = $arr_service_wise_warranty_plans[$service_id][$plan_name];

                            // Check if Product Exists
                            $model_name = trim($sanitizes_row_data[2]);
                            if (empty($arr_service_wise_models[$service_id][$model_name])):
                                $arr_model_mapping_result[$row] = "Model Not Found";
                                continue;
                            endif;
                            $model_id = $arr_service_wise_models[$service_id][$model_name];
                            
                            // Insert Data
                            $model_mapping_data = [];
                            $model_mapping_data['service_id'] = $service_id;
                            $model_mapping_data['model_id'] = $model_id;
                            $model_mapping_data['plan_id'] = $plan_id;
                            $model_mapping_data['create_date'] = date('Y-m-d H:i:s');
                            $model_mapping_data['created_by'] = $this->session->userdata('employee_id');
                            $plan_model_mapping_id = $this->reusable_model->insert_into_table('warranty_plan_model_mapping', $model_mapping_data);
                            $arr_model_mapping_result[$row] = "Success";
                            if(empty($plan_model_mapping_id))
                            {
                                $arr_model_mapping_result[$row] = "Fail";
                            }
                        }
                    }
                }
            }
        }
        
        //        $file_path = "";
        //        if (!empty($file_data)) {
        //            $file_path = TMP_FOLDER . "extended_warranty_data-" . date('Y-m-d');
        //            $file = fopen($file_path . ".txt", "a+") or die("Unable to open file!");
        //            fwrite($file, $arr_state_mapping_result . "\n");
        //            fwrite($file, $arr_part_mapping_result . "\n");
        //            fwrite($file, $arr_model_mapping_result . "\n");
        //            fclose($file);
        //        }
    
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/bulk_upload_add_warranty', ['data' => $returnMsg, 'partner_id' => $post_data['partner_id']]);
    }

}
