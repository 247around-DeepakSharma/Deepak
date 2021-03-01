<?php

class PHPFatalError {

    public function setHandler() {
        register_shutdown_function('handleShutdown');
    }

}

function handleShutdown() {
     $error = error_get_last();
    if ($error['type'] === E_ERROR) {
        $buffer = ob_get_contents();
        if(ob_get_length() > 0) {
            ob_clean();
        }
        //$msg = $buffer;
        $CI = get_instance();
        if(empty($CI->email)){
            // Email class is not loaded
            exit();
        }
        $CI->email->clear(TRUE);
        $CI->email->from(SYS_HEALTH_EMAIL, SYS_HEALTH_NAME);
        $CI->email->to(FATAL_ERROR_EMAIL);
        $CI->email->subject('FATAL ERROR');
        $msg = "<b>Error Message:</b>";
        $msg .= $error['message'];
        $msg .= "<br><br><br>";
        $msg .= "<b>Error File:</b>";
        $msg .= $error['file'];
        $msg .= "<br><br><br>";
        $msg .= "<b>Error Line:</b>";
        $msg .= $error['line'];
        $CI->email->message($msg);
        $CI->email->send();
        exit();
    }
}
