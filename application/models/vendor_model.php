<?php

class vendor_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /**
     * @desc: This function is to view vendor details
     *
     * If vendor_id is given then the details of the specific vendor will come else
     *  the details of all the vendors will be be returned
     *
     * @param: $vendor_id
     * @return: array of vendor details
     */
    function viewvendor($vendor_id = "",$active = "",$sf_list = "", $is_cp = '',$is_wh = '',$state='',$city='') {
        $where_id = "";
        $where_active = "";
        $where_sf = "";
        $where_final = "";
        $cp = "";
        $where_state_city="";
        if($is_cp != ''){
            $cp = " AND service_centres.is_cp = $is_cp";
        }
        
        if($is_wh != ''){
            $cp = " AND service_centres.is_wh = $is_wh";
        }
        
        if ($vendor_id != "") {
            $where_id .= "service_centres.id= '$vendor_id'";
        }
        if ($active != "") {
            $where_active .= "service_centres.active= '$active'";
        }
        if($sf_list != ""){
            $where_sf .= "service_centres.id  IN (" .$sf_list.")";
        }
        if(!empty($state) && empty($city))
        {
            $where_state_city ="AND service_centres.state='$state'";
        }
        elseif(!empty($state) && !empty($city))
        {
            $where_state_city="AND service_centres.state='$state' AND service_centres.district='$city'";
        }
        if($vendor_id != "" && $active != "" ){
            $where_final = 'where '.$where_id." AND ".$where_active.$cp ;
        }
        if($vendor_id != ''){
            $where_final = 'where '.$where_id.$cp;
        }
        if($active != ""){
            $where_final = 'where '.$where_active.$cp;
        }
        if($sf_list != "" ){
            $where_final = 'where '.$where_sf.$cp;
        }
        if($sf_list != "" && $active != ""){
            $where_final = 'where '.$where_sf." AND ".$where_active. $cp;
        }
        if($vendor_id != "" && $active != ""){
            $where_final = 'where '.$where_id." AND ".$where_active . $cp;
        }
        
        if($active === "" && $is_cp !== ""){
            $where_final = "where service_centres.is_cp = '1'";
        }
        $sql = "Select service_centres.*,account_holders_bank_details.bank_name,account_holders_bank_details.account_type,account_holders_bank_details.bank_account, account_holders_bank_details.ifsc_code_api_response,"
                . "account_holders_bank_details.ifsc_code,account_holders_bank_details.cancelled_cheque_file,account_holders_bank_details.beneficiary_name,"
                . "account_holders_bank_details.is_verified  from service_centres LEFT JOIN account_holders_bank_details ON account_holders_bank_details.entity_id=service_centres.id AND "
                . "account_holders_bank_details.entity_type='SF ' AND account_holders_bank_details.is_active=1 $where_final $where_state_city";
        $query = $this->db->query($sql);
       return $query->result_array();
    }

    /**
     * @desc: This function is to get the edit vendor form with vendor details
     *
     * The details of vendor entered while adding them will be displayed in editable fields
     *      so that details can be modified.
     *
     * @param: $id(vendor_id)
     * @return: array of vendor details
     */
    function editvendor($id) {

        $this->db->select('*');
        $this->db->where('id',$id);
        $query = $this->db->get('service_centres');

        return $query->result_array();
    }

    /**
     * @desc: This function edits vendor's details
     *
     * If details of vendor that are edited will be modified else others details will remain same.
     *
     * @param: $vendor
     *          - Array of all the vendor details to be edited.
     * @param: $id(vendor_id)
     *          - Id of vendor which is to be edited.
     * @return: none
     */
    function edit_vendor($vendor, $id) {
        $this->db->where('id', $id);
        $this->db->update('service_centres', $vendor);
    }

    /**
     * @desc: This function is to add a new vendor
     *
     * Vendor details like Service Center's name, owners name, ph no., email, poc name, email, services, brands covered,
     *      bank details, etc.
     *
     * @param: $vendor
     *          - Vendor details to be added.
     * @return: ID for the new vendor
     */
    function add_vendor($vendor) {

        $this->db->insert('service_centres', $vendor);
        return $this->db->insert_id();

    }

     /**
     * @desc: This function is to add login details for a new vendor
     *
     * @param: $login
     *          - Vendor login details to be added.
     * @return: void
     */
    function add_vendor_login($login) {
        $this->db->insert('service_centers_login', $login);
     }

    /**
     * @desc: This function is to get all the active services
     *
     * Will select the services(appliances) which we are handling.
     *
     * @param: void
     * @return: array of all active services
     */
    function selectservice() {
        $query = $this->db->query("Select id,services from services where isBookingActive='1' order by services");
        return $query->result();
    }

    /**
     * @desc: This function is to get all distinct brands
     *
     *  Will show all the brands we are working on.
     *
     * @param: void
     * @return: array of all brands
     */
    function selectbrand() {
        $sql = "Select DISTINCT brand_name from appliance_brands order by brand_name";
        $query = $this->db->query($sql);

        return $query->result();
    }
    
    function update_service_centers_login($where, $data){
        $this->db->where($where);
        return $this->db->update("service_centers_login", $data);
    }


    /**
     * @desc: This function is to activate vendor who is already registered with us.
     *
     *  The vendor could be in activated or deactivated status.
     *
     * @param: $id
     *         - Id of vendor to whom we would like to delete.
     * @return: void
     */
    function delete($id) {
        $sql = "Delete from service_centres where id='$id'";
        $this->db->query($sql);
    }

    /**
     *  @desc : This function is to insert pincode and vendor_id mapping into a temporary table.
     * The original table is dropped and temp table becomes the final table if all pincodes are
     * are inserted without any error.
     *  @param : $details
     *          - Pincodes to be inserted
     *  @return : void
     */
    function insert_vendor_pincode_mapping_temp($details) {
        return $this->db->insert_batch('vendor_pincode_mapping_temp', $details);    //return in not used as I checked
    }

    //THIS FUNCTION LOOKS UNUSED, PLEASE CHECK AND REMOVE
    /**
     *  @desc : This function is to insert pincode in master pincode table
     *  @param : $pincodes
     *  @return : void
     */
    function insert_pincode($pincodes) {
        $this->db->insert_batch('india_pincode', $pincodes);

        return $this->db->affected_rows();
    }

    /**
     *  @desc : This function is to get the non-working days for a particular vendor
     *  @param : $service_centre_id
     *  @return : array of non-working days
     */
    function get_non_working_days_for_vendor($service_centre_id) {
        $this->db->select('non_working_days');
        $this->db->where('id', $service_centre_id);
        $query = $this->db->get('service_centres');
        return $query->result_array();
    }

    /**
     *  @desc : This function drops original pincode mapping table and renames the "vendor_pincode_mapping_temp" table to
     *  "vendor_pincode_mapping"
     *
     *  @param : void
     *  @return : void
     */
    function switch_temp_pincode_table() {
        $this->load->dbforge();

        //rename original to temp2 since there is no efficient copy table command
        $this->dbforge->rename_table('vendor_pincode_mapping', 'vendor_pincode_mapping_temp2');

        //rename temp to original
        $this->dbforge->rename_table('vendor_pincode_mapping_temp', 'vendor_pincode_mapping');

        //rename temp2 to temp
        $this->dbforge->rename_table('vendor_pincode_mapping_temp2', 'vendor_pincode_mapping_temp');

        //truncate temp
        $this->db->empty_table('vendor_pincode_mapping_temp');
    }

    /**
     *  @desc : This function is used get the escalation reasons.
     *
     *  The escalation reasons which are active only those are returned.
     *
     *  @param : void
     *  @return : array of id and escalation reasons
     */
    function getEscalationReason($entity) {
        $this->db->select('id,escalation_reason');
        $this->db->where($entity);
        $query = $this->db->get("vendor_escalation_policy");
        return $query->result_array();
    }

    /**
     *  @desc : This function is used get the escalation reasons.
     *
     *  The escalation reasons which are active only those are returned.
     *
     *  @param : void
     *  @return : array of id and escalation reasons
     */
    function getVendor($booking_id) {

        $this->db->select("service_centres.name, service_centres.id, company_name, "
                . "service_centres.address,service_centres.pincode, service_centres.state, "
                . "service_centres.district, service_centres.primary_contact_name,"
                . "service_centres.primary_contact_phone_1 ");
        $this->db->from('booking_details');
        $this->db->where('booking_id', $booking_id);
        $this->db->join('service_centres', 'service_centres.id = booking_details.assigned_vendor_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     *  @desc : This function is used insert escalation details.
     *
     *  The escalation details are inserted for particular vendor.
     *
     *  @param : $details
     *          - Vendor's id, booking id, escalation reason, etc.
     *  @return : insert_id(id genetated while inserting details)
     */
    function insertVendorEscalationDetails($details) {
        $this->db->insert('vendor_escalation_log', $details);
        return $this->db->insert_id();
    }

    /**
     *  @desc : This function is used to get contact details of a particular vendor
     *
     *  @param : $vendor_id
     *  @return : array of contact details
     */
    function getVendorContact($vendor_id) {
        $this->db->where('id', $vendor_id);
        $query = $this->db->get('service_centres');
        return $query->result_array();
    }

    /**
     *  @desc : This function is used to get all the details of escalation policy
     *
     *  @param : $escalation_reason_id
     *  @return : array of all the details of escalation policy
     */
    function getEscalationPolicyDetails($escalation_reason_id) {
        $this->db->where('id', $escalation_reason_id);
        $query = $this->db->get('vendor_escalation_policy');
        return $query->result_array();
    }

    /**
     *  @desc : This function is used to get user details.
     *
     *  Here user details are found with the help boooking id
     *
     *  @param : $booking_id
     *  @return : array of user details
     */
    function getUserDetails($booking_id) {
        $this->db->select('users.name, users.phone_number');
        $this->db->where('booking_id', $booking_id);
        $this->db->from('booking_details');
        $this->db->join('users', 'users.user_id = booking_details.user_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     *  @desc : This function is used to select state
     *
     *  The states which are active and also where our vendors are active.
     *
     *  @param : $city
     *  @return : array of states
     */
    function selectSate($city = "") {
        $this->db->distinct();
        $this->db->select('vendor_pincode_mapping.State as state');
        $this->db->from('vendor_pincode_mapping');
        $this->db->order_by('vendor_pincode_mapping.State');
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where('service_centres.active', '1');

        if ($city != ""){
            $this->db->where('vendor_pincode_mapping.City', $city);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     *  @desc : This function is to get all states.
     *
     *  All the distinct states of India in Ascending order
     *
     *  @param : $city
     *  @return : array of states
     */
    function getall_state($city = "") {
	$this->db->distinct();
	$this->db->select('state');
	if ($city != "") {
	    $this->db->LIKE('district', $city);
	}
	$this->db->order_by('state');
	$query = $this->db->get('india_pincode');

	return $query->result_array();
    }
    
    /**
     *  @desc : This function is to get all states.
     *
     *  All the distinct states of India in Ascending order From Table state_code
     *
     *  @param : void
     *  @return : array of states
     */
    function get_allstates() {
	$this->db->distinct();
	$this->db->select('state');
	
	$this->db->order_by('state');
	$query = $this->db->get('state_code');

	return $query->result_array();
    }

    /**
     *  @desc : This function is to get State specific to a Pincode
     *  
     *  @param : $pincode
     *  @return : State
     */
    function get_state_from_pincode($pincode) {
        $this->db->distinct();
        // Do not make state capital. It should be 'state'.
        $this->db->select('State as state');
        $this->db->where('Pincode', $pincode);

        $query = $this->db->get('vendor_pincode_mapping');
        if ($query->num_rows > 0) {
            return $query->result_array()[0];
        } else {
            $state['state'] = "";
            return $state;
        }
    }

    
    /**
     *  @desc : This function is to get State specific to a india Pincode table
     *  
     *  @param : $pincode
     *  @return : State
     */
    function get_state_from_india_pincode($pincode) {
	$this->db->distinct();
    // Do not make state capital. It should be 'state'.
	$this->db->select('state');
	$this->db->where('pincode', $pincode);

	$query = $this->db->get('india_pincode');
    if($query->num_rows > 0){
        return $query->result_array()[0];
    } else {
        $state['state'] = "";
        return $state;
    }


    }


    function get_distict_details_from_india_pincode($pincode) {
       // $this->db->cache_on();
        $this->db->distinct();
        // Do not make state capital. It should be 'state'.
        $this->db->select('district, state, taluk, area');
        $this->db->where('pincode', $pincode);

        $query = $this->db->get('india_pincode');
        if ($query->num_rows > 0) {
            return $query->result_array()[0];
        } else {
            $district['district'] = "";
            $district['state'] = "";
            $district['taluk'] = "";
            $district['area'] = "";
            return $district;
        }
    }

    /**
     *  @desc : This function is to select district from India pincode
     *
     *  All the distinct districts of India(if state is given then according to state)
     *
     *  @param : $state
     *  @return : array of districts
     */
    function getDistrict_from_india_pincode($state = "", $pincode ="") {
        $this->db->distinct();
        $this->db->select('district');
        if ($state != "") {
            $this->db->where('LOWER(state)', strtolower($state));
        }
        
        if($pincode != ""){
            $this->db->where('pincode', trim($pincode));
        }
        $this->db->order_by('district');
        $query = $this->db->get('india_pincode');

        return $query->result_array();
    }

    /**
     *  @desc : This function is to select district where our vendors are active and also active for
     *          particular pincode
     *
     *  @param : $state
     *  @return : array of districts
     */
    function getDistrict($state = "", $pincode = "") {
        $this->db->distinct();
        $this->db->select('vendor_pincode_mapping.City as district');
        $this->db->from('vendor_pincode_mapping');
        $this->db->order_by('vendor_pincode_mapping.City');
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where('service_centres.active', '1');

        if ($state != ""){
            $this->db->where('vendor_pincode_mapping.State', $state);
        }
        if($pincode !=""){
             $this->db->where('vendor_pincode_mapping.Pincode', $pincode);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     *  @desc : This function is to select the active pincode and service centers active in this pincode
     *          of a particular district.
     *
     *  @param : $district
     *  @return : array of pincodes
     */
    function getPincode($district) {
        $this->db->distinct();
        $this->db->select('vendor_pincode_mapping.Pincode as pincode');
        $this->db->from('vendor_pincode_mapping');
        $this->db->order_by('vendor_pincode_mapping.Pincode');
        $this->db->where('vendor_pincode_mapping.City', $district);
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where('service_centres.active', '1');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     *  @desc : This function is to get all the pincodes for a particular district in India.
     *
     *  @param : $district
     *  @return : array of pincodes
     */
    function getPincode_from_india_pincode($district="", $is_district = false) {
        $this->db->distinct();
        if($is_district){
            $this->db->select('pincode, district');
        } else{
            $this->db->select('pincode');
        }
        
        if($district != ""){
            $this->db->where('LOWER(district)', strtolower($district));
        }
        $this->db->order_by('pincode');
        $query = $this->db->get('india_pincode');

        return $query->result_array();
    }

    /**
     *  @desc : Get POC and owner email for Active Service Center.
     *
     *  The email id of owner and primary contact.
     *
     *  @param : void
     *  @return : array of emails
     */
    function select_active_service_center_email() {
        $this->db->select('primary_contact_email, owner_email');
        $this->db->where('active', 1);
        $query = $this->db->get('service_centres');
        return $query->result_array();
    }

    /**
     *  @desc : To get booking date and timeslot of a particular booking
     *
     *  @param : $booking_id
     *  @return : array of booking date and timeslot
     */
    function getBookingDateFromBookingID($booking_id) {
        $this->db->select('booking_date, booking_timeslot,count_escalation');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get('booking_details');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }

    /**
     *  @desc : To insert filename and bucketname while uploading pincode excel
     *
     *  @param : $data
     *  @return : void
     */
    function insertS3FileDetails($data) {
        $this->db->insert('pincode_mapping_s3_upload_details', $data);
    }

    /**
     *  @desc : To get latest pincode mapping file name
     *
     *  @param : void
     *  @return : array of file name
     */
    function getLatestVendorPincodeMappingFile() {
        $sql = 'SELECT file_name FROM pincode_mapping_s3_upload_details WHERE bucket_name = "vendor-pincodes" ORDER BY create_date DESC LIMIT 0 , 1';

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    /**
     *  @desc : To get vendor SMS templates
     *
     *  To get the active template for a particular sms tag.
     *
     *  @param : $sms_tag
     *  @return : template if exists else returns blank
     */
    function getVendorSmsTemplate($sms_tag,$otherData = FALSE) {
        $this->db->select("template,is_exception_for_length");
        $this->db->where('tag', $sms_tag);
        $this->db->where('active', 1);
        $query = $this->db->get('sms_template');
        if ($query->num_rows > 0) {
            $template = $query->result_array();
            if(!$otherData){
                return $template[0]['template'];
            }
            else{
                return $template[0]['is_exception_for_length'];
            }
        } else {
            return "";
        }
    }

    /**
     *  @desc : update escalation policy flag in json in vendor escalation log table
     *
     *  To get the active template for a particular sms tag.
     *
     *  @param : $id
     *  @param : $flag
     *  @param : $booking_id
     *  @return : user details of the booking id
     */
    function updateEscalationFlag($id, $flag, $booking_id) {
        unset($flag[0]['escalation_reason']);
        unset($flag[0]['id']);
        unset($flag[0]['sms_body']);
        unset($flag[0]['mail_subject']);
        unset($flag[0]['mail_body']);
        unset($flag[0]['active']);
        unset($flag[0]['create_date']);
        
        $reason_flag['escalation_policy_flag'] = json_encode($flag);
        return $this->update_esclation_policy_flag($id, $reason_flag, $booking_id);
    }

    function update_esclation_policy_flag($id, $reason_flag, $booking_id){
        $this->db->where('id', $id);
        $this->db->update('vendor_escalation_log', $reason_flag);
        return $this->getUserDetails($booking_id);

    }

    /**
     *  @desc : To get all the cities which are active
     *
     *  @param : void
     *  @return : array of cities
     */
    function get_city() {
        $this->db->distinct();
        $this->db->select('City');
        $this->db->order_by('City');
        $query = $this->db->get('vendor_pincode_mapping');
        return $query->result_array();
    }

    /**
     * @desc:  Function to get vendor name, area, region etc which are active.
     * @param: $data
     * @return : Array
     */
    function get_services_category_city_pincode() {
        $service = $this->db->query("Select id,services from services where isBookingActive='1' Order By services");
        $query1['services'] = $service->result_array();
        $query2['city'] = $this->get_city();

        $this->db->distinct();
        $this->db->select('Pincode');
        $this->db->order_by("Pincode");
        $query4 = $this->db->get('vendor_pincode_mapping');

        $query3['pincode'] = $query4->result_array();

        return array_merge($query1, $query2, $query3);
    }

    /**
     * @desc:  Function to get vendor name, area, region etc which are active.
     * @param: $data
     * @return : Array
     */
    function getVendorFromVendorMapping($data) {
        $this->db->distinct();
        $this->db->select('service_centres.name As Vendor_Name, vendor_pincode_mapping.Pincode');
        $this->db->from('vendor_pincode_mapping');
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where('vendor_pincode_mapping.Pincode', $data['pincode']);
//        $this->db->where('Appliance_ID', $data['service_id']);
//        if ($data['city'] != 'Select City')
//            $this->db->where('vendor_pincode_mapping.City', $data['city']);

        if (!empty($data['service_id']))
            $this->db->where('vendor_pincode_mapping.Appliance_ID', $data['service_id']);

        $this->db->where('service_centres.active', 1);
        //Checking Temporary On/Off values
        $this->db->where('service_centres.on_off', 1);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @desc:  get Vendor details
     * @param: Array
     * @return : Array
     */
    function getVendorDetails($select, $where =array(), $order_by='name',$whereIN=array()) {
        $this->db->select($select,FALSE);
        if(!empty($where)){
           $this->db->where($where);
        }
        if(!empty($whereIN)){
            foreach ($whereIN as $fieldName=>$conditionArray){
                    $this->db->where_in($fieldName, $conditionArray);
            }
        }
        $this->db->order_by($order_by);
        $sql = $this->db->get('service_centres');
        return $sql->result_array();
    }

    /**
     * @desc:  To get vendor id and name, services, cities and sources for bookings assigned to service center.
     *
     * This is function to show the performance of a vendor, by showing
     *
     * @param: void
     * @return : Array
     */
    function get_vendor_city_appliance() {
        $service = $this->db->query("Select id,services from services where isBookingActive='1'");
        $query1['services'] = $service->result_array();
        $query2['city'] = $this->get_city();

        $this->db->distinct();
        $this->db->select('id, name');
        $this->db->order_by('name', 'ASC');
        $this->db->where('active', 1);
        $query = $this->db->get('service_centres');
        $query3['vendor'] = $query->result_array();

        $source['source'] = $this->partner_model->get_all_partner_source("not null");

        return array_merge($query1, $query2, $query3, $source);
    }

    /**
     * @desc: This function is to get vendors available in a particular city.
     * @param: $vendor_id
     * @param: $city
     * @return : array of vendor and appliance name
     */
    function getVendorFromMapping($vendor_id = "", $city = "") {

        $cities = "";

        if ($city != "") {
            $cities = " , City";
        }
        $this->db->distinct();
        $this->db->select('vendor_pincode_mapping.Vendor_Name as name, vendor_pincode_mapping.Vendor_ID as id, services.services as Appliance, vendor_pincode_mapping.Appliance_ID');
       
        if ($vendor_id != "")
            $this->db->where('vendor_pincode_mapping.Vendor_ID', $vendor_id);

        if ($city != "")
            $this->db->where('vendor_pincode_mapping.City', $city);

        $this->db->order_by('services.services', 'ASC');
        $this->db->join('services', 'services.id = vendor_pincode_mapping.Appliance_ID');
        $query = $this->db->get('vendor_pincode_mapping');

        return $query->result_array();
    }

    /**
     * @desc: This function is used to check service center code alredy exist or not
     * @param: String(Service center code)
     * @return : if service center code exist return true otherwise return false.
     */
    function check_sc_code_exist($sc_code) {
        $this->db->select('*');
        $this->db->where('sc_code', $sc_code);
        $query = $this->db->get('service_centres');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  @desc: This function is used to get vendor performance.
     *
     *  This performance of vendor can be viewed according to timely basis- weekly, monthly, quatrly and yearly.
     *  Vendor performane can be categorised city wise, Appliance wise, Source wise and timely basis.
     *
     *  @param : $vendor
     *          - Contains details of vendor, vendor_id, number of completed bookings, cancelled bookings, etc.
     *  @return : array of vendor performance details.
     */
    function get_vendor_performance($vendor) {
        $group_By = "";
        $where = "";
        $month = "";
        $source = "";
        $services = "";
        $join = "";
        $sources = "";
        $avg = "";
        $city = "";
        // it used to make group by source when service center id is not empty and source is empty.
        if ($vendor['vendor_id'] != "" && $vendor['source'] == "") {
            $sources = " , source ";
        }
        // It is used to get dataset group by month- year and order by year desc
        if ($vendor['period'] == 'All Month') {
            $group_By .= " GROUP BY DATE_FORMAT(booking_details.`create_date`, '%M, %Y') $sources ORDER BY DATE_FORMAT(`booking_details`.`create_date`, '%Y') DESC, completed_booking";
            // it used to select month and year with dataset
            $month = " DATE_FORMAT(booking_details.`create_date`,'%M, %Y') `month`,";
        }

        // Year Wise Dataset
        // used to get dataset group by year date.
        if ($vendor['period'] == "All Year") {
            $group_By = " GROUP BY DATE_FORMAT(booking_details.`create_date`, '%Y') $sources ORDER BY DATE_FORMAT(booking_details.`create_date`, '%Y') DESC, completed_booking";

            //used to select year
            $month = " DATE_FORMAT(booking_details.`create_date`, '%Y') `month`,";
        }

        // Week Wise Dataset
        if ($vendor['period'] == "Week") {
            // get week wise dataset.
            $group_By = " GROUP BY WEEK(booking_details.`create_date`) $sources ORDER BY DATE_FORMAT(booking_details`create_date`,'%Y') DESC , DATE_FORMAT(booking_details.`create_date`,'%m') DESC, completed_booking";
            //used to select week
            $month = "  CONCAT(date(booking_details.create_date), ' - ', date(booking_details.create_date) + INTERVAL 7 DAY)   `month`,";
        }

        //Quater Wise DataSet
        if ($vendor['period'] == 'Quater') {
            $group_By .= " GROUP BY Year(booking_details.create_date) Desc, QUARTER(booking_details.create_date) DESC $sources ";
            $month = " CASE QUARTER(booking_details.create_date)

        WHEN 1 THEN 'Jan - Mar'

        WHEN 2 THEN 'Apr - Jun'

        WHEN 3 THEN 'July - Sep'

        WHEN 4 THEN 'Oct - Dec'

        END AS `month` ,  Year(booking_details.create_date) as year, ";
        }

        // If service id is not empty then select service name by use join query to services table
        if ($vendor['service_id'] != "") {
            $services = " services.services as Appliance, ";
            $where .= " AND service_id = '" . $vendor['service_id'] . "'";
            $join = " JOIN services on services.id = booking_details.service_id ";
        }
        // if city is not empty then Add where clause in booking details to get completed booking in custom city
        if ($vendor['city'] != "") {
            $city = " city, ";
            $where .=" AND city = '" . $vendor['city'] . "' ";
        }
        // if source is not empty then Add where clause in booking details to get completed booking in custom source
        if ($vendor['source'] != "") {
            $source = "  source, ";
            $where .= " AND source = '" . $vendor['source'] . "'";
        }

        // if vendor id is not empty and source is empty then get data group source wise
        if ($vendor['vendor_id'] != "" && $vendor['source'] == "") {
            if ($group_By == "") {
                $group_By .= " GROUP By source";
            }

            $source = "  source, ";
        }
        // Only get vendor details from vendor mapping table, when vendor id and source are not empty and service id is empty.
        // otherwise get vendor details from service centers table.
        if ($vendor['vendor_id'] != "" && $vendor['source'] != "" && $vendor['service_id'] == "") {

            $service_center = $this->getVendorFromMapping($vendor['vendor_id']);
        } else {
            $service_center = $this->viewvendor($vendor['vendor_id']);
        }

        // initialize empty array
        $array = array();
        foreach ($service_center as $key => $value) {

            $condition = "";
            if (isset($value['Appliance_ID'])) {
                $condition = " AND service_id =  $value[Appliance_ID] ";
                // Calculate avg as a subquery
                $avg = " , AVG(amount_paid) AS amount_paid,
                     (SELECT avg(amount_paid)
                       FROM `booking_details`
                      WHERE booking_details.service_id = '$value[Appliance_ID]' AND source =
                      '$vendor[source]') as avg_amount_paid";
            }

            $sql = "SELECT $month $source $services $city
                SUM(CASE WHEN `current_status` LIKE '%Completed%' THEN 1 ELSE 0 END) AS completed_booking,
                SUM(CASE WHEN `current_status` LIKE '%Cancelled%' THEN 1 ELSE 0 END) AS cancelled_booking
                $avg

                from booking_details $join where assigned_vendor_id = $value[id]  $condition  $where $group_By";
            
            $data = $this->db->query($sql);
            $result = $data->result_array();

            if (!empty($result)) {
                
                $result[0]['Vendor_Name'] = $value['name'];
                $result[0]['Vendor_ID'] = $value['id'];

                foreach ($result as $keys => $center) {

                    if (isset($value['Appliance'])) {
                        $result[$keys]['Appliance'] = $value['Appliance'];
                    }
                    if ($vendor['source'] != "") {
                        $result[$keys]['source'] = $vendor['source'];
                    }

                    if ($center['completed_booking'] == "") {
                        $result[$keys]['completed_booking'] = 0;
                    }

                    if ($center['cancelled_booking'] == "") {
                        $result[$keys]['cancelled_booking'] = 0;
                    }

                    if (($center['completed_booking'] + $center['cancelled_booking'] ) > 0) {
                        $result[$keys]['percentage'] = sprintf("%.2f", (($center['completed_booking'] * 100) / ($center['completed_booking'] + $center['cancelled_booking'] )));
                    } else {

                        $result[$keys]['percentage'] = "0";
                    }
                }
            }
            //Array push
            array_push($array, $result);
        }
        return $array;
    }

    /**
     *  @desc:  This function is to insert service center details for the actions they
     *          performed on the booking assigned to them.
     *
     *  @param : $data
     *          - Contains closing remarks,  charges collected by vendor, etc.
     *  @return : void
     */
    function insert_service_center_action($data) {
        $this->db->insert('service_center_booking_action', $data);
        
        log_message('info', __METHOD__ . "=> Insert Service center Action table SQL: " . $this->db->last_query() );
        
        $assign_sc_id = $this->db->insert_id();
         
        return $assign_sc_id;

    }
        
    /**
     * @Desc: This function is used to get data from the  service_center_booking_action table
     * @params: $select string
     * @params: $where array
     * @return: $query array
     * 
     */
    
    function get_service_center_booking_action_details($select, $where=array()){
        $this->db->select($select);
        $this->db->where($where);
        $query = $this->db->get("service_center_booking_action");
        return $query->result_array();
    }

    /**
     *  @desc:  When reassign service center, update previous action perform by service center
     *
     *  @param : $data
     *          - Contains closing remarks to be updated,  charges collected by vendor, etc.
     *  @return : void
     */
    function update_service_center_action($booking_id,$data) {
	//TODO: Why we are unsetting here?
	if (isset($data['closing_remarks'])) {
            unset($data['closing_remarks']);
        }
        if(isset($data['unit_details_id'])){
           $this->db->where('unit_details_id', $data['unit_details_id']);
        }
        $this->db->where('booking_id', $booking_id);
        $this->db->update('service_center_booking_action', $data);
        log_message('info', __METHOD__ . "=> Update SQL: " . $this->db->last_query() );
    }

    /**
     *  @desc:  When reassign service center, delete previous action perform by service center
     *
     *  @param : $booking_id
     *  @return : void
     */
    function delete_previous_service_center_action($booking_id) {
        if (!empty($booking_id) || $booking_id != "0") {
            $this->db->select('*');
            $this->db->where('booking_id', $booking_id);
            $query = $this->db->get('service_center_booking_action');
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                log_message('info', __METHOD__ . "=> Booking ID: " . $booking_id . "=> Old vendor data " .
                        print_r($result, TRUE));

                $this->db->where('booking_id', $booking_id);
                $this->db->delete("service_center_booking_action");
                log_message('info', __METHOD__ . "=> Delete SQL: " . $this->db->last_query());
            }
        }
    }

    /**
     *  @desc: get cancellation reason for specific vendor
     *
     *  Will count the cancellead bookings with same cancellation reason for a particular service center
     *
     *  @param : $service_center_id
     *  @return : array of cancellation reason count
     */
    function getcancellation_reason($service_center_id) {
        $sql = "SELECT cancellation_reason, count('Distinct cancellation_reason') AS count FROM booking_details where assigned_vendor_id = '$service_center_id' AND  current_status = 'Cancelled' GROUP BY cancellation_reason";

        $data = $this->db->query($sql);
        log_message('info', __METHOD__ . "=> Cancellation Reaon: " . $this->db->last_query() );
        return $data->result_array();
    }
    
    /**
     * @desc: get Active vendor
     */
    function getactive_vendor(){
        $this->db->select('*');
        $this->db->where('active',1);
        $this->db->order_by("name");
        $query = $this->db->get('service_centres');
        return $query->result_array();
    }
    /**
     * @desc: Insert Engineer details
     */
    function insert_engineer($data){
        $this->db->insert('engineer_details', $data);
        return $this->db->insert_id();
    }
    /**
     * @desc: This is used to return Engineers details for custom vendor
     * @param String $service_center_id
     * @return Array
     */
    //TO DO - Remove this method
    function get_engineers($service_center_id){
        if($service_center_id != ""){
            $this->db->where('service_center_id', $service_center_id);
        }
        $this->db->where('delete', 0);
        $query = $this->db->get('engineer_details');
        return $query->result_array();
    }
    
    /**
     * @desc: This is used to get Engineer details based on Engineer ID
     * @param INT engineer ID
     * @param Array Engineer Details
     */
    //TO DO - Remove this method
    function get_engg_by_id($id){
        $this->db->where('id', $id);
        $this->db->where('delete', 0);
        $query = $this->db->get('engineer_details');
        return $query->result_array();
    }

    function update_engineer($where, $data){
        $this->db->where($where);
        $this->db->update('engineer_details', $data);
        log_message('info', __METHOD__ . "=> Update Engineer " . $this->db->last_query() );

    }

    /**
     *  @desc: get distinct vendor details
     *
     *  @param : Service ID(Appliance ID)
     *  @return : array of all data
     */
    function get_distinct_vendor_details($service_id){
         $query = $this->db->query("SELECT DISTINCT vendor_pincode_mapping.Vendor_ID,service_centres.name as Vendor_Name from vendor_pincode_mapping join service_centres ON "
                 . "service_centres.id=vendor_pincode_mapping.Vendor_ID"
                 . " where vendor_pincode_mapping.Appliance_ID = ". $service_id. ' Order By service_centres.name');
         return $query->result_array();
    }
    /**
     *  @desc: get distinct Appliance details
     *
     *  @param : Service ID(Appliance ID)
     *  @return : array of all data
     */
     function get_distinct_vendor_service_details($vendor_id, $pincode=""){
         $where = "";
         if(!empty($pincode)){
             $where  = " AND vendor_pincode_mapping.Pincode = '".$pincode."' ";
         }
         $query = $this->db->query("SELECT DISTINCT services.services AS Appliance,vendor_pincode_mapping.Appliance_ID from "
                 . "vendor_pincode_mapping JOIN services ON services.id= vendor_pincode_mapping.Appliance_ID where vendor_pincode_mapping.Vendor_ID = ". $vendor_id. " $where Order By services.services");
         return $query->result_array();
    }
    
    /**
     *  @desc: get Vendor Details
     *
     *  @param : array
     *  @return : array of all data
     */
    function check_vendor_details($data){
        $this->db->select('*');
        $this->db->where($data);
        $query = $this->db->get('vendor_pincode_mapping');
        if($query->num_rows >0){

            return $query->result_array();

        } else {
            return true;
        }
       
    }
    /**
     * @desc: This is used to insert value in vendor_pincode_mapping table
     * @param Array
     * @return Int ID of inserted data
     */
    function insert_vendor_pincode_mapping($data){
        $this->db->insert('vendor_pincode_mapping', $data);
        return $this->db->insert_id();
    }
    
    /**
     * @desc: This is used to insert value in vendor_pincode_mapping table
     * @param Array
     * @return Int ID of inserted data
     */
    function insert_247Around_vendor_pincode_mapping($data){

        $this->db->insert('247Around_vendor_pincode_mapping', $data);
        
        return $this->db->insert_id();
    }

    function delete_vendor($data) {
        if (!empty($data)) {
            $this->db->where('Appliance_ID', $data['Appliance_ID']);
            $this->db->where('Pincode', $data['Pincode']);
            $this->db->where('Vendor_ID', $data['Vendor_ID']);
            $this->db->delete('vendor_pincode_mapping');
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     *  @desc: get all data from vendor_pincode_mapping
     *
     *  @param : void
     *  @return : array of all data
     */

    function get_all_pincode_mapping(){
        $sql = "SELECT vendor_pincode_mapping.Pincode,vendor_pincode_mapping.State,vendor_pincode_mapping.City, "
                . "CONCAT('',GROUP_CONCAT(DISTINCT(services.services)),'') as Appliance "
                . "FROM vendor_pincode_mapping, service_centres, services "
                . "WHERE Vendor_ID != '0' AND vendor_pincode_mapping.Pincode !=0 "
                . "AND `Vendor_ID` = `service_centres`.`id` "
                . "AND `service_centres`.`active` = 1 "
                . "AND services.id = `Appliance_ID` "
                . "GROUP BY vendor_pincode_mapping.Pincode";
        $query = $this->db->query($sql);
        return $query;
    }

    /**
     * @desc : Check whether vendor is available in a Pincode for a particular appliance or not
     * @param : $pincode String Pincode
     * @param : $service_id Integer Service ID (TV, AC etc)
     * 
     * @return : $vendor_ids Array containing list of Vendor IDs which are available
     */
    function check_vendor_availability($pincode, $service_id) {
        $this->db->distinct();
        $this->db->select('Vendor_ID, service_centres.name as Vendor_Name,service_centres.pincode, service_centres.district, is_upcountry');
        $this->db->where('vendor_pincode_mapping.Appliance_ID', $service_id);
        $this->db->where('vendor_pincode_mapping.Pincode', $pincode);
        $this->db->from('vendor_pincode_mapping');
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where('service_centres.active', "1");
        $this->db->where('service_centres.on_off', "1");
        
        $data = $this->db->get();
        return $data->result_array();
    }

    /**
     *@desc: This function is used to get Active email templates from 247around_email_template
     * params: Array consists of  
     *        select, where values 
     * return: Array of data
     */
    function get_247around_email_template($data){
        if(!isset($data['select'])){
            $data['select'] = '*';
        }
        $this->db->select($data['select']);
        if(isset($data['where'])){
            $this->db->where($data['where']);
        }
        $this->db->where('active',1);
        $query = $this->db->get('247around_email_template');
        return $query->result_array();
    }
    /**
     * @desc: This function is used to fetch values acc to table name and columns, where provided
     * params: Array consisting of table name, primary key column(where clause column),column value to be searched, columns to be selected
     * return: Array if table name provided is present, else FALSE
     * 
     */
    function get_data($data) {
        $this->db->select($data['column_name']);
        $this->db->where($data['primary_key'], $data['id']);
        if ($this->db->table_exists($data['table_name'])) {
            $query = $this->db->get($data['table_name']);
        } else {
            return false;
        }
        return $query->result_array();
    }
    
    /**
     * @desc: This is used to get all active queries which need to be executed
     * 
     * return: Array of Active queries
     */
    function get_around_dashboard_queries($where){
        $this->db->where($where,false);
        $this->db->order_by('priority','ASC');
        $query = $this->db->get('query_report');
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to execute query form query report
     * params: STRING query
     * return : ARRAY containing counts for different queries
     */
    function execute_dashboard_query($query){
        $return_data = array();
        foreach($query as $key=>$value){
            $query1 = $this->db->query($value['query1'])->result_array();
            $sub_description = empty($value['query1_description'])? '' : $value['query1_description'];
            $return_data[$key]['main_description'] = $value['main_description'];
            $return_data[$key]['data']['query1']['description'] = $sub_description;
            $return_data[$key]['data']['query1']['query_data'] = $query1[0]['count'];
            $return_data[$key]['data']['query1']['booking_ids'] = isset($query1[0]['booking_id'])?$query1[0]['booking_id']:'';
            
            if(!empty($value['query2'])){
                $query2 = $this->db->query($value['query2'])->result_array();
                $sub_description = empty($value['query2_description'])? '' : $value['query2_description'];
                $return_data[$key]['data']['query2']['description'] = $sub_description;
                $return_data[$key]['data']['query2']['query_data'] = $query2[0]['count'];
            }
        }
        
        return $return_data;
    }
    
    /**
     *  @desc : To get All Active vendor SMS templates
     *
     *  To get the active template for all sms template which are enabled.
     *
     *  @param : void
     *  @return : Array
     */
    function get_all_active_sms_template($start,$limit,$sidx,$sord,$where) {

        $this->db->select('id,tag,template,comments,active');
        $this->db->limit($limit);
        if ($where != NULL)
            $this->db->where($where, NULL, FALSE);
        $this->db->order_by($sidx, $sord);
        $query = $this->db->get('sms_template', $limit, $start);
       
        return $query->result();
    }
    
    /**
     *  @desc : To get All Active vendor tax rates templates
     *
     *  To get the active template for all tax rates template which are enabled.
     *
     *  @param : void
     *  @return : Array
     */
    function get_all_active_tax_rates_template($start,$limit,$sidx,$sord,$where) {

        $this->db->select('id,tax_code,state,product_type,rate,from_date,to_date,active');
        $this->db->limit($limit);
        if ($where != NULL){
            $this->db->where($where, NULL, FALSE);
        }
        $this->db->order_by($sidx, $sord);
        $query = $this->db->get('tax_rates', $limit, $start);
       
        return $query->result();
    }
    
    /**
     * @desc: This is used to insert value in tax rate template table
     * @param Array
     * @return Int ID of inserted data
     */
    function insert_tax_rates_template($data){

        $this->db->insert('tax_rates', $data);
        
        return $this->db->insert_id();
    }
    /**
     * @desc: This is used to update tax rate template
     * @param ARRAY $data, INT id 
     * return: Boolean
     * 
     */
    function update_tax_rates_template($data,$id){
        $this->db->where('id', $id);
        $this->db->update('tax_rates', $data);
        log_message('info', __METHOD__ . "=> Update Tax rate Template " . $this->db->last_query() );
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @desc: This fucntion is used to delete tax rate template 
     * params: INT 
     *         id tax rate template to be deleted
     * 
     * return: Boolean
     */
    function delete_tax_rate_template($id) {
        $this->db->where('id', $id);
        $this->db->delete('tax_rates');
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    
    
    /**
     * @desc: This is used to insert value in sms template table
     * @param Array
     * @return Int ID of inserted data
     */
    function insert_sms_template($data){

        $this->db->insert('sms_template', $data);
        
        return $this->db->insert_id();
    }
    
    /**
     * @desc: This fucntion is used to delete sms template 
     * params: INT 
     *         id sms template to be deleted
     * 
     * return: Boolean
     */
    function delete_sms_template($id) {
        $this->db->where('id', $id);
        $this->db->delete('sms_template');
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @desc: This is used to update sms_template
     * @param ARRAY $data, INT id 
     * return: Boolean
     * 
     */
    function update_sms_template($data,$id){
        $this->db->where('id', $id);
        $this->db->update('sms_template', $data);
        log_message('info', __METHOD__ . "=> Update SMS Template " . $this->db->last_query() );
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     *  @desc : To get All Active vendor Vendor Escalation Policy templates
     *
     *  To get the active template for all Vendor Escalation Policy template which are enabled.
     *
     *  @param : void
     *  @return : Array
     */
    function get_vandor_escalation_policy_template($start,$limit,$sidx,$sord,$where) {

        $this->db->select('id,escalation_reason,entity,process_type,sms_to_owner,sms_to_poc,sms_body,active');
        $this->db->limit($limit);
        if ($where != NULL){
            $this->db->where($where, NULL, FALSE);
        }
        $this->db->order_by($sidx, $sord);
        $query = $this->db->get('vendor_escalation_policy', $limit, $start);
       
        return $query->result();
    }
    
    /**
     * @desc: This is used to insert value in Vendor Escalation Policy table
     * @param Array
     * @return Int ID of inserted data
     */
    function insert_vandor_escalation_policy_template($data){

        $this->db->insert('vendor_escalation_policy', $data);
        
        return $this->db->insert_id();
    }
    /**
     * @desc: This is used to update Vendor Escalation Policy template
     * @param ARRAY $data, INT id 
     * return: Boolean
     * 
     */
    function update_vandor_escalation_policy_template($data,$id){
        $this->db->where('id', $id);
        $this->db->update('vendor_escalation_policy', $data);
        log_message('info', __METHOD__ . "=> Update Vendor Escalation Policy Template " . $this->db->last_query() );
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @desc: This fucntion is used to delete Vendor Escalation Policy template 
     * params: INT 
     *         id tax rate template to be deleted
     * 
     * return: Boolean
     */
    function delete_vandor_escalation_policy_template($id) {
        $this->db->where('id', $id);
        $this->db->delete('vendor_escalation_policy');
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    

    /**
     * @desc: This is used to insert assigned engineer data into assigned engineer table
     * @param Array $data
     * @return Integer id
     */
    function insert_assigned_engineer($data) {
    $this->db->insert('assigned_engineer', $data);
    log_message('info', __METHOD__ . "=> Booking  SQL " . $this->db->last_query());
    return $this->db->insert_id();
    }

    /**
     * @desc: Get data from assgined engineer table
     * @param Array $where
     * @return boolean
     */
    function get_engineer_assigned($where) {
        $this->db->where($where);
        $query = $this->db->get('assigned_engineer');
        log_message('info', __METHOD__ . "=> Booking  SQL " . $this->db->last_query());
        if ($query->num_rows > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    //Array of queries
    function execute_query($query) {
        foreach ($query as $q) {
            $this->db->query($q);
       }
    }

    /**
     * @desc:  get all vendor 
     * @param: void
     * @return : Array
     */
    function getAllVendor() {
        $this->db->select("service_centres.name, service_centres.id ");
        $this->db->order_by("name");
        $sql = $this->db->get('service_centres');
        return $sql->result_array();
    }
    
    /**
     * @desc:  get all New vendor acc to agent sf listing
     * @param: String sf_list
     * @return : Array
     */
    function get_new_vendor($sf_list = "") {
        if($sf_list != ""){
            $where = " AND service_centres.id  IN (".$sf_list.") ";
        }else{
            $where = "";
        }
        $new_vendor = "SELECT id,name, district, state , active, on_off, 
                                            DATEDIFF(CURRENT_TIMESTAMP , create_date) AS age
                                            FROM  service_centres
                                            WHERE 
                                            create_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()
                                            ".$where."
                                            ORDER BY state";

        return $this->db->query($new_vendor)->result_array();
    }

    /**
     * @desc : This method checks assigned vendor has service tax for the given booking id
     * @param type $booking_id
     * @return Array
     */
    function is_tax_for_booking($booking_id){
        $sql = " SELECT service_centres.id,gst_no FROM booking_details, service_centres WHERE booking_id = '$booking_id' "
                . " AND  service_centres.id = booking_details.assigned_vendor_id ";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
   
    /**
     * 
     * @Desc: This function is used to get employee_relation if present in employee_relation
     * @params: agent_id
     * @return: Array or Empty
     */
    function get_employee_relation($agent_id){
        $this->db->select('*');
        $this->db->where('agent_id',$agent_id);
        $query = $this->db->get('employee_relation');
        $result = $query->result_array();
        if(!empty($result)){
            return $result;
        }else{
            return '';
}
    }
    
    /**
     * @DESC: This function is used to add employee_sf_realtion table
     *          update SF list for particular RM
     * @parmas: agent_id, sf_id
     * @return: boolean
     */
    function add_rm_to_sf_relation($agent_id, $sf_id){
        $this->db->where('agent_id', $agent_id);
        $this->db->set('service_centres_id', "CONCAT( service_centres_id, ',".$sf_id."' )", FALSE);
        $this->db->update('employee_relation');
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @Desc: This function is used to get relation of RM SF is present by using SF ID
     *          We are not getting Row for Admin group present for relation
     * @params: sf_id
     * @return: Array
     * 
     */
    function get_rm_sf_relation_by_sf_id($sf_id){
        if(!empty($sf_id)){
            $sql = "Select employee_relation.*, employee.* from employee_relation,employee "
                . "where FIND_IN_SET($sf_id,employee_relation.service_centres_id) "
                . "AND employee.groups != '"._247AROUND_ADMIN."' "
                . "AND employee_relation.agent_id = employee.id ORDER BY employee_relation.agent_id DESC";
            $response = $this->db->query($sql)->result_array();
        }else{
            $response = false;
        }
        
        return $response;
    }
    
    /**
     * @Desc: This function is used to update employee_relation table
     * @parmas: $agent_id, $sf_id
     * @return: boolean
     * 
     */
    function update_rm_to_sf_relation($agent_id,$sf_id){
        //Getting values of SF RM relation if present
        $query_result = $this->get_rm_sf_relation_by_sf_id($sf_id);
        if(!empty($query_result)){
            //Delete values from this currently assigned RM String
            $arr = explode(",",$query_result[0]['service_centres_id']);
            unset($arr[array_search($sf_id, $arr)]);
            $sf_details_list = implode(",",$arr);
            
            $this->db->where('agent_id',$query_result[0]['agent_id']);
            $this->db->set('service_centres_id',$sf_details_list);
            $this->db->update('employee_relation');
            //Now adding SF to New RM
            return $this->add_rm_to_sf_relation($agent_id, $sf_id);
            
        }else{
            //No assignment has been done earlier ADD NEW
            return $this->add_rm_to_sf_relation($agent_id, $sf_id);
        }
        
    }
    
    /**
     * @Desc: This function is used to add newly added sf to admin present in Employee_SF_relation
     * @params: sf_id
     * @return: boolean
     * 
     */
    function add_sf_to_admin_relation($sf_id){
        $sql = "Select employee_relation.* from employee_relation,employee "
                . "WHERE employee.groups = '"._247AROUND_ADMIN."' "
                . "AND employee_relation.agent_id = employee.id";
        $query_array = $this->db->query($sql)->result_array();
        if(!empty($query_array)){
            foreach($query_array as $value){
                //Now adding SF to Admin
                return $this->add_rm_to_sf_relation($value['agent_id'], $sf_id);
            }   
        }else{
            return FALSE;
        }
    }
    
    function insert_india_pincode_in_batch($rows) {
        if(!empty($rows)){
            $query = $this->db->insert_batch('india_pincode', $rows);
            if($this->db->affected_rows() > 0){
                return $this->db->affected_rows() ;
            }
            else{
                return false;
            }
        }
    }
    
    /**
     * @Desc: This function is used to count total pincodes present
     * @params: void
     * @return: void
     * 
     */
    function get_total_vendor_pincode_mapping(){
        return $this->db->count_all_results("vendor_pincode_mapping");
        
    }
    
    /**
     * @Desc: This function is used to get latest entry details for vendor_pincode_mapping table
     * @params: void
     * @return: void
     * 
     */
    function get_latest_vendor_pincode_mapping_details(){
        $sql = 'SELECT Vendor_Name, Appliance, Brand, Area, Pincode, Region, City, State'
                . ' FROM vendor_pincode_mapping ORDER BY id DESC LIMIT 0 , 1';
        $query = $this->db->query($sql);

        return $query->result_array();
        
    }
    /**
     * @desc: This method is used to assign sc for given booking
     * @param String $booking_id
     * @param Array $data
     * @return boolean
     */
    function assign_service_center_for_booking($booking_id, $data){
       if(!empty($booking_id) || $booking_id != '0'){
            $this->db->where('booking_id', $booking_id);
            $this->db->where('assigned_vendor_id is NULL', NULL, FALSE);
            $this->db->update('booking_details',$data);
            log_message('info', __METHOD__ . "=> Assigned Vendor SQL: " . $this->db->last_query() );
            return $this->db->affected_rows();
        } else {
            return false;
        }
       
    }
    /**
     * @desc This is used to retuen auto assigned booking
     * @return Array
     */
    function auto_assigned_booking(){
        $sql = "SELECT distinct bd.booking_id,bd.city, bd.booking_date,services, bd.assigned_vendor_id,name, bs.create_date "
                . " FROM `booking_state_change` as bs, "
                . " booking_details as bd, service_centres as sc, services "
                . " WHERE bs.agent_id = '"._247AROUND_DEFAULT_AGENT."' "
                . " AND bs.partner_id = '"._247AROUND."' "
                . " AND new_state = '".ASSIGNED_VENDOR."' "
                . " AND bd.booking_id = bs.booking_id "
                . " AND bd.current_status IN ('Pending','Rescheduled') "
                . " AND bs.booking_id NOT IN (SELECT bs1.booking_id "
                . " FROM booking_state_change as bs1"
                . " WHERE new_state = 'Re-Assigned_vendor' "
                . " AND bs1.booking_id = bs.booking_id )"
                . " AND bd.assigned_vendor_id = sc.id "
                . " AND bd.service_id = services.id"
                . " ORDER BY bs.create_date DESC";
       $query = $this->db->query($sql);
       return $query->result_array();
    }
    
    function get_bank_details(){
        $this->db->select('*');
        $this->db->from('bank_details');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_pincode_mapping_form_col($col_name, $where) {
        $this->db->distinct();
        $this->db->select($col_name);
        $this->db->from('vendor_pincode_mapping');
        $this->db->order_by('vendor_pincode_mapping.Pincode');
        if(!empty($where)){
            $this->db->where($where);
        }
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->join('services', 'services.id = vendor_pincode_mapping.Appliance_ID');
        $this->db->where('service_centres.active', '1');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * @desc This method is used to insert booking details when SF not exit.
     * @param Array $data
     * @return INT
     */
    function insert_booking_details_sf_not_exist($data){
        $this->db->insert("sf_not_exist_booking_details", $data);
        return $this->db->insert_id();
    }
    
    function  get_upload_pincode_file_details(){
        $sql = "SELECT e.full_name as agent_name,p.file_name,DATE(p.create_date) AS upload_date
                FROM pincode_mapping_s3_upload_details AS p 
                JOIN employee AS e ON p.agent_id = e.id
                ORDER BY upload_date DESC LIMIT 0,5";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    /**
     * @desc This is used to select data from vendor  pincode mappping and service center table
     * @param Array $where
     * @param String $select
     * @return Array
     */
    function get_vendor_mapping_data($where, $select){
        $this->db->distinct();
        $this->db->select($select);
        $this->db->from('vendor_pincode_mapping');
        $this->db->order_by('vendor_pincode_mapping.City');
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function insert_log_action_on_entity($data){
        $this->db->insert("log_entity_action", $data);
        return $this->db->insert_id();
    }
    
    function vendor_pin_code_uploads_insert($table,$data){
          $this->db->insert($table, $data); 
          return  $this->db->affected_rows();
    }
    
    function delete_vendor_pin_codes($where){
          $this->db->where($where);
          return $this->db->delete('vendor_pincode_mapping'); 
    }
    
    function insert_vendor_pincode_in_bulk($data){
            $this->db->insert_batch('vendor_pincode_mapping', $data); 
            return $this->db->affected_rows();
    }
    
    function get_pin_code_uploaded_file_history($vendorID){
          $this->db->select('');
          $this->db->where('file_type','vendor_pincode_'.$vendorID);
          $query = $this->db->get('file_uploads');
          return $query->result_object();
    }
    
    function update_not_found_sf_table($where,$data){
        $this->db->or_where($where,FALSE);
        $this->db->set($data);
        $this->db->UPDATE('sf_not_exist_booking_details');
    }
    
    function get_india_pincode_distinct_area_data($pincode) {
            $this->db->select('state,district as city');
            $this->db->where('pincode', $pincode);
            $this->db->group_by('district,state');
            $query = $this->db->get('india_pincode');
            return $query->result_array();
     }
     
     function get_vendor_brand($vendorID){
          $this->db->select('brands');
          $this->db->where('id', $vendorID);
          $query = $this->db->get('service_centres');
          return $query->result_array();
     }
     
     function update_file_status($status,$fileName){
         $data=array('result'=>$status);
         $this->db->where('file_name',$fileName);
        return $this->db->update("file_uploads",$data);
     }
     
     function get_vendor_with_bank_details($select,$where){
         $this->db->select($select,FALSE);
        $this->db->where($where);
        $this->db->join('account_holders_bank_details', 'account_holders_bank_details.entity_id = service_centres.id AND account_holders_bank_details.entity_type="SF" AND '
                . 'account_holders_bank_details.is_active=1','left');
        $sql = $this->db->get('service_centres');
        return $sql->result_array();
     }
     /*
      * Delete data from vendor_pincode_mapping on the basis of ids
      */
     function delete_vendor_pin_codes_in_bulk($whereIN){
          $this->db->where_in('id', $whereIN);
          return $this->db->delete('vendor_pincode_mapping'); 
    }
    
    function getvendor_escalation_log($where, $select){
        $this->db->where($where);
        $this->db->select($select);
        $query =  $this->db->get("vendor_escalation_log");
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get detail of given email id
    **/
    function search_email($email_id){
       
        $sql =  "SELECT 'partner' as entity_type, company_name as 'name', CASE 
                            WHEN `primary_contact_email` = '".$email_id."' THEN 'primary_contact_email'
                            WHEN `owner_email` = '".$email_id."' THEN 'owner_email'
                            WHEN `owner_alternate_email` = '".$email_id."' THEN 'owner_alternate_email'
                            WHEN `upcountry_approval_email` = '".$email_id."' THEN 'upcountry_approval_email'
                            END AS 'email_type'
            FROM partners WHERE primary_contact_email = '".$email_id."' OR owner_email = '".$email_id."' OR owner_alternate_email = '".$email_id."' OR upcountry_approval_email = '".$email_id."'
            UNION
            SELECT 'vendor' as entity_type, company_name as 'name', CASE 
                            WHEN `email` = '".$email_id."' THEN 'email'
                            WHEN `primary_contact_email` = '".$email_id."' THEN 'primary_contact_email'
                            WHEN `owner_email` = '".$email_id."' THEN 'owner_email'
                            END AS 'email_type'
            FROM service_centres WHERE email= '".$email_id."' OR primary_contact_email = '".$email_id."' OR owner_email = '".$email_id."'";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to get searched gstin detail
    **/
    function search_gstn_number($gst_no){
       $last_month = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s'). ' - 30 days'));
       $sql =  "SELECT 'vendor' as 'entity', company_name as 'lager_name', gst_no as 'gst_number', gst_status as 'status', gst_taxpayer_type as 'type' FROM service_centres where gst_no = '".$gst_no."'
                UNION
                SELECT 'partner' as 'entity', public_name as 'lager_name', gst_number as 'gst_number', 'status', 'type' FROM partners where gst_number = '".$gst_no."'
                UNION
                SELECT 'Previously Searched Data' as 'entity', lager_name as 'lager_name', gst_number as 'gst_number', status as 'status', type as 'type' FROM gstin_detail where gst_number = '".$gst_no."' and create_date >= '".$last_month."'
                ";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * @desc This is used to select data from vendor  pincode mappping and service center table
     * @param Array $where
     * @param String $select
     * @return Array
     */
    
    
  
            
    function get_micro_warehouse_history($id) {
        $this->db->select('w_on_off.partner_id,w_on_off.vendor_id,w_on_off.active,w_on_off.create_date,s.name,s.district');
        $this->db->from('micro_warehouse_state_mapping as m');
        $where = array('m.id'=>$id); 
        $this->db->join('warehouse_on_of_status as w_on_off', 'm.vendor_id = w_on_off.vendor_id');
        $this->db->join('service_centres as s', 's.id = w_on_off.vendor_id');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_state_data()
    {
        $this->db->select('state,UPPER( `state` ) as statevalue');
        $this->db->from('state_code');
        $result=$this->db->get()->result_array();
        return $result;
    }
    function get_city_bystate($state_value)
    {
        $this->db->select('distinct(district) as city');
        $this->db->from('service_centres');
        $this->db->where('state',$state_value);
        $result=$this->db->get()->result_array();
        return $result;
    }
     /*
     * @desc This is used to get distinct count of pincode of particular state from india pincode table
     */
    function get_india_pincode_group_by_state($array=array()){
        $this->db->select('distinct(`india_pincode`.`state`),count(DISTINCT pincode) as state_pincode_count,state_code.id as state_id');
        $this->db->from('india_pincode');
        $this->db->join('state_code','india_pincode.state=state_code.state','left');
        if(!empty($array)){
            $this->db->where_in('state_code.id',$array);
        }
        $this->db->group_by('india_pincode.state');
        return $this->db->get()->result_array();
    }
    /*
     * @desc This is used to get distinct count of pincode group by state and appliance from vendor pincode mapping
     */
    function get_vendor_mapping_groupby_applliance_state($array=array()){
        $this->db->select('distinct vendor_pincode_mapping.State,state_code.id ,Appliance_ID,count(distinct vendor_pincode_mapping.Pincode) as total_pincode');
        $this->db->from('vendor_pincode_mapping');
        $this->db->join('state_code','vendor_pincode_mapping.State=state_code.state','left');
        if(!empty($array))
        {
            $this->db->where_in('state_code.id',$array);
        }
        $this->db->group_by('vendor_pincode_mapping.Appliance_ID');
        $this->db->group_by('vendor_pincode_mapping.State');
        $result=$this->db->get()->result_array();
        return $result;
           
    }
     /*
     * @desc This is used to get active service from services table
     */
        function get_active_services()
    {
        $return=array();
        $this->db->select('id,services');
        $this->db->from('services');
        $this->db->where('isBookingActive',1);
        $this->db->order_by('services','ASC');
        $result=$this->db->get()->result_array();
        if(count($result)>0)
        {
            foreach($result as $value)
            {
                $id=$value['id'];
                $return[$id]=$value['services'];
            }
        }
        return $return;
    }
     /*
     * @desc This is used to get active state from state_code table
     */
    function get_active_state()
    {
        $return=array();
        $this->db->select('id,state');
        $this->db->from('state_code');
        $result=$this->db->get()->result_array();
        if(count($result)>0)
        {
            foreach($result as $value)
            {
                $id=$value['id'];
                $return[$id]=$value['state'];
            }
        }
        return $return;
    }
     /**
     * @Desc: This function is used to get relation of RM SF is present by using State Code
     *          We are not getting Row for Admin group present for relation
     * @params: sf_id
     * @return: Array
     * 
     */
    function get_rm_sf_relation_by_state_code($state_code){
        if(!empty($state_code)){
            $sql = "Select employee_relation.*, employee.* from employee_relation,employee "
                . "where FIND_IN_SET($state_code,employee_relation.state_code) "
                . "AND employee.groups != '"._247AROUND_ADMIN."' "
                . "AND employee_relation.agent_id = employee.id ORDER BY employee_relation.agent_id DESC";
            $response = $this->db->query($sql)->result_array();
        }else{
            $response = false;
        }
        
        return $response;
    }
    
     /**
     * @Desc: This function is used to insert entity identity proof details
     * @params: $data Array
     * @return: $last insert id
     * 
     */
    function add_entity_identity_proof($data) {
        $this->db->insert('entity_identity_proof', $data);
        return $this->db->insert_id();
    }
    
    function update_entity_identity_proof($where, $data){
        $this->db->where($where);
        $this->db->update('entity_identity_proof', $data);
        log_message('info', __METHOD__ . "=> Update Engineer " . $this->db->last_query() );
    }
    
    function get_engg_full_detail($select="*", $where= array()){
        $this->db->select($select);
        $this->db->where($where);
        $this->db->join("entity_identity_proof", "entity_identity_proof.entity_id = engineer_details.id AND entity_identity_proof.entity_type='engineer'");
        $query = $this->db->get('engineer_details');
        return $query->result_array();
    }
    
    /*
    *@Desc - This function is used to get data for vendor penalty summary
    */
    function sf_panalty_summary($vendors, $start_date, $end_date){
        $vendor_ids = "";
        if(!in_array("All", $vendors)){
            $vendor_ids = "service_centres.id IN (";
            foreach ($vendors as $value) {
                $vendor_ids .= "'".$value."',";
            }
            $vendor_ids = rtrim($vendor_ids, ",");
            $vendor_ids .= ") AND";
        }
       
        $sql = "select service_centres.name, penalty_details.criteria, "
            . "count(DISTINCT penalty_on_booking.booking_id) as total_booking_id, "
            . "count(penalty_on_booking.id) as total_penalty_count, "
            . "SUM(penalty_on_booking.penalty_amount) as penalty_amount "
            . "from penalty_on_booking join service_centres on service_centres.id = penalty_on_booking.service_center_id "
            . "join penalty_details on penalty_details.id = penalty_on_booking.criteria_id "
            . "WHERE ".$vendor_ids." penalty_remove_reason IS NULL AND "
            . "penalty_on_booking.create_date >= '$start_date' AND penalty_on_booking.create_date <= '$end_date' "
            . "group by criteria_id, service_centres.id ORDER BY service_centres.id ASC";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
   
    /*
     * @Desc - This function is used to map brands to a SF
     * @author - Prity Sharma
     * @date - 26-06-2019
     * @params - $Sf_id (Service Center Id) 
    */
    function map_vendor_brands($sf_id, $arr_brands)
    {
        $this->db->delete('service_center_brand_mapping', array('service_center_id' => $sf_id));
        foreach ($arr_brands as $rec_brand) {
            $data = array('service_center_id' => $sf_id, 'brand_name' => $rec_brand);
            $this->db->insert('service_center_brand_mapping',$data);
        }
    }
    
    /*
     * @Desc - This function is used to get brands mappad to a SF
     * @author - Prity Sharma
     * @date - 26-06-2019
     * @params - $Sf_id (Service Center Id) 
    */
    function get_mapped_brands($sf_id)
    {
        $this->db->select('GROUP_CONCAT(service_center_brand_mapping.brand_name) as map_brands');
        $this->db->where(['service_center_id' => $sf_id, 'isActive' => 1]);
        $query = $this->db->get('service_center_brand_mapping');
        return $query->result_array()[0]['map_brands']; 
    }
    function get_sf_call_load($sfArray){
        $sfString = implode("','",$sfArray);
        $sql = "SELECT assigned_vendor_id,COUNT(booking_id) as booking_count FROM booking_details WHERE assigned_vendor_id IN ('".$sfString."') AND current_status NOT IN ('Completed','Cancelled') "
                . "GROUP BY assigned_vendor_id ORDER BY COUNT(booking_id) LIMIT 1";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}
