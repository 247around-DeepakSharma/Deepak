<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pdfconverter extends CI_Controller {

    function __Construct() {
        parent::__Construct();
        
        $this->load->library('s3');
    }
    
    
    /**
     * @desc This function is used to convert the input file into pdf
     * @param void
     * @return json 
     */
    public function excel_to_pdf_converter() {
        if (isset($_POST['auth_key']) && $_POST['auth_key'] === PDF_CONVERTER_AUTH_KEY) {
            if (isset($_FILES['file_contents']['name'])) {

                //get the post data and file data
                $tmpFile = $_FILES['file_contents']['tmp_name'];
                $excel_file_name = $_FILES['file_contents']['name'];
                $bitbucket_directory = $_POST['bucket_dir'];
                $id = $_POST['id'];

                $tmp_path = TMP_FOLDER;

                //set the output pdf file name
                $output_pdf_file_name = explode('.', $excel_file_name)[0];
                $output_pdf_file = $tmp_path . $output_pdf_file_name . ".pdf";

                //convert excel to pdf
                putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/opt/node/bin');

                $tmp_output_file = $tmp_path . 'output_' . __FUNCTION__ . '.txt';
                $cmd = 'echo ' . $tmp_path . ' & echo $PATH & UNO_PATH=/usr/lib/libreoffice & ' .
                        '/usr/bin/unoconv --format pdf --output ' . $output_pdf_file . ' ' .
                        $tmpFile . ' 2> ' . $tmp_output_file;

                $output = '';
                $result_var = '';
                exec($cmd, $output, $result_var);

                $return_data = $this->upload_pdf_to_s3($output_pdf_file, $output_pdf_file_name, $bitbucket_directory);

                if($return_data === 'success') {
                    
                    //return this response when PDF generated Successfully
                    $response_data = array('response' => 'Success',
                                           'response_msg' => 'PDF generated Successfully and uploaded on S3',
                                           'output_pdf_file' => $output_pdf_file_name.'.pdf',
                                           'bucket_dir' => $bitbucket_directory,
                                           'id' => $id
                                          );
                    //delete generated text file
                    exec("rm -rf " . escapeshellarg($tmp_output_file));
                    echo json_encode($response_data);
                } else if($return_data === 'upload_error') {
                    //return this response when PDF generated successfully but unable to upload on S3
                    $response_data = array('response' => 'Error',
                                           'response_msg' => 'PDF generated Successfully But Unable To Upload on S3',
                                           'output_pdf_file' => $output_pdf_file_name.'.pdf',
                                           'bucket_dir' => $bitbucket_directory,
                                           'id' => $id
                                           );
                    echo json_encode($response_data);
                }else if($return_data === 'file_error'){
                    //return this response when pdf is not generated
                    $response_data = array('response' => 'Error',
                                           'response_msg' => 'Error In Generating PDF File',
                                           );
                    echo json_encode($response_data);
                }
            } else {
                //return this response when post file is empty or not valid
                $response_data = array('response' => 'Error',
                                       'response_msg' => 'File Is Missing');
                echo json_encode($response_data);
            }
        } else {
            //return this response when auth key is not valid
            $response_data = array('response' => 'Error',
                                   'response_msg' => 'Invalid Request Type');
            echo json_encode($response_data);
        }
    }
    
    /**
     * @desc This function is used to upload the converted pdf file on se
     * @param $output_pdf_file string
     * @param $output_pdf_file_name string
     * @param $bitbucket_directory string
     * @return string 
     */
    private function upload_pdf_to_s3($output_pdf_file, $output_pdf_file_name, $bitbucket_directory) {

        if (file_exists($output_pdf_file) && (filesize($output_pdf_file)/1000) > 10) {

            $directory_pdf = "invoices-excel/" . $output_pdf_file_name . '.pdf';
            $upload_pdf = $this->s3->putObjectFile(TMP_FOLDER . $output_pdf_file_name . '.pdf', $bitbucket_directory, $directory_pdf, S3::ACL_PUBLIC_READ);

            if ($upload_pdf) {
                
                //delete generated pdf file
                exec("rm -rf " . escapeshellarg($output_pdf_file));
                return "success";
            } else {
                return "upload_error";
            }
        } else {
            $this->send_mail_on_failure($output_pdf_file_name);
            return "file_error";
        }
    }
    
    
    /**
     * @desc This function is used to send the mail when pdf is not created
     * @param $file_name string
     * @return void 
     */
    private function send_mail_on_failure($file_name){
        $to = DEVELOPER_EMAIL;
        $subject = "Error In Converting PDF File";
        $message = "Error in converting excel file to pdf.";
        $attachement = TMP_FOLDER.$file_name.'.xlsx';
        $this->notify->sendEmail('booking@247around.com', $to, '', '', $subject, $message, $attachement);
    }
    
    
    /**
     * @desc this is testing function
     * @param void
     * @return void 
     */
    public function test_pdf() {

        $output_file_excel = TMP_FOLDER . 'BookingJobCard-SY-10751117050813.xlsx';
        $target_url = base_url() . 'pdfconverter/excel_to_pdf_converter';

        if (function_exists('curl_file_create')) { // php 5.5+
            $cFile = curl_file_create($output_file_excel);
        } else { // 
            $cFile = '@' . realpath($output_file_excel);
        }
        $post = array('bucket_dir' => BITBUCKET_DIRECTORY, 'id' => 'dd', 'file_contents' => $cFile,'auth_key'=>PDF_CONVERTER_AUTH_KEY);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        curl_close($ch);

        echo $result;
    }

}
