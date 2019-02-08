<?php
class Person_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

		// public function get_user($username = FALSE, $password = FALSE)
		// {
		//         $query = $this->db->get_where('user', array('password' => $password, 'username' => $username));
		//         return $query->row_array();
		// }

		/*
			Get full persons list
		*/
		public function get_list($document = FALSE)
		{
			if ($document === FALSE)
		        {
		            $query = $this->db->get('person');
		            return $query->result_array();
		        }

            $query = $this->db->get_where('person', array('id' => $document));
            return $query->result_array();
		}
		
		// public function set_news()
		// {
		//     $this->load->helper('url_helper');

		//     $slug = url_title($this->input->post('title'), 'dash', TRUE);

		//     $data = array(
		//         'title' => $this->input->post('title'),
		//         'slug' => $slug,
		//         'text' => $this->input->post('text')
		//     );

		//     return $this->db->insert('news', $data);
		// }
}