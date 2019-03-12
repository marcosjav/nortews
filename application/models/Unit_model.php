<?php
class Unit_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('unit');

			if ($values['id_unit']) $this->db->or_where('unit.id_unit', $values['id_unit']);
			if ($values['name_unit']) $this->db->or_like('unit.name_unit', $values['name_unit']);

			$query = $this->db->get();
			return $query->result();

			// return $this->db->get_compiled_select();

		}

		public function insert($parms){

			$values = $this->get_values($parms);

			if ($values['name_unit'])
			{
				$data = array(
				        'name_unit' => $values['name_unit']
				);

				$this->db->insert('unit', $data);
				return $this->db->insert_id();
			}

			return NULL;
		}

		private function get_values($parms){
			$values = [];

			// ítem
        	$values['id_unit']= (array_key_exists('id_unit', $parms) ? $parms['id_unit'] : NULL);
			$values['name_unit'] 	= (array_key_exists('name_unit', $parms) ? $parms['name_unit'] : NULL);

        	return $values;
		}
}