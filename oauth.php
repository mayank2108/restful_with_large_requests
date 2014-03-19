<?php

class Oauth
{
    public $request;
    public function get()
    {
        function getGUID(){
            if (function_exists('com_create_guid')){
                return com_create_guid();
            }else{
                mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
                $charid = strtoupper(md5(uniqid(rand(), true)));
                $hyphen = chr(45);// "-"
                $uuid = chr(123)// "{"
                    .substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12)
                    .chr(125);// "}"
                return $uuid;
            }
        }

        global $dbh;
        $param=array();

        /*if(isset($this->request->action)&&$this->request->action=='new')
        {
            $data['attributes']=['name','quantity','price','description','is_in_stock'];
            return $this->_response($data,200);

        }*/


        if($dbh->doLogin($this->request->REST_request['server']['PHP_AUTH_USER'],$this->request->REST_request['server']['PHP_AUTH_PW']))
        {
            $GUID = getGUID();
            $param['token']=$GUID;
            $param['username']=$this->request->REST_request['server']['PHP_AUTH_USER'];
            if($dbh->insert_token($param))
            {
                $response['token_type']='bearer';$response['access_token']=$GUID;
                return json_encode($response);
            }

        }

        else
        {
            $response['description']='Wrong username or password';$response['status']='200 OK';
            return json_encode($response);
        }
    }

    public function invalid(){

        $response['description']='Invalid request';$response['status']='200 OK';
        return json_encode($response);
    }


    public function execute($request) {

        //  echo 1;
        $this->request = $request;
        $this->id = $request->id;
        $this->params = $request->params;

        // print_r($request);

        switch ($this->request->method) {
            case 'GET':
                return $this->get();
                break;
             DEFAULT:
                return $this->invalid();
                break;
        }
    }
}