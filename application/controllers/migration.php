<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 36000);

class Migration extends CI_Controller {

    /**
     * load list model and helpers
     */
    function __Construct() {
	parent::__Construct();
	$this->load->model('migration_model');
	$this->load->model('vendor_model');
    }

    function c_test1() {

	$booking_details = $this->migration_model->c_get_all_booking_id();
	//print_r($booking_details);
	foreach ($booking_details as $value) {
	    $booking_id = $value['booking_id'];

	    switch ($value['price_tags']) {
		case 'Installation':
		case 'Installation,':
		case 'InstallationwithoutStand,' :
		case 'Installation,WallMountStand,':
		case 'Installation with Stand,':
		case 'InstallationwithStand,':

		    switch ($value['internal_status']) {
			case 'Completed TV Without Stand':
			case 'Completed With Demo':
			case 'Completed':
			    $data = array();
			    $data['appliance_id'] = $value['appliance_id'];
			    $data['partner_id'] = $value['partner_id'];
			    $data['service_id'] = $value['service_id'];
                            $data['appliance_description'] = $value['appliance_description'];
			    $data['price_tags'] = "Installation & Demo";

			    /* / echo "<br/>";
			      print_r($value['price_tags']);
			      echo "<br/>";
			      print_r($data);
			      echo "<br/>"; */
			    $this->migration_model->update_booking_unit_details($booking_id, $data);
			    break;

			case 'Completed TV With Stand':

			    $data = array();
			    $data['appliance_id'] = $value['appliance_id'];
			    $data['partner_id'] = $value['partner_id'];
			    $data['service_id'] = $value['service_id'];
                            $data['appliance_description'] = $value['appliance_description'];
			    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

			    $data['price_tags'] = "Installation & Demo";
			    $unit_id = $unit_details[0]['id'];


			    $this->migration_model->update_unit_details_by_id($unit_id, $data);

			    $data['booking_id'] = $value['booking_id'];
			    $data['appliance_brand'] = $value['appliance_brand'];
			    $data['appliance_category'] = $value['appliance_category'];
			    $data['appliance_capacity'] = $value['appliance_capacity'];
			    $data['model_number'] = $value['model_number'];

			    $data['appliance_tag'] = $value['appliance_tag'];
			    $data['purchase_year'] = $value['purchase_year'];
			    $data['purchase_month'] = $value['purchase_month'];
			    $data['price_tags'] = "Wall Mount Stand";

			    $this->migration_model->addunitdetails($data);
			    break;

			default:
			    echo $value['booking_id'];
			    break;
		    }

		    break;

		case 'Repair,':
		case 'Repair':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Repair";


		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Visit':
		case 'Visit,':
		case 'VisitCharge,':
		case 'Visit Charge':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Visit";


		    $this->migration_model->update_booking_unit_details($booking_id, $data);

		    break;
		case 'Repair,Installation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Repair";
		    $unit_id = $unit_details[0]['id'];

		    $this->migration_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Installation & Demo";

		    $this->migration_model->addunitdetails($data);
		    break;

		case 'GasRechargewithDryer,':

		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Gas Recharge with Dryer";


		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Installation,Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Installation & Demo";
		    $unit_id = $unit_details[0]['id'];
		    echo "<br/>";
		    $this->migration_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Uninstallation";

		    $this->migration_model->addunitdetails($data);
		    break;

		case 'GasRecharge,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Gas Recharge";
                    $data['appliance_description'] = $value['appliance_description'];

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;
		case 'Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['price_tags'] = "Uninstallation";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;
		case 'Installation,Repair,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Repair";
		    $unit_id = $unit_details[0]['id'];

		    $this->migration_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Installation & Demo";

		    $this->migration_model->addunitdetails($data);
		    break;

		default:
		    echo $value['booking_id'];
		    break;
	    }
	}
    }

    function c_test2() {
	$booking_unit_details = $this->migration_model->c_get_all_booking_unit();
	foreach ($booking_unit_details as  $data) {

	    $booking = $this->migration_model->return_source($data['booking_id']);

	    $partner_id = $this->migration_model->get_price_mapping_partner_code($booking[0]['source']);

	    $prices = $this->migration_model->getPrices($data['service_id'], $data['appliance_category'], $data['appliance_capacity'], $partner_id, $data['price_tags']);

	    $state = $this->vendor_model->get_state_from_pincode($data['booking_pincode']);

	    if (empty($prices)) {
		echo $data['service_id'] . "<br/>" .
		$data['appliance_category'] . "<br/>" .
		$data['appliance_capacity'] . "<br/>" .
		$partner_id . "<br/>" .
		$data['price_tags'] . "<br/>" .
		$data['booking_id'] . "<br/>";

		echo "<br/><br/>";
		print_r($state['state']);
		echo "<br/><br/>";
	    } else {
                $data['unit_id'] = $data['id'];
		unset($data['id']);
		$data['id'] = $prices[0]['id'];

		if (empty($state)) {
		    echo $data['booking_pincode'];
		}

		unset($data['booking_pincode']);

		$this->migration_model->update_prices($data, $data['booking_id'], $state['state']);
	    }
	}
    }

    function c_test3() {
	$booking_details = $this->migration_model->c_getbookingid();
	foreach ($booking_details as  $value) {

	    $data = array();
	    $data['customer_paid_basic_charges'] = $value['service_charge'];
	    $data['customer_paid_extra_charges'] = $value['additional_service_charge'];
	    $data['customer_paid_parts'] = $value['parts_cost'];
	    $data['booking_status'] = "Completed";

	    $this->migration_model->update_unit_price($value['booking_id'], $data);
	    echo "<br/>";
	    // print_r($data);
	    echo "<br/>";
	}

	//print_r($booking_details);
    }

    function p_test1() {
	echo PHP_EOL;

	$booking_details = $this->migration_model->p_get_all_booking_id();
	//print_r($booking_details);
	foreach ($booking_details as  $value) {
	    $booking_id = $value['booking_id'];

	    switch ($value['price_tags']) {
		case 'Installation':
		case 'Installation,':
		case 'InstallationwithoutStand,' :
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Installation & Demo";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Installation,WallMountStand,':
		case 'Installation with Stand,':
		case 'InstallationwithStand,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Installation & Demo";
		    $unit_id = $unit_details[0]['id'];

		    $this->migration_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];
		    $data['appliance_size'] = $value['appliance_size'];
		    $data['serial_number'] = $value['serial_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Wall Mount Stand";

		    $this->migration_model->addunitdetails($data);
		    break;

		case 'Repair,':
		case 'Repair':
		case 'Repair - In Warranty':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Repair";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Visit':
		case 'Visit,':
		case 'VisitCharge,':
		case 'Visit Charge':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Visit";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);

		    break;

		case 'Repair,Installation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Repair";
		    $unit_id = $unit_details[0]['id'];

		    $this->migration_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];
		    $data['appliance_size'] = $value['appliance_size'];
		    $data['serial_number'] = $value['serial_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Installation & Demo";

		    $this->migration_model->addunitdetails($data);
		    break;

		case 'GasRechargewithDryer,':

		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Gas Recharge with Dryer";


		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Installation,Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Installation & Demo";
		    $unit_id = $unit_details[0]['id'];
		    echo "<br/>";
		    $this->migration_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['model_number'] = $value['model_number'];
		    $data['appliance_size'] = $value['appliance_size'];
		    $data['serial_number'] = $value['serial_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];

		    $data['price_tags'] = "Uninstallation";

		    $this->migration_model->addunitdetails($data);
		    break;

		case 'GasRecharge,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Gas Recharge";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
                    $data['appliance_description'] = $value['appliance_description'];

		    $data['price_tags'] = "Uninstallation";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Installation,Repair,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
                    $data['appliance_description'] = $value['appliance_description'];

		    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Repair";
		    $unit_id = $unit_details[0]['id'];

		    $this->migration_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['model_number'] = $value['model_number'];
		    $data['appliance_size'] = $value['appliance_size'];
		    $data['serial_number'] = $value['serial_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];


		    $data['price_tags'] = "Installation & Demo";

		    $this->migration_model->addunitdetails($data);
		    break;

		case 'Installation & Demo':
		case 'Wall Mount Stand':
		    break;

		default:
		    echo $value['booking_id'] . ", " . $value['price_tags'] . PHP_EOL;
		    break;
	    }
	}

	echo PHP_EOL;
    }

    function p_test2() {
	echo PHP_EOL;

	$booking_unit_details = $this->migration_model->p_get_all_booking_unit();
	//print_r($booking_unit_details);

	foreach ($booking_unit_details as $data) {

	    $partner_id = $this->migration_model->get_price_mapping_partner_code("", $data['partner_id']);

	    $prices = $this->migration_model->getPrices($data['service_id'], $data['appliance_category'], $data['appliance_capacity'], $partner_id, $data['price_tags']);

	    $state = $this->vendor_model->get_state_from_pincode($data['booking_pincode']);


	    if (empty($prices)) {
		echo $data['service_id'] . "<br/>" .
		$data['appliance_category'] . "<br/>" .
		$data['appliance_capacity'] . "<br/>" .
		$data['partner_id'] . "<br/>" .
		$partner_id . "<br/>" .
		$data['price_tags'] . "<br/>" .
		$data['booking_id'] . "<br/>";

		echo "<br/><br/>";
		if (empty($state)) {
		    echo $data['booking_pincode'];
		}
		echo "<br/><br/>";
	    } else {
		$data['unit_id'] = $data['id'];

		unset($data['id']);
		$data['id'] = $prices[0]['id'];

		if (empty($state)) {
		    echo $data['booking_pincode'];
		}


		unset($data['booking_pincode']);

		$this->migration_model->update_prices($data, $data['booking_id'], $state['state']);
	    }
	}

	echo PHP_EOL;
    }

    /**
     * @imp: Note-- Some Partner id  or appliance id is not exist in the booking details,
     * so we need to fix these issuse then execute q_test1
     *
     */
    function q_test1() {
	echo PHP_EOL;

	$data = $this->migration_model->get_all_followUp();

	foreach ($data as $value) {
	    switch ($value['price_tags']) {
		case 'Installation':
		case 'InstallationwithoutStand':
		case 'Installation,':
		case 'InstallationwithoutStand,':

		    $this->update_for_installation($value, "Installation & Demo");
		    break;

		case 'InstallationwithStand,':
		case 'InstallationwithoutStand,InstallationwithStand,':

		    $this->update_for_installation_with_stand($value);
		    break;

		case 'Repair,':
		case 'Repair':
		    $this->update_for_installation($value, "Repair");
		    break;

		case 'VisitCharge,':
		case 'VisitCharge':
		    $this->update_for_installation($value, "Visit");
		    break;

		case '':
		    switch ($value['items_selected']) {
			case 'InstallationwithStand,':
			    $this->update_for_installation_with_stand($value);
			    break;

			case 'Installation,':
			case 'InstallationwithoutStand,':
			    $this->update_for_installation($value, "Installation & Demo");
			    break;

			default:
			    echo $value['booking_id'] . ", Price Tag Empty<br/>";
			    break;
		    }

		    break;

		case 'Installation & Demo':
		case 'Wall Mount Stand':
		    break;

		default:
		    echo $value['booking_id'] . ", Match not found<br/>";
		    break;
	    }
	}
	//print_r(count($data));
	echo PHP_EOL;
    }

    function update_for_installation($value, $price_tag) {
	$unit_data = array();
	$booking_id = $value['booking_id'];
	$unit_data['appliance_id'] = $value['appliance_id'];
	$unit_data['partner_id'] = $value['partner_id'];
	$unit_data['service_id'] = $value['service_id'];
	$unit_data['appliance_brand'] = $value['appliance_brand'];
	$unit_data['appliance_capacity'] = $value['appliance_capacity'];
	$unit_data['appliance_category'] = $value['appliance_category'];
        $unit_data['appliance_description'] = $value['appliance_description'];

	$unit_data['price_tags'] = $price_tag;

	$this->migration_model->update_booking_unit_details($booking_id, $unit_data);
	print_r($booking_id);
	if (!empty($value['partner_id'])) {
	    echo $booking_id . "  .... Partner Id Not Exist.";
	    echo "<br/>";
	}

	if (!empty($value['appliance_id'])) {
	    echo $booking_id . "  .... appliance_id Id Not Exist.";
	    echo "<br/>";
	}
    }

    function update_for_installation_with_stand($value) {
	$unit_data = array();
	$booking_id = $value['booking_id'];
	$unit_data['appliance_id'] = $value['appliance_id'];
	$unit_data['partner_id'] = $value['partner_id'];
	$unit_data['service_id'] = $value['service_id'];
	$unit_data['appliance_brand'] = $value['appliance_brand'];
	$unit_data['appliance_capacity'] = $value['appliance_capacity'];
	$unit_data['appliance_category'] = $value['appliance_category'];
        $unit_data['appliance_description'] = $value['appliance_description'];

	$unit_data['price_tags'] = "Installation & Demo";

	$this->migration_model->update_booking_unit_details($booking_id, $unit_data);

	if (!empty($value['partner_id'])) {
	    echo $booking_id . "  .... Partner Id Not Exist.";
	    echo "<br/>";
	}
	if (!empty($value['appliance_id'])) {
	    echo $booking_id . "  .... appliance_id Id Not Exist.";
	    echo "<br/>";
	}

	echo $booking_id . "............Stand";

	$unit_data['model_number'] = $value['model_number'];
	$unit_data['appliance_size'] = $value['appliance_size'];
	$unit_data['appliance_description'] = $value['appliance_description'];
	$unit_data['serial_number'] = $value['serial_number'];
	$unit_data['appliance_tag'] = $value['appliance_tag'];
	$unit_data['purchase_year'] = $value['purchase_year'];
	$unit_data['purchase_month'] = $value['purchase_month'];
	$unit_data['booking_id'] = $value['booking_id'];
	$unit_data['price_tags'] = "Wall Mount Stand";

	$this->migration_model->addunitdetails($unit_data);
    }

    /**
     * Imp; FIrst fix capacity then excute this method
     */
    function q_test2() {
	$booking_unit_details = $this->migration_model->q_get_all_booking_unit();
	$this->update_prices_in_unit_details($booking_unit_details);

	//print_r($booking_unit_details);
    }

    function c_q_test1() {
	$data = $this->migration_model->get_all_cancelled_query();
	//print_r($data);
	foreach ($data as $value) {
	    switch ($value['price_tags']) {
		case 'Repair,':
		case 'Repair':
		    $this->update_for_installation($value, "Repair");
		    break;

		case 'Installation,':
		case 'InstallationwithoutStand,':

		    $this->update_for_installation($value, "Installation & Demo");
		    break;
		case 'InstallationwithStand,':
		case 'Installation with Stand,':

		    $this->update_for_installation_with_stand($value);
		    echo $value['booking_id'] . "............With Stand<br/>";
		    break;

		case 'GasRechargewithDryer,':

		    $this->update_for_installation($value, "Gas Recharge with Dryer");
		    break;

		case 'GasRecharge,':

		    $this->update_for_installation($value, "Gas Recharge");
		    break;

		case 'WetService,':
		case 'Wet Service':
		    # code...
		    break;

		case 'Installation,Uninstallation,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
                    $data['appliance_description'] = $value['appliance_description'];
		    $unit_details = $this->migration_model->get_unit_details($value['booking_id']);

		    $data['price_tags'] = "Installation & Demo";
		    $unit_id = $unit_details[0]['id'];
		    echo "<br/>";
		    $this->migration_model->update_unit_details_by_id($unit_id, $data);

		    $data['booking_id'] = $value['booking_id'];
		    $data['appliance_brand'] = $value['appliance_brand'];
		    $data['appliance_category'] = $value['appliance_category'];
		    $data['appliance_capacity'] = $value['appliance_capacity'];
		    $data['model_number'] = $value['model_number'];

		    $data['appliance_tag'] = $value['appliance_tag'];
		    $data['purchase_year'] = $value['purchase_year'];
		    $data['purchase_month'] = $value['purchase_month'];
		    $data['price_tags'] = "Uninstallation";
		    break;

		case 'VisitCharge,':
		case 'Visit Charge':
		    $this->update_for_installation($value, "Visit");
		    break;

		case 'Service,':
		    # code...
		    break;

		case 'Repair,InstallationwithStand,':
		    # code...
		    break;

		case '':
		    echo $value['booking_id'] . ", Price Tag is empty<br/>";
		    break;

		default:
		    echo $value['booking_id'] . "<br/>";
		    break;
	    }
	}
    }

    function c_q_test2() {

	$booking_unit_details = $this->migration_model->c_q_get_booking_unit();
	$this->update_prices_in_unit_details($booking_unit_details);
    }

    function c_q_test3() {
	$booking_details = $this->migration_model->c_q_getbookingid();
	foreach ($booking_details as $value) {

	    $data = array();
	    $data['customer_paid_basic_charges'] = 0;
	    $data['customer_paid_extra_charges'] = 0;
	    $data['customer_paid_parts'] = 0;
	    $data['booking_status'] = "Cancelled";

	    $this->migration_model->update_unit_price($value['booking_id'], $data);
	    echo "<br/>";
	    print_r($value['booking_id']);
	    echo "<br/>";
	}

	//print_r($booking_details);
    }

    function update_prices_in_unit_details($booking_unit_details) {
	foreach ($booking_unit_details as $data) {

	    $partner_id = $this->migration_model->get_price_mapping_partner_code("", $data['partner_id']);

	    $prices = $this->migration_model->getPrices($data['service_id'], $data['appliance_category'], $data['appliance_capacity'], $partner_id, $data['price_tags']);

	    $state = $this->vendor_model->get_state_from_pincode($data['booking_pincode']);


	    if (empty($prices)) {
		echo $data['service_id'] . "<br/>" .
		$data['appliance_category'] . "<br/>" .
		$data['appliance_capacity'] . "<br/>" .
		$data['partner_id'] . "<br/>" .
		$partner_id . "<br/>" .
		$data['price_tags'] . "<br/>" .
		$data['booking_id'] . "<br/>";

		echo "<br/><br/>";
		if (empty($state)) {
		    echo $data['booking_pincode'];
		}
		echo "<br/><br/>";
	    } else {
		$data['unit_id'] = $data['id'];

		unset($data['id']);
		$data['id'] = $prices[0]['id'];

		if (empty($state)) {
		    echo $data['booking_pincode'];
		}


		unset($data['booking_pincode']);

		$this->migration_model->update_prices($data, $data['booking_id'], $state['state']);
	    }
	}
    }


    function update_service_center_inprocess(){
        $this->migration_model->get_service_center_inprocess();
    }

    function update_service_center_pending(){
        $this->migration_model->get_service_center_pending();
    }

    function update_service_center_completed_or_cancelled(){
        $this->migration_model->get_service_center_completed_or_cancelled();
    }

}
