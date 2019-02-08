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
			//$this->db->join('company_has_item', 'company_has_item.item_id = item.id_item', 'left');

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

		private function get_list_country($parms){
			$values = $this=>get_values($parms);

			$this->db->select('*');
			$this->db->from('country');

			if ($values['id_country']) 
				$this->db->or_where('country.id_country', $values['id_country']);
			if ($values['name_country']) 
				$this->db->or_where('country.name_country', $values['name_country']);

			$query = $this->db->get();

			return $query->result();
		}

		private function get_list_province($parms){
			// $values = $this=>get_values($parms);

			// $this->db->select('*');
			// $this->db->from('country');

			// if ($values['id_country']) 
			// 	$this->db->or_where('country.id_country', $values['id_country']);
			// if ($values['name_country']) 
			// 	$this->db->or_where('country.name_country', $values['name_country']);

			// $query = $this->db->get();

			// return $query->result();
		}

		private function get_list_city($parms){
			// $values = $this=>get_values($parms);

			// $this->db->select('*');
			// $this->db->from('country');

			// if ($values['id_country']) 
			// 	$this->db->or_where('country.id_country', $values['id_country']);
			// if ($values['name_country']) 
			// 	$this->db->or_where('country.name_country', $values['name_country']);

			// $query = $this->db->get();

			// return $query->result();
		}

		public function insert($parms){
        	$values = $this->get_values($parms);

			if ($values['name_country'])
			{
				$data = array(
				        'name_country' => $values['name_country']
				);

				$this->db->insert('country', $data);
				return $this->db->insert_id();
			}
			else if ($values['name_province'] && $values['country']) {
				$data = array(
				        'name_province' => $values['name_province'],
				        'country' => $values['country']
				);

				$this->db->insert('province', $data);
				return $this->db->insert_id();
			}
			else if ($values['name_city'] && $values['province']) {
				$data = array(
				        'name_city' => $values['name_city'],
				        'province' => $values['province']
				);

				$this->db->insert('city', $data);
				return $this->db->insert_id();
			}

			return NULL;
		}

		private function get_values($parms){
			$values = [];

			// country
			$values['id_country'] = (array_key_exists('id_country',	$parms) ? $parms['id_country']   : NULL);
			$values['name_country'] = (array_key_exists('name_country',	$parms) ? $parms['name_country']   : NULL);

			// province
			$values['id_province'] = (array_key_exists('id_province',	$parms) ? $parms['id_province']   : NULL);
			$values['name_province'] = (array_key_exists('name_province',	$parms) ? $parms['name_province']   : NULL);
			$values['country'] = (array_key_exists('country',	$parms) ? $parms['country']   : NULL); //foreign key

			// city
			$values['id_city'] = (array_key_exists('id_city',	$parms) ? $parms['id_city']   : NULL);
			$values['name_city'] = (array_key_exists('name_city',	$parms) ? $parms['name_city']   : NULL);
			$values['province'] = (array_key_exists('province',	$parms) ? $parms['province']   : NULL); //foreign keys

        	return $values;
		}
}