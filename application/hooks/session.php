<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserSession extends CI_Hooks {

    private $CI;
    private $allowedUrls = array('loadView','admin');

    /**
     *  @desc : get all codeigniter instance
     */
    function __construct(){
    
        $this->CI = &get_instance();
    }
    
    /**
     * @desc : This function will check user session. If session is distroyed send it to login page
     * @return : void
     */
    public function checkUserSession() {
        $segments = $this->CI->uri->segments;
        $methodName = $this->getUrl($segments);
        if($this->allowedUrls($methodName) !== "allowed") {
            $this->checkSession();
        }
    }
    
    /**
     *  @desc : This function will check if requested url is allowed without login or not
     *  @param : string (current url)
     *  @return : string (allowed )
     */
    function allowedUrls($currentUrl) {
        $allowedUrls = $this->allowedUrls;
        foreach ($allowedUrls as $allowedUrl) {
            if($allowedUrl == $currentUrl) {
                return "allowed";
            }
        }
    }
    
    /**
     *   @desc : Get Current URL
     *   @param : array( array of controller and method names)
     *   @return : string (controller and method name)
     */
    function getUrl($segments) {
        $checkLogin = null;
        if(sizeof($segments) >= 2) {
            $checkLogin = $segments[2];
        }
        else if(sizeof($segments) == 1) {
             $checkLogin = $segments[1];
        }
        return $checkLogin;
    }
    
    /**
     *  @desc : This function will check if user is logged in or not
     *  @return : redirects to login page
     */
    function checkSession() {
       if (($this->CI->session->userdata('loggedIn')!==TRUE)&&($this->CI->session->userdata('userType')!=='admin'))  {
        redirect('admin');
        } 
    }

    /*function checkSession() {
        if($this->CI->session->userdata('loggedIn') !== TRUE)  {
            echo "<script>window.location.href = '".base_url()."admin"."';</script>";
            //echo "<script>alert('ddsfssf');</script>";
        }
    }
    
    #########################################################################################################################
    #########################################################################################################################
    ####################################   For Session Time out (future use)   ##############################################
                
    #########################################################################################################################
    /**@desc function to set the session variables in this function
 	  *@param customer id, email and account id
	  *@return set session
	  */
 	function setSession($userId,$userName,$accountId) {
	    
    	$userSession = array('userId'=>$userId,
                         		 'email'       => $email,
                                'id'           => $id,
                         		'lastActivity' => $_SERVER["REQUEST_TIME"],
                         		'sess_expiration'=>3600,
                         		'loggedIn'        => TRUE,
                                'userType'        => 'admin' );
		$this->session->set_userdata($userSession);
 	}
 	
 	function checkSessionTimeout() {
 	     $last=$this->session->userdata('lastActivity');
	    $sess_out=$this->session->userdata('sess_expiration');

	    if($sess_out + $last < $this->input->server("REQUEST_TIME")){
	
			$this->session->sess_destroy();
			redirect(base_url());
		}
		else{

		    if($this->session->userdata('id')!='false'){
    
	           	$this->session->set_userdata('lastActivity', $this->input->server("REQUEST_TIME"));
            }
            else{
                redirect(base_url()."admin"); 
            }       
	    }           
 	}
    
}
