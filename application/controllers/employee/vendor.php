<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 360000); //3600 seconds = 60 minutes

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once BASEPATH . 'libraries/spout-2.4.3/src/Spout/Autoloader/autoload.php';

class vendor extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        $this->load->model('employee_model');
        $this->load->model('booking_model');
        $this->load->library('PHPReport');
        $this->load->model('service_centers_model');
        $this->load->model('upcountry_model');
        $this->load->model('vendor_model');
        $this->load->model('service_centre_charges_model');
        $this->load->helper(array('form', 'url'));
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
        $this->load->helper('download');
        $this->load->library('user_agent');
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
        $vendor = [];
        //Getting rm id from post data
        $rm = $this->input->post('rm');
        //Now unset value of rm from POST data
        unset($_POST['rm']);
        
        $data = $this->input->post();
        if(!empty($data['id'])){
            $vendor = $this->vendor_model->getVendorContact($data['id']);
        }
        $checkValidation = $this->checkValidation();
        if ($checkValidation) {
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
            
            //Start Processing CST File Upload
            if (($_FILES['cst_file']['error'] != 4) && !empty($_FILES['cst_file']['tmp_name'])) {
                //Adding file validation
                $checkfilevalidation = $this->file_input_validation('cst_file');
                if ($checkfilevalidation) {
                    //Cross-check if Non Availiable is checked along with file upload
                    if (isset($data['is_cst_doc'])) {
                        unset($_POST['is_cst_doc']);
                    }
                    //Making process for file upload
                    $tmpFile = $_FILES['cst_file']['tmp_name'];
                    $cst_file = implode("", explode(" ", $this->input->post('name'))) . '_cstfile_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['cst_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$cst_file);

                    //Upload files to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/" . $cst_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$cst_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['cst_file'] = $cst_file;
                    
                    $attachment_cst = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$cst_file;
                    
                    //Logging success for file uppload
                    log_message('info',__CLASS__.' CST FILE is being uploaded sucessfully.');
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
            
            //Start Processing TIN File Upload
            if (($_FILES['tin_file']['error'] != 4) && !empty($_FILES['tin_file']['tmp_name'])) {
                //Adding file validation
                $checkfilevalidation = $this->file_input_validation('tin_file');
                if ($checkfilevalidation) {
                    //Cross-check if Non Availiable is checked along with file upload
                    if (isset($data['is_tin_doc'])) {
                        unset($_POST['is_tin_doc']);
                    }
                    $tmpFile = $_FILES['tin_file']['tmp_name'];
                    $tin_file = implode("", explode(" ", $this->input->post('name'))) . '_tinfile_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['tin_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$tin_file);

                    //Upload files to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/" . $tin_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$tin_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['tin_file'] = $tin_file;
                    
                    $attachment_tin = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$tin_file;
                    
                    //Logging success for file uppload
                    log_message('info',__CLASS__.' TIN FILE is being uploaded sucessfully.');
                } else {
                    //Redirect back to Form
                    $data = $this->input->post();
                    if (!empty($_POST['id'])) {
                        $this->editvendor($data['id']);
                    } else {
                        $this->add_vendor();
                    }
                    return FALSE;
                }
            }

            //Start Processing Service Tax File Upload
            if (($_FILES['service_tax_file']['error'] != 4) && !empty($_FILES['service_tax_file']['tmp_name'])) {
                //Adding file validation
                $checkfilevalidation = $this->file_input_validation('service_tax_file');
                if ($checkfilevalidation) {
                    //Cross-check if Non Availiable is checked along with file upload
                    if (isset($data['is_st_doc'])) {
                        unset($_POST['is_st_doc']);
                    }
                    $tmpFile = $_FILES['service_tax_file']['tmp_name'];
                    $service_tax_file = implode("", explode(" ", $this->input->post('name'))) . '_servicetaxfile_' . substr(md5(uniqid(rand(0, 9))), 0, 15) . "." . explode(".", $_FILES['service_tax_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$service_tax_file);

                    //Upload files to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/" . $service_tax_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$service_tax_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['service_tax_file'] = $service_tax_file;
                    
                    $attachment_service_tax = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$service_tax_file;
                    
                    //Logging success for file uppload
                    log_message('info',__CLASS__.' Serivce Tax FILE is being uploaded sucessfully.');
                } else {
                    //Redirect back to Form
                    $data = $this->input->post();
                    if (!empty($_POST['id'])) {
                        $this->editvendor($data['id']);
                    } else {
                        $this->add_vendor();
                    }
                    return FALSE;
                }
            }
            
            //Processing Address Proof File Upload
                if(($_FILES['address_proof_file']['error'] != 4) && !empty($_FILES['address_proof_file']['tmp_name'])){
                    $tmpFile = $_FILES['address_proof_file']['tmp_name'];
                    $address_proof_file = implode("",explode(" ",$this->input->post('name'))).'_addressprooffile_'.substr(md5(uniqid(rand(0,9))), 0, 15).".".explode(".",$_FILES['address_proof_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$address_proof_file);
                    
                    //Upload files to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/".$address_proof_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$address_proof_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['address_proof_file'] = $address_proof_file;
                    
                    $attachment_address_proof = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$address_proof_file;
                    
                    //Logging success for file uppload
                    log_message('info',__CLASS__.' ADDRESS PROOF FILE is being uploaded sucessfully.');
                }
                
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
                    
                    $attachment_cancelled_cheque = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$cancelled_cheque_file;
                    
                    //Logging success for file uppload
                    log_message('info',__CLASS__.' CANCELLED CHEQUE FILE is being uploaded sucessfully.');
                }
                
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
                
                //Processing Contract File Upload
                if(($_FILES['contract_file']['error'] != 4) && !empty($_FILES['contract_file']['tmp_name'])){
                    $tmpFile = $_FILES['contract_file']['tmp_name'];
                    $contract_file = implode("",explode(" ",$this->input->post('name'))).'_contractfile_'.substr(md5(uniqid(rand(0,9))), 0, 15).".".explode(".",$_FILES['contract_file']['name'])[1];
                    move_uploaded_file($tmpFile, TMP_FOLDER.$contract_file);
                    
                    //Upload files to AWS
                    $bucket = BITBUCKET_DIRECTORY;
                    $directory_xls = "vendor-partner-docs/".$contract_file;
                    $this->s3->putObjectFile(TMP_FOLDER.$contract_file, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
                    $_POST['contract_file'] = $contract_file;
                    
                    $attachment_contract = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$contract_file;
                    
                    //Logging success for file uppload
                    log_message('info',__CLASS__.' CONTRACT FILE is being uploaded sucessfully.');
                }
                
       
            
            $non_working_days = $this->input->post('day');
            $appliances = $this->input->post('appliances');
            $brands = $this->input->post('brands');

            if (!empty($non_working_days)) {
                $_POST['non_working_days'] = implode(",", $non_working_days);
            }

            if (!empty($appliances)) {
                $_POST['appliances'] = implode(",", $appliances);
            }

            if (!empty($brands)) {
                $_POST['brands'] = implode(",", $brands);
            }
            if(!isset($_POST['is_st_doc'])){
                $_POST['is_st_doc'] = 1;
            }
            if(!isset($_POST['is_tin_doc'])){
                $_POST['is_tin_doc'] = 1;
            }
            if(!isset($_POST['is_pan_doc'])){
                $_POST['is_pan_doc'] = 1;
            }
            if(!isset($_POST['is_cst_doc'])){
                $_POST['is_cst_doc'] = 1;
            }
            
            if(isset($_POST['is_verified'])){
               $_POST['is_verified'] = '1';
            }else if(!isset($_POST['is_verified']) && $this->session->userdata('user_group') == 'admin')
            {
                $_POST['is_verified'] = '0';
            }
            
            if (!empty($this->input->post('id'))) {
                
                //if vendor exists, details are edited
                $vendor_data = $this->get_vendor_form_data();
                
                $this->vendor_model->edit_vendor($vendor_data, $this->input->post('id'));
                
                 //Getting Logged Employee Full Name
                $logged_user_name = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['full_name'];
                
                //Log Message
                log_message('info', __FUNCTION__.' SF has been updated :'.print_r($vendor_data,TRUE));
                
                //Adding details in Booking State Change
                $this->notify->insert_state_change('', SF_UPDATED, SF_UPDATED, 'Vendor ID : '.$_POST['id'], $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
                
                //Sending Mail for Updated details
                    $html = "<p>Following SF has been Updated :</p><ul>";
                    foreach($vendor_data as $key=>$value){
                        $html .= "<li><b>".$key.'</b> =>';
                        $html .= " ".$value.'</li>';
                    }
                    $html .="</ul>";
                    $to = ANUJ_EMAIL_ID;
                    
                    //Cleaning Email Variables
                        $this->email->clear(TRUE);

                        //Send report via email
                        $this->email->from('booking@247around.com', '247around Team');
                        $this->email->to($to);

                        $this->email->subject("Vendor Updated : " . $_POST['name'].' - By '.$logged_user_name);
                        $this->email->message($html);
                        
                        if(isset($attachment_pan)){
                        $this->email->attach($attachment_pan, 'attachment');
                        }
                        if(isset($attachment_cst)){
                            $this->email->attach($attachment_cst, 'attachment');
                        }
                        if(isset($attachment_tin)){
                            $this->email->attach($attachment_tin, 'attachment');
                        }
                        if(isset($attachment_service_tax)){
                            $this->email->attach($attachment_service_tax, 'attachment');
                        }
                        if(isset($attachment_address_proof)){
                            $this->email->attach($attachment_address_proof, 'attachment');
                        }
                        if(isset($attachment_cancelled_cheque)){
                            $this->email->attach($attachment_cancelled_cheque, 'attachment');
                        }
                        if(isset($attachment_id_proof_1)){
                            $this->email->attach($attachment_id_proof_1, 'attachment');
                        }
                        if(isset($attachment_id_proof_2)){
                            $this->email->attach($attachment_id_proof_2, 'attachment');
                        }
                        if(isset($attachment_contract)){
                            $this->email->attach($attachment_contract, 'attachment');
                        }

                        if ($this->email->send()) {
                            log_message('info', __METHOD__ . ": Mail sent successfully to " . $to);
                        } else {
                            log_message('info', __METHOD__ . ": Mail could not be sent to " . $to);
                        }

                //Updating details of SF in employee_relation table
                $check_update_sf_rm_relation = $this->vendor_model->update_rm_to_sf_relation($rm, $_POST['id']);
                if($check_update_sf_rm_relation){
                    //Loggin Success
                    log_message('info', __FUNCTION__.' SF to RM relation is updated successfully RM = '.print_r($rm,TRUE).' SF = '.print_r($_POST['id'],TRUE));
                }else{
                    //Loggin Error 
                    log_message('info', __FUNCTION__.' Error in mapping SF to RM relation RM = '.print_r($rm,TRUE).' SF = '.print_r($_POST['id'],TRUE));
                }

                redirect(base_url() . 'employee/vendor/viewvendor');
            } else {
                // get service center code by calling generate_service_center_code() method
                $owner_email = $this->input->post('owner_email');
                $primary_contact_email = $this->input->post('primary_contact_email');
                $new_vendor_mail = $owner_email.','.$primary_contact_email;
                //Making Array to add Vendor
                $vendor_data = $this->get_vendor_form_data();
                
                $vendor_data['sc_code'] = $this->generate_service_center_code($_POST['name'], $_POST['district']);

                //if vendor do not exists, vendor is added
                $sc_id = $this->vendor_model->add_vendor($vendor_data);
                
                //Getting Logged Employee Full Name
                $logged_user_name = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['full_name'];
                //Getting RM Official Email details to send Welcome Mails to them as well
                $rm_official_email = $this->employee_model->getemployeefromid($rm)[0]['official_email'];
                
                //Logging
                log_message('info', __FUNCTION__.' SF has been Added :'.print_r($vendor_data,TRUE));
                
                //Adding details in Booking State Change
                $this->notify->insert_state_change('', NEW_SF_ADDED, NEW_SF_ADDED, 'Vendor ID : '.$sc_id, $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
                
                //Sending Mail for Added details
                    $html = "<p>Following SF has been Added :</p><ul>";
                    foreach($vendor_data as $key=>$value){
                        $html .= "<li><b>".$key.'</b> =>';
                        $html .= " ".$value.'</li>';
                    }
                    $html .="</ul>";
                    $to = ANUJ_EMAIL_ID;
                    
                    //Cleaning Email Variables
                        $this->email->clear(TRUE);

                        //Send report via email
                        $this->email->from('booking@247around.com', '247around Team');
                        $this->email->to($to);

                        $this->email->subject("Vendor Added : " . $this->input->post('name').' - By '.$logged_user_name);
                        $this->email->message($html);
                        
                        if(isset($attachment_pan)){
                        $this->email->attach($attachment_pan, 'attachment');
                        }
                        if(isset($attachment_cst)){
                            $this->email->attach($attachment_cst, 'attachment');
                        }
                        if(isset($attachment_tin)){
                            $this->email->attach($attachment_tin, 'attachment');
                        }
                        if(isset($attachment_service_tax)){
                            $this->email->attach($attachment_service_tax, 'attachment');
                        }
                        if(isset($attachment_address_proof)){
                            $this->email->attach($attachment_address_proof, 'attachment');
                        }
                        if(isset($attachment_cancelled_cheque)){
                            $this->email->attach($attachment_cancelled_cheque, 'attachment');
                        }
                        if(isset($attachment_id_proof_1)){
                            $this->email->attach($attachment_id_proof_1, 'attachment');
                        }
                        if(isset($attachment_id_proof_2)){
                            $this->email->attach($attachment_id_proof_2, 'attachment');
                        }
                        if(isset($attachment_contract)){
                            $this->email->attach($attachment_contract, 'attachment');
                        }

                        if ($this->email->send()) {
                            log_message('info', __METHOD__ . ": Mail sent successfully to " . $to);
                        } else {
                            log_message('info', __METHOD__ . ": Mail could not be sent to " . $to);
                        }

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
                $this->sendWelcomeSms($_POST['primary_contact_phone_1'], $_POST['name'],$sc_id);
                $this->sendWelcomeSms($_POST['owner_phone_1'], $_POST['owner_name'],$sc_id);
                
                //Sending Welcome Vendor Mail
                //Getting template from Database
                $template = $this->booking_model->get_booking_email_template("new_vendor_creation");
                if (!empty($template)) {
                    $subject = "Welcome to 247around ".$this->input->post('company_name')." (".$this->input->post('district').")";
                    $emailBody = $template[0];
                    $this->notify->sendEmail($template[2], $new_vendor_mail, $template[3].",".$rm_official_email, '', $subject, $emailBody, "");
                    
                    //Logging
                    log_message('info', " Welcome Email Send successfully" . $emailBody);
                }else{
                    //Logging Error Message
                    log_message('info', " Error in Getting Email Template for New Vendor Welcome Mail");
                }

		  //create vendor login details as well
		   $sc_login_uname = strtolower($vendor_data['sc_code']);
		   $login['service_center_id'] = $sc_id;
		   $login['user_name'] = $sc_login_uname;
		   $login['password'] = md5($sc_login_uname);
		   $login['active'] = 1;
                   $login['full_name'] = $this->input->post('primary_contact_name');

		   $this->vendor_model->add_vendor_login($login);
                   
                   $engineer['service_center_id'] =  $sc_id;
                   $engineer['name'] = "Default Engineer";
                   $this->vendor_model->insert_engineer($engineer);
                   
                // Sending Login details mail to Vendor using Template
                   
                   $login_email = array();
                   //Getting template from Database
                   $login_template = $this->booking_model->get_booking_email_template("vendor_login_details");
                   if(!empty($login_template)){
                   $login_email['username'] = $sc_login_uname;
                   $login_email['password'] = $sc_login_uname;
                   $login_subject = "Partner ERP URL and Login - 247around";
                   
                   $login_emailBody = vsprintf($login_template[0], $login_email);
                   
                   $this->notify->sendEmail($login_template[2], $new_vendor_mail ,  $login_template[3].",".$rm_official_email, '', $login_subject , $login_emailBody, "");
                   
                   //Logging
                   log_message('info', $login_subject." Email Send successfully" . $login_emailBody);
                    
                   }else{
                       //Logging Error
                       log_message('info', " Error in Getting Email Template for New Vendor Login credentials Mail");
                   }
		   redirect(base_url() . 'employee/vendor/viewvendor');
            }
        } else {
            $this->add_vendor();
        }
    }
    
    /**
     * @desc : This function is used to get the form data of vendor
     *
     * @param : void
     * @return : array()
     */
    
    function get_vendor_form_data(){

                $vendor_data['company_name'] = $this->input->post('company_name');
                $vendor_data['name'] = $this->input->post('name');
                $vendor_data['address'] = $this->input->post('address');
                $vendor_data['landmark'] = $this->input->post('landmark');
                $vendor_data['district'] = $this->input->post('district');
                $vendor_data['state'] = $this->input->post('state');
                $vendor_data['pincode'] = $this->input->post('pincode');
                $vendor_data['phone_1'] = $this->input->post('phone_1');
                $vendor_data['phone_2'] = $this->input->post('phone_2');
                $vendor_data['email'] = $this->input->post('email');
                $vendor_data['company_type'] = $this->input->post('company_type');
                $vendor_data['primary_contact_name'] = $this->input->post('primary_contact_name');
                $vendor_data['primary_contact_email'] = $this->input->post('primary_contact_email');
                $vendor_data['primary_contact_phone_1'] = $this->input->post('primary_contact_phone_1');
                $vendor_data['primary_contact_phone_2'] = $this->input->post('primary_contact_phone_2');
                $vendor_data['owner_name'] = $this->input->post('owner_name');
                $vendor_data['owner_email'] = $this->input->post('owner_email');
                $vendor_data['owner_phone_1'] = $this->input->post('owner_phone_1');
                $vendor_data['owner_phone_2'] = $this->input->post('owner_phone_2');
                $vendor_data['name_on_pan'] = $this->input->post('name_on_pan');
                if(!empty($this->input->post('pan_no')))
                    $vendor_data['pan_no'] = $this->input->post('pan_no');
                $vendor_data['is_pan_doc'] = $this->input->post('is_pan_doc');
                if(!empty($this->input->post('cst_no')))
                    $vendor_data['cst_no'] = $this->input->post('cst_no');
                $vendor_data['is_cst_doc'] = $this->input->post('is_cst_doc');
                if(!empty($this->input->post('tin_no')))
                    $vendor_data['tin_no'] = $this->input->post('tin_no');
                $vendor_data['is_tin_doc'] = $this->input->post('is_tin_doc');
                if(!empty($this->input->post('service_tax_no')))
                    $vendor_data['service_tax_no'] = $this->input->post('service_tax_no');
                $vendor_data['is_st_doc'] = $this->input->post('is_st_doc');
                $vendor_data['bank_name'] = $this->input->post('bank_name');
                $vendor_data['account_type'] = $this->input->post('account_type');
                $vendor_data['bank_account'] = $this->input->post('bank_account');
                $vendor_data['ifsc_code'] = $this->input->post('ifsc_code');
                $vendor_data['beneficiary_name'] = $this->input->post('beneficiary_name');
                $vendor_data['is_verified'] = $this->input->post('is_verified');
                if(!empty($this->input->post('contract_file')))
                    $vendor_data['contract_file'] = $this->input->post('contract_file');
                if(!empty($this->input->post('id_proof_2_file')))
                    $vendor_data['id_proof_2_file'] = $this->input->post('id_proof_2_file');
                if(!empty($this->input->post('id_proof_1_file')))
                    $vendor_data['id_proof_1_file'] = $this->input->post('id_proof_1_file');
                if(!empty($this->input->post('cancelled_cheque_file')))
                    $vendor_data['cancelled_cheque_file'] = $this->input->post('cancelled_cheque_file');
                if(!empty($this->input->post('address_proof_file')))
                    $vendor_data['address_proof_file'] = $this->input->post('address_proof_file');
                if(!empty($this->input->post('service_tax_file')))
                    $vendor_data['service_tax_file'] = $this->input->post('service_tax_file');
                if(!empty($this->input->post('tin_file')))
                    $vendor_data['tin_file'] = $this->input->post('tin_file');
                if(!empty($this->input->post('cst_file')))
                    $vendor_data['cst_file'] = $this->input->post('cst_file');
                if(!empty($this->input->post('pan_file')))
                    $vendor_data['pan_file'] = $this->input->post('pan_file');
                if(!empty($this->input->post('non_working_days')))
                    $vendor_data['non_working_days'] = $this->input->post('non_working_days');
                if(!empty($this->input->post('appliances')))
                    $vendor_data['appliances'] = $this->input->post('appliances');
                if(!empty($this->input->post('brands')))
                    $vendor_data['brands'] = $this->input->post('brands');    
            
            return $vendor_data;
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
        $template = $this->vendor_model->getVendorSmsTemplate("new_vendor_creation");
        $smsBody = sprintf($template, $vendor_name);

        $this->notify->sendTransactionalSmsAcl($phone_number, $smsBody);
        //For saving SMS to the database on sucess
    
        $this->notify->add_sms_sent_details($id, 'vendor' , $phone_number,
                   $smsBody, '','new_vendor_creation' );
    

    }

    /**
     * @desc: This function is used to check validation of the entered data
     *
     * @param: void
     * @return : If validation ok returns true else false
     */
    function checkValidation() {
        $this->form_validation->set_rules('company_name', 'Vendor Name', 'trim|required|xss_clean');
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
        $results['services'] = $this->vendor_model->selectservice();
        $results['brands'] = $this->vendor_model->selectbrand();
        $results['select_state'] = $this->vendor_model->getall_state();
        $results['employee_rm'] = $this->employee_model->get_rm_details();
   
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/addvendor', array('results' => $results, 'days' => $days));
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
        $query = $this->vendor_model->editvendor($id);

        $results['services'] = $this->vendor_model->selectservice();
        $results['brands'] = $this->vendor_model->selectbrand();
        $results['select_state'] = $this->vendor_model->getall_state();
        $results['employee_rm'] = $this->employee_model->get_rm_details();

        $appliances = $query[0]['appliances'];
        $selected_appliance_list = explode(",", $appliances);
        $brands = $query[0]['brands'];
        $selected_brands_list = explode(",", $brands);

        $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);
        
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $non_working_days = $query[0]['non_working_days'];
        $selected_non_working_days = explode(",", $non_working_days);
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));

        $this->load->view('employee/addvendor', array('query' => $query, 'results' => $results, 'selected_brands_list'
            => $selected_brands_list, 'selected_appliance_list' => $selected_appliance_list,
            'days' => $days, 'selected_non_working_days' => $selected_non_working_days,'rm'=>$rm));
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
        if (!empty($sf_list)) {
            $sf_list = $sf_list[0]['service_centres_id'];
        }
        //Getting State for SC charges
        $state = $this->service_centre_charges_model->get_unique_states_from_tax_rates();
        $query = $this->vendor_model->viewvendor($vendor_id, $active, $sf_list);
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
        $this->load->view('employee/viewvendor', array('query' => $query,'state' =>$state , 'selected' =>$data));
    }

    /**
     * @desc: This function is to activate a particular vendor
     *
     * For this the vendor must be already registered with us and should be non-active(Active = 0)
     *
     * @param: vendor id
     * @return : void
     */
    function activate($id) {
        $this->vendor_model->activate($id);
        
        //Getting Vendor Details
        $sf_details = $this->vendor_model->getVendorContact($id);
        $sf_name = $sf_details[0]['name'];
        
        //Sending Mail to corresponding RM and admin group 
        $employee_relation = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);
        if (!empty($employee_relation)) {
            $rm_details = $this->employee_model->getemployeefromid($employee_relation[0]['agent_id']);
            $to = $rm_details[0]['official_email'];

            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("sf_permanent_on_off");
            if (!empty($template)) {
                $email['rm_name'] = $rm_details[0]['full_name'];
                $email['sf_name'] = ucfirst($sf_name);
                $email['on_off'] = 'ON';
                $subject = " Permanent ON Vendor " . $sf_name;
                $emailBody = vsprintf($template[0], $email);
                $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "");
            }

            log_message('info', __FUNCTION__ . ' Permanent ON of Vendor' . $sf_name);
        }
        
        //Storing State change values in Booking_State_Change Table
        $this->notify->insert_state_change('', _247AROUND_VENDOR_ACTIVATED, _247AROUND_VENDOR_DEACTIVATED, 'Vendor ID = '.$id, $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }

    /**
     * @desc: This function is to deactivate a particular vendor
     *
     * For this the vendor must be already registered with us and should be active(Active = 1)
     *
     * @param: vendor id
     * @return : void
     */
    function deactivate($id) {
        $this->vendor_model->deactivate($id);
        
        //Getting Vendor Details
        $sf_details = $this->vendor_model->getVendorContact($id);
        $sf_name = $sf_details[0]['name'];
        
        //Sending Mail to corresponding RM and admin group 
        $employee_relation = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);
        if (!empty($employee_relation)) {
            $rm_details = $this->employee_model->getemployeefromid($employee_relation[0]['agent_id']);
            $to = $rm_details[0]['official_email'];

            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("sf_permanent_on_off");
            if (!empty($template)) {
                $email['rm_name'] = $rm_details[0]['full_name'];
                $email['sf_name'] = ucfirst($sf_name);
                $email['on_off'] = 'OFF';
                $subject = " Permanent OFF Vendor " . $sf_name;
                $emailBody = vsprintf($template[0], $email);
                $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "");
            }

            log_message('info', __FUNCTION__ . ' Permanent OFF of Vendor' . $sf_name);
        }
        
        //Storing State change values in Booking_State_Change Table
        $this->notify->insert_state_change('', _247AROUND_VENDOR_DEACTIVATED, _247AROUND_VENDOR_ACTIVATED, 'Vendor ID = '.$id, $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
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
        $this->notify->insert_state_change('', _247AROUND_VENDOR_DELETED, _247AROUND_VENDOR_DELETED, 'Vendor ID = '.$id, $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
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

        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
        log_message('info', __METHOD__ );
        $service_center = $this->input->post('service_center');
        $agent_id =  $this->input->post('agent_id');
        $agent_name =  $this->input->post('agent_name');
        $url = base_url() . "employee/do_background_process/assign_booking";
        $count = 0;
       
        foreach ($service_center as $booking_id => $service_center_id) {
            if ($service_center_id != "") {
                $assigned = $this->miscelleneous->assign_vendor_process($service_center_id,$booking_id, $agent_id,$agent_name);
                if($assigned){
                    // Insert log into booking state change
                    $this->notify->insert_state_change($booking_id, ASSIGNED_VENDOR, _247AROUND_PENDING,
                            "Service Center Id: " . $service_center_id,$agent_id, $agent_name, _247AROUND);

                    $count++;
                } else {
                    log_message('info', __METHOD__ . "=> Not Assign for Sc "
                                    . $service_center_id);
                }
            }
        }

        //Send mail and SMS to SF in background
        $async_data['booking_id'] = $service_center;
        $async_data['agent_id'] =  $agent_id;
        $async_data['agent_name'] = $agent_name;
        $this->asynchronous_lib->do_background_process($url, $async_data);

        echo " Request to Assign Bookings: " . count($service_center) . ", Actual Assigned Bookings: " . $count;

        //redirect(base_url() . DEFAULT_SEARCH_PAGE);
    }
   

    /**
     * @desc: This function is to get the reassign vendor page
     *
     * Its mainly done if already assigned vendor do not covers the pincode taken while entering booking.
     *
     * @param: booking id
     * @return : void
     */
    function get_reassign_vendor_form($booking_id = "") {
        $service_centers = $this->booking_model->select_service_center();

        $this->load->view('employee/header/'.$this->session->userdata('user_group'));

        $this->load->view('employee/reassignvendor', array('booking_id' => $booking_id, 'service_centers' => $service_centers));
    }

    /**
     * @desc: This function reassigns vendor for a particular booking.
     *
     * This is done if the assigned vendor is not able to finish his job due to any reason
     *
     * @param: void
     * @return : void
     */
    function process_reassign_vendor_form() {
        $booking_id = $this->input->post('booking_id');
        $service_center_id = $this->input->post('service');

	if ($service_center_id != "Select") {
//            if (IS_DEFAULT_ENGINEER == TRUE) {
//                $b['assigned_engineer_id'] = DEFAULT_ENGINEER;
//            } else {
//                $engineer = $this->vendor_model->get_engineers($service_center_id);
//                if (!empty($engineer)) {
//                    $b['assigned_engineer_id'] = $engineer[0]['id'];
//                }
//            }
            //Assign service centre and engineer
            $assigned_data = array('assigned_vendor_id'=>$service_center_id, 
                'assigned_engineer_id' =>DEFAULT_ENGINEER,
                'is_upcountry'=>0,
                'upcountry_pincode' =>NULL,
                'sub_vendor_id'=> NULL,
                'sf_upcountry_rate'=> NULL,
                'partner_upcountry_rate'=> NULL,
                'is_penalty' => 0,
                'upcountry_partner_approved' => 1,
                'upcountry_paid_by_customer' =>0,
                'upcountry_distance'=> NULL);
            
            $this->booking_model->update_booking($booking_id, $assigned_data);

            $this->vendor_model->delete_previous_service_center_action($booking_id);
            $unit_details = $this->booking_model->getunit_details($booking_id);
           
            foreach ($unit_details[0]['quantity'] as $value ) {
                $data = array();
                $data['current_status'] = "Pending";
                $data['internal_status'] = "Pending";
                $data['service_center_id'] = $service_center_id;
                $data['booking_id'] = $booking_id;
                $data['create_date'] = date('Y-m-d H:i:s');
                $data['update_date'] = date('Y-m-d H:i:s');
                $data['unit_details_id'] = $value['unit_id'];
                $this->vendor_model->insert_service_center_action($data);
                
            }

            $this->notify->insert_state_change($booking_id, RE_ASSIGNED_VENDOR, ASSIGNED_VENDOR, 
                    "Re-Assigned SF ID: " . $service_center_id, $this->session->userdata('id'), 
                    $this->session->userdata('employee_id'), _247AROUND);
            
            //Prepare job card (For Reassigned Vendor)
            $job_card = array();
	    $job_card_url = base_url() . "employee/bookingjobcard/prepare_job_card_using_booking_id/".$booking_id;
	    $this->asynchronous_lib->do_background_process($job_card_url, $job_card);

            $url = base_url() . "employee/vendor/mark_upcountry_booking/".$booking_id."/".$this->session->userdata('id')
                    ."/".$this->session->userdata('employee_id');
            $async_data['data'] = array();
            $this->asynchronous_lib->do_background_process($url, $async_data);

	    log_message('info', "Reassigned - Booking id: " . $booking_id . "  By " .
            $this->session->userdata('employee_id') . " service center id " . $service_center_id);
             


            redirect(base_url() . DEFAULT_SEARCH_PAGE);
	} else {
            $output = "Please select any service center.";
            $userSession = array('error' => $output);
            $this->session->set_userdata($userSession);
            redirect(base_url() . 'employee/vendor/get_reassign_vendor_form/' . $booking_id, 'refresh');
        }
    }
    
    function mark_upcountry_booking($booking_id, $agent_id,$agent_name){
        log_message('info', __METHOD__ ." Booking_id " . $booking_id . "  By agent id " .
            $agent_id . $agent_name);
        $this->miscelleneous->assign_upcountry_booking($booking_id, $agent_id, $agent_name);
        $this->booking_utilities->lib_prepare_job_card_using_booking_id($booking_id);
    }

    /**
     * @desc: This function to get form to broadcast mail to all vendors
     * @param: void
     * @return : void
     */
    function get_broadcast_mail_to_vendors_form() {
        //$service_centers = $this->booking_model->select_service_center();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/broadcastemailtovendor');
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
        $bcc_poc = $this->input->post('bcc_poc');
        $bcc_owner = $this->input->post('bcc_owner');
        $mail_to = $this->input->post('mail_to');
        $to = NITS_ANUJ_EMAIL_ID.', sales@247around.com,' . $mail_to;
	$cc = $this->input->post('mail_cc');

	$subject = $this->input->post('subject');
	//Replace new lines with line breaks for proper html formatting
	$message = nl2br($this->input->post('mail_body'));

	$tmpFile = $_FILES['fileToUpload']['tmp_name'];
        $fileName = $_FILES['fileToUpload']['name'];
        move_uploaded_file($tmpFile, TMP_FOLDER.$fileName);

        //gets primary contact's email and owner's email
        $service_centers = $this->vendor_model->select_active_service_center_email();
        $bcc = $this->getBccToSendMail($service_centers, $bcc_poc, $bcc_owner);
        $attachment = "";
        if (!empty($fileName)) {
            $attachment = TMP_FOLDER.$fileName;
        }

        log_message('info', "broadcast mail to: " . $to);
        log_message('info', "broadcast mail cc: " . $cc);
        log_message('info', "broadcast mail bcc: " . $bcc);
        log_message('info', "broadcast mail subject: " . $subject);
        log_message('info', "broadcast mail message: " . $message);

        $this->notify->sendEmail("sales@247around.com", $to, $cc, $bcc, $subject, $message, $attachment);

       redirect(base_url() . DEFAULT_SEARCH_PAGE);
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
        $mapping_file['pincode_mapping_file'] = $this->vendor_model->getLatestVendorPincodeMappingFile();
        $mapping_file['total_pincode'] = $this->vendor_model->get_total_vendor_pincode_mapping();
        $mapping_file['latest_vendor_pincode'] = $this->vendor_model->get_latest_vendor_pincode_mapping_details();
        
        if ($error != "") {
            $mapping_file['error'] = $error;
        }
        $file_type = "'Vendor-Pincode'";
        $mapping_file['latest_file'] = $this->reporting_utils->get_all_latest_uploaded_file($file_type);
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/upload_master_pincode_excel');
    }

    /**
     *  @desc : This function is to upload pincode through excel (asynchronously)
     *  @param : void
     *  @return : void
     */
    function process_pincode_excel_upload_form() {
        if(!empty($_FILES['file']['tmp_name'])){
            $inputFileName = $_FILES['file']['tmp_name'];
            log_message('info', __FUNCTION__ . ' => Input ZIP file: ' . $inputFileName);

            $newZipFileName = TMP_FOLDER."vendor_pincode_mapping_temp.zip";
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

            //Logging
            log_message('info', __FUNCTION__ . ' Processing of Pincode CSV File started');

            //Adding Details in File_Uploads table as well

            $data_uploads['file_name'] = "vendor_pincode_mapping_temp.zip";
            $data_uploads['file_type'] = _247AROUND_VENDOR_PINCODE;
            $data_uploads['agent_id'] = $this->session->userdata('id');
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
            $directory_xls = "vendor-partner-docs/vendor_pincode_mapping_temp.zip";
            $this->s3->putObjectFile(TMP_FOLDER . "vendor_pincode_mapping_temp.zip", $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            //Logging
            log_message('info', __FUNCTION__ . ' Vendor Pincode File has been uploaded in S3');

            //Processing SQL Queries
            
            $sql_commands = array();
            array_push($sql_commands, "TRUNCATE TABLE vendor_pincode_mapping_temp;");
            $this->vendor_model->execute_query($sql_commands);
            unset($sql_commands);

            $dbHost=$this->db->hostname;
            $dbUser=$this->db->username;
            $dbPass=$this->db->password;
            $dbName=$this->db->database;

            $csv = TMP_FOLDER.$newCSVFileName;
            $sql = "LOAD DATA LOCAL INFILE '$csv' INTO TABLE vendor_pincode_mapping_temp "
                   . "FIELDS TERMINATED BY ',' ENCLOSED BY '' LINES TERMINATED BY '\r\n' "
                    . "(Vendor_Name,Vendor_ID,Appliance,Appliance_ID,Brand,Area,Pincode,Region,City,State);";

            $res1 = 0;
            system("mysql -u $dbUser -h $dbHost --password=$dbPass --local_infile=1 -e \"$sql\" $dbName", $res1);

            $sql_commands1 = array();
            
            array_push($sql_commands1, "TRUNCATE TABLE vendor_pincode_mapping;");
            array_push($sql_commands1, "INSERT vendor_pincode_mapping SELECT * FROM vendor_pincode_mapping_temp;");

            $this->vendor_model->execute_query($sql_commands1);

            //Uploading csv file to S3
            $directory_xls = "vendor-pincodes/" . $newCSVFileName;
            $this->s3->putObjectFile(TMP_FOLDER . $newCSVFileName, BITBUCKET_DIRECTORY, $directory_xls, S3::ACL_PUBLIC_READ);

            //Inserting file details in pincode_mapping_s3_upload_details
            $data['bucket_name'] = 'vendor-pincodes';// We add Directory of Bucket used
            $data['file_name'] = $newCSVFileName;

            $this->vendor_model->insertS3FileDetails($data);


            system ("rm -rf ".$newZipFileName);
            system ("rm -rf ".TMP_FOLDER . $CSVFileName );
            system ("rm -rf ".TMP_FOLDER . $newCSVFileName );
           log_message('info', __FUNCTION__ . ' => All queries executed: ');
            //log_message('info', __FUNCTION__ . ' => New pincode count: ' . $count);

            redirect(base_url() . DEFAULT_SEARCH_PAGE);
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
        //get escalation reasons for 247around
        $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'247around','active'=> '1','process_type'=>'escalation'));
        $data['vendor_details'] = $this->vendor_model->getVendor($booking_id);
        $data['booking_id'] = $booking_id;

        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
        $escalation['booking_id'] = $this->input->post('booking_id');
        $escalation['vendor_id'] = $this->input->post('vendor_id');
        //Get SF to RM relation if present
        $cc = "";
        $rm = $this->vendor_model->get_rm_sf_relation_by_sf_id($escalation['vendor_id']);
        if(!empty($rm)){
            foreach($rm as $key=>$value){
                if($key == 0){
                    $cc .= "";
                }else{
                    $cc .= ",";
                }
                $cc .= $this->employee_model->getemployeefromid($value['agent_id'])[0]['official_email'];
            }
        }
        $checkValidation = $this->checkValidationOnReason();
        if ($checkValidation) {
            $escalation['escalation_reason'] = $this->input->post('escalation_reason_id');
            
            $this->booking_model->increase_escalation_reschedule($escalation['booking_id'], "count_escalation");
            
            $booking_date_timeslot = $this->vendor_model->getBookingDateFromBookingID($escalation['booking_id']);

            $booking_date = strtotime($booking_date_timeslot[0]['booking_date']);

            $escalation['booking_date'] = date('Y-m-d', $booking_date);
            $escalation['booking_time'] = $booking_date_timeslot[0]['booking_timeslot'];
            
            //inserts vendor escalation details
            $escalation_id = $this->vendor_model->insertVendorEscalationDetails($escalation);
            
            if ($escalation_id) {
                $escalation_policy_details = $this->vendor_model->getEscalationPolicyDetails($escalation['escalation_reason']);
                // Update escalation flag and return userDeatils
                $userDetails = $this->vendor_model->updateEscalationFlag($escalation_id, $escalation_policy_details, $escalation['booking_id']);

                log_message('info', "User Details " . print_r($userDetails, TRUE));
                log_message('info', "Vendor_ID " . $escalation['vendor_id']);

                $vendorContact = $this->vendor_model->getVendorContact($escalation['vendor_id']);

                $return_mail_to = $vendorContact[0]['owner_email'].','.$vendorContact[0]['primary_contact_email'];

                //Getting template from Database
                $template = $this->booking_model->get_booking_email_template("escalation_on_booking");
                if (!empty($template)) {
                    
                    //From will be currently logged in user
                    $from = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['official_email'];
                    
                    //Sending Mail
                    $email['booking_id'] = $escalation['booking_id'];
                    $email['count_escalation'] = $booking_date_timeslot[0]['count_escalation'];
                    $email['reason'] = $escalation_policy_details[0]['escalation_reason'];
                    $emailBody = vsprintf($template[0], $email);

                    $subject['booking_id'] = $escalation['booking_id'];
                    $subjectBody = vsprintf($template[4], $subject);
                    $this->notify->sendEmail($from, $return_mail_to, $template[3] . "," . $cc, '', $subjectBody, $emailBody, "");

                    //Logging
                    log_message('info', " Escalation Mail Send successfully" . $emailBody);
                } else {
                    //Logging Error Message
                    log_message('info', " Error in Getting Email Template for Escalation Mail");
                }
                
                $this->sendSmsToVendor($escalation,$escalation_policy_details, $vendorContact, $escalation['booking_id'], $userDetails);
                $escalation_reason  = $this->vendor_model->getEscalationReason(array('id'=>$escalation['escalation_reason']));
                $remarks = $this->input->post('remarks');
                if(!empty($remarks)){
                    $escalation_reason_final = $escalation_reason[0]['escalation_reason'].' - '.$remarks;
                }else{
                    $escalation_reason_final = $escalation_reason[0]['escalation_reason'];
                }
                
                $this->notify->insert_state_change($escalation['booking_id'], 
                    "Escalation" , "Pending" , $escalation_reason_final, 
                    $this->session->userdata('id'), $this->session->userdata('employee_id'),
                    _247AROUND);
                
                //Processing Penalty on Escalations
                
                $value['booking_id'] = $escalation['booking_id'];
                $value['assigned_vendor_id'] = $escalation['vendor_id'];
                $value['current_state'] = "Escalation";
                $value['agent_id'] = $this->session->userdata('id');
                $value['remarks'] = $escalation_reason_final;
                $where = array('escalation_id' => ESCALATION_PENALTY, 'active' => '1');
                //Adding values in penalty on booking table
                $this->penalty_model->get_data_penalty_on_booking($value, $where);

                log_message('info', 'Penalty added for Escalations - Booking : ' . $escalation['booking_id']);
                

                redirect(base_url() . DEFAULT_SEARCH_PAGE);
	    }
        } else {
            $this->get_vendor_escalation_form($escalation['booking_id']);
        }
    }

    /**
     * @desc: Send SMS to Vendor and Owner when flag of sms to owner and sms to vendor is 1.
     *
     * @param : escalation policy details
     * @param : vendor contact
     * @param : booking id
     * @param : user's details
     * @return : void
     */
    function sendSmsToVendor($escalation,$escalation_policy, $contact, $booking_id, $userDetails) {
        
        $id = $escalation['vendor_id'];
       
        if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 1) {

            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

            $this->notify->sendTransactionalSmsAcl($contact[0]['primary_contact_phone_1'], $smsBody);
            //For saving SMS to the database on sucess

            $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['primary_contact_phone_1'],
                    $smsBody, $booking_id, "Escalation");
            

            $this->notify->sendTransactionalSmsAcl($contact[0]['owner_phone_1'], $smsBody);
            //For saving SMS to the database on sucess

            $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['owner_phone_1'],
                    $smsBody, $booking_id,"Escalation");
            
        } else if ($escalation_policy[0]['sms_to_owner'] == 0 && $escalation_policy[0]['sms_to_poc'] == 1) {

            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

            $this->notify->sendTransactionalSmsAcl($contact[0]['primary_contact_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            
            $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['primary_contact_phone_1'],
                    $smsBody, $booking_id, "Escalation");
            
        } else if ($escalation_policy[0]['sms_to_owner'] == 1 && $escalation_policy[0]['sms_to_poc'] == 0) {

            $smsBody = $this->replaceSms_body($escalation_policy[0]['sms_body'], $booking_id, $userDetails);

            $this->notify->sendTransactionalSmsAcl($contact[0]['owner_phone_1'], $smsBody);
            //For saving SMS to the database on sucess
            
            $this->notify->add_sms_sent_details($id, 'vendor' , $contact[0]['owner_phone_1'],
                    $smsBody, $booking_id, "Escalation");
        
            
    }
    }

    /**
     * @desc: Send SMS to Vendor and Owner when flag of sms to owner and sms to vendor is 1.
     *
     * @param : sms template
     * @param : booking id
     * @param : user's details
     * @return : sms body
     */
    function replaceSms_body($template, $booking_id, $userDetails) {

        $smsBody = sprintf($template, $userDetails[0]['name'], $userDetails[0]['phone_number'], $booking_id);

        return $smsBody;
    }


    /**
     * @desc: This function is to check validation on escalation reason
     *
     * @param : void
     * @return : true if validation is true else false
     */
    function checkValidationOnReason() {
        $this->form_validation->set_rules('escalation_reason_id', 'Escalation Reason', 'required');
        $this->form_validation->set_rules('vendor_id', 'Vendor ID', 'required');
        return $this->form_validation->run();
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
        $data = $this->vendor_model->get_services_category_city_pincode();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
        $this->notify->sendEmail("booking@247around.com", $to, '', '', 'Pincode Changes', $notes, $attachment);
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
        $data = $this->vendor_model->get_vendor_city_appliance();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
        $charges['charges'] = $this->vendor_model->getbooking_charges();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/review_booking_complete_cancel', $charges);
    }

    /**
     * @desc: get cancellation reation for specific vendor id
     * @param: void
     * @return: void
     */
    function getcancellation_reason($vendor_id) {
        $reason['reason'] = $this->vendor_model->getcancellation_reason($vendor_id);
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/vendor_cancellation_reason', $reason);
    }

    /**
     * @desc: get form to send mail to specific vendor
     * @param: void
     * @return: vendor's list to view
     */
    function get_mail_vendor($vendor_id = "") {
        $vendor_info = $this->vendor_model->viewvendor($vendor_id);

        $this->load->view('employee/header/'.$this->session->userdata('user_group'));

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

        $this->notify->sendEmail("sales@247around.com", $to, $cc, $bcc, $subject, $message, $attachment);

        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
            $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
            $data['data'] = $this->vendor_model->get_engg_by_id($id); 
        }
        if($this->session->userdata('userType') == 'service_center'){

            $this->load->view('service_centers/header');
            $this->load->view('service_centers/add_engineer', $data);

        } else {
            $this->load->view('employee/header/'.$this->session->userdata('user_group'));
            $this->load->view('employee/add_engineer', $data);
        }
    }
    /**
     * @desc: This method adds engineers for a service center.
     *  This  function is used by vendor panel and admin panel to load add engineer details.
     */
    function process_add_engineer() {
	$engineer_form_validation = $this->engineer_form_validation();

	if ($engineer_form_validation) {
	    $data['name'] = $this->input->post('name');
	    $data['phone'] = $this->input->post('phone');
	    $data['alternate_phone'] = $this->input->post('alternate_phone');
	    $data['phone_type'] = $this->input->post('phone_type');
	    //$data['address'] = $this->input->post('address');
	    $data['identity_proof'] = $this->input->post('identity_proof');
	    $data['identity_proof_number'] = $this->input->post('identity_id_number');
	    $data['bank_name'] = $this->input->post('bank_name');
	    $data['bank_ac_no'] = $this->input->post('bank_account_no');
	    $data['bank_ifsc_code'] = $this->input->post('bank_ifsc_code');
	    $data['bank_holder_name'] = $this->input->post('bank_holder_name');
	    $data['identity_proof_pic'] = $this->input->post('file');
	    $data['bank_proof_pic'] = $this->input->post('bank_proof_pic');
	    //
	    //Get vendor ID from session if form sent thru vendor CRM
	    //Else from POST variable.
	    if ($this->session->userdata('userType') == 'service_center') {
		$data['service_center_id'] = $this->session->userdata('service_center_id');
	    } else {
		$data['service_center_id'] = $this->input->post('service_center_id');
	    }

	    //applicable services for an engineer come as array in service_id field.
	    $service_id = $this->input->post('service_id');
	    $services = array();
	    foreach ($service_id as $id) {
		array_push($services, array('service_id' => $id));
	    }

	    $data['appliance_id'] = json_encode($services);
	    $data['active'] = "1";
	    $data['create_date'] = date("Y-m-d H:i:s");

	    $engineer_id = $this->vendor_model->insert_engineer($data);

	    if ($engineer_id) {
		log_message('info', __METHOD__ . "=> Engineer Details Added.");

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
	} else { //form validation failed
	    $this->add_engineer();
	}
    }
    /**
     * @desc: This method is used to process edit engineer form
     * params: Post data array
     * 
     */
    function process_edit_engineer(){
        $engineer_form_validation = $this->engineer_form_validation();
        $engineer_id = $this->input->post('id');
        if ($engineer_form_validation) {
	    $data['name'] = $this->input->post('name');
	    $data['phone'] = $this->input->post('phone');
	    $data['alternate_phone'] = $this->input->post('alternate_phone');
	    $data['phone_type'] = $this->input->post('phone_type');
	    $data['identity_proof'] = $this->input->post('identity_proof');
	    $data['identity_proof_number'] = $this->input->post('identity_id_number');
	    $data['bank_name'] = $this->input->post('bank_name');
	    $data['bank_ac_no'] = $this->input->post('bank_account_no');
	    $data['bank_ifsc_code'] = $this->input->post('bank_ifsc_code');
	    $data['bank_holder_name'] = $this->input->post('bank_holder_name');
            if($this->input->post('file')){
	    $data['identity_proof_pic'] = $this->input->post('file');
            }
            if($this->input->post('bank_proof_pic')){
	    $data['bank_proof_pic'] = $this->input->post('bank_proof_pic');
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
	    $services = array();
	    foreach ($service_id as $id) {
		array_push($services, array('service_id' => $id));
	    }

	    $data['appliance_id'] = json_encode($services);
	    $data['update_date'] = date("Y-m-d H:i:s");
            
            $where = array('id' => $engineer_id );
	    $engineer_id = $this->vendor_model->update_engineer($where,$data);

                log_message('info', __METHOD__ . "=> Engineer Details Added.");

		$output = "Engineer Details Updated.";
		$userSession = array('update_success' => $output);

	    $this->session->set_userdata($userSession);

	    if ($this->session->userdata('userType') == 'service_center') {
		log_message('info', __FUNCTION__ . " Engineer updation initiated By Service Center ID ". $engineer_id);

		redirect(base_url() . "employee/vendor/get_engineers");
	    } else {
		log_message('info', __FUNCTION__ . " Engineer updation initiated By 247around ID ". $engineer_id);

		redirect(base_url() . "employee/vendor/get_engineers");
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
        $service_center_id = "";
        if($this->session->userdata('userType') == 'service_center'){

            $service_center_id = $this->session->userdata('service_center_id');
            log_message('info', __FUNCTION__ . " view service center Engineer View  " . print_r($service_center_id, true));
        }

       $data['engineers'] =  $this->vendor_model->get_engineers($service_center_id);
       foreach ($data['engineers'] as $key => $value) {
           $service_center = $this->vendor_model->getActiveVendor($value['service_center_id'],0);
           $data['engineers'][$key]['service_center_name'] = $service_center[0]['name'];
           $service_id  = json_decode($value['appliance_id'],true);
           $appliances = array();
           if(!empty($service_id)){
                foreach ($service_id as  $values) {
                     $service_name = $this->booking_model->selectservicebyid($values['service_id']);
                     array_push($appliances, $service_name[0]['services']);
                }
           }

           $data['engineers'][$key]['appliance_name'] = implode(",", $appliances);
       }
       if($this->session->userdata('userType') == 'service_center'){

            $this->load->view('service_centers/header');
            $this->load->view('service_centers/view_engineers', $data);

       } else {
            $this->load->view('employee/header/'.$this->session->userdata('user_group'));
            $this->load->view('employee/view_engineers', $data);
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

        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('phone', 'Mobile Number', 'trim|numeric|required|xss_clean');
        $this->form_validation->set_rules('alternate_phone', 'Alternate Mobile Number', 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('identity_id_number', 'ID Number', 'xss_clean');
        $this->form_validation->set_rules('identity_proof', 'Identity Proof', 'xss_clean');
        $this->form_validation->set_rules('bank_account_no', 'Bank Account No', 'numeric|xss_clean');
	$this->form_validation->set_rules('service_id', 'Appliance ', 'xss_clean');
        $this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|xss_clean');
        $this->form_validation->set_rules('bank_ifsc_code', 'IFSC Code', 'trim|xss_clean');
        $this->form_validation->set_rules('bank_holder_name', 'Account Holder Name', 'trim|xss_clean');
        $this->form_validation->set_rules('file', 'Identity Proof Pic ', 'callback_upload_identity_proof_pic');
	    $this->form_validation->set_rules('bank_proof_pic', 'Bank Proof Pic', 'callback_upload_bank_proof_pic');

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
		    $pic = str_replace(' ', '-', $this->input->post('name')) . "_" . str_replace(' ', '', $this->input->post('bank_name')) . "_" . uniqid(rand());
		    $picName = $pic . "." . $extension;
		    $_POST['bank_proof_pic'] = $picName;
                    // Uploading to S3
		    $bucket = "bookings-collateral";
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
		    $pic = str_replace(' ', '-', $this->input->post('name')) . "_" . str_replace(' ', '', $this->input->post('identity_proof')) . "_" . uniqid(rand());
		    $picName = $pic . "." . $extension;
		    $_POST['file'] = $picName;
                    //Uploading to S3
		    $bucket = "bookings-collateral";
		    $directory = "engineer-id-proofs/" . $picName;
		    $this->s3->putObjectFile($_FILES["file"]["tmp_name"], $bucket, $directory, S3::ACL_PUBLIC_READ);

		    return TRUE;
		}
	    } else {
		$this->form_validation->set_message('upload_identity_proof_pic', 'File size or file type is not supported. Allowed extentions are "png", "jpg", "jpeg" and "pdf". '
		    . 'Maximum file size is 2 MB.');
		return FALSE;
	    }
	}
    }

     /**
     *  @desc : This function is used to Add Vendor for a particular Pincode
     *  
     *  It is being called using AJAX request.
     * 
     *  @param : POST data like Pincode, appliance, appliance ID, city, brand
      *         or Empty for New entry of Vendor Pincode Mapping
     *  @return : Mixed  
     *           print variable storing view of Vendor Pincode Form.
     */

    function get_add_vendor_to_pincode_form(){
        $data = array();
        if(!empty($this->input->post())){
        $booking_data  = $this->booking_model->getbooking_history($this->input->post('booking_id'));
        $data['pincode'] = $booking_data[0]['booking_pincode'];
        $data['Appliance'] = $booking_data[0]['services'];
        $data['Appliance_ID'] = $booking_data[0]['service_id'];
        $data['brand'] = $booking_data[0]['appliance_brand'];
        $data['city'] = $booking_data[0]['city'];
        
        //Getting data from Database using Booking ID

            $data['vendors'] = $this->vendor_model->get_distinct_vendor_details($data['Appliance_ID']);
            $data['state'] = $this->vendor_model->getall_state();
        
        //Loading view in $data for parsin response in Ajax success
        $data = $this->load->view('employee/add_vendor_to_pincode', $data, TRUE);
        print_r($data);
        }else{
            
            $data['vendor_details'] = $this->vendor_model->getActiveVendor();
            $data['state'] = $this->vendor_model->getall_state();
            // Return view for adding of New Vendor to Pincode
            $this->load->view('employee/header/'.$this->session->userdata('user_group'));
            $this->load->view('employee/add_vendor_to_pincode',$data);
        }

    }

    /**
     *  @desc : This function is used to Process Add Vendor to pincode Form
     *  @param : Array of $_POST data
     *  @return : void
     */

    function process_add_vendor_to_pincode_form(){
        //Getting Post data
        if($this->input->post()){
            //Adding Validation Rules
            $this->form_validation->set_rules('pincode', 'Pincode', 'trim|required|numeric|min_length[6]|max_length[6]');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            $this->form_validation->set_rules('area', 'Area', 'trim|required');
            $this->form_validation->set_rules('state', 'State', 'trim|required');
            $this->form_validation->set_rules('vendor_id', 'Vendor Id', 'required');
            $this->form_validation->set_rules('brand', 'Brand', 'trim|required');
            $this->form_validation->set_rules('choice', 'Services', 'required');

            //Check for Validation
            if ($this->form_validation->run() == FALSE) {

            $data = $this->input->post();
            
            $data['vendor_details'] = $this->vendor_model->getActiveVendor();
            $data['state'] = $this->vendor_model->getall_state();    
            
            $this->load->view('employee/header/'.$this->session->userdata('user_group'));
            $this->load->view('employee/add_vendor_to_pincode', $data);

            }else{

            $choice = $this->input->post('choice');

            $vendor_mapping = array(

                                'Vendor_ID'=>$this->input->post('vendor_id'),
                                'Brand'=>$this->input->post('brand'),
                                'Pincode'=>$this->input->post('pincode'),
                                'Region'=>'Region',
                                'Area'=>$this->input->post('area'),
                                'City'=>$this->input->post('city'),
                                'State'=>$this->input->post('state'),
                                'create_date'=>date('Y-m-d h:m:i'),
                                'active'=>1
                );
            //Looping through Appliance's Selected
            foreach ($choice as $key => $value) {
                    //Getting Appliance Name
                    $appliance = $this->booking_model->selectservicebyid($value);
                    //Getting Vendor Name
                    $vendor_name = $this->vendor_model->getActiveVendor($this->input->post('vendor_id'));
                    //Appending Array
                    $vendor_mapping['Vendor_Name'] = $vendor_name[0]['name'];
                    $vendor_mapping['Appliance'] = $appliance[0]['services'];
                    $vendor_mapping['Appliance_ID'] = $value;

                    //Checking for already entered Record
                    $result = $this->vendor_model->check_vendor_details($vendor_mapping);

                    if($result){
                        //Setting Flash data on success
                        $this->session->set_flashdata('success', 'Pincode Mapped to Vendor successfully.');

                          //Insert Data in vendor_pincode_mapping table
                        $vendor_id = $this->vendor_model->insert_vendor_pincode_mapping($vendor_mapping);
                        if(!empty($vendor_id)){
                            //Logging
                            log_message('info',__FUNCTION__.'Vendor assigned to Pincode in vendor_picode_mapping table. '.print_r($vendor_mapping,TRUE));
                        }else{
                            //Logging
                            log_message('info',__FUNCTION__.' Error in adding vendor to pincode in vendor_pincode_mapping table '.print_r($vendor_mapping,TRUE));
                        }
                        
                        //Replicating data in 247Aroung_vendor_pincode_mapping table
                        $_247_vendor_id = $this->vendor_model->insert_247Around_vendor_pincode_mapping($vendor_mapping);
                        if(!empty($_247_vendor_id)){
                            //Logging
                            log_message('info',__FUNCTION__.'Vendor assigned to Pincode in 247Around_vendor_pincode_mapping table. '.print_r($vendor_mapping,TRUE));
                        }else{
                            //Logging
                            log_message('info',__FUNCTION__.'Error in addding vendor to pincode in 247Around_vendor_pincode_mapping table '.print_r($vendor_mapping,TRUE));
                        }

                    } else {
                        //Echoing duplicay error in Log file
                        log_message('info',__FUNCTION__.'Vendor already assigned to '.$vendor_mapping['Appliance'] );
                        //Setting Flash variable on Error
                        $this->session->set_flashdata('error','Vendor already assigned to '.$vendor_mapping['Appliance']  );
                    }
            }


            //redirect(site_url('employee/booking/view_queries/FollowUp'));
            redirect(base_url() . DEFAULT_SEARCH_PAGE);

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

    /**
     *  @desc : This function is used to Delete assigned vendor to vendor_pincode_mapping
     *          and  process form data
     *  @param : void
     *  @return : array
    */
      function process_vendor_pincode_delete_form() {

	$data = array();
	//Getting data from database
	$data['vendor_details'] = $this->vendor_model->getActiveVendor();
	$data['appliance'] = $this->booking_model->selectservice();
	$data['state'] = $this->vendor_model->getall_state();

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
	$this->load->view('employee/header/'.$this->session->userdata('user_group'));
	$this->load->view('employee/list_vendor_pincode', $data);
    }
    
    /**
     * @desc: This method is used to send mail with Vendor Pincode Mapping file.
     * This is called by Ajax. It gets email and notes by form. Pass it to asynchronous method.
     * @param: void
     * @return: print success
     */
    function download_unique_pincode_excel(){

        log_message('info', __FUNCTION__);

        $template = 'Vendor_Pincode_Mapping_Template.xlsx';
        //set absolute path to directory with template files
        $templateDir = __DIR__ . "/../excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );
        //load template
        $R = new PHPReport($config);
        $vendor = $this->vendor_model->get_all_pincode_mapping();

        $R->load(array(

                 'id' => 'vendor',
                'repeat' => TRUE,
                'data' => $vendor
            ));

        $output_file_dir = TMP_FOLDER;
        $output_file = "Vendor_Pincode_Mapping" . date('y-m-d');
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
     * @desc: This function to send mails to vendors
     * @param: void
     * @return : void
     */
    function get_mail_to_vendors_form() {
        $data = array();
        $data['vendors'] = $this->vendor_model->getActiveVendor();
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
        
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
                $vendors_array = $this->vendor_model->getActiveVendor();
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
                        $this->notify->sendEmail($email_template[0]['from'], $to, '', '', $email_template[0]['subject'], $emailBody, $attachment);
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
                        $this->notify->sendEmail($email_template[0]['from'], $to, '', '', $email_template[0]['subject'], $emailBody, $attachment);
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
        $data_report['query'] = $this->vendor_model->get_around_dashboard_queries();
        $data_report['data'] = $this->vendor_model->execute_around_dashboard_query($data_report['query']);
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/247around_dashboard', $data_report);
    }
    
    /**
     * @desc: This function is used to show editable grid for SMS Templates
     * params: void
     * return: view
     * 
     */
    function get_sms_template_editable_grid(){
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
            if ($searchOper == 'eq')
                $searchString = $searchString;
            if ($searchOper == 'bw' || $searchOper == 'bn')
                $searchString .= '%';
            if ($searchOper == 'ew' || $searchOper == 'en')
                $searchString = '%' . $searchString;
            if ($searchOper == 'cn' || $searchOper == 'nc' || $searchOper == 'in' || $searchOper == 'ni')
                $searchString = '%' . $searchString . '%';

            $where = "$searchField $ops '$searchString' ";
        }

        if (!$sidx)
            $sidx = 1;
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
        //Getting employee sf relation
        $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
        if(!empty($sf_list)){
            $sf_list = $sf_list[0]['service_centres_id'];
        }
        $data['html'] = $this->booking_utilities->booking_report_by_service_center($sf_list,'');
        
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/show_service_center_report',$data);
    }
    
    /**
     * @desc: This function is used to send Report Mail to logged user and is called using AJAX
     * params: void
     * return : Boolean
     */
    function send_report_to_mail(){
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
            
            $this->notify->sendEmail("booking@247around.com", $to, "", "", "Service Center Report", $html, "");
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
        //Getting employee sf relation
        $sf_list = $this->vendor_model->get_employee_relation($this->session->userdata('id'));
        if (!empty($sf_list)) {
            $sf_list = $sf_list[0]['service_centres_id'];
    }
        $data['html'] = $this->booking_utilities->booking_report_for_new_service_center($sf_list);
        $this->load->view('employee/header/' . $this->session->userdata('user_group'));
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
            $this->notify->insert_state_change("", NEW_SF_CRM, OLD_SF_CRM , "New CRM Enabled for SF ID: ".$service_center_id , $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        }else{
            $this->notify->insert_state_change("", OLD_SF_CRM, NEW_SF_CRM , "Old CRM Enabled for SF ID: ".$service_center_id , $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
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

            $this->notify->sendEmail("booking@247around.com", $to, "", "", "New Service Center Report", $html, "");
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
        $vendor  = $this->vendor_model->viewvendor('',1);
        
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
        $vendor = [];
        $vendor[$data['type']] = '';
        //Making Database Entry as Null
        $this->vendor_model->edit_vendor($vendor, $data['id']);
        
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
                    $this->form_validation->set_rules('name_on_pan', 'Name on Pan', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('pan_no', 'Pan Number', 'trim|required|xss_clean');
                    break;
                
             case 'cst_file': 
                    $this->form_validation->set_rules('cst_no', 'CST Number', 'trim|required|xss_clean');
                    break;
                
             case 'tin_file': 
                    $this->form_validation->set_rules('tin_no', 'TIN/VAT Number', 'trim|required|xss_clean');
                    break;
                
             case 'service_tax_file': 
                    $this->form_validation->set_rules('service_tax_no', 'Service Tax Number', 'trim|required|xss_clean');
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
        $this->vendor_model->temporary_on_off_vendor($id,$on_off);
        
        //Check on off
        if($on_off == 1){
            $on_off_value = 'ON';
        }else{
            $on_off_value = 'OFF';
        }
        
        //Getting Vendor Details
        $sf_details = $this->vendor_model->getVendorContact($id);
        $sf_name = $sf_details[0]['name'];
        
        //Sending Mail to corresponding RM and admin group 
        $employee_relation = $this->vendor_model->get_rm_sf_relation_by_sf_id($id);
        if (!empty($employee_relation)) {
            $rm_details = $this->employee_model->getemployeefromid($employee_relation[0]['agent_id']);
            $to = $rm_details[0]['official_email'];

            //Getting template from Database
            $template = $this->booking_model->get_booking_email_template("sf_temporary_on_off");
            if (!empty($template)) {
                $email['rm_name'] = $rm_details[0]['full_name'];
                $email['sf_name'] = ucfirst($sf_name);
                $email['on_off'] = $on_off_value;
                $subject = " Temporary " . $on_off_value . " Vendor " . $sf_name;
                $emailBody = vsprintf($template[0], $email);
                $this->notify->sendEmail($template[2], $to, $template[3], '', $subject, $emailBody, "");
            }

            log_message('info', __FUNCTION__ . ' Temporary  '.$on_off_value.' of Vendor' . $sf_name);
        }
        
        //Storing State change values in Booking_State_Change Table
        $this->notify->insert_state_change('', _247AROUND_VENDOR_SUSPENDED, _247AROUND_VENDOR_NON_SUSPENDED, 'Vendor ID = '.$id, $this->session->userdata('id'), $this->session->userdata('employee_id'),_247AROUND);
        redirect(base_url() . 'employee/vendor/viewvendor', 'refresh');
    }
    
    /**
     * @Desc: This function is used to show list of Documents uploaded for Vendors/ Used to Handle Filter Request
     * @params: void/ POST Array
     * @return: view
     * 
     */
    function show_vendor_documents_view(){
        //Getting RM Lists
        $rm = $this->employee_model->get_rm_details();

        if(!empty($this->input->post())){
            $data = $this->input->post();
            if($data['all_active'] == 'active'){
                $active = 1;
            }else{
                $active = "";
            }
            if($data['rm'] != 'all'){
                //Getting RM to SF Relation
                $sf_list = $this->vendor_model->get_employee_relation($data['rm']);
                $query = $this->vendor_model->viewvendor("", $active, $sf_list[0]['service_centres_id']);
            }else{
                $query = $this->vendor_model->viewvendor("", $active, '');
            }
            
            $this->load->view('employee/header/' . $this->session->userdata('user_group'));
            $this->load->view('employee/show_vendor_documents_view', array('data' => $query, 'rm' =>$rm,'selected'=>$data));
            
        }else{
            $query = $this->vendor_model->viewvendor("", "", "");
            
            $this->load->view('employee/header/' . $this->session->userdata('user_group'));
            $this->load->view('employee/show_vendor_documents_view', array('data' => $query, 'rm' =>$rm));
        }
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
    function download_pincode_latest_file(){
        //Getting latest entry form pincode_mapping_s3_upload_details table
        $latest_pincode_file = $this->vendor_model->getLatestVendorPincodeMappingFile();
        $filename = $latest_pincode_file[0]['file_name'];
        
        //s3 file path
        $file_path = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-pincodes/".$latest_pincode_file[0]['file_name'];
        
        //Downloading File
        if(!empty($latest_pincode_file[0]['file_name'])){

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"$filename\""); 
            readfile($file_path);
            exit;
        }else{
            //Logging_error
            log_message('info',__FUNCTION__.' No latest file has been found to be uploaded.');
        }
        
        
    }
    
    /**
     * @Desc: This function is used to login to particular vendor
     *          This function is being called using AJAX
     * @params: vendor id
     * @return: void
     * 
     */
    function allow_log_in_to_vendor($vendor_id){
        //Getting vendor details
        $vendor_details = $this->vendor_model->getVendorContact($vendor_id);
        $data['user_name'] = strtolower($vendor_details[0]['sc_code']);
        $data['password'] = md5(strtolower($vendor_details[0]['sc_code']));
    
         //Loggin to SF Panel with username and password
         
        $agent = $this->service_centers_model->service_center_login($data);
        if (!empty($agent)) {
        //get sc details now
        $sc_details = $this->vendor_model->getVendorContact($agent['service_center_id']);
            
        //Setting logging vendor session details
        
            $userSession = array(
	    'session_id' => md5(uniqid(mt_rand(), true)),
	    'service_center_id' => $sc_details[0]['id'],
	    'service_center_name' => $sc_details[0]['name'],
            'service_center_agent_id' => $agent['id'],
            'is_update' => $sc_details[0]['is_update'],
            'is_upcountry' => $sc_details[0]['is_upcountry'],
	    'sess_expiration' => 30000,
	    'loggedIn' => TRUE,
	    'userType' => 'service_center'
	);

        $this->session->set_userdata($userSession);

            //Saving Login Details in Database
            $login_data['browser'] = $this->agent->browser();
            $login_data['agent_string'] = $this->agent->agent_string();
            $login_data['ip'] = $this->input->ip_address();
            $login_data['action'] = _247AROUND_LOGIN;
            $login_data['entity_type'] = $this->session->userdata('userType');
            $login_data['agent_id'] = $this->session->userdata('service_center_agent_id');
            $login_data['entity_id'] = $this->session->userdata('service_center_id');

            $login_id = $this->employee_model->add_login_logout_details($login_data);
            //Adding Log Details
            if ($login_id) {
                log_message('info', __FUNCTION__ . ' Logging details have been captured for service center ' . $sc_details[0]['name']);
            } else {
                log_message('info', __FUNCTION__ . ' Err in capturing logging details for service center ' . $sc_details[0]['name']);
            }
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
        $state = $this->input->post('state');
        log_message('info', __FUNCTION__.' Used by :'.$this->session->userdata('employee_id'));
            $sc_charges_data = $this->service_centre_charges_model->get_service_centre_charges($state);
            //Looping through all the values 
            foreach ($sc_charges_data as $value) {
                //Getting Details from Booking Sources
                $booking_sources = $this->partner_model->get_booking_sources_by_price_mapping_id($value['partner_id']);
                $code_source = $booking_sources[0]['code'];
                
                //Calculating vendor base charge 
                $vendor_base_charge = $value['vendor_total']/(1+($value['rate']/100));
                //Calculating vendor tax - [Vendor Total - Vendor Base Charge]
                $vendor_tax = $value['vendor_total'] - $vendor_base_charge;
                
                $array_final['state'] = $state;
                $array_final['sc_code'] = $code_source;
                $array_final['product'] = $value['product'];
                $array_final['category'] = $value['category'];
                $array_final['capacity'] = $value['capacity'];
                $array_final['service_category'] = $value['service_category'];
                $array_final['vendor_basic_charges'] = round($vendor_base_charge,0);
                $array_final['vendor_tax_basic_charges'] = round($vendor_tax,0);
                $array_final['vendor_total'] = round($value['vendor_total'],0);
                $array_final['customer_net_payable'] = round($value['customer_net_payable'],0);
                $array_final['pod'] = $value['pod'];
                
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
            $R = new PHPReport($config);

            $R->load(array(

                     'id' => 'sc',
                    'repeat' => TRUE,
                    'data' => $final_array
                ));

            $output_file_dir = TMP_FOLDER;
            $output_file = ucfirst($state)."-Charges-List-" . date('j-M-Y');
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
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
            if ($searchOper == 'eq')
                $searchString = $searchString;
            if ($searchOper == 'bw' || $searchOper == 'bn')
                $searchString .= '%';
            if ($searchOper == 'ew' || $searchOper == 'en')
                $searchString = '%' . $searchString;
            if ($searchOper == 'cn' || $searchOper == 'nc' || $searchOper == 'in' || $searchOper == 'ni')
                $searchString = '%' . $searchString . '%';

            $where = "$searchField $ops '$searchString' ";
        }

        if (!$sidx)
            $sidx = 1;
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
                print_r($insert_id);
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
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
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
            if ($searchOper == 'eq')
                $searchString = $searchString;
            if ($searchOper == 'bw' || $searchOper == 'bn')
                $searchString .= '%';
            if ($searchOper == 'ew' || $searchOper == 'en')
                $searchString = '%' . $searchString;
            if ($searchOper == 'cn' || $searchOper == 'nc' || $searchOper == 'in' || $searchOper == 'ni')
                $searchString = '%' . $searchString . '%';

            $where = "$searchField $ops '$searchString' ";
        }

        if (!$sidx)
            $sidx = 1;
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
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/sc_upcountry_details',$data);
        
    }

    
    function check_unit_exist_in_sc($booking_id){
        $this->miscelleneous->check_unit_in_sc($booking_id);
    }

    /**
     * @Desc: This function is used to show Penalty booking form
     * @params: String (Booking ID)
     * @return:void
     */
    function get_escalate_booking_form($booking_id,$status,$penalty_active="") {
        //get escalation reasons for 247around
        if($status == 'Completed')
            $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'247around','active'=> '1','process_type'=>'report_complete'));
        else if($status == 'Cancelled')
            $data['escalation_reason'] = $this->vendor_model->getEscalationReason(array('entity'=>'247around','active'=> '1','process_type'=>'report_cancel'));

        $data['vendor_details'] = $this->vendor_model->getVendor($booking_id);
        $data['booking_id'] = $booking_id;
        $data['status'] = $status;
        if($penalty_active == 0 && $penalty_active != Null){
            $data['penalty_active'] = $penalty_active;
        }
        //print("<pre>".  print_r($data,true)."</pre>");exit();
        $this->load->view('employee/header/'.$this->session->userdata('user_group'));
        $this->load->view('employee/get_escalate_booking_form', $data);
    }
    
    /**
     * @Desc: This function is used to process Penalty form
     * @params: POST
     * @return : view
     * 
     */
    function process_get_vendor_escalation_form() {
        $escalation['booking_id'] = $this->input->post('booking_id');
        $status = $this->input->post('status');
        if($this->input->post('penalty_active') == 0){
            $penalty_active=$this->input->post('penalty_active');
        }
        $checkValidation = $this->checkValidationOnReason();
        if ($checkValidation) {
            $escalation['escalation_reason'] = $this->input->post('escalation_reason_id');
            $escalation['vendor_id'] = $this->input->post('vendor_id');
            //Getting date time slot of this booking
            $booking_date_timeslot = $this->vendor_model->getBookingDateFromBookingID($escalation['booking_id']);

            $booking_date = strtotime($booking_date_timeslot[0]['booking_date']);

            $escalation['booking_date'] = date('Y-m-d', $booking_date);
            $escalation['booking_time'] = $booking_date_timeslot[0]['booking_timeslot'];
            
                //Getting escalation reason
                //$escalation_policy_details = $this->vendor_model->getEscalationPolicyDetails($escalation['escalation_reason']);

                log_message('info', "Vendor_ID " . $escalation['vendor_id']);

                $escalation_reason = $this->vendor_model->getEscalationReason(array('id' => $escalation['escalation_reason']));
                $remarks = $this->input->post('remarks');
                if (!empty($remarks)) {
                    $escalation_reason_final = $escalation_reason[0]['escalation_reason'] . ' - ' . $remarks;
                } else {
                    $escalation_reason_final = $escalation_reason[0]['escalation_reason'];
                }

                //Now processing Penalty Operation on basic reason selection
                //We are making selection on basis of Escalation id choosen for Reason
                $value['booking_id'] = $escalation['booking_id'];
                $value['assigned_vendor_id'] = $escalation['vendor_id'];
                $value['agent_id'] = $this->session->userdata('id');
                $value['remarks'] = $escalation_reason_final;
                if($penalty_active == 0 && $penalty_active != Null){
                    $value['penalty_active'] =$penalty_active;
                }

                switch ($escalation['escalation_reason']) {
                    case INCENTIVE_CUT:
                        //Incentive Cut Option selected
                        log_message('info', 'Inside case of - ' . $escalation_reason[0]['escalation_reason']);
                        
                        $where = array('escalation_id' => INCENTIVE_CUT, 'active' => '1');
                        //Adding values in penalty on booking table
                        $penalty = $this->penalty_model->get_data_penalty_on_booking($value, $where);

                        log_message('info', 'Penalty added for Booking' . $escalation['booking_id'] . ' in penalty_on_booking');
                        //Setting validation success message
                        if(!empty($penalty)){
                            $this->session->set_userdata('success', 'Penalty added for Rescheduled without Reason - Booking id : '.$escalation['booking_id']);
                        }
                        else{
                            $this->session->set_userdata('failed', 'Error In adding penalty For Rescheduled without Reason - Booking id : '.$escalation['booking_id'].' Please Try Again');
                        }
                        break;
                    case PENALTY_FAKE_CANCEL:
                        //Penalty - Fake Cancel Option
                        log_message('info', 'Inside case of - ' . $escalation_reason[0]['escalation_reason']);
                       
                        $where = array('escalation_id' => PENALTY_FAKE_CANCEL, 'active' => '1');
                        //Adding values in penalty on booking table
                        $penalty = $this->penalty_model->get_data_penalty_on_booking($value, $where);

                        log_message('info', 'Penalty added for Booking' . $escalation['booking_id'] . ' in penalty_on_booking');
                        //Setting validation success message
                        if(!empty($penalty)){
                            $this->session->set_userdata('success', 'Penalty added for Fake Cancellation - Booking id : '. $escalation['booking_id']);    
                        }
                        else{
                            $this->session->set_userdata('failed', 'Error In adding penalty For Fake Cancellation - Booking id : '.$escalation['booking_id'].' Please Try Again');
                        }
                        break;

                    case PENALTY_FAKE_COMPLETE_CUSTOMER_WANT_INSTALLATION:
                        //Penalty - Fake Cancel Option
                        log_message('info', 'Inside case of - ' . $escalation_reason[0]['escalation_reason']);
                        
                        $where = array('escalation_id' => PENALTY_FAKE_COMPLETE_CUSTOMER_WANT_INSTALLATION, 'active' => '1');
                        //Adding values in penalty on booking table
                        $penalty = $this->penalty_model->get_data_penalty_on_booking($value, $where);

                        log_message('info', 'Penalty added for Booking' . $escalation['booking_id'] . ' in penalty_on_booking');
                        //Setting validation success message
                        if(!empty($penalty)){
                            $this->session->set_userdata('success', 'Penalty added for Fake Completion - Customer want Installation - Booking id : '.$escalation['booking_id']);
                        }
                        else{
                            $this->session->set_userdata('failed', 'Error In adding penalty for Fake Completion   - Customer want Installation - Booking id : '.$escalation['booking_id'].'Please Try Again');
                        }
                        break;
                    
                    case PENALTY_FAKE_COMPLETE_CUSTOMER_NOT_WANT_INSTALLATION:
                        //Penalty - Fake Cancel Option
                        log_message('info', 'Inside case of - ' . $escalation_reason[0]['escalation_reason']);
                     
                        $where = array('escalation_id' => PENALTY_FAKE_COMPLETE_CUSTOMER_NOT_WANT_INSTALLATION, 'active' => '1');
                        //Adding values in penalty on booking table
                        $penalty = $this->penalty_model->get_data_penalty_on_booking($value, $where);

                        log_message('info', 'Penalty added for Booking' . $escalation['booking_id'] . ' in penalty_on_booking');
                        //Setting validation success message
                        
                        if(!empty($penalty)){
                            $this->session->set_userdata('success', 'Penalty added for Fake Completion - Customer Not want Installation - Booking id : '.$escalation['booking_id']);
                        }
                        else{
                            $this->session->set_userdata('failed', 'Error In adding penalty for Fake Completion - Customer Not want Installation - Booking id : '.$escalation['booking_id'].' Please Try Again');
                        }
                        break;
                    default:
                        $penalty = [];
                        //Setting validation success message
                        $this->session->set_userdata('success', 'Report Submitted - Booking id : '.$escalation['booking_id']);
                        break;
                }
                
                //Getting template from Database
                $template = $this->booking_model->get_booking_email_template("penalty_on_booking");
                if (!empty($template)) {
                    $vendorContact = $this->vendor_model->getVendorContact($escalation['vendor_id']);
                    $to = $vendorContact[0]['primary_contact_email'] . ',' . $vendorContact[0]['owner_email'];
                    //From will be currently logged in user
                    $from = $this->employee_model->getemployeefromid($this->session->userdata('id'))[0]['official_email'];

                    //Getting RM Official Email details to send Welcome Mails to them as well
                    $rm_id = $this->vendor_model->get_rm_sf_relation_by_sf_id($escalation['vendor_id'])[0]['agent_id'];
                    $rm_official_email = $this->employee_model->getemployeefromid($rm_id)[0]['official_email'];

                    //Sending Mail
                    $email['penalty_amount'] = isset($penalty['penalty_amount']) ? $penalty['penalty_amount'] : 0;
                    $email['booking_id'] = $escalation['booking_id'];
                    $email['reason'] = $escalation_reason_final;
                    $emailBody = vsprintf($template[0], $email);

                    $subject['penalty_amount'] = isset($penalty['penalty_amount']) ? $penalty['penalty_amount'] : 0;
                    $subject['booking_id'] = $escalation['booking_id'];
                    $subjectBody = vsprintf($template[4], $subject);
                    $this->notify->sendEmail($from, $to, $template[3] . "," . $rm_official_email, '', $subjectBody, $emailBody, "");

                    //Logging
                    log_message('info', " Penalty Report Mail Send successfully" . $emailBody);
                } else {
                    //Logging Error Message
                    log_message('info', " Error in Getting Email Template for Penalty Report Mail");
                }

                redirect(base_url().'employee/booking/viewclosedbooking/'.$status);
           
        } else {
            $this->get_escalate_booking_form($escalation['booking_id'], $status);
        }
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
                $this->notify->sendEmail($from, $to, $template[3] . "," . $rm_official_email, '', $subjectBody, $emailBody, "");

                //Logging
                log_message('info', " Remove Penalty Report Mail Send successfully" . $emailBody);
            } else {
                //Logging
                log_message('info', __FUNCTION__ . ' Error in getting Email Template for remove_penalty_on_booking');
            }

            //Session success
            $this->session->set_userdata('success', 'Penalty removed - Booking id : ' . $booking_id[$key]);
            }   else {
            //Logging
                log_message('info', __FUNCTION__ . ' Penalty already Removed for Booking ID :' . $booking_id[$key]);
                $this->session->set_userdata('error', 'Penalty already Removed for Booking ID : ' . $booking_id[$key]);
            }
        }
    redirect(base_url() . 'employee/booking/viewclosedbooking/' . $status);
    }
    
    function get_penalty_details_data($booking_id, $status){
        
        $where  = array('booking_id'=>$booking_id,'active' => 1);
        $data['penalty_details'] = $this->penalty_model->get_penalty_on_booking_any($where);
        $this->load->view('employee/get_penalty_on_booking_details',array('penalty_details' => $data['penalty_details'], 'status'=>$status));
    }
    
    /**
     * @desc This method is used to update sub_service_center_details table via ajax call
     * @param void()
     * @return string
     */
    function update_sub_service_center_details(){
       if($this->input->post()){
           $data = array('district'=>$this->input->post('district'),
                         'pincode'=>$this->input->post('pincode'),
                         'upcountry_rate'=>$this->input->post('upcountry_rate'));
           $id = $this->input->post('id');
           $update_id = $this->upcountry_model->update_sub_service_center_upcountry_details($data,$id);
           if($update_id){
               echo "success";
           }
           else{
               echo "failed";
           }
       }
    }
    /**
     * @desc This method is used to delete sub office details in sub_service_center_details table via ajax call
     * @param void()
     * @return string
     */
    function delete_sub_service_center_details(){
       if($this->input->post()){
           $id = $this->input->post('id');
           $update_id = $this->upcountry_model->delete_sub_service_center_upcountry_details($id);
           if($update_id){
               echo "success";
           }
           else{
               echo "failed";
           }
       }
    }

}
