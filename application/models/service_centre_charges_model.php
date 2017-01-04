<?php

class service_centre_charges_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
    }

    /**
   *  @desc : This function is to insert service centre charges from excel
   *  @param : all the service centre charges
   *  @return : void
   */
    function insert_service_centre_charges($details) {
        $this->db->insert('service_centre_charges', $details);
    }

     /**
   *  @desc : This function is to select service centre charges 
   *  @param : service id
   *  @return : all the service centre charges for particular service
   */
    public function get_prices_for_particular_appliance($service_id)
    {
		$this->db->select('*');
		$this->db->from('service_centre_charges');
		$this->db->where(array('service_id' => $service_id));
		$this->db->order_by('category ASC, capacity ASC');
		$query = $this->db->get();

		return $query->result();
    }

    function insert_data_in_temp($table_name, $rows){
      return $this->db->insert_batch($table_name, $rows);
    }

    function switch_temp_table($table_name){
      $this->load->dbforge();

      //rename original to temp2 since there is no efficient copy table command
      $this->dbforge->rename_table($table_name, $table_name."2");

      //rename temp to original
      $this->dbforge->rename_table($table_name."_tmp", $table_name);

      //rename temp2 to temp
      $this->dbforge->rename_table($table_name."2", $table_name."_tmp");

      //truncate temp
      $this->db->empty_table($table_name."_tmp");

    }

    function getPartnerID($source){

      $this->db->select('id');
      $this->db->where('source', $source);
      $query = $this->db->get('bookings_sources');
      $data = $query->result_array();
      if ($query->num_rows() > 0) {
          return $data[0]['id'];
      } else {

        return '';
      }

    }
    
    function get_service_city_source_all_appliances_details(){
        $query = $this->db->query("Select id,services from services where isBookingActive='1'");
        $query1['services'] = $query->result_array();
        
        $this->db->distinct();
        $this->db->select('City');
        $sql2 = $this->db->get('vendor_pincode_mapping');

        $query2['city'] = $sql2->result_array();
        
        $query3['source'] = $this->partner_model->get_all_partner_source();

        $query4['categories'] = $this->getcategory();
        
        $query5['capacities'] = $this->getcapacity();

        $query6['appliances'] = $this->getappliances();
        
        return array_merge($query1, $query2, $query3, $query4, $query5, $query6);
        
    }

    function getcategory(){
      $this->db->distinct();
      $this->db->select('category');
      $query = $this->db->get('service_centre_charges');

      return  $query->result_array();
    }

    function getcapacity(){
        $this->db->distinct();
        $this->db->select('capacity');
        $sql = $this->db->get('service_centre_charges');
        
        return $sql->result_array();
    }

    function getappliances(){
      $this->db->distinct();
      $this->db->select('service_category');
      $sql = $this->db->get('service_centre_charges');
        
      return $sql->result_array();
    }
    
    function get_pricing_details($data){
        $this->db->select('service_centre_charges.*,services.services');
        
        if($data['source'] !=""){
          $this->db->where('partner_code', $data['source']);
        }

        if($data['city'] != ""){
          $this->db->where('city', $data['city']);
        }

        if($data['service_id'] != ""){
          $this->db->where('service_id', $data['service_id']);
        }

        if($data['category'] != ""){
          $this->db->where('category', $data['category']);
        }

        if($data['capacity'] != ""){
          $this->db->where('capacity', $data['capacity']);
        }

        if($data['appliances'] != ""){
          $this->db->where('service_category', $data['appliances']);
        }

        $this->db->join('services', 'services.id = service_centre_charges.service_id');

        $sql = $this->db->get('service_centre_charges');
        
        return $sql->result_array();
    }

    function editPriceTable($data){
      $this->db->where('id', $data['id']);
      $this->db->update('service_centre_charges', $data); 

    }
    
    /*
     * @Desc: This function is used to get service center charges table values
     * @params: void
     * @return: Array
     * 
     */
    function get_service_centre_charges($state){
        $this->db->select('service_centre_charges.*, tax_rates.rate , services.services as product');
        $this->db->join('tax_rates','tax_rates.tax_code = service_centre_charges.tax_code'
                . ' AND tax_rates.product_type = service_centre_charges.product_type '
                . 'AND tax_rates.state = "'.$state.'"');
        $this->db->join('services','services.id = service_centre_charges.service_id');
        $query = $this->db->get('service_centre_charges');
        return  $query->result_array();
    }
    
    /**
     * @Desc:This function is used to get unique states from Tax Rates for SC Charges List
     * @params: void
     * @return: void
     * 
     */
    function get_unique_states_from_tax_rates(){
        $sql = 'SELECT DISTINCT state FROM tax_rates Order By State Asc';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    
}