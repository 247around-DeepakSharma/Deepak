<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @desc: This file is used to store Custom Functions used in the Project
 */
/**
 * @desc: This function is used to search for key in array as Pending, Completed, Followup
 * 
 * @param type $array
 * @return int
 * 
 */
if (!function_exists('search_for_key')) {

    function search_for_key($array) {
        $data = array();
        $no = 1;
        foreach ($array as $val) {
            if ($val->current_status === "Pending" || $val->current_status === "Rescheduled") {
                $data['Pending'] = 1;
            } else if ($val->current_status === "Completed") {
                $data['Completed'] = 1;
            } else if ($val->current_status === "Cancelled") {
                $data['Cancelled'] = 1;
            } else if ($val->current_status === "FollowUp") {
                $data['FollowUp'] = 1;
                $data['FollowUp_count'] = $no;
                $no++;
            }
        }
        return $data;
    }

}

function convert_number_to_words($number) {
    $no = (int) floor($number);
        $point = (int) round(($number - $no) * 100);
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'One', '2' => 'Two',
            '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
            '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
            '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
            '13' => 'Thirteen', '14' => 'Fourteen',
            '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
            '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
            '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
            '60' => 'Sixty', '70' => 'Seventy',
            '80' => 'Eighty', '90' => 'Ninety');
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;


            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] .
                        " " . $digits[$counter] . $plural . " " . $hundred :
                        $words[floor($number / 10) * 10]
                        . " " . $words[$number % 10] . " "
                        . $digits[$counter] . $plural . " " . $hundred;
            } else
                $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);


        if ($point > 20) {
            $points = ($point) ?
                    "" . $words[floor($point / 10) * 10] . " " .
                    $words[$point = $point % 10] : '';
        } else {
            $points = $words[$point];
        }
        if ($points != '') {
            return $result . "Rupees  " . $points . " Paise Only";
        } else {

            return $result . "Rupees Only";
        }
}

//function convert_number_to_words($number) {
//
//    $no = round($number);
//    $point = round($number - $no, 2) * 100;
//    $hundred = null;
//    $digits_1 = strlen($no);
//    $i = 0;
//    $str = array();
//    $words = array('0' => '',
//        '1' => 'One',
//        '2' => 'Two',
//        '3' => 'Three',
//        '4' => 'Four',
//        '5' => 'Five',
//        '6' => 'Six',
//        '7' => 'Seven',
//        '8' => 'Eight',
//        '9' => 'Nine',
//        '10' => 'Ten',
//        '11' => 'Eleven',
//        '12' => 'Twelve',
//        '13' => 'Thirteen',
//        '14' => 'Fourteen',
//        '15' => 'Fifteen',
//        '16' => 'Sixteen',
//        '17' => 'Seventeen',
//        '18' => 'Eighteen',
//        '19' => 'Nineteen',
//        '20' => 'Twenty',
//        '30' => 'Thirty',
//        '40' => 'Forty',
//        '50' => 'Fifty',
//        '60' => 'Sixty',
//        '70' => 'Seventy',
//        '80' => 'Eighty',
//        '90' => 'Ninety');
//    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
//    while ($i < $digits_1) {
//        $divider = ($i == 2) ? 10 : 100;
//        $number = floor($no % $divider);
//        $no = floor($no / $divider);
//        $i += ($divider == 10) ? 1 : 2;
//        if ($number) {
//            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
//            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
//            $str [] = ($number < 21) ? $words[$number] .
//                    " " . $digits[$counter] . $plural . " " . $hundred :
//                    $words[floor($number / 10) * 10]
//                    . " " . $words[$number % 10] . " "
//                    . $digits[$counter] . $plural . " " . $hundred;
//        } else
//            $str[] = null;
//    }
//    $str = array_reverse($str);
//    $result = implode('', $str);
//    $points = ($point) ?
//            "." . $words[$point / 10] . " " .
//            $words[$point = $point % 10] : '';
//    return $result . "Rupees Only ";
//}

function execute_paramaterised_query($query, $params_array) {
    $CI = & get_instance();
    $response = $CI->db->query($query, $params_array);
    return $results = $response->result_array();
}
/*
 * Validate SF login if SF has authorization certificate or not
 */
function validate_sf_auth_certificate($has_certificate, $auth_certificate_file, $year) {
    $financial_year = '';
    $current_month = date('m');
    if ($current_month > 3) {
        $financial_year = date('Y') . '-' . (date('Y') + 1);
    } else {
        $financial_year = (date('Y') - 1) . '-' . date('Y');
    }
    if ($has_certificate == 1 && $auth_certificate_file != NULL && $financial_year == $year) {
        return TRUE;
    }
    return FALSE;
}
