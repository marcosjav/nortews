<?php
class Phone_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

		public function get_list($parms)
		{
			$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('phone');
			$this->db->join('phone_type', 'phone_type.id = phone.type', 'left');

			if ($values['area_phone'] > 0)	 $this->db->or_like('phone.area_phone',   $values['area_phone']);
			if ($values['number_phone']) $this->db->or_like('phone.number_phone', $values['number_phone']);
			if ($values['type'] > 0) 	 $this->db->or_like('phone.type', 	  	  $values['type']);

			return $this->db->get()->result();
		}

		public function insert($parms){
			$values = $this->get_values($parms);
			
			if ($values['number_phone']){

				$data = array(
					'area_phone' => $values['area_phone']??0,
					'number_phone' => $values['number_phone'],
					'type' => $values['type']??1
				);

				$this->db->insert('phone', $data);
				return $this->db->insert_id();
				// return $this->db->insert('phone', $data)->get_compiled_select();
			}

			return NULL;
		}

		public function get_types(){
			$this->db->select('*');
			$this->db->from('phone_type');

			return $this->db->get()->result();
		}

		private function get_values($parms){
			/*	ACÁ HAY QUE TRATAR DE TRAER EL PRIMER ID DE PHONE_TYPE POR SI NO DEFINEN NINGUNO  */
			// return $this->db->get()->row()->id;

			// $this->db->select_max('id');
			// $query = $this->db->get('phone_type');

			//$this->db->flush_cache();

			$values = [];
        	$values['id_phone']   = (array_key_exists('id_phone',	$parms) ? $parms['id_phone']   : 0);
        	$values['area_phone']   = (array_key_exists('area_phone',	$parms) ? $parms['area_phone']   : NULL);
        	$values['number_phone'] = (array_key_exists('number_phone', $parms) ? $parms['number_phone'] : NULL);
        	$values['type']   = (array_key_exists('type',	$parms) ? $parms['type']   : NULL);

        	$values['id_company']   = (array_key_exists('id_company',	$parms) ? $parms['id_company']   : NULL);

        	// if (is_null($values['area_phone'])) $values['area_phone'] = 0;
        	// if (is_null($values['type'])) $values['type'] = 0;

        	return $values;
		}

		public function get_company_phones($parms){
			$values = $this->get_values($parms);

			if ($values['id_company']) {
				$this->db->select('*');
				$this->db->from('phone');
				$this->db->join('phone_type', 'phone_type.id = phone.type', 'left');
				$this->db->join('company_has_phone', 'company_has_phone.phone_id_phone = phone.id_phone', 'left');

				$this->db->or_like('company_has_phone.company_id_company',   $values['id_company']);

				return $this->db->get()->result();
			}
		}

}