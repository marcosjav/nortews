<?php
class Brand_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	$name 	= (array_key_exists('name', $parms) ? $parms['name'] : NULL);
        	$id 	= (array_key_exists('id', $parms) ? $parms['id'] : NULL);

        	return $this->get_list_extend($name, $id);
        }

		public function get_list_extend($name = NULL, $id = NULL)
		{
			$this->db->select('*');
			$this->db->from('brand');

			if ($id) $this->db->or_where('brand.id_brand', $id);
			if ($name) $this->db->or_like('brand.name_brand', $name);

			$query = $this->db->get();
			return $query->result();

			// return $this->db->get_compiled_select();

		}

		public function insert($parms){

			$name = (array_key_exists('name', $parms) ? $parms['name'] : NULL);

			if ($name)
			{
				$data = array(
				        'name_brand' => $name
				);

				$this->db->insert('brand', $data);
				return $this->db->insert_id();
			}

			return NULL;
		}
}