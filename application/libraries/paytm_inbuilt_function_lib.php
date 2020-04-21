<?php
class paytm_inbuilt_function_lib{
function encrypt_e_openssl($input, $ky)
{
    $iv   = "@@@@&&&&####$$$$";
    $data = openssl_encrypt($input, "AES-128-CBC", $ky, 0, $iv);
    return $data;
}

function decrypt_e_openssl($crypt, $ky)
{
    $iv   = "@@@@&&&&####$$$$";
    $data = openssl_decrypt($crypt, "AES-128-CBC", $ky, 0, $iv);
    return $data;
}

function generateSalt_e($length)
{
    $random = "";
    srand((double) microtime() * 1000000);
    $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
    $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
    $data .= "0FGH45OP89";
    for ($i = 0; $i < $length; $i++) {
        $random .= substr($data, (rand() % (strlen($data))), 1);
    }
    return $random;
}

function checkString_e($value) {
	if ($value == 'null')
		$value = '';
	return $value;
}

function getChecksumFromArray($arrayList, $key, $sort = 1)
{
    if ($sort != 0) {
        ksort($arrayList);
    }
    $str  =  $this->getArray2Str($arrayList);
    $salt = $this->generateSalt_e(4);
    $finalString = $str . "|" . $salt;
    $hash        = hash("sha256", $finalString);
    $hashString  = $hash . $salt;
    $checksum    = $this->encrypt_e_openssl($hashString, $key);
    return $checksum;
}

function getChecksumFromString($str, $key)
{
    $salt        = $this->generateSalt_e(4);
    $finalString = $str . "|" . $salt;
    $hash        = hash("sha256", $finalString);
    $hashString  = $hash . $salt;
    $checksum    = $this->encrypt_e_openssl($hashString, $key);
    return $checksum;
}

function verifychecksum_e($arrayList, $key, $checksumvalue)
{
    $arrayList = $this->removeCheckSumParam($arrayList);
    ksort($arrayList);
    $str          = $this->getArray2Str($arrayList);
    $paytm_hash   = $this->decrypt_e_openssl($checksumvalue, $key);
    $salt         = substr($paytm_hash, -4);
    $finalString  = $str . "|" . $salt;
    $website_hash = hash("sha256", $finalString);
    $website_hash .= $salt;
    $validFlag = "FALSE";
    if ($website_hash == $paytm_hash) {
        $validFlag = "TRUE";
    } else {
        $validFlag = "FALSE";
    }
    return $validFlag;
}

function verifychecksum_eFromStr($str, $key, $checksumvalue)
{
    $paytm_hash   = decrypt_e_openssl($checksumvalue, $key);
    $salt         = substr($paytm_hash, -4);
    $finalString  = $str . "|" . $salt;
    $website_hash = hash("sha256", $finalString);
    $website_hash .= $salt;
    $validFlag = "FALSE";
    if ($website_hash == $paytm_hash) {
        $validFlag = "TRUE";
    } else {
        $validFlag = "FALSE";
    }
    return $validFlag;
}

function getArray2Str($arrayList)
{
    $findme     = 'REFUND';
    $findmepipe = '|';
    $paramStr   = "";
    $flag       = 1;
    foreach ($arrayList as $key => $value) {
        $pos     = strpos($value, $findme);
        $pospipe = strpos($value, $findmepipe);
        if ($pos !== false || $pospipe !== false) {
            continue;
        }
        
        if ($flag) {
            $paramStr .= $this->checkString_e($value);
            $flag = 0;
        } else {
            $paramStr .= " | " . $this->checkString_e($value);
        }
    }
    return $paramStr;
}

function redirect2PG($paramList, $key)
{
    $hashString = getchecksumFromArray($paramList);
    $checksum   = encrypt_e_openssl($hashString, $key);
}

function removeCheckSumParam($arrayList)
{
    if (isset($arrayList[“CHECKSUMHASH”])) {
        unset($arrayList[“CHECKSUMHASH”]);
    }
    return $arrayList;
}

function getTxnStatus($requestParamList)
{
    return callAPI(PAYTM_STATUS_QUERY_URL, $requestParamList);
}

function initiateTxnRefund($requestParamList)
{
    $CHECKSUM = getChecksumFromArray($requestParamList, PAYTM_MERCHANT_KEY, 0);
    $requestParamList["CHECKSUM"] = $CHECKSUM;
    return callAPI(PAYTM_REFUND_URL, $requestParamList);
}

function callAPI($apiURL, $requestParamList)
{
    $jsonResponse      = "";
    $responseParamList = array();
    $JsonData          = json_encode($requestParamList);
    $postData          = urlencode($JsonData);
    $ch                = curl_init($apiURL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length:' . strlen($postData)
    ));
    $jsonResponse      = curl_exec($ch);
    $responseParamList = json_decode($jsonResponse, true);
    return $responseParamList;
}
}
