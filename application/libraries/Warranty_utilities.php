<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of class Warranty_utilities 
 *
 * @author Prity Sharma
 */
class Warranty_utilities {

    var $My_CI;
    
    function __Construct() {
	$this->My_CI = & get_instance();

	$this->My_CI->load->model('warranty_model');
    }
    
    /**
     * @author Prity Sharma
     * @date 14-Aug-2019
     * @param type $arrBookings
     * @return type
     */
    function get_warranty_data($arrBookings){
        if(empty($arrBookings)){
            return array();
        }
        
        foreach ($arrBookings as $booking_id => $rec_data) {
            // Calculate Purchase Date
            // Used in case data is read from excel
            if (DateTime::createFromFormat('Y-m-d', $rec_data['purchase_date']) === FALSE) {
                $rec_data['purchase_date'] = (double) $rec_data['purchase_date'];
                $unix_date = ($rec_data['purchase_date'] - 25569) * 86400;
                $excel_date = (25569) + ($unix_date / 86400);
                $unix_date = ($excel_date - 25569) * 86400;
                $rec_data['purchase_date'] = date('Y-m-d', $unix_date);
            }
            $arrWhere["(appliance_model_details.model_number = '".$rec_data['model_number']."' and date(warranty_plans.period_start) <= '".$rec_data['purchase_date']."' and date(warranty_plans.period_end) >= '".$rec_data['purchase_date']."' and warranty_plans.partner_id = '".$rec_data['partner_id']."')"] = null; 
        }   
        $arrWarrantyData = $this->My_CI->warranty_model->get_warranty_data($arrWhere);
        return $arrWarrantyData;
    }
    
    /**
     * @author Prity Sharma
     * @date 14-Aug-2019 
     * @param type $arrWarrantyData
     * @return type
     */
    function get_model_wise_warranty_data($arrWarrantyData)
    {
        $arrModelWiseWarrantyData = [];
        foreach($arrWarrantyData as $recWarrantyData)
        {
            $arrModelWiseWarrantyData[$recWarrantyData['model_number']][] = $recWarrantyData;
        }
        return $arrModelWiseWarrantyData;
    }
    
    /**
     * This function
     * @author Prity Sharma
     * @date 14-Aug-2019
     * @param type $arrBooking
     * @param type $arrWarrantyData
     * @return type
     */
    function map_warranty_period_to_booking($arrBooking, $arrWarrantyData){
        $arrBooking['in_warranty_period'] = !empty($arrBooking['in_warranty_period']) ? $arrBooking['in_warranty_period'] : 12;
        $arrBooking['extended_warranty_period'] = !empty($arrBooking['extended_warranty_period']) ? $arrBooking['extended_warranty_period'] : 0;
        foreach($arrWarrantyData as $recWarrantyData)
        {
            if((strtotime($recWarrantyData['plan_start_date']) <= strtotime($arrBooking['purchase_date'])) && (strtotime($recWarrantyData['plan_end_date']) >= strtotime($arrBooking['purchase_date']))){
                if($recWarrantyData['in_warranty_period'] > $arrBooking['in_warranty_period'])
                {
                    $arrBooking['in_warranty_period'] = $recWarrantyData['in_warranty_period'];
                }
                if($recWarrantyData['extended_warranty_period'] > $arrBooking['extended_warranty_period'])
                {
                    $arrBooking['extended_warranty_period'] = $recWarrantyData['extended_warranty_period'];
                }
            }
        }
        return $arrBooking;
    }
    
    /**
     * This function maps warranty status against booking Ids
     * @author Prity Sharma
     * @date 14-Aug-2019
     * @param type $arrBookingsWarrantyData
     * @return type
     */
    function get_bookings_warranty_status($arrBookingsWarrantyData)
    {
        $arrBookingWiseWarrantyStatus = [];                 
        if(!empty($arrBookingsWarrantyData)){
            $arrBookingWiseWarrantyStatus = array_map(function($recWarrantyData) {
                $in_warranty_period = !empty($recWarrantyData['in_warranty_period']) ? $recWarrantyData['in_warranty_period'] : 12;
                $extended_warranty_period = !empty($recWarrantyData['extended_warranty_period']) ? $recWarrantyData['extended_warranty_period'] : 0;
                $warrantyStatus = $this->get_warranty_status($in_warranty_period, $extended_warranty_period, $recWarrantyData['purchase_date'], $recWarrantyData['booking_create_date']);
                return $warrantyStatus;
            }, $arrBookingsWarrantyData);
        }  
        return $arrBookingWiseWarrantyStatus;
    }
    
    /**
     * This function returns Warranty Status(IW,EW,OW)
     * on the basis of warranty period, purchase date and date on which status needs to be calculated.
     * @author Prity Sharma
     * @date 14-Aug-2019
     * @param type $in_warranty_period
     * @param type $extended_warranty_period
     * @param type $purchase_date
     * @param type $create_date
     * @return string
     */
    public function get_warranty_status($in_warranty_period, $extended_warranty_period, $purchase_date, $create_date)
    {
        $warrantyStatus = 'OW';
        $in_warranty_months = !empty($in_warranty_period) ? $in_warranty_period : 12;
        $extended_warranty_months = !empty($extended_warranty_period) ? $extended_warranty_period : 0;                

        $total_warranty_months = $extended_warranty_months + $in_warranty_months;

        // Calculate In-Warranty End Period
        $in_warranty_end_period = strtotime(date("Y-m-d", strtotime($purchase_date)) . " +" . $in_warranty_months . " months");
        $in_warranty_end_period = strtotime(date("Y-m-d", $in_warranty_end_period) . " -1 day");

        // Calculate Extended-Warranty End Period
        $warranty_end_period = strtotime(date("Y-m-d", strtotime($purchase_date)) . " +" . $total_warranty_months . " months");
        $warranty_end_period = strtotime(date("Y-m-d", $warranty_end_period) . " -1 day");

        // Calculate Warranty Status
        if (strtotime($create_date) <= $in_warranty_end_period) :
            $warrantyStatus = 'IW';      
        elseif (strtotime($create_date) <= $warranty_end_period) :
            $warrantyStatus = 'EW';      
        endif; 
        return $warrantyStatus;
    }
}