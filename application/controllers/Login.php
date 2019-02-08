<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Login extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
    }

    public function tosha1_get()
    {
        $this->set_response(sha1($this->get('str')), REST_Controller::HTTP_OK);
    }

    /**
     * URL: http://localhost/CodeIgniter-JWT-Sample/auth/token
     * Method: POST
     * Header Key: Authorization
     * Value: Auth token generated in GET call
     */
    public function login_post()
    {
        $headers = $this->input->request_headers();

        if ((array_key_exists('Username', $headers) && array_key_exists('Password', $headers)) && (!empty($headers['Username']) && !empty($headers['Password']))) {
            
            $user = $this->login_model->login($headers['Username'], sha1($headers['Password']));
            if ($user != false) {
                $output['token'] = AUTHORIZATION::generateToken($user);
                $this->set_response($output, REST_Controller::HTTP_OK);
                return;
            }

        }

        $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
    }
}