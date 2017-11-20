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
    function read_email_attachments(){
        log_message('info',__METHOD__." Entering...");
        $mail_server = SMS_DEACTIVATION_MAIL_SERVER;
        $email = EMAIL_ATTACHMENT_READER_EMAIL;
        $password = EMAIL_ATTACHMENT_READER_PASSWORD;
        //get data from the database to read the email according to that
        $data = $this->around_scheduler_model->get_data_for_parsing_email_attachments(array('active' => 1));
        if(!empty($data)){
            //create email connection
            $conn = $this->email_data_reader->create_email_connection($mail_server,$email,$password);
            if($conn != 'FALSE')
            {
                log_message('info',__METHOD__." Email connection created successfully.");
                
                foreach ($data as $value)
                {
                    $email_search_condition = 'SUBJECT "'.$value['email_subject_text'].'" FROM "'.$value['email_received_from'].'" UNSEEN';
                    //get the email list according to search condition
                    $email_list = $this->email_data_reader->get_emails($email_search_condition);
                    if(!empty($email_list))
                    {
                        foreach ($email_list as $val)
                        {
                            if(!empty($val['attachments']))
                            {
                                foreach ($val['attachments'] as $v)
                                {
                                    $extract_file_name =  $v['file_name'];
                                    if(!empty($extract_file_name) && file_exists(TMP_FOLDER.$extract_file_name))
                                    {
                                        $url = base_url().$value['email_function_name'];
                                        $ext = pathinfo($extract_file_name,PATHINFO_EXTENSION);
                                        if(!empty($ext)){
                                            if($ext === 'zip')
                                            { 
                                                $response = $this->miscelleneous->extract_zip_files(TMP_FOLDER.$extract_file_name,TMP_FOLDER);
                                                if($response['status'])
                                                {
                                                    $extract_file_name = $response['file_name'];
                                                    $ext = pathinfo($extract_file_name,PATHINFO_EXTENSION);
                                                    if($ext === 'csv')
                                                    {
                                                        $objReader = PHPExcel_IOFactory::createReader('CSV');
                                                        $objPHPExcel = $objReader->load(TMP_FOLDER.$extract_file_name);
                                                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                                                        $objWriter->save(TMP_FOLDER.pathinfo($extract_file_name,PATHINFO_FILENAME).".xlsx");
                                                        $extract_file_name = pathinfo($extract_file_name,PATHINFO_FILENAME).".xlsx";
                                                        $res1 =0;
                                                        system(" chmod 777 " . TMP_FOLDER.pathinfo($extract_file_name,PATHINFO_FILENAME).".xlsx" , $res1);
                                                    }
                                                }
                                            }
                                            $file_upload_response = $this->process_uploading_extract_file($url,TMP_FOLDER.$extract_file_name,$val['email_message_id']);
                                            if(file_exists(TMP_FOLDER.$extract_file_name))
                                            {
                                                unlink(TMP_FOLDER.$extract_file_name);
                                            }
                                            $status = imap_setflag_full($conn,$val['email_no'],"\\Seen");
                                            if($status)
                                            {
                                                log_message('info','Email flag set to seen');
                                            }
                                            else
                                            {
                                                log_message('info','error in setting email flag to seen');
                                            }
                                        } 
                                    }else{
                                        log_message('info',__METHOD__."File Does Not Exist for email ".$val['email_no']);
                                    }
                                }
                            } 
                            else 
                            {
                                log_message('info',__METHOD__." attachment not found for email ". print_r($val['email_no'],true));
                            }
                        }
                    }
                    else
                    {
                        log_message('info',__METHOD__." No Email Found for search condition ".$email_search_condition);
                    }     
                }
                
                //close email connection
                $this->email_data_reader->close_email_connection();
            }
            else
            {
                log_message('info',__METHOD__."Error in creating email connection");
            }
        }
    }
    
    private function process_uploading_extract_file($url,$file_path,$email_message_id){
        log_message('info',__METHOD__."Entering...");
        
        if (function_exists('curl_file_create')) {
            $cFile = curl_file_create($file_path);
        } else { // 
            $cFile = '@' . realpath($file_path);
        }
        
        $post = array(
            'file' => $cFile,
            'file_received_date' => date('Y-m-d'),
            'email_message_id' => $email_message_id);        
        
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
}