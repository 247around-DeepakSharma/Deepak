<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Excel extends CI_Controller {

    /**
     * @desc : load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('handyman_model');
        $this->load->model('employee_model');
        $this->load->model('filter_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->helper('download');
        $this->load->library('image_lib');
        $this->load->library('s3');
        if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee') ) {
            return TRUE;
        } else {
            redirect(base_url() . "employee/login");
        }
    }

    /**
     *  @desc : This function load excel view and load vrifylist and load filter
     *  @param : void
     *  @return : void
     */
    public function index() {
        $results['service'] = $this->filter_model->getserviceforfilter();
        $results['agent'] = $this->filter_model->getagent();
        $employee_id = $this->session->userdata('employee_id');
        $results['one'] = $this->employee_model->verifylist($employee_id, '0');
        $results['three'] = $this->employee_model->verifylist($employee_id, '2');
        $results['forteen'] = $this->employee_model->verifylist($employee_id, '14');
        $this->load->view('employee/header', $results);
        $this->load->view('employee/Excel');
    }

    /**
     *  @desc : This function import excel file 
     *  @param : post excel
     *  @return : void
     */
    public function upload() {
        if (!empty($_FILES['file']['name'])) {
            $import = $this->import($_FILES);
            if (empty($import[0])) {
                $result['service'] = $this->filter_model->getserviceforfilter();
                $result['agent'] = $this->filter_model->getagent();
                $employee_id = $this->session->userdata('employee_id');
                $result['one'] = $this->employee_model->verifylist($employee_id, '0');
                $result['three'] = $this->employee_model->verifylist($employee_id, '2');
                $result['forteen'] = $this->employee_model->verifylist($employee_id, '14');
                $output['success'] = "File Import Sucessfully;";
                $this->load->view('admin/header', $result);
                $this->load->view('admin/Excel', $output);
            } else if ($import[0] == 1) {
                $this->loaderror();
            } else {
                $this->duplicateviewload($import);
            }
        }
    }

    /**
     *  @desc : This function check duplicate entry and insert handyman
     *  @param : excel file
     *  @return : if duplicate entry   and if file not valid
     */
    public function import($file) {
        $pathinfo = pathinfo($_FILES["file"]["name"]);

        if ($pathinfo['extension'] == 'csv') {
            if ($file['file']['size'] > 0) {

                $file = $file['file']['tmp_name'];
                $handle = fopen($file, "r");
                $escapeCounter = 0;
                $counthandyman = 0;
                $arrayName = array();
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($escapeCounter > 1) {
                        $handymanExist = $this->handyman_model->handymanExist($data[1], $data[3], $data[9]);
                        array_push($arrayName, $handymanExist);
                        if (empty($handymanExist)) {
                            $handymanDetail = $this->sethandymanData($data);
                            $insert = $this->handyman_model->insertData($handymanDetail);
                            $counthandyman = $counthandyman + 1;
                        }
                    }
                    $escapeCounter = $escapeCounter + 1;
                }
                fclose($handle);
                return $arrayName;
            }
        } else {

            $error[0] = "1";
            return $error;
        }
    }

    /**
     *  @desc : This function for set data index from excel file
     *  @param : excel entry
     *  @return : array(handyman detail)
     */
    public function sethandymanData($slice) {
        //$insert['Agent']               = $slice[0];
        $insert['name'] = $slice[1];
        $insert['date_of_collection'] = $slice[2];
        $insert['service_id'] = $slice[3];
        $insert['is_disabled'] = $slice[4];
        $insert['profile_photo'] = $slice[5];
        $insert['address'] = $slice[6];
        $insert['location'] = $slice[7];
        if (!empty($insert['location'])) {
            $loc = explode("|", $insert['location']);
            if (count($loc) > 1) {
                $loc = array("lattitude" => $loc[0], "longitude" => $loc[1]);
                $insert['location'] = json_encode($loc);
            }
        } else {
            $loc = array("lattitude" => '28.56', "longitude" => '77.34');
            $insert['location'] = json_encode($loc);
        }
        $insert['vendors_area_of_operation'] = $slice[8];
        $insert['phone'] = $slice[9];
        $insert['experience'] = $slice[10];
        $insert['marital_status'] = $slice[11];
        $insert['Android_Phone'] = $slice[12];
        $insert['passport'] = $slice[13];
        $insert['bank_account'] = $slice[14];
        $insert['bank_ac_no'] = $slice[15];
        $insert['id_proof_name'] = $slice[16];
        $insert['id_proof_no'] = $slice[17];
        $insert['id_proof_photo'] = $slice[18];
        $insert['is_paid'] = $slice[19];
        $insert['service_on_call'] = $slice[20];
        $insert['common_charges'] = $slice[21];
        $insert['handyman_previous_customers'] = $slice[22];
        $insert['Other_handyman_contact'] = $slice[23];
        $insert['Rating_by_Agent'] = $slice[24];
        $insert['time_of_data_collection'] = $slice[25];
        $insert['police_verification'] = $slice[26];
        $insert['image_processing'] = '0';
        $insert['action'] = '0';
        return $insert;
    }

    /**
     *  @desc : This function load if duplicate entry exist
     *  @param : void
     *  @return : void
     */
    function duplicateviewload($handymanDetail) {
        $employee_id = $this->session->userdata('employee_id');
        $handyman['result'] = $handymanDetail;
        $result['service'] = $this->filter_model->getserviceforfilter();
        $result['agent'] = $this->filter_model->getagent();
        $result['one'] = $this->employee_model->verifylist($employee_id, '0');
        $result['three'] = $this->employee_model->verifylist($employee_id, '2');
        $result['forteen'] = $this->employee_model->verifylist($employee_id, '14');
        $handyman['error'] = "handyman aleady exist!";
        $this->load->view('employee/header', $result);
        $this->load->view('employee/duplicateExcel', $handyman);
    }

    /**
     *  @desc : This function load if file not valid
     *  @param : void
     *  @return : void
     */
    function loaderror() {
        $employee_id = $this->session->userdata('employee_id');
        $result['service'] = $this->filter_model->getserviceforfilter();
        $result['agent'] = $this->filter_model->getagent();
        $result['one'] = $this->employee_model->verifylist($employee_id, '0');
        $result['three'] = $this->employee_model->verifylist($employee_id, '2');
        $result['forteen'] = $this->employee_model->verifylist($employee_id, '14');
        $output['error'] = "Import valid csv!";
        $this->load->view('employee/header', $result);
        $this->load->view('employee/Excel', $output);
    }

    /**
     * @desc : This funtion for upload csv file
     * @param : temporary name and name of csv
     * @return : name name of csv file
     */
    function uploadCsv($tmp, $name) {
        $csv = md5(uniqid(rand()));
        $sourcePath = $tmp;
        $targetPath = "./uploads/" . $csv . $name;
        move_uploaded_file($sourcePath, $targetPath);
        $targetPaths = $csv . $_FILES["file"]["name"];
        return $targetPaths;
    }

    /**
     * @desc : This funtion get handyman profile photo from s3 buckket and resize to upload s3 buckket
     * @param : handyman id
     * @return : true
     */
    public function getimage($id) {

        $handyman = $this->handyman_model->getexcelimage($id);
        foreach ($handyman as $value) {
            $profile_photo = $value['profile_photo'];
            $handymanid = $value['id'];

            $path = "https://s3.amazonaws.com/vendor-original/" . $profile_photo;
            $path1 = './uploads/fulllength/';
            $path2 = './uploads/resize/';
            if (copy($path, $path1 . $profile_photo)) {
                $config['image_library'] = 'gd2';
                $config['source_image'] = $path1 . $profile_photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 310;
                $config['height'] = 252;
                $config['new_image'] = $path2 . $profile_photo;
                $this->load->library('image_lib', $config);
                $this->image_lib->initialize($config);
                if ($this->image_lib->resize()) {
                    $bucket = "boloaaka-images";
                    $file = $path2 . $profile_photo;
                    $directory = "vendor-320x252/" . $profile_photo;
                    $input = S3::inputFile($file);
                    S3::putObject($input, $bucket, $directory, S3::ACL_PUBLIC_READ);
                }
            } else {
                $this->employee_model->delete($handymanid);
            }
        }
        return true;
    }

}
