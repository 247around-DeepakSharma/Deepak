<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
ini_set('display_errors', '1');
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 360000); //3600 seconds = 60 minutes

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class vendor extends CI_Controller {
   var  $vendorPinArray = array();
   var  $notFoundCityStateArray = array();
   var  $filePath = "";
   var $existServices=array();
    function __Construct() {
        parent::__Construct();
        $this->load->model('employee_model');
        $this->load->model('booking_model');
        $this->load->library('PHPReport');
        $this->load->model('service_centers_model');
        $this->load->model('upcountry_model');
        $this->load->model('vendor_model');
        $this->load->model('service_centre_charges_model');
        $this->load->model('dealer_model');
        $this->load->model('engineer_model');
        $this->load->helper(array('form', 'url','array'));
        $this->load->library('form_validation');
        $this->load->model('partner_model');
        $this->load->model('penalty_model');
        $this->load->library('booking_utilities');
        $this->load->library('partner_utilities');
        $this->load->library('notify');
        $this->load->library("pagination");
        $this->load->library("asynchronous_lib");
        $this->load->library("miscelleneous");
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('email');
        $this->load->library('push_notification_lib');
        $this->load->helper('download');
        $this->load->library('user_agent');
        $this->load->library('invoice_lib');
       $this->load->helper(array('form', 'url', 'file', 'array'));
        $this->load->dbutil();
        $this->load->model('push_notification_model');
        $this->load->model('indiapincode_model');
        
        
    }

    /**
     * @desc : This function is used to add/edit vendor details
     *
     * Vendor details like- vendor's name, owner's name, phone no., email, POC(point of contact) details
     *      are added/edited.
     *
     * Few more details like- appliance(s), brand(s) they handle and there non-working days
     *      can also be added/edited.
     *
     * @param : void
     * @return : void
     */
    function index() { 
        $this->checkUserSession();
        $vendor = [];
        //Getting rm id from post data
        $rm = $this->input->post('rm');
        
        //Now unset value of rm from POST data
        unset($_POST['rm']);
        $data = $this->input->post();
        $checkValidation = $this->checkValidation();
                                             
        if ($checkValidation) {
            //Getting RM Official Email details to send Welcome Mails to them as well
            $rm_official_email = $this->employee_model->getemployeefromid($rm)[0]['official_email'];
            $agentID = $this->session->userdata('id');
            $vendor_data = $this->get_vendor_basic_form_data();
            //If vendor exists, details are edited
            if (!empty($this->input->post('id'))) {
                $vendor_data['agent_id'] = $agentID;
                $this->vendor_model->edit_vendor($vendor_data, $this->input->post('id'));
                //Log Message
                log_message('info', __FUNCTION__.' SF has been updated :'.print_r($vendor_data,TRUE));
                //Adding details in Booking State Change
                $this->notify->insert_state_change('', SF_UPDATED, SF_UPDATED, 'Vendor ID : '.$_POST['id'], $this->session->userdata('id'), $this->session->userdata('employee_id'),
                        ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
                //Updating details of SF in employee_relation table
                $check_update_sf_rm_relation = $this->vendor_model->update_rm_to_sf_relation($rm, $_POST['id']);
                if($check_update_sf_rm_relation){
                    //Loggin Success
                    log_message('info', __FUNCTION__.' SF to RM relation is updated successfully RM = '.print_r($rm,TRUE).' SF = '.print_r($_POST['id'],TRUE));
                }else{
                    //Loggin Error 
                    log_message('info', __FUNCTION__.' Error in mapping SF to RM relation RM = '.print_r($rm,TRUE).' SF = '.print_r($_POST['id'],TRUE));
                }
                $log = array(
                    "entity" => "vendor",
                    "entity_id" => $_POST['id'],
                    "agent_id" => $this->session->userdata('id'),
                    "action" =>  SF_UPDATED
                );
                $this->vendor_model->insert_log_action_on_entity($log);
                //Send SF Update email
                $send_email = $this->send_update_or_add_sf_basic_details_email($_POST['id'],$rm_official_email,$vendor_data, $rm);
                redirect(base_url() . 'employee/vendor/viewvendor');
            } else {
                $vendor_data['create_date'] = date('Y-m-d H:i:s');
                $vendor_data['sc_code'] = $this->generate_service_center_code($_POST['name'], $_POST['district']);
                $vendor_data['agent_id'] = $agentID;

                //if vendor do not exists, vendor is added
                $sc_id = $this->vendor_model->add_vendor($vendor_data);
                if(!empty($sc_id)){
                    $data = array(
                        'partner_id' => _247AROUND,
                        'state' => $vendor_data['state'],
                        'micro_warehouse_charges' => '0',
                        'vendor_id' => $sc_id
                    );
                   
                    $wh_on_of_data = array(
                        'partner_id' => _247AROUND,
                        'agent_id' => $this->session->userdata('id'),
                        'vendor_id' => $sc_id,
                        'active' => 1
                    );
                    
                    $create_auto_micro = $this->booking_utilities->check_feature_enable_or_not(CREATE_AUTO_MICRO_WAREHOUSE);
                    if(!empty($create_auto_micro)){
                      $this->miscelleneous->create_micro_warehouse($data,$wh_on_of_data);  
                    }
                    
                }
                //Logging
                log_message('info', __FUNCTION__.' SF has been Added :'.print_r($vendor_data,TRUE));
                $log = array(
                    "entity" => "vendor",
                    "entity_id" => $sc_id,
                    "agent_id" => $this->session->userdata('id'),
                    "action" =>  NEW_SF_ADDED
                );
                $this->vendor_model->insert_log_action_on_entity($log);
                //Adding details in Booking State Change
                $this->notify->insert_state_change('', NEW_SF_ADDED, NEW_SF_ADDED, 'Vendor ID : '.$sc_id, $this->session->userdata('id'), $this->session->userdata('employee_id'),
                        ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);

                //Adding values in admin groups present in employee_relation table
                $check_admin_sf_relation = $this->vendor_model->add_sf_to_admin_relation($sc_id);
                if($check_admin_sf_relation != FALSE){
                    //Logging success 
                    log_message('info', __FUNCTION__.' New SF and Admin Group has been related sucessfully.');
                }else{
                    //Logging Error 
                    log_message('info', __FUNCTION__.' Error in adding New SF and Admin Group Relation.');
                }

                //Updating details of SF in employee_relation table
                $check_update_sf_rm_relation = $this->vendor_model->add_rm_to_sf_relation($rm, $sc_id);
                if($check_update_sf_rm_relation){
                    //Loggin Success
                    log_message('info', __FUNCTION__.' SF to RM relation is updated sucessfully RM = '.print_r($rm,TRUE).' SF = '.print_r($sc_id,TRUE));
                }else{
                    //Loggin Error 
                    log_message('info', __FUNCTION__.' Error in mapping SF to RM relation RM = '.print_r($rm,TRUE).' SF = '.print_r($sc_id,TRUE));
                }
                   $engineer['service_center_id'] =  $sc_id;
                   $engineer['name'] = "Default Engineer";
                   $this->vendor_model->insert_engineer($engineer);
                   //Send SF Update email
                   $send_email = $this->send_update_or_add_sf_basic_details_email($_POST['id'],$rm_official_email,$vendor_data, $rm);
                    // Sending Login details mail to Vendor using Template
                   $this->session->set_flashdata('vendor_added', "Vendor Basic Details has been added Successfully , Please Fill other details");
	redirect(base_url() . 'employee/vendor/editvendor/'.$sc_id);
            }
        } else {
            $this->add_vendor();
        }
    }
    
    function upload_gst_file($data) {
        //Start  Processing GST File Upload
        if (($_FILES['gst_file']['error'] != 4) && !empty($_FILES['gst_file']['tmp_name'])) {
            //Adding file validation
            $checkfilevalidation = $this->file_input_validation('gst_file');
            if ($checkfilevalidation) {
                //Cross-check if Non Availiable is checked along with file upload
                if (isset($data['is_gst_doc'])) {
                    unset($_POST['is_gst_doc']);
                }
                //Making process for file upload
                $tmpFile = $_FILES['gst_file']['tmp_name'];
                $gst_file = str_replace(' ', '', $this->input->post('name')) . '_gstfile_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['gst_file']['name'])[1];
                move_uploaded_file($tmpFile, TMP_FOLDER . $gst_file);

                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $gst_file;
                $this->s3->putObjectFile(TMP_FOLDER . $gst_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                $_POST['gst_file'] = $gst_file;
                unlink(TMP_FOLDER . $gst_file);

                return "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $gst_file;

                //Logging success for file uppload
                //log_message('info',__CLASS__.' PAN FILE is being uploaded sucessfully.');
            } else {
                //Redirect back to Form

                if (!empty($_POST['id'])) {
                    $this->editvendor($data['id']);
                } else {
                    $this->add_vendor();
                }
                return FALSE;
            }
        }
    }
    
    function get_vendor_basic_form_data(){
        $vendor_data['company_name'] = trim($this->input->post('company_name'));
        $vendor_data['name'] = trim($this->input->post('name'));
        $vendor_data['address'] = trim($this->input->post('address'));
        $vendor_data['landmark'] = trim($this->input->post('landmark'));
        $vendor_data['district'] = trim($this->input->post('district'));
        $vendor_data['state'] = trim($this->input->post('state'));
        $vendor_data['pincode'] = trim($this->input->post('pincode'));
        $vendor_data['phone_1'] = trim($this->input->post('phone_1'));
        $vendor_data['phone_2'] = trim($this->input->post('phone_2'));
        $vendor_data['email'] = trim($this->input->post('email'));
        $vendor_data['company_type'] = $this->input->post('company_type');
        if(!empty($this->input->post('day'))){
            $vendor_data['non_working_days'] = implode(",",$this->input->post('day'));
         } 
        $vendor_data['is_sf'] = $this->input->post('is_sf');
        $vendor_data['is_cp'] = $this->input->post('is_cp');
        $vendor_data['is_wh'] = $this->input->post('is_wh');
        $vendor_data['isEngineerApp'] = $this->input->post('is_engineer');
        $vendor_data['is_buyback_gst_invoice'] = $this->input->post('is_buyback_gst_invoice');
        $vendor_data['min_upcountry_distance'] = $this->input->post('min_upcountry_distance');
        $vendor_data['minimum_guarantee_charge'] = $this->input->post('minimum_guarantee_charge');
        return $vendor_data;
    }

    /**
     * @desc : This function is used to get the form data of vendor
     *
     * @param : void
     * @return : array()
     */
    
    function get_vendor_form_data(){
                $vendor_data['company_name'] = trim($this->input->post('company_name'));
                $vendor_data['name'] = trim($this->input->post('name'));
                $vendor_data['address'] = trim($this->input->post('address'));
                $vendor_data['landmark'] = trim($this->input->post('landmark'));
                $vendor_data['district'] = trim($this->input->post('district'));
                $vendor_data['state'] = trim($this->input->post('state'));
                $vendor_data['pincode'] = trim($this->input->post('pincode'));
                $vendor_data['phone_1'] = trim($this->input->post('phone_1'));
                $vendor_data['phone_2'] = trim($this->input->post('phone_2'));
                $vendor_data['email'] = trim($this->input->post('email'));
                $vendor_data['company_type'] = $this->input->post('company_type');
                $vendor_data['primary_contact_name'] = trim($this->input->post('primary_contact_name'));
                $vendor_data['primary_contact_email'] = trim($this->input->post('primary_contact_email'));
                $vendor_data['primary_contact_phone_1'] = trim($this->input->post('primary_contact_phone_1'));
                $vendor_data['primary_contact_phone_2'] = trim($this->input->post('primary_contact_phone_2'));
                $vendor_data['owner_name'] = trim($this->input->post('owner_name'));
                $vendor_data['owner_email'] = trim($this->input->post('owner_email'));
                $vendor_data['owner_phone_1'] = trim($this->input->post('owner_phone_1'));
                $vendor_data['owner_phone_2'] = trim($this->input->post('owner_phone_2'));
                
                $vendor_data['is_pan_doc'] = $this->input->post('is_pan_doc');
//                $vendor_data['is_cst_doc'] = $this->input->post('is_cst_doc');
//                $vendor_data['is_tin_doc'] = $this->input->post('is_tin_doc');
//                $vendor_data['is_st_doc'] = $this->input->post('is_st_doc');
                $vendor_data['is_gst_doc'] = $this->input->post('is_gst_doc');
                if(empty( $vendor_data['is_cp'])){
                     $vendor_data['is_cp'] = 0;
                }
                
                if(empty( $vendor_data['is_sf'])){
                     $vendor_data['is_sf'] = 0;
                }
                
                if(empty( $vendor_data['is_wh'])){
                     $vendor_data['is_wh'] = 0;
                }
                
                if(!empty($vendor_data['is_pan_doc']) && !empty($this->input->post('pan_no')) ){
                   $vendor_data['pan_no'] = $this->input->post('pan_no');
                   $vendor_data['name_on_pan'] = $this->input->post('name_on_pan');
                }else{
                    $vendor_data['pan_no'] = "";
                    $vendor_data['name_on_pan']= "";
                }
//                if(!empty($vendor_data['is_cst_doc']) && !empty($this->input->post('cst_no'))){
//                    $vendor_data['cst_no'] = $this->input->post('cst_no');
//                }else{
//                     $vendor_data['cst_no'] = "";
//                }
//                if(!empty($vendor_data['is_tin_doc']) &&  !empty($this->input->post('tin_no'))){
//                    $vendor_data['tin_no'] = $this->input->post('tin_no');
//                }else{
//                    $vendor_data['tin_no'] = "";
//                }
//                if(!empty($vendor_data['is_st_doc']) && !empty($this->input->post('service_tax_no'))){
//                    $vendor_data['service_tax_no'] = $this->input->post('service_tax_no');
//                }else{
//                    $vendor_data['service_tax_no'] = "";
//                }
                if(!empty($vendor_data['is_gst_doc']) && !empty($this->input->post('gst_no'))){ 
                    $vendor_data['gst_no'] = $this->input->post('gst_no');
                    $vendor_data['gst_taxpayer_type'] = $this->input->post('gst_type');
                    $vendor_data['gst_status'] = $this->input->post('gst_status');
                    $vendor_data['gst_cancelled_date'] = date("Y-m-d", strtotime($this->input->post('gst_cancelled_date')));
                    $vendor_data['min_upcountry_distance'] = $this->input->post('min_upcountry_distance');
                    //print_r($vendor_data); die;
                }else{
                    $vendor_data['gst_no'] = NULL;
                    $vendor_data['gst_taxpayer_type'] = NULL;
                    $vendor_data['gst_status'] = NULL;
                    $vendor_data['gst_cancelled_date'] = NULL;
                }
             
                $vendor_data['bank_name'] = trim($this->input->post('bank_name'));
                $vendor_data['account_type'] = trim($this->input->post('account_type'));
                $vendor_data['bank_account'] = trim($this->input->post('bank_account'));
                $vendor_data['ifsc_code'] = trim($this->input->post('ifsc_code'));
                $vendor_data['beneficiary_name'] = trim($this->input->post('beneficiary_name'));
                $vendor_data['is_verified'] = $this->input->post('is_verified');
                if(!empty($this->input->post('contract_file'))){
                    $vendor_data['contract_file'] = $this->input->post('contract_file');
                } 
                if(!empty($this->input->post('id_proof_2_file'))){
                    $vendor_data['id_proof_2_file'] = $this->input->post('id_proof_2_file');
                }  
                if(!empty($this->input->post('id_proof_1_file'))){
                    $vendor_data['id_proof_1_file'] = $this->input->post('id_proof_1_file');
                } 
                if(!empty($this->input->post('cancelled_cheque_file'))){
                     $vendor_data['cancelled_cheque_file'] = $this->input->post('cancelled_cheque_file');
                } 
                if(!empty($this->input->post('address_proof_file'))){
                    $vendor_data['address_proof_file'] = $this->input->post('address_proof_file');
                } 
//                if(!empty($this->input->post('service_tax_file'))){
//                    $vendor_data['service_tax_file'] = $this->input->post('service_tax_file');
//                } 
//                if(!empty($this->input->post('tin_file'))){
//                    $vendor_data['tin_file'] = $this->input->post('tin_file');
//                }
//                if(!empty($this->input->post('cst_file'))){
//                    $vendor_data['cst_file'] = $this->input->post('cst_file');
//                }
                if(!empty($this->input->post('pan_file'))){
                    $vendor_data['pan_file'] = $this->input->post('pan_file');
                }  
                if(!empty($this->input->post('signature_file'))){
                    $vendor_data['signature_file'] = $this->input->post('signature_file');
                }  
                if(!empty($this->input->post('non_working_days'))){
                    $vendor_data['non_working_days'] = $this->input->post('non_working_days');
                } 
                if(!empty($this->input->post('appliances'))){
                    $vendor_data['appliances'] = $this->input->post('appliances');
                }
                if(!empty($this->input->post('brands'))){
                    $vendor_data['brands'] = $this->input->post('brands');  
                }   
                if(!empty($this->input->post('gst_file'))){
                     $vendor_data['gst_file'] = $this->input->post('gst_file');
                }
                                $vendor_data['is_sf'] = $this->input->post('is_sf');
                $vendor_data['is_cp'] = $this->input->post('is_cp');
                $vendor_data['is_wh'] = $this->input->post('is_wh');
                $vendor_data['cp_credit_limit'] = $this->input->post('cp_credit_limit');
                   
            
            return $vendor_data;
    }
    function send_update_or_add_sf_basic_details_email($sf_id,$rm_email,$updated_vendor_details, $rm_id=''){
        $logged_user_name = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['full_name'];
        
        if(!empty($rm_id)) {
            $managerData = $this->employee_model->getemployeeManagerDetails("employee.*",array('employee_hierarchy_mapping.employee_id' => $rm_id, 'employee.groups' => 'regionalmanager'));
        }
        if($this->input->post('id') !== null && !empty($this->input->post('id'))){
            $html = "<p>Following SF has been Updated :</p><ul>";
        }else{
            $html = "<p>New Sf Added :</p><ul>";
            
            //send mail to brand on new sf addition
            $template = $this->booking_model->get_booking_email_template(SF_ADDITION_MAIL_TO_BRAND);
            
            if (!empty($template)) {
                $to = $template[1];
                $cc = $template[3];

                if(!empty($managerData)) {
                    $cc .= ",".$managerData[0]['official_email'];
                }
                
                $subject = $template[4];
                $emailBody = $template[0];
                $this->notify->sendEmail($template[2], $to, $cc, "", $subject, $emailBody, "", SF_ADDITION_MAIL_TO_BRAND);
            }
            
        }
        $html .= "<li><b>" . 'SF Name' . '</b> =>';
        $html .= " " . $updated_vendor_details['name'] . '</li>';
        $html .= "<li><b>" . 'Company Name' . '</b> =>';
        $html .= " " . $updated_vendor_details['company_name'] . '</li>';
        $html .= "<li><b>" . 'Address' . '</b> =>';
        $html .= " " . $updated_vendor_details['address'] . '</li>';
        $html .= "<li><b>" . 'Pincode' . '</b> =>';
        $html .= " " . $updated_vendor_details['pincode'] . '</li>';
        $html .= "<li><b>" . 'State' . '</b> =>';
        $html .= " " . $updated_vendor_details['state'] . '</li>';
        $html .= "<li><b>" . 'District' . '</b> =>';
        $html .= " " . $updated_vendor_details['district'] . '</li>';
        $html .= "<li><b>" . 'Landmark' . '</b> =>';
        $html .= " " . $updated_vendor_details['landmark'] . '</li>';
        $html .= "<li><b>" . 'Phone 1' . '</b> =>';
        $html .= " " . $updated_vendor_details['phone_1'] . '</li>';
        $html .= "<li><b>" . 'Phone 2' . '</b> =>';
        $html .= " " . $updated_vendor_details['phone_2'] . '</li>';
        $html .= "<li><b>" . 'Email' . '</b> =>';
        $html .= " " . $updated_vendor_details['email'] . '</li>';
        $html .= "<li><b>" . 'IS SF' . '</b> =>';
        $html .= " " . $updated_vendor_details['is_sf'] . '</li>';
        $html .= "<li><b>" . 'IS CP' . '</b> =>';
        $html .= " " . $updated_vendor_details['is_cp'] . '</li>';
        $html .= "<li><b>" . 'IS WH' . '</b> =>';
        $html .= " " . $updated_vendor_details['is_wh'] . '</li>';
        $html .= "<li><b>" . 'IS Buyback Invoice on GST' . '</b> =>';
        $html .= " " . $updated_vendor_details['is_buyback_gst_invoice'] . '</li>';
        $html .= "</ul>";
        $to = ANUJ_EMAIL_ID . ',' . $rm_email;
        
        if(!empty($managerData)) {
            $to .= ",".$managerData[0]['official_email'];
        }
        
        //Cleaning Email Variables
        $this->email->clear(TRUE);
        //Send report via email
        $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
        $this->email->to($to);
        if($this->input->post('id') !== null && !empty($this->input->post('id'))){
           $subject = "Vendor Updated : " . $_POST['name'] . ' - By ' . $logged_user_name;
        }else{
            $subject = "New Vendor Added : " . $_POST['name'] . ' - By ' . $logged_user_name;
        }
        $this->email->subject($subject);
        $this->email->message($html);
        if ($this->email->send()) {
            $this->notify->add_email_send_details(NOREPLY_EMAIL_ID,$to,"","",$subject,$html,"",VENDOR_UPDATED);
            log_message('info', __METHOD__ . ": Mail sent successfully to " . $to);
            $flag = TRUE;
        } else {
            log_message('info', __METHOD__ . ": Mail could not be sent to " . $to);
            $flag = FALSE;
        }
        return $flag;
    }
    function send_update_sf_mail($sf_id,$rm_email) {
        $this->checkUserSession();
        $s3_url = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/";
        
        //Getting Logged Employee Full Name
        $logged_user_name = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['full_name'];
        
        $updated_vendor_details = $this->vendor_model->get_vendor_with_bank_details("service_centres.*,account_holders_bank_details.bank_name,"
                . "account_holders_bank_details.account_type,account_holders_bank_details.bank_account,account_holders_bank_details.ifsc_code,account_holders_bank_details.cancelled_cheque_file,"
                . "account_holders_bank_details.beneficiary_name,account_holders_bank_details.is_verified",array("service_centres.id"=>$sf_id));

        //Sending Mail for Updated details
        if($this->input->post('id') !== null && !empty($this->input->post('id'))){
            $html = "<p>Following SF has been Updated :</p><ul>";
        }else{
            $html = "<p>New Sf Added :</p><ul>";
        }
        
        $html .= "<li><b>" . 'SF Name' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['name'] . '</li>';
        $html .= "<li><b>" . 'Company Name' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['company_name'] . '</li>';
        $html .= "<li><b>" . 'Address' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['address'] . '</li>';
        $html .= "<li><b>" . 'Pincode' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['pincode'] . '</li>';
        $html .= "<li><b>" . 'State' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['state'] . '</li>';
        $html .= "<li><b>" . 'District' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['district'] . '</li>';
        $html .= "<li><b>" . 'Landmark' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['landmark'] . '</li>';
        $html .= "<li><b>" . 'Registration Number' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['registration_number'] . '</li>';
        $html .= "<li><b>" . 'Address Proof File' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['address_proof_file'] . '</li>';
        $html .= "<li><b>" . 'Location' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['location'] . '</li>';
        $html .= "<li><b>" . 'Phone 1' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['phone_1'] . '</li>';
        $html .= "<li><b>" . 'Phone 2' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['phone_2'] . '</li>';
        $html .= "<li><b>" . 'Email' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['email'] . '</li>';
        $html .= "<li><b>" . 'Appliances' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['appliances'] . '</li>';
        $html .= "<li><b>" . 'Brands' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['brands'] . '</li>';
        $html .= "<li><b>" . 'Name On Pan' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['name_on_pan'] . '</li>';
        $html .= "<li><b>" . 'Pan Number' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['pan_no'] . '</li>';
        $html .= "<li><b>" . 'Pan File' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['pan_file'] . '</li>';
        $html .= "<li><b>" . 'is_pan_doc' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['is_pan_doc'] . '</li>';
//        $html .= "<li><b>" . 'CST Number' . '</b> =>';
//        $html .= " " . $updated_vendor_details[0]['cst_no'] . '</li>';
//        $html .= "<li><b>" . 'CST File' . '</b> =>';
//        $html .= " " . $updated_vendor_details[0]['cst_file'] . '</li>';
//        $html .= "<li><b>" . 'is_cst_doc' . '</b> =>';
//        $html .= " " . $updated_vendor_details[0]['is_cst_doc'] . '</li>';
//        $html .= "<li><b>" . 'Tin Number' . '</b> =>';
//        $html .= " " . $updated_vendor_details[0]['tin_no'] . '</li>';
//        $html .= "<li><b>" . 'Tin File' . '</b> =>';
//        $html .= " " . $updated_vendor_details[0]['tin_file'] . '</li>';
//        $html .= "<li><b>" . 'Service Tax Number' . '</b> =>';
//        $html .= " " . $updated_vendor_details[0]['service_tax_no'] . '</li>';
//        $html .= "<li><b>" . 'Service Tax File' . '</b> =>';
//        $html .= " " . $updated_vendor_details[0]['service_tax_file'] . '</li>';
//        $html .= "<li><b>" . 'is_st_doc' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['is_st_doc'] . '</li>';
        $html .= "<li><b>" . 'Account Type' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['account_type'] . '</li>';
        $html .= "<li><b>" . 'Company Type' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['company_type'] . '</li>';
        $html .= "<li><b>" . 'ID Proof 1 File' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['id_proof_1_file'] . '</li>';
        $html .= "<li><b>" . 'ID Proof 2 File' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['id_proof_2_file'] . '</li>';
        $html .= "<li><b>" . 'Contract File' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['contract_file'] . '</li>';
        $html .= "<li><b>" . 'Primary Contact Name' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['primary_contact_name'] . '</li>';
        $html .= "<li><b>" . 'Primary Contact Email' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['primary_contact_email'] . '</li>';
        $html .= "<li><b>" . 'Primary Contact Phone_1' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['primary_contact_phone_1'] . '</li>';
        $html .= "<li><b>" . 'Primary Contact Phone_1' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['primary_contact_phone_2'] . '</li>';
        $html .= "<li><b>" . 'Owner Name' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['owner_name'] . '</li>';
        $html .= "<li><b>" . 'Owner Email' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['owner_email'] . '</li>';
        $html .= "<li><b>" . 'Owner Phone_1' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['owner_phone_1'] . '</li>';
        $html .= "<li><b>" . 'Owner Phone_2' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['owner_phone_2'] . '</li>';
        $html .= "<li><b>" . 'Bank Name' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['bank_name'] . '</li>';
        $html .= "<li><b>" . 'Bank Account' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['bank_account'] . '</li>';
        $html .= "<li><b>" . 'IFSC Code' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['ifsc_code'] . '</li>';
        $html .= "<li><b>" . 'Beneficiary Name' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['beneficiary_name'] . '</li>';
        $html .= "<li><b>" . 'Cancelled Cheque File' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['cancelled_cheque_file'] . '</li>';
        $html .= "<li><b>" . 'is_verified' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['is_verified'] . '</li>';
        $html .= "<li><b>" . 'Non Working Days' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['non_working_days'] . '</li>';
        $html .= "<li><b>" . 'is_sf' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['is_sf'] . '</li>';
        $html .= "<li><b>" . 'is_cp' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['is_cp'] . '</li>';
        $html .= "<li><b>" . 'is_cst_doc' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['is_cst_doc'] . '</li>';
        $html .= "<li><b>" . 'is_gst_doc' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['is_gst_doc'] . '</li>';
        $html .= "<li><b>" . 'GST Number' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['gst_no'] . '</li>';
        $html .= "<li><b>" . 'GST File' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['gst_file'] . '</li>';
        $html .= "<li><b>" . 'Min Upcountry Distance' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['min_upcountry_distance'] . '</li>';
        $html .= "<li><b>" . 'Signature File' . '</b> =>';
        $html .= " " . $updated_vendor_details[0]['signature_file'] . '</li>';
        $html .= "</ul>";
        
        $to = ANUJ_EMAIL_ID . ',' . $rm_email;

        //Cleaning Email Variables
        $this->email->clear(TRUE);

        //Send report via email
        $this->email->from(NOREPLY_EMAIL_ID, '247around Team');
        $this->email->to($to);
        
        if($this->input->post('id') !== null && !empty($this->input->post('id'))){
           $subject = "Vendor Updated : " . $_POST['name'] . ' - By ' . $logged_user_name;
        }else{
            $subject = "New Vendor Added : " . $_POST['name'] . ' - By ' . $logged_user_name;
        }
        $this->email->subject($subject);
        $this->email->message($html);

        if (!empty($updated_vendor_details[0]['address_proof_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['address_proof_file'], 'attachment');
        }
        if (!empty($updated_vendor_details[0]['pan_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['pan_file'], 'attachment');
        }
//        if (!empty($updated_vendor_details[0]['cst_file'])) {
//            $this->email->attach($s3_url . $updated_vendor_details[0]['cst_file'], 'attachment');
//        }
//        if (!empty($updated_vendor_details[0]['tin_file'])) {
//            $this->email->attach($s3_url . $updated_vendor_details[0]['tin_file'], 'attachment');
//        }
        if (!empty($updated_vendor_details[0]['id_proof_1_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['id_proof_1_file'], 'attachment');
        }
        if (!empty($updated_vendor_details[0]['id_proof_2_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['id_proof_2_file'], 'attachment');
        }
        if (!empty($updated_vendor_details[0]['contract_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['contract_file'], 'attachment');
        }
        if (!empty($updated_vendor_details[0]['cancelled_cheque_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['cancelled_cheque_file'], 'attachment');
        }
        if (!empty($updated_vendor_details[0]['gst_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['gst_file'], 'attachment');
        }
        if (!empty($updated_vendor_details[0]['signature_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['signature_file'], 'attachment');
        }
        if (!empty($updated_vendor_details[0]['address_proof_file'])) {
            $this->email->attach($s3_url . $updated_vendor_details[0]['address_proof_file'], 'attachment');
        }

        if ($this->email->send()) {
            $this->notify->add_email_send_details(NOREPLY_EMAIL_ID,$to,"","",$subject,$html,"",VENDOR_UPDATED);
            log_message('info', __METHOD__ . ": Mail sent successfully to " . $to);
            $flag = TRUE;
        } else {
            log_message('info', __METHOD__ . ": Mail could not be sent to " . $to);
            $flag = FALSE;
        }

        return $flag;
    }

    /**
     * @desc: this function is used to generate service center code.
     * @param: String(Service center name)
     * @param: String(District)
     * @return : String (Service center code)
     */
    function generate_service_center_code($sc_name, $district) {
        //generate 6 random  letter string
        $sc_code = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        $final_sc_code = strtoupper($sc_code); // convert string in upper case
        $status = $this->vendor_model->check_sc_code_exist($final_sc_code);  // check service center code is exist or not
        if ($status == true) {   //if sc code exists
            generate_service_center_code($sc_name, $district); // repeat  process of generating service center code
        } else {
            return $final_sc_code; // if sc code does not  exit, return sc code.
        }
    }

    /**
     * @desc: Sends sms to owner and point of contact of service center on new creation vendor
     *
     * SMS is sent only while adding new vendor not while editing an existing one.
     *
     * @param: String(Service center name)
     * @param: String(District)
     * @return : String (Service center code)
     */
    function sendWelcomeSms($phone_number, $vendor_name,$id) {
        $sms['tag'] = "new_vendor_creation";
        $sms['smsData']['vendor_name'] = $vendor_name;
        $sms['phone_no'] = $phone_number;
        $sms['booking_id'] = "";
        $sms['type'] = "user";
        $sms['type_id'] = $id;
        $sms['smsData'] = "";
        $this->notify->send_sms_msg91($sms);
    }

    /**
     * @desc: This function is used to check validation of the entered data
     *
     * @param: void
     * @return : If validation ok returns true else false
     */
    function checkValidation() {
        $this->form_validation->set_rules('company_name', 'Vendor Name', 'trim|required');
        $this->form_validation->set_rules('address', 'Vendor Address', 'trim|required');
        $this->form_validation->set_rules('state', 'State', 'trim|required');
        $this->form_validation->set_rules('district', 'District', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            return FALSE;
        } else {
            return true;
        }
    }

    /**
     * @desc: This function is used to get add/edit vendor form
     *
     * This form shows all our active services, brands and all the states of India
     *
     * @param: void
     * @return : array(result) to view
     */
    function add_vendor() {
        $this->checkUserSession();
        $results['services'] = $this->vendor_model->selectservice();
        $results['brands'] = $this->vendor_model->selectbrand();
        $results['select_state'] = $this->vendor_model->get_allstates();
        $results['employee_rm'] = $this->employee_model->get_rm_details();
        $results['bank_name'] = $this->vendor_model->get_bank_details();
   
        $saas_module = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/addvendor', array('results' => $results, 'days' => $days,'saas_module' => $saas_module));
    }

    /**
     * @desc: This function is to edit vendor's details
     *
     * Existing details will be be displayed in respective fields(allowed to edit)
     *      and rest of the fields will be displayed blank.
     *
     * @param: vendor id
     * @return : array(of details) to view
     */
    function editvendor($id) {
        $this->checkUserSession();
        log_message('info',__FUNCTION__.' id: '.$id);
        $query = $this->vendor_model->viewvendor($id);
        if(!empty($query)){
        $results['services'] = $this->vendor_model->selectservice();
        $results['brands'] = $this->vendor_model->selectbrand();
        $results['select_state'] = $this->vendor_model->get_allstates();
        $results['employee_rm'] = $this->employee_model->get_rm_details();
        $results['bank_name'] = $this->vendor_model->get_bank_details();

        $appliances = $query[0]['appliances'];
        $selected_appliance_list = explode(",", $appliances);
        $brands = $this->vendor_model->get_mapped_brands($id);
        $selected_brands_list = explode(",", $brands);

        $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);
        
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $non_working_days = $query[0]['non_working_days'];
        $selected_non_working_days = explode(",", $non_working_days);
        $this->miscelleneous->load_nav_header();
        $data['saas_module'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->load->view('employee/addvendor', array('query' => $query, 'results' => $results, 'selected_brands_list'
            => $selected_brands_list, 'selected_appliance_list' => $selected_appliance_list,
            'days' => $days, 'selected_non_working_days' => $selected_non_working_days,'rm'=>$rm,'saas_module' => $data['saas_module']));
        } else{
            echo "Vendor Not Exist";
        }
    }

    /**
     * @desc: This function is to view particular vendor's details
     *
     * Will display all the details of a particular vendor
     *
     * @param: vendor id
     * @return : array(of details) to view
     */
    function viewvendor($vendor_id = "") {
        $this->checkUserSession();
        $id = $this->session->userdata('id');   
        $active = "1";
        $data['active_state'] = $active;
        if(!empty($this->input->get())){
            $data = $this->input->get();
            if($data['active_state'] == 'all'){
                $active = "";
            }
        }
        //Getting employee relation if present for logged in user
        $sf_list = $this->vendor_model->get_employee_relation($id);
        $state_list = $this->vendor_model->get_state_data($id);
        if (!empty($sf_list)) {
            $sf_list = $sf_list[0]['service_centres_id'];
        }
        //Getting State for SC charges
        $state = $this->service_centre_charges_model->get_unique_states_from_tax_rates();
        $query = $this->vendor_model->viewvendor($vendor_id, $active, $sf_list);
        $pushNotification = $this->push_notification_model->get_push_notification_subscribers_by_entity(_247AROUND_SF_STRING);
        $c2c = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
        $saas_module = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/viewvendor', array('query' => $query,'state' =>$state ,'state_list'=>$state_list, 'selected' =>$data,'push_notification'=>$pushNotification,
            'c2c' => $c2c,'saas_module' => $saas_module));
    }
    
    function get_filterd_sf_cp_data(){
        $this->checkUserSession();
        if($this->input->post()){
            
            $sf_cp_type = $this->input->post('sf_cp');
            $active_state = $this->input->post('active_state');
            $state=$this->input->post('state');
            $city=$this->input->post('city');
            if($active_state === 'all'){
                $active = '';
            }else{
                $active = '1';
            }
            
            $is_wh = '';
            $is_cp = '';
            if($sf_cp_type === 'sf'){
                $is_cp = '';
            }else if($sf_cp_type === 'cp'){
                $is_cp = '1';
            }else if($sf_cp_type === 'wh'){
                $is_wh = '1';
            }
            
            $id = $this->session->userdata('id');   
            //$active = "1";
            //Getting employee relation if present for logged in user
            $sf_list = $this->vendor_model->get_employee_relation($id);
            if (!empty($sf_list)) {
                $sf_list = $sf_list[0]['service_centres_id'];
            }
            $query = $this->vendor_model->viewvendor('', $active, $sf_list,$is_cp,$is_wh,$state,$city);
            if(!empty($query)){
                $c2c = $this->booking_utilities->check_feature_enable_or_not(CALLING_FEATURE_IS_ENABLE);
                $response = $this->load->view('employee/viewvendor', array('query' => $query,'is_ajax'=>true, 'c2c' => $c2c));
            }else{
                $response = "No Data Found";
            }
            echo $response;
        }else{
            echo "Invalid Request";
        }
        
    }

    /**
     * @desc: This function is to activate a particular vendor
     *
     * For this the vendor must be already registered with us and should be non-active(Active = 0)
     *
     * @param: vendor id
     * @return : void
     */
    function vendor_activate_deactivate($id, $is_active) {
        $this->checkUserSession();
        if (!empty($id)) {
            $vendor['active'] = $is_active;
            $vendor['agent_id'] = $this->session->userdata("id");
            $agent_name = $this->session->userdata('emp_name');
            $this->vendor_model->edit_vendor($vendor, $id);
            
            $this->vendor_model->update_service_centers_login(array('service_center_id' => $id), array('active' => $is_active));

            //Getting Vendor Details
            $sf_details = $this->vendor_model->getVendorContact($id);
            $sf_name = $sf_details[0]['name'];

            //Sending Mail to corresponding RM and admin group 
            $employee_relation = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);
            if (!empty($employee_relation)) {
            $to = $employee_relation[0]['official_email'];
            
                //Getting template from Database
                $template = $this->booking_model->get_booking_email_template("sf_permanent_on_off");
                if (!empty($template)) {
                    if($sf_details[0]['is_micro_wh'] == 1){
                        $to .= ",".$template[1];
                    }
                    $email['rm_name'] = $employee_relation[0]['full_name'];
                    $email['sf_name'] = ucfirst($sf_name);
                    if($is_active == 1){
                        $email['on_off'] = 'ON';
                        $subject = " Permanent ON Vendor " . $sf_name;
                    } else {
                       $email['on_off'] = 'OFF';
                       $subject = " Permanent OFF Vendor " . $sf_name;
                    }
                    $email['action_by'] = $agent_name;
                    
                    $emailBody = vsprintf($template[0], $email);
                    $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "",'sf_permanent_on_off');
                }

                log_message('info', __FUNCTION__ . ' Permanent ON/OFF of Vendor' . $sf_name. " status ". $is_active);
            }


            $log = array(
                "entity" => "vendor",
                "entity_id" => $id,
                "agent_id" => $this->session->userdata('id'),
                "action" => ($is_active ==1)? _247AROUND_VENDOR_ACTIVATED: _247AROUND_VENDOR_DEACTIVATED
            );
            $this->vendor_model->insert_log_action_on_entity($log);
            redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
        }
    }

    /**
     * @desc: This function to delete a particular vendor
     *
     * For this the vendor must be already registered with us
     *
     * @param: vendor id
     * @return : void
     */
    function delete($id) {
        $this->vendor_model->delete($id);
        //Storing State change values in Booking_State_Change Table
        $this->notify->insert_state_change('', _247AROUND_VENDOR_DELETED, _247AROUND_VENDOR_DELETED, 'Vendor ID = '.$id, $this->session->userdata('id'), 
                $this->session->userdata('employee_id'),ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
        redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }


    /**
     *  @desc : This function is to select all pending bookings to assign vendor(if not already assigned)
     *
     * This form displays all the pending bookings for which still no vendor is assigned in a tabular form.
     *
     * Vendors can be assigned for more than one booking simultaneously.
     *
     *  @param : void
     *  @return : booking details and vendor details to view
     */
    function get_assign_booking_form() {
        $results = array();
        $bookings = $this->booking_model->pendingbookings();

        foreach ($bookings as $booking) {
            array_push($results, $this->booking_model->find_sc_by_pincode_and_appliance($booking['service_id'], $booking['booking_pincode']));
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/assignbooking', array('data' => $bookings, 'results' => $results));
    }

    /**
     *  @desc : Function to assign vendors for pending bookings.
     * 
     * It is called via AJAX, SFs are assigned and entries in SF booking action table are created immediately in
     * this function itself.
     * 
     * Post that, an async request is fired which sends SMS to customers and SFs and creates/emails Job cards
     * for SFs.
     *
     *  @param : void
     *  @return : load pending booking view
     */
    function process_assign_booking_form() {
        log_message('info', __METHOD__ . json_encode($this->input->post(), true) );
        $service_center = $this->input->post('service_center');
        $agent_id =  $this->input->post('agent_id');
        $agent_name =  $this->input->post('agent_name');
        $agent_type =  $this->input->post('agent_type');
        $pincode = $this->input->post('pincode');
        $partner_id = $this->input->post('partner_id');
        $service_id = $this->input->post('service_id');
        $city = $this->input->post('city');
        $order_id = $this->input->post('order_id');
        $url = base_url() . "employee/do_background_process/assign_booking";
        $sf_status = $this->input->post("sf_status");
        $count = 0;
       
        foreach ($service_center as $booking_id => $service_center_id) {
            if(!empty($booking_id) || $booking_id != '0'){
           
                if ($service_center_id != "") {
                   
                    $assigned = $this->miscelleneous->assign_vendor_process($service_center_id, $booking_id, $partner_id[$booking_id], $agent_id, $agent_type);
                    if ($assigned) {
                        //Insert log into booking state change
                       $this->notify->insert_state_change($booking_id, ASSIGNED_VENDOR, _247AROUND_PENDING, "Service Center Id: " . $service_center_id, $agent_id, $agent_name, 
                               ACTOR_ASSIGN_BOOKING_TO_VENDOR,NEXT_ACTION_ASSIGN_BOOKING_TO_VENDOR,_247AROUND);
                       //Send Push Notification
                       $receiverArray['vendor'] = array($service_center_id); 
                       $notificationTextArray['url'] = array($booking_id);
                       $notificationTextArray['msg'] = array($booking_id);
                       $this->push_notification_lib->create_and_send_push_notiifcation(BOOKING_ASSIGN_TO_VENDOR,$receiverArray,$notificationTextArray);
                       //End Push Notification
                        $count++;
                               
                        if($sf_status[$booking_id] == "SF_NOT_EXIST"){
                            //$this->send_mail_when_sf_not_exist($booking_id);
                            $this->miscelleneous->sf_not_exist_for_pincode(array('booking_id' => $booking_id, 'booking_pincode' => $pincode[$booking_id], 
                                'service_id' => $service_id[$booking_id],'partner_id'=>$partner_id[$booking_id],'city'=>$city[$booking_id],'order_id'=>$order_id[$booking_id]));
                        }
                    } else {
                        log_message('info', __METHOD__ . "=> Not Assign for Sc "
                                . $service_center_id);
                    }
                }
            }
        }

        //Send mail /SMS to SF and and update upcountry in background
        $async_data['booking_id'] = $service_center;
        $async_data['agent_id'] =  $agent_id;
        $async_data['agent_name'] = $agent_name;
        $this->asynchronous_lib->do_background_process($url, $async_data);

        echo " Request to Assign Bookings: " . count($service_center) . ", Actual Assigned Bookings: " . $count;

        //redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }

    /**
     * @desc This is used to send mail when SF does not exist in the booking pincode
     * as per vendor pincode mapping file.
     * 
     * @param String $booking_id
     */
    function send_mail_when_sf_not_exist($booking_id){
        $to = SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_TO; 
        $cc = SF_NOT_EXISTING_IN_PINCODE_MAPPING_FILE_CC;
        
        $subject = "Pincode Not Found In Vendor Pincode Mapping File";
        $message = "Hi,<br/>Please add Pincode and SF details in the Vendor Pincode Mapping file and upload new file. Booking ID: " . $booking_id;
        
        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $message, "",SF_NOT_FOUND, "", $booking_id);
    }
    
   

    /**
     * @desc: This function is to get the reassign vendor page
     *
     * Its mainly done if already assigned vendor do not covers the pincode taken while entering booking.
     *
     * @param: booking id
     * @return : void
     */
    function get_reassign_vendor_form($booking_id) {
        $this->checkUserSession();
        if(!empty($booking_id)){
            $service_centers = $this->vendor_model->getVendorDetails("*", array('on_off' => 1, 'is_sf' => 1, 'active' => 1));
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/reassignvendor', array('booking_id' => $booking_id, 'service_centers' => $service_centers));
        }
    }

    /**
     * @desc: This function reassigns vendor for a particular booking.
     *
     * This is done if the assigned vendor is not able to finish his job due to any reason
     *
     * @param: void
     * @return : void
     */
    function process_reassign_vendor_form($cron = 1) {
        log_message('info',__FUNCTION__."In asynchronously ". $cron);
        if($cron){
            $this->checkUserSession();
        }
         $this->form_validation->set_rules('booking_id', 'Booking ID', 'required|trim');
         $this->form_validation->set_rules('service', 'Vendor ID', 'required|trim');
         $this->form_validation->set_rules('remarks', 'Remarks', 'required|trim');
        if ($this->form_validation->run()) {
         //   $spare_data = $this->inventory_model->get_spare_parts_details("id, status", array("booking_id"=>$this->input->post('booking_id')));
        //    if(!empty($spare_data)){
                $booking_id = $this->input->post('booking_id');
                $service_center_id = $this->input->post('service');
                $remarks = $this->input->post('remarks');
                $select = "service_center_booking_action.id, service_center_booking_action.booking_id, service_center_booking_action.current_status,service_center_booking_action.internal_status";
                $where = array("service_center_booking_action.booking_id"=>$booking_id);
                $booking_action_details = $this->vendor_model->get_service_center_booking_action_details($select, $where);
                $previous_sf_id = $this->reusable_model->get_search_query('booking_details','booking_details.assigned_vendor_id, booking_details.partner_id',array('booking_id'=>$booking_id),NULL,NULL,NULL,NULL,NULL)->result_array();
    //            if (IS_DEFAULT_ENGINEER == TRUE) {
    //                $b['assigned_engineer_id'] = DEFAULT_ENGINEER;
    //            } else {
    //                $engineer = $this->vendor_model->get_engineers($service_center_id);
    //                if (!empty($engineer)) {
    //                    $b['assigned_engineer_id'] = $engineer[0]['id'];
    //                }
    //            }
                //Assign service centre and engineer
                $assigned_data = array('assigned_vendor_id' => $service_center_id,
                    'assigned_engineer_id' => NULL,
                    'is_upcountry' => 0,
                    'upcountry_pincode' => NULL,
                    'sub_vendor_id' => NULL,
                    'sf_upcountry_rate' => NULL,
                    'partner_upcountry_rate' => NULL,
                    'is_penalty' => 0,
                    'upcountry_partner_approved' => 1,
                    'upcountry_paid_by_customer' => 0,
                    'service_center_closed_date' => NULL,
                    'cancellation_reason' => NULL,
                    'upcountry_distance' => NULL,
                    'internal_status' => _247AROUND_PENDING);

                $partner_status = $this->booking_utilities->get_partner_status_mapping_data(_247AROUND_PENDING, ASSIGNED_VENDOR, $previous_sf_id[0]['partner_id'], $booking_id);
                $actor = $next_action = 'not_define';
                if (!empty($partner_status)) {
                    $assigned_data['partner_current_status'] = $partner_status[0];
                    $assigned_data['partner_internal_status'] = $partner_status[1];
                    $actor = $assigned_data['actor'] = $partner_status[2];
                    $next_action = $assigned_data['next_action'] = $partner_status[3];
                }
                $this->booking_model->update_booking($booking_id, $assigned_data);

                $this->vendor_model->delete_previous_service_center_action($booking_id);
                $unit_details = $this->booking_model->getunit_details($booking_id);

                $this->engineer_model->delete_booking_from_engineer_table($booking_id);

                $vendor_data = $this->vendor_model->getVendorDetails("isEngineerApp", array("id" =>$service_center_id, "isEngineerApp" => 1));

                $curr_status = (!empty($booking_action_details[0]['current_status'])?$booking_action_details[0]['current_status']:'Pending');
                $internal_status = (!empty($booking_action_details[0]['internal_status'])?$booking_action_details[0]['internal_status']:'Pending');

                if(($curr_status === 'InProcess') && (($internal_status === 'Completed') || ($internal_status === 'Cancelled'))) {
                    $internal_status = 'Pending';
                    $curr_status = 'Pending';
                }

                foreach ($unit_details[0]['quantity'] as $value) {

                    $data['current_status'] = $curr_status;
                    $data['internal_status'] = $internal_status;
                    $data['service_center_id'] = $service_center_id;
                    $data['booking_id'] = $booking_id;
                    $data['create_date'] = date('Y-m-d H:i:s');
                    $data['update_date'] = date('Y-m-d H:i:s');
                    $data['unit_details_id'] = $value['unit_id'];
                    $this->vendor_model->insert_service_center_action($data);

                    if(!empty($vendor_data)){
                        $engineer_action['unit_details_id'] = $value['unit_id'];
                        $engineer_action['service_center_id'] = $service_center_id;
                        $engineer_action['booking_id'] = $booking_id;
                        $engineer_action['current_status'] = _247AROUND_PENDING;
                        $engineer_action['internal_status'] = _247AROUND_PENDING;
                        $engineer_action["create_date"] = date("Y-m-d H:i:s");

                        $enID = $this->engineer_model->insert_engineer_action($engineer_action);
                        if(!$enID){
                             $this->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, "", "", 
                                "BUG in Enginner Table ". $booking_id, "SF Assigned but Action table not updated", "",SF_ASSIGNED_ACTION_TABLE_NOT_UPDATED, "", $booking_id);
                        }
                    }

                    /* update inventory stock for reassign sf
                     * First increase stock for the previous sf and after that decrease stock 
                     * for the new assigned sf
                     */
                    $inventory_data = array();
                    $inventory_data['receiver_entity_type'] = _247AROUND_SF_STRING;
                    $inventory_data['booking_id'] = $booking_id;
                    $inventory_data['agent_id'] = $this->session->userdata('id');
                    $inventory_data['agent_type'] = _247AROUND_EMPLOYEE_STRING;
                    if ($value['price_tags'] == _247AROUND_WALL_MOUNT__PRICE_TAG) {
                        $match = array();
                        preg_match('/[0-9]+/', $unit_details[0]['capacity'], $match);
                        if (!empty($match)) {
                            if ($match[0] <= 32) {
                                $inventory_data['part_number'] = LESS_THAN_32_BRACKETS_PART_NUMBER;
                            } else if ($match[0] > 32) {
                                $inventory_data['part_number'] = GREATER_THAN_32_BRACKETS_PART_NUMBER;
                            }

                            //increase stock for previous assigned vendor
                            $inventory_data['receiver_entity_id'] = $previous_sf_id[0]['assigned_vendor_id'];
                            $inventory_data['stock'] = 1 ;
                            $this->miscelleneous->process_inventory_stocks($inventory_data);
                            //decrease stock for new assigned vendor
                            $inventory_data['receiver_entity_id'] = $service_center_id;
                            $inventory_data['stock'] = -1 ;
                            $this->miscelleneous->process_inventory_stocks($inventory_data);
                        }
                    }
                }

                $this->notify->insert_state_change($booking_id, RE_ASSIGNED_VENDOR, ASSIGNED_VENDOR, "Re-Assigned SF ID: " . $service_center_id . " ". $remarks, $this->session->userdata('id'), 
                        $this->session->userdata('employee_id'), $actor,$next_action, _247AROUND);

                $sp['service_center_id'] = $service_center_id;
                $this->service_centers_model->update_spare_parts(array('booking_id' => $booking_id), $sp);

               $default_id =_247AROUND_DEFAULT_AGENT;
               $defaultagent_name =_247AROUND_DEFAULT_AGENT_NAME ; 
               if (!empty($this->session->userdata('id'))  &&  !empty($this->session->userdata('employee_id'))) {
                    $default_id =$this->session->userdata('id');
                    $defaultagent_name =$this->session->userdata('employee_id') ; 
               }

                //Mark Upcountry & Create Job Card
                $url = base_url() . "employee/vendor/mark_upcountry_booking/" . $booking_id . "/" . $default_id
                        . "/" . $defaultagent_name;

                $async_data['data'] = array();
                $this->asynchronous_lib->do_background_process($url, $async_data);

                $this->booking_utilities->lib_send_mail_to_vendor($booking_id, "");

                log_message('info', "Reassigned - Booking id: " . $booking_id . "  By " .
                        $this->session->userdata('employee_id') . " service center id " . $service_center_id);



                redirect(base_url() . DEFAULT_SEARCH_PAGE);
        // }
        // else{
        //     $booking_id = $this->input->post('booking_id');
        //     $output = "You cann't reassign this booking because spare part already requested. If you want to reassign then please cancel part request.";
        //     $userSession = array('error' => $output);
        //     $this->session->set_userdata($userSession);
        //     redirect(base_url() . "employee/vendor/get_reassign_vendor_form/".$booking_id);
        // }
        } else {
            $booking_id = $this->input->post('booking_id');
            $output = "All Fields are required";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
            redirect(base_url() . "employee/vendor/get_reassign_vendor_form/".$booking_id);
        }
    }

    function mark_upcountry_booking($booking_id, $agent_id, $agent_name) {
        log_message("info", __METHOD__. " Booking ID ".$booking_id);
        //$this->checkUserSession();
        if (!empty($booking_id)) {
            log_message('info', __METHOD__ . " Booking_id " . $booking_id . "  By agent id " .
                    $agent_id . $agent_name);
            $this->miscelleneous->assign_upcountry_booking($booking_id, $agent_id, $agent_name);
            $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
        }
    }

    /**
     * @desc: This function to get form to broadcast mail to all vendors
     * @param: void
     * @return : void
     */
    function get_broadcast_mail_to_vendors_form() {
        //$service_centers = $this->booking_model->select_service_center();
         $this->miscelleneous->load_nav_header();
         $data['saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS); 
         $this->load->view('employee/broadcastemailtovendor',$data);
    }

    /**
     * @desc: This function sends broadcast mail to vendors
     *
     * Sends mail to all the owner and POC of the vendors, if we want to send some information to
     *      all the service centers simultaniously.
     *
     * @param: void
     * @return : void
     */
    function process_broadcast_mail_to_vendors_form() {

        $this->form_validation->set_rules('mail_from', 'Email From', 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required');
        $this->form_validation->set_rules('mail_body', 'Message', 'trim|required');
        $this->form_validation->set_rules('mail_to', 'Email To', 'trim|valid_email');
        $this->form_validation->set_rules('mail_cc', 'Email CC', 'trim|valid_email');
        if($this->form_validation->run() === FALSE) {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/broadcastemailtovendor');
        } else {
            $bcc_poc = $this->input->post('bcc_poc');
            $bcc_owner = $this->input->post('bcc_owner');
            $bcc_partner_poc = $this->input->post('bcc_partner_poc');
            $bcc_partner_owner = $this->input->post('bcc_partner_owner');
            $bcc_employee = $this->input->post('bcc_employee');
            $mail_to = $this->input->post('mail_to');
            $from = $this->input->post('mail_from');

            if (empty($from)) {
                $from = _247AROUND_SALES_EMAIL;
            }

            $to = NITS_ANUJ_EMAIL_ID . ', sales@247around.com,' . $mail_to;
            $cc = $this->input->post('mail_cc');
            $subject = $this->input->post('subject');

            //Replace new lines with line breaks for proper html formatting
            $message = nl2br($this->input->post('mail_body'));

            if (!empty($_FILES['fileToUpload']['tmp_name'])) {
                $tmpFile = $_FILES['fileToUpload']['tmp_name'];
                $fileName = $_FILES['fileToUpload']['name'];
                move_uploaded_file($tmpFile, TMP_FOLDER . $fileName);
            } else {
                $fileName = "";
            }

            $bcc = "";
            //gets primary contact's email and owner's email of service centers
            if (!empty($bcc_owner) || !empty($bcc_poc)) {
                $service_centers = $this->vendor_model->select_active_service_center_email();
                $sf_bcc = $this->getBccToSendMail($service_centers, $bcc_poc, $bcc_owner);
                $bcc .= $sf_bcc;
            }

            //gets primary contact's email and owner's email of partners
            if (!empty($bcc_partner_poc) || !empty($bcc_partner_owner)) {
                $partners = $this->partner_model->getpartner_details('primary_contact_email,owner_email');
                $partner_bcc = $this->getBccToSendMail($partners, $bcc_partner_poc, $bcc_partner_owner);
                $bcc .= $partner_bcc;
            }

            if (!empty($bcc_employee)) {
                $employee = $this->employee_model->get_employee();
                $employee_bcc = implode(',', array_column($employee, 'official_email'));
                $bcc .= $employee_bcc;
            }

            $attachment = "";
            if (!empty($fileName)) {
                $attachment = TMP_FOLDER . $fileName;
            }
            log_message('info', "broadcast mail from: " . $from);
            log_message('info', "broadcast mail to: " . $to);
            log_message('info', "broadcast mail cc: " . $cc);
            log_message('info', "broadcast mail bcc: " . $bcc);
            log_message('info', "broadcast mail subject: " . $subject);
            log_message('info', "broadcast mail message: " . $message);

            $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, $attachment,BROADCAST_EMAIL);

            redirect(base_url() . DEFAULT_SEARCH_PAGE);
        }
    }

    /**
     * @desc: Get Bcc email to send Broadcast mail when poc and owner flg is not empty.
     * @param: $service_centers
     *         Point of Contact's and Owner's Email which are active
     * @param: $bcc_poc
     *          to check if POC checkbox is checked, if checked will have some value
     * @param: $bcc_owner
     *          to check if owners checkbox is checked,if checked will have some value
     * @return : if true bcc_string else empty
     */
    function getBccToSendMail($service_centers, $bcc_poc, $bcc_owner) {
        $bcc = array();
        
        foreach ($service_centers as $key => $email) {
            if (!empty($bcc_poc) && !empty($bcc_owner)) {
                $bcc1 = $email['primary_contact_email'] . "," . $email['owner_email'];
                array_push($bcc, $bcc1);
            } else if (!empty($bcc_poc) && empty($bcc_owner)) {
                $bcc1 = $email['primary_contact_email'];
                array_push($bcc, $bcc1);
            } else if (empty($bcc_poc) && !empty($bcc_owner)) {
                $bcc1 = $email['owner_email'];
                array_push($bcc, $bcc1);
            }
        }

        if (!empty($bcc)) {
            $bcc_string = implode(", ", $bcc);
            return $bcc_string;
        } else {
            return "";
        }
    }

    /**
     *  @desc : This function is to get upload pincode through excel form
     *
     *  Stores the latest vendor pincode mapping file
     *
     *  @param : error
     *  @return : displays the view
     */
    function get_pincode_excel_upload_form($error = "") {
        $mapping_file['total_pincode'] = $this->vendor_model->get_total_vendor_pincode_mapping();
        $mapping_file['latest_vendor_pincode'] = $this->vendor_model->get_latest_vendor_pincode_mapping_details();

        if ($error != "") {
            $mapping_file['error'] = $error;
        }
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_pincode_excel', $mapping_file);
    }

    /**
     *  @desc : This function get upload master pincode through excel form
     *
     *  This is to store all the pincodes available(i.e master pincode)
     *
     *  @param : void
     *  @return : displays the view
     */
    function get_master_pincode_excel_upload_form() {
        $this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/upload_master_pincode_excel');
    }

    /**
     *  @desc : This function is to upload pincode through excel (asynchronously)
     *  @param : void
     *  @return : void
     */
    function process_pincode_excel_upload_form() {
        $this->checkUserSession();
        if(!empty($_FILES['file']['tmp_name'])){    
            $inputFileName = $_FILES['file']['tmp_name'];
            log_message('info', __FUNCTION__ . ' => Input ZIP file: ' . $inputFileName);
            
            $newZipFileName = TMP_FOLDER."vendor_pincode_mapping_temp_".date('j-M-Y').".zip";
            $CSVFileName = "vendor_pincode_mapping.csv";
            $newCSVFileName = "vendor_pincode_mapping_temp_".date('j-M-Y').".csv";
            move_uploaded_file($inputFileName, $newZipFileName);

            $res = 0; 
            system("unzip " . $newZipFileName . " " . $CSVFileName . " -d ".TMP_FOLDER, $res);
            $re1 = 0; 
            system("mv " . TMP_FOLDER . $CSVFileName ." ".TMP_FOLDER . $newCSVFileName, $re1);

            log_message('info', __FUNCTION__ . ' => New CSV file: ' . $newCSVFileName);

            //Checking for Empty file
            if(filesize(TMP_FOLDER.$newCSVFileName) == 0){
                //Logging
                log_message('info',' Empty Pincode File has been Uploaded');
                $this->session->set_flashdata('file_error',' Empty File has been uploaded');
                redirect(base_url() . 'employee/vendor/get_pincode_excel_upload_form');

            }
            $csv = TMP_FOLDER.$newCSVFileName;
            
            //check file is valid or not
            $file = fopen($csv,"r");
            $is_valid_file = TRUE;
            while(! feof($file))
            {
                $csv_content = fgetcsv($file);
                foreach($csv_content as $data){
                    if(empty($data)){
                        $is_valid_file = FALSE;
                        break;
                    }
                }
                
            }
            fclose($file);
            
            //process if file is valid else disply error message
            if($is_valid_file){
                //Logging
                log_message('info', __FUNCTION__ . ' Processing of Pincode CSV File started');
                //Processing SQL Queries
                $sql_commands = array();
                array_push($sql_commands, "TRUNCATE TABLE vendor_pincode_mapping_temp;");
                $this->vendor_model->execute_query($sql_commands);
                unset($sql_commands);

                $dbHost=$this->db->hostname;
                $dbUser=$this->db->username;
                $dbPass=$this->db->password;
                $dbName=$this->db->database;
                
                $sql = "LOAD DATA LOCAL INFILE '$csv' INTO TABLE vendor_pincode_mapping_temp "
                       . "FIELDS TERMINATED BY ',' ENCLOSED BY '' LINES TERMINATED BY '\r\n' "
                        . "(Vendor_Name,Vendor_ID,Appliance,Appliance_ID,Brand,Area,Pincode,Region,City,State);";

                $res1 = 0;
                system("mysql -u $dbUser -h $dbHost --password=$dbPass --local_infile=1 -e \"$sql\" $dbName", $res1);

                $sql_commands1 = array();

                array_push($sql_commands1, "TRUNCATE TABLE vendor_pincode_mapping;");
                array_push($sql_commands1, "INSERT vendor_pincode_mapping SELECT * FROM vendor_pincode_mapping_temp;");

                $this->vendor_model->execute_query($sql_commands1);

                $this->save_file_into_database($newZipFileName, $csv,FILE_UPLOAD_SUCCESS_STATUS);
                
                log_message('info', __FUNCTION__ . ' => All queries executed: ');
                
                $this->session->set_flashdata('success_msg','Pincode File Uploaded successfully');
                redirect(base_url() . 'employee/vendor/get_pincode_excel_upload_form');
            }else{
                
                $this->save_file_into_database($newZipFileName, $csv,FILE_UPLOAD_FAILED_STATUS);
                
                $this->session->set_flashdata('file_error','Pincode File Is Not Valid Please Check And Upload Again');
                redirect(base_url() . 'employee/vendor/get_pincode_excel_upload_form');
            }
            
            
        }else{
            $this->session->set_flashdata('file_error',' Empty File has been uploaded');
            redirect(base_url() . 'employee/vendor/get_pincode_excel_upload_form');
        }
 
    }

    /**
     * @desc: Load Vendor Escalation form and get escalation reason and vendor details from table.
     *
     * This will send notification to vendor if he/she didn't call the customer or
     *      engeineer didn't reached on time to customer's place.
     *
     * @param : Booking Id
     * @return : Takes to view
     */
    function get_vendor_escalation_form($booking_id) {
        $this->checkUserSession();
        //get escalation reasons for 247around
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'247around','active'=> '1','process_type'=>'escalation'));
        $data['vendor_details'] = $this->vendor_model->getVendor($booking_id);
        $data['booking_id'] = $booking_id;
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/vendor_escalation_form', $data);
    }

    /**
     * @desc: Insert Vendor Escalation reason in database
     *
     * And also requests a method to send sms and email to vendor
     *
     * @param : void
     * @return : Takes to view
     */
    function process_vendor_escalation_form() { 
        $this->checkUserSession();
        log_message('info',__FUNCTION__);
        $booking_id= $this->input->post('booking_id');
        $vendor_id = $this->input->post('vendor_id');
        $escalation_reason_id = $this->input->post('escalation_reason_id');
        $remarks = $this->input->post('remarks');
        $status = $this->input->post('status');
        
        $checkValidation = $this->checkValidationOnReason();
       
        if($checkValidation){
            $agent_id = $this->session->userdata('id');
            $employeeID = $this->session->userdata('employee_id');
            $escalation = $this->miscelleneous->process_escalation($booking_id,$vendor_id,$escalation_reason_id,$remarks,$checkValidation,$agent_id,$employeeID);
            if($escalation && $status){
                redirect(base_url() . 'employee/booking/view_bookings_by_status/' . $status);
                
            } else if($escalation && !$status){
                redirect(base_url() . DEFAULT_SEARCH_PAGE);  
                
            } else if(!$escalation && $status){
                $this->session->set_userdata(array("error"=> "Escalation Added But Penalty Not Added"));
                redirect(base_url() . 'employee/booking/view_bookings_by_status/' . $status);
            } else {
                $this->session->set_userdata(array("error"=> "Escalation Added But Penalty Not Added"));
                redirect(base_url(). "employee/vendor/get_vendor_escalation_form");
            }
        }
        else{
            if($status){
                
            } else {
               $this->get_vendor_escalation_form($booking_id); 
            }
        }
    }


    /**
     * @desc: This function is to check validation on escalation reason
     *
     * @param : void
     * @return : true if validation is true else false
     */
    function checkValidationOnReason() {
        $this->form_validation->set_rules('escalation_reason_id', 'Escalation Reason', 'callback_check_escalation_already_applied');
        $this->form_validation->set_rules('vendor_id', 'Vendor ID', 'required');
        
        return $this->form_validation->run();
    }
    
    function check_escalation_already_applied(){
        if($this->input->post("escalation_reason_id")){
            $escalation_reason_id = $this->input->post("escalation_reason_id");
            $booking_id= $this->input->post('booking_id');
            if(!empty($escalation_reason_id)){
                $where = array("booking_id" => $booking_id, "escalation_reason" => $escalation_reason_id,
                "create_date >=  curdate() " => NULL,  "create_date  between (now() - interval ".AROUND_PENALTY_NOT_APPLIED_WITH_IN." minute) and now()" => NULL);
                $data =$this->vendor_model->getvendor_escalation_log($where, "*");
                
                if(empty($data)){
                    return true;
                } else {
                    $this->form_validation->set_message('check_escalation_already_applied', 'Booking is already escalated');
                    return false;
                }
            } else {
              $this->form_validation->set_message('check_escalation_already_applied', 'The Escalation Reason field is required');
              return false;  
            }
        } else {
            $this->form_validation->set_message('check_escalation_already_applied', 'The Escalation Reason field is required');
            return false;
        }    
    }

    /**
     * @desc: Get District of custom State and echo in 'select option value' to load in a form
     *
     * Function also called through Ajax
     *
     * @param : flag (its value determines weather a disrtict is covered by our service centers or not)
     * @return : displays districts to the view
     */
    function getDistrict($flag = "") {
        $state = $this->input->post('state');
        $dis = $this->input->post('district');

        if ($flag == "") {
            $data = $this->vendor_model->getDistrict($state);
        } else {
            $data = $this->vendor_model->getDistrict_from_india_pincode($state);
        }

        if ($dis == "") {
            echo "<option selected='selected' value=''>Select City</option>";
        } else {
            echo "<option value=''>Select City</option>";
        }
        foreach ($data as $district) {
            if (strtolower(trim($dis)) == strtolower(trim($district['district']))) {
                echo "<option selected value='$district[district]'>$district[district]</option>";
            } else {
                echo "<option value='$district[district]'>$district[district]</option>";
            }
        }
    }

    /**
     * @desc: Get Pincode of Custom District and print 'select option value with data' to load in a form
     *
     * Function also called through Ajax.
     *
     * If flag is empty it will give all the pincodes where we have active vendors
     *      else it will give all the pincodes of India.
     *
     * @param : flag (its value determines the pincode)
     * @return : displays pincode to the view
     */
    function getPincode($flag = "") {
        $district = $this->input->post('district');
        $pin = $this->input->post('pincode');
        if ($flag == "") {
            $data = $this->vendor_model->getPincode($district);
        } else {
            $data = $this->vendor_model->getPincode_from_india_pincode($district);
        }
        if (empty($pin)) {
            echo "<option selected='selected' disabled='disabled'>Select Pincode</option>";
        }
        foreach ($data as $pincode) {
            if ($pin == $pincode['pincode']) {
                echo "<option selected value='$pincode[pincode]'>$pincode[pincode]</option>";
            } else {
                echo "<option value='$pincode[pincode]'>$pincode[pincode]</option>";
            }
        }
    }

    /**
     * @desc: Function checks the availability of vendors active in that pincode
     *
     * @param : void
     * @return : Array of pincode to the view
     */
    function vendor_availability_form() {
        $this->checkUserSession();
        $data = $this->vendor_model->get_services_category_city_pincode();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/searchvendor', $data);
    }

    /**
     * @desc: Function checks the availability of vendors in pincodes where we
     *        are active and vendors are active as well.
     *
     *  Function called through AJAX.
     * @param : void
     * @return : Array of active vendor names
     * @return : Array of active pincodes
     * @return : Array of active areas
     */
    function check_availability_for_vendor() {
        $data['service_id'] = $this->input->post('service_id');
        //$data['city'] = $this->input->post('city');
        $data['pincode'] = $this->input->post('pincode');
        $vendor['vendor'] = $this->vendor_model->getVendorFromVendorMapping($data);
        $this->load->view('employee/searchvendor', $vendor);
    }

    /**
     * @desc: Function sends email with the excel sheet with latest pincode list.
     *
     * @param : void
     * @return : displays the view with message as mail sent.
     */
    function send_email_with_latest_pincode_excel() {
        $to = $this->input->post('email');
        $notes = $this->input->post('notes');
        $attachment = $this->input->post('fileUrl');
        $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, '', '', 'Pincode Changes', $notes, $attachment,PINCODE_CHANGES);
        echo '<div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span ;aria-hidden="true">&times;</span>
                    </button>
                    <strong> Mail Sent </strong>
                </div>';
    }

    /**
     * @desc: Function loads a view to check vendors performance
     *
     * @param : void
     * @return : loads the view
     */
    function vendor_performance_view() {
        $this->checkUserSession();
        $data = $this->vendor_model->get_vendor_city_appliance();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/vendorperformance', $data);
    }

    /**
     * @desc: Function displays the vendors performance.
     *
     * This shows the total bookings assigned to a particular vendor, and what is the booking
     *      completion and cancelation reason for the particular vendor.
     *
     * We can select vendor's city, source of booking, and the time period for which we
     *      want to view vvendors performance.
     *
     * @param : void
     * @return : loads the results to view(Array of data)
     */
    function vendor_performance() {
        $vendor['vendor_id'] = $this->input->post('vendor_id');
        $vendor['city'] = $this->input->post('city');
        $vendor['service_id'] = $this->input->post('service_id');
        $vendor['period'] = $this->input->post('period');
        $vendor['source'] = $this->input->post('source');
        $vendor['sort'] = $this->input->post('sort');
        $data['data'] = $this->vendor_model->get_vendor_performance($vendor);
        $result = $this->load->view('employee/vendorperformance', $data);
        print_r($result);
    }

    /**
     * @desc: This function helps to review the bookings
     *
     * Shows mainly the charges collected by vendors on completing the booking and
     *      closing remarks mentioned by vendors.
     *
     * @param : void
     * @return : loads the view with booking charges(Array of charges)
     */
    function review_bookings() {
        $this->checkUserSession();
        $charges['charges'] = $this->vendor_model->getbooking_charges();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/review_booking_complete_cancel', $charges);
    }

    /**
     * @desc: get cancellation reation for specific vendor id
     * @param: void
     * @return: void
     */
    function getcancellation_reason($vendor_id) {
        $this->checkUserSession();
        $reason['reason'] = $this->vendor_model->getcancellation_reason($vendor_id);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/vendor_cancellation_reason', $reason);
    }

    /**
     * @desc: get form to send mail to specific vendor
     * @param: void
     * @return: vendor's list to view
     */
    function get_mail_vendor($vendor_id = "") {
        $vendor_info = $this->vendor_model->viewvendor($vendor_id);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/mail_vendor', array('vendor_info' => $vendor_info));
    }

    /**
     * @desc: sends mail to specific vendor
     *
     * Mail will be sent to the owner and POC1
     *
     * @param: void
     * @return: void
     */
    function process_mail_vendor() {
        $id = $this->input->post('vendor_id');
        $vendor_info = $this->vendor_model->viewvendor($id);
        $to = $vendor_info[0]['owner_email'] . ', ';
        $to .= $vendor_info[0]['primary_contact_email'];
        $cc = NITS_ANUJ_EMAIL_ID;
        $subject = $this->input->post('subject');
        $raw_message = $this->input->post('mail_body');
        //to replace new lines in line breaks for html
        $message = nl2br($raw_message);
        $bcc = ""; 
        $attachment = "";
        $this->notify->sendEmail("sales@247around.com", $to, $cc, $bcc, $subject, $message, $attachment,EMAIL_TO_SPECIFIC_VENDOR);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/viewvendor', array('query' => $vendor_info));
    }

    /**
     * @desc: This method loads add engineer view. It gets active vendor and appliance to display in a form
     * This  function is used by vendor panel and admin panel to load add engineer view
     */
    function add_engineer(){
        $data['service_center'] = $this->vendor_model->getactive_vendor();
        $data['services'] = $this->booking_model->selectservice();
        if($this->session->userdata('userType') == 'service_center'){

            $this->load->view('service_centers/header');
            $this->load->view('service_centers/add_engineer', $data);

        } else {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/add_engineer', $data);
        }
    }
    
    /**
     * @desc: This is used to Edit Engineer
     * params: Engineer ID
     * return : View of Engineer along with Engineer Data Array
     */
    function get_edit_engineer_form($id){
        $data['service_center'] = $this->vendor_model->getactive_vendor();
        $data['services'] = $this->booking_model->selectservice();
        if(!empty($id)){
            $select = "engineer_details.*, entity_identity_proof.identity_proof_type as identity_proof, entity_identity_proof.identity_proof_number, entity_identity_proof.identity_proof_pic";
            $where = array("engineer_details.id"=>$id);
            $data['data'] = $this->vendor_model->get_engg_full_detail($select, $where); 
        }
        $data['data'][0]['appliance_id'] = $this->engineer_model->get_engineer_appliance(array("engineer_id"=>$id, "is_active"=>1), "service_id");
        if($this->session->userdata('userType') == 'service_center'){

            $this->load->view('service_centers/header');
            $this->load->view('service_centers/add_engineer', $data);

        } else {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/add_engineer', $data);
        }
    }

    /**
     * @desc: This method adds engineers for a service center.
     *  This  function is used by vendor panel and admin panel to load add engineer details.
     */
    function process_add_engineer() {
        $data = array();
        $data_identity = array();
        //$engineer_form_validation = $this->engineer_form_validation();
        $engineer_form_validation = true;
        if ($engineer_form_validation) {
            $is_phone = $this->engineer_model->get_engineers_details(array("phone" => $this->input->post('phone')), "name, phone");
            if (empty($is_phone)) {
                $is_entity = $this->dealer_model->entity_login(array("user_id"=>$this->input->post('phone'), "entity" => _247AROUND_ENGINEER_STRING));
                if($is_entity == false){
                    $data['name'] = $this->input->post('name');
                    $data['phone'] = $this->input->post('phone');
                    $data['alternate_phone'] = $this->input->post('alternate_phone');
                    if($this->input->post('identity_proof')){
                        $data_identity['identity_proof_type'] = $this->input->post('identity_proof');
                    }
                    $data_identity['identity_proof_number'] = $this->input->post('identity_id_number');

                    if (($_FILES['file']['error'] != 4) && !empty($_FILES['file']['tmp_name'])) { 
                        //Making process for file upload
                        $tmpFile = $_FILES['file']['tmp_name'];
                        $pan_file = implode("", explode(" ", $this->input->post('name'))) . '_engidentityfile_' . date("Y-m-d-H-i-s") . "." . explode(".", $_FILES['file']['name'])[1];
                        move_uploaded_file($tmpFile, TMP_FOLDER.$pan_file);

                        //Upload files to AWS   
                         $bucket = BITBUCKET_DIRECTORY;
                        $directory_xls = "engineer-id-proofs/" . $pan_file;
                        $this->s3->putObjectFile(TMP_FOLDER.$pan_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                        $data_identity['identity_proof_pic'] = $pan_file;
                    }

                    if ($this->session->userdata('userType') == 'service_center') {
                        $data['service_center_id'] = $this->session->userdata('service_center_id');
                    } else {
                        $data['service_center_id'] = $this->input->post('service_center_id');
                    }

                    //applicable services for an engineer come as array in service_id field.
                    $service_id = $this->input->post('service_id');

                    $data['active'] = "1";
                    $data['create_date'] = date("Y-m-d H:i:s");

                    $engineer_id = $this->vendor_model->insert_engineer($data);

                    if ($engineer_id) {
                        //insert engineer appliance detail in engineer_appliance_mapping table
                        $eng_services_data = array();
                        foreach ($service_id as $id) {
                            $eng_services['engineer_id'] = $engineer_id;
                            $eng_services['service_id'] = $id;
                            $eng_services['is_active'] = 1;
                            array_push($eng_services_data, $eng_services);
                        }

                        $this->engineer_model->insert_engineer_appliance_mapping($eng_services_data);
                        //insert engineer identity proof data
                        $data_identity['entity_type'] = _247AROUND_ENGINEER_STRING;
                        $data_identity['entity_id'] = $engineer_id;
                        $this->vendor_model->add_entity_identity_proof($data_identity);

                        log_message('info', __METHOD__ . "=> Engineer Details Added. " . $engineer_id);
                        $login["entity"] = "engineer";
                        $login["entity_name"] = $data['name'];
                        $login["entity_id"] = $engineer_id;
                        $login["user_id"] = $data['phone'];
                        $login["password"] = md5($data['phone']);
                        $login["create_date"] = date("Y-m-d H:i:s");
                        $login["active"] = 1;
                        $login["clear_password"] = $data['phone'];

                        $eng_login = $this->dealer_model->insert_entity_login($login);
                        if($eng_login){
                            $sms = array();
                            $sms['phone_no'] = $data['phone'];
                            $sms['smsData']['eng_name'] = $data['name'];
                            $sms['smsData']['eng_user_id'] = $data['phone'];
                            $sms['smsData']['eng_password'] = $data['phone'];
                            $sms['tag'] = ENGINEER_LOGIN_SMS_TEMPLATE;
                            $sms['status'] = "";
                            $sms['booking_id'] = "";
                            $sms['type'] = "engineer";
                            $sms['type_id'] = $engineer_id;
                            $this->notify->send_sms_msg91($sms);
                        }

                        $output = "Engineer Details Added.";
                        $userSession = array('success' => $output);
                    } else {
                        log_message('info', __METHOD__ . "=> Engineer Details Not Added. Engineer data  " . print_r($data, true));

                        $output = "Engineer Details Not Added.";
                        $userSession = array('error' => $output);
                    }

                    $this->session->set_userdata($userSession);

                    if ($this->session->userdata('userType') == 'service_center') {
                        log_message('info', __FUNCTION__ . " Engineer addition initiated By Service Center");

                        redirect(base_url() . "service_center/add_engineer");
                    } else {
                        log_message('info', __FUNCTION__ . " Engineer addition initiated By 247around");

                        redirect(base_url() . "employee/vendor/add_engineer");
                    }
                } else {
                    $output = "User Id Already Exist for this phone number";
                    $userSession = array('error' => $output);
                    $this->session->set_userdata($userSession);
                    redirect(base_url() . "employee/vendor/add_engineer");
                }
            } else {
                $output = "Engineer Phone Number Already Exist.";
                $userSession = array('error' => $output);
                $this->session->set_userdata($userSession);
                redirect(base_url() . "employee/vendor/add_engineer");
            }
        } else { //form validation failed
            redirect(base_url() . "employee/vendor/add_engineer");
        }
    }

    /**
     * @desc: This method is used to process edit engineer form
     * params: Post data array
     * 
     */
    function process_edit_engineer() { 
        //$engineer_form_validation = $this->engineer_form_validation();
        $engineer_id = $this->input->post('id');
        if ($engineer_id) {
            $is_phone = $this->engineer_model->get_engineers_details(array("phone" => $this->input->post('phone')), "id, name, phone");
            if (empty($is_phone) || $is_phone[0]['id'] == $engineer_id) {
                $login_entity = $this->dealer_model->entity_login(array("entity" => _247AROUND_ENGINEER_STRING, "user_id" => $this->input->post('phone')));
                if(empty($login_entity) || $login_entity[0]['user_id'] == $this->input->post('phone')){
                    $data['name'] = $this->input->post('name');
                    $data['phone'] = $this->input->post('phone');
                    $data['alternate_phone'] = $this->input->post('alternate_phone');  
                    if($this->input->post('identity_proof')){
                        $data_identity['identity_proof_type'] = $this->input->post('identity_proof');
                    }
                    else{
                        $data_identity['identity_proof_type'] = "";
                    }
                    $data_identity['identity_proof_number'] = $this->input->post('identity_id_number');

                    if (($_FILES['file']['error'] != 4) && !empty($_FILES['file']['tmp_name'])) { 
                        //Making process for file upload
                        $tmpFile = $_FILES['file']['tmp_name'];
                        $pan_file = implode("", explode(" ", $this->input->post('name'))) . '_engidentityfile_' . date("Y-m-d-H-i-s") . "." . explode(".", $_FILES['file']['name'])[1];
                        move_uploaded_file($tmpFile, TMP_FOLDER.$pan_file);

                        //Upload files to AWS   
                         $bucket = BITBUCKET_DIRECTORY;
                        $directory_xls = "engineer-id-proofs/" . $pan_file;
                        $this->s3->putObjectFile(TMP_FOLDER.$pan_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                        $data_identity['identity_proof_pic'] = $pan_file;
                    }

                    //Get vendor ID from session if form sent thru vendor CRM
                    //Else from POST variable.
                    if ($this->session->userdata('userType') == 'service_center') {
                        $data['service_center_id'] = $this->session->userdata('service_center_id');
                    } else {
                        $data['service_center_id'] = $this->input->post('service_center_id');
                    }

                    //applicable services for an engineer come as array in service_id field.
                    $service_id = $this->input->post('service_id');

                    $data['update_date'] = date("Y-m-d H:i:s");

                    $where = array('id' => $engineer_id);
                    $engineer_update_id = $this->vendor_model->update_engineer($where, $data);
                    
                    //Update user id and password in entity login table
                    if($login_entity[0]['user_id'] != $this->input->post('phone')){
                        $entity_data = array(
                            "entity_name" => $this->input->post('name'),
                            "user_id" => $this->input->post('phone'),
                            "password" => md5($this->input->post('phone')),
                            "clear_password" => $this->input->post('phone')
                        );
                        $this->partner_model->update_login_details($entity_data, array("entity" => _247AROUND_ENGINEER_STRING, "entity_id" => $engineer_id));
                    }
                    $where_identity = array("entity_type" => "engineer", "entity_id" => $engineer_id);
                    $this->vendor_model->update_entity_identity_proof($where_identity, $data_identity);

                    if($engineer_id){

                        $this->engineer_model->update_engineer_appliance_mapping($engineer_id, $service_id);
                    }

                    log_message('info', __METHOD__ . "=> Engineer Details Added.");

                    $output = "Engineer Details Updated.";
                    $userSession = array('update_success' => $output);

                    $this->session->set_userdata($userSession);

                    if ($this->session->userdata('userType') == 'service_center') {
                        log_message('info', __FUNCTION__ . " Engineer updation initiated By Service Center ID " . $engineer_id);

                        redirect(base_url() . "employee/vendor/get_engineers");
                    } else {
                        log_message('info', __FUNCTION__ . " Engineer updation initiated By 247around ID " . $engineer_id);

                        redirect(base_url() . "employee/vendor/get_engineers");
                    }
                } else{
                    $output = "User-Id with this phone number is already exist.";
                    $userSession = array('update_error' => $output);
                    $this->session->set_userdata($userSession);

                    $this->get_edit_engineer_form($engineer_id);
                }
            } else {
                $output = "This Phone has aloted to another Engineer.";
                $userSession = array('update_error' => $output);
                $this->session->set_userdata($userSession);

                $this->get_edit_engineer_form($engineer_id);
            }
        } else { //form validation failed
            $output = "Engineer Updation Error.";
            $userSession = array('update_error' => $output);
            $this->session->set_userdata($userSession);

            $this->get_edit_engineer_form($engineer_id);
        }
    }

    /**
     * @desc: This is used to view engineers details. This function is used by vendor panel and admin panel,
     * If it used by vendor panel then it gets only particular vendor engineer's otherwise get all engineer
     */

    function get_engineers(){
        
       if($this->session->userdata('userType') == 'service_center'){

            $this->load->view('service_centers/header');
            $this->load->view('service_centers/view_engineers');

       } else {
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/view_engineers');
       }

    }

    /**
     * @desc: this Method deactivate/ activate engineer
     */
     function change_engineer_activation($engineer_id, $active){
        log_message('info', __FUNCTION__ . " Activate/Deactivate Engineer Id:  " . $engineer_id .
	    "status: " . $active);

	$where  = array('id' => $engineer_id );
        $this->vendor_model->update_engineer($where, array('active'=> $active));
        $log = array(
            "entity" => "engineer",
            "entity_id" => $engineer_id,
        );
        if($this->session->userdata('userType') == 'service_center'){ 
             $log['agent_id'] = $this->session->userdata('service_center_agent_id');
        }
        else{
            $log['agent_id'] = $this->session->userdata('id');
        }
        if($active == 1){
            $log['action'] = "Engineer Enabled";
        }
        else{
            $log['action'] = "Engineer Disabled";
        }
        
        $this->vendor_model->insert_log_action_on_entity($log);
        
        if($this->session->userdata('userType') == 'service_center'){

           redirect(base_url()."service_center/get_engineers");

       } else {

            redirect(base_url()."service_center/get_engineers");
       }

     }
     /**
      * @desc: Delete Engineer from database
      */
    function delete_engineer($engineer_id){

        log_message('info', __FUNCTION__ . " Delete Engineer Id:  " . print_r($engineer_id, true));
        $where  = array('id' => $engineer_id );
        $this->vendor_model->update_engineer($where, array('delete'=> '1'));

        if($this->session->userdata('userType') == 'service_center'){

           redirect(base_url()."service_center/get_engineers");

       } else {

            redirect(base_url()."service_center/get_engineers");
       }

    }
    /**
     * @desc: This is used to validate engineer details form And also used to upload images
     */
    function engineer_form_validation(){

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('phone', 'Mobile Number', 'trim|numeric|required');
        $this->form_validation->set_rules('alternate_phone', 'Alternate Mobile Number', 'trim|numeric');
        $this->form_validation->set_rules('identity_id_number', 'ID Number', 'trim');
        $this->form_validation->set_rules('identity_proof', 'Identity Proof', 'trim');
        $this->form_validation->set_rules('bank_account_no', 'Bank Account No', 'numeric');
//	$this->form_validation->set_rules('service_id', 'Appliance ', 'trim');
    //    $this->form_validation->set_rules('file', 'Identity Proof Pic ', 'callback_upload_identity_proof_pic');
//        $this->form_validation->set_rules('bank_name', 'Bank Name', 'trim');
//        $this->form_validation->set_rules('bank_ifsc_code', 'IFSC Code', 'trim');
//        $this->form_validation->set_rules('bank_holder_name', 'Account Holder Name', 'trim');
        
//	$this->form_validation->set_rules('bank_proof_pic', 'Bank Proof Pic', 'callback_upload_bank_proof_pic');

	if ($this->form_validation->run() == FALSE) {
            return FALSE;
        }
        else {
            return true;
        }
    }

    /**
     * @desc: This is used to upload Bank Proof Image and return true/false depending on result
     */
    public function upload_bank_proof_pic() {
	$allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
	$temp = explode(".", $_FILES["bank_proof_pic"]["name"]);
	$extension = end($temp);
	//$filename = prev($temp);

	if ($_FILES["bank_proof_pic"]["name"] != null) {
	    if (($_FILES["bank_proof_pic"]["size"] < 2e+6) && in_array($extension, $allowedExts)) {
		if ($_FILES["bank_proof_pic"]["error"] > 0) {
		    $this->form_validation->set_message('upload_bank_proof_pic', $_FILES["bank_proof_pic"]["error"]);
		} else {
		    $pic = preg_replace('/\s+/', '', $this->input->post('name')) . "_" . preg_replace('/\s+/', ' ', $this->input->post('bank_name')) . "_" . uniqid(rand());
		    $picName = $pic . "." . $extension;
		    $_POST['bank_proof_pic'] = $picName;
                    // Uploading to S3
		    $bucket = BITBUCKET_DIRECTORY;
		    $directory = "engineer-bank-proofs/" . $picName;
		    $this->s3->putObjectFile($_FILES["bank_proof_pic"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

		    return TRUE;
		}
	    } else {
		$this->form_validation->set_message('upload_bank_proof_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
		    . 'Maximum file size is 2 MB.');
		return FALSE;
	    }
	}
    }

    /**
     * @desc: This is used to upload ID Proof Image and return true/false depending on result
     */
    public function upload_identity_proof_pic() {
	$allowedExts = array("png", "jpg", "jpeg", "JPG", "JPEG", "PNG", "PDF", "pdf");
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	//$filename = prev($temp);

	if ($_FILES["file"]["name"] != null) {
	    if (($_FILES["file"]["size"] < 2e+6) && in_array($extension, $allowedExts)) {
		if ($_FILES["file"]["error"] > 0) {
		    $this->form_validation->set_message('upload_identity_proof_pic', $_FILES["file"]["error"]);
		} else {
		    $pic = preg_replace('/\s+/', '', $this->input->post('name')) . "_" . preg_replace('/\s+/', '', $this->input->post('identity_proof')) . "_" . uniqid(rand());
		    $picName = $pic . "." . $extension;
		    $_POST['identity_file'] = $picName;
                    //Uploading to S3
		    $bucket = BITBUCKET_DIRECTORY;
		    $directory = "engineer-id-proofs/" . $picName;
		    $this->s3->putObjectFile($_FILES["file"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

		    return TRUE;
		}
	    } else {
		$this->form_validation->set_message('upload_identity_proof_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
		    . 'Maximum file size is 2 MB.');
		return FALSE;
	    }
        } else {
            $identity_uploaded = $this->input->post("identity_uploaded");
            if(empty($identity_uploaded)){
                return FALSE;
            }
        }
    }

     /**
     *  @desc : This function is used to Add Vendor for a particular Pincode Form
     *  @param bookingID - this can be send using get or post both
     *  @return : returns a view of form 
     */

    function get_add_vendor_to_pincode_form($booking_id=NULL){
                $data = array();
                if($booking_id == NULL){
                     if(!empty($this->input->post())){
                              $booking_id =$this->input->post('booking_id');
                     }
                }
                else{
                    $this->miscelleneous->load_nav_header();
                }
                if($booking_id != NULL){
                            $booking_data  = $this->booking_model->getbooking_history($booking_id);
                            $data['pincode'] = $booking_data[0]['booking_pincode'];
                            $pincodeAvaliablity = $this->is_pincode_available_in_india_pincode_table($data['pincode']);
                            if($pincodeAvaliablity){
                                    $data['selected_appliance'][0] = array('service_id'=>$booking_data[0]['service_id'],'service_name'=>$booking_data[0]['services']);
                                    $data['all_appliance'] = $this->booking_model->selectservice();
                                    $data['vendors'] = $this->booking_model->get_advance_search_result_data('service_centres','id as Vendor_ID,name as Vendor_Name');
                                    $this->load->view('employee/add_vendor_to_pincode',$data);
                            }
                }

    }

    /*
     * This function is used to create vendor mapping pincode tables possible rows
     * @input - $receivedData(Data we recieved directly from form submission),$areaArray(This array contains area,state,city,region for that particular pincode)
     * @output - $Data(This array will contain all possible combination by merging the input data)
     */
    

    function get_structured_array_for_vendor_pincode_processing($receivedData,$areaArray){
        foreach($receivedData['appliance'] as $appliance_data){
                    $temp = explode("__",$appliance_data);
                    $appliance['Appliance_ID'] = $temp[0];
                    $tempVendor =  explode("__",$receivedData['vendor_id']);
                    $appliance['Vendor_ID'] = $tempVendor[0];
                    $appliance['Vendor_Name'] = $tempVendor[1];
                    $appliance['Pincode'] = $receivedData['pincode'];
                              foreach($areaArray as $areaData){
                                  $appliance['state'] = $areaData['state'] ;
                                  $appliance['city'] = $areaData['city'] ;
                                  $data[] = $appliance;
                              }
           }
            return $data;
    }
    function get_pincode_form_display_msg($displayMsgArray){
        $finalMsg = '';
        if(isset($displayMsgArray['already_exist'])){
            $finalMsg .= count($displayMsgArray['already_exist']).' Combination Already Exists In Mapping Table For '.$displayMsgArray['pincode'].'</br>';
        }
         if(isset($displayMsgArray['success'])){
            $finalMsg .= count($displayMsgArray['success']).' Entries Successfully Created For '.$displayMsgArray['pincode'].'</br>';
        }
        if(isset($displayMsgArray['failed'])){
            $finalMsg .= count($displayMsgArray['failed']).' entries failed to insert in table For '.$displayMsgArray['pincode'].'</br>';
        }
        return $finalMsg;
    }

    /**
     *  @desc : This function is used to Process Add Vendor to pincode Form
     *  @param : Array of $_POST data
     *  @return : void
     */
    function process_add_vendor_to_pincode_form() {
        log_message('info', __FUNCTION__);
        if ($this->input->post()) {
            $this->form_validation->set_rules('pincode', 'Pincode', 'trim|required|numeric|min_length[6]|max_length[6]');
            $this->form_validation->set_rules('vendor_id', 'Vendor_ID', 'required');
            if ($this->form_validation->run() == FALSE) {
                $this->miscelleneous->load_nav_header();
                $this->load->view('employee/add_vendor_to_pincode');
            } else {
                $displayMsgArray['pincode'] = $this->input->post('pincode');
                $areaArray = $this->vendor_model->get_india_pincode_distinct_area_data($this->input->post('pincode'));
                $data = $this->get_structured_array_for_vendor_pincode_processing($this->input->post(), $areaArray);
                $this->miscelleneous->update_pincode_not_found_sf_table($data);
                foreach ($data as $value) {
                    $result = $this->vendor_model->check_vendor_details($value);
                    if ($result == 'true') {
                        $value['create_date'] = date('Y-m-d h:i:s');
                        $vendor_id = $this->vendor_model->insert_vendor_pincode_mapping($value);
                        if (!empty($vendor_id)) {
                            log_message('info', __FUNCTION__ . 'Vendor assigned to Pincode in vendor_picode_mapping table. ' . print_r($value, TRUE));
                            $displayMsgArray['success'][] = $value;
                        } else {
                            $displayMsgArray['failed'][] = $value;
                            log_message('info', __FUNCTION__ . ' Error in adding vendor to pincode in vendor_pincode_mapping table ' . print_r($value, TRUE));
                        }
                    } else {
                        log_message('info', __FUNCTION__ . 'Vendor already assigned to ' . $value['Appliance_ID']);
                        $displayMsgArray['already_exist'][] = $value;
                    }
                }
                $finalMsg = $this->get_pincode_form_display_msg($displayMsgArray);
                $this->session->set_userdata('pincode_msg', $finalMsg);
                redirect(base_url() . 'employee/booking/view_queries/FollowUp/p_nav');
            }
        }
    }

    /**
     *  @desc : This function is used get vendor services based on vendor id
     * Call: This function is called using AJAX from Vendor Pincode adding form.
     *  @param : Vendor ID
     *  @return : JSON
     */

     function get_vendor_services($vendor_id){
        //Getting  distinct vendor service details from Vendor Mapping table
        $vendor_services = $this->vendor_model->get_distinct_vendor_service_details($vendor_id);

        foreach ($vendor_services as $key => $value) {
            $data['Appliance'][] = $value['Appliance'];
            $data['Appliance_ID'][] = $value['Appliance_ID'];
        }
        //Returning data in Json Encoded form
        print_r(json_encode($data));
    }
    
     /*
      * This function use to print json data of brands associated to a vendor
      * @input - vendorID
      * @output - json of all brands which are related to input vendor ID 
      */
     function get_vendor_brands($vendor_id){
          $brands = $this->vendor_model->get_vendor_brand($vendor_id);
          $data['brands'] = explode(",",$brands[0]['brands']);
          print_r(json_encode($data));
     }

    /**
     *  @desc : This function is used to Delete assigned vendor to vendor_pincode_mapping
     *          and  process form data
     *  @param : void
     *  @return : array
    */
      function process_vendor_pincode_delete_form() {

	$data = array();
	//Getting data from database
        $select = "service_centres.name, service_centres.id";
	$data['vendor_details'] = $this->vendor_model->getVendorDetails($select);
	$data['appliance'] = $this->booking_model->selectservice();
	$data['state'] = $this->vendor_model->get_allstates();

	//Process Form
	if ($this->input->post()) {
	    if (!empty($this->input->post('service_id')[0])) {
		$service_id = $this->input->post('service_id');

		foreach ($service_id as $key => $value) {
		    if (!empty($value)) {

			$data_post = array(
			    'Appliance_ID' => $value,
			    'Pincode' => $this->input->post('pincode')[$key],
			    'Vendor_ID' => $this->input->post('vendor_id')[$key]
			);

			//Deleting data
			if ($this->vendor_model->delete_vendor($data_post) == '1') {
			    //Echoing ID to log file
			    log_message('info', __FUNCTION__ . ' Vendor has been deleted in Vendor_Pincode_Mapping table. ' . print_r($data_post, TRUE));

			    $data['delete'] = TRUE;
			} else {
			    log_message('info', __FUNCTION__ . ' Following pincode NOT found in Vendor_Pincode_Mapping table =  ' . $this->input->post('pincode')[$key]);

			    $data['not_found'][] = $this->input->post('pincode')[$key];
			}
		    }
		}
	    } else {

		$data['no_input'] = '';
	    }
	}
	$this->miscelleneous->load_nav_header();
	$this->load->view('employee/list_vendor_pincode', $data);
    }
    
    /**
     * @desc: This method is used to send mail with Vendor Pincode Mapping file.
     * This is called by Ajax. It gets email and notes by form. Pass it to asynchronous method.
     * @param: void
     * @return: print success
     */
    function download_unique_pincode_excel(){

//        log_message('info', __FUNCTION__);
//
//        $template = 'Vendor_Pincode_Mapping_Template.xlsx';
//        //set absolute path to directory with template files
//        $templateDir = __DIR__ . "/../excel-templates/";
//        //set config for report
//        $config = array(
//            'template' => $template,
//            'templateDir' => $templateDir
//        );
//        //load template
//        $R = new PHPReport($config);
//        $vendor = $this->vendor_model->get_all_pincode_mapping();
//
//        $R->load(array(
//
//                 'id' => 'vendor',
//                'repeat' => TRUE,
//                'data' => $vendor
//            ));
//
//        $output_file_dir = TMP_FOLDER;
//        $output_file = "Vendor_Pincode_Mapping" . date('y-m-d');
//        $output_file_name = $output_file . ".xlsx";
//        $output_file_excel = $output_file_dir . $output_file_name;
//        $R->render('excel', $output_file_excel);
//        
//        //Downloading File
//        if(file_exists($output_file_excel)){
//
//            header('Content-Description: File Transfer');
//            header('Content-Type: application/octet-stream');
//            header("Content-Disposition: attachment; filename=\"$output_file_name\""); 
//            readfile($output_file_excel);
//            exit;
//        }
            log_message('info', __FUNCTION__);
            $newCSVFileName = "Vendor_Pincode_Mapping_Template_" . date('j-M-Y') . ".csv";
            $csv = TMP_FOLDER . $newCSVFileName;
            $vendor = $this->vendor_model->get_all_pincode_mapping();
            $delimiter = ",";
            $newline = "\r\n";
            $new_report = $this->dbutil->csv_from_result($vendor, $delimiter, $newline);
            
            log_message('info', __FUNCTION__ . ' => Rendered CSV');
            write_file($csv, $new_report);
            //Downloading Generated CSV
             if (file_exists($csv)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($csv));
                readfile($csv);
                exec("rm -rf " . escapeshellarg($csv));
                exit;
            }
        

    }

        /**
     * @desc: This function to send mails to vendors
     * @param: void
     * @return : void
     */
    function get_mail_to_vendors_form() {
        $data = array();
        $select = "service_centres.name, service_centres.id";
        $data['vendors'] = $this->vendor_model->getVendorDetails($select);
        $data['partners'] = $this->partner_model->getpartner();

        //Declaring array for modal call to get_247around_email_template function
        //For vendors
        $email = array();
        $email['where'] = array(
            'entity' => 'vendor'
        );
        $email['select'] = 'id,template,subject';
        $data['email_template'] = $this->vendor_model->get_247around_email_template($email);
        
        //For partners
        $partner_email = array();
        $partner_email['where'] = array(
            'entity' => 'partner'
        );
        $partner_email['select'] = 'id,template,subject';
        $data['partner_email_template'] = $this->vendor_model->get_247around_email_template($partner_email);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/sendemailtovendor', $data);
    }

    /**
     * @desc: This function is used to send mails to the selected vendor along with emails 
     *        It is being called using AJAX
     * params: $_FILES, POST array
     * return: void
     * 
     */
    function process_mail_to_vendor($id) {
        //Setting flag as TRUE ->Success and FALSE -> Failure
        $flag = TRUE;
        $attachment = "";
        //Do file upload if attachment is provided
        if (($_FILES['attachment_'.$id]['error'] != 4) && !empty($_FILES['attachment_'.$id]['tmp_name'])) {
            $tmpFile = $_FILES['attachment_'.$id]['tmp_name'];
            $fileName = $_FILES['attachment_'.$id]['name'];

           move_uploaded_file($tmpFile, TMP_FOLDER.$fileName);
            // move_uploaded_file($tmpFile, "c:\users\bredkhan"."\\$fileName");
            if (!empty($fileName)) {
               $attachment = TMP_FOLDER.$fileName;
                // $attachment = "c:\users\bredkhan"."\\$fileName";

            }
        }
        if ($this->input->post()) {
            $vendors = $this->input->post('vendors');
            //Checking for ALL vendors selected
            if($vendors[0] == 0){
                $select = "service_centres.name, service_centres.id";
                $vendors_array = $this->vendor_model->getVendorDetails($select);
                foreach ($vendors_array as $value) {
                    $vendors_list[] = $value['id'];
                }
            }else{
                $vendors_list = $vendors;
            }
            //Get email template values
            $email = array();
            $email['where'] = array(
                'entity' => 'vendor',
                'id' => $id
            );
            $email['select'] = '*';
            $email_template = $this->vendor_model->get_247around_email_template($email);

            if (!empty($email_template)) {
                $template_value = $email_template[0]['template_values'];
                //Making array for template values 
                $template_array = explode(',', $template_value);

                //Getting value in array from template_values column
                foreach ($template_array as $val) {
                    $table['table_name'] = explode('.', $val)[0];
                    $table['column_name'] = explode('.', $val)[1];
                    $table['primary_key'] = explode('.', $val)[2];
                    $template[] = $table;
                }

                foreach ($vendors_list as $value) {
                    $vendor_details = $this->vendor_model->getVendorContact($value);
                    //Setting TO for Email
                    $to = $vendor_details[0]['owner_email'] . ',' . $vendor_details[0]['primary_contact_email'];

                    foreach ($template as $value) {
                        $value['id'] = $vendor_details[0]['id'];
                        //Getting vendor details
                        $vendor_data = $this->vendor_model->get_data($value);

                        if ($vendor_data) {
                            $temp[] = $vendor_data[0]['user_name'];
                        } else {
                            //Logging error when values not found
                            log_message('info', __FUNCTION__ . ' Mail send Error. No data found to the following vendor ID ' . $vendor_details[0]['id']);
                            log_message('info', __FUNCTION__ . ' Template values are - ' . print_r($value, TRUE));
                            //Set Flag to check success or error of AJAX call
                            $flag = FALSE;
                        }
                    }
                    //Sending Mail to the vendor
                    if (isset($temp)) {
                        $emailBody = vsprintf($email_template[0]['body'], $temp);
                        //Sending Mail
                        $this->notify->sendEmail($email_template[0]['from'], $to, '', '', $email_template[0]['subject'], $emailBody, $attachment,$email_template[0]['template']);
                        //Loggin send mail details
                        log_message('info', __FUNCTION__ . ' Mail send to the following vendor ID ' . $vendor_details[0]['id']);
                        //Set Flag to check success or error of AJAX call
                        $flag = TRUE;
                    }
                }
            }
        } else {
            $flag = FALSE;
        }
        //Returning Flag value to AJAX request
            echo $flag;
    }
    
     /**
     * @desc: This function is used to send mails to the selected partner along with emails 
     *        It is being called using AJAX
     * params: $_FILES, POST array
     * return: void
     * 
     */
     function process_mail_to_partner($id) {
        //Setting flag as TRUE ->Success and FALSE -> Failure
        $flag = TRUE;
        $attachment = "";
        //Do file upload if attachment is provided
        if (($_FILES['attachment_'.$id]['error'] != 4) && !empty($_FILES['attachment_'.$id]['tmp_name'])) {
            $tmpFile = $_FILES['attachment_' . $id]['tmp_name'];
            $fileName = $_FILES['attachment_' . $id]['name'];

            move_uploaded_file($tmpFile, TMP_FOLDER.$fileName);
            if (!empty($fileName)) {
                $attachment = TMP_FOLDER.$fileName;
}
        }
        if ($this->input->post()) {
            $partners = $this->input->post('partners');
            //Checking for ALL vendors selected
            if ($partners[0] == 0) {
                $partners_array = $this->partner_model->getpartner();
                foreach ($partners_array as $value) {
                    $partners_list[] = $value['id'];
                }
            } else {
                $partners_list = $partners;
            }
            //Get email template values for partners
            $email = array();
            $email['where'] = array(
                'entity' => 'partner',
                'id' => $id
            );
            $email['select'] = '*';
            $email_template = $this->vendor_model->get_247around_email_template($email);

            if (!empty($email_template)) {
                $template_value = $email_template[0]['template_values'];
                //Making array for template values 
                $template_array = explode(',', $template_value);

                //Getting value in array from template_values column
                foreach ($template_array as $val) {
                    $table['table_name'] = explode('.', $val)[0];
                    $table['column_name'] = explode('.', $val)[1];
                    $table['primary_key'] = explode('.', $val)[2];
                    $template[] = $table;
                }

                foreach ($partners_list as $value) {
                    $partner_details = $this->partner_model->getpartner($value);
                    //Setting TO for Email
                    $to = $partner_details[0]['owner_email'] . ',' . $partner_details[0]['primary_contact_email'];

                    foreach ($template as $value) {
                        $value['id'] = $partner_details[0]['id'];
                        //Getting vendor details
                        $partner_data = $this->vendor_model->get_data($value);

                        if ($partner_data) {
                            $temp[] = $partner_data[0]['user_name'];
                        } else {
                            //Logging error when values not found
                            log_message('info', __FUNCTION__ . ' Mail send Error. No data found to the following vendor ID ' . $partner_details[0]['id']);
                            log_message('info', __FUNCTION__ . ' Template values are - ' . print_r($value, TRUE));
                            //Set Flag to check success or error of AJAX call
                            $flag = FALSE;
                        }
                    }
                    //Sending Mail to the vendor
                    if (isset($temp)) {
                        $emailBody = vsprintf($email_template[0]['body'], $temp);
                        //Sending Mail
                        $this->notify->sendEmail($email_template[0]['from'], $to, '', '', $email_template[0]['subject'], $emailBody, $attachment,$email_template[0]['template']);
                        //Loggin send mail details
                        log_message('info', __FUNCTION__ . ' Mail send to the following vendor ID ' . $partner_details[0]['id']);
                        //Set Flag to check success or error of AJAX call
                        $flag = TRUE;
                    }
                }
            }
        } else {
            $flag = FALSE;
        }
        //Returning Flag value to AJAX request
        echo $flag;
    }
    
    /**
     * @desc: This function is used to show misc counts for 247around
     * params: void
     * return: view
     * 
     */
    function show_around_dashboard(){
        //Initializing array data for where and select clause
        $data_report['query'] = $this->vendor_model->get_around_dashboard_queries(array('active' => 1,'type'=> 'service'));
        $data_report['data'] = $this->vendor_model->execute_dashboard_query($data_report['query']);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/247around_dashboard', $data_report);
    }
    
    /**
     * @desc: This function is used to show editable grid for SMS Templates
     * params: void
     * return: view
     * 
     */
    function get_sms_template_editable_grid(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/sms_template_editable_grid');
    }

    /**
     * @desc: This funtion is called from AJAX to get sms templates
     * params: void
     * return: ARRAY
     */
    function get_active_sms_template() {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $limit = isset($_POST['rows']) ? $_POST['rows'] : 10;
        $sidx = isset($_POST['sidx']) ? $_POST['sidx'] : 'name';
        $sord = isset($_POST['sord']) ? $_POST['sord'] : '';
        $start = $limit * $page - $limit;
        $start = ($start < 0) ? 0 : $start;

        $where = "";
        $searchField = isset($_POST['searchField']) ? $_POST['searchField'] : false;
        $searchOper = isset($_POST['searchOper']) ? $_POST['searchOper'] : false;
        $searchString = isset($_POST['searchString']) ? $_POST['searchString'] : false;

        if ($_POST['_search'] == 'true') {
            $ops = array(
                'eq' => '=',
                'ne' => '<>',
                'lt' => '<',
                'le' => '<=',
                'gt' => '>',
                'ge' => '>=',
                'bw' => 'LIKE',
                'bn' => 'NOT LIKE',
                'in' => 'LIKE',
                'ni' => 'NOT LIKE',
                'ew' => 'LIKE',
                'en' => 'NOT LIKE',
                'cn' => 'LIKE',
                'nc' => 'NOT LIKE'
            );
            foreach ($ops as $key => $value) {
                if ($searchOper == $key) {
                    $ops = $value;
                }
            }
            if ($searchOper == 'eq'){
                $searchString = $searchString;   
            }
            if ($searchOper == 'bw' || $searchOper == 'bn'){
                $searchString .= '%';
            }   
            if ($searchOper == 'ew' || $searchOper == 'en'){
                $searchString = '%' . $searchString;
            }
            if ($searchOper == 'cn' || $searchOper == 'nc' || $searchOper == 'in' || $searchOper == 'ni'){
                $searchString = '%' . $searchString . '%';
            }
                

            $where = "$searchField $ops '$searchString' ";
        }

        if (!$sidx){
            $sidx = 1;
        }
        $count = $this->db->count_all_results('sms_template');
         
        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }
       
        $query = $this->vendor_model->get_all_active_sms_template($start, $limit, $sidx, $sord, $where);
        
        $responce = new StdClass;
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;
                
        foreach ($query as $row) {
            $responce->rows[$i]['id'] = $row->id;
            $responce->rows[$i]['cell'] = array($row->tag, $row->template, $row->comments, $row->active);
            $i++;
        }
 
        echo json_encode($responce);
    }
    
    function update_sms_template() {
        $data = $this->input->post();
        $operation = $data['oper'];

        switch ($operation) {
            case 'add':
                //Initializing array for adding data
                $insert_data = [];
                //Checking active value checked
                if ($data['active'] == 'on') {
                    $data['active'] = 1;
                } else {
                    $data['active'] = 0;
                }
                //Setting insert array data
                $insert_data['tag'] = $data['tag'];
                $insert_data['template'] = $data['template'];
                $insert_data['comments'] = $data['comments'];
                $insert_data['active'] = $data['active'];
                $insert_data['create_date'] = date('Y-m-d H:i:s');
                $insert_id = $this->vendor_model->insert_sms_template($insert_data);
                if ($insert_id) {
                    log_message('info', __FUNCTION__ . ' New Sms Template has been added with ID ' . $insert_id);
                } else {
                    log_message('info', __FUNCTION__ . ' Err in adding New Sms Template');
                }
                break;
            case 'edit':
                //Initializing array for updating data
                $update_data = [];
                //Checking active value checked
                if ($data['active'] == 'on') {
                    $data['active'] = 1;
                } else {
                    $data['active'] = 0;
                }
                //Setting insert array data
                $update_data['tag'] = $data['tag'];
                $update_data['template'] = $data['template'];
                $update_data['comments'] = $data['comments'];
                $update_data['active'] = $data['active'];
                $update_id = $this->vendor_model->update_sms_template($update_data,$data['id']);
                if ($update_id) {
                    log_message('info', __FUNCTION__ . ' Sms Template has been updated with ID ' . $update_id);
                } else {
                    log_message('info', __FUNCTION__ . ' Err in updating New Sms Template');
                }
                break;

            case 'del':
                $delete = $this->vendor_model->delete_sms_template($data['id']);
                if ($delete) {
                    log_message('info', __FUNCTION__ . ' Sms Template has been deleted with ID'. $data['id'] );
                } else {
                    log_message('info', __FUNCTION__ . ' Err in deleting Sms Template');
                }
                break;
        }
    }
    
    /**
     * @desc: This function is used to show Service Center Report
     * params: void
     * return: view
     */
    function show_service_center_report(){
        $this->checkUserSession();
        //Getting employee sf relation
        $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
        if(!empty($sf_list)){
            $sf_list = $sf_list[0]['service_centres_id'];
        }
        
        $sf_closed_date = NULL;
        if(!empty($this->input->post('date'))) {
            $sf_closed_date = $this->input->post('date');
        }
        $data['html'] = $this->booking_utilities->booking_report_by_service_center($sf_list,'', '0', $sf_closed_date);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_service_center_report',$data);
    }
    
    /**
     * @desc: This function is used to send Report Mail to logged user and is called using AJAX
     * params: void
     * return : Boolean
     */
    function send_report_to_mail(){
        $this->checkUserSession();
        $user =$this->session->userdata;
        $employee_details = $this->employee_model->getemployeefromid($user['id']);
        if(isset($employee_details[0]['official_email']) && $employee_details[0]['official_email']){
            //Getting employee sf relation
            $sf_list = $this->vendor_model->get_employee_relation($user['id']);
            if(!empty($sf_list)){
                $sf_list = $sf_list[0]['service_centres_id'];
            }
            $html = $this->booking_utilities->booking_report_by_service_center($sf_list,'');
            $to = $employee_details[0]['official_email'];
            
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", "Service Center Report", $html, "",SERVICE_CENTERS_REPORT);
            log_message('info', __FUNCTION__ . ' Service Center Report mail sent to '. $to);
            echo TRUE;
        }else{
            echo FALSE;
        }
    }
    
    /**
     * @desc: This function is used to delete mail template 
     * parmas: INT id of mail template
     * return: Boolean
     * 
     */
    function delete_mail_template($id) {
        if ($this->booking_model->delete_mail_template_by_id($id)) {
            echo TRUE;
        } else {
            echo FALSE;
        }
    }
    
    /**
     * @desc: This function is used to show Service Center Report for New Vendors
     * params: void
     * return: view
     */
    function new_service_center_report(){
        $this->checkUserSession();
        //Getting employee sf relation
        $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
        if (!empty($sf_list)) {
            $sf_list = $sf_list[0]['service_centres_id'];
    }
        $data['html'] = $this->booking_utilities->booking_report_for_new_service_center($sf_list);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/new_service_center_report', $data);
    }
    
    /**
     * @desc: This method called by ajax to display available vendor list in dropdown
     * @param type $pincode
     * @param type $service_id
     */
    function get_vendor_availability($pincode, $service_id){
        $data = $this->vendor_model->check_vendor_availability($pincode, $service_id);
        if (!empty($data)) {
            foreach ($data as $value) {
                echo "<option selected value='$value[Vendor_ID]'>$value[Vendor_Name]</option>";
            }
        } else {
            echo "";
        }
    }

    /**
     * @desc: This method is used to update is_update field. It gets 0 Or 1 flag to update service center
     * @param String $service_center_id
     * @param String $flag
     */
    function control_update_process($service_center_id, $flag){
        $this->vendor_model->edit_vendor(array('is_update'=> $flag), $service_center_id);
        //Adding details in Booking State Change Table
        if($flag == 1){
            $this->notify->insert_state_change("", NEW_SF_CRM, OLD_SF_CRM , "New CRM Enabled for SF ID: ".$service_center_id , $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
        }else{
            $this->notify->insert_state_change("", OLD_SF_CRM, NEW_SF_CRM , "Old CRM Enabled for SF ID: ".$service_center_id , $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
        }
        redirect(base_url() . 'employee/vendor/viewvendor');
    }

    /**
     * @desc: This function is used to send Report Mail to logged user and is called using AJAX
     * params: void
     * return : Boolean
     */
    function new_service_center_report_to_mail(){
        $user =$this->session->userdata;
        $employee_details = $this->employee_model->getemployeefromid($user['id']);
        if(isset($employee_details[0]['official_email']) && $employee_details[0]['official_email']){
            //Getting employee sf relation
            $sf_list = $this->vendor_model->get_employee_relation($user['id']);
            if(!empty($sf_list)){
                $sf_list = $sf_list[0]['service_centres_id'];
            }
            $html = $this->booking_utilities->booking_report_for_new_service_center($sf_list);
            $to = $employee_details[0]['official_email'];

            $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, "", "", "New Service Center Report", $html, "",NEW_SERVICE_CENTERS_REPORT);
            log_message('info', __FUNCTION__ . ' New Service Center Report mail sent to '. $to);
            echo TRUE;
        }else{
            echo FALSE;
        }
    }

   /**
     * @Desc: This function is used to download Active vendors list
     *      in Excel. It is called using AJAX
     * params: void
     * @return: void
     * 
     */
    function download_sf_list_excel(){
        //Getting only Active Vendors List
        //$vendor  = $this->vendor_model->viewvendor('',1);
        $where = array('active' => '1','on_off' => '1');
        $select = "*";
        $whereIN = array();
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
            $serviceCenters = $sf_list[0]['service_centres_id'];
            $whereIN =array("id"=>explode(",",$serviceCenters));
        }
        $vendor = $this->vendor_model->getVendorDetails($select, $where,'name',$whereIN);
        $districArray = $this->miscelleneous->get_district_covered_by_vendors();
        foreach($vendor as $index=>$values){
            $vendor[$index]['covered_state'] = '';
            $vendor[$index]['sf_rm_name'] = '';
            $vendor[$index]['sf_rm_phone'] = '';
            $rm_detail = $this->vendor_model->get_rm_sf_relation_by_sf_id($values['id']);
            if(!empty($rm_detail)){
                $vendor[$index]['sf_rm_name'] = $rm_detail[0]['full_name'];
                $vendor[$index]['sf_rm_phone'] = $rm_detail[0]['phone'];
            }
            if(array_key_exists($values['id'], $districArray)){
                $vendor[$index]['covered_state'] = $districArray[$values['id']];
            }
        }
        log_message('info', __FUNCTION__);

        $template = 'SF_List_Template.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
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

                 'id' => 'vendor',
                'repeat' => TRUE,
                'data' => $vendor
            ));

        $output_file_dir = TMP_FOLDER;
        $output_file = "SF_List_" . date('y-m-d');
        $output_file_name = $output_file . ".xls";
        $output_file_excel = $output_file_dir . $output_file_name;
        $R->render('excel2003', $output_file_excel);
        
        //Downloading File
        if(file_exists($output_file_excel)){

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$output_file_name\""); 
            readfile($output_file_excel);
            exit;
        }

    }

    /**
     * @Desc: This function is used to remove images from vendor add/edit form
     *          It is being called using AJAX Request
     * parmas: type(column_name),vendor id
     * return: Boolean
     */
    function remove_image(){
        $data = $this->input->post();
        if($data['type'] == 'cancelled_cheque_file'){
                $this->reusable_model->update_table("account_holders_bank_details",array('cancelled_cheque_file'=>''),array('entity_type'=>'SF','entity_id'=>$data['id'],'is_active'=>1));
        }
        else{
        $vendor = [];
        $vendor[$data['type']] = '';
        //Making Database Entry as Null
        $this->vendor_model->edit_vendor($vendor, $data['id']);
        }
        //Logging 
        log_message('info',__FUNCTION__.' Following Images has been removed sucessfully: '.print_r($data, TRUE));
        echo TRUE;
}
    
    /**
     * @Desc: This function is used to check validation for file inputs through add/edit vendor form
     * 
     * @params: file_type
     * @return: Boolean
     * 
     */
     function file_input_validation($file_type){
        switch($file_type){
             case 'pan_file': 
                    $this->form_validation->set_rules('name_on_pan', 'Name on Pan', 'trim|required');
                    $this->form_validation->set_rules('pan_no', 'Pan Number', 'trim|required');
                    break;
                
             case 'cst_file': 
                    $this->form_validation->set_rules('cst_no', 'CST Number', 'trim|required');
                    break;
                
             case 'tin_file': 
                    $this->form_validation->set_rules('tin_no', 'TIN/VAT Number', 'trim|required');
                    break;
                
             case 'service_tax_file': 
                    $this->form_validation->set_rules('service_tax_no', 'Service Tax Number', 'trim|required');
                    break;
             case 'gst_file': 
                    $this->form_validation->set_rules('gst_no', 'GST Number', 'trim|required');
                    break;
        }
         return $this->form_validation->run();
     }

     /**
     * @desc: This function is to temporarily activate deactivate a particular vendor
     *
     * For this the vendor must be already registered with us and we change on_off flag of vendor in service_centres table
     *
     * @param: vendor id, on_off value
     * @return : void
     */
    function temporary_on_off_vendor($id, $on_off) {
        $this->checkUserSession();
        log_message('info',__FUNCTION__.' id: '.$id.' on_off: '.$on_off);
        $agentID = $this->session->userdata('id');
        $vendor['on_off'] = $on_off;
        $vendor['agent_id'] = $agentID;
        $this->vendor_model->edit_vendor($vendor, $id);
        $agent_name = $this->session->userdata('emp_name');
        
        //Check on off
        if($on_off == 1){
            $on_off_value = 'ON';
            $new_state = _247AROUND_VENDOR_NON_SUSPENDED;
            $old_state = _247AROUND_VENDOR_SUSPENDED;
        }else{
            $on_off_value = 'OFF';
            $new_state = _247AROUND_VENDOR_SUSPENDED;
            $old_state = _247AROUND_VENDOR_NON_SUSPENDED;
        }
        
        //Getting Vendor Details
        $sf_details = $this->vendor_model->getVendorContact($id);
        $sf_name = $sf_details[0]['name'];
        
        //Sending Mail to corresponding RM and admin group 
        $employee_relation = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);
        if (!empty($employee_relation)) {
            $to = $employee_relation[0]['official_email'];
            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("sf_temporary_on_off");
            if (!empty($template)) {
                if($sf_details[0]['is_micro_wh'] == 1){
                    $to .= ",".$template[1];
                }
                $email['rm_name'] = $employee_relation[0]['full_name'];
                $email['sf_name'] = ucfirst($sf_name);
                $email['on_off'] = $on_off_value;
                $email['action_by'] = $agent_name;
                $subject = " Temporary " . $on_off_value . " Vendor " . $sf_name;
                $emailBody = vsprintf($template[0], $email);
                $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "",'sf_temporary_on_off');
            }

            log_message('info', __FUNCTION__ . ' Temporary  '.$on_off_value.' of Vendor' . $sf_name);
        }
        
        $log = array(
            "entity" => "vendor",
            "entity_id" => $id,
            "agent_id" => $this->session->userdata('id'),
            "action" =>  $new_state
        );
        $this->vendor_model->insert_log_action_on_entity($log);
        redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }
    
    /**
     * @Desc: This function is used to show list of Documents uploaded for Vendors/ Used to Handle Filter Request
     * @params: void/ POST Array
     * @return: view
     * 
     */
    function show_vendor_documents_view(){
        $this->checkUserSession();
        //Getting RM Lists
        $rm = $this->employee_model->get_rm_details();
        $current_rm_id = '';
        $active = '';
        $serviceCenters ='';
        $selectedData["all_active"] = "all";
        $selectedData["rm"] = $rm;
        $data = array();
        if($this->input->post()){
            $data = $this->input->post();   
        }
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $current_rm_id = $this->session->userdata('id');
        }
        if(array_key_exists("rm",$data)){
            if($data['rm'] != 'all'){
                $selectedData["rm"] = $current_rm_id = $data['rm'];
            }
        }
         if(array_key_exists("all_active",$data)){
             if($data['all_active'] == 'active'){
                $active = 1;
                $selectedData["all_active"] = "active";
             }
        }
        if($current_rm_id != ''){
            $sf_list = $this->vendor_model->get_employee_relation($current_rm_id);
            $serviceCenters = $sf_list[0]['service_centres_id'];
        }
        $query = $this->vendor_model->viewvendor("", $active, $serviceCenters);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_vendor_documents_view', array('data' => $query, 'rm' =>$rm,'selected'=>$selectedData));
    }
    
    /**
     * @Desc: This function is used to remove images from vendor add/edit form
     *          It is being called using AJAX Request
     * parmas: type(column_name),vendor id
     * return: Boolean
     */
    function remove_engineer_image(){
        $data = $this->input->post();
        
        $engineer[$data['type']] = "";
        $where = array('id' => $data['id'] );
	$engineer_id = $this->vendor_model->update_engineer($where,$engineer);
        
        //Logging 
        log_message('info',__FUNCTION__.' '.$data['type'].' Following Images has been removed sucessfully for engineer ID : '.print_r($engineer_id));
        echo TRUE;
}

/**
     * @Desc: This function is used to download latest pincode file uploaded in s3
     * @params: void
     * @return:void
     * 
     */
    function download_pincode_latest_file($file_name){
        //s3 file path
        $file_path = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-pincodes/".$file_name;
        
        //Downloading File
        if(!empty($file_name)){

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$file_name\""); 
            readfile($file_path);
            exit;
        }else{
            //Logging_error
            log_message('info',__FUNCTION__.' No latest file has been found to be uploaded.');
        }
        
        
    }
    
    /**
     * 
     * @Desc: This function is used to show SC Charges list according to state
     * @params: state
     * @return: View
     * 
     * 
     */
    function get_sc_charges_list(){
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('employee_id'));
        $sc_charges_data = $this->service_centre_charges_model->get_service_caharges_data("partner_id,services,category,capacity,service_category,vendor_basic_charges,"
                . "vendor_tax_basic_charges,vendor_total,customer_net_payable",array("partner_id <> " => _247AROUND_DEMO_PARTNER , "service_category NOT IN ('".REPEAT_BOOKING_TAG."','".SPARE_PART_BOOKING_TAG."')" => NULL));
        $partner_id_array = array_unique(array_column($sc_charges_data, 'partner_id'));
        foreach($partner_id_array as $partnerID){
            $booking_sources_array[$partnerID] = '';
             $booking_sources = $this->partner_model->get_booking_sources_by_price_mapping_id($partnerID);
             if(!empty($booking_sources[0]['code'])){
                 $booking_sources_array[$partnerID] = $booking_sources[0]['code'];
             }
        }
            //Looping through all the values 
            foreach ($sc_charges_data as $value) {
                $array_final['sc_code'] = $booking_sources_array[$value['partner_id']];
                $array_final['product'] = $value['services'];
                $array_final['category'] = $value['category'];
                $array_final['capacity'] = $value['capacity'];
                $array_final['service_category'] = $value['service_category'];
                $array_final['vendor_basic_charges'] = round($value['vendor_basic_charges'],0);
                $array_final['vendor_tax_basic_charges'] = round($value['vendor_tax_basic_charges'],0);
                $array_final['vendor_total'] = round($value['vendor_total'],0);
                $array_final['customer_net_payable'] = round($value['customer_net_payable'],0);
                $final_array[] = $array_final;
            }
            $template = 'SC-Charges-List-Template.xlsx';
            //set absolute path to directory with template files
            $templateDir = __DIR__ . "/../excel-templates/";
            //set config for report
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

                     'id' => 'sc',
                    'repeat' => TRUE,
                    'data' => $final_array
                ));

            $output_file_dir = TMP_FOLDER;
            $output_file = "Charges-List-" . date('j-M-Y');
            $output_file_name = $output_file . ".xlsx";
            $output_file_excel = $output_file_dir . $output_file_name;
            $R->render('excel', $output_file_excel);
            
            //Downloading File
            if(file_exists($output_file_excel)){

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"$output_file_name\""); 
                readfile($output_file_excel);
                exit;
            }           
    }
    
    /**
     * @desc: This function is used to show editable grid for tax rates Templates
     * params: void
     * return: view
     * 
     */
    function get_tax_rates_template_editable_grid(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/tax_rates_template_editable_grid');      
    }
    
    /**
     * @desc: This funtion is called from AJAX to get tax rates templates
     * params: void
     * return: ARRAY
     */
    function get_active_tax_rates_template() {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $limit = isset($_POST['rows']) ? $_POST['rows'] : 10;
        $sidx = isset($_POST['sidx']) ? $_POST['sidx'] : 'name';
        $sord = isset($_POST['sord']) ? $_POST['sord'] : '';
        $start = $limit * $page - $limit;
        $start = ($start < 0) ? 0 : $start;

        $where = "";
        $searchField = isset($_POST['searchField']) ? $_POST['searchField'] : false;
        $searchOper = isset($_POST['searchOper']) ? $_POST['searchOper'] : false;
        $searchString = isset($_POST['searchString']) ? $_POST['searchString'] : false;

        if ($_POST['_search'] == 'true') {
            $ops = array(
                'eq' => '=',
                'ne' => '<>',
                'lt' => '<',
                'le' => '<=',
                'gt' => '>',
                'ge' => '>=',
                'bw' => 'LIKE',
                'bn' => 'NOT LIKE',
                'in' => 'LIKE',
                'ni' => 'NOT LIKE',
                'ew' => 'LIKE',
                'en' => 'NOT LIKE',
                'cn' => 'LIKE',
                'nc' => 'NOT LIKE'
            );
            foreach ($ops as $key => $value) {
                if ($searchOper == $key) {
                    $ops = $value;
                }
            }
            if ($searchOper == 'eq'){
                $searchString = $searchString;
            } 
            if ($searchOper == 'bw' || $searchOper == 'bn'){
                $searchString .= '%';
            }
            if ($searchOper == 'ew' || $searchOper == 'en'){
                 $searchString = '%' . $searchString;
            }
            if ($searchOper == 'cn' || $searchOper == 'nc' || $searchOper == 'in' || $searchOper == 'ni'){
                $searchString = '%' . $searchString . '%';
            }
                

            $where = "$searchField $ops '$searchString' ";
        }

        if (!$sidx){
            $sidx = 1;
        }
        $count = $this->db->count_all_results('tax_rates');
         
        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }
       
        $query = $this->vendor_model->get_all_active_tax_rates_template($start, $limit, $sidx, $sord, $where);
        
        $responce = new StdClass;
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;
                
        foreach ($query as $row) {
            $responce->rows[$i]['id'] = $row->id;
            $responce->rows[$i]['cell'] = array($row->tax_code, $row->state, $row->product_type, $row->rate,$row->from_date,$row->to_date,$row->active);
            $i++;
        }
 
        echo json_encode($responce);
    }
    
    function update_tax_rate_template() {
        $data = $this->input->post();
        $operation = $data['oper'];

        switch ($operation) {
            case 'add':
                //Initializing array for adding data
                $insert_data = [];
                //Checking active value checked
                if ($data['active'] == 'on') {
                    $data['active'] = 1;
                } else {
                    $data['active'] = 0;
                }
                //Setting insert array data
                $insert_data['tax_code'] = $data['tax_code'];
                $insert_data['state'] = $data['state'];
                $insert_data['product_type'] = $data['product_type'];
                $insert_data['rate'] = $data['rate'];
                $insert_data['from_date'] = $data['from_date'];
                $insert_data['to_date'] = $data['to_date'];
                $insert_data['active'] = $data['active'];
                $insert_data['create_date'] = date('Y-m-d H:i:s');
                $insert_id = $this->vendor_model->insert_tax_rates_template($insert_data);                
                if ($insert_id) {
                    log_message('info', __FUNCTION__ . ' New Tax Rate Template has been added with ID ' . $insert_id);
                } else {
                    log_message('info', __FUNCTION__ . ' Err in adding New Tax Rate Template');
                }
                break;
            case 'edit':
                //Initializing array for updating data
                $update_data = [];
                //Checking active value checked
                if ($data['active'] == 'on') {
                    $data['active'] = 1;
                } else {
                    $data['active'] = 0;
                }
                //Setting insert array data
                $update_data['tax_code'] = $data['tax_code'];
                $update_data['state'] = $data['state'];
                $update_data['product_type'] = $data['product_type'];
                $update_data['rate'] = $data['rate'];
                $update_data['from_date'] = $data['from_date'];
                $update_data['to_date'] = $data['to_date'];
                $update_data['active'] = $data['active'];
                $update_id = $this->vendor_model->update_tax_rates_template($update_data,$data['id']);
                if ($update_id) {
                    log_message('info', __FUNCTION__ . ' Sms Template has been updated with ID ' . $update_id);
                } else {
                    log_message('info', __FUNCTION__ . ' Err in updating New Sms Template');
                }
                break;

            case 'del':
                $delete = $this->vendor_model->delete_tax_rate_template($data['id']);
                if ($delete) {
                    log_message('info', __FUNCTION__ . ' Tax Rate Template has been deleted with ID'. $data['id'] );
                } else {
                    log_message('info', __FUNCTION__ . ' Err in deleting Tax Rate Template');
                }
                break;
        }
    }
    
     /**
     * @desc: This function is used to show editable grid for vendor escalation policy
     * params: void
     * return: view
     * 
     */
    function get_vandor_escalation_policy_editable_grid(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/vandor_escalation_policy_template_editable_grid');
        
    }
    
    /**
     * @desc: This funtion is called from AJAX to get vendor escalation policy
     * params: void
     * return: ARRAY
     */
    function get_vandor_escalation_policy_rates_template() {
        $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $limit = isset($_POST['rows']) ? $_POST['rows'] : 10;
        $sidx = isset($_POST['sidx']) ? $_POST['sidx'] : 'name';
        $sord = isset($_POST['sord']) ? $_POST['sord'] : '';
        $start = $limit * $page - $limit;
        $start = ($start < 0) ? 0 : $start;

        $where = "";
        $searchField = isset($_POST['searchField']) ? $_POST['searchField'] : false;
        $searchOper = isset($_POST['searchOper']) ? $_POST['searchOper'] : false;
        $searchString = isset($_POST['searchString']) ? $_POST['searchString'] : false;

        if ($_POST['_search'] == 'true') {
            $ops = array(
                'eq' => '=',
                'ne' => '<>',
                'lt' => '<',
                'le' => '<=',
                'gt' => '>',
                'ge' => '>=',
                'bw' => 'LIKE',
                'bn' => 'NOT LIKE',
                'in' => 'LIKE',
                'ni' => 'NOT LIKE',
                'ew' => 'LIKE',
                'en' => 'NOT LIKE',
                'cn' => 'LIKE',
                'nc' => 'NOT LIKE'
            );
            foreach ($ops as $key => $value) {
                if ($searchOper == $key) {
                    $ops = $value;
                }
            }
            if ($searchOper == 'eq'){
                $searchString = $searchString;
            }   
            if ($searchOper == 'bw' || $searchOper == 'bn'){
                $searchString .= '%';
            }   
            if ($searchOper == 'ew' || $searchOper == 'en'){
                $searchString = '%' . $searchString;
            }  
            if ($searchOper == 'cn' || $searchOper == 'nc' || $searchOper == 'in' || $searchOper == 'ni'){
                $searchString = '%' . $searchString . '%';
            }
                

            $where = "$searchField $ops '$searchString' ";
        }

        if (!$sidx){
            $sidx = 1;
        }
            
        $count = $this->db->count_all_results('vendor_escalation_policy');
         
        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages){
            $page = $total_pages;
        }
       
        $query = $this->vendor_model->get_vandor_escalation_policy_template($start, $limit, $sidx, $sord, $where);
        
        $responce = new StdClass;
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;
                
        foreach ($query as $row) {
            $responce->rows[$i]['id'] = $row->id;
            $responce->rows[$i]['cell'] = array($row->escalation_reason, $row->entity, $row->process_type, $row->sms_to_owner,$row->sms_to_poc,$row->sms_body,$row->active);
            $i++;
        }
 
        echo json_encode($responce);
    }

    /**
     * @desc: This funtion is called from AJAX to update vendor escalation policy
     * params: void
     * return: ARRAY
     */
    function update_vandor_escalation_policy_template() {
        $data = $this->input->post();
        $operation = $data['oper'];

        switch ($operation) {
            case 'add':
                //Initializing array for adding data
                $insert_data = [];
                //Checking active value checked
                if ($data['active'] == 'on') {
                    $data['active'] = 1;
                } else {
                    $data['active'] = 0;
                }
                if ($data['sms_to_owner'] == 'on') {
                    $data['sms_to_owner'] = 1;
                } else {
                    $data['sms_to_owner'] = 0;
                }
                if ($data['sms_to_poc'] == 'on') {
                    $data['sms_to_poc'] = 1;
                } else {
                    $data['sms_to_poc'] = 0;
                }
                //Setting insert array data
                $insert_data['escalation_reason'] = $data['escalation_reason'];
                $insert_data['entity'] = $data['entity'];
                $insert_data['process_type'] = $data['process_type'];
                $insert_data['sms_to_owner'] = $data['sms_to_owner'];
                $insert_data['sms_to_poc'] = $data['sms_to_poc'];
                $insert_data['sms_body'] = $data['sms_body'];
                $insert_data['active'] = $data['active'];
                $insert_data['create_date'] = date('Y-m-d H:i:s');
                $insert_id = $this->vendor_model->insert_vandor_escalation_policy_template($insert_data);
                print_r($insert_id);
                if ($insert_id) {
                    log_message('info', __FUNCTION__ . ' New Vendor Escalation Policy has been added with ID ' . $insert_id);
                } else {
                    log_message('info', __FUNCTION__ . ' Err in adding New Vendor Escalation Policy Template');
                }
                break;
            case 'edit':
                //Initializing array for updating data
                $update_data = [];
                //Checking active value checked
                if ($data['active'] == 'on') {
                    $data['active'] = 1;
                } else {
                    $data['active'] = 0;
                }
                 if ($data['sms_to_owner'] == 'on') {
                    $data['sms_to_owner'] = 1;
                } else {
                    $data['sms_to_owner'] = 0;
                }
                if ($data['sms_to_poc'] == 'on') {
                    $data['sms_to_poc'] = 1;
                } else {
                    $data['sms_to_poc'] = 0;
                }
                //Setting insert array data
                $update_data['escalation_reason'] = $data['escalation_reason'];
                $update_data['entity'] = $data['entity'];
                $update_data['process_type'] = $data['process_type'];
                $update_data['sms_to_owner'] = $data['sms_to_owner'];
                $update_data['sms_to_poc'] = $data['sms_to_poc'];
                $update_data['sms_body'] = $data['sms_body'];
                $update_data['active'] = $data['active'];
                $update_id = $this->vendor_model->update_vandor_escalation_policy_template($update_data,$data['id']);
                if ($update_id) {
                    log_message('info', __FUNCTION__ . ' Vendor Escalation Policy Template has been updated with ID ' . $update_id);
                } else {
                    log_message('info', __FUNCTION__ . ' Err in updating New Vendor Escalation Policy');
                }
                break;

            case 'del':
                $delete = $this->vendor_model->delete_vandor_escalation_policy_template($data['id']);
                if ($delete) {
                    log_message('info', __FUNCTION__ . ' Vendor Escalation Policy Template has been deleted with ID'. $data['id'] );
                } else {
                    log_message('info', __FUNCTION__ . ' Err in deleting Vendor Escalation Policy');
                }
                break;
        }
    }
    
    function get_sc_upcountry_details($service_center_id){
        $data['data'] = $this->upcountry_model->get_sub_service_center_details(array('service_center_id' =>$service_center_id));
        $data['saas'] = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/sc_upcountry_details',$data);      
    }

    /**
     * @desc This method is used toi update sc booking table and upcountry details in booking details.
     * @param String $booking_id
     * @param Integer $up_flag
     */
    function update_upcountry_and_unit_in_sc($booking_id, $up_flag){
        log_message('info', "Booking iD " . $booking_id." Flag ". $up_flag);
        if($up_flag == 1){
            $this->miscelleneous->assign_upcountry_booking($booking_id, 
                    _247AROUND_DEFAULT_AGENT, _247AROUND_DEFAULT_AGENT_NAME);
        }
        $this->miscelleneous->check_unit_in_sc($booking_id);
    }

    /**
     * @Desc: This function is used to show Penalty booking form
     * @params: String (Booking ID)
     * @return:void
     */
    function get_escalate_booking_form($booking_id,$status,$penalty_active="") {
        //get escalation reasons for 247around
        if($status == 'Completed'){
            $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'247around','active'=> '1','process_type'=>'report_complete'));
        } else if($status == 'Cancelled'){
            $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'247around','active'=> '1','process_type'=>'report_cancel'));
        }
            

        $data['vendor_details'] = $this->vendor_model->getVendor($booking_id);
        $data['booking_id'] = $booking_id;
        $data['status'] = $status;
        if($penalty_active == 0 && $penalty_active != Null){
            $data['penalty_active'] = $penalty_active;
        }
        //print("<pre>".  print_r($data,true)."</pre>");exit();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/get_escalate_booking_form', $data);
    }
    

    /**
     * @Desc: This function is used to remove Penalty on Booking
     * @params: Booking ID, Status
     * @return : View
     * 
     */
    function process_remove_penalty(){
        
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $booking_id = $this->input->post('booking_id');
        $penalty_remove_reason = $this->input->post('penalty_remove_reason');
        $penalty_remove_agent_id = $this->session->userdata('id');
        $penalty_remove_date = date("Y-m-d H:i:s");
        $flag = FALSE;
        foreach($id as $key=>$value){
            
            $data = array('active' => 0,
                          'penalty_remove_reason'=>$penalty_remove_reason[$key],
                          'penalty_remove_agent_id'=>$penalty_remove_agent_id,
                          'penalty_remove_date'=>$penalty_remove_date);
            $update = $this->penalty_model->update_penalty_on_booking($value, $data);
            
            if ($update) {
            //Logging
            log_message('info', __FUNCTION__ . ' Penalty has been Removed from Booking ID :' . $booking_id[$key]);
            
            //Getting Booking Details 
            $booking_details = $this->booking_model->getbooking_history($booking_id[$key], 'service_centres');
           
                log_message("info", __METHOD__. " remove key ".$penalty_remove_reason[$key]);
            $a = array('service_center_id' => $booking_details[0]['assigned_vendor_id'],
                    "criteria_id" => BOOKING_NOT_UPDATED_PENALTY_CRITERIA,
                    "active" => 1,
                    "booking_id" => $booking_id[$key]);
            $aData = $this->reusable_model->get_search_query('penalty_on_booking','*',$a,NULL,NULL,NULL,NULL,NULL,NULL)->result_array();
            
            if(empty($aData)){
                $this->booking_model->update_booking($booking_id[$key], array('is_penalty' => 0));
            }
            
            
            
            //Sending Mails

            $template = $this->booking_model->get_booking_email_template("remove_penalty_on_booking");
            if (!empty($template)) {
                $to = $booking_details[0]['primary_contact_email'] . ',' . $booking_details[0]['owner_email'];
                //From will be currently logged in user's official Email
                $from = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['official_email'];

                //Getting RM Official Email details to send Welcome Mails to them as well
                $rm_id = $this->vendor_model->get_rm_sf_relation_by_sf_id($booking_details[0]['assigned_vendor_id'])[0]['agent_id'];
                $rm_official_email = $this->employee_model->getemployeefromid($rm_id)[0]['official_email'];
                //Sending Mail
                $email['booking_id'] = $booking_id[$key];
                $emailBody = vsprintf($template[0], $email);

                $subject['booking_id'] = $booking_id[$key];
                $subjectBody = vsprintf($template[4], $subject);
                $this->notify->sendEmail($from, $to, $template[3] . "," . $rm_official_email, '', $subjectBody, $emailBody, "",'remove_penalty_on_booking', "", $booking_id[$key]);

                //Logging
                log_message('info', " Remove Penalty Report Mail Send successfully" . $emailBody);
            } else {
                //Logging
                log_message('info', __FUNCTION__ . ' Error in getting Email Template for remove_penalty_on_booking');
            }

            $flag = TRUE;
            }   else {
            //Logging
                log_message('info', __FUNCTION__ . ' Penalty already Removed for Booking ID :' . $booking_id[$key]);
                $flag = TRUE;
            }
        }
     if($flag){
         //Session success
        $this->session->set_userdata('success', 'Penalty removed Successfully');
     }else{
         $this->session->set_userdata('success', 'Error In Remopving Penalty!!! Please Try Again');
     }
    if($status === _247AROUND_PENDING || $status === _247AROUND_RESCHEDULED){
        redirect(base_url() . 'employee/booking/view_bookings_by_status/'._247AROUND_PENDING);
    }else{
        redirect(base_url() . 'employee/booking/view_bookings_by_status/' . $status);
    }
    }
    
    function get_penalty_details_data($booking_id, $status){

        $where  = array('penalty_on_booking.booking_id'=>$booking_id,'penalty_on_booking.active' => 1);
        $data['penalty_details'] = $this->penalty_model->get_penalty_on_booking_any($where,'penalty_on_booking.*,name',array('*'));

        $sf_id =  array_unique(array_map(function ($k) {
                            return $k['service_center_id'];
                        }, $data['penalty_details']));
        $array = array();
        foreach($sf_id as $value){
            $date = "DATE_FORMAT( CURRENT_DATE - INTERVAL 1 MONTH, '%Y/%m/01' ) ";
            $date1 = "DATE_FORMAT( CURRENT_DATE, '%Y/%m/01' )";
            $remove_penalty_where = array('penalty_on_booking.service_center_id' => $value,
                        'penalty_on_booking.active' => 0,
                         'penalty_on_booking.penalty_remove_date < '.$date1.'' => NULL,
                        'penalty_on_booking.penalty_remove_date >= '.$date.'' => NULL);
           
            $remove_penalty = $this->reusable_model->get_search_query('penalty_on_booking','name as sf_name,count(*) as count',$remove_penalty_where,array('service_centres' => 'penalty_on_booking.service_center_id = service_centres.id'),NULL,NULL,NULL,NULL,'penalty_on_booking.service_center_id')->result_array();
          
            if(!empty($remove_penalty)){
                array_push($array,$remove_penalty[0]);
            }
        }
        
        $data['remove_penalty_details'] = $array;
        $data['status'] = $status;
        $this->load->view('employee/get_penalty_on_booking_details',$data);
    }
    
    /**
     * @desc This method is used to update sub_service_center_details table via ajax call
     * @param void()
     * @return string
     */
    function update_sub_service_center_details(){
        log_message('info',__FUNCTION__);
        if($this->input->post()){
            $id = $this->input->post('id');
            $sc_id = $this->input->post('service_center_id');
            $update_array = [];
            $update_array['upcountry_rate'] = $this->input->post('upcountry_rate');
            $update_id = $this->upcountry_model->update_sub_service_center_upcountry_details($update_array,$id);
            
            if($update_id) {
                 $log = array(
                     "entity" => "vendor",
                     "entity_id" => $sc_id,
                     "agent_id" => $this->session->userdata('id'),
                     "action" =>  "SC HQ Updated",
                     "remarks" => "Update SC HQ ID ".$id
                 );
                 $this->vendor_model->insert_log_action_on_entity($log);
                echo "success";
            } else {
                echo "failed";
            }
        }
    }

    /**
     * @desc This method is used to delete sub office details in sub_service_center_details table via ajax call
     * @param void()
     * @return string
     */
    function de_activate_sub_service_center_details($active_flag){
        log_message('info',__FUNCTION__);
       if($this->input->post()){
           $id = $this->input->post('id');
           $sc_id = $this->input->post('service_center_id');
           $update_id = $this->upcountry_model->update_sub_service_center_upcountry_details(array("active" => $active_flag), $id);
           if($update_id){
               $log = array(
                    "entity" => "vendor",
                    "entity_id" => $sc_id,
                    "agent_id" => $this->session->userdata('id'),
                    "action" =>  "SC HQ Updated",
                    "remarks" => "Deleted SC HQ ID ".$id
                );
                $this->vendor_model->insert_log_action_on_entity($log);
               echo "success";
           }
           else{
               echo "failed";
           }
       }
    }

    /**
     * @desc: This function is used to get the reassign partner page
     * @param: booking id
     * @return : void
     */
    function get_reassign_partner_form() {
        $partners = $this->partner_model->get_all_partner();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/reassignvendor', array('partners' => $partners,'type'=>'partner'));
    }

    
    /**
     * @desc: This function reassigns partner for a particular booking.
     * @param: void
     * @return : void
     */
    function process_reassign_partner_form(){
        log_message('info',__FUNCTION__);
        $booking_id = $this->input->post('booking_id');
        $partner = $this->input->post('partner');
        if(count($booking_id) === count($partner)){
            foreach($booking_id as $key=>$value){
                
                // update partner to corresponding booking id in booking_details table
                $booking_details_data = array('partner_id'=>$partner[$key],);
                $this->booking_model->update_booking(trim($value), $booking_details_data);
                
                // update partner to corresponding booking id in booking_unit_details table
                $booking_unit_details_data = array('partner_id'=>$partner[$key],);
                $this->booking_model->update_booking_unit_details(trim($value), $booking_unit_details_data);
                
                log_message('info', "Reassigned Partner For Booking_id: " . $value . "  By " .
                $this->session->userdata('employee_id') . " and new Partner Id =" . $partner[$key]);
            }
            $output = "Booking Updated Successfully";
            $userSession = array('success' => $output);
            $this->session->set_userdata($userSession);
            redirect(base_url() . 'employee/vendor/get_reassign_partner_form');
        }else{
            $output = "Please fill all the input field";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
            redirect(base_url() . 'employee/vendor/get_reassign_partner_form');
        }
        
    }

    /**
     * if pincode exist in the india pincode table the echo success other wise Not Exist
     * @param String $pincode
     */
    function check_pincode_exist_in_india_pincode($pincode = ""){
        
        $city = $this->vendor_model->getDistrict_from_india_pincode("",$pincode);
        if(!empty($city)){
            echo "Success";
        } else {
            echo "Not Exist";
        }
    }
    
    /**
     * @desc This is used to check upcountry for those booking who have not marked upcountry
     * This called from CRON
     */
    function re_check_upcountry_for_pending_booking() {
        log_message("info", __METHOD__);
        $am_email = array();
        $this->load->library('table');
        $data = $this->booking_model->date_sorted_booking(500, 0, "");
        $this->table->set_heading('Booking ID', 'Account Manager Name');
        $flag = 0;       
        foreach ($data as $value) {
            if (!empty($value->booking_id) && $value->is_upcountry == 0) {
                $vendor_data = array();
                $vendor_data[0]['vendor_id'] = $value->assigned_vendor_id;
                $vendor_data[0]['min_upcountry_distance'] = $value->min_upcountry_distance;
                if (!empty($value->district)) {
                    $vendor_data[0]['city'] = $value->district;
                } else {
                    $vendor_data[0]['city'] = $this->vendor_model->get_distict_details_from_india_pincode($value->booking_pincode)['district'];
                }
                $p_where = array('id' => $value->partner_id);
                $partner_details = $this->partner_model->get_all_partner($p_where);
                $data = $this->upcountry_model->action_upcountry_booking($value->city, $value->booking_pincode, $vendor_data, $partner_details);
                switch ($data['message']) {
                    case UPCOUNTRY_BOOKING:
                    case UPCOUNTRY_LIMIT_EXCEED:
                        $flag = 1;
                        //$am_detail = $this->partner_model->getpartner_details('official_email, full_name', array('partners.id' => $value->partner_id),"", TRUE);
                        $am_detail = $this->partner_model->getpartner_data("employee.official_email, employee.full_name", 
                            array('partners.id' => $value->partner_id),"",1,1,1);
                        foreach($am_detail as $am) {
                            $this->table->add_row($value->booking_id, $am['full_name']);
                            array_push($am_email, $am['official_email']);
                        }
                        break;

                }
            }
        }
        if($flag == 1){
            $template = $this->booking_model->get_booking_email_template(MISSED_UPCOUNTRY_BOOKING);
            if (!empty($template)) {
                $am_emails = implode(",", array_unique($am_email));
                $to = $am_emails;
                $cc = DEVELOPER_EMAIL;
                $subject = $template[4];
                $emailBody = vsprintf($template[0],$this->table->generate());
                $this->notify->sendEmail(NOREPLY_EMAIL_ID, $to, $cc, "", $subject, $emailBody, "", UPCOUNTRY_BOOKING_NOT_MARKED);
            }
        } else {
            log_message("info", __METHOD__." There is no pending booking which need to update for upcountry");
        }
    }
    
    
    /**
     * @Desc: This function is used to get the service center for filtered brackets list
     * @param void
     * @return: string
     * 
     */
    function get_service_center_details(){
        
        $is_wh = $this->input->post('is_wh');
        if(!empty($is_wh)){
            $select = "service_centres.district, service_centres.id,service_centres.state, service_centres.name";
            $where = array('is_wh' => 1,'active' => 1);
            $option = '<option selected="" disabled="">Select Warehouse</option>';
        }else{
            $select = "service_centres.name, service_centres.id";
            $where = "";
            $option = '<option selected="" disabled="">Select Service Center</option>';
        }
        if($this->session->userdata('user_group') == 'regionalmanager'){
            $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id') );
            $serviceCenters = $sf_list[0]['service_centres_id'];
            $whereIN = array("id"=>explode(",",$serviceCenters));
        }
        else{
            $whereIN = NULL;
        }
        $data= $this->reusable_model->get_search_result_data("service_centres",$select,$where,NULL,NULL,NULL,$whereIN,NULL,array());
        $saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);
        
        foreach ($data as $value) {
            $option .= "<option value='" . $value['id'] . "'";
            
            
            if(!empty($is_wh)){
                $option .= " data-warehose='1' > ";
                if($saas){
                    $option .=  $value['name'] ." ( <strong>". $value['state']. " </strong>)"."</option>";
                } else {
                    $option .=  _247AROUND_EMPLOYEE_STRING." ".$value['district'] ." ( <strong>". $value['state']. " </strong>)"."</option>";
                }
                
            }else{
                $option .= " > ";
                $option .= $value['name'] . "</option>";
            }
        }

        echo $option;
    }
    
    /**
     * @Desc: This function is used to get the service center for filtered brackets list
     * @param void
     * @return: string
     * 
     */
    function get_service_center_with_micro_wh() {
        log_message('info', __METHOD__ . print_r($this->input->post('partner_id'), true));

        $partner_id = $this->input->post('partner_id');

        $partner_data = $this->partner_model->getpartner($partner_id);
        $saas = $this->booking_utilities->check_feature_enable_or_not(PARTNER_ON_SAAS);

        $option = '<option selected="" disabled="">Select Warehouse</option>';
        if ($partner_data[0]['is_wh'] == 1) {
            $select = "service_centres.district, service_centres.id,service_centres.state, service_centres.name";
            $where = array('is_wh' => 1, 'active' => 1);

            $data = $this->reusable_model->get_search_result_data("service_centres", $select, $where, NULL, NULL, NULL, array(), NULL, array());

            foreach ($data as $value) {
                $option .= "<option data-warehose='1' value='" . $value['id'] . "'";
                $option .= " > ";
                if($saas){
                    $option .=  $value['name'] . " ( <strong>" . $value['state'] . " </strong>) - (Central Warehouse)" . "</option>";
                } else {
                    $option .= _247AROUND_EMPLOYEE_STRING . " " . $value['district'] . " ( <strong>" . $value['state'] . " </strong>) - (Central Warehouse)" . "</option>";
                }
            }
        }
        if ($partner_data[0]['is_micro_wh'] == 1) {
             $micro_wh_state_mapp_data_list = $this->inventory_model->get_micro_wh_state_mapping_partner_id($partner_id);

            if (!empty($micro_wh_state_mapp_data_list)) {
                foreach ($micro_wh_state_mapp_data_list as $value) {
                    $option .= "<option  data-warehose='2' value='" . $value['vendor_id'] . "'";
                    $option .= " > ";
                    $option .= $value['name'] . " - (Micro Warehouse) </option>";
                    $option .= $value['name'] . " " . $value['district'] . " ( <strong>" . $value['state'] . "</strong>)" . "</option>";
                }
            }
        }
        

        echo $option;
    }

    function upload_signature_file() {
        //Start Processing signature File Upload
        if (($_FILES['signature_file']['error'] != 4) && !empty($_FILES['signature_file']['tmp_name'])) {
            //Adding file validation
            //$checkfilevalidation = $this->file_input_validation('signature_file');
            $checkfilevalidation = 1;
            if ($checkfilevalidation) {
                
                //Making process for file upload
                $tmpFile = $_FILES['signature_file']['tmp_name'];
                $signature_file = preg_replace('/\s+/', '', $this->input->post('name')) . '_signature_file_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['signature_file']['name'])[1];
                move_uploaded_file($tmpFile, TMP_FOLDER . $signature_file);

                //Upload files to AWS
                $bucket = BITBUCKET_DIRECTORY;
                $directory_xls = "vendor-partner-docs/" . $signature_file;
                $this->s3->putObjectFile(TMP_FOLDER . $signature_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                $_POST['signature_file'] = $signature_file;

                $attachment_signature = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $signature_file;
                unlink(TMP_FOLDER . $signature_file);

                //Logging success for file uppload
                log_message('info', __CLASS__ . ' signature file is being uploaded sucessfully.');
                return $attachment_signature;
            } else {
                //Redirect back to Form
                $data = $this->input->post();
                //Checking if form is for add or edit
                if (!empty($_POST['id'])) {
                    //Redirect to edit form for particular id
                    $this->editvendor($data['id']);
                } else {
                    //Redirect to add vendor form
                    $this->add_vendor();
                }
                return FALSE;
            }
        }
    }
          /*
           * This Function will return excel containing all pincode mapping combination for a vendor
           * @input - VendorID
           */
        function download_vendor_pin_code($vendorID) {
        log_message('info',__METHOD__. " Vendor ID ". $vendorID);
        //ob_start();
        $join = array("service_centres" => "service_centres.id = pm.Vendor_ID", "services" => "services.id=pm.Appliance_ID");
        $orderBYArray = array("services.services" => "ASC");
        $pincodeArray = $this->reusable_model->get_search_result_data("vendor_pincode_mapping pm", "pm.Pincode,service_centres.name as Vendor_Name,services.services as Appliance", array("pm.Vendor_ID" => $vendorID), $join, NULL, $orderBYArray, NULL, NULL, array('Appliance', 'pm.Pincode'));
        //$config = array('template' => "vendor_pin_code.xlsx", 'templateDir' => __DIR__ . "/../excel-templates/");
        log_message('info', __FUNCTION__ . ' Download Data ' . print_r($pincodeArray, TRUE));
        $template = 'vendor_pin_code.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
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
            'id' => 'order',
            'repeat' => TRUE,
            'data' => $pincodeArray
        ));

        $output_file_dir = TMP_FOLDER;
        $output_file = "SF-" . $vendorID . "-vendor_pincode_file-" . date('y-m-d');
        $output_file_name = $output_file . ".xlsx";
        $output_file_excel = $output_file_dir . $output_file_name;
        $R->render('excel', $output_file_excel);
         if(file_exists($output_file_excel)){

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"$output_file_name\""); 
                readfile($output_file_excel);
                exit;
            } 

       // echo json_encode(array("response" => "success", "path" => base_url() . "file_process/downloadFile/" . $output_file_name));
    }

    /*
          * This function will return the view to upload the vendor pin code mapping file
          */
         function upload_pin_code_vendor($vendorID){
                    $serviceArray = $this->reusable_model->get_search_result_data("services","services",array("isBookingActive"=>1),NULL,NULL,array("services"=>"ASC"),NULL,NULL,array());
                    $this->miscelleneous->load_nav_header();
                    $this->load->view('employee/vendor_pincode_upload',array('vendorID'=>$vendorID,"services"=>$serviceArray));
          }
          /*
           * This function will save the vendor upload pincode file in file uploads table
           */
          function save_vendor_pin_code_file($tempName,$vendorID){
                    $msg = FALSE;
                    $bucket = BITBUCKET_DIRECTORY;
                    $this->filePath = "vendor_pincode_mapping_".rand(10,100)."_".date('j-M-Y')."_".$vendorID.".xlsx";
                    $directory_xls = "vendor-partner-docs/".$this->filePath;
                    $is_success = $this->s3->putObjectFile($tempName, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    if($is_success){
                              $affected_rows =  $this->vendor_model->vendor_pin_code_uploads_insert("file_uploads",array("file_name"=>$this->filePath,"file_type"=>"vendor_pincode_".$vendorID,"agent_id"=>$this->session->userdata['id']));
                              if($affected_rows>0){
                                        $msg =  TRUE; 
                              }
                    }
                    return $msg;
          }
          /*
           *  This function will check is file extension xls
           * @output (TRUE OR FALSE)
           */
          function  is_file_extension_excel($fileName){
                    $msg = "Please upload only excel file";
                    $pathinfo = pathinfo($fileName);
                    if($pathinfo['extension'] == 'xls' || $pathinfo['extension'] == 'xlsx'){
                              $msg = TRUE;
                    }
                    return $msg;
          }
          /*
           * This function will check is uploded file less then 2 MB
           */
          function is_file_less_then_size($fileSize){
                    $msg = "File Size Should be less then 2 MB";
                    $MB = 1048576;
                    if ($fileSize > 0 && $fileSize < 2 * $MB) {
                              $msg = TRUE;
                    }
                    return $msg;
          }
          /*
           * Pass the file name to function and it will return file reader version for excel file
           */
          function get_excel_reader_version($fileName){
                    $pathinfo = pathinfo($fileName);
                    if ($pathinfo['extension'] == 'xlsx') {
                        $readerVersion = 'Excel2007';
                    } 
                    else {
                        $readerVersion = 'Excel5';
                    }
                    return $readerVersion;
          }
          /*
           * This function checkes uploded vendor mapping pincode file must have only 1 vendor 
           */
          function is_file_contains_only_valid_service(){
              $uniqueServicesArray = array();
              foreach ($this->vendorPinArray as $index=>$data){
                  $uniqueServicesArray[$data['appliance']][] = $index;  
              }
             $serviceTableData = array();
             $serviceTableData =  $this->reusable_model->get_search_result_data("services","id,services",NULL,NULL,NULL,NULL,array("services"=>array_keys($uniqueServicesArray)),NULL);
             $existServices =array();
             foreach($serviceTableData as $serviceData){
                    $this->existServices[$serviceData['services']] = $serviceData['id'] ; 
             }
              $wrongServiceArray = array_diff(array_keys($uniqueServicesArray),array_keys($this->existServices));
              if($wrongServiceArray){
               foreach($wrongServiceArray as $wrongService){
                   $msg[]= implode(",",$uniqueServicesArray[$wrongService]);
               }
               $msg = implode(",",$msg)." Lines Contains Wrong Appliance,Please Select Appliance only from appliance list Or Check the Spelling</br>";
               return $msg;
            }
            else{
                return TRUE;
            }
          }
          /*
           * This function checks is Uploded file blank
           */
          function is_uploded_file_blank($excelDataArray){
                    $msg = TRUE;
                    if(empty($excelDataArray)){
                              $msg = "File Does'nt Contains Any Record";
                    }
                   return $msg;
          }
          /*
           * This function checks pincode must be 6 digit and it should be numaric
           */
          function is_pin_code_valid($index,$data){
                    $msg = TRUE;
                            if(strlen($data['pincode']) != 6){
                                      $msg = "File Contains invalid pincode. Error at line ".($index+2);
                            }
                            else{
                                if(!is_numeric($data['pincode'])){
                                    $msg = "File Contains invalid pincode. Error at line ".($index+2);
                                }
                            }
                   return $msg;
          }
          /*
           * This function checks area_brand_pincode_serviceID Combination must be unique in uploded vendor pincode mapping file
           */
          function delete_duplicate_pincode_service(){
              $msg = true;
              $tempArray = array();
              $excelDataArray = $this->vendorPinArray;
              foreach($excelDataArray as $index=>$data){
                    $uniqueString = $data['pincode'].",".$data['appliance'];
                    if(array_key_exists($uniqueString, $tempArray)){
                        unset($excelDataArray[$index]);
                    }
                    else{
                        $tempArray[$uniqueString] = NULL;
                    }
             }
             $this->vendorPinArray = array_values($excelDataArray);
          }
          /*
           * This function checks is there any blank field in uploded excel file
           */
          function is_any_field_blank($index,$data){
                    $msg = true;
                              foreach($data as $value){
                                        if(!$value){
                                            $msg = "File Contains Blank Values. Error at line ".($index+2);
                                        }
                              }
                    return $msg;
          }
          /*
           * This function is used to check is uploded file (Vendor pincode mapping) valid or not
           */
          
          function  is_vendor_pin_code_file_valid($file,$vendorID){
                   $msg['extension']=$this->is_file_extension_excel($file['file']['name']);
                   if($msg['extension'] == 1){
                              $msg['size'] = $this->is_file_less_then_size($file['file']['size']);
                                        if($msg['size'] == 1){
                                                  $readerVersion = $this->get_excel_reader_version($file['file']['name']);
                                                  $this->vendorPinArray =  $this->miscelleneous->excel_to_Array_converter($file,$readerVersion);
                                                  $msg['blank'] = $this->is_uploded_file_blank($this->vendorPinArray);
                                                  if($msg['blank'] == 1){
                                                      $msg['valid_service'] = $this->is_file_contains_only_valid_service(); 
                                                      if($msg['valid_service'] == 1){
                                                            foreach($this->vendorPinArray as $index=>$data){
                                                                                $msg['pin_code'] = $this->is_pin_code_valid($index,$data);
                                                                                if($msg['pin_code'] == 1){
                                                                                          $msg['field_blank'] = $this->is_any_field_blank($index,$data);
                                                                                          if($msg['field_blank'] != 1 ){
                                                                                                    return $msg['field_blank']; 
                                                                                          }
                                                                                }
                                                                                else{
                                                                                          return $msg['pin_code'];
                                                                                }
                                                            }
                                                            $this->delete_duplicate_pincode_service();
                                                      }
                                                      else{
                                                          return $msg['valid_service'];
                                                      }
                                                  }
                                                  else{
                                                             return $msg['blank'];
                                                  }
                                        }
                                        else{
                                                  return $msg['size'];
                                        }
                   }
                   else{
                              return $msg['extension'];
                   }
                   return TRUE;
          }
          function send_push_notification_if_new_pincode_added($vendorID){
              $currentPincodeCount = $this->reusable_model->get_search_result_data('vendor_pincode_mapping','COUNT(DISTINCT pincode) as current_count ',array('Vendor_ID'=>$vendorID),NULL,NULL,NULL,NULL
                      ,NULL,array());
              $oldPincodeCount = $currentPincodeCount[0]['current_count'];
              $newPincodesCount = count(array_unique(array_column($this->vendorPinArray, 'pincode')));
              if($newPincodesCount>$oldPincodeCount){
                  $newPincodes = $newPincodesCount - $oldPincodeCount;
                  $notificationTextArray['title'] = array((string)$newPincodes);
                  // Send Push Notification to vendors and partners
                  $this->push_notification_lib->create_and_send_push_notiifcation(NEW_PINCODE_ADDED,array(),$notificationTextArray);
              }
          }
          /*
           * This function is used to Update vendor pincode mapping table on the basis of uploded excel
           */
          function update_vendor_pin_code_file($vendorID){
                    $this->send_push_notification_if_new_pincode_added($vendorID);
                    $deleteMsg = $this->vendor_model->delete_vendor_pin_codes(array('Vendor_ID'=>$vendorID));
                    $pincodeArray = array_unique(array_column($this->vendorPinArray, 'pincode'));
                    $pincodeData = $this->reusable_model->get_search_result_data("india_pincode","pincode,district,state",NULL,NULL,NULL,NULL,array('pincode'=>$pincodeArray),NULL,array('pincode','district'));
                    foreach($pincodeData as $pinData){
                        $pincodeDataArray[$pinData['pincode']][] = array("city"=>$pinData['district'],"state"=>$pinData['state']);
                    }
                    $this->notFoundCityStateArray = array_values(array_diff($pincodeArray,array_keys($pincodeDataArray)));
                    if($deleteMsg == TRUE){
                              $finalInsertArray = array();
                              foreach($this->vendorPinArray as $key=>$data){
                                        $insertArray['Vendor_ID'] = $vendorID;
                                        $insertArray['Appliance_ID'] = $this->existServices[$data['appliance']];
                                        $insertArray['Pincode'] = $data['pincode'];
                                        $insertArray['City']  = NULL;
                                        $insertArray['State']  = NULL;
                                        if(array_key_exists($data['pincode'], $pincodeDataArray)){
                                            foreach($pincodeDataArray[$data['pincode']] as $pin_codes_data){
                                                $insertArray['City']  = $pin_codes_data['city'];
                                                $insertArray['State']  = $pin_codes_data['state'];
                                        $finalInsertArray[] = $insertArray;
                              }
                                        } 
                                        else{
                                            //$finalInsertArray[] = $insertArray;
                                        }
                              }
                              if(!empty($finalInsertArray)){
                                       $affectedRows =  $this->vendor_model->insert_vendor_pincode_in_bulk($finalInsertArray);
                                       if($affectedRows>0){
                                                  return "Successfully Done";
                                       }
                                       else{
                                                  return "Something Went Wrong Please Contact to admin";
                                       }
                              }
                    }
                    else{
                        return $deleteMsg;
                    }
          }
          /*
           * Update india pincode table if any updated  pincode exist in not found sf table
           * It will change the flag for all bookings which has the uploded pincode
           */
          
          function manage_pincode_not_found_sf_table(){
              foreach($this->vendorPinArray as $key=>$values){
                        $temp['Pincode'] = $values['pincode'];
                        $temp['Appliance_ID'] = $this->existServices[$values['appliance']];
                        $pincodeServiceArray[] = $temp;
              }
              $this->miscelleneous->update_pincode_not_found_sf_table($pincodeServiceArray);
          }
          /*
           * This function update vendor pincode mapping table,before updating it checks following condition
           * Is file excel?,Is pincode valid,file must contains the data only for 1 vendor
           * @input - pincode excel file and vendor ID
           */
          function process_upload_pin_code_vendor(){
                      if(!empty($_FILES['file']['tmp_name'])){  
                              $tempName = $_FILES['file']['tmp_name'];
                              $vendorID = $this->input->post('vendorID');
                              $is_saved = $this->save_vendor_pin_code_file($tempName,$vendorID);
                              if($is_saved == 1){
                                        $msgVerfied = $this->is_vendor_pin_code_file_valid($_FILES,$vendorID);
                                        if($msgVerfied == 1){
                                                       $fileStatus = 'Failure';
                                                       $this->manage_pincode_not_found_sf_table();
                                                       $finalMsg = $updateMsg = $this->update_vendor_pin_code_file($vendorID);
                                                       log_message('info', __FUNCTION__ . ' Uploaded Data ' . print_r($this->vendorPinArray, TRUE));
                                                       if($finalMsg == 'Successfully Done'){
                                                           $fileStatus = 'Success';
                                                       }
                                        }
                                        else{
                                                  $fileStatus = 'Invalid';
                                                  $finalMsg =  $msgVerfied;
                                        }
                              }
                              else{
                                        $finalMsg =  $is_saved;
                              }
                    }
                    else{
                        
                    }
                    $this->vendor_model->update_file_status($fileStatus,$this->filePath); 
                    if(!empty($this->notFoundCityStateArray)){
                        $this->add_multiple_entry_in_india_pincode($this->notFoundCityStateArray);
                    }
                    else{
                        $msg['final_msg'] = $finalMsg;
                        $this->session->set_userdata($msg);
                        redirect(base_url()."employee/vendor/upload_pin_code_vendor/".$vendorID);
                    }
          }
          /*
           * This function use to create pincode update form
           * @input - post data pincode and service related to that pincode in json format
           * @output - it will create the pincode form view and if pincode is not found in india pincode then it will automatically redirect to add pincode form
           */
          function insert_pincode_form(){
            $data = $this->input->post();
            $pincodeAvaliablity = $this->is_pincode_available_in_india_pincode_table($data['pincode']);
                    if($pincodeAvaliablity){
                            if(!empty(json_decode($data['service']))){
                                   $data['selected_appliance'] = json_decode($data['service'],TRUE);
                            }
                            $data['all_appliance'] = $this->booking_model->selectservice();
                            $data['vendors'] = $this->booking_model->get_advance_search_result_data('service_centres','id as Vendor_ID,name as Vendor_Name',array('active'=>1));
                            $this->miscelleneous->load_nav_header();
                            $this->load->view('employee/add_vendor_to_pincode',$data);
                    }
          }
          
          /*
           * This function will check is_pincode available in india pincode
           * If yes then it will redirect the pincode to add pincode in india pincode table form
           * if not then it will return false
           */
          function is_pincode_available_in_india_pincode_table($pincode=''){
              $stateArray = array();
              if(!empty($pincode)){
                  $state  =   $this->vendor_model->get_state_from_india_pincode($pincode);
                  $stateArray = $state['state'];
              }
                  if(empty($stateArray)){
                     $states  =   $this->vendor_model->get_allstates();
                     $city  =   $this->vendor_model->getDistrict_from_india_pincode();
                     $this->miscelleneous->load_nav_header();
                     $this->load->view('employee/add_new_pincode',array('pincode'=>$pincode,'states'=>$states,'city'=>$city));
                     return false;
                  }
                  else{
                      return true;
                  }
          }
          /*
           * This Function used to add new pincode in India Pincode Table
           * will get pincode, state, city from post
           * save district, taluk,region,division,area value as city value in dataBase table
           */
          function add_new_pincode(){
               $data = $this->input->post();
               $pincode = $data['pincode'];
               $state = $data['states'];
               $cityArray = $data['city'];
               $length = count($cityArray);
               for($i=0;$i<$length;$i++){
                    $tempArray['district'] = $cityArray[$i];
                    $tempArray['state'] = $state;
                    $tempArray['pincode'] = $pincode;
                    $insertArray[] = $tempArray;
               }
               $insertResult = $this->vendor_model->insert_india_pincode_in_batch($insertArray);
               if($insertResult){
//                   $finalMsg = $insertResult." Rows has been inserted for the pincode ".$pincode." , Now You can assign SF to ".$pincode;
//                   $this->session->set_userdata('pincode_msg',$finalMsg);
//                   redirect(base_url() . 'employee/booking/view_queries/FollowUp/p_nav');
                   
                   $_POST['pincode'] = $pincode;
                   $_POST['service'] = '';
                   $this->insert_pincode_form();
               }
          }  
    function save_file_into_database($newZipFileName, $csv, $status) {
        //Adding Details in File_Uploads table as well
        $data_uploads['file_name'] = "vendor_pincode_mapping_temp_" . date('j-M-Y') . ".zip";
        $data_uploads['file_type'] = _247AROUND_VENDOR_PINCODE;
        $data_uploads['agent_id'] = $this->session->userdata('id');
        $data_uploads['result'] = $status;
        $insert_id = $this->partner_model->add_file_upload_details($data_uploads);
        if (!empty($insert_id)) {
            //Logging success
            log_message('info', __FUNCTION__ . ' Added details to File Uploads ' . print_r($data_uploads, TRUE));
        } else {
            //Loggin Error
            log_message('info', __FUNCTION__ . ' Error in adding details to File Uploads ' . print_r($data_uploads, TRUE));
        }


        //Upload files to AWS
        $bucket = BITBUCKET_DIRECTORY;
        $directory_xls = "vendor-partner-docs/" . "vendor_pincode_mapping_temp_" . date('j-M-Y') . ".zip";
        $this->s3->putObjectFile($newZipFileName, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
        //Logging
        log_message('info', __FUNCTION__ . ' Vendor Pincode Zipped File has been uploaded in S3');

        //remove file from system
        unlink($csv);
        unlink($newZipFileName);
    }
    
    /**
     * @desc : This function is used to resend the login details on request.
     * @param : $type employee/SF/partner string
     * @param : $id employee_id/sf_id/partner_id string
     * @return : void
     */
    function resend_login_details($type, $id) {
        $agent = $this->service_centers_model->get_sc_login_details_by_id($id);
        if(!empty($agent)){
            $template = $this->booking_model->get_booking_email_template("resend_login_details");
            if (!empty($template)) {
                $sf_details = $this->vendor_model->getVendorDetails('primary_contact_email,owner_email,name',array('id'=>$id));
                $rm_email = $this->vendor_model->get_rm_sf_relation_by_sf_id($id)[0]['official_email'];
                $login_details['username'] = $agent[0]['user_name'];
                $login_details['password'] = $agent[0]['user_name'];
                $subject = vsprintf($template[4], $sf_details[0]['name']);
                $emailBody = vsprintf($template[0], $login_details);
                $to = $this->session->userdata('official_email').",".$sf_details[0]['primary_contact_email'].",".$sf_details[0]['owner_email'];
                $cc = $rm_email.",".$template[3];
                $this->notify->sendEmail($template[2],$to , $cc, '', $subject, $emailBody, "",'resend_login_details');
                $this->session->set_userdata('success','Login Details Send To Registered Email Id');
                redirect(base_url() . 'employee/vendor/viewvendor'); 
            }else{
                $this->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, '','', 'Email Template Not Found', 'resend_login_details email template not found. Please update this into the database.', "",'resend_login_details');
                $this->session->set_userdata('error','Error!!! Please Try Again...');
                redirect(base_url() . 'employee/vendor/viewvendor');  
            }
        }else{
            echo "Service Center Not Found";
        }
        
    }

    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
         if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }
    
    function pending_bookings_on_vendor($vendorID){
         $count = $this->reusable_model->get_search_result_count("booking_details","booking_id",array('assigned_vendor_id'=>$vendorID),NULL,NULL,NULL,
                 array("current_status"=>array(_247AROUND_RESCHEDULED,_247AROUND_PENDING)),NULL );
         echo $count;
    }
    
    /**
     * @desc: This function is used to show the bank details
     * @param: void
     * @return: void
     */
    function show_bank_details(){
        $this->checkUserSession();
        //check if request is from ajax call or direct url
        // for ajax request echo reponse else echo data with header
        if($this->input->post()){
            if($this->input->post('sf_type') === '1'){
                $where = array('entity_type' => 'SF','service_centres.active' => 1,'account_holders_bank_details.is_verified' => $this->input->post('is_bank_details_verified'));
            }else if($this->input->post('sf_type') === '0'){
                $where = array('entity_type' => 'SF','service_centres.active' => 0,'account_holders_bank_details.is_verified' => $this->input->post('is_bank_details_verified'));
            }else if($this->input->post('sf_type') === 'all'){
                $where = array('entity_type' => 'SF','account_holders_bank_details.is_verified' => $this->input->post('is_bank_details_verified'));
            }
            $data['is_ajax'] = TRUE;
        }else{
            $where = array('entity_type' => 'SF','service_centres.active' => 1,'account_holders_bank_details.is_verified' => 0,'account_holders_bank_details.is_rejected'=>0);
            $data['is_ajax'] = FALSE;
            $data['rm_details'] = $this->employee_model->get_rm_details();
            
        }
        
        //get bank details
        $join = array('service_centres' => 'account_holders_bank_details.entity_id = service_centres.id AND account_holders_bank_details.is_active = 1');
        $data['bank_details'] = $this->reusable_model->get_search_query('account_holders_bank_details', 'account_holders_bank_details.*,service_centres.name,service_centres.primary_contact_email,service_centres.owner_email', $where, $join, NULL, NULL, NULL, NULL)->result_array();
        
        //get rm details
        if (!empty($data['bank_details'])) {
            foreach ($data['bank_details'] as $key => $value) {
                $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($value['entity_id']);
                $data['bank_details'][$key]['rm_name'] = !empty($rm) ? $rm[0]['full_name'] : '';
                $data['bank_details'][$key]['rm_email'] = !empty($rm) ? $rm[0]['official_email'] : '';
            }
        }else{
            $data['bank_details'] = array();
        }



        


        //exit;
        
        //output data
        if($data['is_ajax']){
            echo $this->load->view('employee/show_bank_details', $data);
        }else{
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/show_bank_details', $data);
        }
        
    }
    
    /**
     * @desc: This function is used to verify the bank details
     * @param: void
     * @return: string response
     */
    function verify_bank_details(){
        $entity_id = $this->input->post('id');
        $entity_type = $this->input->post('type');
        $action = $this->input->post('action');
        
        if($action == 'approve'){
            $update_data = array('is_verified'=> 1,'agent_id' => $this->session->userdata('id'));
        }else if($action == 'reject'){
            $update_data = array('is_rejected'=> 1,'agent_id' => $this->session->userdata('id'));
        }
        
        $update = $this->reusable_model->update_table('account_holders_bank_details',$update_data,array('entity_id' => $entity_id,'entity_type' => $entity_type,'is_active'=>1));
        
        if(!empty($update)){
            //send email to sf owner,poc and rm
            if($action == 'reject'){
                $rm_email = $this->input->post('rm_email');
                $poc_email = $this->input->post('poc_email');
                $owner_email = $this->input->post('owner_email');
                //send email to sf and rm
                $template = $this->booking_model->get_booking_email_template("bank_details_verification_email");
                if (!empty($template) && (!empty($poc_email) || !empty($owner_email))) {
                    $to = $poc_email.','.$owner_email;
                    //From will be currently logged in user's official Email
                    $from = $rm_email;
                    $emailBody = $template[0];
                    $subject['sf_name'] = $this->input->post('sf_name');
                    $subjectBody = vsprintf($template[4], $subject);
                    $this->notify->sendEmail($from, $to, $this->session->userdata('official_email') . ",".$template[3].','.$rm_email , '', $subjectBody, $emailBody, "",'bank_details_verification_email');
                }
            }
            echo "success";
        }else{
            echo "fail";
        }
    }
       function get_partner_vendor_updation_history_view($entityID,$orignalTable,$triggerTable){
        $data = $this->miscelleneous->table_updated_history_view($orignalTable,$triggerTable,$entityID);
       $table = '<table class="table table-striped table-bordered table-responsive">
    <thead><tr>
        <th>S.N</th>
        <th>Action Performed On</th>
        <th>Action Performed By</th>
        <th>Date</th>
      </tr></thead>
    <tbody>';
       if(!empty($data)){
       foreach($data['data'] as $index=>$updatedData){
      $table .= '<tr>
        <td>'.($index+1).'</td>
        <td>'.implode(",</br>",$updatedData).'</td>
        <td>'.$data['updated_by'][$index].'</td>
        <td>'.$data['update_date'][$index].'</td>
      </tr>'; }}
   echo $table .= '</tbody></table>';
    }
    function show_escalation_graph_by_sf($sfID,$startDate,$endDate){
            if($this->session->userdata('userType') == 'employee'){
        $this->miscelleneous->load_nav_header();
        }
        else if($this->session->userdata('userType') == 'partner'){
            $this->miscelleneous->load_partner_nav_header();
        }
        $this->load->view('employee/sf_escalation_view', array('data' => array("vendor_id"=>$sfID,"startDate"=>$startDate,"endDate"=>$endDate)));
    }
    function getServicesForVendor($vendorID){
        $appliance  = $this->reusable_model->get_search_result_data("vendor_pincode_mapping","CONCAT(vendor_pincode_mapping.Appliance_ID,'__',services.services) as service",
        array("Vendor_ID"=>$vendorID),array("services"=>"services.id=vendor_pincode_mapping.Appliance_ID"),NULL,NULL,NULL,NULL,array("Appliance_ID"));
        echo json_encode($appliance);
        }
        /*
         * This Function is used to open multiple pincode form, (Pincode which are not available in india pincode and someone try to add those in vendor pincode table)
         */
        function add_multiple_entry_in_india_pincode($pincodeArray){
            $states  =   $this->vendor_model->get_allstates();
            $city  =   $this->vendor_model->getDistrict_from_india_pincode();
            $this->miscelleneous->load_nav_header();
            $this->load->view('employee/add_multiple_new_pincode',array('pincodeArray'=>$pincodeArray,'states'=>$states,'city'=>$city));
        }
        /*
         * This is a helper function for  add multiple pincode this is used to update city and state for pincode which was not available in india pincode table intially
         */
        private function update_vendor_pincode_mapping_table($finalArray){
            $pincodesArray = array_column($finalArray, 'pincode');
            //get data from vendor_pincode_mapping where pincode in lisr
            $vendorMappingData = $this->reusable_model->get_search_result_data("vendor_pincode_mapping","*",NULL,NULL,NULL,NULL,array('pincode'=>$pincodesArray),NULL,array());
            foreach($finalArray as $pinData){
                $pincodeDataArray[$pinData['pincode']][] = array("city"=>$pinData['district'],"state"=>$pinData['state']);
            }
            foreach($vendorMappingData as $key=>$data){
                                        $idArray[] = $data['id'];
                                        $vendorID  = $data['Vendor_ID'];
                                        $insertArray['Vendor_ID'] = $data['Vendor_ID'];
                                        $insertArray['Appliance_ID'] = $data['Appliance_ID'];
                                        $insertArray['Pincode'] = $data['Pincode'];
                                        $insertArray['City']  = NULL;
                                        $insertArray['State']  = NULL;
                                        if(array_key_exists($data['Pincode'], $pincodeDataArray)){
                                            foreach($pincodeDataArray[$data['Pincode']] as $pin_codes_data){
                                                $insertArray['City']  = $pin_codes_data['city'];
                                                $insertArray['State']  = $pin_codes_data['state'];
                                                $finalInsertArray[] = $insertArray;
                                                }
                                        } 
                                        else{
                                            $finalInsertArray[] = $insertArray;
                                        }
                              }
                              if(!empty($idArray)){
                                    $deleteMsg = $this->vendor_model->delete_vendor_pin_codes_in_bulk($idArray);
                                    if($deleteMsg){
                                            $this->vendor_model->insert_vendor_pincode_in_bulk($finalInsertArray);
                                            $msg['final_msg'] = "Successfully Done";
                                            $this->session->set_userdata($msg);
                                            redirect(base_url()."employee/vendor/upload_pin_code_vendor/".$vendorID);
                                    }
                              }
            }
            /*
             * This function used to update multiple pincode data in india pincode table
             */
        function add_multiple_pincode(){
            $finalArray = [];
            $pincodeCount = $this->input->post('pincode_count');
            for($i=0;$i<$pincodeCount;$i++){
                $cityArray = $this->input->post('city_'.$i);
                foreach($cityArray as $city){
                $dataArray['district'] = $city;
                $dataArray['state'] = $this->input->post('states_'.$i);
                $dataArray['pincode'] = $this->input->post('pincode_'.$i);
                $finalArray[] = $dataArray; 
                }
            }
            $this->reusable_model->insert_batch("india_pincode",$finalArray);
            $this->update_vendor_pincode_mapping_table($finalArray);
        }
    
    /**
     * @desc This is used to load view to fetch challan ID
     */
    function fetch_challan_id(){
        $vendor = $this->vendor_model->getVendorDetails('id, name, sc_code');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/fetch_challan_id', array('service_center' => $vendor));
    }
    /**
     * @desc This is used to fetch challan id. It called from ajax. 
     * In this method, simply pass sc code of vendor then it return latest challan id
     * @param String $sc_code
     */
    function get_challan_id($sc_code){
        echo $this->miscelleneous->create_sf_challan_id($sc_code);
    }
    /**
     * @desc This is used to get misc charges data as table
     * @param String $booking_id
     */
    function get_miscellaneous_charges($booking_id, $is_sf = 0, $is_partner = 0){
       if(!empty($booking_id)){
            $data = $this->booking_model->get_misc_charges_data('*', array('booking_id' => $booking_id, 'active' => 1));
            $html = "<table class='table  table-striped table-bordered' ><thead><tr><th>Description</th>";
            if($is_partner){
               $html  .= "<th>Partner Offer</th>";
            }
            if($is_sf){
                $html  .= "<th>SF Earning</th>";
            }
            
            if($is_partner){
                $html .=  "<th>Partner Invoice Id</th>";
                $html .=  "<th>Approval File</th><th>Remarks</th>";
            }
            if($is_sf){
                $html  .= "<th>Vendor Invoice Id</th>";
            }
            
            
            if($this->session->userdata('userType') == 'employee'){ 
                $html .=  "<th>Action</th>";
            }
            $html .= "</tr></thead><tbody>";
           foreach ($data as $value) {
               $html .= "<tr>";
               $html .= '<td>'.$value['description'].'</td>';
               if($is_partner){
               $html .= '<td>'.$value['partner_charge'].'</td>';
               }
               if($is_sf){
                   $html .= '<td>'.($value['vendor_basic_charges'] + $value['vendor_tax']).'</td>';
               }
               
               if($is_partner){
                    $html .= '<td>'.$value['partner_invoice_id'].'</td>';
                    if(!empty($value['approval_file'])){
                        
                        $html .= '<td><a target="_blank" href="'.S3_WEBSITE_URL.'misc-images/'.$value['approval_file'].'" >Click Here</a></td>';
                    } else {
                        $html .= '<td></td>';
                    }
                }
               if($is_sf){
                   $html .= '<td>'.$value['vendor_invoice_id'].'</td>';
               }
               
               $html .= '<td>'.$value['remarks'].'</td>';
               if($this->session->userdata('userType') == 'employee'){
                   $b = "'$booking_id'";
               $html .= '<td><a target="_blank" style="color:#000; margin-left:10px; margin-right:10px;" href="'.base_url().'employee/service_centre_charges/update_misc_charges/'.$booking_id.'" class="glyphicon glyphicon-pencil"></a>';
               $html .= '<span style="color:#000;margin-left:10px; cursor:pointer; margin-right:10px;" onclick="removeMiscitem('.$value['id'].', '.$b.')" class="glyphicon glyphicon-remove"></span></td>';
               }
               
               $html .= "</tr>";
           }
           $html .= "</tbody></table>";
           echo $html;
       } else {
            echo "Failed";
       }
   }     
   function download_upcountry_report(){
        $this->checkUserSession();
        $upcountryCsv= "Upcountry_Report" . date('j-M-Y-H-i-s') . ".csv";
        $csv = TMP_FOLDER . $upcountryCsv;
        $report = $this->upcountry_model->get_upcountry_non_upcountry_district();
        $delimiter = ",";
        $newline = "\r\n";
        $new_report = $this->dbutil->csv_from_result($report, $delimiter, $newline);
        log_message('info', __FUNCTION__ . ' => Rendered CSV');
        write_file($csv, $new_report);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($csv) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);
        exec("rm -rf " . escapeshellarg($csv));
        log_message('info', __FUNCTION__ . ' Function End');
        //unlink($csv);
    }
    
    function check_GST_number($gst, $vendor_partner_id="", $vendor_partner=""){
        if($vendor_partner == 'partner'){ 
            $GST_number = $this->partner_model->getpartner_details('partners.id', array('gst_number'=>$gst, 'partners.id != "'.$vendor_partner_id.'"'=>null));
            if(empty($GST_number)){
                $api_response = $this->invoice_lib->taxpro_gstin_checking_curl_call($gst, $vendor_partner_id, $vendor_partner);
                if (!$api_response) {
                  echo '{"status_cd":"0","errorMsg":"Error occured while checking GST number try again"}';
                } else {
                  echo $api_response;
                }
            }
            else{
                echo '{"status_cd":"0","errorMsg":"GST Number Already Exist"}';
            }
        }
        else{
            $api_response = $this->invoice_lib->taxpro_gstin_checking_curl_call($gst, $vendor_partner_id, $vendor_partner);
            if (!$api_response) {
              echo '{"status_cd":"0","errorMsg":"Error occured while checking GST number try again"}';
            } else {
              echo $api_response;
            }
        }
    }
        function save_vendor_documents(){
            $this->checkUserSession();
            $vendor = [];
            $data = $this->input->post();
            $vendorArray = $this->reusable_model->get_search_result_data("service_centres", "name", array("id"=>$data['id']), NULL, NULL, NULL, NULL, NULL, array());
            $_POST['name'] = $vendorArray[0]['name'];
            //Start  Processing PAN File Upload
            if (($_FILES['pan_file']['error'] != 4) && !empty($_FILES['pan_file']['tmp_name'])) {
                //Adding file validation
                $checkfilevalidation = $this->file_input_validation('pan_file');
                if ($checkfilevalidation) {
                    //Cross-check if Non Availiable is checked along with file upload
                    if (isset($data['is_pan_doc'])) {
                        unset($_POST['is_pan_doc']);
                    }
                    //Making process for file upload
                    $tmpFile = $_FILES['pan_file']['tmp_name'];
                    $pan_file = implode("", explode(" ", $this->input->post('name'))) . '_panfile_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['pan_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$pan_file);

                    //Upload files to AWS   
                     $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/" . $pan_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$pan_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['pan_file'] = $pan_file;
                    
                    $attachment_pan = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$pan_file;
                    
                    //Logging success for file uppload
                    //log_message('info',__CLASS__.' PAN FILE is being uploaded sucessfully.');
                } else {
                    //Redirect back to Form

                    if (!empty($_POST['id'])) {
                        $this->editvendor($data['id']);
                    } else {
                        
                        $this->add_vendor();
                    }
                    return FALSE;
                }
            }
            if (($_FILES['address_proof_file']['error'] != 4) && !empty($_FILES['address_proof_file']['tmp_name'])) {
                //Adding file validation
                $checkfilevalidation = 1;
                if ($checkfilevalidation) {
                    //Making process for file upload
                    $tmpFile = $_FILES['address_proof_file']['tmp_name'];
                    $address_proof_file = implode("", explode(" ", $this->input->post('name'))) . '_address_proof_file_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['address_proof_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$address_proof_file);

                    //Upload files to AWS
                     $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/" . $address_proof_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$address_proof_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['address_proof_file'] = $address_proof_file;
                    
                    $attachment_address_proof = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$address_proof_file;
                    
                    //Logging success for file uppload
                    //log_message('info',__CLASS__.' PAN FILE is being uploaded sucessfully.');
                } else {
                    //Redirect back to Form

                    if (!empty($_POST['id'])) {
                        $this->editvendor($data['id']);
                    } else {
                        
                        $this->add_vendor();
                    }
                    return FALSE;
                }
            }
            if (($_FILES['contract_file']['error'] != 4) && !empty($_FILES['contract_file']['tmp_name'])) {
                //Adding file validation
                $checkfilevalidation = 1;
                if ($checkfilevalidation) {
                    //Making process for file upload
                    $tmpFile = $_FILES['contract_file']['tmp_name'];
                    $contract_file = implode("", explode(" ", $this->input->post('name'))) . '_contract_file_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['contract_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$contract_file);

                    //Upload files to AWS
                     $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/" . $contract_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$address_proof_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['contract_file'] = $contract_file;
                    
                    $attachment_contract_file = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$contract_file;
                    
                    //Logging success for file uppload
                    //log_message('info',__CLASS__.' PAN FILE is being uploaded sucessfully.');
                } else {
                    //Redirect back to Form

                    if (!empty($_POST['id'])) {
                        $this->editvendor($data['id']);
                    } else {
                        
                        $this->add_vendor();
                    }
                    return FALSE;
                }
            }
            if (($_FILES['gst_file']['error'] != 4) && !empty($_FILES['gst_file']['tmp_name'])) {
                $attachment_gst = $this->upload_gst_file($data);
                if($attachment_gst){} else {
                    return FALSE;
                }
            }        
            if (($_FILES['signature_file']['error'] != 4) && !empty($_FILES['signature_file']['tmp_name'])) {
                $attachment_signature = $this->upload_signature_file($data);
               // print_r($attachment_signature);
                if($attachment_signature){} else {
                    //return FALSE;
                }
            }
            if(!isset($_POST['is_pan_doc'])){
                $_POST['is_pan_doc'] = 1;
            }
            if(!isset($_POST['is_gst_doc'])){
                $_POST['is_gst_doc'] = 1;
            }
            $agentID = $this->session->userdata('id');
            if (!empty($this->input->post('id'))) {
                //if vendor exists, details are edited
                $vendor_data['is_pan_doc'] = $this->input->post('is_pan_doc');
                $vendor_data['is_gst_doc'] = $this->input->post('is_gst_doc');
                if(!empty($vendor_data['is_pan_doc']) && !empty($this->input->post('pan_no')) ){
                   $vendor_data['pan_no'] = $this->input->post('pan_no');
                   $vendor_data['name_on_pan'] = $this->input->post('name_on_pan');
                }else{
                    $vendor_data['pan_no'] = "";
                    $vendor_data['name_on_pan']= "";
                }
                 if(!empty($vendor_data['is_gst_doc']) && !empty($this->input->post('gst_no'))){
                    $vendor_data['gst_no'] = $this->input->post('gst_no');
                    $vendor_data['gst_taxpayer_type'] = $this->input->post('gst_type');
                    $vendor_data['gst_status'] = $this->input->post('gst_status');
                    $vendor_data['gst_cancelled_date'] = date("Y-m-d", strtotime($this->input->post('gst_cancelled_date')));
                }else{
                    $vendor_data['gst_no'] = NULL;
                    $vendor_data['gst_taxpayer_type'] = NULL;
                    $vendor_data['gst_status'] = NULL;
                    $vendor_data['gst_cancelled_date'] = NULL;
                }
                if(!empty($this->input->post('pan_file'))){
                    $vendor_data['pan_file'] = $this->input->post('pan_file');
                }  
                if(!empty($this->input->post('signature_file'))){
                    $vendor_data['signature_file'] = $this->input->post('signature_file');
                }  
                if(!empty($this->input->post('gst_file'))){
                     $vendor_data['gst_file'] = $this->input->post('gst_file');
                }
                if(!empty($this->input->post('address_proof_file'))){
                     $vendor_data['address_proof_file'] = $this->input->post('address_proof_file');
                }
                 if(!empty($this->input->post('contract_file'))){
                     $vendor_data['contract_file'] = $this->input->post('contract_file');
                }
                $vendor_data['agent_id'] = $agentID;
                $this->vendor_model->edit_vendor($vendor_data, $this->input->post('id'));
                $this->notify->insert_state_change('', NEW_SF_DOCUMENTS, NEW_SF_DOCUMENTS, 'Vendor ID : '.$this->input->post('id'), $this->session->userdata('id'), $this->session->userdata('employee_id'),
                        ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
                $this->session->set_flashdata('vendor_added', "Vendor Documents Has been updated Successfully , Please Fill other details");
                redirect(base_url() . 'employee/vendor/editvendor/'.$data['id']);
            } 
    }
       //save brand details 
    function save_vendor_brand_mapping(){
        $this->checkUserSession();
        $vendor = [];
        $agentID = $this->session->userdata('id');
        if (!empty($this->input->post('id'))) {
            if(!empty($this->input->post('appliances'))){
                $vendor_data['appliances'] = implode(",",$this->input->post('appliances'));
            }
            $vendor_data['agent_id'] = $agentID;
            $this->vendor_model->edit_vendor($vendor_data, $this->input->post('id'));
            $this->vendor_model->map_vendor_brands($this->input->post('id'), $this->input->post('brands'));
            $this->notify->insert_state_change('', NEW_SF_BRANDS, NEW_SF_BRANDS, 'Vendor ID : '.$this->input->post('id'), $this->session->userdata('id'), $this->session->userdata('employee_id'),
                        ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
            $this->session->set_flashdata('vendor_added', "Vendor Brands Has been updated Successfully , Please Fill other details");
            redirect(base_url() . 'employee/vendor/editvendor/'.$this->input->post('id'));
        }
    }
    function save_vendor_contact_person(){
        $this->checkUserSession();
        $data = $this->input->post();
        //Processing ID Proof 1 File Upload
        if(($_FILES['id_proof_1_file']['error'] != 4) && !empty($_FILES['id_proof_1_file']['tmp_name'])){
            $tmpFile = $_FILES['id_proof_1_file']['tmp_name'];
            $id_proof_1_file = implode("",explode(" ",$this->input->post('name'))).'_idproof1file_'.substr(md5(uniqid(rand(0,9))), 0, 15).".".explode(".",$_FILES['id_proof_1_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER.$id_proof_1_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/".$id_proof_1_file;
            $this->s3->putObjectFile(TMP_FOLDER.$id_proof_1_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $_POST['id_proof_1_file'] = $id_proof_1_file;

            $attachment_id_proof_1 = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$id_proof_1_file;

            //Logging success for file uppload
            log_message('info',__CLASS__.' ID PROOF 1 FILE is being uploaded sucessfully.');
        }
        //Processing ID Proof 1 File Upload
        if(($_FILES['id_proof_2_file']['error'] != 4) && !empty($_FILES['id_proof_2_file']['tmp_name'])){
            $tmpFile = $_FILES['id_proof_2_file']['tmp_name'];
            $id_proof_2_file = implode("",explode(" ",$this->input->post('name'))).'_idproof2file_'.substr(md5(uniqid(rand(0,9))), 0, 15).".".explode(".",$_FILES['id_proof_2_file']['name'])[1];
            move_uploaded_file($tmpFile, TMP_FOLDER.$id_proof_2_file);

            //Upload files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "vendor-partner-docs/".$id_proof_2_file;
            $this->s3->putObjectFile(TMP_FOLDER.$id_proof_2_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            $_POST['id_proof_2_file'] = $id_proof_2_file;

            $attachment_id_proof_2 = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$id_proof_2_file;

            //Logging success for file uppload
            log_message('info',__CLASS__.' ID PROOF 2 FILE is being uploaded sucessfully.');
        }               
        $agentID = $this->session->userdata('id');
        $vendor_data['agent_id'] = $agentID;
        $vendor_data['primary_contact_name'] = trim($this->input->post('primary_contact_name'));
        $vendor_data['primary_contact_email'] = trim($this->input->post('primary_contact_email'));
        $vendor_data['primary_contact_phone_1'] = trim($this->input->post('primary_contact_phone_1'));
        $vendor_data['primary_contact_phone_2'] = trim($this->input->post('primary_contact_phone_2'));
        $vendor_data['owner_name'] = trim($this->input->post('owner_name'));
        $vendor_data['owner_email'] = trim($this->input->post('owner_email'));
        $vendor_data['owner_phone_1'] = trim($this->input->post('owner_phone_1'));
        $vendor_data['owner_phone_2'] = trim($this->input->post('owner_phone_2'));
        //Get Rm Email
        $rm_id = $this->vendor_model->get_rm_sf_relation_by_sf_id($this->input->post('id'))[0]['agent_id'];
        $rm_email = $this->employee_model->getemployeefromid($rm_id)[0]['official_email'];
        //create vendor login details as well
        $new_vendor_mail = $this->input->post('owner_email').','.$this->input->post('primary_contact_email');
        if($this->input->post('already_send_notification') == 0){
        $this->create_vendor_login($new_vendor_mail,$rm_email);
        //Send Vendor creation notification to internals and vendor officials
            $notificationUrl = base_url() . "employee/do_background_process/send_vendor_creation_notification/";
            $postArray['rm_email'] = $rm_email;
            $postArray['owner_email'] = $this->input->post('owner_email');
            $postArray['primary_contact_phone_1'] = $this->input->post('primary_contact_phone_1');
            $postArray['primary_contact_email'] = $this->input->post('primary_contact_email');
            $postArray['name'] = $this->input->post('name');
            $postArray['owner_phone_1'] = $this->input->post('owner_phone_1');
            $postArray['owner_name'] = $this->input->post('owner_name');
            $postArray['company_name'] = $this->input->post('company_name');
            $postArray['district'] = $this->input->post('district');
            $postArray['id'] = $this->input->post('id');
            $this->asynchronous_lib->do_background_process($notificationUrl, $postArray);
        }
        if(!empty($this->input->post('id_proof_2_file'))){
            $vendor_data['id_proof_2_file'] = $this->input->post('id_proof_2_file');
        }  
        if(!empty($this->input->post('id_proof_1_file'))){
            $vendor_data['id_proof_1_file'] = $this->input->post('id_proof_1_file');
        }
        $this->notify->insert_state_change('', NEW_SF_CONTACTS, NEW_SF_CONTACTS, 'Vendor ID : '.$this->input->post('id'), $this->session->userdata('id'), $this->session->userdata('employee_id'), ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
        $this->session->set_flashdata('vendor_added', "Vendor Contacts Has been updated Successfully , Please Fill other details");
        $this->vendor_model->edit_vendor($vendor_data, $this->input->post('id'));
        redirect(base_url() . 'employee/vendor/editvendor/'.$data['id']);
    }
    function save_vendor_bank_details(){
         //Processing Cancelled Cheque File Upload
                if(($_FILES['cancelled_cheque_file']['error'] != 4) && !empty($_FILES['cancelled_cheque_file']['tmp_name'])){
                    $tmpFile = $_FILES['cancelled_cheque_file']['tmp_name'];
                    $cancelled_cheque_file = implode("",explode(" ",$this->input->post('name'))).'_cancelledchequefile_'.substr(md5(uniqid(rand(0,9))), 0, 15).".".explode(".",$_FILES['cancelled_cheque_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$cancelled_cheque_file);
                    
                    //Upload files to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/".$cancelled_cheque_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$cancelled_cheque_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['cancelled_cheque_file'] = $cancelled_cheque_file;
                    
                    echo $attachment_cancelled_cheque = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$cancelled_cheque_file;
                    
                    //Logging success for file uppload
                    log_message('info',__CLASS__.' CANCELLED CHEQUE FILE is being uploaded sucessfully.');
                }
                if (isset($_POST['is_verified']) && !empty($_POST['is_verified'])) {
                    $_POST['is_verified'] = '1';
                } else if (!isset($_POST['is_verified']) && $this->session->userdata('user_group') == 'admin') {
                    $_POST['is_verified'] = '0';
                }
                $bank_data['bank_name'] = trim($this->input->post('bank_name'));
                $bank_data['account_type'] = trim($this->input->post('account_type'));
                $bank_data['bank_account'] = trim($this->input->post('bank_account'));
                $bank_data['ifsc_code'] = trim($this->input->post('ifsc_code'));
                $bank_data['beneficiary_name'] = trim($this->input->post('beneficiary_name'));
                $bank_data['ifsc_code_api_response'] = trim($this->input->post('ifsc_validation'));
                $bank_data['is_verified'] = $this->input->post('is_verified');
                $bank_data['entity_id']= $this->input->post('id');
                $bank_data['entity_type'] = 'SF';
                $bank_data['agent_id'] = $this->session->userdata('id');
                $bank_data['cancelled_cheque_file']= $this->input->post('cancelled_cheque_file');
                $bank_data['is_rejected']= '0';
                $this->notify->insert_state_change('', NEW_SF_BANK_DETAILS, NEW_SF_BANK_DETAILS, 'Vendor ID : '.$this->input->post('id'), $this->session->userdata('id'), $this->session->userdata('employee_id'),
                        ACTOR_NOT_DEFINE,NEXT_ACTION_NOT_DEFINE,_247AROUND);
                $this->session->set_flashdata('vendor_added', "Vendor Bank Details Has been updated Successfully");
                $this->miscelleneous->update_insert_bank_account_details($bank_data,'update');
                redirect(base_url() . 'employee/vendor/editvendor/'.$this->input->post('id'));
    }
    function create_vendor_login($new_vendor_mail,$rm_email){
        $sc_login_uname = strtolower($this->input->post('sc_code'));
        $login['service_center_id'] = $this->input->post('id');
        $login['user_name'] = trim($sc_login_uname);
        $login['password'] = trim(md5($sc_login_uname));
        $login['active'] = 1;
        $login['full_name'] = $this->input->post('primary_contact_name');
        $this->vendor_model->add_vendor_login($login);
        // Sending Login details mail to Vendor using Template
        $login_email = array();
        //Getting template from Database
        $login_template = $this->booking_model->get_booking_email_template("vendor_login_details");
        if(!empty($login_template)){
        $login_email['username'] = $sc_login_uname;
        $login_email['password'] = $sc_login_uname;
        $login_subject = "Partner ERP URL and Login - 247around";
        $login_emailBody = vsprintf($login_template[0], $login_email);
        $this->notify->sendEmail($login_template[2], $new_vendor_mail ,  $login_template[3].",".$rm_email, '', $login_subject , $login_emailBody, "",'vendor_login_details');
        //Logging
        log_message('info', $login_subject." Email Send successfully" . $login_emailBody);
        }else{
            //Logging Error
            log_message('info', " Error in Getting Email Template for New Vendor Login credentials Mail");
            redirect(base_url() . 'employee/vendor/viewvendor');
        }
    }
    function send_vendor_creation_notification($new_vendor_mail,$rm_official_email){
        $this->sendWelcomeSms($this->input->post('primary_contact_phone_1'), $this->input->post('name'),$this->input->post('id'));
        $this->sendWelcomeSms($this->input->post('owner_phone_1'), $this->input->post('owner_name'),$this->input->post('id'));
        //Sending Welcome Vendor Mail
        //Getting template from Database
        $template = $this->booking_model->get_booking_email_template("new_vendor_creation");
        if (!empty($template)) {
            $subject = "Welcome to 247around ".$this->input->post('company_name')." (".$this->input->post('district').")";
            $emailBody = $template[0];
            $this->notify->sendEmail($template[2], $new_vendor_mail, $template[3].",".$rm_official_email, '', $subject, $emailBody, "",'new_vendor_creation');

            //Logging
            log_message('info', " Welcome Email Send successfully" . $emailBody);
        }else{
            //Logging Error Message
            log_message('info', " Error in Getting Email Template for New Vendor Welcome Mail");
        }
    }
    
    /**
    * @desc This is used to load email search form.
    * This form helps to search email in whole database
    */
    function seach_by_email(){
        $email_id = trim($this->input->post("email_id"));
        if(!empty($email_id)){ 
            $data['data'] = $this->vendor_model->search_email($email_id);
            $this->miscelleneous->load_nav_header();
            $this->load->view("employee/search_email_form", $data);
        } else {
            $this->miscelleneous->load_nav_header();
            $this->load->view("employee/search_email_form");
        }
    }
    
    /**
    * @desc This is used to load email search form.
    * This form helps to search email in whole database
    */
    function send_sms_to_poc(){
        $sms = array();
        $sms['phone_no'] = trim($this->input->post("phone_no"));
        $sms['smsData'] = $this->input->post("msg");
        $sms['tag'] = $this->input->post("sms_tag");
        $sms['status'] = "";
        $sms['booking_id'] = "";
        $sms['type'] = "vendor";
        $sms['type_id'] = $this->input->post("vendor_id");
        $this->notify->send_sms_msg91($sms);
    }
    
     /**
    * @desc This is used to load search GSTIN form.
    * This form helps to search GSTIN for vendor and partner if not found then by using api.
    */
    function seach_gst_number(){
        $api_response = "";
        $gstin = strtoupper(trim($this->input->post("gst_number")));
        if(!empty($gstin)){
            while(substr($gstin, -1) == ','){
                $gstin = rtrim($gstin,","); 
            }
            $gst =  explode(",",$gstin);
            $i = 0;
            foreach ($gst as $value) {
                $dbData = $this->vendor_model->search_gstn_number(trim($value));
                if(!empty($dbData)){
                    $data['data'][] = $dbData[0];
                }
                else{
                    $api_response = json_decode($this->invoice_lib->taxpro_gstin_checking_curl_call(trim($value)),true);
                    //$api_response = '{"stjCd":"UP530","lgnm":"NEERAJ RASTOGI","dty":"Regular","stj":"Ghaziabad Sector-4 , AC","adadr":[],"cxdt":"","gstin":"09ABJPR2848D1ZF","nba":["Service Provision","Office / Sale Office","Retail Business"],"lstupdt":"03/08/2018","ctb":"Proprietorship","rgdt":"01/07/2017","pradr":{"addr":{"bnm":"R.D.C","loc":"GHAZIABAD","st":"RAJ NAGAR","bno":"R-7/6","dst":"Ghaziabad","stcd":"Uttar Pradesh","city":"","flno":"","lt":"","pncd":"201002","lg":""},"ntr":"Service Provision, Office / Sale Office, Retail Business"},"tradeNam":"M/S SHIVAY ELECTRONICS","ctjCd":"YE0103","sts":"Active","ctj":"RANGE - 3"}';
                    //$api_response = '{"status_cd":"0","error":{"error_cd":"GSP050D","message":"Error while decrypting or decoding received data. Upstream Response: {\"url\":\"/\",\"message\":null,\"errorCode\":\"SWEB_9035\"}"}}';
                    //$api_response = json_decode($api_response, true);
                    if(!(isset($api_response['error']))){
                        $data['data'][$i]['lager_name'] = $api_response['lgnm'];
                        $data['data'][$i]['gst_number'] = $api_response['gstin'];
                        $data['data'][$i]['status'] = $api_response['sts'];
                        $data['data'][$i]['type'] = $api_response['dty'];
                        $data['data'][$i]['address'] = json_encode($api_response['pradr']);
                        $data['data'][$i]['company_name'] = $api_response['tradeNam'];
                        $data['data'][$i]['cancellation_date'] = $api_response['cxdt'];
                        $data['data'][$i]['nature_of_business'] = $api_response['ctb'];
                        $data['data'][$i]['create_date'] = date('Y-m-d H:i:s');
                        
                        $checkGSTDetail = $this->reusable_model->get_search_query("gstin_detail", 'id', array('gst_number'=>$api_response['gstin']), null, null, null, null, null, null)->result_array();
                        if(empty($checkGSTDetail)){ 
                            $this->reusable_model->insert_into_table("gstin_detail", $data['data'][$i]);
                        }
                        else{ 
                            $this->reusable_model->update_table("gstin_detail", $data['data'][$i], array('gst_number'=>$api_response['gstin']));
                        }
                        $data['data'][$i]['entity'] = "By API";
                    }
                    else{
                        $data['gst_not_found'] = $value;
                    }
                }
                $i++;
            }
            $this->miscelleneous->load_nav_header();
            $this->load->view("employee/search_gst_number", $data);
        } else {
            $this->miscelleneous->load_nav_header();
            $this->load->view("employee/search_gst_number");
        }
    }
    
    function send_broadcast_sms_to_vendors(){
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/send_broadcast_sms_to_vendors_form');
    }
    
    function process_broadcast_sms_to_vendors(){
        $select = "id";
        $vendor_owner = $this->input->post('vendor_owner');
        $venor_poc = $this->input->post('venor_poc');
        if($vendor_owner == 'on'){
            $select .= ",owner_phone_1";
        }
        if($venor_poc == 'on'){
            $select .= ",primary_contact_phone_1";
        }
        $where = array('active'=> 1);
        $vender_detail = $this->vendor_model->getVendorDetails($select, $where);
        foreach ($vender_detail as $key=>$value){
            $sms_number = "";
            $vendor_id = $value['id'];
            if(isset($value['owner_phone_1'])){
                $sms_number = $value['owner_phone_1'];
                $sms = array();
                $sms['phone_no'] = $sms_number;
                $sms['smsData'] = $this->input->post('mail_body');
                $sms['tag'] = BROADCAST_SMS_TO_VENDOR;
                $sms['status'] = "";
                $sms['booking_id'] = "";
                $sms['type'] = "vendor";
                $sms['type_id'] = $vendor_id;
                $this->notify->send_sms_msg91($sms);
            }
            if(isset($value['primary_contact_phone_1'])){
                $sms_number = $value['primary_contact_phone_1'];
                $sms = array();
                $sms['phone_no'] = $sms_number;
                $sms['smsData'] = $this->input->post('mail_body');
                $sms['tag'] = BROADCAST_SMS_TO_VENDOR;
                $sms['status'] = "";
                $sms['booking_id'] = "";
                $sms['type'] = "vendor";
                $sms['type_id'] = $vendor_id;
                $this->notify->send_sms_msg91($sms);
            }
        }
        
        $this->session->set_flashdata('success', "SMS Sent Successfully");
        redirect(base_url() . 'employee/vendor/send_broadcast_sms_to_vendors');
    }
    function get_city()
    {
       $state_value=$this->input->post('state'); 
       $city_arr=$this->vendor_model->get_city_bystate($state_value);
       $city_option='<option value="">Select City</option>';
       if(count($city_arr)>0)
       {
           foreach($city_arr as $value)
           {
           $city_option.='<option value="'.$value['city'].'">'.$value['city'].'</option>';
           }
       }
//       else
//       {
//           $city_option.='<option value="">No City Found For This State</option>';
//       }
      
       echo $city_option;
    }
    
    function validate_ifsc_code(){
        $ifsc_code = $this->input->post("ifsc_code");
        $entity_id = $this->input->post("entity_id");
        $entity_type = $this->input->post("entity_type");
        $api_response = $this->invoice_lib->validate_bank_ifsc_code($ifsc_code, $entity_type, $entity_id);
        echo $api_response;
    }
    
    
    function view_pincodes(){
         $this->miscelleneous->load_nav_header();
         $this->load->view("employee/show_indiapincode");
        
    }
    
        function getIndiaPincodes(){
        $data = $this->get_indiapincode_master_list_data();
        $post = $data['post'];
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->indiapincode_model->count_all_indiapincode_master_list($post),
            "recordsFiltered" =>  $this->indiapincode_model->count_filtered_indiapincode_master_list($post),
            "data" => $data['data'],
        );
        
        echo json_encode($output);
    }
    
    
    
        function get_indiapincode_master_list_data(){
        $post = $this->get_post_data();
        $post['column_order'] = array();
        $post['column_search'] = array('pincode','district','state','taluk','region','division','area');
        $select = "*";
        $list = $this->indiapincode_model->get_indiapincode_master_list($post,$select); 
       // $partners = array_column($this->partner_model->getpartner_details("partners.id,public_name",array('partners.is_active' => 1,'partners.is_wh' => 1)), 'public_name','id');
        $data = array();
        $no = $post['start'];
        foreach ($list as $stock_list) {
            $no++;
            $row = $this->get_indiapincode_master_list_table($stock_list, $no);
            $data[] = $row;
        }       
        return array(
            'data' => $data,
            'post' => $post
            
        );
    }
    
    
    
        function get_indiapincode_master_list_table($stock_list, $no){
        $row = array();           
        $json_data = json_encode($stock_list);      
        $row[] = $no;
        $row[] = $stock_list->pincode;
        $row[] = $stock_list->division;
        $row[] = $stock_list->area;
        $row[] = $stock_list->region;
        $row[] = "<span style='word-break: break-all;'>". $stock_list->taluk ."</span>";
        $row[] = "<span style='word-break: break-all;'>". $stock_list->district ."</span>";
        $row[] = $stock_list->state;                 
        $row[] = "<a href='javascript:void(0)' class ='btn btn-primary' id='edit_master_details' data-id='$json_data' title='Edit Details'><i class = 'fa fa-edit'></i></a>";           
        return $row;
    }
    
    
        private function get_post_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        return $post;
    }
    
    function process_updateIndiaPincode(){
         $id =trim($this->input->post('id'));
         $data=array(
            'area'=>trim($this->input->post('area')),
            'region'=>trim($this->input->post('region')),
            'pincode'=>trim($this->input->post('pincode')),
            'division'=>trim($this->input->post('division')),
            'taluk'=>trim($this->input->post('taluk')),
            'district'=>trim($this->input->post('district')),
            'state'=>trim($this->input->post('state'))
        );
        
        
        $check= $this->indiapincode_model->checkDuplicatePincode($data);
        if(!empty($check)){ 
            echo json_encode(array('response' =>247));
        }else{     
             $update=$this->indiapincode_model->updateIndiaPincode($data,$id);
             if($update){
                 echo json_encode(array('response' =>'success'));
             }else{
                 echo json_encode(array('response' =>'error'));
             }
        }
        
        
    }
    
    
    
        /**
     * @Desc: This function is used to get the service center for filtered brackets list
     * @param void
     * @return: string
     * 
     */
    function get_all_service_center_with_micro_wh() {

            $option = '<option selected="" value="" disabled="">Select Warehouse</option>';
            $select = "service_centres.district, service_centres.id,service_centres.state, service_centres.name";
            $where = array('is_wh' => 1, 'active' => 1);

            $data = $this->reusable_model->get_search_result_data("service_centres", $select, $where, NULL, NULL, NULL, array(), NULL, array());

            foreach ($data as $value) {
                $option .= "<option data-warehose='1' value='" . $value['id'] . "'";
                $option .= " > ";

                    $option .=  $value['name']. " " . $value['district'] . " ( <strong>" . $value['state'] . " </strong>) - (Central Warehouse)" . "</option>";
                    
            }
       
        echo $option;
    }

    function get_warehouse_data($id){
        $wh_details = $this->vendor_model->getVendorContact($id);
        echo json_encode($wh_details);
    }
    /*
     @Desc - This function is used to load view for download SF penalty summary
     */
    function penalty_summary(){
        $this->checkUserSession();
        $data = array();
        $where['is_sf'] = 1;
        $where['active'] = 1;
        $data['vendor'] = $this->vendor_model->getVendorDetails("service_centres.name, service_centres.id", $where);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/vendor_penalty_summary', $data);
    }
    
    /*
     @Desc - This function is used to download SF penalty summary csv
     */
    function download_vendor_penalty_summary(){
        $vendors = $this->input->post("service_center");
        $daterange = explode("-", $this->input->post("daterange"));
        $startDate = trim($daterange[0]);
        $endDate = trim($daterange[1]);
        $list = $this->vendor_model->sf_panalty_summary($vendors, $startDate, $endDate);
        $headings = array("Vendor Name", "Penalty Reason", "Total Bookings", "Total Penalties", "Total Penalty Amount");
        $this->miscelleneous->downloadCSV($list, $headings,"SF_penalty_summary");
    }
    
    function engineer_wise_calls() {
        
        $this->load->view('service_centers/header');
        $data['engineers'] = $this->reusable_model->get_search_result_data("engineer_details","engineer_details.id,name",[],NULL,NULL,array("name"=>"ASC"),NULL,array());
        $data['status'] = ['Pending', 'FollowUp', 'Completed', 'Rescheduled', 'Cancelled'];
        $this->load->view('service_centers/view_engineer_vise_calls',$data);
        
    }
    
    function get_engineer_vise_call_details() {
        $post = $this->get_post_data();
        if ($this->input->post('engineer_id')) {
            $post[''] = array();
          
            $list = $this->engineer_model->get_engineer_vise_call_list($this->input->post());
           
            $data = array();
            $no = $post['start'];
            foreach ($list as $call_list) {
                
                $no++;
                $row = $this->get_engineer_vise_calls_table($call_list, $no);
                $data[] = $row;
            }
            $post['length'] = -1;
            //$countlist = $this->inventory_model->get_inventory_stock_list($post, "sum(inventory_stocks.stock) as stock");


            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => count($list),
                "recordsFiltered" => count($list),
                'stock' => 0,
                "data" => $data,
            );
        } else {
            $output = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                'stock' => 0,
                "data" => array(),
            );
        }
        echo json_encode($output);

    }
    
    private function get_engineer_vise_calls_table($call_list, $sn) {
        $row = array();
        
        $row[] = $sn;
        $row[] = '<span>' . $call_list['booking_id'] . '</span>';
        $row[] = '<span>' . $call_list['username']."<br>".$call_list['booking_primary_contact_no'] . '</span>';
        $row[] = '<span>' . $call_list['booking_address'] . '</span>';
        $row[] = '<span><b>' . $call_list['request_type'] .'</b> '.$call_list['services'] . '</span>';
        $row[] = '<span>' . $call_list['booking_date'] . '</span>';
        $row[] = '<span>' . $call_list['age_of_booking'] . '</span>';
        $row[] = '<span>' . $call_list['partner_name'] . '</span>';
        $row[] = '<span>' . $call_list['appliance_brand'] . '</span>';
        $row[] = '<span>' . $call_list['count_escalation'] . '</span>';

        return $row;
    }
    
    function getRMs() {
        $data = $this->employee_model->get_state_wise_rm($this->input->post('state'));
        $rm_id = $this->input->post('rm_id');
        $option = '<option value="" disabled '.(empty($rm_id) && count($data) > 1 ? 'selected' : '').'>Select Regional Manager</option>';
        foreach ($data as $employee) {
            $option .= "<option value='{$employee['id']}' ".(!empty($rm_id) && $rm_id == $employee['id'] ? 'selected' : '').">{$employee['full_name']}</option>";
        }
        
        echo $option;
    }
    
    function get_brands() {
        $post_data = $this->input->post();
        $data['appliances'] = $post_data['appliance'];
        $service_center_id = $post_data['service_center_id'];
        if(!empty($service_center_id)) {
            $assigned_brands = $this->reusable_model->get_search_result_data('service_center_brand_mapping', '*', ['service_center_id' => $service_center_id], NULL, NULL, NULL, NULL, NULL);
            $sf_brands = [];
            foreach($assigned_brands as $assigned_brand) {
                $sf_brands[$assigned_brand['service_id']][] = (!empty($assigned_brand['brand_id']) ? $assigned_brand['brand_id'] : $assigned_brand['brand_name']);
            }
        }
        $data['sf_brands'] = $sf_brands;
        
        $brand_view =  $this->load->view('employee/appliance_brand', $data, true);
        echo $brand_view;exit;
    }
    
    function verify_engineer(){
        $where = array("id"=>$this->input->post("engineer_id"));
        $data = array("varified"=>$this->input->post("varified_status"));
        $this->vendor_model->update_engineer($where, $data);
        echo true;
    }

}
