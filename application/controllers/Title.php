<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Title extends REST_Controller
{

    public function __construct()
        {
            parent::__construct();
            $this->load->model('title_model');
        }


    /*  FULL list  */
    public function list_get()
    {
        $this->set_response($this->title_model->get_list($this->get()), REST_Controller::HTTP_OK);
    }

    /*   TITLES LIST   */
    public function titles_get()
    {
        $this->set_response($this->title_model->get_title_list($this->get()), REST_Controller::HTTP_OK);
    }

    /*   SUBTITLES LIST   */
    public function subtitles_get()
    {
        $this->set_response($this->title_model->get_subtitle_list($this->get()), REST_Controller::HTTP_OK);
    }

    public function add_title_get()
    {
        $this->set_response($this->title_model->insert_title($this->get()), REST_Controller::HTTP_OK);
    }

    public function add_subtitle_get()
    {
        $this->set_response($this->title_model->insert_subtitle($this->get()), REST_Controller::HTTP_OK);
    }

}