<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

ini_set('max_execution_time', 360000); //3600 seconds = 60 minutes

class Upload_buyback_process extends CI_Controller {
    var $Columfailed = "";
    var $upload_sheet_data = array();

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('PHPReport');
        $this->load->library('form_validation');
        $this->load->library('buyback');
        $this->load->library("initialized_variable");
        $this->load->library('table');
        $this->load->library('partner_utilities');
        $this->load->library('s3');
        $this->load->library('notify');

        $this->load->model("partner_model");
        $this->load->model('reporting_utils');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo PHP_EOL . 'Terminal Access Not Allowed' . PHP_EOL;
            redirect(base_url() . "employee/login");
        }
    }

    function index() {

        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/order_details_file_upload');
        $this->load->view('dashboard/dashboard_footer');
    }
    
     function price_sheet_upload() {

        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/price_sheet_upload');
        $this->load->view('dashboard/dashboard_footer');
    }

    function process_upload_order() {

        if (!empty($_FILES['file']['name'])) {
            $order_file = $_FILES["file"]["name"];
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            $MB = 1048576;
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls')) {
                if($_FILES['file']['size'] > 0 && $_FILES['file']['size'] < 2*$MB){
                    $inputFileName = $_FILES['file']['tmp_name'];
                    if ($pathinfo['extension'] == 'xlsx') {
                        $inputFileExtn = 'Excel2007';
                    } else {
                        $inputFileExtn = 'Excel5';
                    }

                    $template = array(
                        'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
                    );

                    $this->table->set_template($template);

                    $this->table->set_heading(array('Order ID'));

                    try {
    //                    $notify['notification'] = "Please Wait. File is under process.";
    //                    $this->load->view('notification', $notify, FALSE);

                        $objReader = PHPExcel_IOFactory::createReader($inputFileExtn);
                        $objPHPExcel = $objReader->load($inputFileName);

                        $sheet = $objPHPExcel->getSheet(0);
                        $highestRow = $sheet->getHighestRow();
                        $highestColumn = $sheet->getHighestColumn();

                        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);

                        $headings_new = array();
                        foreach ($headings as $heading) {
                            $heading = str_replace(array("/", "(", ")", " ", "."), "", $heading);
                            array_push($headings_new, str_replace(array(" "), "_", $heading));
                        }
                        $message = "";
                        $error = false;
                        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
                            //  Read a row of data into an array
                            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                            $rowData11 = array_combine($headings_new[0], $rowData[0]);
                            $rowData1 = array_change_key_case($rowData11);
                            $this->Columfailed = "";
                            $status = $this->check_column_exist($rowData1);
                            if ($status) {
                                //Change index in lower case
                                $this->initialized_variable->set_post_buyback_order_details(array());

                                $rowData1['partner_id'] = 247024;
                                $rowData1['partner_name'] = "Amazon";
                                $rowData1['partner_charge'] = $rowData1['discount_value'];
                                $dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData1['order_day']);
                                $rowData1['order_date'] = $dateObj1->format('Y-m-d');
                                $rowData1['order_key'] = $rowData1['buyback_details'];
                                $rowData1['current_status'] = $rowData1['orderstatus'];
                                $rowData1['partner_sweetner_charges'] = isset($rowData1['sweetenervalue'])?$rowData1['sweetenervalue']:'';
                                $rowData1['partner_order_id'] = $rowData1['order_id'];
                                $rowData1['partner_basic_charge'] = $rowData1['discount_value'];
                                $rowData1['delivery_date'] = "";
                                $rowData1['file_received_date'] = date('Y-m-d', strtotime($this->input->post('file_received_date')));
                                if ($rowData1['city'] == '0') {
                                    $rowData1['city'] = "";
                                }
                                if (!empty($rowData1['old_item_del_date'])) {
                                    $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData1['old_item_del_date']);
                                    $rowData1['delivery_date'] = $dateObj2->format('Y-m-d');
                                }
                                
                                $temp_arr['file_name'] = $order_file;
                                $temp_arr['file_received_date'] = $rowData1['file_received_date'];
                                $temp_arr['order_day'] = $dateObj1->format('Y-m-d');
                                $temp_arr['partner_name'] = $rowData1['partner_name'];
                                $temp_arr['subcat'] = $rowData1['subcat'];
                                $temp_arr['order_id'] = $rowData1['order_id'];
                                $temp_arr['city'] = $rowData1['city'];
                                $temp_arr['tracking_id'] = isset($rowData1['tracking_id'])?$rowData1['tracking_id']:'';
                                $temp_arr['discount_value'] = $rowData1['discount_value'];
                                $temp_arr['order_status'] = $rowData1['orderstatus'];
                                $temp_arr['old_item_del_date'] = $rowData1['delivery_date'];
                                $temp_arr['buyback_details'] =$rowData1['buyback_details'];
                                $temp_arr['sweetner_value'] = isset($rowData1['sweetenervalue'])?$rowData1['sweetenervalue']:'';
                                array_push($this->upload_sheet_data, $temp_arr);

                                unset($temp_arr);
                                unset($rowData1['order_id']);
                                unset($rowData1['discount_value']);
                                unset($rowData1['order_day']);
                                unset($rowData1['usediteminfo']);
                                unset($rowData1['orderstatus']);
                                unset($rowData1['sweetnervalue']);
                                unset($rowData1['old_item_del_date']);

                                //set file data into global variable
                                $this->initialized_variable->set_post_buyback_order_details($rowData1);

                                //Insert/Update BB order details
                                $status1 = $this->buyback->check_action_order_details();
                                $this->initialized_variable->set_post_buyback_order_details(array());
                                if ($status1) {

                                } else {

                                    $this->table->add_row($rowData1['partner_order_id']);
                                }
                            } else {

                                $error = true;
                                break;
                            }
                        }
                        
                        $insert_id = $this->bb_model->insert_bb_sheet_data($this->upload_sheet_data);
                        if($insert_id){
                            log_message('info',"Buyback Sheet Data Inserted");
                        }else{
                            log_message('info',"Error In Inserting Buyback Sheet Data");
                        }
                        $total_lead = $i;

                        if ($error) {
                            $response = array("code" => -247, "msg" => $this->Columfailed);
                            echo json_encode($response);
                        } else {

                            $to = NITS_ANUJ_EMAIL_ID . "," . ADIL_EMAIL_ID;
                            $cc = "abhaya@247around.com";


                            $subject = "Buyback Order is uploaded by " . $this->session->userdata('employee_id');

                            $message .= "Order File Name ---->".$order_file."<br/><br/>";
                            $message .= "Total lead  ---->" . $total_lead . "<br/><br/>";

                            $message .= "Total Delivered ---->" . ($this->initialized_variable->delivered_count()) . "<br/><br/>";
                            $message .= "Total Inserted ---->" . ($this->initialized_variable->total_inserted()) . "<br/><br/>";
                            $message .= "Total Updated ---->" . ($this->initialized_variable->total_updated()) . "<br/><br/>";
                            $message .= "Total Not Assigned ---->" . ($this->initialized_variable->not_assigned_order()) . "<br/><br/>";
                            $message .= "Please check below orders, these were neither inserted nor updated: <br/><br/><br/>";
                            $message .= $this->table->generate();

                            $this->notify->sendEmail("buyback@247around.com", $to, $cc, "", $subject, $message, "");

                            $this->upload_file_to_S3($order_file, _247AROUND_BB_ORDER_LIST, $_FILES['file']['tmp_name']);
                            $response = array("code" => 247, "msg" => "File sucessfully processed.");
                            echo json_encode($response);
                        }
                    } catch (Exception $e) {
                        die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                    }
                } else {
                    $response = array("code" => -247, "msg" => "Upload file size must be less than 2mb.");
                    echo json_encode($response);
                }
            } else {
                $response = array("code" => -247, "msg" => "File Format Is Incorrct. Please Upload Only xlsx Or xlx file");
                echo json_encode($response);
            }
        }
    }
    
    function upload_file_to_S3($file_name, $file_type, $file_path){
        $data['file_name'] = $file_name;
        $data['file_type'] = $file_type;
        $data['agent_id'] = $this->session->userdata('id');
        $insert_id = $this->partner_model->add_file_upload_details($data);
        if ($insert_id) {
            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/" . $file_name;
            $this->s3->putObjectFile($file_path, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        }
        return true;
    }

    /**
     * @desc this is used to check column field exist or not 
     * @param Array $rowData1
     * @return boolean
     */
    function check_column_exist($rowData1) {
        $message = "";
        $error = false;
        
        if (!array_key_exists('buyback_details', $rowData1)) {
            $message .= " BuyBack Details Column does not exist.<br/><br/>";
            $this->Columfailed .= " Buyback, ";
            $error = true;
        }

//        if (!array_key_exists('sweetenervalue', $rowData1)) {
//            $message .= " Sweetener Value Column does not exist. <br/><br/>";
//            $this->Columfailed .= " Sweetener Value, ";
//            $error = true;
//        }
        
        if (!array_key_exists('order_id', $rowData1)) {
      
            $message .= " Order ID Column does not exist. <br/><br/>";
            $this->Columfailed .= "Order ID Column, ";
            $error = true;
        }
         
        if (!array_key_exists('discount_value', $rowData1)) {
       
            $message .= " Discount Value Column does not exist. <br/><br/>";
            $this->Columfailed .= " Discount Value Column, ";
            $error = true;
        }
        if (!array_key_exists('order_day', $rowData1)) {

            $message .= " Order day Column does not exist. <br/><br/>";
            $this->Columfailed .= " Order Day Column, ";
            $error = true;
        }
        if (!array_key_exists('city', $rowData1)) {
      
            $message .= " City Column does not exist. <br/><br/>";
            $this->Columfailed .= "City Column, ";
            $error = true;
        }
        if (!array_key_exists('orderstatus', $rowData1)) {
      
            $message .= " Order Status does not exist. <br/><br/>";
            $this->Columfailed .= "Order Status ";
            $error = true;
        }
         
        if ($error) {
            $message .= " Please check and upload again.";
            $this->Columfailed .= " column does not exist.";
            $to = NITS_ANUJ_EMAIL_ID . "," . ADIL_EMAIL_ID;
            $cc = "abhaya@247around.com";
            $subject = "Failure! Buyback Order is uploaded by " . $this->session->userdata('employee_id');
            $this->notify->sendEmail("buyback@247around.com", $to, $cc, "", $subject, $message, "");
            return false;
        } else {
            return true;
        }
    }

    /**
     * @desc This function is used to upload the charges list excel
     * @para, void
     * @return json
     */
    function proces_upload_bb_price_charges() {
        $return = $this->partner_utilities->validate_file($_FILES);
        if ($return == "true") {

            //Making process for file upload
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if ($pathinfo['extension'] == 'xlsx') {
                $inputFileExtn = 'Excel2007';
            } else {
                $inputFileExtn = 'Excel5';
            }
            $tmpFile = $_FILES['file']['tmp_name'];
            $charges_file = "Buyback-Charges-List-" . date('Y-m-d-H-i-s') . '.xlsx';
            move_uploaded_file($tmpFile, TMP_FOLDER . $charges_file);

            //Processing File
            $is_insert = $this->process_bb_chargs_file(TMP_FOLDER . $charges_file, $inputFileExtn);
            if ($is_insert) {

                //Adding Details in File_Uploads table as well
                $data['file_name'] = $charges_file;
                $data['file_type'] = _247AROUND_BB_PRICE_LIST;
                $data['agent_id'] = $this->session->userdata('id');
                $insert_id = $this->partner_model->add_file_upload_details($data);
                if ($insert_id) {
                    //Upload files to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/" . $charges_file;
                    $this->s3->putObjectFile(TMP_FOLDER . $charges_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                }

                //delete file from the system
                exec("rm -rf " . escapeshellarg(TMP_FOLDER . $charges_file));

                //Return success Message
                $msg = "File Uploaded Successfully.";
                $response = array("code" => '247', "msg" => $msg);
                echo json_encode($response);
            } else {
                $msg = "Error!!! Please Try Again...";
                $response = array("code" => '-247', "msg" => $msg);
                echo json_encode($response);
            }
        } else {
            $msg = $return['error'];
            $response = array("code" => '-247', "msg" => $msg);
            echo json_encode($response);
        }
    }

    /**
     * @desc This function is used to process the charges list excel and insert into the table
     * @para, $charges_file string
     * @return $flag boolean
     */
    private function process_bb_chargs_file($charges_file) {
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($charges_file);
        $charges_data = array();
        $count = 1;
        $flag = False;
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($count > 1) {
                    $data = $this->set_charges_rows_data($row);
                    array_push($charges_data, $data);
                }
                $count++;
            }
            $count = 1;
            $return = $this->bb_model->insert_charges_data_in_batch($charges_data);
            if ($return) {
                $flag = True;
            } else {
                $flag = False;
            }
        }

        return $flag;
    }

    /**
     * @desc This function is used to make final data from charges list excel to insert into the table
     * @para, $row array
     * @return $tmp array
     */
    private function set_charges_rows_data($row) {
        $tmp['partner_id'] = $row[0];
        $tmp['cp_id'] = $row[2];
        $tmp['service_id'] = $row[4];
        $tmp['category'] = $row[5];
        $tmp['brand'] = isset($row[6]) ? $row[6] : NULL;
        $tmp['physical_condition'] = isset($row[7]) ? $row[7] : NULL;
        $tmp['working_condition'] = isset($row[8]) ? $row[8] : '';
        $tmp['city'] = $row[9];
        $tmp['order_key'] = $row[10];
        $tmp['partner_basic'] = isset($row[11]) ? $row[11] : '0';
        $tmp['partner_tax'] = isset($row[12]) ? $row[12] : '0';
        $tmp['partner_total'] = isset($row[13]) ? $row[13] : '0';
        $tmp['cp_basic'] = isset($row[14]) ? $row[14] : '0';
        $tmp['cp_tax'] = isset($row[15]) ? $row[15] : '0';
        $tmp['cp_total'] = isset($row[16]) ? $row[16] : '0';
        $tmp['around_basic'] = isset($row[17]) ? $row[17] : '0';
        $tmp['around_tax'] = isset($row[18]) ? $row[18] : '0';
        $tmp['around_total'] = isset($row[19]) ? $row[19] : '0';
        $tmp['visible_to_partner'] = isset($row[20]) ? $row[20] : '0';
        $tmp['visible_to_cp'] = isset($row[21]) ? $row[21] : '0';
        $tmp['create_date'] = date("Y-m-d H:i:s");

        return $tmp;
    }

    public function upload_file_history($file_type) {
        $data = $this->reporting_utils->get_uploaded_file_history($file_type);
        print_r(json_encode($data, TRUE));
    }

}
