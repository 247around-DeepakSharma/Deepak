<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Accessories extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
         parent::__Construct();
        $this->load->model('accessories_model');
        $this->load->model('vendor_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->library('s3');
        $this->load->library('miscelleneous');
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
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
   public function add_accessories() {

        $data['services_detail'] = $this->vendor_model->selectservice();
        $data['hsn_code_detail'] = $this->accessories_model->fetch_table_data('hsn_code_details');
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/add_accessories', $data);
    }

     /**
     * @Desc: This function is to submit add product form
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
     */
     public function process_submit_add_product() {
        if (!empty($this->input->post('appliance_id'))) {
            $data = array(
                'service_id' => $this->input->post('appliance_id'),
                'product_name' => $this->input->post('product_name'),
                'description' => $this->input->post('description'),
                'basic_charge' => $this->input->post('basic_charge'),
                'hsn_code' => $this->input->post('hsn_code'),
                'tax_rate' => $this->input->post('tax_rate'),
                'agent_id' => $this->session->userdata('id'),
            );


            $this->form_validation->set_rules('appliance_id', 'Appliance id', 'required');
            $this->form_validation->set_rules('product_name', 'Product name', "required");
            $this->form_validation->set_rules('description', 'Description', 'required');
            $this->form_validation->set_rules('basic_charge', 'Basic charge', 'required|numeric');
            $this->form_validation->set_rules('hsn_code', 'Hsn Code', 'required');
            $this->form_validation->set_rules('tax_rate', 'Tax rate', 'required|numeric');

            //validate all input fields
            if ($this->form_validation->run() == FALSE) {
                $array['status'] = 'error';
                $array['msg'] = validation_errors();
                echo json_encode($array);
            } else {
                $product = $this->accessories_model->show_accessories_list(array('accessories_product_description.service_id' => $this->input->post('appliance_id'), 'accessories_product_description.product_name' => $this->input->post('product_name')));
                if (count($product) > 0) {
                    $status = $product[0]['status'];
                    if ($status == 0) {
                        $statusText = 'Inactive';
                    } else {
                        $statusText = 'Active';
                    }
                    $array['status'] = 'error';
                    $array['msg'] = 'Product already added with  ' . $statusText . ' status.';
                    echo json_encode($array);
                } else {
                    $this->accessories_model->insert_product_data('accessories_product_description', $data);
                    $array['status'] = 'success';
                    $array['msg'] = 'Product added successfully.';
                    echo json_encode($array);
                }
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
    function calculate_tax() {
        $hsncode = $this->input->post('hsncode');
        if (!empty($hsncode)) {
            $where = array('hsn_code' => $hsncode);
            $hsn_detail = $this->accessories_model->fetch_table_data('hsn_code_details', $where);
            echo $hsn_detail[0]['gst_rate'];
        }
    }

    /**
     * @Desc: This function is to used to show generate invoice view
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
     */
    function sf_accessories_invoice() {
        $data['sf_list'] = $this->vendor_model->viewvendor('', 1);
        $data['services_name'] = $this->accessories_model->show_accessories_list(array('accessories_product_description.status' => 1));
        $data['quantity_list'] = range(1, 1000);
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
    function show_accessories_list() {
        $data['product_list'] = $this->accessories_model->show_accessories_list();
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
                                . "district, owner_email as invoice_email_to, owner_phone_1, primary_contact_phone_1 ,email as invoice_email_cc", array('id' => $sfID))[0];
            
            $invoice_id = $this->invoice_lib->create_invoice_id("ARD-9");
            $accessories = $this->input->post('accessories');
            $count=0;

            if(!empty($accessories)) {
                foreach ($accessories as $key=>$accessory) {
                    $accessory_details=$this->accessories_model->show_accessories_list(array('accessories_product_description.id' => $accessory['id']))[0];
                    
                    if($accessory_details['status'] == 0) {
                        $this->session->set_userdata(array('error' => $accessory_details['product_name']." Is Not Active !!"));
                        redirect(base_url()."employee/accessories/sf_accessories_invoice");
                    }
                    else {
                        $total_amount = $amount = sprintf("%.2f", ($accessory_details['basic_charge']*(1+($accessory_details['tax_rate']/100)))*$accessory['qty']);                
                        $hsn_code = $accessory_details['hsn_code'];
                        $gst_rate = $accessory_details['tax_rate'];
                        $data[$count]['description'] =  $accessory_details['product_name'];
                        $tax_charge = $this->booking_model->get_calculated_tax_charge($total_amount, $gst_rate);
                        $data[$count]['taxable_value'] = sprintf("%.2f", ($total_amount  - $tax_charge));
                        $data[$count]['product_or_services'] = "Product";
                        if(!empty($vendor_data['gst_no'])){
                            $data[$count]['gst_number'] = $vendor_data['gst_no'];
                        } else {
                            $data[$count]['gst_number'] = TRUE;
                        }

                        $data[$count]['company_name'] = $vendor_data['company_name']. " ( Ph No: ".
                                $vendor_data['primary_contact_phone_1'].",". 
                                $vendor_data['owner_phone_1']. " )";
                        $data[$count]['company_address'] = $vendor_data['company_address'];
                        $data[$count]['district'] = $vendor_data['district'];
                        $data[$count]['pincode'] = $vendor_data['pincode'];
                        $data[$count]['state'] = $vendor_data['state'];
                        $data[$count]['rate'] = sprintf("%.2f", ($data[$count]['taxable_value']/$accessory['qty']));
                        $data[$count]['qty'] = $accessory['qty'];
                        $data[$count]['hsn_code'] = $hsn_code;
                        $data[$count]['gst_rate'] = $gst_rate;
                        $data[$count]['owner_phone_1'] = $vendor_data['owner_phone_1'];
                        $data[$count]['from_gst_number'] = 7; // For Invoice ID 'ARD-9' : from_gst_number '7' will be stored in invoice_details table i.e. id for 247around(UP) in entity_gst_details table
                        ++$count;
                    }
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
                
                $this->invoice_lib->upload_invoice_to_S3($invoice_id, false);
                
                $cmd = "curl " . S3_WEBSITE_URL . "invoices-excel/" . $output_pdf_file_name . " -o " . TMP_FOLDER.$output_pdf_file_name;
                exec($cmd); 

                $email_tag = SF_ACCESSORIES_INVOICE;    
                $email_template = $this->booking_model->get_booking_email_template($email_tag);
                $subject = $email_template[4];
                $message = $email_template[0];
                $email_from = $email_template[2];
                $to = $vendor_data['invoice_email_to'].",".$this->session->userdata("official_email").(!empty($email_template[1]) ? ",".$email_template[1] : "");
                $cc = $vendor_data['invoice_email_cc'].(!empty($email_template[3]) ? ",".$email_template[3] : "");
                $pdf_attachement_url = S3_WEBSITE_URL . "invoices-excel/" . $output_pdf_file_name;
                $this->notify->sendEmail($email_from, $to, $cc, $email_template[5], $subject, $message, $pdf_attachement_url, $email_tag);
                
                unlink(TMP_FOLDER.$output_pdf_file_name);
                unlink(TMP_FOLDER."copy_".$output_pdf_file_name);

                unlink(TMP_FOLDER.$invoice_id.".xlsx");
                unlink(TMP_FOLDER."copy_".$invoice_id.".xlsx");
                
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
    
     /**
     * @Desc: This function is to used update status of accessories (make product active inactive), and update existing accessories
     * @params: void
     * @return: NULL
     * @author Ghanshyam
     * @date : 17-02-2020
     */
    function update_accessories_status() {
        if (!empty($this->input->post('idtodelete'))) {

            $idtodelete = $this->input->post('idtodelete');
            $status = $this->input->post('status');
            $where = array('id' => $idtodelete);
            $columnUpdate = array('status' => $status, 'update_date' => date('Y-m-d H:i:s'));
            $this->accessories_model->update_accessories_data($columnUpdate, $where);
        }
    }
    
    /**
     * @Desc: This function is to used to edit accessories view page
     * @params: void
     * @return: NULL
     * @author Raman
     * @date : 17-02-2020
     */
    public function edit_accessories($id = '') {
        if (!empty($id)) {
            $data['id'] = $id;
            $data['services_detail'] = $this->vendor_model->selectservice();
            $data['hsn_code_detail'] = $this->accessories_model->fetch_table_data('hsn_code_details');
            $data['accessories_detail'] = $this->accessories_model->fetch_table_data('accessories_product_description', array('id' => $id));
            if (!empty($data['accessories_detail'])) {
                $this->miscelleneous->load_nav_header();
                $this->load->view('employee/edit_accessories', $data);
            } else {
                redirect(base_url() . "employee/accessories/show_accessories_list");
            }
        } else {
            redirect(base_url() . "employee/accessories/show_accessories_list");
        }
    }

    public function process_submit_edit_product() {
        if (!empty($this->input->post('appliance_id'))) {
            $data = array(
                'service_id' => $this->input->post('appliance_id'),
                'product_name' => $this->input->post('product_name'),
                'description' => $this->input->post('description'),
                'basic_charge' => $this->input->post('basic_charge'),
                'hsn_code' => $this->input->post('hsn_code'),
                'tax_rate' => $this->input->post('tax_rate'),
                'update_date' => date('Y-m-d H:i:s')
            );

            $idtoedit = $this->input->post('idtoedit');


            $this->form_validation->set_rules('appliance_id', 'Appliance id', 'required');
            $this->form_validation->set_rules('product_name', 'Product name', "required");
            $this->form_validation->set_rules('description', 'Description', 'required');
            $this->form_validation->set_rules('basic_charge', 'Basic charge', 'required|numeric');
            $this->form_validation->set_rules('hsn_code', 'Hsn Code', 'required');
            $this->form_validation->set_rules('tax_rate', 'Tax rate', 'required|numeric');

            //validate all input fields
            if ($this->form_validation->run() == FALSE) {
                $array['status'] = 'error';
                $array['msg'] = validation_errors();
                ;
                echo json_encode($array);
            } else {
                $product = $this->accessories_model->show_accessories_list(array('accessories_product_description.service_id' => $this->input->post('appliance_id'), 'accessories_product_description.product_name' => $this->input->post('product_name')), $this->input->post('idtoedit'));
                if (count($product) > 0) {
                    $status = $product[0]['status'];
                    if ($status == 0) {
                        $statusText = 'Inactive';
                    } else {
                        $statusText = 'Active';
                    }
                    $array['status'] = 'error';
                    $array['msg'] = 'Product already added with  ' . $statusText . ' status.';
                    echo json_encode($array);
                } else {
                    $where = array('id' => $idtoedit);
                    $this->accessories_model->update_accessories_data($data, $where);
                    $array['status'] = 'success';
                    $array['msg'] = 'Product updated successfully.';
                    echo json_encode($array);
                }
            }
        }
    }
     
}
