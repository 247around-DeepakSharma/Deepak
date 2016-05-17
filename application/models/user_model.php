<?php

class User_model extends CI_Model{

/**
* @desc load both db
*/
function __construct(){
parent::__Construct();

$this->db_location = $this->load->database('default1', TRUE,TRUE);
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
        $this->db->select('save_used_handyman.id,save_used_handyman.device_id,
              save_used_handyman.handyman_id,users.name,users.phone_number,
              handyman.service_id,handyman.profile_photo,handyman.phone,services.services,
              handyman.name as handyman_name');
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
    function search_user($phone_number) {
      $this->db->select("*");
      $this->db->where('phone_number', $phone_number);

      $query = $this->db->get("users");
      return $query->result_array();
    }


    /*function total_user_count($userName) {
       $this->db->select('user_id');
       $this->db->like('name', $userName); 
       $this->db->from('users');
       $query = $this->db->get();
       $result = $query->result_array();
        return count($result);
    }*/

    function get_searched_user($userName){
       $this->db->select('*');
       $this->db->like('name', $userName); 
       $this->db->from('users');
       $query = $this->db->get();
       $result = $query->result_array();
        if ($query->num_rows() > 0) {
           return $result;
        }
      return false;

    }

    /** @description : Function to search bookings with booking id from find user page
     *  @param : booking id
     *  @return : array(matching bookings)
     */
    function search_bookings_by_booking_id($booking_id) {
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

    /** @description : Function to add new user
     *  @param : user deyails
     *  @return : id of inserted user
     */
    function add_user($user) {

         $this->db->insert('users', $user);

        $id = $this->db->insert_id();

        return $id;
      }

    /** @description : Search user and booking details for a particular user through his phone number, also for pagination
     *  @param : phone no, start and limit
     *  @return : array of user and booking details
     */
  function booking_history($phone_number, $limit, $start) {
    $sql = "Select services.services, users.user_id, users.city, users.state, users.phone_number, users.user_email, users.home_address, users.name, users.pincode, booking_details.booking_id,"
            . "booking_details.booking_date, booking_details.booking_timeslot, booking_details.current_status,"
            . "booking_details.closed_date from booking_details, users,services where "
            . "users.phone_number='$phone_number' and booking_details.user_id=users.user_id and "
            . "services.id=booking_details.service_id LIMIT $start, $limit";
    $query=$this->db->query($sql);
    return $query->result_array();
  }

    /** @description : Function to edit user's details
     *  @param : user's details to be updated
     *  @return : void
     */

    function edit_user($edit) {
      $this->db->where('user_id', $edit['user_id']);
      $this->db->update('users',$edit);
        //return $sql;
  }
    /* @description : Function to get appliance details and user details for users booking history page(appliance wallet)
     *  @param : phone no
     *  @return : array(user and appliance details)
     */

    //
    function appliance_details($phone_number) {
    $query=$this->db->query("SELECT users.user_id ,users.name,services.id as service_id, 
      services.services, appliance_details.id, appliance_details.brand, appliance_details.category,
       appliance_details.capacity, appliance_details.tag, appliance_details.model_number, 
       appliance_details.purchase_month,appliance_details.purchase_year FROM services, users, 
       appliance_details WHERE users.phone_number='$phone_number' AND users.user_id= 
       appliance_details.user_id AND services.id = appliance_details.service_id");
    return $query->result_array();
  }

  /**
   * @desc : this function is used to get unique user or unique user in months, completed booking, cancelled booking
   * @param : Array(city, source, type(unique user in table or unique user month wise))
   * @return : Array()
   */
  function get_count_user($data){

    $where = "";

    $result = $this->GroupBYForUserCount($data);

    //  city or date range not empty
    if($data['city'] !="" || $data['source'] !="" ){
        $where .=" where `user_id` !='' "; // user_id filed is not empty
    }
    
    // Array key city is not empty
    if($data['city'] != ''){ 

      $where .= "AND `city` = '".$data['city']."'";
    }

    if($data['source'] !=""){
        $where .= " AND source = '". $data['source']."'";
    }
   

    $sql = "SELECT $result[month]  count(Distinct `booking_details`.`user_id`) as total_user, 

               SUM( CASE WHEN `current_status` = 'Completed' THEN 1 ELSE 0 END) AS completed_booking_user,
               SUM( CASE WHEN `current_status` = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_booking_user
               

               FROM `booking_details` $where  $result[group_By]";

    $query1 = $this->db->query($sql);

    return $query1->result_array();


  }

  function GroupBYForUserCount($data){
  
    $user['group_By'] = "";
    $user['month'] = "";
    // Month Wise DataSet
    if($data['type'] == "All Month"){
         // get group by create date column.
         $user['group_By'] = " GROUP BY DATE_FORMAT(`create_date`, '%M, %Y') ORDER BY DATE_FORMAT(`create_date`,'%Y') DESC , DATE_FORMAT(`create_date`,'%m') DESC";
         $user['month'] = "DATE_FORMAT(`create_date`,'%M, %Y') `month`,";
    }
    
    // Year Wise Dataset
    if($data['type'] == "All Year"){
        // get group by create date column.
        $user['group_By'] = " GROUP BY DATE_FORMAT(`create_date`, '%Y') ORDER BY DATE_FORMAT(`create_date`, '%Y') DESC";
        $user['month'] = "DATE_FORMAT(`create_date`, '%Y') `month`,";
    }

     // Week Wise Dataset
    if($data['type'] == "Week"){
        // get group by create date column.
        $user['group_By'] = " GROUP BY WEEK(`create_date`)  ORDER BY DATE_FORMAT(`create_date`,'%Y') DESC , DATE_FORMAT(`create_date`,'%m') DESC";
        $user['month'] = "  CONCAT(date(create_date), ' - ', date(create_date) + INTERVAL 7 DAY)   `month`,";
    }
    
    //Quater Wise DataSet
    if($data['type']== 'Quater'){
        $user['group_By'] .= " GROUP BY Year(`create_date`) Desc, QUARTER(`create_date`) DESC";
        $user['month'] = " CASE QUARTER(`create_date`) 

        WHEN 1 THEN 'Jan - Mar'

        WHEN 2 THEN 'Apr - Jun'
 
        WHEN 3 THEN 'July - Sep'

        WHEN 4 THEN 'Oct - Dec'

        END AS `month` ,  Year(`create_date`) as year, ";
    }

    return $user;
  }

    
  
  function getBookingId_by_orderId($partner_id, $order_id){
   
    $booking = array();

    $partner_code = $this->partner_model->get_source_code_for_partner($partner_id);

    $union = "";
    if($partner_code == "SS"){
         $union = "UNION 

                  SELECT CRM_Remarks_SR_No as booking from snapdeal_leads where Sub_Order_ID = '$order_id'";
    }
     
    $query = $this->db->query("SELECT 247aroundBookingID  as booking from partner_leads where OrderID = '$order_id' And  PartnerID = '$partner_id' $union");

    $data = $query->result_array();
    
    if (count($data) > 0) {

      foreach ($data as $value) {

        $booking_data = $this->search_bookings_by_booking_id($value['booking']);

        if(count($booking_data) > 0 ){
          array_push($booking, $booking_data[0]);
        }
      }
      return $booking;
   
    } else {

       return $booking;
    }
    
  }

  function get_city_source(){
    $query1['city'] = $this->vendor_model->get_city();
    $query2['source'] = $this->partner_model->get_all_partner_source();
    return array_merge($query1, $query2);
  }
      // end of model
}

