<?php
class Address_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	
        	$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('address');

			if ($values['id_address']) 
				$this->db->or_where('address.id_address', $values['id_address']);
			if ($values['street']) 
				$this->db->or_like('address.street', $values['street']);
			if ($values['number']) 
				$this->db->or_like('address.number', $values['number']);
			if ($values['zip_code']) 
				$this->db->or_like('address.zip_code', $values['zip_code']);
			if ($values['city']) 
				$this->db->or_like('address.city', $values['city']);

			$query = $this->db->get();

			return $query->result();

		}

		public function insert($parms){
        	$values = $this->get_values($parms);

			if ($values['street'] && $values['city'])
			{
				$data = array(
				        'street' => $values['street'],
				        'city' => $values['city']
				);

				$this->db->insert('address', $data);
				return $this->db->insert_id();
			}

			return NULL;
		}

		private function get_values($parms){
			$values = [];

			// ítem
			$values['id_address'] = (array_key_exists('id_address',	$parms) ? $parms['id_address']   : NULL);
        	$values['street']   = (array_key_exists('street',	$parms) ? $parms['street']   : NULL);
        	$values['number']   = (array_key_exists('number',	$parms) ? $parms['number']   : 0);
        	$values['zip_code']   = (array_key_exists('zip_code',	$parms) ? $parms['zip_code']   : 0);
        	$values['coordinates']   = (array_key_exists('coordinates',	$parms) ? $parms['coordinates']   : NULL);
        	$values['city']   = (array_key_exists('city',	$parms) ? $parms['city']   : NULL);

        	return $values;
		}
}