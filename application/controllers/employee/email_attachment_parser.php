<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}


ini_set('memory_limit', '-1');
ini_set('max_execution_time', 360000);

class Email_attachment_parser extends CI_Controller {
    
    function __Construct() {
        parent::__Construct();
        
        $this->load->helper(array('form', 'url', 'file'));
        $this->load->dbutil();
        
        $this->load->model('around_scheduler_model');
        $this->load->library('email_data_reader');
        $this->load->library("miscelleneous");
    }
    
    /**
    * @desc     Get email attachment according to the search criteria
    *           And then upload the attachment file data to server
    * @param    void
    * @return   void
    */
    function read_email_attachments() { 
        log_message('info', __METHOD__ . " Entering...");
        $mail_server = SMS_DEACTIVATION_MAIL_SERVER;
        $email = EMAIL_ATTACHMENT_READER_EMAIL;
        $password = EMAIL_ATTACHMENT_READER_PASSWORD;
        ///create email connection
        $conn = $this->email_data_reader->create_email_connection($mail_server, $email, $password);
        if ($conn != 'FALSE') {
            log_message('info', __METHOD__ . " Email connection created successfully.");
            $email_search_condition = 'UNSEEN';
            //$email_search_condition = 'SUBJECT "'.$value['email_subject_text'].'" FROM "'.$value['email_received_from'].'" UNSEEN';
            //get the email list according to search condition
            $email_list = $this->email_data_reader->get_emails($email_search_condition);
            if (!empty($email_list)) {
                foreach ($email_list as $val) {
                    if (!empty($val['attachments'])) {
                        foreach ($val['attachments'] as $v) {
                            $extract_file_name = $v['file_name'];
                            if (!empty($extract_file_name)) {
                                if (file_exists(TMP_FOLDER . $extract_file_name)) {
                                    //send attachment to this url for processing
                                    $file_details = $this->get_url_to_upload_file($val);
                                    if (!empty($file_details)) {
                                        $ext = pathinfo($extract_file_name, PATHINFO_EXTENSION);
                                        if (!empty($ext)) {
                                            //if file is in zip format then extract file and convert(only if it is in csv format) it into xlsx
                                            if ($ext === 'zip') {
                                                $response = $this->miscelleneous->extract_zip_files(TMP_FOLDER . $extract_file_name, TMP_FOLDER);
                                                if ($response['status']) {
                                                    $extract_file_name = $response['file_name'];
                                                    $ext = pathinfo($extract_file_name, PATHINFO_EXTENSION);
                                                    if ($ext === 'csv') {
                                                        $objReader = PHPExcel_IOFactory::createReader('CSV');
                                                        $objPHPExcel = $objReader->load(TMP_FOLDER . $extract_file_name);
                                                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                                                        $objWriter->save(TMP_FOLDER . pathinfo($extract_file_name, PATHINFO_FILENAME) . ".xlsx");
                                                        $extract_file_name = pathinfo($extract_file_name, PATHINFO_FILENAME) . ".xlsx";
                                                        $res1 = 0;
                                                        system(" chmod 777 " . TMP_FOLDER . pathinfo($extract_file_name, PATHINFO_FILENAME) . ".xlsx", $res1);
                                                        unlink(TMP_FOLDER . pathinfo($extract_file_name, PATHINFO_FILENAME) . ".csv");
                                                    }
                                                }
                                            }
                                            $file_details['file_email_subject'] = $val['subject'];
                                            $file_upload_response = $this->process_uploading_extract_file($file_details['url'], TMP_FOLDER . $extract_file_name, $val['email_message_id'], $file_details);
                                            log_message('info',TMP_FOLDER . $extract_file_name);
                                            //delete file from the system after processing
                                            if (file_exists(TMP_FOLDER . $extract_file_name)) {
                                                $res1 = 0;
                                                system(" chmod 777 " . TMP_FOLDER.$extract_file_name, $res1);
                                                unlink(TMP_FOLDER . $extract_file_name);
                                            }
                                            if (file_exists(TMP_FOLDER . $v['file_name'])) {
                                                $res1 = 0;
                                                system(" chmod 777 " . TMP_FOLDER.$v['file_name'], $res1);
                                                unlink(TMP_FOLDER . $v['file_name']);
                                            }
                                            //set flag to read after processing the attachment
                                            $status = imap_setflag_full($conn, $val['email_no'], "\\Seen");
                                            if ($status) {
                                                log_message('info', 'Email flag set to seen');
                                            } else {
                                                log_message('info', 'error in setting email flag to seen');
                                            }
                                        }
                                    }
                                } else {
                                    log_message('info', __METHOD__ . "Attachment Exist But File Not Found in the system for email " . $val['email_message_id']);
                                    $subject = "Attachment Exist But File Not Found In the System for " . $val['subject'];
                                    $msg = "Attachment Exist But File Not Found In the System for " . $val['subject'];
                                    $msg .= "<br><b>Search Condition </b> : " . $email_search_condition;
                                    $msg .= "<br><b>File Name </b> : " . $extract_file_name;
                                    $this->notify->sendEmail(NOREPLY_EMAIL_ID, _247AROUND_SALES_EMAIL, DEVELOPER_EMAIL, "", $subject, $msg, "",ATTACHMENT_EXIST_FILE_NOT_FOUND_IN_SYSTEM);
                                }
                            }
                        }
                    } else {
                        log_message('info', __METHOD__ . " attachment not found for email " . print_r($val['email_message_id'], true));
                        $subject = "Attachment Not Found for HOST:" . $val['host']." Subject: ".$val['subject'];
                        $msg = "Email attachment not found for the subject " . $val['subject']." Host: ".$val['host'];
                        $msg .= "<br><b>Search Condition: </b> " . $email_search_condition;
                        $this->notify->sendEmail(NOREPLY_EMAIL_ID, _247AROUND_SALES_EMAIL, DEVELOPER_EMAIL, "", $subject, $msg, "",ATTACHMENT_NOT_FOUND);
                    }
                }
            } else {
                log_message('info', __METHOD__ . " No Email Found for search condition " . $email_search_condition);
            }
            //close email connection
            $this->email_data_reader->close_email_connection();
        } else {
            log_message('info', __METHOD__ . "Error in creating email connection");
            $subject = "Error in creating email connection for attachment parser";
            $msg = "There was some error in creating connection to email server for extracting the attachemnt from email.";
            $msg .= "<br><b>File Name: </b> " . __CLASS__;
            $msg .= "<br><b>Function Name: </b> " . __METHOD__;
            $this->notify->sendEmail(NOREPLY_EMAIL_ID, DEVELOPER_EMAIL, '', "", $subject, $msg, "",ERROR_IN_CREATING_EMAIL_CONNECTION);
        }
    }

    /**
    * @desc     It is used to send the extract file to respective controller to 
    *           process the file data
    * @param    void
    * @return   void
    */
    private function process_uploading_extract_file($url,$file_path,$email_message_id,$file_details){
        log_message('info',__METHOD__."Entering...");
        
        if (function_exists('curl_file_create')) {
            $cFile = curl_file_create($file_path);
        } else { 
            $cFile = '@' . realpath($file_path);
        }
        
        //data to send
        $post = array(
            'file' => $cFile,
            'file_received_date' => date('Y-m-d'),
            'email_message_id' => $email_message_id,
            'email_send_to' => $file_details['email_send_to'],
            'file_type' => $file_details['file_type'],
            'partner_id' => $file_details['partner_id'],
            'partner_source' => $file_details['file_type']."-excel",
            'is_file_send_back' => $file_details['is_file_send_back'],
            'file_read_column' => $file_details['file_read_column'],
            'file_write_column' => $file_details['file_write_column'],
            'revert_file_email' => $file_details['revert_file_email'],
            'file_email_subject' => $file_details['file_email_subject'],
            );
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        
        $result = curl_exec($ch);
        // get HTTP response code
        //$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $result;
    }
    
    /**
    * @desc     This function is used to get the details of uploaded file from database
    * @param    void
    * @return   void
    */
    function get_url_to_upload_file($data){
        $return_data = array();
        $url_details = $this->around_scheduler_model->get_data_for_parsing_email_attachments(array('email_host' => $data['host']));
        if(!empty($url_details)){
            $return_data['partner_id'] = $url_details[0]['partner_id'];
            $return_data['email_send_to'] = $url_details[0]['email_send_to'];
            $return_data['file_type'] = $url_details[0]['file_type'];
            $return_data['is_file_send_back'] = $url_details[0]['send_file_back'];
            $return_data['file_read_column'] = $url_details[0]['order_id_read_column'];
            $return_data['file_write_column'] = $url_details[0]['booking_id_write_column'];
            $return_data['revert_file_email'] = $url_details[0]['revert_file_to_email'];
            
            if((stripos($data['subject'],$url_details[0]['email_subject_text']) !== FALSE) && $data['host'] == '247around.com'){
                $return_data['url'] = base_url().'employee/do_background_upload_excel/upload_snapdeal_file';
            }else{
                $return_data['url'] = base_url().$url_details[0]['email_function_name'];
            }
        }
        
        return $return_data;
    }
}