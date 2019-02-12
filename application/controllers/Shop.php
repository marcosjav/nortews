<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Shop extends REST_Controller
{

    public function __construct()
        {
            parent::__construct();
            $this->load->model('shop_model');
        }


    public function list_get()
    {
        $this->set_response($this->shop_model->get_list($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

    public function fulllist_get()
    {
        $this->set_response($this->shop_model->get_full_list($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

    public function add_post()
    {
        $this->set_response($this->shop_model->insert($this->post()), REST_Controller::HTTP_OK);
    }

}