<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Covid19 extends CI_Controller {

    function __Construct() {
        parent::__Construct();
    }
    /**
     * @desc This function is used to get Crona positive district wise of all state. For this we are using Rapid API
     */
    function index() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://corona-virus-world-and-india-data.p.rapidapi.com/api_india",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "x-rapidapi-host: corona-virus-world-and-india-data.p.rapidapi.com",
                "x-rapidapi-key: 82e5f65a5emshaf78d7b408460a1p11dab6jsnae2a248c3637"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            //print_r(json_decode($response, true));
            echo $response;
        }
    }

}
