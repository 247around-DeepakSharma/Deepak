<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Blogs extends CI_Controller {

    /**
     * load list modal and helpers
     */
    function __Construct() {
        parent::__Construct();
        $this->load->model('employee_model');
        $this->load->model('blogs_model');
        $this->load->model('filter_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library("session");
        $this->load->library('s3');
        if (($this->session->userdata('loggedIn') == TRUE) &&
                ($this->session->userdata('userType') == 'employee')) {
            return TRUE;
        } else {
            echo "<pre/>";
            echo "LoggedIn: " . $this->session->userdata('loggedIn');
            echo "UserType: " . $this->session->userdata('userType');
            redirect(base_url() . "employee/login");
        }
    }

    /*
     * @desc: This function is to process to add a new blog or edit an existing blog.
     * @param: void
     * @return: void
     */

    function index() {
        $blog['id'] = $this->input->post('id');
        $blog['title'] = $this->input->post('title');
        $blog['url'] = $this->input->post('url');
        $blog['description'] = $this->input->post('description');
        $blog['keyword'] = $this->input->post('keyword');
        $blog['author'] = $this->input->post('author');
        $blog['content'] = $this->input->post('content');
        $blog['file_input'] = $this->input->post('file_input');
        $blog['alternate_text'] = $this->input->post('alternate_text');

        if (isset($_FILES['file_input']['name'])) {
            $blog['file_input'] = $_FILES['file_input']['name'];
            $size = $_FILES['file_input']['size'];

            $bucket = 'appliance-pics';
            //$this->s3->putObjectFile($_FILES['file_input']['tmp_name'], $bucket, $blog['file_input'], S3::ACL_PUBLIC_READ);
        } else {
            //$image_file = "";
            echo "No File Found";
        }

        if ($blog['id'] == '') {
            $this->blogs_model->add_blog($blog);
            $query = $this->blogs_model->view_blogs();

            $this->load->view('employee/header');
            $this->load->view('employee/viewblogs', array('query' => $query));
        } else {
            $this->blogs_model->edit_blog($blog);
            $query = $this->blogs_model->view_blogs();

            $this->load->view('employee/header');
            $this->load->view('employee/viewblogs', array('query' => $query));
        }
    }

    /*
     * @desc: This function is to get a form to add a new blog.
     * @param: void
     * @return: void
     */

    function addblog() {
        $this->load->view('employee/header');
        $this->load->view('employee/addblog');
    }

    /*
     * @desc: This function is to display details of all the blogs present
     * @param: void
     * @return: void
     */

    function viewblogs() {
        $query = $this->blogs_model->view_blogs();
        $this->load->view('employee/header');
        $this->load->view('employee/viewblogs', array('query' => $query));
    }

    /*
     * @desc: This function is to edit a particular blog already present
     * @param: $id- Id of the blog which we want to edit
     * @return: void
     */

    function editblog($id) {
        $query = $this->blogs_model->editblog($id);
        $this->load->view('employee/header');
        $this->load->view('employee/addblog', array('query' => $query));
    }

    /*
     * @desc: This function is to publish a particular blog on its request
     * @param: $id- Id of the blog which we want to publish
     * @return: void
     */

    function publish($id) {
//        $query = $this->blogs_model->publish($id);  // $query looks unused
        $this->blogs_model->publish($id);

        $query = $this->blogs_model->view_blogs();
        $this->load->view('employee/header');
        $this->load->view('employee/viewblogs', array('query' => $query));
    }

    /*
     * @desc: This function is to unpublish a particular blog on its request
     * @param: $id- Id of the blog which we want to unpublish
     * @return: void
     */

    function unpublish($id) {
//        $query = $this->blogs_model->unpublish($id);    // $query looks unused
        $this->blogs_model->unpublish($id);

        $query = $this->blogs_model->view_blogs();
        $this->load->view('employee/header');
        $this->load->view('employee/viewblogs', array('query' => $query));
    }

    /*
     * @desc: This function is to delete a particular blog on its request
     * @param: $id- Id of the blog which we want to delete
     * @return: void
     */

    function delete($id) {
//        $query = $this->blogs_model->delete($id);     // $query looks unused
        $this->blogs_model->delete($id);

        $query = $this->blogs_model->view_blogs();
        $this->load->view('employee/header');
        $this->load->view('employee/viewblogs', array('query' => $query));
    }

}
