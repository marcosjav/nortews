<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Person extends REST_Controller
{

    public function __construct()
        {
            parent::__construct();
            $this->load->model('person_model');
        }


    /**
     * URL: http://localhost/CodeIgniter-JWT-Sample/auth/token
     * Method: GET
     */
    public function list_get()
    {
        // $tokenData = array();
        // $tokenData['id'] = 1; //TODO: Replace with data for token
        // $output['token'] = AUTHORIZATION::generateToken($tokenData);
        // $this->set_response($output, REST_Controller::HTTP_OK);
        $this->set_response($this->person_model->get_list(), REST_Controller::HTTP_OK);
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
    // public function token_post()
    // {
    //     $headers = $this->input->request_headers();

    //     if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
    //         $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
    //         if ($decodedToken != false) {
    //             $this->set_response($decodedToken, REST_Controller::HTTP_OK);
    //             return;
    //         }
    //     }

    //     $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
    // }
}