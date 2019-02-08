<?php
class Picture_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	$values = $this->get_values($parms);

        	if ($values === []) {
        		$this->db->select('*');
				$this->db->from('picture');

				return $this->db->get()->result();
        	}

        	$this->get_picture($parms);
        }

		public function get_picture($parms){
        	$values = $this->get_values($parms);
			
			$this->db->select('*');
			$this->db->from('picture');

			if ($values['item_id']){
				$this->db->join('item_has_picture', 'item_has_picture.picture_id = picture.id_picture', 'left');
				$this->db->where('item_has_picture.item_id', $values['item_id']);

			} else if ($values['company_id']){
				$this->db->join('company_has_picture', 'company_has_picture.picture_id = picture.id_picture', 'left');
				$this->db->where('company_has_picture.company_id', $values['company_id']);
			
			} else if ($values['person_id']){
				$this->db->join('person_has_picture', 'person_has_picture.picture_id = picture.id_picture', 'left');
				$this->db->where('person_has_picture.person_id', $values['person_id']);
			
			} else {
				$this->db->where('picture.id_picture', $values['id_picture']);
			}

        	
        	return $this->db->get()->result();
        }

        public function insert($parms){
			$values = $this->get_values($parms);

			// First of all we save the picture and get the id
			if ($values['data_picture']){

				$data = array('data_picture' => $values['data_picture']);
				$id_picture = $this->db->insert('picture', $data)->get_compiled_select();

				if ($values['item_id']){
					$data = array(
						'item_id' => $values['item_id'],
						'picture_id' => $id_picture
					);
					$id_other = $this->db->insert('item_has_picture', $data)->get_compiled_select();

				} else if ($values['company_id']){
					$data = array(
						'company_id' => $values['company_id'],
						'picture_id' => $id_picture
					);
					$id_other = $this->db->insert('company_has_picture', $data)->get_compiled_select();

				} else if ($values['person_id']){
					$data = array(
						'person_id' => $values['person_id'],
						'picture_id' => $id_picture
					);
					$id_other = $this->db->insert('person_has_picture', $data)->get_compiled_select();


				}

				return $id_picture;
			}

			return NULL;
		}

		private function get_values($parms){
			$values = [];
			// from PICTURE table
        	$values['id_picture']   = (array_key_exists('id_picture',	$parms) ? $parms['id_picture']   : NULL);
        	// $values['picture_id']   = (array_key_exists('picture_id',	$parms) ? $parms['picture_id']   : NULL);
        	$values['data_picture'] = (array_key_exists('data_picture', $parms) ? $parms['data_picture'] : NULL);

        	//from ITEM_HAS_PICTURE table
        	$values['item_id']   = (array_key_exists('item_id',	$parms) ? $parms['item_id']   : NULL);

        	// From COMPANY_HAS_PICTURE
        	$values['company_id']   = (array_key_exists('company_id',	$parms) ? $parms['company_id']   : NULL);

        	// From PERSON_HAS_PICTURE
        	$values['person_id']   = (array_key_exists('person_id',	$parms) ? $parms['person_id']   : NULL);
		}

}