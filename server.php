<?php
include_once('API.php');
include_once('Controller.php');
include_once('Products.php');
include_once('oauth.php');
include_once('db.php');
include(__DIR__ . '/config.php');
/**
 * Created by PhpStorm.
 * User: mayank
 * Date: 18/3/14
 * Time: 1:53 AM
 */
//require_once __DIR__ . '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection =new AMQPConnection(HOST, PORT, USER, PASS, VHOST);
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', false, false, false, false);


echo " [x] Awaiting RPC requests\n";
$callback = function($req) {

    $RESTresponse=get_object_vars(json_decode($req->body));
    $RESTresponse['server']=get_object_vars($RESTresponse['server']);
    $RESTresponse['request']=get_object_vars($RESTresponse['request']);
    if(isset($RESTresponse['params']))
    $RESTresponse['params']=get_object_vars($RESTresponse['params']);
    $RESTresponse['raw']=get_object_vars($RESTresponse['raw']);

    print_r($RESTresponse);

    $restful=new RESTFUL($RESTresponse);
    $controller=ucfirst($restful->controller);
    $controller= new $controller;
    global $dbh;
    $dbh=new db();

    $response=$controller->execute($restful);
  //  print_r($response);

    $msg = new AMQPMessage(
        $response,
        array('correlation_id' => $req->get('correlation_id'),'content_type' => 'application/json')
    );

    $req->delivery_info['channel']->basic_publish(
        $msg, '', $req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack(
        $req->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>