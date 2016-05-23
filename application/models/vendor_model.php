<?php

class vendor_model extends CI_Model {
  /**
  * @desc load both db
  */
    function __construct() {
    parent::__Construct();

    $this->db_location = $this->load->database('default1', TRUE,TRUE);
    $this->db = $this->load->database('default', TRUE,TRUE);
  }

    function viewvendor($vendor_id = "") {
      $where = "";

      if($vendor_id != ""){
        $where .= "where id= '$vendor_id'";
      }

      $sql="Select * from service_centres $where";

      $query = $this->db->query($sql);

      return $query->result_array();
  }

    function editvendor($id) {
    $sql="Select * from service_centres where id='$id'";

    $query = $this->db->query($sql);

    return $query->result_array();
  }

    function edit_vendor($vendor, $id) {
    $this->db->where('id',$id);
    $this->db->update('service_centres',$vendor);
  }

    function add_vendor($vendor) {

    $this->db->insert('service_centres', $vendor);
  }

    function selectservice() {
  $query = $this->db->query("Select id,services from services where isBookingActive='1'");
  return $query->result();
    }

    function selectbrand() {
  $sql = "Select DISTINCT brand_name from appliance_brands order by brand_name";
  $query = $this->db->query($sql);

  return $query->result();
    }

    function activate($id) {
  $sql = "Update service_centres set active= 1 where id='$id'";
  $query = $this->db->query($sql);
    }

    function deactivate($id) {
  $sql = "Update service_centres set active= 0 where id='$id'";
  $query = $this->db->query($sql);
    }

    function delete($id) {
  $sql = "Delete from service_centres where id='$id'";
  $query = $this->db->query($sql);
    }

    /**
     *  @desc : This function is to insert pincode and vendor_id mapping into a temporary table.
     * The original table is dropped and temp table becomes the final table if all pincodes are
     * are inserted without any error.
     *  @param :
     *  @return : void
     */
    function insert_vendor_pincode_mapping_temp($details) {
  return $this->db->insert_batch('vendor_pincode_mapping_temp', $details);
    }

    /**
     *  @desc : This function is to insert pincode in master pincode table
     *  @param :
     *  @return : void
     */
    function insert_pincode($pincodes) {
  $this->db->insert_batch('india_pincode', $pincodes);

  return $this->db->affected_rows();
    }

    function get_non_working_days_for_vendor($service_centre_id) {
        $this->db->select('non_working_days');
        $this->db->where('id', $service_centre_id);
        $query = $this->db->get('service_centres');
        return $query->result_array();
    }

    //Drops original pincode mapping table and renames the "vendor_pincode_mapping_temp" table to
    //"vendor_pincode_mapping"
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

    function getEscalationReason(){
      $this->db->select('id,escalation_reason');
      $this->db->where('active', '1');
      $query = $this->db->get("vendor_escalation_policy");
      return $query->result_array();

    }

    function getVendor($booking_id){

      $this->db->select("service_centres.name, service_centres.id ");
      $this->db->from('booking_details');
      $this->db->where('booking_id',$booking_id);
      $this->db->join('service_centres','service_centres.id = booking_details.assigned_vendor_id');
      $query = $this->db->get();
      return $query->result_array();
    }

    function insertVendorEscalationDetails($details){
      
      $this->db->insert('vendor_escalation_log', $details);
      return   $this->db->insert_id();
    }

    function getVendorContact($vendor_id){
       $this->db->select('name, primary_contact_phone_1, primary_contact_email, primary_contact_phone_1, owner_email, owner_phone_1');
       $this->db->where('id',$vendor_id);
       $query = $this->db->get('service_centres');
       return $query->result_array();
    }

    function getEscalationPolicyDetails($escalation_reason_id){
      $this->db->select('*');
      $this->db->where('id', $escalation_reason_id);
      $query = $this->db->get('vendor_escalation_policy');
      return $query->result_array();
          
    }

    function getUserDetails($booking_id){
      $this->db->select('users.name, users.phone_number');
      $this->db->where('booking_id',$booking_id);
      $this->db->from('booking_details');
      $this->db->join('users', 'users.user_id = booking_details.user_id');
      $query = $this->db->get();
      return $query->result_array();
    }

    function selectSate($city=""){
      $this->db->distinct();
      $this->db->select('state');
      $this->db->order_by('state');
      if($city !="")
         $this->db->where('city', $city);
      $query = $this->db->get('india_pincode');
      return $query->result_array();
    }

    function getDistrict($state){
      $this->db->distinct();
      $this->db->select('district');
      $this->db->order_by('district');
      $this->db->where('state',$state);
      $query = $this->db->get('india_pincode');
      return $query->result_array();
    }

    function getPincode($district){
      $this->db->distinct();
      $this->db->select('pincode');
      $this->db->order_by('pincode');
      $this->db->where('district', $district);
      $query = $this->db->get('india_pincode');
      return $query->result_array();
    }
    
    /**
     * Get POC and owner email for Active Service Center. 
     */
    function select_active_service_center_email(){
      $this->db->select('primary_contact_email, owner_email');
      $this->db->where('active',1);
      $query = $this->db->get('service_centres');
      return $query->result_array();
        
    }

    function getBookingDateFromBookingID($booking_id){
      $this->db->select('booking_date, booking_timeslot');
      $this->db->where('booking_id', $booking_id);
      $query = $this->db->get('booking_details');
      if ($query->num_rows() > 0) {
         return $query->result_array();
      }
    }

    function insertS3FileDetails($data){
  $this->db->insert('pincode_mapping_s3_upload_details', $data);
    }

    function getLatestVendorPincodeMappingFile(){
  $sql = 'SELECT file_name FROM pincode_mapping_s3_upload_details WHERE bucket_name = "vendor-pincodes" ORDER BY create_date DESC LIMIT 0 , 1';

      $query = $this->db->query($sql);

      return $query->result_array();
    }

    function getVendorSmsTemplate($sms_tag){

      $this->db->select("template");
      $this->db->where('tag', $sms_tag);
      $this->db->where('active', 1);
      $query = $this->db->get('sms_template');
      $template = $query->result_array();
      return $template[0]['template'];

    }
    
    // update escalation policy flag in json in vendor escalation log table
    function updateEscalationFlag($id, $flag){
      unset($flag[0]['escalation_reason']); 
      unset($flag[0]['id']);
      unset($flag[0]['sms_body']);
      unset($flag[0]['mail_subject']);
      unset($flag[0]['mail_body']);
      unset($flag[0]['active']);
      unset($flag[0]['create_date']);

      $reason_flag['escalation_policy_flag'] = json_encode($flag);
      
      $this->db->where('id', $id);
      $this->db->update('vendor_escalation_log', $reason_flag);
    }

    function get_city(){

      $this->db->distinct();
      $this->db->select('City');
      $this->db->where('active', 1);
      $query = $this->db->get('vendor_pincode_mapping');
      return $query->result_array(); 

    }

    function get_services_category_city_pincode(){
      $service = $this->db->query("Select id,services from services where isBookingActive='1'");
      $query1['services'] = $service->result_array();
      $query2['city'] = $this->get_city(); 

      $this->db->distinct();
      $this->db->select('Pincode');
      $this->db->where('active', 1);
      $query4 = $this->db->get('vendor_pincode_mapping');

      $query3['pincode'] = $query4->result_array(); 

      return array_merge($query1, $query2, $query3);
     

    }

    function getVendorFromVendorMapping($data){
      $this->db->distinct();
      $this->db->select('Vendor_Name, Brand, Area, Region, Pincode');
      $this->db->where('Appliance_ID', $data['service_id']);
      if($data['city'] != 'Select City')
         $this->db->where('City', $data['city'] );

       if($data['pincode'] != "Select Pincode")
        $this->db->where('Pincode', $data['pincode']);

      $this->db->where('active', 1);

      $query = $this->db->get('vendor_pincode_mapping');
      return $query->result_array();
    }

    function getActiveVendor(){
       $this->db->select("service_centres.name, service_centres.id ");
       $this->db->where('active', 1);
       $sql = $this->db->get('service_centres');
       return $sql->result_array();

    }

    function get_vendor_city_appliance(){
      $service = $this->db->query("Select id,services from services where isBookingActive='1'");
      $query1['services'] = $service->result_array();
      $query2['city'] = $this->get_city(); 

      $this->db->distinct();
      $this->db->select('id, name');
      $this->db->order_by('name', 'ASC');
      $this->db->where('active',1);
      $query = $this->db->get('service_centres');
      $query3['vendor'] = $query->result_array();

      $source['source'] = $this->partner_model->get_all_partner_source();

       return array_merge($query1, $query2, $query3, $source);
    }

    function getVendorFromMapping($vendor_id ="", $city="", $Appliance_ID = ""){
      $appliances = "";
      $cities = "";
      if($Appliance_ID  !=""){
        $appliances = ", Appliance";
      }
      if($city !=""){
        $cities = " , City";
      }
      $this->db->distinct();
      $this->db->select('Vendor_Name, Vendor_ID'. $cities.$appliances);
      $this->db->where('active',1);
      if($vendor_id !="" )
        $this->db->where('Vendor_ID', $vendor_id);

      if($city !="")
        $this->db->where('City', $city);

      if($Appliance_ID !="")
        $this->db->where('Appliance_ID', $Appliance_ID);

      //$this->db->order_by('Vendor_ID');
      
      $query = $this->db->get('vendor_pincode_mapping');

      return $query->result_array();

    }

     /**
     * @desc: This function is used to check service center code alredy exist or not
     * @param: String(Service center code)
     * @return : if service center code exist return true otherwise return false.
     */
    function check_sc_code_exist($sc_code){
      $this->db->select('*');
      $this->db->where('sc_code', $sc_code);
      $query = $this->db->get('service_centres');
      if ($query->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
    }
    function get_vendor_performance($vendor){

      $group_By = "";
      $where = "";
      $month = "";
      if($vendor['period'] == 'All Month'){
          $group_By .= " GROUP BY DATE_FORMAT(`closed_date`, '%M, %Y') ORDER BY DATE_FORMAT(`booking_details`.`create_date`, '%Y') DESC, completed_booking";
          $month = " DATE_FORMAT(`closed_date`,'%M, %Y') `month`,";
      }

       // Year Wise Dataset
    if($vendor['period'] == "All Year"){
        // get group by create date column.
        $group_By = " GROUP BY DATE_FORMAT(`create_date`, '%Y') ORDER BY DATE_FORMAT(`create_date`, '%Y') DESC, completed_booking";
        $month = " DATE_FORMAT(`create_date`, '%Y') `month`,";
    }

     // Week Wise Dataset
    if($vendor['period'] == "Week"){
        // get group by create date column.
        $group_By = " GROUP BY WEEK(`create_date`)  ORDER BY DATE_FORMAT(`create_date`,'%Y') DESC , DATE_FORMAT(`create_date`,'%m') DESC, completed_booking";
        $month = "  CONCAT(date(create_date), ' - ', date(create_date) + INTERVAL 7 DAY)   `month`,";
    }
    
    //Quater Wise DataSet
    if($vendor['period']== 'Quater'){
        $group_By .= " GROUP BY Year(create_date) Desc, QUARTER(create_date) DESC";
        $month = " CASE QUARTER(create_date) 

        WHEN 1 THEN 'Jan - Mar'

        WHEN 2 THEN 'Apr - Jun'
 
        WHEN 3 THEN 'July - Sep'

        WHEN 4 THEN 'Oct - Dec'

        END AS `month` ,  Year(create_date) as year, ";
    }

    if($vendor['service_id'] !=""){
        $where .= " AND service_id = '".$vendor['service_id']."'";
    }

    if($vendor['source'] !=""){
        $where .= " AND source = '".$vendor['source']."'" ;
    }

      $vendors = $this->getVendorFromMapping($vendor['vendor_id'], $vendor['city'], $vendor['service_id']);


      $array =  array();
     
      foreach ($vendors as $key => $value) {

          $sql = "SELECT $month
                  SUM(CASE WHEN `current_status` = 'Completed' THEN 1 ELSE 0 END) AS completed_booking,
                  SUM(CASE WHEN `current_status` = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_booking
                  from booking_details where assigned_vendor_id = $value[Vendor_ID]  $where $group_By";

            $data = $this->db->query($sql);
            $result = $data->result_array();
           

             if(!empty($result)){
                 if($vendor['service_id'] !=""){
                    $result[0]['Appliance'] = $value['Appliance'];
                 }
                 if($vendor['city'] !=""){
                    $result[0]['City'] = $value['City'];
                 }

                 if($vendor['source'] !=""){
                     $result[0]['source'] = $vendor['source'];
                 }

                 $result[0]['Vendor_Name'] = $value['Vendor_Name'];
                 $result[0]['Vendor_ID'] = $value['Vendor_ID'];
          
                 array_push($array, $result);
            }
      }
      if($vendor['sort'] == "ASC"){
         arsort($array);
      } else {
        asort($array);
      }
      
     return $array;

    }

    function insert_service_center_action($data){
      $this->db->insert('service_center_booking_action', $data);

    }

    function update_service_center_action($data){
      $this->db->where('booking_id', $data['booking_id']);
      $this->db->update('service_center_booking_action', $data);
    }

    function getbooking_charges(){
      $charges = $this->booking_model->getbooking_charges();
      foreach ($charges as $key => $value) {
        $charges[$key]['service_centres']  = $this->getVendor($value['booking_id']);
        $charges[$key]['query2'] = $this->booking_model->get_unit_details($value['booking_id']);
        $charges[$key]['booking'] = $this->booking_model->booking_history_by_booking_id($value['booking_id']);
      }

      return $charges;
    }
    /**
     * @desc:  when reassign service center, delete previous action perform by service center
     */
    function delete_previous_service_center_action($booking_id){
        $charges = $this->booking_model->getbooking_charges(); 
        log_message('info', "Entering: " . __METHOD__."  ". print_r($charges));
        $this->db->where('booking_id', $booking_id);
        $this->db->delete("service_center_booking_action");
    }

}