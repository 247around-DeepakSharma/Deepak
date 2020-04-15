<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

class Main extends CI_Controller {

    function __Construct() {
        parent::__Construct();

        $this->load->model('web_bookings');
        $this->load->model('web_user_model');
        $this->load->model('web_booking_model');
        $this->load->model('thoughts_model');
        $this->load->model('url_table_model');
        $this->load->model('web_footer_model');
        $this->load->model('ad_campaigns');

        $this->load->helper(array('html', 'url', 'form'));
        $this->load->library('session');
        $this->load->library('notify');
        $this->load->library('image_lib');
        $this->load->library('form_validation');
    }

    public function test($a = "") {
        echo "Looks like things are working" . PHP_EOL;
        echo "Argument = " . $a . PHP_EOL;
    }

    /**
     * Home Page
     */
    public function index() {
        //log_message('info', __FUNCTION__);

        $data['city'] = $this->web_booking_model->get_active_city();
        $services = $this->web_booking_model->selectservice();
        $data['services'] = json_decode(json_encode($services), True);
        $data['partner_logo'] = $this->web_booking_model->get_partner_logo();
        $data['counter'] = $this->web_booking_model->get_counter_data();
        $this->load->view('website/index', $data);
        $this->load->view('website/logo_slider');
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer', $data);
    }

    /**
     * City based on selected appliance
     */
    public function get_city_based_on_appliance() {
        log_message('info', __FUNCTION__);
        $appliance_name = $this->input->post('id');
        $appliance_id = $this->web_booking_model->selectidbyservice($appliance_name);

        $city = $this->web_booking_model->get_appliance_active_city($appliance_id[0]['id']);

        $option = '<option selected disabled>Select City</option>';

        foreach ($city as $value) {
            $option .= "<option value='" . $value['City'] . "'";
            $option .= " > ";
            $option .= $value['City'] . "</option>";
        }

        echo $option;
    }

    /**
     * Appliance Repair Pages
     */
    public function appliance_repair($appl) {
        log_message('info', __FUNCTION__);

        $appliance = str_replace("-", " ", $appl);
        $data['appliance_details'] = $this->thoughts_model->get_appliance_page_details($appliance);

        $all_blogs = $this->thoughts_model->get_thoughts($appliance);
        //Show max of 4 blogs
        $num = (count($all_blogs) >= 4 ? 4 : count($all_blogs));
        $rand_keys = array_rand($all_blogs, $num);

        for ($i = 0; $i < $num; $i++) {
            $blogs[] = $all_blogs[$rand_keys[$i]];
        }

        //Limit number of blogs to 2 in case of mobile phones
        $isMobile = (bool) preg_match('#\b(ip(hone|od)|android\b.+\bmobile|opera m(ob|in)i|windows (phone|ce)|blackberry' .
                        '|s(ymbian|eries60|amsung)|p(alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]' .
                        '|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT']);

        if ($isMobile && $num > 2) {
            $num = 2;
        }

        $data['num_blogs'] = $num;
        $data['blogs'] = $blogs;
        $data['appliance'] = $appliance;
        $data['city'] = $this->web_booking_model->get_active_city();
        $services = $this->web_booking_model->selectservice();
        $data['services'] = json_decode(json_encode($services), True);

        $this->load->view('website/appliance-repair-min', $data);
        $this->load->view('website/testimonials');

        $data2['city'] = $data['city'];
        $data2['brands'] = $this->web_footer_model->get_brands();
        $data2['appliance'] = $appliance;
        $this->load->view('website/footer-min', $data2);
    }

    /**
     * About Us
     */
    public function about_us() {
        log_message('info', __FUNCTION__);


        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $data2['employee'] = $this->web_booking_model->get_employee_details();

        $data2['users'] = $this->web_footer_model->get_users_count();
        $data1['cities'] = $this->web_footer_model->get_city_count();
        $data1['partners'] = $this->web_footer_model->get_partners_count();

        $city_count = $data1['cities'][0]['total_city'];
        $city_count_final = ceil(($city_count / 100)) * 100;
        $data2['city_count'] = $city_count_final;

        $partner_count = $data1['partners'][0]['sum_count'];
        $partner_count_final = ceil(($partner_count / 100)) * 100;
        $data2['partner_count'] = $partner_count_final;

        $this->load->view('website/about-us', $data2);



        $this->load->view('website/footer-min', $data);
    }

    /**
     * FAQs
     */
    public function faq() {
        log_message('info', __FUNCTION__);

        $this->load->view('website/faq');

        //$this->load->view('website/testimonials');
        //$this->load->view('website/how-it-works');
        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    /**
     * Terms
     */
    public function terms() {
        log_message('info', __FUNCTION__);

        $this->load->view('website/terms');

        //$this->load->view('website/testimonials');
        //$this->load->view('website/how-it-works');
        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    public function contact_us() {
        log_message('info', __FUNCTION__);

        $this->load->view('website/contact-us');
        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    public function our_team() {
        log_message('info', __FUNCTION__);

        $this->load->view('website/our-team');
        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    public function contact_query() {
        log_message('info', __FUNCTION__);

        $name   = $this->input->post('fullname');
        $email  = $this->input->post('email');
        $phone  = $this->input->post('phone');
        $reason = $this->input->post('reason');
        $msg    = $this->input->post('message');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('fullname', 'Full Name', 'trim|required|regex_match[/^[A-Z a-z]*[aeiouAEIOU][A-Z a-z]*$/]');
        $this->form_validation->set_rules('phone', 'Mobile Number', 'trim|required|regex_match[/^[789][0-9]{9}$/]');
        $this->form_validation->set_rules('email', 'email', 'trim|required|regex_match[/^[A-z0-9_\.-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z.]{2,4}$/]');
        $this->form_validation->set_rules('reason', 'Reason', 'trim|required');

        if ($this->form_validation->run() === TRUE) {
            $data['full_name'] = $name;
            $data['email'] = $email;
            $data['mobile_no'] = $phone;
            $data['reason'] = $reason;
            $data['msg'] = $msg;
            
            $insert_id = $this->web_booking_model->insert_contact_us_query($data);
            if($insert_id){
                log_message('info','Details added successfully '. print_r($data,true));
            }else{
                log_message('info','Error In Inserting Details '. print_r($data,true));
            }
            
            $name = $name;
            $subject = "Feedback/Query from Website";
            $message = "Dear Admin,<br><br>Feedback / Query received from Website with below details:<br><br>";
            $message .= ("Name: " . $name . "<br>");
            $message .= ("Email: " . $email . "<br>");
            $message .= ("Phone: " . $phone . "<br>");
            $message .= ("Reason: " . $reason . "<br>");
            $message .= ("Message: " . $msg . "<br><br>" . "Please follow up.<br><br>Regards,<br>247around Team");

            $this->notify->sendEmail($subject, $message, FALSE);

            $responseData = array('result' => 'ok',
                'title' => 'Thank You For Your Time !!!',
                'message' => '247around team will get back to you very soon.'
            );
        } else {
            $responseData = array('result' => 'error',
                'title' => 'Apologies for Inconvenience',
                'message' => 'Your request could not be submitted, please try again after sometime.');
        }

        $this->load->view('website/thank-you', $responseData);
        $this->load->view('website/testimonials');
        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    /**
     *
     * Privacy
     */
    public function privacy() {
        log_message('info', __FUNCTION__);

        $this->load->view('website/privacy');

        //$this->load->view('website/testimonials');
        //$this->load->view('website/how-it-works');
        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    public function blog() {
        log_message('info', __FUNCTION__);

        $data['city'] = $this->web_booking_model->get_active_city();
        $services = $this->web_booking_model->selectservice();
        $data['services'] = json_decode(json_encode($services), True);
        $this->load->view('website/blog-mini', $data);

        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    /**
     * Charges
     */
    public function charges() {
        log_message('info', __FUNCTION__);

        $data['charges'] = $this->web_bookings->get_charges();

        $this->load->view('website/charges', $data);
        //$this->load->view('website/testimonials');
        //$this->load->view('website/how-it-works');
        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    /**
     * Book service from main page. It insert Verified data
     */
    public function book_service($verified_data) {
        log_message('info', __FUNCTION__ . '=> ' . $_SERVER['HTTP_REFERER']);

        $this->get_client_ip();

        $name = $verified_data[0]['name'];
        $mobile = $verified_data[0]['mobile'];
        $service = $verified_data[0]['service'];
        $city = $verified_data[0]['city'];
        $address = $verified_data[0]['address'];
        $pincode = $verified_data[0]['pincode'];
        $booking_date = $verified_data[0]['booking_date'];
        $booking_remarks = $verified_data[0]['booking_remarks'];
        $brand = $verified_data[0]['brand'];
        $category = $verified_data[0]['category'];
        $capacity = $verified_data[0]['capacity'];

        log_message('info', __FUNCTION__ . ' => Booking data: ' . print_r($verified_data[0], TRUE));

        $fv = TRUE;
        //Make it FALSE if all params are NULL (bot-did-this-booking), issue needs to be debugged further
        if (($name == '') && ($mobile == '') && ($service == '') && ($city == '') && ($booking_remarks == '') && ($pincode == '') && ($address == '') && ($booking_date == '') && ($brand == '') && ($category == '')) {
            $fv = FALSE;
        }

        if ($fv === TRUE) {
            //Store it in main bookings table as a query
            //for this, first check whether user exists or not
            //if it does not exist, create user first
            $output = $this->web_user_model->search_user($mobile);
            if (empty($output)) {
                //echo 'User doesnt exist';
                $user['name'] = $name;
                $user['phone_number'] = $mobile;
                $user['pincode'] = $pincode;
                $user['home_address'] = $address;
                $user['city'] = $city;

                $state = $this->web_booking_model->getall_state($user['city']);
                $user['state'] = $state['0']['state'];

                $booking['user_id'] = $this->web_user_model->add_user($user);
                //echo print_r($user, true);
            } else {
                //echo 'User exists';
                $booking['user_id'] = $output[0]['user_id'];
            }
            log_message('info', 'User ID: ' . $booking['user_id']);

            //Add sample appliances for this user
            $count = $this->web_booking_model->getApplianceCountByUser($booking['user_id']);
            //Add sample appliances if user has < 5 appliances in wallet
            if ($count < 5) {
                $this->web_booking_model->addSampleAppliances($booking['user_id'], 5 - intval($count));
            }

            //Add this query now
            $appliance_details['user_id'] = $booking['user_id'];
            $unit_details['service_id'] = $appliance_details['service_id'] = $booking['service_id'] = $this->web_booking_model->getServiceId($service);
            log_message('info', 'Service ID: ' . $booking['service_id']);

            $yy = date("y");
            $mm = date("m");
            $dd = date("d");
            $booking['booking_id'] = str_pad($booking['user_id'], 4, "0", STR_PAD_LEFT) . $yy . $mm . $dd;
            $booking['booking_id'] .= (intval($this->web_booking_model->getBookingCountByUser($booking['user_id'])) + 1);

            //Add source
            $booking['source'] = "SB";
            $booking['request_type'] = "Repair - Out of Warranty";
            $response_booking_id = $booking['source'] . "-" . $booking['booking_id'];
            $booking['booking_id'] = "Q-" . $booking['source'] . "-" . $booking['booking_id'];
            $booking['create_date'] = date('Y-m-d H:i:s');

            $unit_details['booking_id'] = $booking['booking_id'];
            $booking['quantity'] = '1';
            $booking['partner_id'] = $unit_details['partner_id'] = "247001";
            $booking['partner_source'] = "Website";

            $unit_details['appliance_brand'] = $appliance_details['brand'] = $brand;
            $unit_details['appliance_category'] = $appliance_details['category'] = $category;
            $unit_details['appliance_capacity'] = $appliance_details['capacity'] = $capacity;
            $unit_details['appliance_tag'] = $appliance_details['tag'] = '';
            $unit_details['purchase_year'] = '';
            $unit_details['create_date'] = date('Y-m-d H:i:s');

            $unit_details['appliance_id'] = $this->web_booking_model->addappliance($appliance_details);
            log_message('info', 'Appliance ID: ' . $unit_details['appliance_id']);

            $unit_id = $this->web_booking_model->addunitdetails($unit_details);
            log_message('info', 'Unit ID: ' . $unit_id);

            $booking['current_status'] = "FollowUp";
            $booking['internal_status'] = "FollowUp";
            $booking['type'] = "Query";
            $booking['city'] = $city;
            $booking['booking_primary_contact_no'] = $mobile;
            $booking['booking_alternate_contact_no'] = '';
            $booking['booking_date'] = $booking_date;
            $booking['booking_timeslot'] = '';
            $booking['booking_address'] = $address;
            $booking['booking_pincode'] = $pincode;
            $booking['amount_due'] = '';
            $booking['query_remarks'] = $booking_remarks . '. If estimate not approved from customer, Rs. 300 '
                    . 'visit charge to be taken.<br/><br/>If Service Center could not arrange parts, no visit '
                    . 'charge to be collected from customer.';
            $booking['booking_remarks'] = $booking_remarks . '. If estimate not approved from customer, Rs. 300 '
                    . 'visit charge to be taken.<br/><br/>If Service Center could not arrange parts, no visit '
                    . 'charge to be collected from customer.';

            $state = $this->web_booking_model->getall_state($city);

            if (!empty($state)) {
                $booking['state'] = $state[0]['state'];
            }

            //Insert query
            $id = $this->web_booking_model->addbooking($booking);
            log_message('info', 'Booking Status: ' . $id);

            //Update campaign info table with new booking
            $url = strtok($_SERVER['HTTP_REFERER'], '?');
            //Get query string
            $query = substr($_SERVER['HTTP_REFERER'], strlen($url) + 1);
            $this->set_ad_campaign_info($query, $booking['booking_id']);

            if (!empty($id)) {
                //Send mail to Admin
                $subject = "New Booking Request from 247around.com";
                $message = "Dear Admin,<br/><br/>Congratulations! You have received new booking, details are mentioned below:<br/><br/>";
                $message .= "User: " . $name . "<br/>";
                $message .= "Mobile: " . $mobile . "<br/>";
                $message .= "Appliance: " . $service . "<br/>";
                $message .= "Address: " . $address . "<br/>";
                $message .= "City: " . $city . "<br/>";
                $message .= "Pincode: " . $pincode . "<br/>";
                $message .= "Booking Date: " . $booking_date . "<br/>";
                $message .= "Problem: " . $booking_remarks . "<br/>";
                $message .= "<br/>Thanks!";

                $this->notify->sendEmail('booking@247around.com', "nits@247around.com, booking@247around.com, sales@247around.com", '', "anuj.aggarwal@gmail.com", $subject, $message, '', 'Website Booking');

                //Send SMS to customer
                $sms_template = "Got it! Your request for %s Repair is confirmed. Your Booking ID is %s. Further discounts on App goo.gl/m0iAcS. 247Around 9555000247";
                $sms = sprintf($sms_template, $service, $response_booking_id);
                $this->notify->sendTransactionalSmsMsg91($mobile, $sms, 'Website Assign Vendor');

                //Send SMS to 247 sales team
                $sms2 = "Dear Sales, New booking from Website - Customer: " . $name . ", Mobile: " . $mobile . ", Appliance: " . $service . ". Please call urgently.";
                $this->notify->sendTransactionalSmsMsg91("8130572244", $sms2, 'Webiste Booking');

                $state_change['booking_id'] = $booking['booking_id'];
                $state_change['new_state'] = _247AROUND_FOLLOWUP;
                $state_change['old_state'] = _247AROUND_NEW_QUERY;
                $state_change['agent_id'] = "1"; // Default Agent 247around
                $state_change['partner_id'] = _247AROUND;
                $state_change['remarks'] = "Booking Inserted From Website";


                // Insert data into booking state change
                $this->web_booking_model->insert_booking_state_change($state_change);

                $response_msg = "Your Booking is Confirmed Now for " . date("d-M-Y", strtotime($booking_date)) . ", Booking ID is " . $response_booking_id .
                        ".<br/><br/>Our engineer will call you before visit. You can also reach us at 9555000247 for any queries or feedback."
                        . "<br/><br/>";

                $responseData = array('result' => 'ok',
                    'title' => 'Thank You !!!',
                    'message' => $response_msg
                );
            } else {
                log_message('info', __FUNCTION__ . 'Add booking failed from Website: ' . print_r($booking, TRUE));

                $response_msg = "We are sorry, Your request could not be confirmed, Please try after sometime."
                        . "<br/><br/>You can also reach us at 9555000247 for any queries or feedback.";

                $responseData = array('result' => 'error',
                    'title' => 'Apologies for Inconvenience',
                    'message' => $response_msg);

                $this->booking_submission_failed($name, $mobile, $service, $city);
            }
        } else {
            log_message('info', 'All inputs blank / Mobile invalid - Bot booking');

            $responseData = array('result' => 'error',
                'title' => 'Apologies for Inconvenience',
                'message' => 'Invalid input, please retry.');
            //$this->booking_submission_failed($name, $mobile, $service, $city);
        }

        $this->load->view('website/thank-you', $responseData);
        $this->load->view('website/testimonials');

        $data['city'] = $this->web_booking_model->get_active_city();
        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    /**
     * @desc: Send Mail to admin when booking is not inserted in db
     * @param: Customer name, mobile, service, city
     * @return : void
     */
    function booking_submission_failed($name, $mobile, $service, $city) {
        log_message('info', __FUNCTION__);

        //Send mail to Admin
        $subject = "Booking Submission failed from Website";
        $message = "Dear Admin,<br/><br/>We have not received new booking, details are mentioned below:<br/><br/>";
        $message .= "Customer Name: " . $name . "<br/>";
        $message .= "Customer Mobile: " . $mobile . "<br/>";
        $message .= "Appliance: " . $service . "<br/>";
        $message .= "City: " . $city . "<br/>";
        $message .= "<br/>Thanks!";

        $this->notify->sendEmail('booking@247around.com', "nits@247around.com, booking@247around.com, sales@247around.com", '', "anuj.aggarwal@gmail.com", $subject, $message, '', 'Booking Submission failed from Website');
    }

    public function find_all_thoughts($cat) {
        log_message('info', __FUNCTION__);

        $category = str_replace("-", " ", $cat);
        $data['thoughts_list'] = $this->thoughts_model->get_thoughts($category);
        $data['city'] = $this->web_booking_model->get_active_city();
        $services = $this->web_booking_model->selectservice();
        $data['services'] = json_decode(json_encode($services), True);

        $this->load->view('website/thoughts', $data);

        //$this->load->view('website/testimonials');
        //$this->load->view('website/how-it-works');

        $data['brands'] = $this->web_footer_model->get_brands();
        $this->load->view('website/footer-min', $data);
    }

    public function find_thought($u = "") {
        //log_message('info', __FUNCTION__);
        //log_message('info', __FUNCTION__ . $_SERVER['HTTP_REFERER']);

        $url = urldecode($u);
        $blogs = $this->url_table_model->get_blog_id($url);

        if (count($blogs) > 0) {
            $thoughts = $this->thoughts_model->get_thought($blogs[0]['blog_id']);

            $data = $thoughts[0];

            //Remove "-" from brand and place
            $data['brand'] = str_replace("-", " ", $blogs[0]['brand']);
            $data['place'] = str_replace("-", " ", $blogs[0]['place']);

            //TODO: To be corrected
            //We have defined <place> only in controller whereas we are using <city>
            //and <area> in our templates. It has to be made generic.
            //title
            $data['title'] = str_replace("<brand>", $data['brand'], $data['title']);
            $data['title'] = str_replace("<place>", $data['place'], $data['title']);
            $data['title'] = str_replace("<area>", $data['place'], $data['title']);
            $data['title'] = str_replace("<city>", $data['place'], $data['title']);

            //description
            $data['description'] = str_replace("<brand>", $data['brand'], $data['description']);
            $data['description'] = str_replace("<place>", $data['place'], $data['description']);
            $data['description'] = str_replace("<area>", $data['place'], $data['description']);
            $data['description'] = str_replace("<city>", $data['place'], $data['description']);

            //keyword
            $data['keyword'] = str_replace("<brand>", $data['brand'], $data['keyword']);
            $data['keyword'] = str_replace("<place>", $data['place'], $data['keyword']);
            $data['keyword'] = str_replace("<area>", $data['place'], $data['keyword']);
            $data['keyword'] = str_replace("<city>", $data['place'], $data['keyword']);

            //alternate_text
            $data['alternate_text'] = str_replace("<brand>", $data['brand'], $data['alternate_text']);
            $data['alternate_text'] = str_replace("<place>", $data['place'], $data['alternate_text']);
            $data['alternate_text'] = str_replace("<area>", $data['place'], $data['alternate_text']);
            $data['alternate_text'] = str_replace("<city>", $data['place'], $data['alternate_text']);

            //content
            $data['content'] = str_replace("<brand>", $data['brand'], $data['content']);
            $data['content'] = str_replace("<place>", $data['place'], $data['content']);
            $data['content'] = str_replace("<area>", $data['place'], $data['content']);
            $data['content'] = str_replace("<city>", $data['place'], $data['content']);

            //Return random master articles from all categories
            $all_blogs = $this->thoughts_model->get_thoughts("all");

            //Find 4 random technical articles
            $num = 4;
            $rand_keys = array_rand($all_blogs, $num);

            for ($i = 0; $i < $num; $i++) {
                $blogs_random[] = $all_blogs[$rand_keys[$i]];
            }

            //Limit number of blogs to 2 in case of mobile phones
            $isMobile = (bool) preg_match('#\b(ip(hone|od)|android\b.+\bmobile|opera m(ob|in)i|windows (phone|ce)|blackberry' .
                            '|s(ymbian|eries60|amsung)|p(alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]' .
                            '|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT']);

            if ($isMobile) {
                $num = 2;
            }

            $data['num_blogs'] = $num;
            $data['blogs'] = $blogs_random;

            $data['city'] = $this->web_booking_model->get_active_city();
            $services = $this->web_booking_model->selectservice();
            $data['services'] = json_decode(json_encode($services), True);
            $this->load->view('website/247around-blog-template-min', $data);
            $this->load->view('website/testimonials');

            $data2['city'] = $data['city'];
            $data2['brands'] = $this->web_footer_model->get_brands();
            $this->load->view('website/footer-min', $data2);
        } else {
            //Return 404
            show_404($url);
        }
    }

    // Function to get the client IP address
    function get_client_ip() {
        log_message('info', __FUNCTION__);

        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            log_message('info', 'Ip HTTP_CLIENT_IP Address ' . $ipaddress);
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            log_message('info', 'Ip Proxy Address ' . $ipaddress);
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            log_message('info', 'Ip Proxy Address ' . $ipaddress);
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            log_message('info', 'Ip Proxy Address ' . $ipaddress);
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
            log_message('info', 'Ip Proxy Address ' . $ipaddress);
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
            log_message('info', 'Ip Address ' . $ipaddress);
        } else {
            $ipaddress = 'UNKNOWN';
            log_message('info', 'Ip Proxy Address ' . $ipaddress);
        }

        return;
    }

    /**
     * @desc:
     */
    public function booking_schedule($booking_id) {
        log_message('info', __FUNCTION__);
        $data['flag'] = $this->web_booking_model->get_scheduling_flag($booking_id);
        if ($data['flag'] == "0") {
            //$booking_id = "Q-SS-162681608132";
            $data['booking_details'] = $this->web_booking_model->booking_history1($booking_id);
            $data['appliance_details'] = $this->web_booking_model->get_appliance_details($data['booking_details'][0]['appliance_id']);
            $data['cost'] = $this->get_product_charge($data['booking_details'][0]['services']);
            $data['city'] = $this->web_booking_model->get_active_city();

            $this->load->view('website/booking_schedule', $data);
            $this->load->view('website/logo_slider');

            $data['brands'] = $this->web_footer_model->get_brands();
            $this->load->view('website/footer', $data);
        } else {

            redirect(base_url() . 'thanku_message');
        }
    }

    function process_schedule_booking($booking_id) {
        log_message('info', __FUNCTION__);
        log_message('info', "booking_id " . print_r($booking_id, true));
        $data['flag'] = $this->web_booking_model->get_scheduling_flag($booking_id);
        if ($data['flag'] == "0") {
            $booking['booking_alternate_contact_no'] = $this->input->post('booking_alternate_contact_no');
            $booking['booking_primary_contact_no'] = $this->input->post('booking_primary_contact_no');
            $booking['city'] = $this->input->post('city');
            $booking['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
            $booking['booking_pincode'] = $this->input->post('booking_pincode');
            $booking['booking_timeslot'] = "4PM-7PM";
            $booking['booking_remarks'] = $this->input->post('problem_description');
            $booking['booking_address'] = $this->input->post('booking_address');
            $booking['query_remarks'] = "Customer has scheduled this";
            $booking['update_date'] = date('Y-m-d H:i:s');

            $this->web_booking_model->update_flag($booking_id, array('flag' => '1'));

            log_message('info', "Sd booking update " . print_r($booking, true));
            $status = $this->web_booking_model->update_booking($booking_id, $booking);
            if ($status == FALSE) {
                log_message('info', "Sd booking update fail");
                $subject = "Sd booking Updation failed from website";
                $message = " Booking id is " . $booking_id;

                $this->notify->sendEmail('booking@247around.com', "nits@247around.com, booking@247around.com, sales@247around.com", '', "anuj.aggarwal@gmail.com", $subject, $message, '', 'Schedule Booking');
            }

            redirect(base_url() . 'thanku_message');
        } else {
            redirect(base_url() . 'thanku_message');
        }
    }

    function thanku_message() {
        $response_msg = "Your request has been successfully submitted." .
                ".<br/><br/>Our team will contact you shortly. "
                . "<br/><br/>You can also reach us at 9555000247.";

        $responseData = array('result' => 'ok',
            'title' => 'Thank You !!!',
            'message' => $response_msg
        );


        $this->load->view('website/thank-you', $responseData);
    }

    function generate_otp() {
        $digits = 4;
        //random no between 1000 and 9999
        $otp_number = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

        return $otp_number;
    }

    /**
     * @desc: This is used to get data from booking form and validate data.
     * Also, an OTP is sent to user to confirm the phone number.
     * All entered data is saved in web_booking table.
     */
    function verify_booking() {
        log_message('info', __FUNCTION__ . '=> ' . $_SERVER['HTTP_REFERER']);

        $this->get_client_ip();

        //check validation for input post data
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|trim');
        $this->form_validation->set_rules('service', 'service', 'required|trim');
        $this->form_validation->set_rules('city', 'City', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            log_message('info', 'Empty Field');
            redirect(base_url());
        } else {
            $data['name'] = $this->input->post('name');
            $data['mobile'] = $this->input->post('mobile');
            $data['service'] = $this->input->post('service');
            $data['city'] = $this->input->post('city');

            log_message('info', " Website Booking Request Data:" . print_r($data, true));

            $fv = TRUE;
            //Make it FALSE if all params are NULL (bot-did-this-booking), issue needs to be debugged further
            if (($this->input->post('name') == '') && ($this->input->post('mobile') == '') && ($this->input->post('service') == '') && ($this->input->post('city') == '')) {
                $fv = FALSE;
            }

            //mobile no should be of 10 digits and should start with 7, 8 or 9 only.
            $l = substr($this->input->post('mobile'), 0, 1);
            if ((strlen($this->input->post('mobile')) == 10) &&
                    ($l >= 7)) {
                $fv = TRUE;
            } else {
                $fv = FALSE;
            }

            //if service is blank, make it TV
            //todo: this should not happen by the way
            if ($this->input->post('service') == '') {
                $data['service'] = 'Television';
            }

            if ($fv === TRUE) {
                //input is clean, send otp now
                $data['otp_number'] = $this->generate_otp();
                $data['request_verification_code'] = md5($data['mobile'] . $data['otp_number']);

                $web_booking_id = $this->web_booking_model->insert_web_booking($data);
                if ($web_booking_id) {
                    //booking saved in web_booking table since user is not yet verified the OTP
                    $verification_code['request_verification_code'] = $data['request_verification_code'];

                    //send SMS
                    $sms = "Use " . $data['otp_number'] . " as OTP (One Time Password) to book your Service Request on 247around. Do not share this OTP with anyone for security reason. 247around Team";
                    $this->notify->sendTransactionalSmsMsg91($data['mobile'], $sms, 'OTP Register Website Booking');

                    //send the verification code as well to match OTP with the specific request
                    $service_id = $this->web_booking_model->selectidbyservice($data['service']);
                    $verification_code['brand'] = $this->web_booking_model->getBrandForService($service_id[0]['id']);
                    $verification_code['service_id'] = $service_id[0]['id'];

                    $this->load->view('website/get_otp_page', $verification_code);
                    $this->load->view('website/testimonials');

                    $data['city'] = $this->web_booking_model->get_active_city();
                    $data['brands'] = $this->web_footer_model->get_brands();

                    $this->load->view('website/footer-min', $data);
                } else {
                    log_message('info', " Website Booking: Insert into web_booking table failed: " . print_r($data, true));
                    //$this->booking_verification();
                    redirect(base_url());
                }
            } else {
                log_message('info', " Website Booking Invalid data");
                //$this->booking_verification();
                redirect(base_url());
            }
        }
    }

    /**
     * @desc: This is used to verify otp number. If otp verified then insert booking
     */
    function booking_verification() {
        //log_message('info', __FUNCTION__ . '=> ' . $_SERVER['HTTP_REFERER']);
        // Check, Is data coming from post
        if ($this->input->post()) {

            //check validation for input post data
            $this->form_validation->set_rules('pincode', 'Pincode', 'required|trim');
            $this->form_validation->set_rules('booking_date', 'Booking Date', 'required|trim');
            $this->form_validation->set_rules('address', 'Address', 'required|trim');
            $this->form_validation->set_rules('booking_remarks', 'Booking Remarks', 'required|trim');
            $this->form_validation->set_rules('brand', 'Brand', 'required|trim');
            $this->form_validation->set_rules('category', 'Category', 'required|trim');
            //$this->form_validation->set_rules('capacity', 'Capacity', 'trim');

            if ($this->form_validation->run() == FALSE) {
                log_message('info', 'Empty Field');

                $verification_code['request_verification_code'] = $this->input->post('request_verification_code');
                $verification_code['empty_field'] = "Please Fill all the data";

                $verification_code['brand'] = $this->web_booking_model->getBrandForService($this->input->post('service_id'));
                $verification_code['service_id'] = $this->input->post('service_id');

                $this->load->view('website/get_otp_page', $verification_code);
                $this->load->view('website/testimonials');

                $data['city'] = $this->web_booking_model->get_active_city();
                $data['brands'] = $this->web_footer_model->get_brands();

                $this->load->view('website/footer-min', $data);
            } else {
                $code['request_verification_code'] = $this->input->post('request_verification_code');
                $code['pincode'] = $this->input->post('pincode');
                $code['booking_date'] = date('d-m-Y', strtotime($this->input->post('booking_date')));
                $code['address'] = $this->input->post('address');
                $code['booking_remarks'] = $this->input->post('booking_remarks');
                $code['otp_number'] = $this->input->post('otp_number');
                $code['brand'] = $this->input->post('brand');
                $code['category'] = $this->input->post('category');
                $code['capacity'] = $this->input->post('capacity');

                log_message('info', ' Request verification code: ' . $code['request_verification_code'] . ", "
                        . "OTP Number" . $code['otp_number']);
                
                $verified = $this->web_booking_model->verify_otp($code);
                // check data is verified
                if ($verified) {
                    log_message('info', 'OTP Verified');

                    $verified[0]['pincode'] = $code['pincode'];
                    $verified[0]['address'] = $code['address'];
                    $verified[0]['booking_date'] = $code['booking_date'];
                    $verified[0]['booking_remarks'] = $code['booking_remarks'];
                    $verified[0]['brand'] = $code['brand'];
                    $verified[0]['category'] = $code['category'];
                    $verified[0]['capacity'] = $code['capacity'];

                    $this->book_service($verified);
                } else {
                    //Not Verified
                    log_message('info', 'OTP Not Verified');

                    $verification_code['request_verification_code'] = $code['request_verification_code'];
                    $verification_code['invalid_otp'] = "OTP verification failed, please enter valid OTP";

                    $verification_code['brand'] = $this->web_booking_model->getBrandForService($this->input->post('service_id'));
                    $verification_code['service_id'] = $this->input->post('service_id');

                    $this->load->view('website/get_otp_page', $verification_code);
                    $this->load->view('website/testimonials');

                    $data['city'] = $this->web_booking_model->get_active_city();
                    $data['brands'] = $this->web_footer_model->get_brands();

                    $this->load->view('website/footer-min', $data);
                }
            }
        } else {
            //Website Booking Direct access
            log_message('info', ' Website Booking Direct access ');

            $responseData = array('result' => 'error',
                'title' => 'Apologies for Inconvenience',
                'message' => 'Oops, your have exceeded the time limit. Please try again.');

            $this->load->view('website/thank-you', $responseData);
            $this->load->view('website/testimonials');

            $data['city'] = $this->web_booking_model->get_active_city();
            $data['brands'] = $this->web_footer_model->get_brands();

            $this->load->view('website/footer-min', $data);
        }
    }

    function get_product_charge($service) {

        switch ($service) {
            case 'Washing Machine':

                return 'We will provide free installation & demo';
                break;

            case 'Refrigerator':

                return 'We will provide free installation & demo';
                break;

            case 'Microwave':

                return 'We will provide free installation & demo';
                break;

            case 'Television':

                return 'We will provide free installation with wall-mounted stand';
                break;

            case 'Water Purifier':

                return 'We will provide free installation & demo';
                break;

            case 'Air Conditioner':

                return 'We will provide Window AC installation at Rs550  &  Ac-split at Rs1400';
                break;

            default:
                break;
        }
    }

    /**
     * Get ad-campaign info for this booking, if any.
     *
     * Checks the query string, gets all the campaign parameters and update
     * the booking-campaign table. If no query string, user came through some
     * other means (emailer from Sendgrid for e.g.) and not through google campaign.
     *
     * @param String Query String
     *
     * Query string is of the template when user reaching us through clicking on Google Ads:
     * campaignid=123&adgroupid=456&feeditemid=789&targetid=101112&loc_interest_ms=112345
     * &loc_physical_ms=2837829&matchtype=b&device=oiho&devicemodel=aklsoiow&creative=819920901
     * &keyword=tvrepair&adposition=1
     *
     * Query string could be empty as well if user has reached us through some other means.
     *
     * @param String Booking ID
     *
     * @return	void
     */
    function set_ad_campaign_info($query, $booking_id) {
        log_message('info', __FUNCTION__);

        //Initialize array
        $data = array();

        if (strstr($query, '&')) {
            //Save all query params in an array
            $params = explode("&", $query);

            foreach ($params as $p) {
                //$p contains 'key=value' string now, explode it again
                $kv = explode("=", $p);
                $data += [$kv[0] => urldecode($kv[1])];
            }
        }

        //Add booking ID as well
        $data += ['booking_id' => $booking_id];
        log_message('info', print_r($data, true));

        //Insert
        $this->ad_campaigns->insert($data);
    }

    function get_Category_For_Service() {

        $service_id = $this->input->post('service_id');
        $brand = $this->input->post('brand');
        $priceMappingId = "247001";
        $result = $this->web_booking_model->getCategoryForService($service_id, $priceMappingId);

        $option = '<option selected disabled>Select Category</option>';

        foreach ($result as $value) {
            $option .= "<option value='" . $value['category'] . "'";
            $option .= " > ";
            $option .= $value['category'] . "</option>";
        }

        echo $option;
    }

    function get_capacity_For_category() {

        $service_id = $this->input->post('service_id');
        $category = $this->input->post('category');
        $brand = $this->input->post('brand');
        $priceMappingId = "247001";
        $result = $this->web_booking_model->getCapacityForCategory($service_id, $category, "", $priceMappingId);
        if(!empty($result[0]['capacity'])){
            $option = '<option selected disabled>Select Capacity</option>';
            
            foreach ($result as $value) {
                $option .= "<option value='" . $value['capacity'] . "'";
                $option .= " > ";
                $option .= $value['capacity'] . "</option>";
            }
            
            echo $option;
            
        }else{
            echo "empty";
        }
        

        
    }
    
    /**
     * @desc This is used to download QR code image 
     * @param String $en_booking_id base64_encoded
     */
    function getQrCode($qr_id) {
        $id = base64_decode(urldecode($qr_id));

        if (!empty($id)) {

            $qrData = $this->web_booking_model->get_paytm_payment_qr_code(array("id" => $id));

            if (!empty($qrData)) {

                if (!empty($qrData[0]['qr_image_url'])) {

                    $file_name = $qrData[0]['qr_image_name'];
                    $file_url = S3_URL . $qrData[0]['qr_image_url']; 
                    header('Content-Type: application/octet-stream');
                    header("Content-Transfer-Encoding: Binary");
                    header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
                    readfile($file_url);
                    exit();
                } else {
                    log_message('info', __METHOD__ . " QR Image Link Not Found" . $id);
                }
            } else {
                log_message('info', __METHOD__ . " QR Code not generated " . $id);
            }
        } else {
            log_message('info', __METHOD__ . " QR Code not generated " . $id);
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
