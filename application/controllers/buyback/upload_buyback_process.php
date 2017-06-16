<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
                    $notify['notification'] = "Please Wait. File is under process.";
                    $this->load->view('notification', $notify);
                    
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
                    $is_mail_flag = false;
                    for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
                        //  Read a row of data into an array
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                        $rowData1 = array_combine($headings_new[0], $rowData[0]);
                       
                        $rowData1['partner_id'] = 247024;
                        $rowData1['partner_name'] = "Amazon";
                        $rowData1['partner_charge'] = $rowData1['DISCOUNT_VALUE'];
                        $dateObj1 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData1['ORDER_DAY']);             
                        $rowData1['order_date'] = $dateObj1->format('Y-m-d');
                        $rowData1['order_key'] = $rowData1['UseditemInfo'];
                        $rowData1['current_status'] = $rowData1['ORDERSTATUS'];
                        $rowData1['partner_sweetner_charges'] = $rowData1['Sweetnervalue'];
                        $rowData1['partner_order_id'] = $rowData1['ORDER_ID'];
                        $rowData1['partner_basic_charge'] = $rowData1['DISCOUNT_VALUE'];
                        $rowData1['delivery_date'] = "";
                        if(!empty($rowData1['OLD_ITEM_DEL_DATE'])){
                            $dateObj2 = PHPExcel_Shared_Date::ExcelToPHPObject($rowData1['OLD_ITEM_DEL_DATE']);    
                            $rowData1['delivery_date'] = $dateObj2->format('Y-m-d');
                        }

                        unset($rowData1['ORDER_ID']);
                        unset($rowData1['DISCOUNT_VALUE']);
                        unset($rowData1['ORDER_DAY']);
                        unset($rowData1['UseditemInfo']);
                        unset($rowData1['ORDERSTATUS']);
                        unset($rowData1['Sweetnervalue']);
                        unset($rowData1['OLD_ITEM_DEL_DATE']);
                        //Change index in lower case
                        $rowData2 = array_change_key_case($rowData1);
                        //set file data into global variable
                        $this->initialized_variable->set_post_buyback_order_details($rowData2);
                        //unset row data
                        unset($rowData2);
                        //Insert/Update BB order details
                        $status = $this->buyback->check_action_order_details();
                        if($status){} else{
                            $is_mail_flag = true;
                            $this->table->add_row($rowData1['order_id']);
                        }
                       
                    }
                    
                    $notify['notification'] = "File completely processed. ";
                    $this->load->view('notification', $notify);
                    
                    if($is_mail_flag){
                        $to = NITS_ANUJ_EMAIL_ID.",".ADIL_EMAIL_ID;
                        $cc = "abhaya@247around.com";

                        $subject = "Buyback Order Not inseted/Updated Issues.";
                        $message = "Please check below Order <br/><br/><br/>";
                        $message .= $this->table->generate();

                        $this->notify->sendEmail("booking@247around.com", $to, $cc, "", $subject, $message, "");
                    }
                    $response = array("code" => 247, "msg" => "File sucessfully processed.");
                    echo json_encode($response);
                    
                } catch (Exception $e) {
                    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                }
            } else {
                echo json_decode("Error", "File format is not correct. Only XLS or XLSX files are allowed.");
            }
        }
    }
    
    function upload_file(){
        if($_FILES['file']['name'] && $_FILES['file']['size'] > 0){
            echo json_encode(array("code"=>"247","msg"=>"success"));
        }else{
            echo json_encode(array("code"=>"-247","msg"=>"error"));
        }
        
    }
    

}