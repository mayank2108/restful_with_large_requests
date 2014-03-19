<?php
include(__DIR__ . '/config.php');
//require_once __DIR__ . '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RestClient {
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct() {
        $this->connection = new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "", false, false, true, false);
        $this->channel->basic_consume(
            $this->callback_queue, '', false, false, false, false,
            array($this, 'on_response'));
    }
    public function on_response($rep) {
        if($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($request) {
        $this->response = null;
        $this->corr_id = uniqid();

        $msg = new AMQPMessage(
            $request,
            array('correlation_id' => $this->corr_id,'content_type' => 'application/json',
                'reply_to' => $this->callback_queue)
        );
        $this->channel->basic_publish($msg, '', 'rpc_queue');
        while(!$this->response) {
            $this->channel->wait();
        }
        return $this->response;
    }
};

$raw = '';
$httpContent = fopen('php://input', 'r');
while ($kb = fread($httpContent, 1024)) {
    $raw .= $kb;
}

fclose($httpContent);
$params = array();
parse_str($raw, $params);

$request['server']=$_SERVER;
$request['request']=$_REQUEST;
$request['params']=$params;
$request['raw']=$raw;

$request =json_encode($request);
//print_r($request);
$restful = new RestClient();
$response = $restful->call($request);
print ($response);

?>