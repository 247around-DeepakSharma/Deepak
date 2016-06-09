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
        $this->db->select('service_category as price_tags, customer_total, partner_net_payable, rate as tax_rate, product_or_services, partner_payable_basic as partner_paid_basic_charges');
        $this->db->from('service_centre_charges');
        $this->db->where('service_centre_charges.id', $services_details['id']); // service center charges table (id)
        $this->db->join('tax_rates','tax_rates.tax_code = service_centre_charges.tax_code AND tax_rates.state = service_centre_charges.state AND tax_rates.product_type = service_centre_charges.product_type');
        $this->db->where('tax_rates.active','1');

        $query = $this->db->get();
        $data = $query->result_array();
        $result = array_merge($services_details, $data[0]);

        unset($result['id']);  // unset service center charge  id  because there is no need to insert id in the booking unit details table 
        $result['customer_net_payable'] = $result['customer_total'] - $result['partner_net_payable'] - $result['around_net_payable']; 
        log_message ('info', __METHOD__ . "booking_unit_details data". print_r($result));
        $this->db->insert('booking_unit_details', $result);

        return $result;
    }
    
    /**
     * @desc: this is used to add appliances
     * @param: Array,  User Id
     * @return: appliance id
     */
    function addappliance($services, $user_id){
        $appliance_detail = array("user_id" => $user_id,
        "service_id" => $services['service_id'],
        "brand" => $services['appliance_brand'],
        "category" => $services['appliance_category'],
        "capacity" => $services['appliance_capacity'],
        "model_number" => $services['model_number'],
        "purchase_year" => $services['purchase_year'],
        "purchase_month" => $services['purchase_month'],
        "tag" => $services['appliance_tag'],
        "last_service_date" => date('Y-m-d H:i:s'));
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
            $this->db->select('id as unit_id, price_tags, customer_total, around_net_payable, partner_net_payable, customer_net_payable');
            $this->db->where('appliance_id', $value['appliance_id']);
            $query2 = $this->db->get('booking_unit_details');

            $result = $query2->result_array();
            $appliance[$key]['qunatity'] = $result; // add booking unit details array into quantity key of previous array
        }

        return $appliance;
    }
    
    /**
     * @desc: update booking in booking unit details
     */
    function update_unit_details($data){
        // get booking unit data on the basis of id
        $this->db->select('around_net_payable, partner_net_payable, tax_rate, price_tags');
        $this->db->where('id', $data['id']);
        $query = $this->db->get('booking_unit_details');
        $unit_details = $query->result_array();
       // check price tage is Wall Mount Stand
       if($unit_details[0]['price_tags'] == "Wall Mount Stand"){
            // Check coming internal status is 'Completed TV Without Stand' or 'Completed With Demo'
            // In this case all price in unit table is zero
            if($data['internal_status'] == "Completed TV Without Stand" || $data['internal_status'] = "Completed With Demo"){

                $data['customer_total'] = 0;
                $unit_details[0]['partner_net_payable'] = 0;
                $unit_details[0]['around_net_payable'] =0;
                $unit_details[0]['tax_rate'] = 0;
                $data['customer_net_payable'] = 0;
                
            } 
            // Update price in unit table
            $this->update_price_in_unit_details($data, $unit_details);
        // Check price tag is Installation & Demo
        } else if($unit_details[0]['price_tags'] == "Installation & Demo"){
            // check Coming internal status is Completed TV With Stand
            if($data['internal_status'] == "Completed TV With Stand"){
                // update price for installation & Demo
                $this->update_price_in_unit_details($data, $unit_details);
                // Get tax rate details for  Stand
                $unit_details_for_insert = $this->gettax_rate_details($data['id'], "Wall Mount Stand");
                // Insert new row in unit details for stand
                $this->copy_and_insert_in_booking_unit_details($data['id'],$unit_details_for_insert );

            } else {
                $this->update_price_in_unit_details($data, $unit_details); 
            }

        } else {

            $this->update_price_in_unit_details($data, $unit_details);
        }
    }
    
    /**
     * @desc: calculate tax charge
     * @param : total charges and tax rate
     * @return calculate charges
     */
    function get_calculated_tax_charge($total_charges, $tax_rate){
        
        $st_vat_charge = sprintf ("%.2f", ($total_charges / ((100 + $tax_rate)/100)) * (($tax_rate)/100));
        return $st_vat_charge;
    }
    
    /**
     * @desc: get tax rate for specific booking 
     */
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

    }
    
    // Update Price in unit details
    function update_price_in_unit_details($data, $unit_details){

        $data['around_net_payable'] = $unit_details[0]['around_net_payable'];
        $data['partner_net_payable'] = $unit_details[0]['partner_net_payable'];
        $data['tax_rate'] = $unit_details[0]['tax_rate'];
       
        
        $vendor_total_basic_charges =  ($data['customer_paid_basic_charges'] + $data['partner_net_payable'] + $data['around_net_payable']) * basic_percentage;
        $around_total_basic_charges = ($data['customer_paid_basic_charges'] + $data['partner_net_payable'] + $data['around_net_payable'] - $vendor_total_basic_charges);

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
     */
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


// end model
}
