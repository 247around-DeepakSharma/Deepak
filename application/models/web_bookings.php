<?php

class Web_bookings extends CI_Model {

    /**
     * @desc Load database
     */
    function __construct() {
        parent::__Construct();

        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    function get_charges() {
        $sql = "SELECT s.services, scc.category, scc.capacity, scc.service_category, scc.customer_total AS total_charges
                    FROM service_centre_charges scc, services s
                    WHERE s.id = scc.service_id
                    AND scc.active =  '1'
                    AND  `partner_id` =247001
                    AND product_or_services =  'Service'
                    AND service_category !=  'Repeat Booking'
                    ORDER BY s.services, scc.category, scc.capacity, scc.service_category";
	$query = $this->db->query($sql);

        return $query->result();
    }

}
