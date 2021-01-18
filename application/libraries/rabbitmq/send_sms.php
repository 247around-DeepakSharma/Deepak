<?php

include(__DIR__ . '/../../config/rabbitmq_constants.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Library to send SMS through RabbitMQ
 *
 * @author anujaggarwal
 */
class send_sms {
    var $My_CI;
    var $exchange = RABBITMQ_EXCHANGE_DEFAULT, $queue = RABBITMQ_QUEUE_SMS;
    var $channel;

    function __Construct() {
        log_message('info', __FUNCTION__ . " >>>");
        
        $this->My_CI = & get_instance();
        
        $connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, 
                RABBITMQ_USER, RABBITMQ_PASS, RABBITMQ_VHOST);
        $this->channel = $connection->channel();

        /*
            The following code is the same both in the consumer and the producer.
            In this way we are sure we always have a queue to consume from and an
                exchange where to publish messages.
        */

        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $this->channel->queue_declare($this->queue, false, true, false, false);

        /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */

        $this->channel->exchange_declare($this->exchange, 
                AMQPExchangeType::DIRECT, false, true, false);

        $this->channel->queue_bind($this->queue, $this->exchange);
        
        log_message('info', __FUNCTION__ . " <<<");
    }
    
    function send_sms_msg91($phone_number, $message) {
        $sms = json_encode(array('phone_number' => $phone_number, 'message' => $message));
        
        $message = new AMQPMessage($sms, array('content_type' => 'text/plain',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        
        //TODO: Exception handling needs to be relooked at
        try{        
            $this->channel->basic_publish($message, $this->exchange);
        } catch (RuntimeException $e) {
             echo "AMQP Exception: ", $e->getMessage(), "\n";
        }
    }

}
