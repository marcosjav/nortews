<?php
class Shop_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	
        	$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('shop');

			if ($values['id_shop']) 
				$this->db->or_where('shop.id_shop', $values['id_shop']);
			if ($values['name_shop']) 
				$this->db->or_like('shop.name_shop', $values['name_shop']);

			$query = $this->db->get();

			return $query->result();

		}

		public function insert($parms){
        	$values = $this->get_values($parms);

			if ($values['name_shop'])
			{
				$data = array(
				        'name_shop' => $values['name_shop']
				);

				$this->db->insert('shop', $data);
				return $this->db->insert_id();
			}

			return NULL;
		}

		private function get_values($parms){
			$values = [];

			// ítem
			$values['id_shop'] = (array_key_exists('id_shop',	$parms) ? $parms['id_shop']   : 0);
        	$values['name_shop']   = (array_key_exists('name_shop',	$parms) ? $parms['name_shop']   : NULL);
        	$values['address_id_address']   = (array_key_exists('address_id_address',	$parms) ? $parms['address_id_address']   : 0);

        	return $values;
		}
}