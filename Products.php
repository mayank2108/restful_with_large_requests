<?php

class Products extends  ProductsController {

    public $request;

    public function get()
    {

        echo '->>>>';
        print_r($this->request->REST_request['request']);

        global $dbh;
        $param=array();

        if(isset($this->request->action)&&$this->request->action=='new')
        {
            $data['attributes']=['name','quantity','price','description','is_in_stock'];
            return $this->_response($data,200);

        }

        else
        {

            // print_r($_REQUEST);
            if(isset($this->request->id))
            {
                $param['id']=$this->request->id;

            }


            else if(isset($this->request->REST_request['request']['searchtxt']) && strlen($this->request->REST_request['request']['searchtxt'])>=1)
            {
                $param['searchtxt']=$this->request->REST_request['request']['searchtxt'];

            }

            print_r($param);
            $data['products']=$dbh->read($param);

            //  print_r($param);

            if(isset($data['products'][0]))
                return $this->_response($data,200);

            else
            {
                $data='';
                $data['description']='The product you were looking for is not available';
                $data['error']='404 Not Found';
                return $this->_response($data,404);

            }
        }
    }

    public function destroy()
    {
        if(isset($this->request->REST_request['request']['access_token']))
        {
            global $dbh;
            if($dbh->check_token($this->request->REST_request['request']['access_token']))
            {
                if(isset($this->request->id))
                {
                    $param['id']=$this->request->id;
                    global $dbh;
                    if($dbh->destroy($param)>0)
                    {
                        $data['description']='Deleted product with id:'.$param['id'];
                        return $this->_response($data,200);
                    }

                    else
                    {
                        $data['description']='Cannot delete product with id:'.$param['id'];
                        return $this->_response($data,404);
                    }
                }
            }

            else
            {
                $data['description']='Invalid access token';
                return $this->_response($data,200);
            }


        }

        else
        {
            $data['description']='Provide your access token';
            return $this->_response($data,200);
        }


    }

    public function create()
    {
        print_r($this->request->REST_request);
        if(isset($this->request->REST_request['request']['access_token']))
        {
            global $dbh;
            if($dbh->check_token($this->request->REST_request['request']['access_token']))
            {

                global $dbh;

                $id=$dbh->insert($this->params);
                if(isset($id)&&$id>0)
                {
                    $data['description']='Product created with id:'.$id;
                    return $this->_response($data,201);
                }

                else
                {
                    $data['description']='Product not created';
                    return $this->_response($data,200);

                }
            }
            else
            {
                $data['description']='Invalid access token';
                return $this->_response($data,200);
            }

        }
        else
        {
            $data['description']='Provide access token';
            return $this->_response($data,200);
        }
    }

    public function update()
    {
        if(isset($this->request->REST_request['request']['access_token']))
        {
            global $dbh;
            if($dbh->check_token($this->request->REST_request['request']['access_token']))
            {


                if(isset($this->request->id))
                {
                    $param['id']=$this->request->id;
                    print_r($param);
                    print_r($this->params);
                    global $dbh;
                    $rec = $dbh->read($param);
                    if($rec)
                    {
                        if($dbh->update($this->request->id,$this->params)>0)
                        {
                            $data['description']='Product updated with id:'.$this->request->id;
                            return $this->_response($data,201);
                        }
                        else
                        {
                            $data['description']='Product not updated';
                            return $this->_response($data,200);
                        }
                    }

                    else
                    {
                        $data['description']='Wrong Parameters or not found';
                        return $this->_response($data,404);
                    }

                }
            }
            else
            {
                $data['description']='Invalid access token';
                return $this->_response($data,200);
            }
        }
        else
        {
            $data['description']='Provide access token';
            return $this->_response($data,200);
        }


    }

}


?>