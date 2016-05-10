<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** PHPExcel_IOFactory */
//require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';

class service_centre_charges extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        $this->load->helper(array('form', 'url'));
        $this->load->helper('download');

        $this->load->library('form_validation');
        $this->load->library('s3');
        $this->load->library('PHPReport');

        $this->load->model('user_model');
        $this->load->model('booking_model');
        $this->load->model('service_centre_charges_model');

        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    public function index() {
        $this->load->view('employee/header');
        $this->load->view('employee/upload_service_centre_charges_excel');
    }

    /**
   *  @desc : This function is to add service centre charges from excel
   *  @param : void
   *  @return : all the charges added to view
   */
    public function add_service_centre_chrges_from_excel() {
        //$inputFileName = dirname(__FILE__) . "/bookings-2.xlsx";

        if (!empty($_FILES['file']['name'])) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);

            if ($pathinfo['extension'] == 'xlsx') {
                if ($_FILES['file']['size'] > 0) {

                    $inputFileName = $_FILES['file']['tmp_name'];
                }
            }
        }

        //echo $inputFileName, EOL;

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            /**  Advise the Reader that we only want to load cell data  * */
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->setActiveSheetIndexbyName('Sheet1');
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

//        echo "highest row: ", $highestRow, EOL;
//        echo "highest col: ", $highestColumn, EOL;
//        echo "highest col index: ", $highestColumnIndex, EOL;
        $sheet = $objPHPExcel->getSheet(0);
        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);

        $headings_new = array();
        foreach ($headings as $heading) {
            array_push($headings_new, str_replace(" ", "_", $heading));
        }

//        $booking = array();
        for ($row = 2, $i = 0; $row <= $highestRow; $row++, $i++) {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData[0] = array_combine($headings_new[0], $rowData[0]);

            
            //Insert service centre 
            $charges = $rowData[0];
            
            $lead_details['id'] = $this->service_centre_charges_model->insert_service_centre_charges($charges);

            //Make an array to store all the data, to display all the entered data in view
            $to_display[] = $rowData[0];
        }

        $data['booking'] = $to_display;

        $this->load->view('employee/header');
        $this->load->view('employee/service_centre_charges_summary', $data);
    }

    /**
   *  @desc : This function is to display service centre charges
   *  @param : void
   *  @return : all the services to view
   */
    public function display_service_centre_charges() {
        $services=$this->booking_model->selectservice();

        $this->load->view('employee/header');
        $this->load->view('employee/service_centre_price_list',array('services'=>$services));
    }

    /**
   *  @desc : This function called through ajax is to display service centre charges for particular service
   *  @param : service id
   *  @return : all the service centre charges for particular service added to view
   */
    public function display_charges_for_particular_appliance($service_id)
    {
        $result = $this->service_centre_charges_model->get_prices_for_particular_appliance($service_id);
        
        foreach ($result as $prices) 
        {
            echo "<tr><td width='10%;'>".$prices->category."</td>
                 <td width='10%;'>".$prices->capacity."</td>
                 <td width='15%;'>".$prices->service_category."</td>
                 <td width='5%;'>".$prices->total_charges."</td>
                 <td width='5%;'>".$prices->vendor_price."</td>
                 <td width='5%;'>".$prices->around_markup."</td>
                 <td width='5%;'>".$prices->service_charges."</td>
                 <td width='5%;'>".$prices->service_tax."</td>
                 </tr>";
        }
    }

}