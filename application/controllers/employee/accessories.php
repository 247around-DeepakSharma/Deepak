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
     
}
