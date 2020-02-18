<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Accessories extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
		 $this->load->model('accessories_model');
        $this->load->model('user_model');
        $this->load->model('employee_model');
        $this->load->model('service_centers_model');
        $this->load->model('booking_model');
        $this->load->model('partner_model');
        $this->load->model('vendor_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('miscelleneous');
        $this->load->library('notify');
        $this->load->library('booking_utilities');
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') ) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }   
	
	 /**
     * @Desc: This function is to show form to add product and hsn code
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
    */
	public function add_accessories()
	{
		
		$data['services_detail']=$this->vendor_model->selectservice();
		$data['hsn_code_detail']=$this->accessories_model->fetch_accessories_data('hsn_code_details');
		$this->miscelleneous->load_nav_header();
		$this->load->view('employee/add_accessories',$data);
	}
	
	/**
     * @Desc: This function is to submit add product form
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
    */
	public function process_submit_add_product()
	{
		if(!empty($this->input->post('appliance_id')))
		{
			$data=array(
			'appliance' =>		$this->input->post('appliance_id'),
			'product_name' =>	$this->input->post('product_name'),
			'description' =>	$this->input->post('description'),
			'basic_charge' =>	$this->input->post('basic_charge'),
			'hsn_code' =>		$this->input->post('hsn_code'),
			'tax_rate' =>		$this->input->post('tax_rate'),
			'created_by' =>		$this->session->userdata('id'),
			);
			
			
			$this->form_validation->set_rules('appliance_id', 'Appliance id', 'required');
			$this->form_validation->set_rules('product_name', 'Product name', "required");
			$this->form_validation->set_rules('description', 'Description', 'required');
			$this->form_validation->set_rules('basic_charge', 'Basic charge', 'required|numeric');
			$this->form_validation->set_rules('hsn_code', 'Hsn Code', 'required');
			$this->form_validation->set_rules('tax_rate', 'Tax rate', 'required|numeric');
			
			###############################validate all input fields######################################3
			if ($this->form_validation->run() == FALSE)
			{
				$array['status']='error';
				$array['msg']=validation_errors();;
				echo json_encode($array);
			}
			else
			{

				$this->accessories_model->insert_product_data('accessories_product_description',$data);

				$array['status']='success';
				$array['msg']='Product added successfully.';
				echo json_encode($array);
			}
		}
	}
	
	/**
     * @Desc: This function is to used to calculate tax based on hsn code
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
    */
	function calculate_tax()
    {
        $hsncode=$this->input->post('hsncode');
		if(!empty($hsncode))
		{
			$where=array('id'=>$hsncode);
			$hsn_detail=$this->accessories_model->fetch_accessories_data('hsn_code_details',$where);
			echo $hsn_detail[0]['gst_rate'];
		}
    }

	 function sf_accessories_invoice(){
        $data['sf_list']=$this->vendor_model->viewvendor('',1);
		$data['services_name']=$this->accessories_model->show_accessories_list();
		$data['quantity_list']=range(1,30);
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/sf_accessories_invoice', $data);
    }

	/**
     * @Desc: This function is to used to show product list
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
    */
	function show_accessories_list(){
		
        $data['product_list']=$this->accessories_model->show_accessories_list();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/show_accessories_list', $data);
    }
    
    /**
     * @desc: This method is used to generate SF accessories invoices.
     * This method is used to get data from Form.
     */
    function process_sf_accessories_invoice() {
        $sfID = $this->input->post('sf_id');
        
        if($sfID){
            log_message('info', __FUNCTION__ . " Entering......");
            
            $data = array();
            $total_amount = $amount = 0;
            $sd = $ed = $invoice_date = date("Y-m-d");
            $vendor_data = $this->vendor_model->getVendorDetails("service_centres.id, gst_no, "
                                . "state,address as company_address, owner_phone_1,"
                                . "company_name, pincode, "
                                . "district, owner_email as invoice_email_to, email as invoice_email_cc", array('id' => $sfID))[0];
            
            $invoice_id = $this->invoice_lib->create_invoice_id("ARD-9");
            $accessories = $this->input->post('accessories');
            $quantity = $this->input->post('quantity');

            if(!empty($accessories)) {
                foreach ($accessories as $key=>$accessory) {
                    $accessory_details=$this->accessories_model->show_accessories_list(array('accessories_product_description.id' => $accessory))[0];

                    $total_amount = $amount = sprintf("%.2f", ($accessory_details['basic_charge']*(1+($accessory_details['tax_rate']/100)))*$quantity[$key]);                
                    $hsn_code = $accessory_details['hsn_code'];
                    $gst_rate = $accessory_details['tax_rate'];
                    $data[$key]['description'] =  "SF Invoice for Accessory ".$accessory_details['product_name'];
                    $data[$key]['taxable_value'] = sprintf("%.2f", $total_amount);
                    $data[$key]['product_or_services'] = "Product";
                    $data[$key]['gst_number']="";

                    $data[$key]['company_name'] = $vendor_data['company_name'];
                    $data[$key]['company_address'] = $vendor_data['company_address'];
                    $data[$key]['district'] = $vendor_data['district'];
                    $data[$key]['pincode'] = $vendor_data['pincode'];
                    $data[$key]['state'] = $vendor_data['state'];
                    $data[$key]['rate'] = sprintf("%.2f", ($data[$key]['taxable_value']/$quantity[$key]));
                    $data[$key]['qty'] = $quantity[$key];
                    $data[$key]['hsn_code'] = $hsn_code;
                    $data[$key]['gst_rate'] = $gst_rate;
                    $data[$key]['owner_phone_1'] = $vendor_data['owner_phone_1'];
                }
            }
            
            if(!empty($data)){
                $response = $this->invoices_model->_set_partner_excel_invoice_data($data, $sd, $ed, "Tax Invoice",$invoice_date);
                $response['meta']['invoice_id'] = $invoice_id;
                
                $status = $this->invoice_lib->send_request_to_create_main_excel($response, "final");
                if($status){
                    $convert = $this->invoice_lib->convert_invoice_file_into_pdf($response, "final");
                    $output_pdf_file_name = $convert['main_pdf_file_name'];
                    $response['meta']['invoice_file_main'] = $output_pdf_file_name;
                    $response['meta']['copy_file'] = $convert['copy_file'];
                    $response['meta']['invoice_file_excel'] = $invoice_id.".xlsx";
                    $response['meta']['invoice_detailed_excel'] = NULL;

                    $this->invoice_lib->upload_invoice_to_S3($invoice_id, false);

                    $cmd = "curl " . S3_WEBSITE_URL . "invoices-excel/" . $output_pdf_file_name . " -o " . TMP_FOLDER.$output_pdf_file_name;
                    exec($cmd); 

                    unlink(TMP_FOLDER.$output_pdf_file_name);
                    unlink(TMP_FOLDER."copy_".$output_pdf_file_name);

                    unlink(TMP_FOLDER.$invoice_id.".xlsx");
                    unlink(TMP_FOLDER."copy_".$invoice_id.".xlsx");
                }

                $invoice_tag_details = $this->invoices_model->get_invoice_tag('vertical, category, sub_category', array('tag' => ACCESSORIES_TAG));

                if(!empty($invoice_tag_details)) {
                    $response['meta']['vertical'] = $invoice_tag_details[0]['vertical'];
                    $response['meta']['category'] = $invoice_tag_details[0]['category'];
                    $response['meta']['sub_category'] = $invoice_tag_details[0]['sub_category'];
                }
                $response['meta']['accounting'] = 1;
                $response['meta']['due_date'] = $response['meta']['invoice_date'];
                
                $this->invoice_lib->insert_invoice_breackup($response);
                $invoice_details = $this->invoice_lib->insert_vendor_partner_main_invoice($response, "A", "Parts", _247AROUND_SF_STRING, $sfID, $convert, $this->session->userdata('id'), $hsn_code);
                $inserted_invoice = $this->invoices_model->insert_new_invoice($invoice_details);
                
                $this->session->set_userdata(array('success' => "SF Accessories Invoice Inserted Successfully!!"));
                redirect(base_url()."employee/accessories/sf_accessories_invoice");
            }
            else{
                $this->session->set_userdata(array('error' => "No Accessories Data Found!!"));
                redirect(base_url()."employee/accessories/sf_accessories_invoice");
            }
        }
        else{
            $this->session->set_userdata(array('error' => "Select Service Center"));
            redirect(base_url()."employee/accessories/sf_accessories_invoice");
        }
    }
     
}
