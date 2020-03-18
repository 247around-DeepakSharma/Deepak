<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure HTTP basic authorization: basicAuth
$config = Karix\Configuration::getDefaultConfiguration();
$config->setUsername('be546f8d-468b-4e75-a93b-7159ceaeb2b0');
$config->setPassword('d01bb1f0-cd7e-469f-b3f7-d61cc76223b0');

$apiInstance = new Karix\Api\MessageApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$message = new Karix\Model\CreateMessage(); // Karix\Model\CreateAccount | Subaccount object

date_default_timezone_set('UTC');

$message->setChannel("sms"); // Use "sms" or "whatsapp"
$message->setDestination(["+917275746702"]);
$message->setSource("+13253077759");
$message->setContent([
	"text" => "Hello Prity . this is test SMS",
]);

try {
    $result = $apiInstance->sendMessage($message);
    echo "<pre>";
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling MessageApi->createMessage: ', $e->getMessage(), PHP_EOL;
}

?>
