<?php


class Invoice_lib {
    
   public function __construct() {
	$this->ci = & get_instance();
        $this->ci->load->library('PHPReport');
        $this->ci->load->library("miscelleneous");
        $this->ci->load->library('s3');
    }
    
    function create_invoice_id($start_name){
        $invoice_id_tmp = $this->_get_partial_invoice_id($start_name);
        $where = "( invoice_id LIKE '%".$invoice_id_tmp."%' )";
        $invoice_no_temp = $this->ci->invoices_model->get_invoices_details($where);

        $invoice_no = 1;
        $int_invoice = array();
        if (!empty($invoice_no_temp)) {
            foreach ($invoice_no_temp as  $value) {
                 $explode = explode($invoice_id_tmp, $value['invoice_id']);
                 array_push($int_invoice, $explode[1] + 1);
            }
            rsort($int_invoice);
            $invoice_no = $int_invoice[0];
        }
        log_message('info', __FUNCTION__ . " Exit....");
   
        return trim($invoice_id_tmp . sprintf("%'.04d\n", $invoice_no));
    }
    
    function _get_partial_invoice_id($start_name){
        $current_month = date('m');
        // 3 means March Month
        if ($current_month > 3) {
            $financial = date('y'). (date('y') + 1);
        } else {
            $financial = (date('y') - 1) .  date('y');
        }

        return $start_name . "-"  . $financial . "-" ;
        
    }
    
    function send_request_to_create_main_excel($invoices, $invoice_type){
        $invoices['meta']['recipient_type'] = "Original Copy";
        $output_file_excel = TMP_FOLDER . $invoices['meta']['invoice_id'] . "-draft.xlsx";
        $copy_output_file_excel = TMP_FOLDER . "copy_".$invoices['meta']['invoice_id'] . "-draft.xlsx";
        if ($invoice_type == "final") {
            $output_file_excel = TMP_FOLDER . $invoices['meta']['invoice_id'] . ".xlsx";
            $copy_output_file_excel = TMP_FOLDER . "copy_".$invoices['meta']['invoice_id'] . ".xlsx";
            }

        $status = $this->generate_invoice_excel($invoices['meta']['invoice_template'],  $invoices['meta'], $invoices['booking'], $output_file_excel);
        if($status){
             $invoices['meta']['recipient_type'] = "Duplicate Copy";
             $this->generate_invoice_excel($invoices['meta']['invoice_template'], $invoices['meta'], $invoices['booking'],$copy_output_file_excel);
             return TRUE;
        } else{
            return FALSE;
        }
    }
    
    function generate_invoice_excel($template, $meta, $data, $output_file_excel) {
       
        // directory
        $templateDir = FCPATH . "application/controllers/excel-templates/";
        $config = array(
                'template' => $template,
                'templateDir' => $templateDir
            );

            //load template
        $R = new PHPReport($config);
        $R->load(array(
            array(
                'id' => 'meta',
                'repeat' => false,
                'data' => $meta,
                'format' => array(
                    'date' => array('datetime' => 'd/M/Y')
                )
            ),
            array(
                'id' => 'booking',
                'repeat' => true,
                'data' => $data,
            ),
                )
        );
        
        $res1 = 0;
        if (file_exists($output_file_excel)) {

            system(" chmod 777 " . $output_file_excel, $res1);
            unlink($output_file_excel);
        }
        $cell = false;
        $sign_path = false;
//        if(isset($meta['sign_path'])){
//          $cell = $meta['cell'];
//          $sign_path = $meta['sign_path'];
//        }
        $R->render('excel', $output_file_excel,$cell, $sign_path);
        
        log_message('info', __FUNCTION__ . ' File created ' . $output_file_excel);

        if (file_exists($output_file_excel)) {
            system(" chmod 777 " . $output_file_excel, $res1);
            return true;
            
        } else {
            return false;
        }
    }
    
    function send_request_to_convert_excel_to_pdf($invoice_id, $invoice_type, $copy = false){
        $excel_file_to_convert_in_pdf = $invoice_id.'-draft.xlsx';
        
        if ($invoice_type == "final") {
            //generate main invoice pdf
            $excel_file_to_convert_in_pdf = $invoice_id.'.xlsx';
            
        } 
        $main_pdf = $this->_request_to_convert_excel_to_pdf($excel_file_to_convert_in_pdf,$invoice_id, "invoices-excel");
        $copy_invoice = "copy_".$excel_file_to_convert_in_pdf;
        $copy_pdf = $this->_request_to_convert_excel_to_pdf($copy_invoice,$invoice_id, "invoices-excel");
        
        if($copy){
           $array = array("main_pdf_file_name" =>$copy_pdf, "excel_file" => $excel_file_to_convert_in_pdf, "copy_file" =>$main_pdf );
        } else {
            $array = array("main_pdf_file_name" =>$main_pdf, "excel_file" => $excel_file_to_convert_in_pdf, "copy_file" => $copy_pdf );
        }
        
       return $array;
    }
    
    function _request_to_convert_excel_to_pdf($excel_file, $invoice_id, $directory ){
        $json_result = $this->ci->miscelleneous->convert_excel_to_pdf(TMP_FOLDER.$excel_file,$invoice_id, $directory);
        log_message('info', __FUNCTION__ . ' PDF JSON RESPONSE' . print_r($json_result,TRUE));
        $pdf_response = json_decode($json_result,TRUE);
        $output_pdf_file_name = $excel_file;
        if($pdf_response['response'] === 'Success'){
            $output_pdf_file_name = $pdf_response['output_pdf_file'];
            log_message('info', __FUNCTION__ . ' Generated PDF File Name' . $output_pdf_file_name);
        } else if($pdf_response['response'] === 'Error'){
               
            log_message('info', __FUNCTION__ . ' Error in Generating PDF File');
       }
       
       return $output_pdf_file_name;
    }
    
    function upload_invoice_to_S3($invoice_id, $detailed){
        $bucket = BITBUCKET_DIRECTORY;

        $directory_xls = "invoices-excel/" . $invoice_id . ".xlsx";
        $directory_copy_xls = "invoices-excel/copy_" . $invoice_id . ".xlsx";

        $this->ci->s3->putObjectFile(TMP_FOLDER . $invoice_id . ".xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        $this->ci->s3->putObjectFile(TMP_FOLDER . "copy_".$invoice_id . ".xlsx", $bucket, $directory_copy_xls, S3::ACL_PUBLIC_READ);
        if($detailed){
            $directory_detailed = "invoices-excel/" . $invoice_id . "-detailed.xlsx";
            $this->ci->s3->putObjectFile(TMP_FOLDER . $invoice_id . "-detailed.xlsx", $bucket, $directory_detailed, S3::ACL_PUBLIC_READ);
        }
    }
}
