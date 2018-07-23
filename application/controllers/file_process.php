<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class File_process extends CI_Controller {
    
    function __Construct() {
        parent::__Construct();

        $this->load->model('partner_model');
        $this->load->model('dashboard_model');
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
    function downloadSpareRequestedParts($partner_id,$entity_type) {
        log_message("info", __METHOD__ . " Partner ID " . $partner_id);

        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status = '" . SPARE_PARTS_REQUESTED . "' AND entity_type = '".$entity_type."' "
                . " AND booking_details.current_status IN ('Pending', 'Rescheduled') AND wh_ack_received_part = 1";

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
    
    function create_inventory_dashboard_file($category,$partner_id){
        if(!empty($category) && !empty($partner_id)){
            $file_response = "";
            switch ($category){
                case 'total':
                    $file_response = $this->create_total_spare_count_file($partner_id,$category);
                    break;
                case 'partner_out_of_tat':
                    $file_response = $this->create_partner_OOT_file($partner_id,$category);
                    break;
                case 'sf_out_of_tat':
                    $file_response = $this->create_sf_OOT_file($partner_id,$category);
                    break;
                default :
                    $file_response = FALSE;
            }
            
            if(!empty($file_response)){
                $res['status'] = TRUE;
                $res['msg'] = base_url() . "file_process/downloadFile/".$file_response;
            }else{
                $res['status'] = false;
                $res['msg'] = 'error in generating file';
            }
        }else{
            $res['status'] = false;
            $res['msg'] = 'required parameter missing';
        }
        
        echo json_encode($res);
    }
    
    function create_total_spare_count_file($partner_id,$category){
        $select = "Select spare_parts_details.booking_id,parts_requested,spare_parts_details.model_number,spare_parts_details.serial_number,"
                . "parts_shipped,spare_parts_details.model_number_shipped,spare_parts_details.shipped_date as 'partner_shipped_date',spare_parts_details.defective_part_shipped,"
                . "remarks_by_partner,courier_name_by_partner,awb_by_partner,partner_challan_number";
        $file_data = $this->dashboard_model->get_partner_total_spare_details($partner_id,$select);
        
        if(!empty($file_data)){
            $template = "partner_total_spare_list.xlsx";
            $output_file_excel = $category.'_'. date("Y_m_d_H_i_s") . ".xlsx";
            //generate excel
            $file_name = $this->generate_excel_file($template,$output_file_excel,$file_data);
            
        }else{
            $file_name = "";
        }
        
        return $file_name;
    }
    
    function create_partner_OOT_file($partner_id,$category){
        $select = "Select spare_parts_details.booking_id,parts_requested,spare_parts_details.model_number,spare_parts_details.serial_number,"
                . "parts_shipped,spare_parts_details.model_number_shipped,spare_parts_details.shipped_date as 'partner_shipped_date',spare_parts_details.defective_part_shipped,"
                . "remarks_by_partner,courier_name_by_partner,awb_by_partner,partner_challan_number";
        $file_data = $this->dashboard_model->get_partner_oot_spare_details_by_partner_id($partner_id,$select);
        
        if(!empty($file_data)){
            $template = "partner_total_spare_list.xlsx";
            $output_file_excel = $category.'_'. date("Y_m_d_H_i_s") . ".xlsx";
            //generate excel
            $file_name = $this->generate_excel_file($template,$output_file_excel,$file_data);
            
        }else{
            $file_name = "";
        }
        
        return $file_name;
    }
    
    function create_sf_OOT_file($partner_id,$category){
        $select = "Select spare_parts_details.booking_id,parts_requested,spare_parts_details.model_number,spare_parts_details.serial_number,"
                . "parts_shipped,spare_parts_details.model_number_shipped,spare_parts_details.shipped_date as 'partner_shipped_date',spare_parts_details.defective_part_shipped,"
                . "remarks_by_partner,courier_name_by_partner,awb_by_partner,partner_challan_number";
        $file_data = $this->dashboard_model->get_sf_oot_spare_details_by_partner_id($partner_id,$select);
        
        if(!empty($file_data)){
            $template = "partner_total_spare_list.xlsx";
            $output_file_excel = $category.'_'. date("Y_m_d_H_i_s") . ".xlsx";
            //generate excel
            $file_name = $this->generate_excel_file($template,$output_file_excel,$file_data);
            
        }else{
            $file_name = "";
        }
        
        return $file_name;
    }
    
    function generate_excel_file($excel_template, $file_name, $excel_data) {
        $templateDir = __DIR__ . "/excel-templates/";
        $config = array(
            'template' => $excel_template,
            'templateDir' => $templateDir
        );

        //load template
        $R = new PHPReport($config);
        $R->load(array(
            array(
                'id' => 'spare_data',
                'repeat' => true,
                'data' => $excel_data
            ),
                )
        );

        $R->render('excel', TMP_FOLDER . $file_name);

        if (file_exists(TMP_FOLDER . $file_name)) {
            log_message('info', __FUNCTION__ . ' File created ' . $file_name);
            $res1 = 0;
            system(" chmod 777 " . TMP_FOLDER . $file_name, $res1);

            $file_name = $file_name;
        } else {
            log_message('info', __FUNCTION__ . ' File not created ' . $file_name);
            $file_name = "";
        }
        
        return $file_name;
    }

}