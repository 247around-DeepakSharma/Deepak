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
        $invoice_no_temp = $this->ci->invoices_model->get_invoices_details($where, "invoice_id");
        
        $new_invoice = $this->ci->invoices_model->get_new_invoice_data($where, "invoice_id");
        $invoice_array = array();
        if(!empty($invoice_no_temp) && !empty($new_invoice)){
            
           $invoice_array =  array_merge($invoice_no_temp, $new_invoice);
            
        } else if(!empty ($invoice_no_temp) || empty($new_invoice)){
            
           $invoice_array =  $invoice_no_temp;
            
        } else if(empty ($invoice_no_temp) || !empty($new_invoice)){
            $invoice_array =  $new_invoice;
        }

        $invoice_no = 1;
        $int_invoice = array();
        if (!empty($invoice_array)) {
            foreach ($invoice_array as  $value) {
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
    
    function send_request_to_create_main_excel($invoices, $invoice_type, $triplicate = FALSE){
        $invoices['meta']['recipient_type'] = "Original Copy";
        $output_file_excel = TMP_FOLDER . $invoices['meta']['invoice_id'] . "-draft.xlsx";
        $copy_output_file_excel = TMP_FOLDER . "copy_".$invoices['meta']['invoice_id'] . "-draft.xlsx";
        if ($invoice_type == "final") {
            $output_file_excel = TMP_FOLDER . $invoices['meta']['invoice_id'] . ".xlsx";
            $copy_output_file_excel = TMP_FOLDER . "copy_".$invoices['meta']['invoice_id'] . ".xlsx";
            if($triplicate){
                $triple_output_file_excel = TMP_FOLDER . "triplicate_".$invoices['meta']['invoice_id'] . ".xlsx";
            }
        }

        $status = $this->generate_invoice_excel($invoices['meta']['invoice_template'],  $invoices['meta'], $invoices['booking'], $output_file_excel);
        if($status){
             $invoices['meta']['recipient_type'] = "Duplicate Copy";
             $this->generate_invoice_excel($invoices['meta']['invoice_template'], $invoices['meta'], $invoices['booking'],$copy_output_file_excel);
             if($triplicate){
                $invoices['meta']['recipient_type'] = "Triplicate Copy";
                $this->generate_invoice_excel($invoices['meta']['invoice_template'], $invoices['meta'], $invoices['booking'],$triple_output_file_excel);
             }
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
    /**
     * @desc This function is used to generate invoice from html to pdf 
     * @param Array $invoices
     * @param String $invoice_type
     * @param boolean $copy
     * @param boolean $triplicate
     * @return Array
     */
    function convert_invoice_file_into_pdf($invoices, $invoice_type, $copy = false, $triplicate = FALSE){
       
        $output_file_name = $invoices['meta']['invoice_id'].'-draft';
        if ($invoice_type == "final") {
            //generate main invoice pdf
            $output_file_name = $invoices['meta']['invoice_id'];
            
        }
        $main_template = explode(".xlsx", $invoices['meta']['invoice_template']);
        $invoices['meta']['recipient_type'] = "Original Copy";
         $html = $this->ci->load->view('templates/'.$main_template[0], $invoices, true); 
        //convert html into pdf
        $json_result = $this->ci->miscelleneous->convert_html_to_pdf($html,$invoices['meta']['invoice_id'],$output_file_name.".pdf","invoices-excel");
        $pdf_response = json_decode($json_result,TRUE);
        
        if(!empty($pdf_response) && $pdf_response['response'] == "Success"){
            $copy_invoice = "copy_".$output_file_name.".pdf";
            $invoices['meta']['recipient_type'] = "Duplicate Copy";
            
            $html1 = $this->ci->load->view('templates/'.$main_template[0], $invoices, true); 
            $this->ci->miscelleneous->convert_html_to_pdf($html1,$invoices['meta']['invoice_id'],$copy_invoice,"invoices-excel");
          
            if($triplicate){
             
                $triplicate_invoice = "triplicate_".$output_file_name.".pdf";
                $invoices['meta']['recipient_type'] = "Triplicate Copy";
                $html2 = $this->ci->load->view('templates/'.$main_template[0], $invoices, true); 
                $this->ci->miscelleneous->convert_html_to_pdf($html2,$invoices['meta']['invoice_id'],$triplicate_invoice,"invoices-excel");
               
                
                $array = array("main_pdf_file_name" =>$copy_invoice, "copy_file" =>$output_file_name.".pdf",
                    'triplicate_file' => $triplicate_invoice, "excel_file" => $output_file_name.".xlsx");
            } else if($copy){
                $array = array("main_pdf_file_name" =>$copy_invoice, "copy_file" =>$output_file_name.".pdf", "excel_file" => $output_file_name.".xlsx" );
             } else {
                $array = array("main_pdf_file_name" =>$output_file_name.".pdf",  "copy_file" => $copy_invoice, "excel_file" => $output_file_name.".xlsx" );
             }
             
             return $array;
        } else {
            return $this->send_request_to_convert_excel_to_pdf($invoices['meta']['invoice_id'], $invoice_type, $copy, $triplicate);
        }
    }
    
    function send_request_to_convert_excel_to_pdf($invoice_id, $invoice_type, $copy = false, $triplicate = FALSE){
        $excel_file_to_convert_in_pdf = $invoice_id.'-draft.xlsx';
        
        if ($invoice_type == "final") {
            //generate main invoice pdf
            $excel_file_to_convert_in_pdf = $invoice_id.'.xlsx';
            
        } 
        $main_pdf = $this->_request_to_convert_excel_to_pdf($excel_file_to_convert_in_pdf,$invoice_id, "invoices-excel");
        $copy_invoice = "copy_".$excel_file_to_convert_in_pdf;
        $copy_pdf = $this->_request_to_convert_excel_to_pdf($copy_invoice,$invoice_id, "invoices-excel");
        
        if($triplicate){
            $triplicate_invoice = "triplicate_".$excel_file_to_convert_in_pdf;
            $triplicate_pdf = $this->_request_to_convert_excel_to_pdf($triplicate_invoice,$invoice_id, "invoices-excel");
            
            $array = array("main_pdf_file_name" =>$copy_pdf, "excel_file" => $excel_file_to_convert_in_pdf, "copy_file" =>$main_pdf,
                    'triplicate_file' => $triplicate_pdf);
            
        } else if($copy){
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
    
    function upload_invoice_to_S3($invoice_id, $detailed, $triplicate = false){
        $bucket = BITBUCKET_DIRECTORY;

        $directory_xls = "invoices-excel/" . $invoice_id . ".xlsx";
        $directory_copy_xls = "invoices-excel/copy_" . $invoice_id . ".xlsx";

        $this->ci->s3->putObjectFile(TMP_FOLDER . $invoice_id . ".xlsx", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        $this->ci->s3->putObjectFile(TMP_FOLDER . "copy_".$invoice_id . ".xlsx", $bucket, $directory_copy_xls, S3::ACL_PUBLIC_READ);
        if($triplicate){
            $directory_triplicate_xls = "invoices-excel/copy_" . $invoice_id . ".xlsx";
            $this->ci->s3->putObjectFile(TMP_FOLDER . "triplicate_".$invoice_id . ".xlsx", $bucket, $directory_triplicate_xls, S3::ACL_PUBLIC_READ);
        }
        if($detailed){
            $directory_detailed = "invoices-excel/" . $invoice_id . "-detailed.xlsx";
            $this->ci->s3->putObjectFile(TMP_FOLDER . $invoice_id . "-detailed.xlsx", $bucket, $directory_detailed, S3::ACL_PUBLIC_READ);
        }
    }
    
    /**
     * @desc This function is used to call taxpro gst api 
     * @param String $vendor_id
     * @param String $gst_number
     * @return api response 
     */
    function taxpro_gstin_checking_curl_call($gst_no, $vendor_id=""){ 
        if(!$vendor_id){
          $vendor_id = _247AROUND;
        }
        $activity = array(
            'entity_type' => 'vendor',
            'partner_id' => $vendor_id,
            'activity' => __METHOD__,
            'header' => "",
            'json_request_data' => "https://api.taxprogsp.co.in/commonapi/v1.1/search?aspid=".ASP_ID."&password=".ASP_PASSWORD."&Action=TP&Gstin=".$gst_no,
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.taxprogsp.co.in/commonapi/v1.1/search?aspid=".ASP_ID."&password=".ASP_PASSWORD."&Action=TP&Gstin=".$gst_no,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $api_response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if($err){
            $activity['json_response_string'] = $err;
            return false;
        }
        else{
            $activity['json_response_string'] = $api_response;
            $response = json_decode($api_response, true);
            if(isset($response['error'])){ 
                if($response['error']['error_cd'] != INVALID_GSTIN){  /**** mail not send on invalid gst number *****/
                    $email_template = $this->ci->booking_model->get_booking_email_template(TAXPRO_API_FAIL);
                    if(!empty($email_template)){
                        $message = vsprintf($email_template[0], array("GST NO - ".$gst_no,"Filled by - ".$this->ci->session->userdata('emp_name'), $api_response));  
                        $to = $email_template[1];
                        $this->ci->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $email_template[3] , $email_template[5], $email_template[4], $message, '', TAXPRO_API_FAIL);
                    }
                }
                return $api_response;
            }
            else{ 
               return $api_response;
            }
        }
        $this->ci->partner_model->log_partner_activity($activity);
    }
    
    /**
     * @desc This function is used to check GSTIN detail by using taxpro api
     * @param String $vendor_id
     * @return array gst type and status
     */
    function get_gstin_status_by_api($vendor_id){
        $data = array();
        $vendor = $this->ci->vendor_model->getVendorDetails('gst_no, gst_status, gst_taxpayer_type, company_name, gst_cancelled_date', array('id'=>$vendor_id), 'id', array());
        if(!empty($vendor[0]['gst_no'])){
            
            $api_response = $this->taxpro_gstin_checking_curl_call($vendor[0]['gst_no'], $vendor_id);
            if (!$api_response) {
                $data['status'] = 'error'; 
                return $data;
            } else { 
                //$response = '{"stjCd":"DL086","lgnm":"SUDESH KUMAR","stj":"Ward 86","dty":"Regular","adadr":[],"cxdt":"","gstin":"07ALDPK4562B1ZG","nba":["Recipient of Goods or Services","Service Provision","Retail Business","Wholesale Business","Works Contract"],"lstupdt":"17/04/2018","rgdt":"01/07/2017","ctb":"Proprietorship","pradr":{"addr":{"bnm":"BLOCK 4","st":"GALI NO. 5","loc":"HARI NAGAR ASHRAM","bno":"A-144/5","dst":"","stcd":"Delhi","city":"","flno":"G/F","lt":"","pncd":"110014","lg":""},"ntr":"Recipient of Goods or Services, Service Provision, Retail Business, Wholesale Business, Works Contract"},"tradeNam":"UNITED HOME CARE","sts":"Active","ctjCd":"ZK0601","ctj":"RANGE - 161"}';
                //$api_response = '{"status_cd":"0","error":{"error_cd":"GSP020A","message":"Error: Invalid ASP Password."}}';
                $response = json_decode($api_response, true);
                if(isset($response['error'])){ 
                    $data['status'] = 'error'; 
                    return $data;
                }
                else{ 
                    $data['gst_taxpayer_type'] = $response['dty']; //Regular
                    $data['gst_status'] = $response['sts']; //Active
                    if(isset($response['cxdt']) && !empty($response['cxdt'])){
                       
                        $data['gst_cancelled_date'] = date("Y-m-d", strtotime(str_replace('/','-',$response['cxdt'])));
                    } else {
                        $data['gst_cancelled_date'] = NULL;
                    }

                    if($vendor[0]['gst_taxpayer_type'] != $response['dty'] || $vendor[0]['gst_status'] != $response['sts']){

                        $email_template = $this->ci->booking_model->get_booking_email_template(GST_DETAIL_UPDATED);
                        if(!empty($email_template)){ 
                            $subject = vsprintf($email_template[4], array($vendor[0]['company_name']));
                            $message = vsprintf($email_template[0], array($vendor[0]['gst_no'], $vendor[0]['gst_status'], $vendor[0]['gst_taxpayer_type'], $vendor[0]['gst_cancelled_date'], $response['gstin'], $response['sts'], $response['dty'], $response['cxdt']));
                            $to = $email_template[1];
                            $this->ci->notify->sendEmail($email_template[2], $to, $email_template[3], $email_template[5], $subject, $message, '', GST_DETAIL_UPDATED);
                        }
                        $this->ci->vendor_model->edit_vendor($data, $vendor_id);
                    }
                    $data['gst_no'] = $response['gstin'];
                    $data['status'] = 'success'; 
                    return $data;
                }
            }
        }
        else{
            $data['status'] = 'error'; 
            return $data;
        }
    }
    /**
     * @desc This function is used to check gst number is valid or not
     * Currently it triggered from Invoice Model
     * @param String $vendor_id
     * @param String $gst_number
     * @return string
     */
    function check_gst_number_valid($vendor_id, $gst_number) {
        if (!empty($gst_number)) {
            $gstin = $this->get_gstin_status_by_api($vendor_id);
            if (!empty($gstin)) {
                if ($gstin['status'] == "success") {
                    if ($gstin['gst_taxpayer_type'] == "Regular" && $gstin['gst_status'] == "Active") {
                        return $gst_number;
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

}
