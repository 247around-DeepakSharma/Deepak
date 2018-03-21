<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class File_process extends CI_Controller {
    
    function __Construct() {
        parent::__Construct();

        $this->load->model('partner_model');
        $this->load->library('PHPReport');
        $this->load->library("session");
        $this->load->library('form_validation');

        $this->load->helper(array('form', 'url', 'file', 'array'));
        $this->load->dbutil();
    }
    /**
     * @desc This is used to generate spare requested data file
     * @param String $partner_id
     */
    function downloadSpareRequestedParts($partner_id) {
        log_message("info", __METHOD__ . " Partner ID " . $partner_id);

        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') ";

        $spare_parts = $this->partner_model->get_spare_parts_booking_list($where, false, false, true);
        if (!empty($spare_parts)) {
            $template = "Spare Requested Parts.xlsx";
            $templateDir = __DIR__ . "/excel-templates/";
            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
            $R = new PHPReport($config);
            $R->load(array(
                array(
                    'id' => 'spare',
                    'repeat' => true,
                    'data' => $spare_parts
                ),
                    )
            );

            $output_file_excel = "spare_parts-" . date("Y-m-d") . ".xlsx";
            $opt = TMP_FOLDER. $output_file_excel;

            $R->render('excel', $opt);

            log_message('info', __FUNCTION__ . ' File created ' . $output_file_excel);
            $res1 = 0;

            if (file_exists($opt)) {
                system(" chmod 777 " . $opt, $res1);

                echo json_encode(array("response" => "success", "path" => base_url() . "file_process/downloadFile/" . $output_file_excel));
            } else {
                log_message("info", __METHOD__ . " Partner ID " . $partner_id. " File Not Generated");
                echo json_encode(array("response" => "failed", "message" => "File Not Generated"));
            }
        } else {
            log_message("info", __METHOD__ . " Partner ID " . $partner_id. " Data Not Found");
            echo json_encode(array("response" => "failed", "message" => "Data Not Found"));
        }
    }
    
    function downloadFile($filename){
        $output_file_excel = TMP_FOLDER.$filename;
        if (file_exists($output_file_excel)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($output_file_excel) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($output_file_excel));
            readfile($output_file_excel);
            exec("rm -rf " . escapeshellarg($output_file_excel));
            exit;
        }
    }

}