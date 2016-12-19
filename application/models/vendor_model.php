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
    function viewvendor($vendor_id = "",$active = "",$sf_list = "") {
        $where_id = "";
        $where_active = "";
        $where_sf = "";
        $where_final = "";

        if ($vendor_id != "") {
            $where_id .= "id= '$vendor_id'";
        }
        if ($active != "") {
            $where_active .= "active= '$active'";
        }
        if($sf_list != ""){
            $where_sf .= "service_centres.id  IN (" .$sf_list.")";
        }
        if($vendor_id != "" && $active != "" ){
            $where_final = 'where '.$where_id." AND ".$where_active;
        }
        if($vendor_id != ''){
            $where_final = 'where '.$where_id;
        }
        if($active != ""){
            $where_final = 'where '.$where_active;
        }
        if($sf_list != "" ){
            $where_final = 'where '.$where_sf;
        }
        if($sf_list != "" && $active != ""){
            $where_final = 'where '.$where_sf." AND ".$where_active;
        }
        
        $sql = "Select * from service_centres $where_final";

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
        $sql = "Select * from service_centres where id='$id'";

        $query = $this->db->query($sql);

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
        $query = $this->db->query("Select id,services from services where isBookingActive='1'");
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

    /**
     * @desc: This function is to activate vendor who is already registered with us and are inactive/deactivated.
     *
     * @param: $id
     *         - Id of vendor to whom we would like to activate
     * @return: void
     */
    function activate($id) {
        $sql = "Update service_centres set active= 1 where id='$id'";
        $this->db->query($sql);
        //Changing Flag Active to 1 in service centres login table
        $sql = "Update service_centers_login set active= 1 where service_center_id='$id'";
        $this->db->query($sql);
    }

    /**
     * @desc: This function is to deactivate vendor who is already registered with us and are active.
     *
     * @param: $id
     *         - Id of vendor to whom we would like to deactivate
     * @return: void
     */
    function deactivate($id) {
        $sql = "Update service_centres set active= 0 where id='$id'";
        $this->db->query($sql);
        //Changing Flag Active to 0 in service centres login table
        $sql = "Update service_centers_login set active= 0 where service_center_id='$id'";
        $this->db->query($sql);
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

        $this->db->select("service_centres.name, service_centres.id ");
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
        $this->db->where('vendor_pincode_mapping.active', 1);
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where('service_centres.active', '1');

        if ($city != "")
            $this->db->where('vendor_pincode_mapping.City', $city);
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
    if($query->num_rows > 0){
        return $query->result_array()[0];
    } else {
        $state['state'] = "";
        return $state;
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
    function getDistrict_from_india_pincode($state = "") {
        $this->db->distinct();
        $this->db->select('district');
        if ($state != "") {
            $this->db->where('LOWER(state)', strtolower($state));
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
    function getDistrict($state = "") {
        $this->db->distinct();
        $this->db->select('vendor_pincode_mapping.City as district');
        $this->db->from('vendor_pincode_mapping');
        $this->db->order_by('vendor_pincode_mapping.City');

        $this->db->where('vendor_pincode_mapping.active', 1);
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');
        $this->db->where('service_centres.active', '1');

        if ($state != "")
            $this->db->where('vendor_pincode_mapping.State', $state);

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
        $this->db->where('vendor_pincode_mapping.active', 1);
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
    function getPincode_from_india_pincode($district) {
        $this->db->distinct();
        $this->db->select('pincode');
        $this->db->where('LOWER(district)', strtolower($district));
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
        $this->db->select('booking_date, booking_timeslot');
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
    function getVendorSmsTemplate($sms_tag) {

        $this->db->select("template");
        $this->db->where('tag', $sms_tag);
        $this->db->where('active', 1);
        $query = $this->db->get('sms_template');
        if ($query->num_rows > 0) {
            $template = $query->result_array();
            return $template[0]['template'];
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
        $this->db->where('active', 1);
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
        $this->db->where('active', 1);
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
        $this->db->select('Vendor_Name, Brand, Area, Region, vendor_pincode_mapping.Pincode');
        $this->db->from('vendor_pincode_mapping');
        $this->db->join('service_centres', 'service_centres.id = vendor_pincode_mapping.Vendor_ID');

        $this->db->where('Appliance_ID', $data['service_id']);
        if ($data['city'] != 'Select City')
            $this->db->where('vendor_pincode_mapping.City', $data['city']);

        if ($data['pincode'] != "Select Pincode")
            $this->db->where('vendor_pincode_mapping.Pincode', $data['pincode']);

        $this->db->where('vendor_pincode_mapping.active', 1);
        $this->db->where('service_centres.active', 1);
        //Checking Temporary On/Off values
        $this->db->where('service_centres.on_off', 1);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @desc:  get vendor name and id. set flag 1 for active vendor and 0 for inactive vendor
     * @param: service_center_id, flag
     * @return : Array
     */
    function getActiveVendor($service_center_id = "", $active = 1) {
        $this->db->select("service_centres.name, service_centres.id ");
        if ($service_center_id != "") {
            $this->db->where('id', $service_center_id);
        }
        $this->db->order_by("name");
        if ($active == 1)
            $this->db->where('active', 1);
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
        $this->db->select('Vendor_Name as name, Vendor_ID as id, Appliance, Appliance_ID');
        $this->db->where('active', 1);
        if ($vendor_id != "")
            $this->db->where('Vendor_ID', $vendor_id);

        if ($city != "")
            $this->db->where('City', $city);

        $this->db->order_by('Appliance', 'ASC');

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
            $service_center = $this->getActiveVendor($vendor['vendor_id']);
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
        $this->db->select('*');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get('service_center_booking_action');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
	    log_message('info', __METHOD__ . "=> Booking ID: " . $booking_id . "=> Old vendor data " .
		print_r($result, TRUE));

	    $this->db->where('booking_id', $booking_id);
        $this->db->delete("service_center_booking_action");
        log_message('info', __METHOD__ . "=> Delete SQL: " . $this->db->last_query() );

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
         $query = $this->db->query("SELECT DISTINCT Vendor_ID,Vendor_Name from vendor_pincode_mapping where Appliance_ID = ". $service_id. ' Order By Vendor_Name');
         return $query->result_array();
    }
    /**
     *  @desc: get distinct Appliance details
     *
     *  @param : Service ID(Appliance ID)
     *  @return : array of all data
     */
     function get_distinct_vendor_service_details($vendor_id){
         $query = $this->db->query("SELECT DISTINCT Appliance,Appliance_ID from vendor_pincode_mapping where Vendor_ID = ". $vendor_id. ' Order By Appliance');
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
        $this->db->where('Vendor_ID', $data['Vendor_ID']);
        $this->db->where('Pincode', $data['Pincode']);
        $this->db->where('Area', $data['Area']);
        $this->db->where('City', $data['City']);
        $this->db->where('State', $data['State']);
        $this->db->where('Appliance_ID', $data['Appliance_ID']);
        $query = $this->db->get('vendor_pincode_mapping');
        
        if($query->num_rows >0){

            return false;

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

    function delete_vendor($data){
        $this->db->where('Appliance_ID', $data['Appliance_ID']);
        $this->db->where('Pincode', $data['Pincode']);
        $this->db->where('Vendor_ID', $data['Vendor_ID']);
        $this->db->delete('vendor_pincode_mapping'); 
        if($this->db->affected_rows() > 0){
            return true;
        }else{
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
        $sql = "SELECT vendor_pincode_mapping.Pincode, "
                . "CONCAT('',GROUP_CONCAT(DISTINCT(vendor_pincode_mapping.Appliance)),'') as Appliance "
                . "FROM vendor_pincode_mapping, service_centres "
                . "WHERE Vendor_ID != '0' AND `Vendor_ID` = `service_centres`.`id` "
                . "AND `service_centres`.`active` = 1 "
                . "GROUP BY vendor_pincode_mapping.Pincode ";
        $query = $this->db->query($sql);
        return $query->result_array();
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
        $this->db->select('Vendor_ID, Vendor_Name');
        $this->db->where('vendor_pincode_mapping.Appliance_ID', $service_id);
        $this->db->where('vendor_pincode_mapping.Pincode', $pincode);
        $this->db->where('vendor_pincode_mapping.active', "1");
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
    function get_around_dashboard_queries(){
        $this->db->where('active',1);        
        $query = $this->db->get('query_report');
        
        return $query->result_array();
    }
    
    /**
     * @desc: This function is used to execute query form query report
     * params: STRING query
     * return : ARRAY containing counts for different queries
     */
    function execute_around_dashboard_query($query){
        foreach($query as $key=>$value){
            $query = $this->db->query($value['query']);
            $count[$key] = $query->result_array();
        }
        
        return $count;
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
        $new_vendor = "SELECT id,name, district, state ,
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
    function is_tax_for_booking($booking_id, $where){
        $sql = " SELECT service_centres.id FROM booking_details, service_centres WHERE booking_id = '$booking_id' "
                . " AND  service_centres.id = booking_details.assigned_vendor_id AND $where";
        $query = $this->db->query($sql);
        if($query->num_rows > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
     /**
     * @desc: This function is to suspend vendor who is already registered with us 
     *
     * @param: $id,$on_off
     *         
     * @return: void
     */
    function temporary_on_off_vendor($id,$on_off) {
        $sql = "Update service_centres set on_off = '$on_off' where id='$id'";
        $this->db->query($sql);
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
        $sql = "Select employee_relation.* from employee_relation,employee "
                . "where FIND_IN_SET($sf_id,employee_relation.service_centres_id) "
                . "AND employee.groups != '"._247AROUND_ADMIN."' "
                . "AND employee_relation.agent_id = employee.id";
        return $this->db->query($sql)->result_array();
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
             $this->add_rm_to_sf_relation($agent_id, $sf_id);
            
        }else{
            //No assignment has been done earlier ADD NEW
            $this->add_rm_to_sf_relation($agent_id, $sf_id);
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
                 $this->add_rm_to_sf_relation($value['agent_id'], $sf_id);
            }   
        }else{
            return FALSE;
        }
    }
    
    function insert_india_pincode_in_batch($rows) {
	$query = $this->db->insert_batch('india_pincode', $rows);
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
                . ' FROM vendor_pincode_mapping ORDER BY create_date DESC LIMIT 0 , 1';
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
        $this->db->where('booking_id', $booking_id);
        $this->db->where('assigned_vendor_id is NOT NULL', NULL, FALSE);
        $this->db->update('booking_details',$data);
        return $this->db->affected_rows();
        
    }
    
}