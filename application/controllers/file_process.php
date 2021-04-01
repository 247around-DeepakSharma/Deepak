<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class File_process extends CI_Controller {
    
    function __Construct() {
        parent::__Construct();

        $this->load->model('partner_model');
        $this->load->model('dashboard_model');
        $this->load->model('inventory_model');
        $this->load->model('service_centers_model');
        $this->load->model('accounting_model');
        $this->load->model('booking_model');
        $this->load->library('PHPReport');
        $this->load->library("session");
        $this->load->library("invoice_lib");
        $this->load->library('form_validation');
        $this->load->library('notify');
        $this->load->library('email');
        $this->load->helper(array('form', 'url', 'file', 'array'));
        $this->load->dbutil();
    }
    /**
     * @desc This is used to generate spare requested data file
     * @param String $partner_id
     */
    function downloadSpareRequestedParts($partner_id,$entity_type) {
        log_message("info", __METHOD__ . " Partner ID " . $partner_id);

        $where = "spare_parts_details.partner_id = '" . $partner_id . "' AND status = '" . SPARE_PARTS_REQUESTED . "' AND spare_parts_details.entity_type = '".$entity_type."' "
                . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') AND wh_ack_received_part = 1";
        if($this->input->post('state')){
           $state = $this->input->post('state');
           $where = $where." AND booking_details.state = '$state'";
       }

        $spare_parts = $this->partner_model->get_spare_parts_booking_list($where, false, false, true,0,NULL,true);
        if (!empty($spare_parts)) {
            $template = "Spare_Requested_Parts.xlsx";
            $templateDir = __DIR__ . "/excel-templates/";
            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
            if(ob_get_length() > 0) {
            ob_end_clean();
        }
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
            ob_end_clean();
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($output_file_excel) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($output_file_excel));
            readfile($output_file_excel);
            exec("rm -rf " . escapeshellarg($output_file_excel));
            if(file_exists($output_file_excel))
            {
                unlink($output_file_excel);
            }
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
                case 'sf_out_of_tat_by_partner_shipped_date':
                    $file_response = $this->create_sf_OOT_from_partner_shipped_date_file($partner_id,$category);
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
    
    function create_sf_OOT_from_partner_shipped_date_file($partner_id,$category){
        $select = "Select spare_parts_details.booking_id,parts_requested,spare_parts_details.model_number,spare_parts_details.serial_number,"
                . "parts_shipped,spare_parts_details.model_number_shipped,spare_parts_details.shipped_date as 'partner_shipped_date',spare_parts_details.defective_part_shipped,"
                . "remarks_by_partner,courier_name_by_partner,awb_by_partner,partner_challan_number";
        $file_data = $this->dashboard_model->get_sf_oot_spare_from_partner_shipped_details_by_partner_id($partner_id,$select);
        
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
        if(ob_get_length() > 0) {
            ob_end_clean();
        }
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

    /**
     * @desc This is used to generate spare requested data file
     * @param String $partner_id
     */
    function downloadSpareAssignedToPartner() {
              $sf_id = $this->session->userdata('service_center_id');
        
        $sf_states = $this->service_centers_model->get_warehouse_state($sf_id);
        //echo"<pre>";print_r($sf_states);exit;
        $where = "spare_parts_details.entity_type =  '"._247AROUND_PARTNER_STRING."' AND status = '" . SPARE_PARTS_REQUESTED . "' "
                . " AND booking_details.current_status IN ('"._247AROUND_PENDING."', '"._247AROUND_RESCHEDULED."') "
                . " AND wh_ack_received_part != 0 "
                . (!empty($sf_states)? " AND booking_details.state IN ('".implode("','",$sf_states)."')" : "");
        
        $select = "spare_parts_details.id, users.phone_number as customer_mobile, spare_parts_details.booking_id, spare_parts_details.partner_id, spare_parts_details.entity_type, spare_parts_details.service_center_id, spare_parts_details.partner_challan_number,GROUP_CONCAT(DISTINCT spare_parts_details.parts_requested) as parts_requested, purchase_invoice_id, users.name, "
                . "booking_details.booking_primary_contact_no, booking_details.partner_id as booking_partner_id, booking_details.flat_upcountry,"
                . "booking_details.booking_address,booking_details.initial_booking_date, booking_details.is_upcountry, "
                . "booking_details.upcountry_paid_by_customer,booking_details.amount_due,booking_details.state, service_centres.name as vendor_name, "
                . "service_centres.address, service_centres.state, service_centres.gst_no, service_centres.pincode, "
                . "service_centres.district,service_centres.id as sf_id,service_centres.is_gst_doc,service_centres.signature_file, "
                . " GROUP_CONCAT(DISTINCT inventory_stocks.stock) as stock, DATEDIFF(CURRENT_TIMESTAMP,  STR_TO_DATE(date_of_request, '%Y-%m-%d')) AS age_of_request,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.model_number) as model_number, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.serial_number) as serial_number,"
                . " spare_parts_details.quantity,"
                . " spare_parts_details.shipped_quantity,"
                . " GROUP_CONCAT(DISTINCT spare_parts_details.remarks_by_sc) as remarks_by_sc, spare_parts_details.partner_id, "
                . " GROUP_CONCAT(DISTINCT spare_parts_details.id) as spare_id, serial_number_pic, GROUP_CONCAT(DISTINCT spare_parts_details.inventory_invoice_on_booking) as inventory_invoice_on_booking, i.part_number ";

        $spare_parts = $this->service_centers_model->spare_assigned_to_partner($where, $select, "spare_parts_details.booking_id", $sf_id);

        if (!empty($spare_parts)) {
            $template = "Spare_Assigned_To_Partner.xlsx";
            $templateDir = __DIR__ . "/excel-templates/";
            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
            if(ob_get_length() > 0) {
            ob_end_clean();
        }
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
    
    
    /**
     * @desc This is used to generate spare quote requested data file
     * @param String $partner_id
     */
    function downloadPendingSpareQuote($partner_id,$entity_type) {
        log_message("info", __METHOD__ . " Partner ID " . $partner_id);

        $spare_parts = $this->partner_model->get_spare_parts_quote_booking_list($partner_id,$entity_type);
        if (!empty($spare_parts)) {
            $template = "Spare_Quote_Requested_Parts.xlsx";
            $templateDir = __DIR__ . "/excel-templates/";
            $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
            if(ob_get_length() > 0) {
            ob_end_clean();
        }
            $R = new PHPReport($config);
            $R->load(array(
                array(
                    'id' => 'spare',
                    'repeat' => true,
                    'data' => $spare_parts
                ),
                    )
            );

            $output_file_excel = "spare_parts_quote-" . date("Y-m-d") . ".xlsx";
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
    /**
     * @desc This function is used to download part invoice summary excel file.
     */
    function download_part_invoice_summary(){
        $date_range = $this->input->post('date_range');
        if(!empty($date_range)){
           $date_array = explode('-', $date_range);
           if(count($date_array) == 2){
               $from_date = date("Y-m-d", strtotime($date_array[0]));  
               $to_date = date("Y-m-d", strtotime($date_array[1].  "+1 days"));
               
               $c_balance = $this->inventory_model->call_procedure('part_invoice_summary',"'$from_date'");
               $res = $this->_download_part_invoice_summary($c_balance, $from_date, $to_date);
               echo $res;
               
           } else {
               echo json_encode(array('status' => false, 'message' => 'Please Select Valid Date'), true);
           }
        } 
    }
    /**
     * @desc This function is used to download part invoice summary excel file.
     */
    function _download_part_invoice_summary($c_balance, $from_date, $to_date){
        $data = $this->inventory_model->call_procedure('part_invoice_breakup',"'$from_date', '$to_date' ");
        $meta = array();
        $meta['meta']['opening_balance_date'] = date("d F Y", strtotime($from_date."-1 days"));
        $meta['meta']['opening_balance'] = $c_balance[0]['diff'];
        $meta['meta']['file_period'] = date("d F Y", strtotime($from_date))." To ".date("d F Y", strtotime($to_date. "-1 days"));
        $template = "Part Invoice Summary.xlsx";
        $output_file_excel = "part_invoice_summary".date('YmdHis').".xlsx";
        $res = $this->invoice_lib->generate_invoice_excel($template, $meta['meta'], $data, TMP_FOLDER.$output_file_excel);
        if($res){
            return json_encode(array('status' => true, 'message' => $output_file_excel, 'path' => base_url() . "file_process/downloadFile/" . $output_file_excel), true);
        } else {
            return json_encode(array('status' => false, 'message' => 'File is not creating. Please refresh & try again'), true);
        }
        //$this->downloadFile($output_file_excel);
        
    }
    /**
     * @desc This function is used to send part invoice summary balance to Account team. 
     */
    function send_part_invoice_summary_to_mail(){
        $from_date = date("Y-m-01");  
        $to_date = date("Y-m-d", strtotime("-1 days"));
        $fdate = date("Y-m-01", strtotime("-1 months"));

        $c_balance = $this->inventory_model->call_procedure('part_invoice_summary',"'$fdate'");
        $c = array();
        $c['purchase_invoice'] = $c_balance[0]['purchase'];
        $c['sale_invoice'] = $c_balance[0]['sale'];
        $c['opening_balance'] = $c_balance[0]['diff'];
        $c['opening_balance_date'] = date('Y-m-d', strtotime($fdate));
        //Insert opening blance the 
        $this->accounting_model->insert_part_invoice_opening_balance($c);
        $res = $this->_download_part_invoice_summary($c_balance, $from_date, $to_date);
        $s = json_decode($res, true);
        if($s['status']){
            $template = $this->booking_model->get_booking_email_template("part_invoice_summary");
            if(!empty($template)){
                $body = $template[0];
                $to = $template[1];
                $from = $template[2];
                $cc = $template[3];
                $subject = $template[4];

                $bcc = $template[5];

                $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $body, TMP_FOLDER.$s['message'],'part_invoice_summary');
                echo $this->email->print_debugger();
            }
            
        }
    }

}
