<?php 
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

ini_set('display_errors', '1');
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 360000); //3600 seconds = 60 minutes





class reports extends CI_Controller {

     /**
     * load list model and helpers
     */
    function __Construct() {
    parent::__Construct();
        $this->load->model('reporting_utils');
        $this->load->library('notify');
        $this->load->helper(array('form', 'url','array'));
        $this->load->library("miscelleneous");
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('notify');
        $this->load->library("initialized_variable");
        $this->load->library("push_notification_lib");
        $this->load->helper('file');
        $this->load->dbutil();
    }

/**
     * This function is used to return the query for the chosen tag and to view the custom report viewpage.
     * @param-flag
     * @return - when flag is 0 it will display custom report view page when flag is 1 it will return the query
     */
     function custom_reports($flag = 0) {
        $data = $this->reporting_utils->get_custom_query_data();
        $name = "";
        foreach ($data as $key => $value) {
            if($value['tag'] == "insert_partner_contacts_list"){
                $subject= sprintf($value['subject'], $name);
                $sql = $value['query'];
            }
        }
        $query = $this->db->query($sql); 
        if($flag){
            return $query;
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/custom_report');

    }
    /** Desc- This function is used to download Custom Report
    */
    function download_custom_report(){
        $custom_report= "Custom Report " . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $custom_report;
        $report = $this->custom_reports(1);
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        if(!empty($csv)){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        }
        log_message('info', __FUNCTION__ . ' Function End');
        //unlink($csv);
    }


}
    ?>