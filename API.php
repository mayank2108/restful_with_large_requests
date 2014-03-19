<?php
class RESTFUL
{
    public  $method, $controller, $action, $id, $params,$REST_request;

    public function __construct($REST_request)
    {
        /*header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        */
        $this->REST_request=$REST_request;
        header("Content-Type: application/json");
        $this->method=$REST_request['server']['REQUEST_METHOD'];

        if ($this->method == 'PUT') {
            /* $raw = '';
             $httpContent = fopen('php://input', 'r');
             while ($kb = fread($httpContent, 1024)) {
                 $raw .= $kb;
             }

             fclose($httpContent);
             $params = array();
             parse_str($raw, $params);*/


            if (isset($REST_request['request']['data'])) {
                $this->params = json_decode(stripslashes($REST_request['request']['data']));

            }
            else {
                $params = json_decode(stripslashes($REST_request['raw']));
                $this->params = $params;
            }
        }
        else {

            $this->params = (isset($REST_request['request']['data'])) ? json_decode(stripslashes($REST_request['request']['data'])) : null;

            if (isset($REST_request['request']['data'])) {
                $this->params = json_decode(stripslashes($REST_request['request']['data']));
            } else {
                /* $raw = '';
                 $httpContent = fopen('php://input', 'r');
                 while ($kb = fread($httpContent, 1024)) {
                     $raw .= $kb;
                 }*/
                $params = json_decode(stripslashes($REST_request['raw']));
                if ($params) {
                    $this->params = $params;
                    echo 'saasf';
                    print_r($this->params);

                }
            }

        }

        if (isset($REST_request['server']["PATH_INFO"])) {



            $cai = '/^\/([a-z]+\w)\/([a-z]+\w)\/([0-9]+)$/'; // /controller/action/id
            $ca = '/^\/([a-z]+\w)\/([a-z]+)$/'; // /controller/action
            $ci = '/^\/([a-z]+\w)\/([0-9]+)$/'; // /controller/id
            $c = '/^\/([a-z]+\w)$/'; // /controller
            $i = '/^\/([0-9]+)$/'; // /id
            $matches = array();
            if (preg_match($cai, $REST_request['server']["PATH_INFO"], $matches)) {
                $this->controller = $matches[1];
                $this->action = $matches[2];
                $this->id = $matches[3];
            } else if (preg_match($ca, $REST_request['server']["PATH_INFO"], $matches)) {
                $this->controller = $matches[1];
                $this->action = $matches[2];
            } else if (preg_match($ci, $REST_request['server']["PATH_INFO"], $matches)) {
                $this->controller = $matches[1];
                $this->id = $matches[2];
            } else if (preg_match($c, $REST_request['server']["PATH_INFO"], $matches)) {

                //echo '1';

                $this->controller = $matches[1];
            } else if (preg_match($i, $REST_request['server']["PATH_INFO"], $matches)) {
                $this->id = $matches[1];
            }
        }


    }
}