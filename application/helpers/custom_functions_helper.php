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


    /*
     * @desc: This function is used to create acc to Service center, state and city
     * 
     * params: void
     * return :void
     * 
     */
if(!function_exists('booking_report_by_service_center')){

    function booking_report_by_service_center() {
        
        $CI = get_instance();
        $CI->load->model('reporting_utils');
        $data = $CI->reporting_utils->get_booking_by_service_center();
        //Generating HTML for the email
        $html = '
                    <html xmlns="http://www.w3.org/1999/xhtml">
                      <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                      </head>
                      <body><div style="margin-top: 30px;font-family:Helvetica;" class="container-fluid">
                          <table style="width: 90%;margin-bottom: 20px;border: 1px solid #ddd; border-collapse: collapse;">
                            <thead>
                              <tr style="padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd">
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">State</th>
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE"></th>
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Yesterday Booked</th>
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Yesterday Completed</th>
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">Yesterday Cancelled</th>
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">' . date('M') . ' Booking Completed</th>
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">' . date('M') . ' Booking Cancelled</th>
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE">3-5 Days</th>
                                <th style="text-align: center;border: 1px solid #ddd;background:#EEEEEE"> > 5 Days</th>

                              </tr>
                            </thead>
                            <tbody >';
        foreach ($data['service_center_id'] as $key => $val) {

            //Setting State and City value
            if (isset($data['data'][$val]['yesterday_booked']['state'])) {
                $state = $data['data'][$val]['yesterday_booked']['state'];
                $city = $data['data'][$val]['yesterday_booked']['city'];
                $service_center_name = $data['data'][$val]['yesterday_booked']['service_center_name'];
            }
            if (isset($data['data'][$val]['yesterday_completed']['state'])) {
                $state = $data['data'][$val]['yesterday_completed']['state'];
                $city = $data['data'][$val]['yesterday_completed']['city'];
                $service_center_name = $data['data'][$val]['yesterday_completed']['service_center_name'];
            }
            if (isset($data['data'][$val]['yesterday_cancelled']['state'])) {
                $state = $data['data'][$val]['yesterday_cancelled']['state'];
                $city = $data['data'][$val]['yesterday_cancelled']['city'];
                $service_center_name = $data['data'][$val]['yesterday_cancelled']['service_center_name'];
            }
            if (isset($data['data'][$val]['month_completed']['state'])) {
                $state = $data['data'][$val]['month_completed']['state'];
                $city = $data['data'][$val]['month_completed']['city'];
                $service_center_name = $data['data'][$val]['month_completed']['service_center_name'];
            }
            if (isset($data['data'][$val]['month_cancelled']['state'])) {
                $state = $data['data'][$val]['month_cancelled']['state'];
                $city = $data['data'][$val]['month_cancelled']['city'];
                $service_center_name = $data['data'][$val]['month_cancelled']['service_center_name'];
            }

            if (isset($data['data'][$val]['last_3_day']['state'])) {
                $state = $data['data'][$val]['last_3_day']['state'];
                $city = $data['data'][$val]['last_3_day']['city'];
                $service_center_name = $data['data'][$val]['last_3_day']['service_center_name'];
            }
            if (isset($data['data'][$val]['greater_than_5_days']['state'])) {
                $state = $data['data'][$val]['greater_than_5_days']['state'];
                $city = $data['data'][$val]['greater_than_5_days']['city'];
                $service_center_name = $data['data'][$val]['greater_than_5_days']['service_center_name'];
            }

            $state_final[] = $state;
            $way_final['state'] = $state;
            $way_final['city'] = $city;
            $way_final['service_center_name'] = $service_center_name;
            $way_final['yesterday_booked'] = (isset($data['data'][$val]['yesterday_booked']['booked']) ? $data['data'][$val]['yesterday_booked']['booked'] : '  ');
            $way_final['yesterday_completed'] = (isset($data['data'][$val]['yesterday_completed']['completed']) ? $data['data'][$val]['yesterday_completed']['completed'] : ' ');
            $way_final['yesterday_cancelled'] = (isset($data['data'][$val]['yesterday_cancelled']['cancelled']) ? $data['data'][$val]['yesterday_cancelled']['cancelled'] : '  ');
            $way_final['month_completed'] = (isset($data['data'][$val]['month_completed']['completed']) ? $data['data'][$val]['month_completed']['completed'] : '  ');
            $way_final['month_cancelled'] = (isset($data['data'][$val]['month_cancelled']['cancelled']) ? $data['data'][$val]['month_cancelled']['cancelled'] : '  ');
            $way_final['last_3_day'] = (isset($data['data'][$val]['last_3_day']['booked']) ? $data['data'][$val]['last_3_day']['booked'] : '  ');
            $way_final['greater_than_5_days'] = (isset($data['data'][$val]['greater_than_5_days']['booked']) ? $data['data'][$val]['greater_than_5_days']['booked'] : '  ');

            $final_way[] = $way_final;
        }

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
        $last_3_day = 0;
        $overall_last_3_day = 0;
        $state_final = array_unique($state_final);
        foreach ($state_final as $val) {

            foreach ($final_way as $key => $value) {

                if ($value['state'] == $val) {

                    $show_state[$key] = (in_array($val, $show_state)) ? '' : $val;

                    if ($show_state[$key] != '') {
                        if ($key >= 1) {
                            $html.="<tr>" .
                                    "<td style='text-align: center;border: 1px solid #001D48;'>" . '' .
                                    "</td><td style='text-align: center;border: 1px solid #001D48;font-size:80%;'>" . '' .
                                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_booked .
                                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_completed .
                                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_cancelled .
                                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $month_completed .
                                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $month_cancelled .
                                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $last_3_day .
                                    " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $greater_than_5_days .
                                    " </td></tr>";
                            $yesterday_booked = 0;
                            $yesterday_completed = 0;
                            $yesterday_cancelled = 0;
                            $month_completed = 0;
                            $month_cancelled = 0;
                            $last_3_day = 0;
                            $greater_than_5_days = 0;
                        }
                        $html.= "<tr style='padding: 8px;line-height: 1.42857143;vertical-align: top; border-top: 1px solid #ddd;border: 1px solid #ddd;'>"
                                . "<td colspan='2'><span style='color:#FF9900;'>" .
                                $value['state'] . "</span></td></tr>";
                    }

                    $html.="<tr>" .
                            "<td style='text-align: center;border: 1px solid #001D48;'>" . $value['city'] .
                            "</td><td style='text-align: center;border: 1px solid #001D48;font-size:80%;'>" . $value['service_center_name'] .
                            " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1'>" . $value['yesterday_booked'] .
                            " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1'>" . $value['yesterday_completed'] .
                            " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1'>" . $value['yesterday_cancelled'] .
                            " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1'>" . $value['month_completed'] .
                            " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1'>" . $value['month_cancelled'] .
                            " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1'>" . $value['last_3_day'] .
                            " </td><td style='text-align: center;border: 1px solid #001D48;background:#E5E0D1'>" . $value['greater_than_5_days'] .
                            " </td></tr>";

                    $yesterday_booked += $value['yesterday_booked'];
                    $overall_yesterday_booked += $value['yesterday_booked'];
                    $yesterday_completed += $value['yesterday_completed'];
                    $overall_yesterday_completed += $value['yesterday_completed'];
                    $yesterday_cancelled += $value['yesterday_booked'];
                    $overall_yesterday_cancelled += $value['yesterday_booked'];
                    $month_completed += $value['month_completed'];
                    $overall_month_completed += $value['month_completed'];
                    $month_cancelled += $value['month_cancelled'];
                    $overall_month_cancelled += $value['month_cancelled'];
                    $last_3_day += $value['last_3_day'];
                    $overall_last_3_day += $value['last_3_day'];
                    $greater_than_5_days += $value['greater_than_5_days'];
                    $overall_greater_than_5_days += $value['greater_than_5_days'];
                }
            }
        }
        $html.="<tr>" .
                "<td style='text-align: center;border: 1px solid #001D48;'>" . '' .
                "</td><td style='text-align: center;border: 1px solid #001D48;font-size:80%;'>" . '' .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_booked .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_completed .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $yesterday_cancelled .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $month_completed .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $month_cancelled .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $last_3_day .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#D3DCE3'>" . $greater_than_5_days .
                " </td></tr>";

        $html.="<tr><td>&nbsp;</td></tr>";
        $html.="<tr>" .
                "<td style='text-align: center;border: 1px solid #001D48;'>" . '' .
                "</td><td style='text-align: center;border: 1px solid #001D48;font-size:80%;background:#FF9900'>" . 'TOTAL' .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900'><strong>" . $overall_yesterday_booked . '<strong>' .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900'><strong>" . $overall_yesterday_completed . '<strong>' .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900'><strong>" . $overall_yesterday_cancelled . '<strong>' .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900'><strong>" . $overall_month_completed . '<strong>' .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900'><strong>" . $overall_month_cancelled . '<strong>' .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900'><strong>" . $overall_last_3_day . '<strong>' .
                " </td><td style='text-align: center;border: 1px solid #001D48;background:#FF9900'><strong>" . $overall_greater_than_5_days . '<strong>' .
                " </td></tr>";

        $html .= '</tbody>
                          </table>
                        </div>';
        $html .= '</body>
                    </html>';
        
        return $html;

    }
}
