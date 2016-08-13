<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Partner extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->model('user_model');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('notify');


    }

    /**
     * @desc: This is used to load Partner  Login Page
     *
     * @param: void
     * @return: void
     */
    function index() {
       $this->load->view('partner/partner_login');

    }

     /**
     * @desc: This is used to login
     *
     * If user name and password matches allowed to login and redirect pending booking, else error message appears.
     *
     * @param: void
     * @return: void
     */
    function partner_login() {

        $data['user_name'] = $this->input->post('user_name');
        $data['password'] = md5($this->input->post('password'));
        $partner_id = $this->partner_model->partner_login($data);

        if ($partner_id) {
        //get partner details now
        $partner_details = $this->partner_model->getpartner($partner_id);

        $this->setSession($partner_details[0]['id'], $partner_details[0]['name']);
        log_message('info', 'Partner loggedIn  partner id' . $partner_details[0]['id'] . " Partner name" . $partner_details[0]['name']);

        redirect(base_url() . "partner/pending_booking");
        } else {

            $userSession = array('error' => 'Please enter correct user name and password' );
            $this->session->set_userdata($userSession);
            redirect(base_url() . "partner/login");
        }
    }

     /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void
     */
    function pending_booking($offset = 0) {
       $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $config['base_url'] = base_url() . 'partner/pending_booking';
        $config['total_rows'] = $this->partner_model->getPending_booking("count","",$partner_id);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getPending_booking($config['per_page'], $offset, $partner_id);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');
         log_message('info', 'Partner view Pending booking  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name')." data ". print_r($data, true));
        $this->load->view('partner/header');
        $this->load->view('partner/pending_booking', $data);
    }

    /**
     * @desc: this is used to load pending booking
     * @param: Offset and page no.
     * @return: void
     */
    function pending_queries($offset = 0){
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');
        $config['base_url'] = base_url() . 'partner/pending_queries';
        $config['total_rows'] = $this->partner_model->getPending_queries("count","",$partner_id);

        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getPending_queries($config['per_page'], $offset, $partner_id);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');
         log_message('info', 'Partner view Pending query  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name')." data ". print_r($data, true));
        $this->load->view('partner/header');
        $this->load->view('partner/pending_queries', $data);

    }


    /**
     * @desc: this is used to display completed booking for specific service center
     */
    function closed_booking($state, $offset = 0){
        $this->checkUserSession();
        $partner_id = $this->session->userdata('partner_id');

        $config['base_url'] = base_url() . 'partner/closed_booking/'.$state;
        $config['total_rows'] = $this->partner_model->getclosed_booking("count","",$partner_id, $state);

        $config['per_page'] = 50;
        $config['uri_segment'] = 4;
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['count'] = $config['total_rows'];
        $data['bookings'] = $this->partner_model->getclosed_booking($config['per_page'], $offset, $partner_id, $state);

        if ($this->session->flashdata('result') != '')
            $data['success'] = $this->session->flashdata('result');

        $data['status'] = $state;

        log_message('info', 'Partner view '.$state.' booking  partner id' . $partner_id . " Partner name" . $this->session->userdata('partner_name'). " data ". print_r($data, true));

        $this->load->view('partner/header');
        $this->load->view('partner/closed_booking', $data);

    }

        /**
     * @desc: this is used to get booking details by using booking ID And load booking booking details view
     * @param: booking id
     * @return: void
     */
    function booking_details($booking_id) {
        $this->checkUserSession();
        $data['booking_history'] = $this->booking_model->getbooking_history($booking_id);
        $data['unit_details'] = $this->booking_model->get_unit_details($booking_id);


        log_message('info', 'Partner view booking details booking  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'). " data ". print_r($data, true));

        $this->load->view('partner/header');
        $this->load->view('partner/booking_details', $data);
    }


    /**
     * @desc: This function Sets Session
     * @param: Partrner id
     * @param: Partner name
     * @return: void
     */
    function setSession($partner_id, $partner_name) {
    $userSession = array(
        'session_id' => md5(uniqid(mt_rand(), true)),
        'partner_id' => $partner_id,
        'partner_name' => $partner_name,
        'sess_expiration' => 30000,
        'loggedIn' => TRUE,
        'userType' => 'partner'
    );

        $this->session->set_userdata($userSession);
    }

    /**
     * @desc: This funtion will check Session
     * @param: void
     * @return: true if details matches else session is distroyed.
     */
    function checkUserSession() {
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'partner')) {
            return TRUE;
        } else {
            $this->session->sess_destroy();
            redirect(base_url() . "partner/login");
        }
    }

    /**
     * @desc : This funtion for logout
     * @param: void
     * @return: void
     */
    function logout() {
        log_message('info', 'Partner logout  partner id' . $this->session->userdata('partner_id') . " Partner name" . $this->session->userdata('partner_name'));

        $this->session->sess_destroy();
        redirect(base_url() . "partner/login");
    }
    /**
     * @desc: This method loads abb booking form
     * it gets user details(if exist), city, source, services
     */
    function get_addbooking_form($phone_number){
        $data = $this->booking_model->get_city_booking_source_services($phone_number);
        $this->load->view('partner/header');
        $this->load->view('partner/addbookingmodel');
        $this->load->view('partner/get_addbooking', $data);
    }
    
    /**
     * @desc: This method called by ajax to load category in the add booking form
     * @param: service id, city, partner id 
     */
    function get_category(){
        $service_id = $this->input->post('service_id');
        $city = $this->input->post('city');
        $partner_id = $this->input->post('partner_id');

        $partner_mapping_id = $this->booking_model->get_price_mapping_partner_code("",$partner_id);
        //print_r($partner_mapping_id);
      
        $state = "";
        $result = $this->booking_model->getCategoryForService($service_id, $state, $partner_mapping_id);

        echo "<option selected disabled>Select Appliance Category</option>";
        foreach ($result as $category) {
            echo "<option  >$category[category]</option>";
        }  
    }
    /**
     * @desc: This method called by ajax to load capacity in the add booking form
     * @param: service id, city, partner id, category
     */
    function get_capacity() {
        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        //$city = $this->input->post('city');
        $partner_id = $this->input->post('partner_id');
        $state = "";

        $partner_mapping_id = $this->booking_model->get_price_mapping_partner_code("", $partner_id);

        $result = $this->booking_model->getCapacityForCategory($service_id, $category, $state, $partner_mapping_id);

        foreach ($result as $capacity) {
            echo "<option>$capacity[capacity]</option>";
        }
    }

    /**
     * @desc: This method called by ajax to load price table in the add booking form
     * @param: service id, partner id, category, capacity, brand, div clone number
     */
     public function get_price_table() {

    $service_id = $this->input->post('service_id');
    $category = $this->input->post('category');
    $capacity = $this->input->post('capacity');
    $brand = $this->input->post('brand');
    $partner_id = $this->input->post('partner_id');
    //$city = $this->input->post('city');
    $clone_number = $this->input->post('clone_number');
    $state = "";

    $partner_mapping_id = $this->booking_model->get_price_mapping_partner_code("", $partner_id);
   
    $result = $this->booking_model->getPricesForCategoryCapacity($service_id, $category, $capacity, $partner_mapping_id, $state);
    if (!empty($result)) {

        echo "<tr><th>Service Category</th><th>Std. Charges</th><th>Partner Discount</th><th>Final Charges</th><th>247around Discount</th><th>Selected Services</th></tr>";
        $html = "";

        $i = 0;

        foreach ($result as $prices) {
        $service_category = $prices['service_category'];

        $html .="<tr><td>" . $prices['service_category'] . "</td>";
        $html .= "<td>" . $prices['customer_total'] . "</td>";
        $html .= "<td><input  type='text' class='form-control partner_discount' name= 'partner_paid_basic_charges[$brand][" . $prices['id'] . "][]'  id='partner_paid_basic_charges_" . $i . "_" . $clone_number . "' value = '" . $prices['partner_net_payable'] . "' placeholder='Enter discount' readonly/></td>";
        $html .= "<td>" . $prices['customer_net_payable'] . "</td>";
        $html .= "<td><input  type='text' class='form-control discount' name= 'discount[$brand][" . $prices['id'] . "][]'  id='discount_" . $i . "_" . $clone_number . "' value = '0' placeholder='Enter discount' readonly></td>";
        $html .= "<td><input class='price_checkbox'";
        if ($prices['service_category'] == 'Repair') {
            $html .= "checked";
        }

        $html .=" type='checkbox' id='checkbox_" . $i . "_" . $clone_number . "'";
        $html .= "name='prices[$brand][]'";
        $html .= " " .
            "value=" . $prices['id'] . "_" . intval($prices['customer_net_payable']) . " ></td><tr>";

        $i++;
        // onclick='final_price(), enable_discount(this.id)'
        }
        echo $html;
    } else {
        echo "Price Table Not Found";
    }
    }

    /**
     * @desc: This method insert booking details into table and send SMS and email if booking type is Booking
     */
    function process_addbooking(){
        $booking = $this->getAllBookingInput();
        $this->booking_model->addbooking($booking);

        if ($booking['type'] == 'Booking') {
            $to = "anuj@247around.com, nits@247around.com";
            //$to = "abhaya@247around.com, anuj@247Around";
            $from = "booking@247around.com";
            $cc = "";
            $bcc = "";
            $subject = 'Booking Confirmation-AROUND';
            $this->notify->sendEmail($from, $to, $cc, $bcc, $subject, $message, "");
           //-------Sending SMS on booking--------//

           $smsBody = "Got it! Request for " . trim($service) . " Repair is confirmed for " .
           $booking['booking_date'] . ", " . $booking['booking_timeslot'] .
        ". 247Around Indias 1st Multibrand Appliance repair App goo.gl/m0iAcS. 011-39595200";

           $this->notify->sendTransactionalSms($booking['booking_primary_contact_no'], $smsBody);
        }
        redirect(base_url() . "partner/pending_booking");
    }
    
     /**
     * @desc: This method get all input from add booking and insert into appliance details, booking unit details and users table
     */
    function getAllBookingInput(){
        $user_id = $this->input->post('user_id');
        $user['city'] = $booking['city'] = $this->input->post('city');
        $user['home_address'] =  $booking['booking_address'] = $this->input->post('home_address');
        $user['pincode'] = $booking['booking_pincode'] = $this->input->post('booking_pincode');
        $user['phone_number'] = $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
        $user['alternate_phone_number'] = $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
        $booking['partner_id'] = $this->input->post('partner_id');
        $booking['source'] = $this->partner_model->get_source_code_for_partner($booking['partner_id']);
        $booking['service_id'] = $this->input->post('service_id');
        $booking['amount_due'] = $this->input->post('grand_total_price');
        $booking_date = $this->input->post('booking_date');
        $booking['booking_date'] = date('d-m-Y', strtotime($booking_date));
        $booking['booking_landmark'] =  $this->input->post('landmark');
        $booking['type'] = 'Booking';

        // select state by city
        $state = $this->vendor_model->selectSate($booking['city']);
        $booking['state'] =  $state[0]['state'];

        if(empty($user_id)){
            $user['name'] = $this->input->post('user_name');
            $user['user_email'] =  $this->input->post('user_email');
            $user['state'] =  $state[0]['state'];

            $user_id = $this->user_model->add_user($user);
          
        }
        $booking['user_id'] = $user_id;

        $booking['booking_id'] = $this->create_booking_id($user_id, $booking['source'], $booking['type'], $booking['booking_date']);
        $service_name = $this->input->post('service_name');
        $booking['order_id'] = $this->input->post('order_id');
        $booking['booking_timeslot'] = $this->input->post('booking_timeslot');
        $booking['partner_source'] =  $this->input->post('partner_source');
        $booking['booking_remarks'] =  $this->input->post('booking_remarks');

        // All brand comming in array eg-- array([0]=> LG, [1]=> BPL)
        $appliance_brand = $this->input->post('appliance_brand');
        // All category comming in array eg-- array([0]=> TV-LCD, [1]=> TV-LED)
        $appliance_category = $this->input->post('appliance_category');
        // All capacity comming in array eg-- array([0]=> 19-30, [1]=> 31-42)
        $appliance_capacity = $this->input->post('appliance_capacity');
        // All model number comming in array eg-- array([0]=> ABC123, [1]=> CDE1478)
        $model_number = $this->input->post('model_number');
        // All purchase year comming in array eg-- array([0]=> 2016, [1]=> 2002)
        $purchase_year = $this->input->post('purchase_year');
        // All purchase month comming in array eg-- array([0]=> Jan, [1]=> Feb)
        $months = $this->input->post('purchase_month');
        $booking['quantity'] = count($appliance_brand);

        $partner_net_payable = $this->input->post('partner_paid_basic_charges');
        // All discount comming in array.  Array ( [BPL] => Array ( [100] => Array ( [0] => 200 ) [102] => Array ( [0] => 100 ) [103] => Array ( [0] => 0 ) ) .. Key is Appliance brand, unit id and discount value.
        $discount = $this->input->post('discount');
        // All prices comming in array with pricing table id
        /* Array(
        [BPL] => Array( [0] => 100_300 [1] => 102_250)

        [Micromax] => Array([0] => 100_300)
         ) */
        //Array ( ['brand'] => Array ( [0] => id_price ) )
        $pricesWithId = $this->input->post("prices");

        foreach ($appliance_brand as $key => $value) {
            $services_details = "";
            $appliances_details = "";
            $appliances_details['user_id'] = $user_id;
            $appliances_details['brand'] = $services_details['appliance_brand'] = $value; // brand
            // get category from appiance category array for only specific key.
            $appliances_details['category'] = $services_details['appliance_category'] = $appliance_category[$key];
            // get appliance_capacity from appliance_capacity array for only specific key.
            $appliances_details['capacity'] = $services_details['appliance_capacity'] = $appliance_capacity[$key];
            // get model_number from appliance_capacity array for only specific key such as $model_number[0].
            $appliances_details['model_number'] = $services_details['model_number'] = $model_number[$key];
            // get purchase year from purchase year array for only specific key such as $purchase_year[0].
            $appliances_details['purchase_year'] = $services_details['purchase_year'] = $purchase_year[$key];
            $services_details['booking_id'] = $booking['booking_id'];
            // get purchase months from months array for only specific key such as $months[0].
            $appliances_details['purchase_month'] = $services_details['purchase_month'] = $months[$key];
            $appliances_details['service_id'] = $services_details['service_id'] = $booking['service_id'];
            $appliances_details['last_service_date'] = date('Y-m-d H:i:s');
            $services_details['partner_id'] = $booking['partner_id'];

            $services_details['appliance_id'] = $this->booking_model->addappliance($appliances_details);
            // log_message ('info', __METHOD__ . "Appliance details data". print_r($appliances_details));
            //Array ( ['brand'] => Array ( [0] => id_price ) )
            foreach ($pricesWithId[$value] as $keys => $values) {

                $prices = explode("_", $values);  // split string..
                $services_details['id'] = $prices[0]; // This is id of service_centre_charges table.
                // discount for appliances. Array ( [BPL] => Array ( [100] => Array ( [0] => 200 ) [102] => Array ( [0] => 100 ) [103] => Array ( [0] => 0 ) )

                $services_details['around_paid_basic_charges'] = $discount[$value][$services_details['id']][0];
                $services_details['partner_paid_basic_charges'] = $partner_net_payable[$value][$services_details['id']][0];

                $result = $this->booking_model->insert_data_in_booking_unit_details($services_details, $booking['state']);
                //Log this state change as well for this booking
                //param:-- booking id, new state, old state, employee id, employee name
                //$this->notify->insert_state_change($booking['booking_id'], "Inserted", "New", $this->session->userdata('partner_id'), $this->session->userdata('partner_name'));
            }

        }

        return $booking;
    }

     /**
     * @desc: this method generates booking id. booking id is the combination of booking source, 4 digit random number, date and month
     * @param: user id, booking source, booking type
     * @return: booking id
     */
    function create_booking_id($user_id, $source, $type, $booking_date) {
    $booking['booking_id'] = '';

    $yy = date("y", strtotime($booking_date));
    $mm = date("m", strtotime($booking_date));
    $dd = date("d", strtotime($booking_date));

    $booking['booking_id'] = str_pad($user_id, 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
    $booking['booking_id'] .= (intval($this->booking_model->getBookingCountByUser($user_id)) + 1);


    //Add source
    $booking['source'] = $source;
    if ($type == "Booking") {
        $booking['booking_id'] = $booking['source'] . "-" . $booking['booking_id'];
    } else {
        $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
    }

    return $booking['booking_id'];
    }


}