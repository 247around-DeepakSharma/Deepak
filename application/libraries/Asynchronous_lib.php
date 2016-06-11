<?php

class Asynchronous_lib {

    public function __construct() {
	$this->ci = & get_instance();
    }

    /**
     *  @desc : Send Curl Request to server
     *  @param : String(URL)
     *  @param : Array(Service center)
     *  @return : void
     */
    function do_background_process($url, $params) {
	log_message('info', "Entering: " . __METHOD__);
	log_message('info', "URL: " . $url . ", Params: " . print_r($params, TRUE));

	$post_string = http_build_query($params);
	$parts = parse_url($url);
	$errno = 0;
	$errstr = "";

	//Use SSL & port 443 for secure servers
	//Use otherwise for localhost and non-secure servers
	//For secure server
	$fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 30);
	//For localhost
	//$fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);

	if (!$fp) {
	    echo "Error occured while opening the socket: " . $errno . " Msg: " . $errstr;
	    log_message('info', "Error occured while opening the socket: " . $errno . " Msg: " . $errstr);
	}

	$out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
	$out.= "Host: " . $parts['host'] . "\r\n";
	$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
	$out.= "Content-Length: " . strlen($post_string) . "\r\n";
	$out.= "Connection: Close\r\n\r\n";

	if (isset($post_string))
	    $out .= $post_string;

	fwrite($fp, $out);
	fclose($fp);
    }

}

?>
