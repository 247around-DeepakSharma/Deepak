<?php
$db->select('*');
$db->where('active', 1);
$query = $db->get('third_party_api_credentials');
$data = $query->result_array();
if(!empty($data)){
    foreach ($data as $key => $value) {
        define($value['constant_tag'], $value['secret_key']);
    }
}



//define ('SENDGRID_API_KEY','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');
//define('MERCHANT_GUID','d0fb852e-7a86-4235-9044-4c17910eaff0');
//define('MID','247PAR00642934549295');
//define('PAYTM_MERCHANT_KEY', 'GLmqfJkuK0UsEX&Z');
//define('PUSH_NOTIFICATION_API_KEY','5e80dc70981389335ae38d969ca075be');
//define('_247AROUND_CRM_GGL_ANALYTICS_TAG_ID', 'UA-115612033-1');
//define("GOOGLE_URL_SHORTNER_KEY", "AIzaSyAx1favE357DJusgkTXhFItRwfOIMvaD9w");
//define('TRACKINGMORE_PROD_API_KEY','06ec4a31-6125-4b01-9da9-bf8d420767df');
//define('MSG91_AUTH_KEY', '141750AFjh6p9j58a80789');
//define('MSG91_SENDER_NAME', 'AROUND');
//define('GOOGLE_MAPS_API_KEY', 'AIzaSyB4pxS4j-_NBuxwcSwSFJ2ZFU-7uep1hKc');