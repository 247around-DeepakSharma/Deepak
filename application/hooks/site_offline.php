<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *@desc: This is called using Pre-System Hooks
 * 
 * It is used to show Site Under Maintenace mode when $config['is_offline'] is set to TRUE 
 */
class Site_Offline extends CI_Hooks {

    function __construct() {

    }

    public function is_offline() {
        if (file_exists(APPPATH . 'config/config.php')) {
            include(APPPATH . 'config/config.php');
            
            $this->My_CI = & get_instance();
            $this->My_CI->load->library("session");
             
            //Getting Base Url
            $base_url=(isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'];
            $base_url.= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            if (isset($config['is_offline']) && $config['is_offline'] === TRUE ) {
               if ($this->My_CI->session->userdata('loggedIn') == TRUE) {
                    if(($this->My_CI->session->userdata('userType') == 'employee') &&( ($this->My_CI->session->userdata('id') == 1) || ($this->My_CI->session->userdata('id') == 36))){
                         //Here we allow to access crm
                    }else  if(($this->My_CI->session->userdata('userType') == 'partner') && ($this->My_CI->session->userdata('agent_id') == 3) ){
                         //Here we allow to access crm
                    } else if(($this->My_CI->session->userdata('userType') == 'service_center') && (($this->My_CI->session->userdata('service_center_agent_id') == 1548) || ($this->My_CI->session->userdata('service_center_agent_id') == 1568))){
                         //Here we allow to access crm
                    }  else {
                        $this->My_CI->session->sess_destroy();
                        $this->show_site_offline($base_url);
                        exit;
                    }
                    
                } else {
                    $devmode = explode('/',$_SERVER['REQUEST_URI']);
                    if(isset($devmode[2]) && !empty($devmode[2]) && $devmode[2] == $config['developer_mode']){
                        //Here we allow to login
                    } else {
                        //$this->show_site_offline($base_url);
                       // exit;
                    }
                    
                }
            }
        }
    }

    private function show_site_offline($base_url) {
        
//        echo '<html><body><div style="margin:5% 10% 10% 20%;position: relative;overflow: hidden;"><img src="http://247around-adminp-aws/images/maintenance.jpg"  style="max-width:100%;"/>
//        <div style=" border: 2px solid #73ad21;
//                    border-radius: 25px;
//                    max-width:770px;
//                    width: auto;
//                    height:auto;
//                    position: relative;
//                    margin:-5px 5px 5px 5px auto;">
//           <span style="margin-left:35%">&nbsp;&nbsp;&nbsp;Regards, 247Around Team</span>
//        </div></div></body></html>';
        
        //background-image: url("'.$base_url.'images/background_image.png");

        echo '<!DOCTYPE html>
<html>
<style>
body, html {
    height: 100%;
    margin: 0;
}

.bgimg {
    height: 100%;
    background-position: center;
    background-size: cover;
    position: relative;
    color: #000000;
    font-family: "Courier New", Courier, monospace;
    font-size: 25px;
}

.topleft {
    position: absolute;
    top: 0;
    left: 16px;
}

.bottomleft {
    position: absolute;
    bottom: 0;
    left: 16px;
}

.middle {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

hr {
    margin: auto;
    width: 40%;
}
</style>
<body>

<div class="bgimg">
  <div class="topleft">
    <p><img src="'.$base_url.'images/logo_small.png" ></p>
  </div>
  
  <div class="middle">
  <p><center><img src = "'.$base_url.'images/news_maintenance201302_small.jpg"></center></p>
    <h1 style="font-size:50px; color:green">We will be back shortly!</h1>
    <h5>We are upgrading our CRM to make it better & stronger</h5>
    <hr>
    <p id="demo" style="font-size:30px"></p>
  </div>
  <div class="bottomleft">
   
  </div>
</div>

<script>

var countDownDate = new Date("Aug 26, 2020 20:30:00").getTime();

// Update the count down every 1 second
var countdownfunction = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
    
    // Find the distance between now an the count down date
    var distance = countDownDate - now;
    
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    // Output the result in an element with id="demo"
    document.getElementById("demo").innerHTML = days + "d " + hours + "h "
    + minutes + "m " + seconds + "s ";
    
    // If the count down is over, write some text 
    if (distance < 0) {
        clearInterval(countdownfunction);
        document.getElementById("demo").innerHTML = "EXPIRED";
    }
}, 1000);
</script>

</body>
</html>';
    }
    
}
