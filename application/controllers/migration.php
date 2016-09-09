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

    //migrates completed bookings
    function c_test1() {
	echo PHP_EOL;

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
		case '':
		    switch ($value['internal_status']) {
			case 'Completed TV Without Stand':
			case 'Completed Without Stand':
			case 'Completed With Demo':
			case 'Completed':
			    $data = array();
			    $data['appliance_id'] = $value['appliance_id'];
			    $data['partner_id'] = $value['partner_id'];
			    $data['service_id'] = $value['service_id'];
                            $data['appliance_description'] = $value['appliance_description'];
			    $data['price_tags'] = "Installation & Demo";

			    /* / echo PHP_EOL;
			      print_r($value['price_tags']);
			      echo PHP_EOL;
			      print_r($data);
			      echo PHP_EOL; */
			    $this->migration_model->update_booking_unit_details($booking_id, $data);
			    break;

			case 'Completed TV With Stand':
			case 'Completed With Stand':
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
			    echo 'internal_status not found: ' . $value['booking_id'] . PHP_EOL;
			    break;
		    }

		    break;

		case 'Repair,':
		case 'Repair':
		case 'Repair,InstallationwithStand,':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Repair";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Repair - In Warranty':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Repair - In Warranty";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Repair - Out Of Warranty':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Repair - Out Of Warranty";

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

		case 'WetService,':
		case 'WetService':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Wet Service";
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
		    echo PHP_EOL;
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

		case 'Installation & Demo':
		case 'Wall Mount Stand':
		    echo $value['booking_id'] . ", " . $value['price_tags'] . PHP_EOL;
		    break;

		default:
		    echo $value['booking_id'] . ", " . $value['price_tags'] . PHP_EOL;
		    break;
	    }
	}
    }

    function c_test2() {
	$c = 0;
	$booking_unit_details = $this->migration_model->c_get_all_booking_unit();
	foreach ($booking_unit_details as  $data) {

	    $booking = $this->migration_model->return_source($data['booking_id']);

	    $partner_id = $this->migration_model->get_price_mapping_partner_code($booking[0]['source']);

	    $prices = $this->migration_model->getPrices($data['service_id'], $data['appliance_category'], $data['appliance_capacity'], $partner_id, $data['price_tags']);

	    $state = $this->vendor_model->get_state_from_pincode($data['booking_pincode']);

	    if (empty($prices)) {
		echo $data['service_id'] . PHP_EOL .
		$data['appliance_category'] . PHP_EOL .
		$data['appliance_capacity'] . PHP_EOL .
		$partner_id . PHP_EOL .
		$data['price_tags'] . PHP_EOL .
		$data['booking_id'] . PHP_EOL;

		if (empty($state['state'])) {
		    echo "State not found: " . $data['booking_pincode'] . PHP_EOL;
		}

		echo PHP_EOL;
	    } else {
		$c++;
		$data['unit_id'] = $data['id'];
		unset($data['id']);
		$data['id'] = $prices[0]['id'];

		if (empty($state['state'])) {
		    echo "State not found: " . $data['booking_pincode'] . PHP_EOL;
		}

		unset($data['booking_pincode']);

		$this->migration_model->update_prices($data, $data['booking_id'], $state['state']);
		echo ".";
	    }
	}

	echo PHP_EOL;
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
	    echo PHP_EOL;
	    //print_r($data);
	    echo $value['booking_id'];
	    echo PHP_EOL;
	}

//	print_r($booking_details);
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
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Repair";

		    $this->migration_model->update_booking_unit_details($booking_id, $data);
		    break;

		case 'Repair - In Warranty':
		    $data = array();
		    $data['appliance_id'] = $value['appliance_id'];
		    $data['partner_id'] = $value['partner_id'];
		    $data['service_id'] = $value['service_id'];
		    $data['appliance_description'] = $value['appliance_description'];
		    $data['price_tags'] = "Repair - In Warranty";

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
		    echo PHP_EOL;
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
		    echo $value['booking_id'] . ", " . $value['price_tags'] . PHP_EOL;
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
		echo $data['service_id'] . PHP_EOL .
		$data['appliance_category'] . PHP_EOL .
		$data['appliance_capacity'] . PHP_EOL .
		$data['partner_id'] . PHP_EOL .
		$partner_id . PHP_EOL .
		$data['price_tags'] . PHP_EOL .
		$data['booking_id'] . PHP_EOL;

		echo PHP_EOL . PHP_EOL;

		if (empty($state['state'])) {
		    echo "Pincode not found: " . $data['booking_pincode'] . PHP_EOL;
		}

		echo PHP_EOL . PHP_EOL;
	    } else {
		$data['unit_id'] = $data['id'];

		unset($data['id']);

		$data['id'] = $prices[0]['id'];

		if (empty($state['state'])) {
		    echo "Pincode not found: " . $data['booking_pincode'] . PHP_EOL;
		}

		unset($data['booking_pincode']);

		$this->migration_model->update_prices($data, $data['booking_id'], $state['state']);
		echo ".";
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
		case 'Repair,InstallationwithStand,':
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
			    switch ($value['source']) {
				case 'SS':
				case 'SY':
				case 'SR':
				case 'SP':
				case 'SZ':
				    $this->update_for_installation($value, "Installation & Demo");
				    break;

				default:
				    $this->update_for_installation($value, "Repair");
				    break;
			    }
			    //echo $value['booking_id'] . ", Price Tag Empty<br/>";
			    break;
		    }

		    break;

		case 'Installation & Demo':
		case 'Wall Mount Stand':
		    echo $value['booking_id'] . PHP_EOL;
		    break;

		default:
		    echo $value['booking_id'] . ", Match not found" . PHP_EOL;
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

	if (empty($value['partner_id'])) {
	    echo $booking_id . "  .... Partner Id Not Exist.";
	    echo PHP_EOL;
	}

	if (empty($value['appliance_id'])) {
	    echo $booking_id . "  .... appliance_id Id Not Exist.";
	    echo PHP_EOL;
	}
		    echo '.';
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

	if (empty($value['partner_id'])) {
	    echo $booking_id . "  .... Partner Id Not Exist.";
	    echo PHP_EOL;
	}
	if (empty($value['appliance_id'])) {
	    echo $booking_id . "  .... appliance_id Id Not Exist.";
	    echo PHP_EOL;
	}

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
		    echo '.';
    }

    /**
     * Imp; FIrst fix capacity then excute this method
     */
    function q_test2() {
	$booking_unit_details = $this->migration_model->q_get_all_booking_unit();
	$this->update_prices_in_unit_details($booking_unit_details);
	//print_r($booking_unit_details);
    }

    //migrates cancelled queries and bookings
    function c_q_test1() {
	$data = $this->migration_model->get_all_cancelled_query();
	//print_r($data);
	foreach ($data as $value) {
	    switch ($value['price_tags']) {
		case 'Repair,':
		case 'Repair':
		    $this->update_for_installation($value, "Repair");
		    break;

		case 'Repair - In Warranty':
		    $this->update_for_installation($value, "Repair - In Warranty");
		    break;

		case 'Installation':
		case 'Installation,':
		case 'InstallationwithoutStand,':
		    $this->update_for_installation($value, "Installation & Demo");
		    break;

		case 'InstallationwithStand':
		case 'Installation with Stand':
		case 'InstallationwithStand,':
		case 'Installation with Stand,':
		    $this->update_for_installation_with_stand($value);
		    break;

		case 'GasRechargewithDryer,':
		    $this->update_for_installation($value, "Gas Recharge with Dryer");
		    break;

		case 'GasRecharge,':
		    $this->update_for_installation($value, "Gas Recharge");
		    break;

		case 'WetService,':
		case 'Wet Service':
		    echo $value['booking_id'] . 'WetService,' . PHP_EOL;
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

		case 'VisitCharge,':
		case 'Visit Charge':
		    $this->update_for_installation($value, "Visit");
		    break;

		case 'Service,':
		    echo $value['booking_id'] . 'Service' . PHP_EOL;
		    break;

		case 'Repair,InstallationwithStand,':
		    echo $value['booking_id'] . 'Repair,InstallationwithStand,' . PHP_EOL;
		break;

		case '':
//		    echo $value['booking_id'] . ", Price Tag is empty<br/>" . PHP_EOL;
		    switch ($value['source']) {
			case 'SS':
			case 'SY':
			case 'SR':
			case 'SP':
			case 'SZ':
			    $this->update_for_installation($value, "Installation & Demo");
			    break;

			default:
			    $this->update_for_installation($value, "Repair");
			    break;
		    }
		    break;

		case 'Installation & Demo':
		    echo $value['booking_id'] . $value['price_tags'] . PHP_EOL;
		    break;

		default:
		    echo $value['booking_id'] . $value['price_tags'] . PHP_EOL;
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
	    echo PHP_EOL;
	    print_r($value['booking_id']);
	    echo PHP_EOL;
	}

	//print_r($booking_details);
    }

    function update_prices_in_unit_details($booking_unit_details) {
	foreach ($booking_unit_details as $data) {

	    $partner_id = $this->migration_model->get_price_mapping_partner_code("", $data['partner_id']);

	    $prices = $this->migration_model->getPrices($data['service_id'], $data['appliance_category'], $data['appliance_capacity'], $partner_id, $data['price_tags']);

	    $state = $this->vendor_model->get_state_from_pincode($data['booking_pincode']);

	    if (empty($prices)) {
		echo $data['service_id'] . PHP_EOL .
		$data['appliance_category'] . PHP_EOL .
		$data['appliance_capacity'] . PHP_EOL .
		$data['partner_id'] . PHP_EOL .
		$partner_id . PHP_EOL .
		$data['price_tags'] . PHP_EOL .
		$data['booking_id'] . PHP_EOL;

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
