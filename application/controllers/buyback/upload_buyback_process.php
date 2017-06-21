<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

ini_set('max_execution_time', 360000); //3600 seconds = 60 minutes
require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';
class Upload_buyback_process extends CI_Controller {

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
    
    function index(){
        
        $this->load->view('dashboard/header/' . $this->session->userdata('user_group'));
        $this->load->view('buyback/order_details_file_upload');
        $this->load->view('dashboard/dashboard_footer');
    }
    
    function process_upload_order() {
        if (!empty($_FILES['file']['name'])) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $_FILES['file']['size'] > 0) {
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
                        if (isset($rowData1['usediteminfo'])) {
                            //Change index in lower case
                            $this->initialized_variable->set_post_buyback_order_details(array());
                            $rowData1['partner_id'] = 247024;
                            $rowData1['partner_name'] = "Amazon";
                            $rowData1['partner_charge'] = $rowData1['discount_value'];
                            $dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData1['order_day']);
                            $rowData1['order_date'] = $dateObj1->format('Y-m-d');
                            $rowData1['order_key'] = $rowData1['usediteminfo'];
                            $rowData1['current_status'] = $rowData1['orderstatus'];
                            $rowData1['partner_sweetner_charges'] = $rowData1['sweetnervalue'];
                            $rowData1['partner_order_id'] = $rowData1['order_id'];
                            $rowData1['partner_basic_charge'] = $rowData1['discount_value'];
                            $rowData1['delivery_date'] = "";
                            if($rowData1['city'] == '0'){
                                $rowData1['city'] = "";
                            } 
                            if (!empty($rowData1['old_item_del_date'])) {
                                $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData1['old_item_del_date']);
                                $rowData1['delivery_date'] = $dateObj2->format('Y-m-d');
                            }

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
                            $status = $this->buyback->check_action_order_details();
                            $this->initialized_variable->set_post_buyback_order_details(array());
                            if ($status) {
                                
                            } else {

                                $this->table->add_row($rowData1['partner_order_id']);
                            }
                        } else {
                            $message = " Used Item Info Column is not exit. Please check and upload again. <br/><br/>";
                            $error = true;
                            break;
                        }
                    }
                    $total_lead = $i;
                   
                    $to = NITS_ANUJ_EMAIL_ID.",".ADIL_EMAIL_ID;
                    $cc = "abhaya@247around.com";
                    

                    $subject = "Buyback Order is uploaded by ".$this->session->userdata('employee_id');
                    $message  .= "Total lead ".$total_lead."<br/>";
                    $message .= "Total Delivered(Inserted/Updated) ".($this->initialized_variable->delivered_count() -1)."<br/>";
                    $message .= "Total Inserted".($this->initialized_variable->total_inserted() -1)."<br/>";
                    $message .= "Total Updated".($this->initialized_variable->total_updated() -1)."<br/>";
                    $message .= "Please check below Order, these are neither inserted and nor uddated <br/><br/><br/>";
                    $message .= $this->table->generate();

                    $this->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $message, "");
                    if($error){
                        $response = array("code" => -247, "msg" => "Used Item Info Filed is not exist.");
                        echo json_encode($response);
                        
                    } else {
                        $response = array("code" => 247, "msg" => "File sucessfully processed.");
                        echo json_encode($response);
                    }
                    
                    
                } catch (Exception $e) {
                    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                }
            } else {
                echo json_decode("Error", "File format is not correct. Only XLS or XLSX files are allowed.");
            }
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
            $is_insert = $this->process_bb_chargs_file(TMP_FOLDER . $charges_file,$inputFileExtn);
            if ($is_insert) {

                //Adding Details in File_Uploads table as well
                $data['file_name'] = $charges_file;
                $data['file_type'] = _247AROUND_BB_PRICE_LIST;
                $data['agent_id'] = $this->session->userdata('id');
                $insert_id = $this->partner_model->add_file_upload_details($data);
                if($insert_id){ 
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
            if($return){
                $flag = True;
            }else{
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
        $tmp['service_id'] = $row[4] ;
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
    
    public function upload_file_history($file_type){
        $data = $this->reporting_utils->get_uploaded_file_history($file_type);
        print_r(json_encode($data, TRUE));
    }

}