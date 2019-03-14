<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Stock extends REST_Controller
{

    public function __construct()
        {
            parent::__construct();
            $this->load->model('stock_model');
        }


    public function list_get()
    {
        $this->set_response($this->stock_model->get_list($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

    public function add_get()
    {
        $this->set_response($this->stock_model->insert($this->get()), REST_Controller::HTTP_OK);
    }

}