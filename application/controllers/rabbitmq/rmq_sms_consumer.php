<?php

/**
 * Consumer app for Sending SMS to mobile devices.
 *
 * @author anujaggarwal
 */
include(__DIR__ . '/../../config/rabbitmq_constants.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class Rmq_sms_consumer extends CI_Controller {

    function __Construct() {
        parent::__Construct();
    }

    public function rmq_sms_consumer_test($a = "a", $b = "b") {
        log_message('info', __FUNCTION__ . ": looks like things are working");
        echo "looks like things are working" . PHP_EOL;

        echo "A = " . $a . PHP_EOL;
        echo "B = " . $b . PHP_EOL;
    }

    function rmq_sms_consumer_start() {
        $consumerTag = 'consumer';

        try {
            $connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT,
                    RABBITMQ_USER, RABBITMQ_PASS, RABBITMQ_VHOST);
//            $connection->connect();
            $channel = $connection->channel();
        } catch (Exception $e) {
            
        }

        

        /*
          name: $queue
          passive: false
          durable: true // the queue will survive server restarts
          exclusive: false // the queue can be accessed in other channels
          auto_delete: false //the queue won't be deleted once the channel is closed.
         */
        $channel->queue_declare(RABBITMQ_QUEUE_SMS,
                RABBITMQ_QUEUE_DECLARE_PASSIVE_FALSE,
                RABBITMQ_QUEUE_DECLARE_DURABLE_TRUE,
                RABBITMQ_QUEUE_DECLARE_EXCLUSIVE_FALSE,
                RABBITMQ_QUEUE_DECLARE_AUTODELETE_FALSE);

        /*
          name: $exchange
          type: direct
          passive: false
          durable: true // the exchange will survive server restarts
          auto_delete: false //the exchange won't be deleted once the channel is closed.
         */

        $channel->exchange_declare(RABBITMQ_EXCHANGE_DEFAULT, AMQPExchangeType::DIRECT,
                RABBITMQ_EXCHANGE_DECLARE_PASSIVE_FALSE,
                RABBITMQ_EXCHANGE_DECLARE_DURABLE_TRUE,
                RABBITMQ_EXCHANGE_DECLARE_AUTODELETE_FALSE);

        $channel->queue_bind(RABBITMQ_QUEUE_SMS, RABBITMQ_EXCHANGE_DEFAULT);

        /*
          queue: Queue from where to get the messages
          consumer_tag: Consumer identifier
          no_local: Don't receive messages published by this consumer.
          no_ack: If set to true, automatic acknowledgement mode will be used by this consumer.
          See https://www.rabbitmq.com/confirms.html for details.
          exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
          nowait:
          callback: A PHP Callback
         */

        $channel->basic_consume(RABBITMQ_QUEUE_SMS, $consumerTag,
                RABBITMQ_CHANNEL_CONSUME_NOLOCAL_FALSE, RABBITMQ_CHANNEL_CONSUME_AUTOACK_FALSE,
                RABBITMQ_CHANNEL_CONSUME_EXCLUSIVE_FALSE, RABBITMQ_CHANNEL_CONSUME_NOWAIT_FALSE,
                [$this, 'rmq_sms_process_message']);

        register_shutdown_function([$this, 'rmq_sms_consumer_shutdown'], $channel,
                $connection);

        // Loop as long as the channel has callbacks registered
        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    /**
     * @param \PhpAmqpLib\Message\AMQPMessage $message
     */
    function rmq_sms_process_message($message) {
        $sms = json_decode($message->body, true);
        
        echo "\n--------\n";
        echo  "Ph: ". $sms['phone_number'] . ", Msg: " . $sms['message'];
        echo "\n--------\n";
        
        //Enable this when you want to actually send SMS
//        //Send SMS through MSG91 platform
//        $m = urlencode($sms['message']);
//        $url = "https://control.msg91.com/api/sendhttp.php?authkey=" . MSG91_AUTH_KEY . "&mobiles="
//                . $sms['phone_number'] . "&message=" . $m
//                . "&sender=" . MSG91_SENDER_NAME . "&route=4&country=91";
//        
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_exec($ch);
//        curl_close($ch);

        //Save this SMS in database
        //TODO
        
        //Send ack to client
        $message->ack();
    }

    /**
     * @param \PhpAmqpLib\Channel\AMQPChannel $channel
     * @param \PhpAmqpLib\Connection\AbstractConnection $connection
     */
    function rmq_sms_consumer_shutdown($channel, $connection) {
        $channel->close();
        $connection->close();
    }

}
