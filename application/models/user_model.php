<?php

class User_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
        $this->load->model('reusable_model');
    }

    /**
     * @desc : This funtion count total no of user
     * @param : void
     * @return : total number of users
     */
    function total_user() {

        return $this->db->count_all_results("users");
    }

    /** @desc: This funtion gets all users
     *  @param : limit (between 10)
     *  @return :  array (user details)
     */
    function getuser($limit, $start) {
        $this->db->limit($limit, $start);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    /** @desc: This funtion get user id to hide user information of this id
     *  @param : user id and update action for user not active
     *  @return : update action
     */
    function removeuser($user_id, $updateAction) {

        $this->db->where('user_id', $user_id);
        $this->db->update('users', $updateAction);
        $getuseremail = $this->getusername($user_id);
        return $getuseremail[0]['user_email'];
    }

    /** @desc: This funtion get user email from user's id
     *  @param : user id
     *  @return : array(user_email)
     */
    function getusername($user_id) {
        $this->db->select('user_email');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('users');
        return $query->result_array();
    }

    /** @desc: This funtion is to get user repot
     *  @param : void
     *  @return : array()
     */
    function getuserreport() {
        $this->db->select('save_used_handyman.id,save_used_handyman.device_id,
              save_used_handyman.handyman_id,users.name,users.phone_number,
              handyman.service_id,handyman.profile_photo,handyman.phone,services.services,
              handyman.name as handyman_name');
        //$this->db->where('isreport_active',1);
        $this->db->from('save_used_handyman');
        $this->db->where('type', 'report');
        $this->db->join('users', 'users.device_id = save_used_handyman.device_id');
        $this->db->join('handyman', 'handyman.id = save_used_handyman.handyman_id');
        $this->db->join('services', 'services.id = handyman.service_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /** @desc: This funtion is to get marketting message
     *  @param : void
     *  @return : message
     */
    function getmail_message() {
        $query = $this->db->get('marketing_mail');
        return $query->result_array();
    }

    /** @description* This funtion is to update mail message
     *  @param :  comment
     *  @return : void
     */
    function mail_messageSave($data) {
        $this->db->where('id', 1);
        $this->db->update('marketing_mail', $data);
    }

    /** @description* This funtion is to get user's email
     *  @param :  void
     *  @return : array(email)
     */
    function get_email() {
        $this->db->select('user_email');
        $query = $this->db->get('users');
        return $query->result_array();
    }

    /** @description* This funtion is to insert comment for the used handyman
     *  @param :  id , comment
     *  @return : void
     */
    function add_comment_report($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('save_used_handyman', $data);
    }

    /** @description* when deactivate user(report) handyman get unverified
     *  @param : id (user report in save handyman )
     *  @return : array()
     */
    function add_verificationlist($id) {
        $this->db->select('save_used_handyman.handyman_id');
        $this->db->where('save_used_handyman.id', $id);
        $this->db->from('save_used_handyman');
        $this->db->join('handyman', 'handyman.id = save_used_handyman.handyman_id');
        $query = $this->db->get();
        $result = $query->result_array();
        $handymanid['id'] = $result[0]['handyman_id'];
        $update['action'] = '0';
        $update['approved'] = '0';
        $update['verified'] = '0';
        $this->db->where($handymanid);
        $this->db->update('handyman', $update);
    }

    function search_user($phone_number, $start ="", $end = "", $is_object = false) {
        $limit = "";
        if($start !=""){
            $limit = " LIMIT $start, $end ";
        }
        $sql = "SELECT u.name,u.name as customername, u.pincode,u.city, u.state, u.user_email, bd.user_id, bd.*, "
                . "u.phone_number,home_address,u.alternate_phone_number, services.services "
                . " FROM users as u LEFT JOIN booking_details as bd ON (bd.user_id = u.user_id) LEFT JOIN services ON (bd.service_id = services.id) "
                . " WHERE (bd.booking_primary_contact_no = '$phone_number' OR bd.booking_alternate_contact_no = '$phone_number' OR u.phone_number = '$phone_number')"
                . "  ORDER BY bd.create_date desc $limit ";
//        $sql = "SELECT u.name,u.pincode,u.city, u.state, u.user_email, "
//                . " bd.user_id, bd.*, "
//                . " u.phone_number,home_address,u.alternate_phone_number, services.services FROM booking_details as bd, users as u, services "
//                . " WHERE (bd.booking_primary_contact_no = '$phone_number' "
//                . " OR bd.booking_alternate_contact_no = '$phone_number'"
//                . " OR u.phone_number = '$phone_number') AND bd.user_id = u.user_id "
//                . " AND services.id = bd.service_id  ORDER BY bd.create_date desc  $limit";
//       
        $query = $this->db->query($sql);
       
        if($query->num_rows > 0){
            if($is_object){
                 return $query->result();
            } else {
                 return $query->result_array();
            }
           
            
        } else {
           
            return false; #$this->get_users_by_any(array('users.phone_number' => $phone_number));
            
        }
    }
    
    function get_users_by_any($where){
        $this->db->select('name,pincode,city,state, user_email,user_id, home_address, phone_number,alternate_phone_number ');
        $this->db->where($where);
        $query = $this->db->get('users');
        return $query->result_array();
    }
 

    /** @description : Function to search booking with booking id from find user page
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
     * 
     *  User is added with all his details available.
     * 
     *  @param : user details
     *  @return : id of inserted user
     */
    function add_user($user) {

        $this->db->insert('users', $user);

        $id = $this->db->insert_id();

        return $id;
    }
    
     /** @description : Search user and booking details for a particular user through his phone number, also for pagination
     * 
     *  This gets the basic user and booking details and services name as well
     * 
     *  @param : phone no, start and limit
     *  @return : array of user and booking details
     */
    function booking_history($phone_number, $limit, $start) {
        $sql = " SELECT services.services, users.user_id, users.city, users.state, users.phone_number, users.user_email, users.home_address, users.name, users.pincode, "
                . " booking_details.* "
                . " FROM booking_details, users,services WHERE "
                . " users.phone_number='$phone_number' AND booking_details.user_id=users.user_id AND "
                . " services.id=booking_details.service_id LIMIT $start, $limit";
        $query = $this->db->query($sql);
        
        //log_message ('info', __METHOD__ . "=> Booking  SQL ". $this->db->last_query());
        
        return $query->result_array();
    }

    /** @description : Search user and booking details for a particular user through his phone number,partner ID also for pagination
     * 
     *  This gets the basic user and booking details and services name as well
     * 
     *  @param : phone no,partner ID start and limit
     *  @return : array of user and booking details
     */
    function search_by_partner($phone_number,$partner_id, $start, $end) {
       $limit = "";
        if($start !=""){
            $limit = " LIMIT $start, $end ";
        }
        $sql = "SELECT u.name,u.pincode,u.city, u.state, u.user_email, "
                . " bd.user_id, bd.*, "
                . " u.phone_number,home_address,u.alternate_phone_number, services.services FROM booking_details as bd, users as u, services "
                . " WHERE (bd.booking_primary_contact_no = '$phone_number' "
                . " OR bd.booking_alternate_contact_no = '$phone_number'"
                . " OR u.phone_number = '$phone_number') AND bd.user_id = u.user_id "
                . " AND bd.partner_id = '$partner_id' AND services.id = bd.service_id  ORDER BY bd.create_date desc  $limit";
       
        $query = $this->db->query($sql);
       
        if($query->num_rows > 0){
            return $query->result_array();
            
        } else {
            $sql1 = "SELECT name,pincode,city,state, user_email,user_id, home_address, phone_number,alternate_phone_number FROM "
                    . " users WHERE (users.phone_number = '$phone_number')";
            $query1 = $this->db->query($sql1);
            return $query1->result_array();
        }
    }

    /** @description : Function to edit user's details
     *  @param : user's details to be updated
     *  @return : void
     */
    function edit_user($edit) {
        $this->db->where('user_id', $edit['user_id']);
        $result = $this->db->update('users', $edit);
        return $result;
    }

    /* @description : Function to get appliance details and user details for users booking history page(appliance wallet)
     *  @param : phone number
     *  @return : array(user and appliance details)
     */

    function appliance_details($phone_number) {
        $query = $this->db->query("SELECT users.user_id ,users.name,services.id as service_id, 
      services.services, appliance_details.id, appliance_details.brand, appliance_details.category,
       appliance_details.capacity, appliance_details.tag, appliance_details.model_number, 
       appliance_details.purchase_date FROM services, users, 
       appliance_details WHERE users.phone_number='$phone_number' AND users.user_id= 
       appliance_details.user_id AND services.id = appliance_details.service_id");
        return $query->result_array();
    }
    
    /**
     * @desc : This function is used to get unique user
     * 
     *  Or unique users in months, completed booking, cancelled booking
     * 
     * @param : Array(city, source, type(unique user in table or unique user month wise))
     * @return : Array()
     */
    function get_count_user($data) {

        $where = "";
        $result['month'] = "";
        $result['group_By'] = "";

        if ($data['type'] != "") {
            $result = $this->GroupBYForUserCount($data, "booking_details", "source, ");
        } else {
            $result['group_By'] = " Group By source";
        }

        //  city or date source not empty
        if ($data['city'] != "" || $data['source'] != "" || $data['type'] == "Today") {
            $where .=" where `user_id` !='' "; // user_id filed is not empty
        }

        // Array key city is not empty
        if ($data['city'] != '') {

            $where .= "AND `city` = '" . $data['city'] . "'";
        }

        if ($data['source'] != "") {
            $where .= " AND source = '" . $data['source'] . "'";
        }

        if ($data['type'] == "Today") {
            $where .= " AND booking_details.create_date >= CURDATE() AND booking_details.create_date < CURDATE() + INTERVAL 1 DAY  ";
            if ($result['group_By'] == "") {

                $result['group_By'] = " Group By source";
            }
        }

        $sql = "SELECT $result[month] source,
                 SUM(CASE WHEN `current_status` LIKE '%Cancelled%'  THEN 1 ELSE 0 END) AS cancelled_booking_user,
                 SUM(CASE WHEN `current_status` LIKE '%FollowUp%'  THEN 1 ELSE 0 END) AS followup,
                 SUM(CASE WHEN `current_status` LIKE '%Completed%' THEN 1 ELSE 0 END) AS completed_booking_user,
                 SUM(CASE WHEN `current_status` LIKE '%Pending%' OR `current_status`  LIKE '%Rescheduled%'  THEN 1 ELSE 0 END) as scheduled,
                 SUM(CASE WHEN `current_status` LIKE '%FollowUp%' OR `current_status` LIKE '%Completed%' OR `current_status` LIKE '%Cancelled%' OR `current_status` LIKE '%Pending%' OR `current_status` LIKE '%Rescheduled%' THEN 1 ELSE 0 END) AS total_booking 
  
                from booking_details $where $result[group_By] ; ";


        $query1 = $this->db->query($sql);

        return $query1->result_array();
    }

    /**
     * @desc : This function is used to group users by count
     * 
     *  User's count grouping is done on the basis of type of count, like All months, All years, week and quarter wise.
     * 
     * @param : Array(data, table, source)
     * @return : Array(users)
     */
    function GroupBYForUserCount($data, $table, $source = "") {

        $user['group_By'] = "";
        $user['month'] = "";
        // Month Wise DataSet
        if ($data['type'] == "All Month") {
            // get group by create date column.
            $user['group_By'] = " GROUP BY " . $source . "  DATE_FORMAT(" . $table . ".`create_date`, '%M, %Y') ORDER BY DATE_FORMAT(" . $table . ".`create_date`,'%Y') DESC , DATE_FORMAT(" . $table . ".`create_date`,'%m') DESC";
            $user['month'] = "DATE_FORMAT(" . $table . ".`create_date`,'%M, %Y') `month`,";
        }

        // Year Wise Dataset
        if ($data['type'] == "All Year") {
            // get group by create date column.
            $user['group_By'] = " GROUP BY " . $source . " DATE_FORMAT(" . $table . ".`create_date`, '%Y') ORDER BY DATE_FORMAT(" . $table . ".`create_date`, '%Y') DESC";
            $user['month'] = "DATE_FORMAT(" . $table . ".`create_date`, '%Y') `month`,";
        }

        // Week Wise Dataset
        if ($data['type'] == "Week") {
            // get group by create date column.
            $user['group_By'] = " GROUP BY  " . $source . " WEEK(" . $table . ".`create_date`)  ORDER BY DATE_FORMAT(" . $table . ".`create_date`,'%Y') DESC , DATE_FORMAT(" . $table . ".`create_date`,'%m') DESC";
            $user['month'] = "  CONCAT(date(" . $table . ".create_date), ' - ', date(" . $table . ".create_date) + INTERVAL 7 DAY)   `month`,";
        }

        //Quater Wise DataSet
        if ($data['type'] == 'Quater') {
            $user['group_By'] .= " GROUP BY " . $source . " Year(" . $table . ".`create_date`) Desc, QUARTER(" . $table . ".`create_date`) DESC";
            $user['month'] = " CASE QUARTER(" . $table . ".`create_date`) 

        WHEN 1 THEN 'Jan - Mar'

        WHEN 2 THEN 'Apr - Jun'
 
        WHEN 3 THEN 'July - Sep'

        WHEN 4 THEN 'Oct - Dec'

        END AS `month` ,  Year(" . $table . ".`create_date`) as year, ";
        }

        return $user;
    }


    /**
     * @desc : This function is used to get city source
     * 
     *  Get all the cities where we are active
     * 
     * @param : void
     * @return : Array(city)
     */
    function get_city_source() {
        $query1['city'] = $this->vendor_model->get_city();
        $query2['source'] = $this->partner_model->get_all_partner_source("not null");
        return array_merge($query1, $query2);
    }

    /**
     * @desc : This function is used count transactional users     
     * @param : data
     * @return : user's count
     */
    function get_count_transactional_user($data) {
        $where = "";
        $join = "";

        $result = $this->GroupBYForUserCount($data, "users");

        if ($data['source'] != "") {
            if ($data['source'] == "SA") {
                $where .=" where user_token IS NOT NULL";
            } else {
                $where .= " where booking_details.source = '" . $data['source'] . "'";
                $join = "Join booking_details on booking_details.user_id =  users.user_id";
            }
        }



        $sql = "SELECT $result[month]  count(Distinct `phone_number`) as total_user 
               FROM `users` $join $where  $result[group_By]";
        $query1 = $this->db->query($sql);

        return $query1->result_array();
    }
    
    /**
     * @Desc: This function is used to get user device id, from phone number
     * @params: phone number
     * @return: Array
     * 
     */
    function get_user_device_id_by_phone($phone){
        $this->db->select('device_id');
        $this->db->where('phone_number',$phone);
        $query = $this->db->get('users');
        return $query->result_array();
    }
    
    function update_sms_deactivation_status($numbers){
        $this->db->set('ndnc', '1');
        $this->db->where_in('phone_number', $numbers);
        $this->db->update('users');
        return $this->db->affected_rows();
    }
    
    /**
     * 
     * @param type $entity_type
     * @param type $id
     * @param type $old_password
     * @return type
     */
    function verify_entity_password($entity_type, $id, $old_password) {
        
        if($entity_type == _247AROUND_EMPLOYEE_STRING) {
            $record = $this->reusable_model->get_search_result_data('employee', '*', ['id' => $id, 'employee_password' => md5($old_password)],null,null,null,null,null,[]);
        }
        
        if($entity_type == _247AROUND_SF_STRING) {
            $record = $this->reusable_model->get_search_result_data('service_centers_login', '*', ['service_center_id' => $id, 'password' => md5($old_password)],null,null,null,null,null,[]);
        }
        
        return (!empty($record) ? '1' : '0');
    }
	
    /**
     * 
     * @param type $entity_type
     * @param type $id
     * @param type $new_password
     * @return type
     */
    function change_entity_password($entity_type, $id, $new_password) {
        
        if($entity_type == _247AROUND_EMPLOYEE_STRING) {
            return $this->reusable_model->update_table('employee', ['employee_password' => md5($new_password), 'clear_password'=> $new_password], ['id' => $id]);
        }
        
        if($entity_type == _247AROUND_SF_STRING) {
            return $this->reusable_model->update_table('service_centers_login', ['password' => md5($new_password), 'clear_text' => $new_password], ['service_center_id' => $id]);
        }
    }    
    // end of model
}
