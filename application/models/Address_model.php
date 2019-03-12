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
			$this->db->join('city', 'address.city = city.id_city', 'left');
			$this->db->join('province', 'city.province = province.id_province', 'left');
			$this->db->join('country', 'province.country = country.id_country', 'left');

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
			else if ($values['province']) 
				$this->db->or_like('province.id_province', $values['province']);
			else if ($values['country']) 
				$this->db->or_like('country.id_country', $values['country']);


			$query = $this->db->get();

			return $query->result();

		}

		public function get_address($parms){
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

			$query = $this->db->get();

			return $query->result();
		}

		public function insert($parms){
        	$values = $this->get_values($parms);

			if ($values['street'] && $values['city'])
			{
				$data = array(
				        'street' => $values['street'],
				        'number' => $values['number'],
				        'zip_code' => $values['zip_code'],
				        'coordinates' => $values['coordinates'],
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
        	$values['province']   = (array_key_exists('province',	$parms) ? $parms['province']   : NULL);
        	$values['country']   = (array_key_exists('country',	$parms) ? $parms['country']   : NULL);
        	$values['id_company']   = (array_key_exists('id_company',	$parms) ? $parms['id_company']   : NULL);

        	return $values;
		}

		public function insert_col($parms){
			$ids = [];

			foreach ($parms as $v) {
				$addr = $this->get_values($v);
				$id = $this->insert($addr);
				if ($id)
					array_push($ids, $id);
			}
			return $ids;
		}


		public function get_company_addresses($parms){
			$values = $this->get_values($parms);

			if ($values['id_company']) {
				$this->db->select('*');
				$this->db->from('address');
				$this->db->join('company_has_address', 'company_has_address.address_id = address.id_address', 'left');

				$this->db->or_like('company_has_address.company_id',   $values['id_company']);

				return $this->db->get()->result();
			}
		}
}