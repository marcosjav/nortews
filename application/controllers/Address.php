<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Address extends REST_Controller
{

    public function __construct()
        {
            parent::__construct();
            $this->load->model('address_model');
        }


    public function list_get()
    {
        $this->set_response($this->address_model->get_list($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

    public function address_get()
    {
        $this->set_response($this->address_model->get_address($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

    public function add_get()
    {
        $this->set_response($this->address_model->insert($this->get()), REST_Controller::HTTP_OK);
    }

}