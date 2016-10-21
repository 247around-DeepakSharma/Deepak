<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
        foreach ($array as $key => $val) {
            if ($val->current_status === "Pending" || $val->current_status === "Rescheduled") {
                $data['Pending'] = 1;
            } else if ($val->current_status === "Completed") {
                $data['Completed'] = 1;
            } else if ($val->current_status === "Cancelled") {
                $data['Cancelled'] = 1;
            } else if ($val->current_status === "FollowUp") {
                $data['FollowUp'] = 1;
            }
        }
        return $data;
    }

}