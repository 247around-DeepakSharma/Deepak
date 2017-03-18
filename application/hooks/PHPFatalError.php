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
        ob_clean();
        //$msg = $buffer;
        $CI = get_instance();
        $CI->email->clear(TRUE);
        $CI->email->from('booking@247around.com', '247around Team');
        $CI->email->to(DEVELOPER_EMAIL);
        $CI->email->subject('fatel error');
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
