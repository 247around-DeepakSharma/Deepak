<?php

class Web_user_model extends CI_Model{

/**
* @desc load both db
*/
function __construct(){
parent::__Construct();

$this->db = $this->load->database('default', TRUE,TRUE);
}


  /**
   * @desc : This funtion count total no of user
   * @param : void
   * @return : total no user
   */

   function total_user(){

    return $this->db->count_all_results("users");
   }


   /** @description* This funtion get all user
    *  @param : limit (between 10)
    *  @return :  array (user details)
    */
    function getuser($limit, $start){
      $this->db->limit($limit, $start);
      $query = $this->db->get('users');
      if ($query->num_rows() > 0) {
        return $query->result_array();
      }
      return false;
      }

      /** @description* This funtion get user id to hide user information of this id
      *  @param : user id and update action for user not active
      *  @return : update action
      */
      function  removeuser($user_id,$updateAction){

        $this->db->where('user_id', $user_id);
        $this->db->update('users',$updateAction);
        $getuseremail  = $this->getusername($user_id);
        return $getuseremail[0]['user_email'];

      }

   /** @description* This funtion get user email from id
    *  @param : user id
    *  @return : array(name)
    */

      function getusername($user_id){
        $this->db->select('user_email');
        $this->db->where('user_id',$user_id);
        $query =  $this->db->get('users');
        return $query->result_array();
      }

     /** @description* This funtion get user repot
      *  @param : void
      *  @return : array()
      */

      function getuserreport(){
        $this->db->select('save_used_handyman.id,save_used_handyman.device_id,save_used_handyman.handyman_id,users.name,users.phone_number,handyman.service_id,handyman.profile_photo,handyman.phone,services.services,handyman.name as handyman_name');
    	  //$this->db->where('isreport_active',1);
        $this->db->from('save_used_handyman');
    	  $this->db->where('type','report');
    	  $this->db->join('users','users.device_id = save_used_handyman.device_id');
    	  $this->db->join('handyman','handyman.id = save_used_handyman.handyman_id');
    	  $this->db->join('services','services.id = handyman.service_id');
    	  $query = $this->db->get();
    	  return $query->result_array();

      }

    /** @description* This funtion  get marketting message
      *  @param : void
      *  @return : message
      */

      function getmail_message(){
        $query = $this->db->get('marketing_mail');
        return $query->result_array();

      }

     /** @description* This funtion  update mail message
      *  @param :  comment
      *  @return : void
      */

      function mail_messageSave($data){
        $this->db->where('id',1);
        $this->db->update('marketing_mail',$data);

      }

     /** @description* This funtion  get user email
      *  @param :  void
      *  @return : array(email)
      */

      function get_email(){
        $this->db->select('user_email');
        $query = $this->db->get('users');
        return $query->result_array();
      }


     /** @description* This funtion  insert comment
      *  @param :  id , comment
      *  @return : void
      */

      function add_comment_report($id,$data){
        $this->db->where('id',$id);
        $this->db->update('save_used_handyman',$data);

      }

     /** @description* when deactivate user(report) handyman get unverified
      *  @param : id (user report in save handyman )
      *  @return : array()
      */

      function add_verificationlist($id){
        $this->db->select('save_used_handyman.handyman_id');
        $this->db->where('save_used_handyman.id',$id);
        $this->db->from('save_used_handyman');
        $this->db->join('handyman','handyman.id = save_used_handyman.handyman_id');
        $query  = $this->db->get();
        $result = $query->result_array();
        $handymanid['id']   = $result[0]['handyman_id'];
        $update['action']   = '0';
        $update['approved'] = '0';
        $update['verified'] = '0';
        $this->db->where($handymanid);
        $this->db->update('handyman',$update);

      }

      /** @description* This funtion get user from phone number
    *  @param : phone no
    *  @return : array(phone no)
    */
      //Function to search user with phone no
      function search_user($phone_number)
      {

        $sql="Select user_id,user_email,phone_number,name,home_address from users
           where phone_number='$phone_number'";
        $query=$this->db->query($sql);

        return $query->result_array();
      }

      //Function to search user with booking id
      function search_user_by_booking_id($booking_id)
      {
        $query = $this->db->query("Select services.services,
            users.name as customername, users.phone_number,
            booking_details.*, service_centres.name as service_centre_name,
            service_centres.primary_contact_name,service_centres.primary_contact_phone_1
            from booking_details
            JOIN  `users` ON  `users`.`user_id` =  `booking_details`.`user_id`
            JOIN  `services` ON  `services`.`id` =  `booking_details`.`service_id`
            LEFT JOIN  `service_centres` ON  `booking_details`.`assigned_vendor_id`
            = `service_centres`.`id` WHERE booking_id like '%$booking_id%'"
        );

        return $query->result();
      }

      function add_user($user)
      {
         $this->db->insert('users', $user);

        $id = $this->db->insert_id();

        return $id;
      }

      function booking_history($phone_number, $limit, $start) {
      $sql = "Select services.services, users.user_id, users.home_address, users.name, booking_details.booking_id,"
            . "booking_details.booking_date, booking_details.booking_timeslot, booking_details.current_status,"
            . "booking_details.closed_date from booking_details, users,services where "
            . "users.phone_number='$phone_number' and booking_details.user_id=users.user_id and "
            . "services.id=booking_details.service_id LIMIT $start, $limit";
        $query=$this->db->query($sql);
    return $query->result_array();
  }
  function view($phone_number)
      {
      $sql="Select services.services,users.user_id,users.home_address,users.name,booking_details.booking_id,booking_details.booking_date,booking_details.booking_timeslot,booking_details.current_status,booking_details.closed_date from booking_details,users,services where users.phone_number='$phone_number' and booking_details.user_id=users.user_id and services.id=booking_details.service_id";

    $query=$this->db->query($sql);

    return $query->result_array();
  }

  public function viewbooking()
  {
    $query = $this->db->query("Select services.services,users.name,booking_details.user_id,booking_details.service_id,booking_details.booking_id,booking_details.booking_date,booking_details.booking_timeslot,booking_details.current_status,booking_details.closed_date from booking_details,users,services where booking_details.user_id=users.user_id and services.id=booking_details.service_id");

      return $query->result();
  }

  public function total_booking()
   {
      return $this->db->count_all_results("booking_details");
   }


   function cancelreason()
    {
      $query=$this->db->query("Select id,reason from booking_cancellation_reasons");
      return $query->result();
    }

   function cancel_booking($booking_id,$data)
   {



    $sql = "Update booking_details set update_date='".$data['update_date']."',cancellation_reason='".$data['cancellation_reason']."',current_status='Cancelled' where booking_id='$booking_id' and (current_status='Pending' or current_status='Rescheduled')";

    $query=$this->db->query($sql);

    return $query;
   }

   function getbooking($booking_id){

    /*
    $query=$this->db->query("Select services.services,users.name,booking_details.user_id,booking_details.service_id,booking_details.booking_id,booking_details.booking_date,booking_details.booking_timeslot,booking_details.current_status,booking_details.closed_date from booking_details,users,services where booking_details.user_id=users.user_id and services.id=booking_details.service_id");

    return $query;

    */

    $this->db->select('*');
   // $this->db->where('action', '1');
    $this->db->where('booking_id', $booking_id);
    $query = $this->db->get('booking_details');
    return $query->result_array();


  }

  function complete_booking($booking_id,$data)
  {
    $sql="Update booking_details set current_status='Completed',closed_date='$data[closed_date]', closing_remarks='$data[closing_remarks]',total_price='$data[total_price]' where booking_id='$booking_id' and (current_status='Rescheduled' or current_status='Pending')";
    //echo "<pre>";print_r($sql);exit;
    $query=$this->db->query($sql);
    return $query;
  }

  function reschedule_booking($booking_id,$data)
  {
    $query=$this->db->query("Update booking_details set current_status='Rescheduled',update_date='$data[update_date]',booking_date='$data[booking_date]',booking_timeslot='$data[booking_timeslot]' where booking_id='$booking_id' and (current_status='Pending' or current_status='Rescheduled')");
    //echo "<pre>";print_r($query);exit;
    return $query;
  }





  function user_details()
  {
    $sql="Select users.name,users.home_address,users.phone_number,users.user_email,booking_details.booking_id from users,booking_details where users.user_id=booking_details.user_id   ";
  }
  function edit_user($edit)
  {
    $sql=$this->db->query("Update users set name='$edit[name]',home_address='$edit[home_address]',phone_number='$edit[phone_number]',user_email='$edit[user_email]' where user_id='$edit[user_id]'");
    return $sql;
  }
  function appliance_details($phone_number)
  {
    $query=$this->db->query("SELECT users.user_id ,users.name,services.id as service_id,
      services.services, appliance_details.id, appliance_details.brand, appliance_details.category,
       appliance_details.capacity, appliance_details.tag, appliance_details.model_number,
       appliance_details.purchase_month,appliance_details.purchase_year FROM services, users,
       appliance_details WHERE users.phone_number='$phone_number' AND users.user_id=
       appliance_details.user_id AND services.id = appliance_details.service_id");
    return $query->result_array();
  }
      // end of model
}

