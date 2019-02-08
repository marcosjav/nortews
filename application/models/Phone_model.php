<?php
class Phone_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	$values = $this->get_values($parms);

        	return $this->get_list_extend($values['area'], $values['number'], $values['type']);
        }

		public function get_list_extend($area = NULL, $number = NULL, $type = NULL)
		{
			$this->db->select('*');
			$this->db->from('phone');

			if ($area)	 $this->db->or_like('phone.area_phone',   $area);
			if ($number) $this->db->or_like('phone.number_phone', $number);
			if ($type) 	 $this->db->or_like('phone.type', 	  	  $type);

			return $this->db->get()->result();
		}

		public function insert($parms){
			$values = $this->get_values($parms);
			
			if ($values['number']){

				$data = array(
					'area_phone' => $values['area'],
					'number_phone' => $values['number'],
					'type' => $values['type']
				);

				$this->db->insert('phone', $data);
				return $this->db->insert_id();
				// return $this->db->insert('phone', $data)->get_compiled_select();
			}

			return NULL;
		}

		private function get_values($parms){
			/*	ACÁ HAY QUE TRATAR DE TRAER EL PRIMER ID DE PHONE_TYPE POR SI NO DEFINEN NINGUNO  */
			// return $this->db->get()->row()->id;

			// $this->db->select_max('id');
			// $query = $this->db->get('phone_type');

			//$this->db->flush_cache();

			$values = [];
        	$values['area']   = (array_key_exists('area',	$parms) ? $parms['area']   : 0);
        	$values['number'] = (array_key_exists('number', $parms) ? $parms['number'] : 0);
        	$values['type']   = (array_key_exists('type',	$parms) ? $parms['type']   : 1);

        	if (is_null($values['area'])) $values['area'] = 0;
        	if (is_null($values['type'])) $values['type'] = 1;

        	return $values;
		}

}