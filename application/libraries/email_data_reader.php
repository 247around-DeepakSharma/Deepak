<?php
/*
 * @desc - This class is used to read email data with the help of php script
 */
class email_data_reader  {
    var $connection;
    
    function __construct() {
       
    }
    
    /*
 * @desc - This function create connection for email account 
   *@input - Server(String),email(String),password(string)
    *@output - Set connection object to global variable and returns connection on success and false on failure
 */
    function create_email_connection($mail_server,$email,$password){
        $this->connection = imap_open(trim($mail_server),trim($email),$password);
        return $this->connection;
    }
    
    function close_email_connection(){
        imap_close($this->connection);
    }
    
    /*
 * @desc - This function returns total number of emails for open connection
 */
    function get_total_number_of_emails(){
        return imap_num_msg($this->connection);
    }
    
     /*
 * @desc - This function get all data from email account within given counts
 */
    function fetch_emails_between_two_counts($total_emails_count,$processed_emails_count=0){
        for ($i=$processed_emails_count;$i<=$total_emails_count;$i++){
            $email_data_holder[] = imap_fetch_overview($this->connection, $i, 0);
        }
        return $email_data_holder;
    }  
       
     /*
 * @desc - This function use to fetch emails between 2 dates
 * @input - to_date(string),from_date(string)
   @output - email_data_holder(array)
 */
    function fetch_emails_between_two_dates($to_date,$from_date){
        $email_numbers = imap_search($this->connection, 'SINCE "'.date('d M Y',strtotime($from_date)).'" BEFORE "'.date('d M Y',strtotime($to_date)).'"', SE_UID);
        $count = count($email_numbers);
        $email_data_holder = array();
        for ($i=$email_numbers[0];$i<=$email_numbers[$count-1];$i++){
            $email_data_holder[] = imap_fetch_overview($this->connection, $i, 0);
        }
        return $email_data_holder;
    }
    
    /**
    * @desc     Get email according to the search criteria
    *           By Default It search All email
    * @param    $search_criteria  string
    * @return   $response   array 
    */
    public function get_emails($search_criteria = "ALL"){
        $response = array();
        $emails = imap_search($this->connection, $search_criteria);
        if(!empty($emails)){
            //sort $emails to get the latest data first
            rsort($emails);
            $no_of_emails = $emails ? count($emails) : 0;
            for ($i = 0; $i < $no_of_emails; $i++) {
                
                $attachments = array();
                $return_attachments_details = array();
                //get email headers
                $header = imap_header($this->connection, $emails[$i]);
                //get unique email message_id
                $message_id = $header->message_id;
                // get email send from
                $from = $header->fromaddress;
                //get email subject
                $subject = $header->subject;
                //get email host
                $host = $header->from[0]->host;
                //get email structure
                $structure = imap_fetchstructure($this->connection, $emails[$i]);
                //if structure is not empty then get email body
                if (!empty($structure->parts)) {
                    for ($j = 0, $k = count($structure->parts); $j < $k; $j++) {
                        $attachments[$j] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );
                        if(isset($structure->parts[$j]->ifdparameters) && $structure->parts[$j]->ifdparameters) {
                            foreach($structure->parts[$j]->dparameters as $object) {
                                    if(strtolower($object->attribute) == 'filename') {
                                            $attachments[$j]['is_attachment'] = true;
                                            $attachments[$j]['filename'] = $object->value;
                                    }
                            }
                        }
                        
                        if(isset($structure->parts[$j]->ifparameters) && $structure->parts[$j]->ifparameters) {
                                foreach($structure->parts[$j]->parameters as $object) {
                                        if(strtolower($object->attribute) == 'name') {
                                                $attachments[$j]['is_attachment'] = true;
                                                $attachments[$j]['name'] = $object->value;
                                        }
                                }
                        }
                        
                        if(!empty($attachments) && $attachments[$j]['is_attachment']) {
                                $attachments[$j]['attachment'] = imap_fetchbody($this->connection, $emails[$i], $j+1);
                                if($structure->parts[$j]->encoding == 3) { // 3 = BASE64
                                        $attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);
                                }
                                elseif($structure->parts[$j]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                                        $attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);
                                }
                        }
                        
                        $part = $structure->parts[$j];
                        if ($part->subtype == 'PLAIN') {
                            $body = imap_fetchbody($this->connection, $emails[$i], $j + 1);
                        }else{
                            $body = imap_body($this->connection, $emails[$i]);
                        }
                        
                        
                        /* iterate through each attachment and save it */
                        foreach($attachments as $key => $attachment)
                        {
                            $filename = "";
                            if($attachment['is_attachment'] == 1)
                            {
                                $filename = str_replace(array(" ","-"), "_", $attachment['filename']);
                                if(empty($filename))
                                {
                                    $filename = str_replace(array(" ","-"), "_", $attachment['filename']);
                                }
                                write_file(TMP_FOLDER.$filename, $attachment['attachment']);
                            }
                            
                            $return_attachments_details[$key] = array(
                                'file_name' => $filename
                            );
                        }

                    }
 
                } else {
                    $body = imap_body($this->connection, $emails[$i]);
                }
                
                //make return array
                array_push($response, array('email_message_id'=>$message_id, 'email_no' => $emails[$i], 'from' => $from,'host' => $host, 'subject' => $subject, 'body' => $body,'attachments' =>$return_attachments_details));
            }
        }
        
        return $response;
    }
}