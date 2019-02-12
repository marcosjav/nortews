<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Location extends REST_Controller
{

    public function __construct()
        {
            parent::__construct();
            $this->load->model('location_model');
        }


    public function list_get()
    {
        $this->set_response($this->location_model->get_list($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

    public function add_get()
    {
        $this->set_response($this->location_model->insert($this->get()), REST_Controller::HTTP_OK);
    }

    public function city_get()
    {
        $this->set_response($this->location_model->get_city($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

    public function province_get()
    {
        $this->set_response($this->location_model->get_province($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

    public function country_get()
    {
        $this->set_response($this->location_model->get_country($this->get()), REST_Controller::HTTP_OK);
        // $this->set_response($this->get(), REST_Controller::HTTP_OK);
    }

}