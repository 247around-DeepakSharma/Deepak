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
}