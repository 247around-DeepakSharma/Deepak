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
        $this->My_CI->load->library('booking_utilities');
    }
    
    /**
     * @author Prity Sharma
     * @date 14-Aug-2019
     * @param type $arrBookings
     * @return type
     */
    function get_warranty_data($arrBookings, $is_excel = false){
        if(empty($arrBookings)){
            return array();
        }
        
        foreach ($arrBookings as $booking_id => $rec_data) {
            // Calculate Purchase Date
            // Used in case data is read from excel   
            $purchase_date = date('Y-m-d', strtotime($rec_data['purchase_date']));   
            if ($is_excel && $rec_data['purchase_date'] != "0000-00-00" && DateTime::createFromFormat('d-m-Y', $rec_data['purchase_date']) === FALSE && is_numeric($rec_data['purchase_date'])) {
                $purchase_date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($rec_data['purchase_date']));
            }
            
            
            // Get All Valid Plans against Model, lies within Purchase Date of Product
            // get Product specific plans (for E-commerce Partners)
            if(!empty($rec_data['service_id'])){
                // Get Model Specific Plans
                if(!empty($rec_data['model_number'])){
                    //removes the single as well as double quotes from model name
                    $model_number = str_replace('"', '', str_replace("'", "", $rec_data['model_number']));
                    $arrOrWhere["((appliance_model_details.model_number = '".$model_number."' OR (warranty_plans.service_id = '".$rec_data['service_id']."' AND warranty_plans.plan_depends_on = ". WARRANTY_PLAN_ON_PRODUCT .")) and date(warranty_plans.period_start) <= '".$purchase_date."' and date(warranty_plans.period_end) >= '".$purchase_date."' and warranty_plans.partner_id = '".$rec_data['partner_id']."')"] = null; 
                }
                else{
                    $arrOrWhere["((warranty_plans.service_id = '".$rec_data['service_id']."' AND warranty_plans.plan_depends_on = ". WARRANTY_PLAN_ON_PRODUCT .") and date(warranty_plans.period_start) <= '".$purchase_date."' and date(warranty_plans.period_end) >= '".$purchase_date."' AND warranty_plans.partner_id = '".$rec_data['partner_id']."')"] = null;                 
                }
            } 
            else {
                // for Bulk checker, when we don't have service Id
                //removes the single as well as double quotes from start and end
                $model_number = str_replace('"', '', str_replace("'", "", $rec_data['model_number']));
                $arrOrWhere["(appliance_model_details.model_number = '".$model_number."' and date(warranty_plans.period_start) <= '".$purchase_date."' and date(warranty_plans.period_end) >= '".$purchase_date."' and warranty_plans.partner_id = '".$rec_data['partner_id']."')"] = null; 
            }            
        }  
                
        $arrWarrantyData = $this->My_CI->warranty_model->get_warranty_data($arrOrWhere);        
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
        $arrBooking['in_warranty_period'] = !empty($arrBooking['in_warranty_period']) ? $arrBooking['in_warranty_period'] : 0;
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
        // If no In-warranty Plan found, set default In-warranty to 12 Months
        $arrBooking['in_warranty_period'] = !empty($arrBooking['in_warranty_period']) ? $arrBooking['in_warranty_period'] : 12;
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
                $warranty_found = !empty($recWarrantyData['in_warranty_period']) ? true : false;
                $in_warranty_period = !empty($recWarrantyData['in_warranty_period']) ? $recWarrantyData['in_warranty_period'] : 12;
                $extended_warranty_period = !empty($recWarrantyData['extended_warranty_period']) ? $recWarrantyData['extended_warranty_period'] : 0;
                $warrantyStatus = $this->get_warranty_status($in_warranty_period, $extended_warranty_period, $recWarrantyData['purchase_date'], $recWarrantyData['booking_create_date'], $warranty_found);
                if($recWarrantyData['purchase_date'] == '1970-01-01' || empty($recWarrantyData['purchase_date'])):
                    return "DOP Not Valid";
                elseif($recWarrantyData['booking_create_date'] == '1970-01-01'):
                    return "Booking Create Date Not Valid";
                elseif(empty($recWarrantyData['service_id'])):
                    return "Product Not Valid";
                elseif(empty($recWarrantyData['partner_id'])):
                    return "Partner Not Valid";
                elseif(empty($recWarrantyData['booking_id'])):
                    return "Booking Id Not Valid";
                else:
                    return $warrantyStatus;
                endif;                
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
    public function get_warranty_status($in_warranty_period, $extended_warranty_period, $purchase_date, $create_date, $warranty_found = true)
    {
        $create_date = date('Y-m-d', strtotime($create_date));
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
        elseif(!$warranty_found):
            $warrantyStatus = 'No Data Found'; 
        endif; 
        return $warrantyStatus;
    }
    
    /**
     * this function is used to get the warranty status of booking
     * @author Prity Sharma
     * @date 20-08-2019
     * @return JSON
     */
    public function get_warranty_status_of_bookings($arrBookings, $checkInstallationDate = 0){  
        // Check if warranty is to be calculated on the basis of DOI od DOP
        // If warranty is to calculated on the basis of DOI, replace DOP with DOI
        $partner_id = $arrBookings[0]['partner_id'];
        $checkInstallationDate = $this->My_CI->partner_model->getpartner($partner_id)[0]['check_warranty_from'];
        if($checkInstallationDate == WARRANTY_ON_DOI){
            $arrInstallationData = $this->My_CI->booking_utilities->get_installation_date_of_booking($arrBookings);
            if(!empty($arrInstallationData['installation_date'])){
                $arrBookings[0]['purchase_date'] = date("d-m-Y", strtotime($arrInstallationData['installation_date']));
            } 
        }
        $arrWarrantyData = $this->get_warranty_data($arrBookings);  
        $arrModelWiseWarrantyData = $this->get_model_wise_warranty_data($arrWarrantyData);         
        foreach($arrBookings as $key => $arrBooking)
        {            
            $model_number = (!empty($arrBooking['model_number']) ? trim($arrBooking['model_number']) : "");
            if(!empty($model_number) && !empty($arrModelWiseWarrantyData[$model_number]))
            {   
                $arrBookings[$key] = $this->map_warranty_period_to_booking($arrBooking, $arrModelWiseWarrantyData[$model_number]);
            }
            if (!empty($arrBooking['service_id']) && !empty($arrModelWiseWarrantyData['ALL'.$arrBooking['service_id']])) {
                $arrBookings[$key] = $this->map_warranty_period_to_booking($arrBooking, $arrModelWiseWarrantyData['ALL'.$arrBooking['service_id']]);
            }
            $arrBookings[$arrBooking['booking_id']] = $arrBookings[$key];
            unset($arrBookings[$key]);
        }
        $arrBookingsWarrantyStatus = $this->get_bookings_warranty_status($arrBookings);  
        if(!empty($checkInstallationDate) && !empty($arrInstallationData['installation_date'])){
            $arrBookingsWarrantyStatus['installation_date'] = $arrInstallationData['installation_date'];
            $arrBookingsWarrantyStatus['installation_booking'] = $arrInstallationData['installation_booking'];
        }
        return $arrBookingsWarrantyStatus;
    }

    function get_warranty_specific_data_of_bookings($arrBookingIds){
        $arrWarrantySpecificData = $this->My_CI->warranty_model->get_warranty_specific_data_of_bookings($arrBookingIds);
        return $arrWarrantySpecificData;
    }
    
    function match_warranty_status_with_request_type($arrBookings, $arrBookingsWarrantyStatus){
        $arrReturn = [];
        $selected_booking_request_types = $arrBookings[0]['booking_request_types'];
        if(empty($selected_booking_request_types))
        {
            return json_encode($arrReturn);
        }
        $booking_request_type = $this->My_CI->booking_utilities->get_booking_request_type($selected_booking_request_types); 
        $booking_id = $arrBookings[0]['booking_id'];
        $arr_warranty_status = [
            'IW' => ['In Warranty', 'Presale Repair', 'AMC', 'Repeat', 'Installation', 'PDI', 'Demo', 'Tech Visit', 'Replacement', 'Spare Cannibalization', 'Free Remote Assistance', 'Handling Charges'],
            'OW' => ['Out Of Warranty', 'Out Warranty', 'AMC', 'Repeat', 'PDI', 'Tech Visit', 'Spare Cannibalization', 'Free Remote Assistance', 'Handling Charges'],
            'EW' => ['Extended', 'AMC', 'Repeat', 'PDI', 'Tech Visit', 'Spare Cannibalization', 'Free Remote Assistance', 'Handling Charges']
        ];
        $arr_warranty_status_full_names = ['IW' => 'In Warranty', 'OW' => 'Out Of Warranty', 'EW' => 'Extended Warranty'];
        $warranty_checker_status = $arrBookingsWarrantyStatus[$booking_id];
        // If no data found against warranty, consider booking as of Out Warranty
        if($warranty_checker_status != 'IW' && $warranty_checker_status != 'EW'):
            $warranty_checker_status = "OW";
        endif;
        $warranty_mismatch = 0;
        $returnMessage = "";

        if(!empty($arr_warranty_status[$warranty_checker_status]))
        {
            $warranty_mismatch = 1;
            foreach($arr_warranty_status[$warranty_checker_status] as $request_types)
            {
                if(strpos(strtoupper(str_replace(" ","",$booking_request_type)), strtoupper(str_replace(" ","",$request_types))) !== false)
                {
                    $warranty_mismatch = 0;
                    break;
                }
            }
        }

        if(!empty($warranty_mismatch))
        {
            if((strpos(strtoupper(str_replace(" ","",$booking_request_type)), 'OUTOFWARRANTY') !== false))
            {
                // we can create a Booking in OW even if its status is IW (only if any spare is not requested in IW)
                $IW_spare = $this->My_CI->booking_utilities->is_spare_requested_in_IW($booking_id);
                if(!$IW_spare)
                {
                    $warranty_mismatch = 0;
                    $returnMessage = "Booking Warranty Status (".$arr_warranty_status_full_names[$warranty_checker_status].") is not matching with current request type (".$booking_request_type.") of Booking, but if needed you may proceed with current request type.";
                }
                else {
                    $returnMessage = "Booking Warranty Status (".$arr_warranty_status_full_names[$warranty_checker_status].") is not matching with current request type (".$booking_request_type.") of Booking, Spare is also Requested in IW. ";
                }
            }
            else
            { 
                $returnMessage = "Booking Warranty Status (".$arr_warranty_status_full_names[$warranty_checker_status].") is not matching with current request type (".$booking_request_type."), to request part please change request type of the Booking.";
            }   
        }
        if(!empty($arrBookingsWarrantyStatus['installation_date']) && !empty($arrBookingsWarrantyStatus['installation_booking'])){
            $returnMessage .= " Product Installation Date : ".date('d-M-Y', strtotime($arrBookingsWarrantyStatus['installation_date'])).",  Booking : ".$arrBookingsWarrantyStatus['installation_booking'];
            $arrReturn['installation_date'] = $arrBookingsWarrantyStatus['installation_date'];
            $arrReturn['installation_booking'] = $arrBookingsWarrantyStatus['installation_booking'];
        }
        $arrReturn['status'] = $warranty_mismatch;
        $arrReturn['message'] = $returnMessage;
        return json_encode($arrReturn);
    }
}
