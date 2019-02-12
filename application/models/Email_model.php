<?php
class Email_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

		public function get_list($parms)
		{
			$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('email');

			if ($values['id_email']) $this->db->or_where('email.id_email', $values['id_email']);
			if ($values['address_email']) $this->db->or_like('email.address_email', $values['address_email']);

			$query = $this->db->get();
			return $query->result();
		}

		public function insert($parms){

			$values = $this->get_values($parms);

			if ($values['address_email'])
			{
				$data = array(
				        'address_email' => $values['address_email']
				);

				$this->db->insert('email', $data);
				return $this->db->insert_id();
			}

			return NULL;
		}

		private function get_values($parms){
			$values = [];

			$values['id_email'] = (array_key_exists('id_email',	$parms) ? $parms['id_email']   : NULL);
			$values['address_email'] = (array_key_exists('address_email',	$parms) ? $parms['address_email']   : NULL);

        	return $values;
		}
}