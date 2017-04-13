<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

/**
 * Description of Booking_utilities
 *
 * @author anujaggarwal
 */
class Booking_utilities {

    var $My_CI;

    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->library('PHPReport');
	$this->My_CI->load->library('email');
	$this->My_CI->load->library('s3');
	$this->My_CI->load->library('form_validation');
	$this->My_CI->load->library("session");
        $this->My_CI->load->library("notify");
	$this->My_CI->load->helper('download');
	$this->My_CI->load->helper(array('form', 'url'));
	$this->My_CI->load->model('employee_model');
	$this->My_CI->load->model('booking_model');
	$this->My_CI->load->model('reporting_utils');
    }

    public function lib_prepare_job_card_using_booking_id($booking_id) {
	log_message('info', __FUNCTION__ . " => Entering, Booking ID: " . $booking_id);

        $template = 'BookingJobCard_Template-v8.xlsx';
        //set absolute path to directory with template files
        $templateDir = FCPATH . "application/controllers/excel-templates/";
        //set config for report
        $config = array(
            'template' => $template,
            'templateDir' => $templateDir
        );

        //load template
        $R = new PHPReport($config);
        //log_message('info', "PHP report");
        $booking_details = $this->My_CI->booking_model->getbooking_history($booking_id);
        if (!empty($booking_details)) {
            $unit_where = array('booking_id' => $booking_id, 'pay_to_sf' => '1');
            $unit_details = $this->My_CI->booking_model->get_unit_details($unit_where);
            $meta = array();
            $meta['upcountry_charges'] = 0;
            
            if ($booking_details[0]['upcountry_paid_by_customer'] == 1) {
                $meta['upcountry_charges'] = $booking_details[0]['upcountry_distance'] * DEFAULT_UPCOUNTRY_RATE;
            }
            
            $meta['appliance_description'] = "";
            if(!empty($unit_details)){
                $meta['appliance_description'] = $unit_details[0]['appliance_description'];
            }
            $booking_unit_details = array();
            log_message('info', __FUNCTION__ . " => Entering, Booking ID: " . print_r($unit_details, true));
            foreach ($unit_details as $value) {
                $array = array();
                $array['appliance_category'] = $value['appliance_category'];
                $array['appliance_capacity'] = $value['appliance_capacity'];
                $array['appliance_brand'] = $value['appliance_brand'];
                $array['model_number'] = $value['model_number'];
                $array['price_tags'] = $value['price_tags'];
                $array['customer_net_payable'] = $value['customer_net_payable'];
                array_push($booking_unit_details, $array);
            }
            $R->load(array(
                array(
                    'id' => 'booking',
                    //'repeat' => TRUE,
                    'data' => $booking_details[0],
                    //'minRows' => 2,
                    'format' => array(
                        'booking_date' => array('datetime' => 'd/M/Y'),
                        'amount_due' => array('number' => array('prefix' => 'Rs. ')),
                    )
                ),
                array(
                    'id' => 'unit',
                    'repeat' => TRUE,
                    'data' => $booking_unit_details,
                    //'minRows' => 2,
                    'format' => array(
                        //'create_date' => array('datetime' => 'd/M/Y'),
                        'total_price' => array('number' => array('prefix' => 'Rs. ')),
                    )
                ),
                array(
                    'id' => 'meta',
                    'repeat' => false,
                    'data' => $meta,
                ),
                    )
            );

            //Get populated XLS with data
            if ($booking_details[0]['current_status'] == "Rescheduled") {
                $output_file_suffix = "-RESC-" . $booking_details[0]['booking_date'];
            } else {
                $output_file_suffix = "";
            }

            $output_file_dir = TMP_FOLDER;
            $output_file = "BookingJobCard-" . $booking_id . $output_file_suffix;

            $output_file_excel = $output_file_dir . $output_file . ".xlsx";
            if (file_exists($output_file_excel)) {
                $res1 = 0;
                system(" chmod 777 " . $output_file_excel, $res1);
                unlink($output_file_excel);
            }
            $R->render('excel', $output_file_excel);
            $res1 = 0;
            system(" chmod 777 " . $output_file_excel, $res1);
            //$output_file_pdf = $output_file_dir . $output_file . ".pdf";
           

            //$cmd = "curl -F file=@" . $output_file_excel . " http://do.convertapi.com/Excel2Pdf?apikey=278325305" . " -o " . $output_file_pdf;
//            putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');
//            $tmp_path = libreoffice_pdf;
//            $tmp_output_file = libreoffice_output_file;
//            $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
//                    '/usr/bin/unoconv --format pdf --output ' . $output_file_pdf . ' ' .
//                    $output_file_excel . ' 2> ' . $tmp_output_file;
//
//            $output = '';
//            $result_var = '';
//
//            exec($cmd, $output, $result_var);
//            $res2 = 0;
//            system(" chmod 777 " . $output_file_pdf, $res2);
//
//            log_message('info', __FUNCTION__ . " Result Var Job card creation " . print_r($result_var, true));

            //Upload Excel & PDF files to AWS
            $bucket = BITBUCKET_DIRECTORY;
            $directory_xls = "jobcards-pdf/" . $output_file . ".xlsx";
            $this->My_CI->s3->putObjectFile($output_file_excel, $bucket, $directory_xls, S3::ACL_PUBLIC_READ);
            
            //Update JOb Card Booking
            $this->My_CI->booking_model->update_booking($booking_id,  array('booking_jobcard_filename' =>$output_file . ".xlsx"));

            //$directory_pdf = "jobcards-pdf/" . $output_file . ".pdf";
           // $this->My_CI->s3->putObjectFile($output_file_pdf, $bucket, $directory_pdf, S3::ACL_PUBLIC_READ);
            //exec("rm -rf " . escapeshellarg($output_file_pdf));
            exec("rm -rf " . escapeshellarg($output_file_excel));
        }

        log_message('info', __FUNCTION__ . " => Exiting, Booking ID: " . $booking_id);
    }

    //This function sends email/sms to the assigned vendor
    function lib_send_mail_to_vendor($booking_id, $additional_note) {
        log_message('info', __FUNCTION__ . " => Entering, Booking Id: ". $booking_id);

        $getbooking = $this->My_CI->booking_model->getbooking_history($booking_id,"join");

        if (!empty($getbooking)) {
            $date1 = date('d-m-Y', strtotime('now'));
            $date2 = $getbooking[0]['booking_date'];
            $datediff = ($date1 - $date2) / (60 * 60 * 24);

            $month = date("m", strtotime($getbooking[0]['booking_date']));
            $dd = date("d", strtotime($getbooking[0]['booking_date']));
            $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $mm = $months[$month - 1];

            if ($datediff == 0) {
                $bookingdate = "Today";
            } elseif ($datediff == 1) {
                $bookingdate = "Tomorrow";
            } else {
                $bookingdate = $dd . " " . $mm;
            }

            $smsBody = "Booking - " . substr($getbooking[0]['name'], 0, 20) . ", " . $getbooking[0]['booking_primary_contact_no'] 
                    . ", " . $getbooking[0]['services'] . ", " . $bookingdate ."/" 
                    . $getbooking[0]['booking_timeslot'] .  ", " . substr($getbooking[0]['booking_address'], 0, 60)
                    . ", ". $getbooking[0]['booking_pincode'] . ". 247around";
            
            //Send SMS to vendor
            $this->My_CI->notify->sendTransactionalSms($getbooking[0]['primary_contact_phone_1'], $smsBody);
            
            //For saving SMS to the database on sucess
            $this->My_CI->notify->add_sms_sent_details($getbooking[0]['user_id'], 'vendor' , $getbooking[0]['primary_contact_phone_1'],
                    $smsBody, $getbooking[0]['booking_id'],"booking_details_to_sf");            
        } else {
            echo "Booking does not exist.";
        }
        
        log_message('info', __FUNCTION__ . " => Exiting, Booking Id: ". $booking_id);
    }
    
     /*
     * @desc: This function is used to create acc to Service center, state and city
     * 
    * params: String sf_list
    * return :void
    *
    */

   function booking_report_by_service_center($sf_list,$cron_flag) {

       $data = $this->My_CI->reporting_utils->get_booking_by_service_center($sf_list);
       
       foreach ($data['service_center_id'] as $key => $val) {
           
           //Setting State and City value
           if (isset($data['data'][$val]['yesterday_booked']['state'])) {
               $state = $data['data'][$val]['yesterday_booked']['state'];
               $city = $data['data'][$val]['yesterday_booked']['city'];
               $service_center_name = $data['data'][$val]['yesterday_booked']['service_center_name'];
               $service_center_id = $data['data'][$val]['yesterday_booked']['service_center_id'];
               $active = $data['data'][$val]['yesterday_booked']['active'];
               $temporary_on_off = $data['data'][$val]['yesterday_booked']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['yesterday_completed']['state'])) {
               $state = $data['data'][$val]['yesterday_completed']['state'];
               $city = $data['data'][$val]['yesterday_completed']['city'];
               $service_center_name = $data['data'][$val]['yesterday_completed']['service_center_name'];
               $service_center_id = $data['data'][$val]['yesterday_completed']['service_center_id'];
               $active = $data['data'][$val]['yesterday_completed']['active'];
               $temporary_on_off = $data['data'][$val]['yesterday_completed']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['yesterday_cancelled']['state'])) {
               $state = $data['data'][$val]['yesterday_cancelled']['state'];
               $city = $data['data'][$val]['yesterday_cancelled']['city'];
               $service_center_name = $data['data'][$val]['yesterday_cancelled']['service_center_name'];
               $service_center_id = $data['data'][$val]['yesterday_cancelled']['service_center_id'];
               $active = $data['data'][$val]['yesterday_cancelled']['active'];
               $temporary_on_off = $data['data'][$val]['yesterday_cancelled']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['month_completed']['state'])) {
               $state = $data['data'][$val]['month_completed']['state'];
               $city = $data['data'][$val]['month_completed']['city'];
               $service_center_name = $data['data'][$val]['month_completed']['service_center_name'];
               $service_center_id = $data['data'][$val]['month_completed']['service_center_id'];
               $active = $data['data'][$val]['month_completed']['active'];
               $temporary_on_off = $data['data'][$val]['month_completed']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['month_cancelled']['state'])) {
               $state = $data['data'][$val]['month_cancelled']['state'];
               $city = $data['data'][$val]['month_cancelled']['city'];
               $service_center_name = $data['data'][$val]['month_cancelled']['service_center_name'];
               $service_center_id = $data['data'][$val]['month_cancelled']['service_center_id'];
               $active = $data['data'][$val]['month_cancelled']['active'];
               $temporary_on_off = $data['data'][$val]['month_cancelled']['temporary_on_off'];
           }

           if (isset($data['data'][$val]['last_2_day']['state'])) {
               $state = $data['data'][$val]['last_2_day']['state'];
               $city = $data['data'][$val]['last_2_day']['city'];
               $service_center_name = $data['data'][$val]['last_2_day']['service_center_name'];
               $service_center_id = $data['data'][$val]['last_2_day']['service_center_id'];
               $active = $data['data'][$val]['last_2_day']['active'];
               $temporary_on_off = $data['data'][$val]['last_2_day']['temporary_on_off'];
           }
           
           if (isset($data['data'][$val]['last_3_day']['state'])) {
               $state = $data['data'][$val]['last_3_day']['state'];
               $city = $data['data'][$val]['last_3_day']['city'];
               $service_center_name = $data['data'][$val]['last_3_day']['service_center_name'];
               $service_center_id = $data['data'][$val]['last_3_day']['service_center_id'];
               $active = $data['data'][$val]['last_3_day']['active'];
               $temporary_on_off = $data['data'][$val]['last_3_day']['temporary_on_off'];
           }
           if (isset($data['data'][$val]['greater_than_5_days']['state'])) {
               $state = $data['data'][$val]['greater_than_5_days']['state'];
               $city = $data['data'][$val]['greater_than_5_days']['city'];
               $service_center_name = $data['data'][$val]['greater_than_5_days']['service_center_name'];
               $service_center_id = $data['data'][$val]['greater_than_5_days']['service_center_id'];
               $active = $data['data'][$val]['greater_than_5_days']['active'];
               $temporary_on_off = $data['data'][$val]['greater_than_5_days']['temporary_on_off'];
           }

           $state_final[] = $state;
           $way_final['state'] = $state;
           $way_final['city'] = $city;
           $way_final['active'] = $active;
           $way_final['temporary_on_off'] = $temporary_on_off;
           $way_final['service_center_name'] = $service_center_name;
           $way_final['service_center_id'] = $service_center_id;
           $way_final['yesterday_booked'] = (isset($data['data'][$val]['yesterday_booked']['booked']) ? $data['data'][$val]['yesterday_booked']['booked'] : '  ');
           $way_final['yesterday_completed'] = (isset($data['data'][$val]['yesterday_completed']['completed']) ? $data['data'][$val]['yesterday_completed']['completed'] : ' ');
           $way_final['yesterday_cancelled'] = (isset($data['data'][$val]['yesterday_cancelled']['cancelled']) ? $data['data'][$val]['yesterday_cancelled']['cancelled'] : '  ');
           $way_final['month_completed'] = (isset($data['data'][$val]['month_completed']['completed']) ? $data['data'][$val]['month_completed']['completed'] : '  ');
           $way_final['month_cancelled'] = (isset($data['data'][$val]['month_cancelled']['cancelled']) ? $data['data'][$val]['month_cancelled']['cancelled'] : '  ');
           $way_final['last_2_day'] = (isset($data['data'][$val]['last_2_day']['booked']) ? $data['data'][$val]['last_2_day']['booked'] : '  ');
           $way_final['last_3_day'] = (isset($data['data'][$val]['last_3_day']['booked']) ? $data['data'][$val]['last_3_day']['booked'] : '  ');
           $way_final['greater_than_5_days'] = (isset($data['data'][$val]['greater_than_5_days']['booked']) ? $data['data'][$val]['greater_than_5_days']['booked'] : '  ');

           $final_way[] = $way_final;
       }
       
       //Getting States and City List
       foreach($final_way as $value){
           $state_array[] = $value['state'];
           $city_array[] = $value['city'];
           $sf_array[] = $value['service_center_name'];
       }
       $state_array = array_unique($state_array);
       $city_array = array_unique($city_array);
       $sf_array = array_unique($sf_array);
       
       //Making State Option
       $state_option = "";
       sort($state_array);
       foreach($state_array as $value){
            $state_option .= "<option value='".$value."'>".$value.'</option>';
       }
       //Making City Option
       $city_option = "";
       sort($city_array);
       foreach($city_array as $value){
            $city_option .= "<option value='".$value."'>".$value.'</option>';
       }
       
       //Making SF Name Option
       $sf_option = "";
       sort($sf_array);
       foreach($sf_array as $value){
            $sf_option .= "<option value='".$value."'>".$value.'</option>';
       }
       
       //Getting RM List for Dropdown
       $rm_details = $this->My_CI->employee_model->get_rm_details();
       $rm_option = "";
       foreach($rm_details as $value){
           $rm_option .= "<option value='".$value['full_name']."'>".$value['full_name'].'</option>';
       }
       
       //Add Bootstrap CSS for CRON calls
       $css = '';
       if($cron_flag == 1){
           $css = '<link href="http://localhost/247around-dev/css/bootstrap.min.css" rel="stylesheet">';
       }
       
       //Generating HTML for the email
       $html = '
                   <html xmlns="http://www.w3.org/1999/xhtml">
                     <head>
                       <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                     </head><body>'.$css.'
                     <div style="margin-top: 30px;font-family:Helvetica;" class="container-fluid table-responsive">
                         <table style="margin-bottom: 20px;border: 1px solid #ddd; border-collapse: collapse;" class="js-dynamitable">
                           <thead>
                             <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:9%">Regional Manager</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:10%">State</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:9%">City</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Name</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Yesterday Booked<p><span class="js-sorter-desc  glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Yesterday Completed<p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Yesterday Cancelled<p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">' . date('M') . ' Booking Completed <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">' . date('M') . ' Booking Cancelled <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">0-2 Days <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">3-5 Days <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE"> > 5 Days <p><span class="js-sorter-desc     glyphicon glyphicon-chevron-down pull-right"></span> <span class="js-sorter-asc     glyphicon glyphicon-chevron-up pull-right"></span></th>

                             </tr>
                             <tr><td>&nbsp;</td></tr>
                             <tr>
                                <th>
                                <select class="js-filter  form-control">
                                    <option value="">Filter RM</option>'.$rm_option.'
                                </select>

                                </th>
                                <th>
                                <select class="js-filter  form-control">
                                    <option value="">Filter State</option>'.$state_option.'
                                </select>
                                </th>
                                <th>
                                <select class="js-filter  form-control">
                                    <option value="">Filter City</option>'.$city_option.'
                                </select>
                                </th>
                                <th>
                                <select class="js-filter  form-control">
                                    <option value="">Filter Name</option>'.$sf_option.'
                                </select>
                                </th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                                <th><input class="js-filter  form-control" type="text" value=""></th>
                              </tr>
                              <tr><td>&nbsp;</td></tr>
                           </thead>
                           <tbody >';
       

       $show_state = [];
       $greater_than_5_days = 0;
       $overall_greater_than_5_days = 0;
       $yesterday_booked = 0;
       $overall_yesterday_booked = 0;
       $yesterday_completed = 0;
       $overall_yesterday_completed = 0;
       $yesterday_cancelled = 0;
       $overall_yesterday_cancelled = 0;
       $month_completed = 0;
       $overall_month_completed = 0;
       $month_cancelled = 0;
       $overall_month_cancelled = 0;
       $last_2_day = 0;
       $overall_last_2_day = 0;
       $last_3_day = 0;
       $overall_last_3_day = 0;
       $state_final = array_unique($state_final);
       foreach ($state_final as $val) {

           foreach ($final_way as $key => $value) {
               
               //Getting  RM Details
                $employee_relation = $this->My_CI->vendor_model->get_rm_sf_relation_by_sf_id($value['service_center_id']);
                if (!empty($employee_relation)) {
                    $rm = $this->My_CI->employee_model->getemployeefromid($employee_relation[0]['agent_id'])[0]['full_name'];
                } else {
                    $rm = "";
                }

                $style = '';
               if($value['active'] == 0 ){
                   $style = "background:#f25788";
               }else if(is_null($value['active'])){
                   $style = "background:#f4f44b";
               }else if($value['temporary_on_off'] == 0){
                   $style = "background:#7986CB";
               }
               
               if ($value['state'] == $val) {
                       $yesterday_booked = 0;
                           $yesterday_completed = 0;
                           $yesterday_cancelled = 0;
                           $month_completed = 0;
                           $month_cancelled = 0;
                           $last_2_day = 0;
                           $last_3_day = 0;
                           $greater_than_5_days = 0;

                   $html.="<tr style='".$style."'>" .
                           "<td style='text-align: center;border: 1px solid #001D48;padding:5px;font-size:80%' class='text-right'>" . $rm .
                           "<td style='text-align: center;border: 1px solid #001D48;padding:5px;font-size:80%' class='text-right'>" . $value['state'] .
                           "<td style='text-align: center;border: 1px solid #001D48;padding:5px;font-size:80%;' class='text-right'>" . $value['city'] .
                           "</td><td style='text-align: center;border: 1px solid #001D48;font-size:80%;padding:5px;' class='text-right'>" . $value['service_center_name'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right'>" . $value['yesterday_booked'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right'>" . $value['yesterday_completed'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right'>" . $value['yesterday_cancelled'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right'>" . $value['month_completed'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right'>" . $value['month_cancelled'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right'>" . $value['last_2_day'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right'>" . $value['last_3_day'] .
                           " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1;padding:5px;font-size:80%' class='text-right'>" . $value['greater_than_5_days'] .
                           " </td></tr>";

                   $yesterday_booked += $value['yesterday_booked'];
                   $overall_yesterday_booked += $value['yesterday_booked'];
                   $yesterday_completed += $value['yesterday_completed'];
                   $overall_yesterday_completed += $value['yesterday_completed'];
                   $yesterday_cancelled += $value['yesterday_cancelled'];
                   $overall_yesterday_cancelled += $value['yesterday_cancelled'];
                   $month_completed += $value['month_completed'];
                   $overall_month_completed += $value['month_completed'];
                   $month_cancelled += $value['month_cancelled'];
                   $overall_month_cancelled += $value['month_cancelled'];
                   $last_2_day += $value['last_2_day'];
                   $overall_last_2_day += $value['last_2_day'];
                   $last_3_day += $value['last_3_day'];
                   $overall_last_3_day += $value['last_3_day'];
                   $greater_than_5_days += $value['greater_than_5_days'];
                   $overall_greater_than_5_days += $value['greater_than_5_days'];
               }
           }
       }

       $html .="</tbody>
                         </table>";
       $html.="<table style='margin-bottom: 20px;border: 1px solid #ddd; border-collapse: collapse;'><tbody><tr>" .
               "</td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:570px'>" . 'TOTAL' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong>" . $overall_yesterday_booked . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong>" . $overall_yesterday_completed . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong>" . $overall_yesterday_cancelled . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong>" . $overall_month_completed . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong>" . $overall_month_cancelled . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong>" . $overall_last_2_day . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong>" . $overall_last_3_day . '<strong>' .
               " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900;width:7%'><strong>" . $overall_greater_than_5_days . '<strong>' .
               " </td></tr>";

       $html .= '</tbody>
                         </table></div>
                 <script src="'.  base_url().'js/dynamitable.jquery.min.js"></script>'
               . '<script type="text/javascript">$("select").select2();</script>';
       $html .= '</body>
                   </html>';

       return $html;

   }
   
   /*
     * @desc: This function is used to get the partner current status and partner internal status
     * from partner_status_booking_mapping table
     * params: string
     * return :array
     *
     */
    function get_partner_status_mapping_data($current_status, $internal_status, $partner_id = "", $booking_id = "") {
        $partner_status = $this->My_CI->booking_model->get_partner_status($partner_id, $current_status, $internal_status);

        if (!empty($partner_status[0]['partner_current_status']) && !empty($partner_status[0]['partner_internal_status'])) {
            $booking['partner_current_status'] = $partner_status[0]['partner_current_status'];
            $booking['partner_internal_status'] = $partner_status[0]['partner_internal_status'];
        } else {
            $booking['partner_current_status'] = $current_status;
            $booking['partner_internal_status'] = $current_status;

            $this->send_mail_When_no_data_found($current_status, $internal_status, $booking_id, $partner_id);
        }

        return array($booking['partner_current_status'], $booking['partner_internal_status']);
    }

    /*
     * @desc: This function is used to create report for service centers who is new (withins 2 months old)
     * params: String sf list
     * return :void
     *
     */

    function booking_report_for_new_service_center($sf_list) {
        $CI = get_instance();
        $CI->load->model('reporting_utils');
        $CI->load->model('vendor_model');
        $data = $CI->reporting_utils->get_booking_for_new_service_center($sf_list);
        $new_vendors = $CI->vendor_model->get_new_vendor($sf_list);

        //Generating HTML for the email
        $html = '
                   <html xmlns="http://www.w3.org/1999/xhtml">
                     <head>
                       <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                     </head>
                     <body><div style="margin-top: 30px;font-family:Helvetica;" class="container-fluid">
                         <table style="width: 95%;margin-bottom: 20px;border: 1px solid #ddd; border-collapse: collapse;">
                           <thead>
                             <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:14%;padding-top:8px;">STATE</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:14%;padding-top:8px;">CITY</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:14%;padding-top:8px;">NAME</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;">AGE</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;">TODAY BOOKINGS</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;">BOOKINGS MTD</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> BOOKINGS COMPLETED</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> BOOKINGS CANCELLED</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> 0-2 Days</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> 3-5 Days</th>
                               <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE;width:5%;padding-top:8px;"> >5 Days</th>

                             </tr>
                           </thead>
                           <tbody>';

        foreach ($new_vendors as $value) {
            if (!empty($data['yesterday_bookings_gone'][$value['id']])) {
                $yesterday = $data['yesterday_bookings_gone'][$value['id']]['booked'];
            } else {
                $yesterday = "";
            }
            if (!empty($data['booking_gone_mtd'][$value['id']])) {
                $month_completed = $data['booking_gone_mtd'][$value['id']]['completed'];
            } else {
                $month_completed = "";
            }
            if (!empty($data['bookings_cancelled'][$value['id']])) {
                $yesterday_cancelled = $data['bookings_cancelled'][$value['id']]['cancelled'];
            } else {
                $yesterday_cancelled = "";
            }
            if (!empty($data['bookings_completed'][$value['id']])) {
                $yesterday_completed = $data['bookings_completed'][$value['id']]['completed'];
            } else {
                $yesterday_completed = "";
            }
            if (!empty($data['pending_bookings_last_2_days'][$value['id']])) {
                $pending_bookings_last_2_days = $data['pending_bookings_last_2_days'][$value['id']]['pending'];
            } else {
                $pending_bookings_last_2_days = "";
            }
            if (!empty($data['pending_bookings_last_3_5_days'][$value['id']])) {
                $pending_bookings_last_3_5_days = $data['pending_bookings_last_3_5_days'][$value['id']]['pending'];
            } else {
                $pending_bookings_last_3_5_days = "";
            }
            if (!empty($data['pending_bookings_greater_than_5_days'][$value['id']])) {
                $pending_bookings_greater_than_5_days = $data['pending_bookings_greater_than_5_days'][$value['id']]['pending'];
            } else {
                $pending_bookings_greater_than_5_days = "";
            }
            
            $style = '';
            if ($value['active'] == 0) {
                $style = "background:#f25788";
            } else if ($value['on_off'] == 0) {
                $style = "background:#7986CB";
            }

            $html.="<tr  style=".$style.">" .
                    "<td style='text-align: center;border: 1px solid #001D48;padding:10px;'>" . $value['state'] .
                    "</td><td style='text-align: center;border: 1px solid #001D48;'>" . $value['district'] .
                    "</td><td style='text-align: center;border: 1px solid #001D48;font-size:90%;'>" . $value['name'] .
                    "</td><td style='text-align: center;border: 1px solid #001D48;font-size:90%;'>" . $value['age'] .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $month_completed .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_completed .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_cancelled .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $pending_bookings_last_2_days .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $pending_bookings_last_3_5_days .
                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $pending_bookings_greater_than_5_days .
                    " </td></tr>";
        }

        $html .= '</tbody>
                         </table>
                       </div>';
        $html .="<h2>Table Details:</h2><ul><li>"
                . "<b>Age:</b> No of Days from when vendor is created.</li>"
                . "<li><b>Today Bookings:</b> Bookings assigned to vendor Today.</li>"
                . "<li><b>Bookings MTD:</b>Total Bookings assigned to vendor this month. </li>"
                . "<li><b>Bookings Completed:</b> Bookings that are assigned to vendor and completed this month. </li>"
                . "<li><b>Bookings Cancelled:</b> Bookings that are assigned to vendor and cancelled this month. </li>"
                . "<li><b>0-2 Days:</b> Total count of Bookings that are Pending, Rescheduled between 0-2 Days. </li>"
                . "<li><b>3-5 Days:</b> Total count of Bookings that are Pending, Rescheduled bewteen 3-5 Days. </li>"
                . "<li><b> >5 Days:</b> Total count of Bookings that are Pending, Rescheduled greater than 5 Days. </li>"
                . "<li><b> <span style='width:20px;background:#f25788;border-radius:5px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>:</b> Permanent Deactivated SF's </li>"
                . "<li><b> <span style='width:20px;background:#7986CB;border-radius:5px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>:</b> Temporary Off SF's </li>"
                . "</ul>";
        $html .= '</body>
                   </html>';
        return $html;
    }

    /**
     * @Desc: This function is used to Send The Email When No Data found from partner_booking_status_mapping_table
     * @params: array()
     * @return: void
     * 
     */
    function send_mail_When_no_data_found($current_status,$internal_status,$booking_id,$partner_id){
        $to = ANUJ_EMAIL_ID;
        $cc = "";
        $bcc = "";
        $subject = " No Data found for '".$current_status."' and '".$internal_status."' in partner_booking_status_mapping Table";
        $message = "
                    <html>
                    <head></head>
                        <body>
                            <h3> No Data Found in partner_booking_status_mapping Table For Following Data</h3>
                            <p><b>Booking ID </b> '".$booking_id."'</p>
                            <p><b>Partner ID </b> '".$partner_id."' </p>
                            <p><b>Current Status</b> '".$current_status."'</p>
                            <p><b>Internal Status</b> '".$internal_status."'</p>
                                
                        </body>
                    </html>";
        $this->My_CI->notify->sendEmail("booking@247around.com", $to, $cc, $bcc, $subject, $message, "");
    }

}
