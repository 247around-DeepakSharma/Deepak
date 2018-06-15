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
            
            //Getting Base Url
            $base_url=(isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'];
            $base_url.= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
            $_247_CRM = explode('/',$_SERVER['REQUEST_URI'])[1];
            //&& $_247_CRM !="employee"
            if (isset($config['is_offline']) && $config['is_offline'] === TRUE ) {
               
                $this->show_site_offline($base_url);
                exit;
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

        echo '<!DOCTYPE html>
<html>
<style>
body, html {
    height: 100%;
    margin: 0;
}

.bgimg {
    background-image: url("'.$base_url.'images/background_image.png");
    height: 100%;
    background-position: center;
    background-size: cover;
    position: relative;
    color: white;
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
    top: 50%;
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
    <h1 style="font-size:50px;">We will be back shortly!</h1>
    <h5>We are upgrading our CRM to make it better & stronger</h5>
    <hr>
    <p id="demo" style="font-size:30px"></p>
  </div>
  <div class="bottomleft">
   
  </div>
</div>

<script>

var countDownDate = new Date("June 15, 2018 21:00:00").getTime();

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