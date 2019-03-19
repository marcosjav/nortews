<?php
class Location_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('city');
			$this->db->join('province', 'province.id_province = city.province', 'left');
			$this->db->join('country', 'country.id_country = province.country', 'left');

			// city conditions
			if ($values['id_city']) 
				$this->db->or_where('city.id_city', $values['id_city']);
			if ($values['name_city']) 
				$this->db->or_where('city.name_city', $values['name_city']);

			// province conditions
			if ($values['id_province']) 
				$this->db->or_where('province.id_province', $values['id_province']);
			if ($values['name_province']) 
				$this->db->or_where('province.name_province', $values['name_province']);

			// country conditions
			if ($values['id_country']) 
				$this->db->or_where('country.id_country', $values['id_country']);
			if ($values['name_country']) 
				$this->db->or_where('country.name_country', $values['name_country']);

			$query = $this->db->get();

			return $query->result();

		}

		public function get_country($parms){
			$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('country');

			if ($values['id_country']) 
				$this->db->or_where('country.id_country', $values['id_country']);
			if ($values['name_country']) 
				$this->db->or_where('country.name_country', $values['name_country']);

			$query = $this->db->get();

			return $query->result();
		}

		public function get_province($parms){
			$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('province');

			if ($values['id_province']) 
				$this->db->or_where('province.id_province', $values['id_province']);
			if ($values['name_province']) 
				$this->db->or_where('province.name_province', $values['name_province']);
			if ($values['country']) 
				$this->db->or_where('province.country', $values['country']);
			if ($values['id_country']) 
				$this->db->or_where('province.country', $values['id_country']);

			$query = $this->db->get();

			return $query->result();
		}

		public function get_city($parms){
			$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('city');

			if ($values['id_city']) 
				$this->db->or_where('city.id_city', $values['id_city']);
			if ($values['name_city']) 
				$this->db->or_where('city.name_city', $values['name_city']);
			if ($values['province']) 
				$this->db->or_where('city.province', $values['province']);
			if ($values['country']) 
				$this->db->or_where('province.country', $values['country']);
			
			if ($values['id_province']) 
				$this->db->or_where('city.province', $values['id_province']);
			if ($values['id_country']) 
				$this->db->or_where('province.country', $values['id_country']);

			$query = $this->db->get();

			return $query->result();
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