<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Company extends REST_Controller
{

    public function __construct()
        {
            parent::__construct();
            $this->load->model('company_model');
        }


    public function list_get()
    {
        $this->set_response($this->company_model->get_list($this->get()), REST_Controller::HTTP_OK);
    }

    public function add_post()
    {
        $this->set_response($this->company_model->insert($this->post()), REST_Controller::HTTP_OK);
        // $this->set_response($this->company_model->get_values($this->get()), REST_Controller::HTTP_OK);
    }

    public function probar_get(){
        $this->set_response($this->company_model->probar($this->get()), REST_Controller::HTTP_OK);
    }

}