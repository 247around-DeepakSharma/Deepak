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
            $_247_CRM = explode('/',$_SERVER['PATH_INFO'])[1];
            
            if (isset($config['is_offline']) && $config['is_offline'] === TRUE && $_247_CRM != "employee") {
                $this->show_site_offline($base_url);
                exit;
            }
        }
    }

    private function show_site_offline($base_url) {
        
        echo '<html><body><div style="margin:5% 10% 10% 20%;position: relative;overflow: hidden;"><img src="'. $base_url .'images/maintenance.jpg"  style="max-width:100%;"/>
        <div style=" border: 2px solid #73ad21;
                    border-radius: 25px;
                    max-width:770px;
                    width: auto;
                    height:auto;
                    position: relative;
                    margin:-5px 5px 5px 5px auto;">
           <span style="margin-left:35%">&nbsp;&nbsp;&nbsp;Regards, 247Around Team</span>
        </div></div></body></html>';
    }
    
}