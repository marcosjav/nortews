<?php
class Title_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        /*
			Title functions
        */
        public function get_title_list($parms){
        	$name 	= (array_key_exists('name', $parms) ? $parms['name'] : NULL);
        	$id 	= (array_key_exists('id', $parms) ? $parms['id'] : NULL);

        	return $this->get_title_list_extend($name, $id);
        }

		private function get_title_list_extend($name = NULL, $id = NULL)
		{
			$this->db->select('*');
			$this->db->from('title');

			if ($id) $this->db->or_where('title.id_title', $id);
			if ($name) $this->db->or_like('title.name_title', $name);
			
			return $this->db->get()->result();
		}

		public function insert_title($parms){
			$name = (array_key_exists('name_title', $parms) ? $parms['name_title'] : NULL);

			if ($name){
				$data = array('name_title' => $name);

				$this->db->insert('title', $data);
				return $this->db->insert_id();
			}
			return NULL;
		}

		/*
			SUBTITLES functions
		*/
		public function get_subtitle_list($parms){
        	$name 	  = (array_key_exists('name', $parms) ? $parms['name'] : NULL);
        	$id 	  = (array_key_exists('id', $parms) ? $parms['id'] : NULL);
        	$title_id = (array_key_exists('title_id', $parms) ? $parms['title_id'] : NULL);

        	return $this->get_subtitle_list_extend($name, $id, $title_id);
        }

		private function get_subtitle_list_extend($name = NULL, $id = NULL, $title_id = NULL)
		{
			$this->db->select('*');
			$this->db->from('subtitle');

			if ($id) $this->db->or_where('subtitle.id_subtitle', $id);
			if ($name) $this->db->or_like('subtitle.name_subtitle', $name);
			if ($title_id) $this->db->or_like('subtitle.title_id', $title_id);
			
			return $this->db->get()->result();
		}

		public function insert_subtitle($parms){
			$name = (array_key_exists('name_subtitle', $parms) ? $parms['name_subtitle'] : NULL);
			$id = (array_key_exists('title_id', $parms) ? $parms['title_id'] : NULL);

			if ($name && $id){
				$data = array(
					'name_subtitle' => $name,
					'title_id' => $id
				);

				$this->db->insert('subtitle', $data);
				return $this->db->insert_id();
			}
			return NULL;
		}

		/*
			FULL functions
		*/
		public function get_list($parms){
        	$name 	= (array_key_exists('name', $parms) ? $parms['name'] : NULL);
        	$id 	= (array_key_exists('id', $parms) ? $parms['id'] : NULL);

        	return $this->get_list_extend($name, $id);
        }

		private function get_list_extend($name = NULL, $id = NULL)
		{
			$this->db->select('*');
			$this->db->from('title');
			$this->db->join('subtitle', 'subtitle.title_id = title.id_title', 'left');

			if ($id) {
				$this->db->or_where('subtitle.id_subtitle', $id);
				$this->db->or_where('title.id_title', $id);
			}

			if ($name) {
				$this->db->or_like('subtitle.name_subtitle', $name);
				$this->db->or_like('title.name_title', $name);
			}
			
			return $this->db->get()->result();
		}

}