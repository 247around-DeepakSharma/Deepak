<?php

//error_reporting(E_ERROR);
//ini_set('display_errors', '0');

/**
 * Model class to offer discounts
 *
 * @author anujaggarwal
 */

class discount extends CI_Model {

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();

        $this->db_location = $this->load->database('default1', TRUE, TRUE);
        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    function validate_coupon($coupon_code, $service) {
        log_message('info', "Entering: " . __METHOD__);
        log_message('info', "Coupon: " . $coupon_code . ", Service: " . $service);

        $discount_value = "0";
        $discount_type = "";
        $coupon_result_text = "Coupon code invalid";
        $coupon_result_val = "0";

        $sql = "SELECT * FROM discount_coupons WHERE `coupon_code` LIKE '$coupon_code'";
        $query = $this->db->query($sql);

        log_message('info', "SQL: " . $this->db->last_query());

        $results = $query->result_array();

        foreach ($results as $result) {
            //if (($result['valid_services'] == 'All') &&
            $date1 = new DateTime();
            $date2 = new DateTime($result['coupon_expiry_date']);

            $time_interval = date_diff($date1, $date2);
            $diff = (int) $time_interval->format("%r%a");

            if ($diff >= 0) {
                log_message('info', "Coupon not expired");

                if ($result['valid_services'] == "all" || ($result['valid_services'] == $service)) {
                    log_message('info', "Coupon code is valid");

                    $discount_type = $result['type'];
                    $discount_value = $result['value'];
                    $coupon_result_text = "Coupon code applied";
                    $coupon_result_val = "1";
                } else {
                    log_message('info', "Coupon code is for another appliance");
                    $coupon_result_text = "Coupon code is for other appliance";
                }
            } else {
                log_message('info', "Coupon code expired");
                $coupon_result_text = "Coupon code expired";
            }
        }

        $arr = array(
            "result_text" => $coupon_result_text,
            "result" => $coupon_result_val,
            "discount_type" => $discount_type,
            "discount_value" => $discount_value
        );

        log_message('info', print_r($arr, true));

        return $arr;
    }

}
