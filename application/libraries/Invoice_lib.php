<?php


class Invoice_lib {
    
   public function __construct() {
	$this->ci = & get_instance();
        $this->ci->load->library('PHPReport');
        $this->ci->load->library("miscelleneous");
        $this->ci->load->library('s3');
        $this->ci->load->library('table');
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
    
    function generate_invoice_excel($template, $meta, $data, $output_file_excel, $meta_repeat = false) {
       
        // directory
        $templateDir = FCPATH . "application/controllers/excel-templates/";
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
                'id' => 'meta',
                'repeat' => $meta_repeat,
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
        $logo_path = false;
        $seal_path = false;
        $sign_path = false;
        $imagePath = array();
        
        if(isset($meta['main_company_logo_cell'])){ 
          if($meta['main_company_logo']){ 
            $main_logo_path = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_logo'];
            if($this->remote_file_exists($main_logo_path)){ 
                if(copy($main_logo_path, TMP_FOLDER . $meta['main_company_logo'])){
                    $logo_cell = $meta['main_company_logo_cell'];
                    $logo_path = TMP_FOLDER . $meta['main_company_logo'];
                    $res1 = 0;
                    system(" chmod 777 " . $logo_path, $res1);
                    $logo_detail = array("image_path" => $logo_path, "cell" => $logo_cell);
                    array_push($imagePath, $logo_detail);
                }
            }
          }
        }
       
        if(isset($meta['main_company_seal_cell'])){
          if($meta['main_company_seal']){
            $main_seal_path = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_seal'];
            if($this->remote_file_exists($main_seal_path)){
                if(copy($main_seal_path, TMP_FOLDER . $meta['main_company_seal'])){
                    $seal_cell = $meta['main_company_seal_cell'];
                    $seal_path = TMP_FOLDER . $meta['main_company_seal'];
                    $res1 = 0;
                    system(" chmod 777 " . $seal_path, $res1);
                    $seal_detail = array("image_path" => $seal_path, "cell" => $seal_cell);
                    array_push($imagePath, $seal_detail);
                }
            }
          }
        }
        
        if(isset($meta['main_company_sign_cell'])){
            if($meta['main_company_seal']){
                $main_sign_path = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$meta['main_company_signature'];
                if($this->remote_file_exists($main_sign_path)){
                    if(copy($main_sign_path, TMP_FOLDER . $meta['main_company_signature'])){
                        $sign_cell = $meta['main_company_sign_cell'];
                        $sign_path = TMP_FOLDER . $meta['main_company_signature'];
                        $res1 = 0;
                        system(" chmod 777 " . $sign_path, $res1);
                        $sign_detail = array("image_path" => $sign_path, "cell" => $sign_cell);
                        array_push($imagePath, $sign_detail);
                    }
                }
            }
        }
        
        $R->render('excel', $output_file_excel,$cell, $imagePath);
        
        if(file_exists($logo_path)){
            unlink($logo_path);
        }
        if(file_exists($seal_path)){
           unlink($seal_path);
        }
        if(file_exists($sign_path)){
            unlink($sign_path);
        }
        
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
    
    function taxpro_api_curl_call($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
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
                if($response['error']['error_cd'] != INVALID_LENGHT_GSTIN && $response['error']['error_cd'] != INVALID_GSTIN){  /**** mail not send on invalid gst number or invalid length of gst number *****/
                    $email_template = $this->ci->booking_model->get_booking_email_template(TAXPRO_API_FAIL);
                    if(!empty($email_template)){
                        $message = vsprintf($email_template[0], array("Called by - ".$this->ci->session->userdata('emp_name'), $api_response));  
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
    }
    
    
    
    /**
     * @desc This function is used to call taxpro gst api 
     * @param String $vendor_id
     * @param String $gst_number
     * @return api response 
     */
    function taxpro_gstin_checking_curl_call($gst_no, $vendor_id="", $vendor_partner=""){ 
        if(!$vendor_id){
          $vendor_id = _247AROUND;
        }
        $url = "https://api.taxprogsp.co.in/commonapi/v1.1/search?aspid=".ASP_ID."&password=".ASP_PASSWORD."&Action=TP&Gstin=".$gst_no;
        $activity = array(
            'entity_type' => $vendor_partner,
            'partner_id' => $vendor_id,
            'activity' => __METHOD__,
            'header' => "",
            'json_request_data' => $url,
        );
        $api_response = $this->taxpro_api_curl_call($url);
        $activity['json_response_string'] = $api_response;
        $this->ci->partner_model->log_partner_activity($activity);
        return $api_response;
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
            
            $api_response = $this->taxpro_gstin_checking_curl_call($vendor[0]['gst_no'], $vendor_id, 'vendor');
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
                    
                    if ($gstin['gst_taxpayer_type'] == "Regular" && ($gstin['gst_status'] == "Active" || $gstin['gst_status'] == "Provisional" )) {
                        
                        return array('status' => TRUE, "gst_type" => TRUE);
                        
                    } else if($gstin['gst_status'] == "Cancelled" || $gstin['gst_taxpayer_type'] == "Composition"){
                        
                        return array('status' => TRUE, "gst_type" => FALSE);
                    }else {
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
    /**
     * @desc This is used to get partner notification details data.
     * Who has un-paid invoice and whose date of payment exceed from today
     * @param Array $partner_details
     */
    function get_postpaid_partner_outstanding($partner_details) {
        log_message("info", __METHOD__ . " Partner ID " . print_r($partner_details, true));

        $result = array('active' => 1, "is_notification" => FALSE, "notification_msg" => "", "partner_id" => $partner_details['id']);
        if (!empty($partner_details) && $partner_details['is_prepaid'] == 0) {
            // GET un-paid invoice whose due date less than today
            $invoicingSummary = $this->ci->invoices_model->get_summary_invoice_amount("partner", $partner_details['id'], " AND `due_date` <= CURRENT_DATE() ");
            if (!empty($invoicingSummary)) {
                // If final_amount is greater than zero means partner will have to pay
                if ($invoicingSummary[0]['final_amount'] > 0) {
                    $invoice_select = "invoice_id, invoice_date, from_date, to_date, amount_collected_paid, amount_paid ";
                    $where['where'] = array('vendor_partner' => "partner", "vendor_partner_id" =>
                        $partner_details['id'], "settle_amount" => 0, "amount_collected_paid > 0 " => NULL);
                    $where['length'] = -1;
                    $where['order_by'] = array("invoice_date" => "ASC");
                    // Get all un-mpaid invoices
                    $invoice_data = $this->ci->invoices_model->searchInvoicesdata($invoice_select, $where);
                    if (!empty($invoice_data)) {
                        // Add notification days and invoice date
                        $invoice_date = date('Y-m-d', strtotime($invoice_data[0]->invoice_date . " +" . $partner_details['postpaid_notification_limit'] . " days"));
                        //if invoice date less than today then we will show/send notification to Partner
                        if (date('Y-m-d') >=  $invoice_date) {
                            $result['is_notification'] = true;
                            $result['notification_msg'] = POSTPAID_PARTNER_UNPAID_INVOICE_MESSAGE;
                            $result['invoice_data'] = $invoice_data;
                        }

                        $due_date = date('Y-m-d', strtotime($invoice_data[0]->invoice_date . " +" . $partner_details['postpaid_credit_period'] . " days"));
                        // If due date is less than today then we de-activate CRM
                        if (date('Y-m-d') >= $due_date) {
                            $result['active'] = 0;
                            $grace_period = date('Y-m-d', strtotime($partner_details['postpaid_grace_period']));
                            if (!empty($grace_period)) {
                                // IF grace period is greater than today then activate CRM

                                if ($grace_period >= date('Y-m-d')) {
                                    $result['active'] = 1;
                                }
                            }
                        }
                    }
                } else {
                    log_message("info", __METHOD__ . " Partner ID " . $partner_details['id']);
                }
            } else {
                log_message("info", __METHOD__ . " Partner ID " . $partner_details['id']);
            }
        } else {
            log_message("info", __METHOD__ . " Partner ID " . $partner_details['id']);
        }
        return $result;
    }
    /**
     * @desc This function is used to insert new bank transaction data from FOrm/Excel
     * @param Array $invoice_id_array
     * @return boolean
     */
    function process_add_new_transaction($invoice_id_array) {
        log_message('info', __METHOD__. " Starting ". print_r($invoice_id_array, true));
        $account_statement['partner_vendor'] = $invoice_id_array['partner_vendor'];
        $account_statement['partner_vendor_id'] = $invoice_id_array['partner_vendor_id'];
        $account_statement['bankname'] = $invoice_id_array['bankname'];
        $account_statement['transaction_mode'] = $invoice_id_array['transaction_mode'];
        
        $agent_id = $invoice_id_array['agent_id'];
        
        $transaction_date = $invoice_id_array['tdate'];
        $account_statement['transaction_date'] = date("Y-m-d", strtotime($transaction_date));
        if(isset($invoice_id_array['description'])){
            $account_statement['description'] = $invoice_id_array['description'];
        } else {
            $account_statement['description'] = "";
        }
        
        $account_statement['transaction_id'] = $invoice_id_array['transaction_id'];
        //Get bank txn id while update other wise empty.
        $bank_txn_id = $invoice_id_array['bank_txn_id'];
        
        $paid_amount = 0;
        $tds = 0;
        $payment_history = array();
        $invoices = array();
        foreach ($invoice_id_array['invoice_id'] as $invoice_id => $value) {
            if (!empty($invoice_id)) {
                array_push($invoices, $invoice_id);
                $p_history = array();
                $vp_details = array();
                $where = array('invoice_id' => $invoice_id);
                $data = $this->ci->invoices_model->get_invoices_details($where);
                if(!isset($value['tds_amount'])){
                    if($data[0]['amount_paid'] == 0){
                        $value['tds_amount'] = $data[0]['tds_amount'];
                    } else {
                        $value['tds_amount'] = 0;
                    }
                }
                $credit_debit = $value['credit_debit'];
                $p_history['invoice_id'] = $invoice_id;
                $p_history['credit_debit'] = $credit_debit;
                $p_history['credit_debit_amount'] = sprintf("%.2f", $value['credit_debit_amount']);
                $p_history['agent_id'] = $agent_id;
                $p_history['tds_amount'] = $value['tds_amount'];
                $p_history['create_date'] = date("Y-m-d H:i:s");
                array_push($payment_history, $p_history);

                if ($credit_debit == 'Credit') {

                    $paid_amount += sprintf("%.2f",$value['credit_debit_amount']);
                    $amount_collected = abs(sprintf("%.2f",($data[0]['amount_collected_paid'] - $data[0]['amount_paid'])));
                    
                } else if ($credit_debit == 'Debit') {

                    $paid_amount += (-sprintf("%.2f",$value['credit_debit_amount']));
                    $amount_collected = abs(sprintf("%.2f",($data[0]['amount_collected_paid'] + $data[0]['amount_paid'])));
                }
              
                $tds += $value['tds_amount'];

                if (round($amount_collected,0) == round($value['credit_debit_amount'], 0)) {

                    $vp_details['settle_amount'] = 1;
                    $vp_details['amount_paid'] = $value['credit_debit_amount'] + $data[0]['amount_paid'];
                } else {
                    //partner Pay to 247Around
                    if ($account_statement['partner_vendor'] == "partner" && $credit_debit == 'Credit' && $data[0]['tds_amount'] == 0) {
                        $per_tds = 0;
                        $vp_details['tds_amount'] = $value['tds_amount'];
                        $vp_details['tds_rate'] = $per_tds;
                        $amount_collected = $data[0]['total_amount_collected'] - $vp_details['tds_amount'];
                        $vp_details['around_royalty'] = $vp_details['amount_collected_paid'] = $amount_collected;

                        if (round($amount_collected, 0) == round(($data[0]['amount_paid'] + $value['credit_debit_amount']), 0)) {
                            $vp_details['settle_amount'] = 1;
                        } else {
                            $vp_details['settle_amount'] = 0;
                        }
                        $vp_details['amount_paid'] = $data[0]['amount_paid'] + $value['credit_debit_amount'];
                        
                    } else if($account_statement['partner_vendor'] == "partner" && $credit_debit == 'Debit' && $data[0]['tds_amount'] == 0 && $value['tds_amount'] > 0){
                        
                        $per_tds = 0;
                        $vp_details['tds_amount'] = $value['tds_amount'];
                        $vp_details['tds_rate'] = $per_tds;
                        $amount_collected = $data[0]['total_amount_collected'] - $vp_details['tds_amount'];
                        if (round($amount_collected, 0) == round(($data[0]['amount_paid'] + $value['credit_debit_amount']), 0)) {
                            $vp_details['settle_amount'] = 1;
                        } else {
                            $vp_details['settle_amount'] = 0;
                        }
                        $vp_details['amount_paid'] = $data[0]['amount_paid'] + $value['credit_debit_amount'];
                        $vp_details['amount_collected_paid'] = -$amount_collected;
                    } else {

                        $vp_details['settle_amount'] = 0;
                        $vp_details['amount_paid'] = $data[0]['amount_paid'] + $value['credit_debit_amount'];
                    }
                }

                $this->ci->invoices_model->update_partner_invoices(array('invoice_id' => $invoice_id), $vp_details);
            }
        }
        $account_statement['invoice_id'] = implode(",", $invoices);
        if ($paid_amount > 0) {
            $account_statement['debit_amount'] = '0';
            $account_statement['credit_amount'] = abs($paid_amount);
            $account_statement['credit_debit'] = 'Credit';
        } else {
            $account_statement['debit_amount'] = abs($paid_amount);
            $account_statement['credit_amount'] = '0';
            $account_statement['credit_debit'] = 'Debit';
        }

        $account_statement['agent_id'] =  $agent_id;
        $account_statement['tds_amount'] = $tds;            
                    
        if (empty($bank_txn_id)) {
            $bank_txn_id = $this->ci->invoices_model->bankAccountTransaction($account_statement);
        } else {
            $this->ci->invoices_model->update_bank_transactions(array('id' => $bank_txn_id), $account_statement);
        }
        //Donot remove $value
        foreach ($payment_history as $key => $value) {
            $payment_history[$key]['bank_transaction_id'] = $bank_txn_id;
        }
        $this->ci->accounting_model->insert_batch_payment_history($payment_history);

        //Send SMS to vendors about payment
        if ($account_statement['partner_vendor'] == 'vendor') {

             $this->send_payment_sms_to_vendor($account_statement);
        }
        return true;

    }
    
    function send_payment_sms_to_vendor($account_statement) {

        $vendor_arr = $this->ci->vendor_model->getVendorContact($account_statement['partner_vendor_id']);
        $v = $vendor_arr[0];

        $sms['tag'] = "payment_made_to_vendor";
        $sms['phone_no'] = $v['owner_phone_1'];
        $sms['smsData'] = "previous month";
        $sms['booking_id'] = "";
        $sms['type'] = $account_statement['partner_vendor'];
        $sms['type_id'] = $account_statement['partner_vendor_id'];
        $this->ci->notify->send_sms_msg91($sms);
    }
    
    /**
     * @desc this function is used to generate the challan PDF file
     * @param array $sf_details
     * @param array $partner_details
     * @param String $sf_challan_number
     * @param Array $spare_details
     * @param Array $partner_challan_number
     * @return String $output_pdf_file_name
     */
    function process_create_sf_challan_file($sf_details, $partner_details, $sf_challan_number, $spare_details, $partner_challan_number = "", $service_center_closed_date = "") {
        $excel_data = array();
        $partner_on_saas = $this->ci->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $main_partner = $this->ci->partner_model->get_main_partner_invoice_detail($partner_on_saas);
        $excel_data['excel_data']['main_company_logo'] = $main_partner['main_company_logo'];
        if(!empty($sf_details)){
            $excel_data['excel_data']['sf_name'] = $sf_details[0]['company_name'];
            $excel_data['excel_data']['sf_address'] = $sf_details[0]['address'];
            $excel_data['excel_data']['sf_contact_person_name'] = $sf_details[0]['contact_person_name'];
            $excel_data['excel_data']['sf_contact_number'] = $sf_details[0]['contact_number'];
            $excel_data['excel_data']['sf_gst_number'] = $sf_details[0]['gst_number'];
        }
                            
        if(!empty($partner_details)){
                        
            $excel_data['excel_data']['partner_name'] = $partner_details[0]['company_name'];
            $excel_data['excel_data']['partner_address'] = $partner_details[0]['address'];
            $excel_data['excel_data']['partner_contact_person_name'] = $partner_details[0]['contact_person_name'];
            $excel_data['excel_data']['partner_contact_number'] = $partner_details[0]['contact_number'];
            $excel_data['excel_data']['partner_gst'] = $partner_details[0]['gst_number'];  
        }
        
        $excel_data['excel_data']['partner_challan_no'] = $partner_challan_number;
        $excel_data['excel_data']['sf_challan_no'] = $sf_challan_number;
        $excel_data['excel_data']['date'] = "";
        
        $booking_id = $spare_details[0]['booking_id'];
        $excel_data['excel_data_line_item'] = array();


        foreach ($spare_details as $value2) {
            
            if (!empty($value2)) {
                $tmp_arr = array();
                $tmp_arr['value'] = $value2[0]['challan_approx_value'];
                $tmp_arr['booking_id'] = $value2[0]['booking_id'];
                $tmp_arr['spare_desc'] = $value2[0]['parts_shipped'];
                $tmp_arr['part_number'] =(isset($value2[0]['part_number'])) ? $value2[0]['part_number'] : '-'; 
                $tmp_arr['qty'] = $value2[0]['shipped_quantity'];

                array_push($excel_data['excel_data_line_item'], $tmp_arr);
            }
        }
        
        if ($sf_details[0]['is_gst_doc'] == 1) {
            $template = 'delivery_challan_template';
            $excel_data['excel_data']['sf_gst'] = $sf_details[0]['gst_number'];
            $signature_file = FALSE;
        } else {
            $template = "delivery_challan_without_gst";
            $excel_data['excel_data']['sf_gst'] = '';
            $excel_data['excel_data']['sf_owner_name'] = $sf_details[0]['owner_name'];
            //get signature file from s3 and save it to server
            if (!empty($sf_details[0]['signature_file'])) {
                $s3_bucket = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . trim($sf_details[0]['signature_file']);
                copy($s3_bucket, TMP_FOLDER . $sf_details[0]['signature_file']);
                system(" chmod 777 " . TMP_FOLDER . $sf_details[0]['signature_file']);
                $excel_data['excel_data']['signature_file'] = $signature_file = $sf_details[0]['signature_file'];
            } else {
                $excel_data['excel_data']['signature_file'] = $signature_file = "";
            }
        }

        if (!empty($template)) {
            //generated pdf file name
            $output_file = "delivery_challan_" . $booking_id . "_" . rand(10, 100) . "_" . date('d_M_Y_H_i_s');
            //generated pdf file template
            $html_file = $this->ci->load->view('templates/' . $template, $excel_data, true);
            echo $html_file;
            $output_pdf_file_name = $output_file . ".pdf";
             $json_result = $this->ci->miscelleneous->convert_html_to_pdf($html_file, $booking_id, $output_pdf_file_name, 'vendor-partner-docs');
            log_message('info', __FUNCTION__ . 'HTML TO PDF JSON RESPONSE' . print_r($json_result, TRUE));
            $pdf_response = json_decode($json_result, TRUE);

            if ($pdf_response['response'] === 'Success') {
                log_message('info', ' Mpdf ' . $pdf_response['response_msg']);
                $output_pdf_file_name = $pdf_response['output_pdf_file'];
                if (file_exists(TMP_FOLDER . $output_pdf_file_name)) {
                    $res1 = 0;
                    system(" chmod 777 " . TMP_FOLDER . $output_pdf_file_name, $res1);
                    
                }
            } else {
                log_message('info', $pdf_response['response_msg'] . ' Error in generating pdf');
                $output_pdf_file_name = '';
            }

            if ($signature_file && file_exists(TMP_FOLDER . $sf_details[0]['signature_file'])) {
                unlink(TMP_FOLDER . $sf_details[0]['signature_file']);
            }
        } else {
            log_message('info', 'pdf file can not be generated because no template found');
            $output_pdf_file_name = "";
        }

        return $output_pdf_file_name;
    }

    /**
     * @desc Generate Challan file
     * @param type $booking_id
     * @return boolean
     */
    function generate_challan_file($spare_id, $service_center_id, $service_center_closed_date = "") {

        $spare_parts_details=array();
        $spare_ids = explode(',',$spare_id);
        foreach ($spare_ids as  $spare_id) {
        $select = 'spare_parts_details.*';
        $where = array('spare_parts_details.id' => $spare_id, "status" => DEFECTIVE_PARTS_PENDING, 'defective_part_required' => 1);
        $spare_parts_details[] = $this->ci->partner_model->get_spare_parts_by_any($select, $where); 
        }

        if (!empty($spare_parts_details)) {
            $partner_challan_number = trim(implode(',', array_column($spare_parts_details, 'partner_challan_number')), ',');

           
            $shipped_inventory_id ='';
            foreach ($spare_parts_details as $spare_key =>  $spare_parts_details_value) {
                if (!empty($spare_parts_details_value[0]['shipped_inventory_id'])) {
                   $shipped_inventory_id = $spare_parts_details_value[0]['shipped_inventory_id'];

                  if (!empty($shipped_inventory_id)){
                  $whereinventory = array('inventory_id'=>$shipped_inventory_id);
                  $inventory_master_data = $this->ci->inventory_model->get_inventory_master_list_data('part_number', $whereinventory);
                   $spare_parts_details_value[$spare_key]['part_number']=$inventory_master_data[0]['part_number'];   
                  }else{
                 $spare_parts_details_value[$spare_key]['part_number']='-';    
                 }
                }else{
                    $spare_parts_details_value[$spare_key]['part_number']='-';
                }
  
            }


            $sf_details = $this->ci->vendor_model->getVendorDetails('name as company_name,address,sc_code,is_gst_doc,owner_name,signature_file,gst_no,gst_no as gst_number, is_signature_doc,primary_contact_name as contact_person_name,primary_contact_phone_1 as contact_number', array('id' => $service_center_id));

            $select = "concat('C/o ',contact_person.name,',', warehouse_address_line1,',',warehouse_address_line2,',',warehouse_details.warehouse_city,' Pincode -',warehouse_pincode, ',',warehouse_details.warehouse_state) as address,contact_person.name as contact_person_name,contact_person.official_contact_number as contact_number";


            $where = array('contact_person.entity_id' => $spare_parts_details[0][0]['defective_return_to_entity_id'],
                'contact_person.entity_type' => $spare_parts_details[0][0]['defective_return_to_entity_type']);
            $wh_address_details = $this->ci->inventory_model->get_warehouse_details($select, $where, false, true);

            $partner_details = array();

            if ($spare_parts_details[0][0]['defective_return_to_entity_type'] == _247AROUND_PARTNER_STRING) {
                $partner_details = $this->ci->partner_model->getpartner_details('company_name, address,gst_number,primary_contact_name as contact_person_name ,primary_contact_phone_1 as contact_number', array('partners.id' => $spare_parts_details[0][0]['defective_return_to_entity_id']));
            } else if ($spare_parts_details[0][0]['defective_return_to_entity_type'] === _247AROUND_SF_STRING) {
                $partner_details = $this->ci->vendor_model->getVendorDetails('name as company_name,address,owner_name,gst_no as gst_number', array('id' => $spare_parts_details[0][0]['defective_return_to_entity_id']));
            }

            if (!empty($wh_address_details)) {
                $partner_details[0]['address'] = $wh_address_details[0]['address'];
                $partner_details[0]['contact_person_name'] = $wh_address_details[0]['contact_person_name'];
                $partner_details[0]['contact_number'] = $wh_address_details[0]['contact_number'];
            
            }
            
            $partner_details[0]['is_gst_doc'] = $sf_details[0]['is_gst_doc'];
            $partner_details[0]['owner_name'] = $sf_details[0]['owner_name'];
            
            log_message('info', __FUNCTION__ . 'sf challan debugging spare_id: ' . $spare_id, true);

            $sf_challan_number = $spare_parts_details[0][0]['sf_challan_number'];

            if (empty($sf_challan_number)) {
                $sf_challan_number = $this->ci->miscelleneous->create_sf_challan_id($sf_details[0]['sc_code']);
            }

            
            $sf_challan_file = $this->process_create_sf_challan_file($partner_details, $sf_details, $sf_challan_number, $spare_parts_details, $partner_challan_number, $service_center_closed_date);

            $data['sf_challan_number'] = $sf_challan_number;
            $data['sf_challan_file'] = $sf_challan_file;



            foreach ($spare_parts_details as $value) {
                $this->ci->service_centers_model->update_spare_parts(array('id' => $value[0]['id']), $data);
            }
        }
           $partner_on_saas = $this->ci->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            if ($partner_on_saas) {
                return $sf_challan_file;
            }else{
                return true; 
            }

       
    }





 function force_generate_challan_file($spare_id, $service_center_id, $service_center_closed_date = "") {

        $spare_parts_details=array();
        $spare_ids = explode(',',$spare_id);
        foreach ($spare_ids as  $spare_id) {
        $select = 'spare_parts_details.*';
        $where = array('spare_parts_details.id' => $spare_id,'defective_part_required' => 1);
        $spare_parts_details[] = $this->ci->partner_model->get_spare_parts_by_any($select, $where); 
        }

        if (!empty($spare_parts_details)) {
            $partner_challan_number = trim(implode(',', array_column($spare_parts_details, 'partner_challan_number')), ',');

           
            $shipped_inventory_id ='';
            foreach ($spare_parts_details as $spare_key =>  $spare_parts_details_value) {
                if (!empty($spare_parts_details_value[0]['shipped_inventory_id'])) {
                   $shipped_inventory_id = $spare_parts_details_value[0]['shipped_inventory_id'];

                  if (!empty($shipped_inventory_id)){
                  $whereinventory = array('inventory_id'=>$shipped_inventory_id);
                  $inventory_master_data = $this->ci->inventory_model->get_inventory_master_list_data('part_number', $whereinventory);
                   $spare_parts_details_value[$spare_key]['part_number']=$inventory_master_data[0]['part_number'];   
                  }else{
                 $spare_parts_details_value[$spare_key]['part_number']='-';    
                 }
                }else{
                    $spare_parts_details_value[$spare_key]['part_number']='-';
                }
  
            }


            $sf_details = $this->ci->vendor_model->getVendorDetails('name as company_name,address,sc_code,is_gst_doc,owner_name,signature_file,gst_no,gst_no as gst_number, is_signature_doc,primary_contact_name as contact_person_name,primary_contact_phone_1 as contact_number', array('id' => $service_center_id));

            $select = "concat('C/o ',contact_person.name,',', warehouse_address_line1,',',warehouse_address_line2,',',warehouse_details.warehouse_city,' Pincode -',warehouse_pincode, ',',warehouse_details.warehouse_state) as address,contact_person.name as contact_person_name,contact_person.official_contact_number as contact_number";


            $where = array('contact_person.entity_id' => $spare_parts_details[0][0]['defective_return_to_entity_id'],
                'contact_person.entity_type' => $spare_parts_details[0][0]['defective_return_to_entity_type']);
            $wh_address_details = $this->ci->inventory_model->get_warehouse_details($select, $where, false, true);

            $partner_details = array();

            if ($spare_parts_details[0][0]['defective_return_to_entity_type'] == _247AROUND_PARTNER_STRING) {
                $partner_details = $this->ci->partner_model->getpartner_details('company_name, address,gst_number,primary_contact_name as contact_person_name ,primary_contact_phone_1 as contact_number', array('partners.id' => $spare_parts_details[0][0]['defective_return_to_entity_id']));
            } else if ($spare_parts_details[0][0]['defective_return_to_entity_type'] === _247AROUND_SF_STRING) {
                $partner_details = $this->ci->vendor_model->getVendorDetails('name as company_name,address,owner_name,gst_no as gst_number', array('id' => $spare_parts_details[0][0]['defective_return_to_entity_id']));
            }

            if (!empty($wh_address_details)) {
                $partner_details[0]['address'] = $wh_address_details[0]['address'];
                $partner_details[0]['contact_person_name'] = $wh_address_details[0]['contact_person_name'];
                $partner_details[0]['contact_number'] = $wh_address_details[0]['contact_number'];
            
            }
            
            $partner_details[0]['is_gst_doc'] = $sf_details[0]['is_gst_doc'];
            $partner_details[0]['owner_name'] = $sf_details[0]['owner_name'];
            
            log_message('info', __FUNCTION__ . 'sf challan debugging spare_id: ' . $spare_id, true);

            $sf_challan_number = $spare_parts_details[0][0]['sf_challan_number'];

            if (empty($sf_challan_number)) {
                $sf_challan_number = $this->ci->miscelleneous->create_sf_challan_id($sf_details[0]['sc_code']);
            }

            
            $sf_challan_file = $this->process_create_sf_challan_file($partner_details, $sf_details, $sf_challan_number, $spare_parts_details, $partner_challan_number, $service_center_closed_date);

            $data['sf_challan_number'] = $sf_challan_number;
            $data['sf_challan_file'] = $sf_challan_file;



            foreach ($spare_parts_details as $value) {
                $this->ci->service_centers_model->update_spare_parts(array('id' => $value[0]['id']), $data);
            }
        }
           $partner_on_saas = $this->ci->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
            if ($partner_on_saas) {
                return $sf_challan_file;
            }else{
                return true; 
            }

       
    }


    /**
     * @desc This function is used to get settle inventory data
     * @param Array $postData
     * @param String $invoice_id
     * @return boolean
     */
    function settle_inventory_invoice_annexure($postData, $from_gst_id = "") {
        $processPostData = array();
        $not_updated = array();
        $booking_partner_id = "";
        foreach ($postData as $value) {
            if (!empty($value['inventory_id'])) {
                $booking_partner_id = $value['booking_partner_id'];
                $where = array('inventory_id' => $value['inventory_id'],
                    'vendor_partner_id' => $value['booking_partner_id'], "invoice_details.is_settle" => 0);
                if (!empty($from_gst_id)) {
                    $where['to_gst_number'] = $from_gst_id;
                }
                $order_by = array('column_name' => "(qty -settle_qty)", 'param' => 'asc');

                $unsettle = $this->ci->invoices_model->get_unsettle_inventory_invoice('invoice_details.*', $where, $order_by);

                if (!empty($unsettle)) {
                    $qty = 1;
                    $inventory_details = $this->ci->inventory_model->get_inventory_master_list_data('*', array('inventory_id' => $value['inventory_id']));

                    foreach ($unsettle as $key => $b) {

                        $restQty = $b['qty'] - $b['settle_qty'];
                        if ($restQty == $qty) {



                            $s = $this->get_array_settle_data($b, $inventory_details, $restQty, $value);
                            if (!empty($s)) {
                                $this->ci->invoices_model->update_invoice_breakup(array('id' => $b['id']), array('is_settle' => 1, 'settle_qty' => $b['qty']));
                                $mapping = array('incoming_invoice_id' => $b['invoice_id'], 'settle_qty' => $restQty, 'create_date' => date('Y-m-d H:i:s'), "inventory_id" => $value['inventory_id']);

                                if (!array_key_exists($s['from_state_code'] . "-" . $s['to_state_code'], $processPostData)) {

                                    $processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['data'][0] = $s;
                                    $processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['from_state_code'][0] = $s['from_state_code'];

                                    $processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['mapping'][0] = $mapping;
                                } else {

                                    array_push($processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['data'], $s);
                                    array_push($processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['mapping'], $mapping);
                                }

                                log_message('info', __METHOD__ . " Settle " . print_r($s, true));
                                $qty = 0;
                                break;
                            } else {
                                $this->invoices_not_found($value);
                                array_push($not_updated, $value['booking_id']);
                                log_message('info', __METHOD__ . " Unsettle Invoice is not Found. Spare Invoice is not generating for booking id " . $value['booking_id'] . " Inventory id " . $value['inventory_id']);
                            }
                        } else if ($restQty < $qty) {



                            $s = $this->get_array_settle_data($b, $inventory_details, $restQty, $value);

                            if (!empty($s)) {
                                $this->ci->invoices_model->update_invoice_breakup(array('id' => $b['id']), array('is_settle' => 1, 'settle_qty' => $b['qty']));
                                $mapping = array('incoming_invoice_id' => $b['invoice_id'], 'settle_qty' => $restQty, 'create_date' => date('Y-m-d H:i:s'), "inventory_id" => $value['inventory_id']);

                                if (!array_key_exists($s['from_state_code'] . "-" . $s['to_state_code'], $processPostData)) {

                                    $processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['data'][0] = $s;

                                    $processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['mapping'][0] = $mapping;
                                } else {

                                    array_push($processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['data'], $s);
                                    array_push($processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['mapping'], $mapping);
                                }

                                $qty = $qty - $restQty;
                            } else {
                                $this->invoices_not_found($value);
                                array_push($not_updated, $value['booking_id']);
                                log_message('info', __METHOD__ . " Unsettle Invoice is not Found. Spare Invoice is not generating for booking id " . $value['booking_id'] . " Inventory id " . $value['inventory_id']);
                            }
                        } else if ($restQty > $qty) {



                            $s = $this->get_array_settle_data($b, $inventory_details, $qty, $value);
                            if (!empty($s)) {
                                $this->ci->invoices_model->update_invoice_breakup(array('id' => $b['id']), array('is_settle' => 0, 'settle_qty' => $b['settle_qty'] + $qty));
                                $mapping = array('incoming_invoice_id' => $b['invoice_id'], 'settle_qty' => $qty, 'create_date' => date('Y-m-d H:i:s'), "inventory_id" => $value['inventory_id']);

                                if (!array_key_exists($s['from_state_code'] . "-" . $s['to_state_code'], $processPostData)) {

                                    $processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['data'][0] = $s;

                                    $processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['mapping'][0] = $mapping;
                                } else {

                                    array_push($processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['data'], $s);
                                    array_push($processPostData[$s['from_state_code'] . "-" . $s['to_state_code']]['mapping'], $mapping);
                                }

                                $qty = 0;

                                break;
                            } else {
                                $this->invoices_not_found($value);
                                array_push($not_updated, $value['booking_id']);
                                log_message('info', __METHOD__ . " Unsettle Invoice is not Found. Spare Invoice is not generating for booking id " . $value['booking_id'] . " Inventory id " . $value['inventory_id']);
                            }
                        } else {
                            if ($qty > 0) {
                                $this->invoices_not_found($value);
                                array_push($not_updated, $value['booking_id']);
                                log_message('info', __METHOD__ . " Unsettle Invoice is not Found. Spare Invoice is not generating for booking id " . $value['booking_id'] . " Inventory id " . $value['inventory_id']);
                            }
                        }
                    }
                } else {
                    $this->invoices_not_found($value);
                    array_push($not_updated, $value['booking_id']);
                    log_message('info', __METHOD__ . " Unsettle Invoice is not Found. Spare Invoice is not generating for booking id " . $value['booking_id'] . " Inventory id " . $value['inventory_id']);
                }
            } else {
                $this->invoices_not_found($value);
                array_push($not_updated, $value['booking_id']);
                log_message('info', __METHOD__ . " Inventory ID Missing. Spare Invoice is not generating for booking id " . $value['booking_id'] . " Inventory id " . $value['inventory_id']);
            }
        }

        return array(
            'processData' => $processPostData,
            'not_update_booking_id' => $not_updated,
            'booking_partner_id' => $booking_partner_id);
    }

    function get_array_settle_data($b, $inventory_details, $restQty, $value){
        
        $partner_gst = $this->ci->inventory_model->get_entity_gst_data("entity_gst_details.*", array('entity_gst_details.id' => $b['from_gst_number']));
        $around_gst = $this->ci->inventory_model->get_entity_gst_data("entity_gst_details.*", array('entity_gst_details.id' => $b['to_gst_number']));
        
        if(!empty($around_gst) && !empty($partner_gst)){
            $around_gst_number = $around_gst[0]['gst_number'];
            $around_state_code = $around_gst[0]['state'];
            $around_address = $around_gst[0]['address'];
            $around_pincode = $around_gst[0]['pincode'];
            $around_city = $around_gst[0]['city'];
            
            $partner_state_code = $partner_gst[0]['state'];
            $partner_gst_number = $partner_gst[0]['gst_number'];
            $partner_address = $partner_gst[0]['address'];
            $partner_pincode = $partner_gst[0]['pincode'];
            $partner_city = $partner_gst[0]['city'];
            
            return array(
            'incoming_invoice_id' => $b['invoice_id'], 
            "qty" => $restQty, 
            "part_name" => $inventory_details[0]['part_name'],
            "part_number" => $inventory_details[0]['part_number'],
            "booking_id" => (isset($value['booking_id']))?$value['booking_id']:"",
            "rate" => $b['rate'],
            "spare_id" => (isset($value['spare_id']))?$value['spare_id']:"",
            "booking_partner_id" => $value['booking_partner_id'],
            "inventory_id" => $value['inventory_id'],
            "hsn_code" => $inventory_details[0]['hsn_code'],
            "gst_rate" => $b['cgst_tax_rate'] + $b['sgst_tax_rate'] +$b['igst_tax_rate'],
            "to_gst_number" => $partner_gst_number,
            "to_gst_number_id" => $b['from_gst_number'],
            "to_state_code" => $partner_state_code,
            "to_address" => $partner_address,
            "to_pincode" => $partner_pincode,
            "to_city" => $partner_city,
            "from_gst_number" => $around_gst_number,
            "from_state_code" =>$around_state_code,
            "from_address" => $around_address,
            "from_pincode" => $around_pincode,
            "from_city" => $around_city,
            "from_gst_number_id" => $b['to_gst_number'],
            );
        } else {
            return false;
        }
        
    }
    
    /**
     * @desc If there is no any un-settle invoice available then it will send a mail to developer or accountant
     * @param Array $data
     */
    function invoices_not_found($data) {
        log_message('info', __METHOD__ . " Invoice Qty Not found " . print_r($data, true));

        $template1 = array(
            'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
        );

        $this->ci->table->set_template($template1);

        $this->ci->table->set_heading(array('Part Name', 'Booking ID', "Inventory ID "));
        
        $this->ci->table->add_row($data['part_name'], $data['booking_id'], $data['inventory_id']);
        

        $this->ci->table->set_template($template1);
        $html_table = $this->ci->table->generate();

        $email_template = $this->ci->booking_model->get_booking_email_template("spare_invoice_not_found");
        $subject = $email_template[4];
        $message = vsprintf($email_template[0], array($html_table,json_encode($data, true)));
        $email_from = $email_template[2];

        $to = $email_template[1];
        $cc = $email_template[3];
        $bcc = $email_template[5];

        $this->ci->notify->sendEmail($email_from, $to, $cc, $bcc, $subject, $message, "", 'spare_invoice_not_found');
    }
    
    /**
     * @desc this is used to insert invoice break up in the new invoice table
     * @param Array $response
     * @return boolean
     */
    function insert_def_invoice_breakup($response, $is_settle = 1){
        log_message('info', __METHOD__. " Insert invoice breakup");
        $a = array();
        foreach ($response['booking'] as $value) {
            $invoice = array();
            if(isset($value['invoice_id'])){
                $invoice['invoice_id'] = $value['invoice_id'];
            } else {
                $invoice['invoice_id'] = $response['meta']['invoice_id'];
            }
            
            $invoice['description'] = $value['description'];
            $invoice['product_or_services'] = "Product";
            $invoice['hsn_code'] = $value['hsn_code'];
            $invoice['qty'] = $value['qty'];
            $invoice['rate'] = $value['rate'];
            $invoice['inventory_id'] = (isset($value['inventory_id']) ? $value['inventory_id'] : NULL);
            $invoice['taxable_value'] = $value['taxable_value'];
            
            $invoice['cgst_tax_amount'] = $invoice['sgst_tax_amount'] = isset($value['sgst_tax_amount']) ?$value['sgst_tax_amount']:0;
            $invoice['cgst_tax_rate'] = $invoice['sgst_tax_rate'] = isset($value['cgst_rate']) ?$value['cgst_rate']:0;
            $invoice['igst_tax_amount'] = isset($value['igst_tax_amount']) ?$value['igst_tax_amount']:0;
            $invoice['igst_tax_rate'] = isset($value['igst_rate']) ?$value['igst_rate']:0;
            if($is_settle == 1){
                $invoice['is_settle'] = $is_settle;
                $invoice['settle_qty'] = $value['qty'];
            } else {
                $invoice['is_settle'] = 0;
                $invoice['settle_qty'] = 0;
            }
            if(isset($value['spare_id'])){
                $invoice['spare_id'] = $value['spare_id'];
            }
            if(isset($value['from_gst_number_id'])){
                $invoice['from_gst_number'] = $value['from_gst_number_id'];
            }
            if(isset($value['to_gst_number_id'])){
                $invoice['to_gst_number'] = $value['to_gst_number_id'];
            }
            $invoice['total_amount'] = $value['total_amount'];
            $invoice['create_date'] = date('Y-m-d H:i:s');
            
            array_push($a, $invoice);

        }
        
        return $this->ci->invoices_model->insert_invoice_breakup($a);
        
    }
    
    /**
     * @desc This function is used to generate array( return inventory ledger). This array will be use in the invoice generation.
     * @param Array $processData
     * @param Array $b
     * @param int $restQty
     * @param Array $value
     * @return Array
     */
    function get_array_settle_data_for_new_part_return($processData, $b, $restQty, $value){
        $partner_gst = $this->ci->inventory_model->get_entity_gst_data("*", array('id' => $b['from_gst_number']));
        $around_gst = $this->ci->inventory_model->get_entity_gst_data("*", array('id' => $b['to_gst_number']));
        $around_gst_number = "";
        $around_state_code = "";
        $partner_gst_number = "";
        $partner_state_code = "";
        if(!empty($around_gst)){
            $around_gst_number = $around_gst[0]['gst_number'];
            $around_state_code = $around_gst[0]['state'];
        }

        if(!empty($partner_gst)){
            $partner_state_code = $partner_gst[0]['state'];
            $partner_gst_number = $partner_gst[0]['gst_number'];
        }
        return array(
            'incoming_invoice_id' => $b['invoice_id'], 
            "qty" => $restQty, 
            "part_name" => $value['part_name'],
            "part_number" => $value['part_number'],
            "rate" => $b['rate'],
            "inventory_id" => $value['inventory_id'],
            "hsn_code" => $b['hsn_code'],
            "gst_rate" => ($b['cgst_tax_rate'] + $b['sgst_tax_rate'] +$b['igst_tax_rate']),
            "to_gst_number" => $partner_gst_number,
            "to_state_code" => $partner_state_code,
            "from_gst_number" => $around_gst_number,
            "from_state_code" => $around_state_code,
            "product_or_services" => "Product",
            "booking_id" => "",
            "taxable_value" =>  $b['rate'] * $restQty,
            "partner_id" =>  $value['booking_partner_id']
            );
        
    }
    
    /**
     * @desc this function is used to return array to insert invoice in the vendor partner invoice table
     * @param Array $response
     * @param String $type_code
     * @param String $type
     * @param String $entity_type
     * @param int $entity_id
     * @param Array $convert
     * @param int $agent_id
     * @param String $hsn_code
     * @return Array
     */
    function insert_vendor_partner_main_invoice($response, $type_code, $type, $entity_type, $entity_id,$convert,$agent_id,$hsn_code = ''){
        
        $invoice_details = array(
                'invoice_id' => $response['meta']['invoice_id'],
                'type_code' => $type_code,
                'type' => $type,
                'vendor_partner' => $entity_type,
                'vendor_partner_id' => $entity_id,
                "third_party_entity" => (isset($response['meta']['third_party_entity']))?$response['meta']['third_party_entity']:NULL,
                "third_party_entity_id" => (isset($response['meta']['third_party_entity_id']))?$response['meta']['third_party_entity_id']:NULL,
                'invoice_file_main' => $convert['main_pdf_file_name'],
                'invoice_file_excel' => $response['meta']['invoice_id'] . ".xlsx",
                'invoice_detailed_excel' => $response['meta']['invoice_id'] . '-detailed.xlsx',
                'from_date' => date("Y-m-d", strtotime($response['meta']['sd'])), //??? Check this next time, format should be YYYY-MM-DD
                'to_date' => date("Y-m-d", strtotime($response['meta']['sd'])),
                'num_bookings' => $response['meta']['service_count'],
                'total_service_charge' => ($response['meta']['total_ins_charge']),
                'total_additional_service_charge' => (isset($response['meta']['total_additional_service_charge']))?$response['meta']['total_additional_service_charge']:0,
                'parts_cost' => $response['meta']['total_parts_charge'],
                'total_amount_collected' => $response['meta']['sub_total_amount'],
                'tds_amount' => (isset($response['meta']['tds']))?$response['meta']['tds']:0,
                'tds_rate' => (isset($response['meta']['tds_tax_rate']))?$response['meta']['tds_tax_rate']:0,
                'upcountry_booking' => (isset($response['meta']['upcountry_booking']))?$response['meta']['upcountry_booking']:0,
                'upcountry_distance' => (isset($response['meta']['upcountry_distance']))?$response['meta']['upcountry_distance']:0,
                'courier_charges' =>  (isset($response['meta']['total_courier_charge']))?$response['meta']['total_courier_charge']:0,
                'upcountry_price' => (isset($response['meta']['total_upcountry_price']))?$response['meta']['total_upcountry_price']:0,
                'rating' => 5,
                'invoice_date' => (isset($response['meta']['invoice_date']))? date('Y-m-d', strtotime($response['meta']['invoice_date'])): date('Y-m-d'),
                'around_royalty' => (isset($response['meta']['around_royalty']))?$response['meta']['around_royalty']:0,
                'due_date' => (isset($response['meta']['due_date']))? date('Y-m-d', strtotime($response['meta']['due_date'])): date('Y-m-d', strtotime("+1 month")),
                //Amount needs to be collected from Vendor
                'amount_collected_paid' => ($type_code =="A")?$response['meta']['sub_total_amount']:(0-$response['meta']['sub_total_amount']),
                //add agent_id
                'agent_id' => $agent_id,
                "cgst_tax_rate" => (isset($response['meta']['cgst_tax_rate']))?$response['meta']['cgst_tax_rate']:0,
                "sgst_tax_rate" => (isset($response['meta']['sgst_tax_rate']))?$response['meta']['sgst_tax_rate']:0,
                "igst_tax_rate" => (isset($response['meta']['igst_tax_rate']))?$response['meta']['igst_tax_rate']:0,
                "igst_tax_amount" => (isset($response['meta']['igst_total_tax_amount']))?$response['meta']['igst_total_tax_amount']:0,
                "sgst_tax_amount" => (isset($response['meta']['sgst_total_tax_amount']))?$response['meta']['sgst_total_tax_amount']:0,
                "cgst_tax_amount" => (isset($response['meta']['cgst_total_tax_amount']))?$response['meta']['cgst_total_tax_amount']:0,
                "parts_count" => (isset($response['meta']['parts_count']))?$response['meta']['parts_count']:0,
                "invoice_file_pdf" => $convert['copy_file'], 
                "hsn_code" => $hsn_code,
                'packaging_quantity' => (isset($response['meta']['packaging_quantity']))?$response['meta']['packaging_quantity']:0,
                'packaging_rate' => (isset($response['meta']['packaging_rate']))?$response['meta']['packaging_rate']:0,
                'miscellaneous_charges' => (isset($response['meta']['miscellaneous_charges']))?$response['meta']['miscellaneous_charges']:0,
                'warehouse_storage_charges' => (isset($response['meta']['warehouse_storage_charges']))?$response['meta']['warehouse_storage_charges']:0,
                'penalty_amount'=> (isset($response['meta']['penalty_amount']))?$response['meta']['penalty_amount']:0,
                'penalty_bookings_count' => (isset($response['meta']['penalty_bookings_count']))?$response['meta']['penalty_bookings_count']:0,
                'vertical' => $response['meta']['vertical'],
                'category' => $response['meta']['category'],
                'sub_category' => $response['meta']['sub_category'],
                'accounting' => $response['meta']['accounting']
            );
        
            return $invoice_details;
    }
    
    /**
     * @desc this function is used to return array to insert invoice breakup into invoice_details
     * @param Array $invoice
     * @return Array
     */
    function insert_invoice_breackup($invoice){
        $invoice_breakup = array();
        foreach($invoice['booking'] as $value){
            $invoice_details = array(
                "invoice_id" => $invoice['meta']['invoice_id'],
                "description" => $value['description'],
                "qty" => $value['qty'],
                "product_or_services" => $value['product_or_services'],
                "rate" => $value['rate'],
                "taxable_value" => $value['taxable_value'],
                "cgst_tax_rate" => (isset($value['cgst_rate']) ? $value['cgst_rate'] : 0),
                "sgst_tax_rate" => (isset($value['sgst_rate']) ? $value['sgst_rate'] : 0),
                "igst_tax_rate" => (isset($value['igst_rate']) ? $value['igst_rate'] : 0),
                "cgst_tax_amount" => (isset($value['cgst_tax_amount']) ? $value['cgst_tax_amount'] : 0),
                "sgst_tax_amount" => (isset($value['sgst_tax_amount']) ? $value['sgst_tax_amount'] : 0),
                "igst_tax_amount" => (isset($value['igst_tax_amount']) ? $value['igst_tax_amount'] : 0),
                "hsn_code" => $value['hsn_code'],
                "total_amount" => $value['total_amount'],
                "create_date" => date('Y-m-d H:i:s')
                
            );
            
            array_push($invoice_breakup, $invoice_details);
        }
       return $this->ci->invoices_model->insert_invoice_breakup($invoice_breakup);
    }
    
    function insert_couier_data($sender_entity_id, $sender_entity_type, $receiver_entity_id,
            $receiver_entity_type, $awb_number, $courier_name, $qty, $bill_to_partner, $booking_id_array, 
            $courier_file, $courier_shipment_date, $courier_charge){
        $courier_data = array();
        $courier_data['sender_entity_id'] = $sender_entity_id;
        $courier_data['sender_entity_type'] = $sender_entity_type;
        $courier_data['receiver_entity_id'] = $receiver_entity_id;
        $courier_data['receiver_entity_type'] = $receiver_entity_type;
        $courier_data['AWB_no'] = $awb_number;
        $courier_data['courier_name'] = $courier_name;
        $courier_data['create_date'] = date('Y-m-d H:i:s');
        $courier_data['quantity'] = $qty;
        $courier_data['bill_to_partner'] = $bill_to_partner;
        $courier_data['courier_charge'] = $courier_charge;
        $courier_data['status']= COURIER_DETAILS_STATUS;
        if (!empty($booking_id_array)) {
            $courier_data['booking_id'] = implode(",", $booking_id_array);
        }

        if (!empty($courier_file['message'])) {
            $courier_data['courier_file'] = $courier_file;
        }

        if (!empty($courier_shipment_date)) {
            $courier_data['shipment_date'] = date('Y-m-d', strtotime($courier_shipment_date));
        }

        return $this->ci->inventory_model->insert_courier_details($courier_data);
    }
    
    function create_proforma_invoice_id($start_name){
        $invoice_id_tmp = $this->_get_partial_invoice_id($start_name);
        $where = "( invoice_id LIKE '%".$invoice_id_tmp."%' )";
        $invoice_array = $this->ci->invoices_model->get_proforma_invoices_details($where, "invoice_id");
        
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
    
    /**
     * @desc this function is used to call curl on rezorpay ifsc api server and send mail in case of failure
     * @param $url
     * @return $api_response
     */
    function razorpay_ifsc_code_curl_call($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
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
            $api_response = $err;
        }
        else{
            if($api_response != '"Not Found"' && !$this->IsJsonString($api_response)){
                $email_template = $this->ci->booking_model->get_booking_email_template(IFSC_CODE_VALIDATION_API_FAIL);
                if(!empty($email_template)){
                    $message = vsprintf($email_template[0], array("Called by - ".$this->ci->session->userdata('emp_name'), $api_response));  
                    $to = $email_template[1];
                    $this->ci->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $email_template[3] , $email_template[5], $email_template[4], $message, '', IFSC_CODE_VALIDATION_API_FAIL);
                } 
            }
        }
        return $api_response;
    }
    
     /**
     * @desc this function is used to save rezorpay ifsc code verification api response
     * @param $ifsc_code, $vendor_partner, $vendor_partner_id
     * @return $api_response
     */
    function validate_bank_ifsc_code($ifsc_code, $vendor_partner, $vendor_partner_id){
        $url = IFSC_CODE_VALIDATION_API_URL.$ifsc_code;
        $activity = array(
            'entity_type' => $vendor_partner,
            'partner_id' => $vendor_partner_id,
            'activity' => __METHOD__,
            'header' => "",
            'json_request_data' => $url,
        );
        $api_response = $this->razorpay_ifsc_code_curl_call($url);
        $activity['json_response_string'] = $api_response;
        $this->ci->partner_model->log_partner_activity($activity);
        return $api_response;
    }
    
    /**
     * @desc this function is used to check given string is json or not
     * @param $str
     * @return boolean
     */
    function IsJsonString($str) {
        json_decode($str);
        return (json_last_error()===JSON_ERROR_NONE);
    }
    
    /**
     * @desc this function is used to check file in $url is exist or not
     * @param $url
     * @return boolean
     */
    function remote_file_exists($url){
        $header_response = get_headers($url, 1);
        return(bool)preg_match('~HTTP/1\.\d\s+200\s+OK~', $header_response[0]);
    }  
}
