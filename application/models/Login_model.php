<?php
class Login_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

		public function login($username = FALSE, $password = FALSE)
		{
	        $query = $this->db->get_where('user', array('password' => $password, 'username' => $username));
	        $user = $query->row_array();
	        unset($user['password']);
	        return $user;
		}

		
}