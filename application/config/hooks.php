<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'][] = array(
                       'class' => 'site_offline',
                       'function' => 'is_offline', 
                       'filename' => 'site_offline.php',
                       'filepath' => 'hooks'
);

//$hook['post_controller_constructor'][] = array(
//                                'class'    => 'UserSession',
//                                'function' => 'checkUserSession',
//                                'filename' => 'session.php',
//                                'filepath' => 'hooks'
//                                );

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
