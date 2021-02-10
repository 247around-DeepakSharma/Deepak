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
     * This function is used to view the custom report viewpage.
    */
    function custom_reports(){
       $this->miscelleneous->load_nav_header();
       $reports = $this->reporting_utils->get_custom_query_data();
       $this->load->view('employee/custom_report', ['reports' => $reports]); 
    }

    /**
     * This function is used to return the query for the chosen tag.
     * @param - $tag
     * @param - $arr_date_filter (Array of Date Filter)
     * @return - $query 
     */
    function custom_reporting($tag, $arr_date_filter) {
        log_message('info', __FUNCTION__ . ' => Report => ' . $tag . 'Filter => ' . json_encode($arr_date_filter));

        $query = "";
        $data = $this->reporting_utils->get_custom_query_data($tag, $arr_date_filter);       

        if (!empty($data[0]['query'])) {
            $sql = $data[0]['query'];
            if(!empty($data[0]['date_filter'])){
                $start_date = $end_date = date("Y-m-d"); 
                if(!empty($arr_date_filter[0])){
                    $start_date = $arr_date_filter[0];
                }
                if(!empty($arr_date_filter[1])){
                    $end_date = $arr_date_filter[1];
                }
                $sql = sprintf($sql, $start_date, $end_date);
            }
            $query = $this->db->query($sql);
        }         
        return $query;
    }
    /** Desc- This function is used to download Custom Report dynamically.
     */
    function download_custom_report($tag, $period = "") {
        $arr_date_filter = array();
        if(!empty($period)){
            $period = urldecode($period);
            $arr_date_filter = explode(" - ", $period);
        }
        log_message('info', __FUNCTION__ . ' => Tag: ' . $tag);
		
        $custom_report = $tag . "-" . time() . ".csv";
        $csv = TMP_FOLDER . $custom_report;
		
        log_message('info', __FUNCTION__ . ' => Tag-URI: %%%' . $tag . " => " .json_encode($arr_date_filter));

        $report = $this->custom_reporting($tag, $arr_date_filter);

        if (!empty($report)) {
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);

            log_message('info', __FUNCTION__ . ' => Rendered CSV');

            write_file($csv, $new_report);

            if (!empty($csv)) {
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
        }

        log_message('info', __FUNCTION__ . ' Function End');
    }


}
