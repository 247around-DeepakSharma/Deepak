<?php

define('basic_percentage', 0.7);
define('addtitional_percentage', .85);
define('parts_percentage', .95);


class New_booking_model extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
    parent::__Construct();

    $this->db_location = $this->load->database('default1', TRUE, TRUE);
    $this->db = $this->load->database('default', TRUE, TRUE);
    }

    /** @description:* add booking
     *  @param : booking
     *  @return : array (booking)
     */

    function addbooking($booking){
        log_message ('info', __METHOD__ . "booking_unit_details data". print_r($booking));
        $this->db->insert('booking_details', $booking);
        return $this->db->insert_id();
    }
    
    /**
     * @desc: this method is used to get cit services, sources and user details
     * @param : user phone no.
     * @return : array()
     */
    function get_city_booking_source_services($phone_number){
    
        $query1['services'] = $this->booking_model->selectservice();
        
        $query2['city'] = $this->vendor_model->getDistrict();
        $query3['sources'] = $this->partner_model->get_all_partner_source("0");
        $query4['user'] = $this->user_model->search_user($phone_number);

        return $query = array_merge($query1, $query2, $query3, $query4);

    }
    
    /**
     * @desc: this is used to copy price and tax rate of custom service center id and insert into booking unit details table with 
     * booking id and details.
     * @param: Array()
     * @return : Array() 
     */
    function insert_data_in_booking_unit_details($services_details){
        $data = $this->getpricesdetails_with_tax($services_details['id']);
       
        $result = array_merge($services_details, $data[0]);

        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table 
        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges']; 
        log_message ('info', __METHOD__ . "booking_unit_details data". print_r($result));
        $this->db->insert('booking_unit_details', $result);

        return $result;
    }
    
    /**
     * @desc: this is used to add appliances
     * @param: Array(Appliances details)
     * @return: appliance id
     */
    function addappliance($appliance_detail){
       
        $this->db->insert('appliance_details', $appliance_detail);
        return $this->db->insert_id();
    }
    
    /**
     * @desc: get booking history
     * @param: booking id, flag to make join with service center table
     * @return : array
     */
    function getbooking_history($booking_id, $join=""){

        $service_centre = "";
        $condition ="";
        $service_center_name ="";
        if($join !=""){
            $service_center_name =",service_centres.name as vendor_name, service_centres.district ";
            $service_centre = ", service_centres ";
            $condition = " and booking_details.assigned_vendor_id =  service_centres.id";
        }

        $sql = " SELECT `services`.`services`, users.*, booking_details.* ".  $service_center_name
               . "from booking_details, users, services " . $service_centre
               . "where booking_details.booking_id='$booking_id' and "
               . "booking_details.user_id = users.user_id and "
               . "services.id = booking_details.service_id  ". $condition;

        $query = $this->db->query($sql);

        return $query->result_array();
    }
    
    /**
     * @desc: get all booking details for given booking id
     * @param: booking id 
     * @return:  Array
     */
    function getunit_details($booking_id){

        $sql = "SELECT distinct(appliance_id), brand, category, capacity, `appliance_details`.`model_number`,description, `appliance_details`.`purchase_month`, `appliance_details`.`purchase_year`, appliance_tag
            from booking_unit_details,  appliance_details Where `booking_unit_details`.booking_id = '$booking_id' AND `appliance_details`.`id` = `booking_unit_details`.`appliance_id`  ";

        $query = $this->db->query($sql);
        $appliance =  $query->result_array();

        foreach ($appliance as $key => $value) {
            // get data from booking unit details table on the basis of appliance id
            $this->db->select('id as unit_id, price_tags, customer_total, around_net_payable, partner_net_payable, customer_net_payable, customer_paid_basic_charges, customer_paid_extra_charges, customer_paid_parts, booking_status, partner_paid_basic_charges');
            $this->db->where('appliance_id', $value['appliance_id']);
            $query2 = $this->db->get('booking_unit_details');

            $result = $query2->result_array();
            $appliance[$key]['qunatity'] = $result; // add booking unit details array into quantity key of previous array
        }

        return $appliance;
    }
    
    /**
     * @desc: update price in booking unit details
     */
    function update_unit_details($data){
        // get booking unit data on the basis of id
        $this->db->select('around_net_payable, partner_net_payable, tax_rate, price_tags, partner_paid_basic_charges, around_paid_basic_charges');
        $this->db->where('id', $data['id']);
        $query = $this->db->get('booking_unit_details');
        $unit_details = $query->result_array();

        if($data['booking_status'] == "Completed"){

            $this->update_price_in_unit_details($data, $unit_details);

        } else {

            $data['customer_total'] = 0;
            $unit_details[0]['partner_net_payable'] = 0;
            $unit_details[0]['around_net_payable'] =0;
            $unit_details[0]['tax_rate'] = 0;
            $data['customer_net_payable'] = 0;
            $data['partner_paid_basic_charges'] = 0;
            $data['around_paid_basic_charges'] = 0;
            

            // Update price in unit table
            $this->update_price_in_unit_details($data, $unit_details);
        }

    }
    
    /**
     * @desc: calculate service charges and vat charges
     * @param : total charges and tax rate
     * @return calculate charges
     */
    function get_calculated_tax_charge($total_charges, $tax_rate){
          //52.50 = (402.50 / ((100 + 15)/100)) * ((15)/100)
          //52.50 =  (402.50 / 1.15) * (0.15)
        $st_vat_charge = sprintf ("%.2f", ($total_charges / ((100 + $tax_rate)/100)) * (($tax_rate)/100));
        return $st_vat_charge;
    }
    
    /**
     * @desc: get tax rate for specific booking 
     
    function gettax_rate_details($booking_unit_id, $service_category){
        $this->db->select('booking_unit_details.around_net_payable, service_centre_charges.partner_payable_basic as partner_paid_basic_charges, service_centre_charges.product_or_services, service_centre_charges.customer_total as customer_total, service_centre_charges.service_category as price_tags, service_centre_charges.partner_net_payable, tax_rates.rate as tax_rate');
        $this->db->from('booking_unit_details');
        $this->db->where('booking_unit_details.id', $booking_unit_id);
        $this->db->join('booking_details','booking_details.booking_id =  booking_unit_details.booking_id');
        $this->db->join('bookings_sources', 'bookings_sources.code = booking_details.source');
        $this->db->join('service_centre_charges','service_centre_charges.partner_id = bookings_sources.price_mapping_id AND service_centre_charges.state = booking_details.state AND service_centre_charges.service_id = booking_unit_details.service_id AND service_centre_charges.category =  booking_unit_details.appliance_category AND service_centre_charges.capacity = booking_unit_details.appliance_capacity ');
        $this->db->join('tax_rates','tax_rates.tax_code = service_centre_charges.tax_code AND tax_rates.state = booking_details.state AND tax_rates.product_type =  service_centre_charges.product_type');
        if($service_category !=""){
            $this->db->where('service_centre_charges.service_category', $service_category);
        }
        
        $this->db->where('service_centre_charges.active','1');
        $this->db->where('tax_rates.active','1');
       
        $query = $this->db->get();
        
        $unit_details = $query->result_array();

        return $unit_details ;

    }*/
    
    // Update Price in unit details
    function update_price_in_unit_details($data, $unit_details){

        $data['around_paid_basic_charges'] = $unit_details[0]['around_paid_basic_charges'];
        $data['partner_paid_basic_charges'] = $unit_details[0]['partner_paid_basic_charges'];
        $data['tax_rate'] = $unit_details[0]['tax_rate'];
      
        
        $vendor_total_basic_charges =  ($data['customer_paid_basic_charges'] + $data['partner_paid_basic_charges'] + $data['around_paid_basic_charges']) * basic_percentage;
        $around_total_basic_charges = ($data['customer_paid_basic_charges'] + $data['partner_paid_basic_charges'] + $data['around_paid_basic_charges'] - $vendor_total_basic_charges);

        $data['around_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($around_total_basic_charges, $data['tax_rate'] );
        $data['vendor_st_or_vat_basic_charges'] = $this->get_calculated_tax_charge($vendor_total_basic_charges, $data['tax_rate'] ); 

        $data['around_comm_basic_charges'] = $around_total_basic_charges - $data['around_st_or_vat_basic_charges'];
        $data['vendor_basic_charges'] = $vendor_total_basic_charges - $data['vendor_st_or_vat_basic_charges'];

        $total_vendor_addition_charge = $data['customer_paid_extra_charges'] * addtitional_percentage;
        $total_around_additional_charge = $data['customer_paid_extra_charges'] - $total_vendor_addition_charge;

        $data['around_st_extra_charges'] = $this->get_calculated_tax_charge($total_around_additional_charge, $data['tax_rate']);
        $data['vendor_st_extra_charges'] = $this->get_calculated_tax_charge($total_vendor_addition_charge, $data['tax_rate']  );

        $data['around_comm_extra_charges'] = $total_around_additional_charge - $data['around_st_extra_charges'];
        $data['vendor_extra_charges'] = $total_vendor_addition_charge - $data['vendor_st_extra_charges'] ;

        $total_vendor_parts_charge = $data['customer_paid_parts'] * parts_percentage;
        $total_around_parts_charge =  $data['customer_paid_parts'] - $total_vendor_parts_charge;
        $data['around_st_parts'] = $this->get_calculated_tax_charge($total_around_parts_charge, $data['tax_rate'] );
        $data['vendor_st_parts'] =  $this->get_calculated_tax_charge($total_vendor_parts_charge,  $data['tax_rate']);
        $data['around_comm_parts'] =  $total_around_parts_charge - $data['around_st_parts'];
        $data['vendor_parts'] = $total_vendor_parts_charge - $data['vendor_st_parts'] ;

        $vendor_around_charge = ($data['customer_paid_basic_charges'] + $data['customer_paid_parts'] + $data['customer_paid_extra_charges']) - ($vendor_total_basic_charges + $total_vendor_addition_charge + $total_vendor_parts_charge );

        if($vendor_around_charge > 0){

            $data['vendor_to_around'] = $vendor_around_charge;
            $data['around_to_vendor'] = 0;

        } else {
            $data['vendor_to_around'] = 0;
            $data['around_to_vendor'] = abs($vendor_around_charge);
        }
        unset($data['internal_status']);
        $this->db->where('id', $data['id']);
        $this->db->update('booking_unit_details',$data);
    }
    
    /**
     * @desc: copy data of specific id, insert appliance details in new row and update price in it  
     * @param: unit id, Array
     * @return: void
    function copy_and_insert_in_booking_unit_details($unit_id, $unit_details){
        $this->db->select('booking_id,partner_id, service_id, appliance_id, appliance_brand, appliance_category, appliance_capacity,    appliance_size, model_number, appliance_tag, purchase_year, purchase_month');
        $this->db->where('id', $unit_id);
        $query = $this->db->get('booking_unit_details');
        $result = $query->result_array();
        $result[0]['around_net_payable'] = 0;
        $result[0]['customer_net_payable'] = 0;

         $this->db->insert('booking_unit_details', $result[0]); 

         $data['id'] = $this->db->insert_id();
         $data['customer_paid_basic_charges'] = "0";
         $data['customer_paid_extra_charges'] = "0";
         $data['customer_paid_parts'] = "0";
         $data['product_or_services'] = $unit_details[0]['product_or_services'];
         $data['customer_total'] = $unit_details[0]['customer_total'];
         $data['around_paid_basic_charges'] = 0;
         $data['partner_paid_basic_charges'] = $unit_details[0]['partner_paid_basic_charges'];
         $data['price_tags'] = $unit_details[0]['price_tags'];

        $this->update_price_in_unit_details($data, $unit_details);

    }

    */
    
    /**
     * @desc: this method is used  to return appliance id. It checks service id, user id, brand, category and capacity exist or not in the appliance_details table. If exist, it updates data otherwise it insert data in appliances_details table.
     * @param: Array, user id
     * @return : appliance id
     */
    function check_appliancesforuser($services_details){
        $this->db->select('id');
        $this->db->where('service_id', $services_details['service_id']);
        $this->db->where('brand', $services_details['brand']);
        $this->db->where('user_id', $services_details['user_id']);
        $this->db->where('category', $services_details['category']);
        $this->db->where('capacity', $services_details['capacity']);
        $query = $this->db->get('appliance_details');
        if($query->num_rows>0){

            $result = $query->result_array();
            $appliance_id = $result[0]['id'];

            $this->db->where('id', $appliance_id);
            $this->db->update('appliance_details', $services_details); 

            return $appliance_id;

        } else {

           $result =  $this->addappliance($services_details);
           return $result;
        }
    }
    
    /**
     * @desc: this method get prices details and check price tag is exist in unit details or not. if price tags does not exist, it inserts data in booking unit details and if price tags exist, it update booking unit details.
     * @param: Array
     * @return: Price tags.
     */
    function update_booking($services_details){

        $data = $this->getpricesdetails_with_tax($services_details['id']);

        $result = array_merge($services_details, $data[0]);

        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table 
         $result['customer_net_payable'] = $result['customer_total'] - $result['partner_paid_basic_charges'] - $result['around_paid_basic_charges']; 
               //log_message ('info', __METHOD__ . "update booking_unit_details data". print_r($result));

        $this->db->select('id');
        $this->db->where('appliance_id', $services_details['appliance_id']);
        $this->db->where('price_tags', $data[0]['price_tags']);
        $this->db->where('booking_id', $services_details['booking_id']);
        $query = $this->db->get('booking_unit_details');

        if($query->num_rows >0){

            $unit_details = $query->result_array();
            $this->db->where('id',  $unit_details[0]['id']);
            $this->db->update('booking_unit_details', $result); 

         } else {
             
            $this->db->insert('booking_unit_details', $result);
         }

         return $data[0]['price_tags'];
    }

    function getpricesdetails_with_tax($service_centre_charges_id){

        $sql =" SELECT service_category as price_tags, customer_total, partner_net_payable, rate as tax_rate, product_or_services from service_centre_charges, tax_rates where `service_centre_charges`.id = '$service_centre_charges_id' AND `tax_rates`.tax_code = `service_centre_charges`.tax_code AND `tax_rates`.state = `service_centre_charges`.state AND `tax_rates`.product_type = `service_centre_charges`.product_type AND (to_date is NULL or to_date >= CURDATE() ) AND `tax_rates`.active = 1 ";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function check_price_tags_status($booking_id, $price_tags){

        $this->db->select('id, price_tags');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get('booking_unit_details');
        if($query->num_rows>0){
            $result = $query->result_array();
            foreach ($result as $key => $value) {
                 if (in_array($value['price_tags'], $price_tags)) {
                   // echo "Match found";
                 }
                 else {
                    //echo "Match not found";
                   $data = array('booking_status' => "Not Completed" );
                   $this->db->where('id', $value['id']);
                   $this->db->update('booking_unit_details', $data); 

                }
            }
        }
        return;
    }

    function update_booking_details($booking){
        $this->db->where('booking_id', $booking['booking_id']);
        $this->db->update('booking_details', $booking);
    }

// end model
}


