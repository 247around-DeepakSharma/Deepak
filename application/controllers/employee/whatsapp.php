<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Whatsapp extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();

        $this->load->model('user_model');
        $this->load->model('whatsapp_model');
        $this->load->library("session");
        $this->load->library('form_validation');
        $this->load->library("miscelleneous");
        $this->load->library("notify");
        $this->load->helper(array('form', 'url'));
    }

    /**
     * @desc this is used to whatsapp history table
     * @Author Abhishek AWasthi
     */
    function history() {
		$this->checkUserSession();
        $this->miscelleneous->load_nav_header();
        $this->load->view('employee/whatsapp_chat_history');
    }

    /** @desc: This function is used to get the chat list.
     * @param: void
     * @return void
     */
    function get_whatsapp_log() {
        $post = $this->get_post_data();

        $post[''] = array();
        $post['column_order'] = array();
        $post['column_search'] = array('source', 'destination', 'content');
        // $post['group_by'] = 'destination';          
        $select = "id,source,destination,channel,direction,content,created_on as created_on,status,content_type";

        $list = $this->whatsapp_model->get_whatsapp_log_list($post, $select);
        $data = array();
        $no = $post['start'];

        foreach ($list as $chat_list) {
            $no++;
            $row = $this->get_whatsapp_table($chat_list, $no);
            $data[] = $row;
        }

        $post['length'] = -1;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->whatsapp_model->count_all_whatsapp_log($post),
            "recordsFiltered" => $this->whatsapp_model->count_filtered_whatsapp_log($post),
            // 'stock' => $countlist[0]->stock,
            "data" => $data,
        );

        echo json_encode($output);
    }
	
	
	function checkUserSession() {
         if (($this->session->userdata('loggedIn') == TRUE) && ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            log_message('info', __FUNCTION__. " Session Expire for Service Center");
            $this->session->sess_destroy();
            redirect(base_url() . "employee/login");
        }
    }

    /**
     * @desc this is used to generate  table
     * @Author Abhishek AWasthi
     */
    private function get_whatsapp_table($log_list, $sn) {
        $row = array();
        $row[] = $sn;
        $row[] = '<a class="" style="text-decoration:none;cursor:not-allowed;" data-number="' . $log_list['source'] . '" href="#">' . $log_list['source'] . '<a>';
        $row[] = '<a class="chat_number" data-id="' . $log_list['id'] . '" id="destination' . $log_list['id'] . '" style="text-decoration:none;" data-number="' . $log_list['destination'] . '" href="#">' . $log_list['destination'] . '<a>';
        $row[] = $log_list['channel'];
        $row[] = $log_list['direction'];
        
        if($log_list['content_type']=='media'){
             $media = json_decode($log_list['content']);
           //  print_r($media);
             $url = $media->url;
             $type_media = $media->type;
             if($type_media=='image'){
                 $row[] = "<img src='".$url."' style='height:50px;width:50px;' />" ;
             }else if($type_media=='video'){
               $video = ' <video width="50" height="50" controls src="https://api.karix.io/media/cadcf426-f30f-4cc4-baa8-43cee0e059d2">Video</video>';
                 $row[] = $video ;
             }else{
                 $row[] = "<a target='_blank' href='".$url."' style='height:50px;width:50px;' >Download</a>" ;
             }
             
             
        }else if($log_list['content_type']=='location'){
             $location = json_decode($log_list['content']);
             $lat  = $location->latitude;
             $long = $location->longitude;   
             $map = "http://maps.google.com/maps?q=loc:".$lat.",".$long."77.7060387";
             $row[] ='<a style="font-size: 25px;" href="'.$map.'" ><i class="fa fa-map-marker" aria-hidden="true"></i> Click on icon </a>'; 
            
        }else{
           $row[] = $log_list['content'];   
        } 
        
       
        
        
        
        if ($log_list['status'] == 'failed') {
            $row[] = '<span class="label label-danger">Failed</span>';
        } else {
            $row[] = '<span class="label label-success">Success</span>';
        }


        return $row;
    }

    /**
     *  @desc : This function is used to get the post data for chat
     *  @param : void()
     *  @return : $post Array()
     */
    private function get_post_data() {
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');

        return $post;
    }

    /**
     * @desc this is used to get whatsapp history of a number
     * @Author Abhishek AWasthi
     */
    function getChatByNumber() {
        $number = '+' . trim($_GET['number']);
        $chat = $this->whatsapp_model->getChatByNumber($number);
        echo json_encode(array('result' => $chat));
    }

    /**
     * @desc this is used to send to whatsapp to any number
     * @Author Abhishek AWasthi
     */
    function send_whatsapp_to_any_number() {

        if (isset($_POST['number']) && !empty($_POST['number']) && isset($_POST['message']) && !empty($_POST['message'])) {
            $message = trim($_POST['message']);
            $number = trim($_POST['number']);
            $result = $this->notify->send_whatsapp_to_any_number($number, $message);
            echo json_encode(array('result' => $result));
        } else {
            echo json_encode(array('result' => 'error'));
        }
    }

}
