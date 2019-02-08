<?php
class Company_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
			$values = $this->get_values($parms);

        	return $this->get_list_extend($values['name'], $values['id'], $values['cuit']);
        }

		public function get_list_extend($name = NULL, $id = NULL, $cuit = NULL)
		{
			$this->db->select('*');
			$this->db->from('company');
			$this->db->join('company_has_address', 'company_has_address.company_id = company.id_company', 'left');
			$this->db->join('address', 'address.id_address = company_has_address.address_id', 'left');

			$this->db->join('company_has_email', 'company_has_email.company_id = company.id_company', 'left');
			$this->db->join('email', 'email.id_email = company_has_email.email_id', 'left');

			$this->db->join('company_has_phone', 'company_has_phone.company_id_company = company.id_company', 'left');
			$this->db->join('phone', 'phone.id_phone = company_has_phone.phone_id_phone', 'left');

			$this->db->join('company_has_picture', 'company_has_picture.company_id = company.id_company', 'left');
			$this->db->join('picture', 'picture.id_picture = company_has_picture.picture_id', 'left');

			if ($id) $this->db->or_where('company.id_company', $id);
			if ($name) $this->db->or_like('company.name_company', $name);
			if ($cuit) $this->db->or_like('company.cuit_company', $cuit);

			$query = $this->db->get();
			return $query->result();

		}

		public function insert($parms){
			$values = $this->get_values($parms);

			if ($values['name'])
			{
				$data = array(
				        'name_company' => $values['name'],
				        'cuit_company' => $values['cuit'],
				        'web_company' => $values['web'],
				);

				$this->db->insert('company', $data);
				$id_company = $this->db->insert_id();

				// return $id_company;

				if ($values['phones']) {
					$this->db->flush_cache();

					$id_phones = $this->insert_phones($values['phones']);
					$company_has_phone = [];

					foreach ($id_phones as $id) {
						$c_h_p = array(
						'company_id_company' => $id_company,
							'phone_id_phone' => $id
						);
						array_push($company_has_phone, $c_h_p);
					}

					$this->db->insert_batch('company_has_phone', $company_has_phone);

					return $id_company;
				}
			}

			return NULL;
		}

		public function get_values($parms){
			$values = [];
        	$values['id'] 	 	  = (array_key_exists('id',    		$parms) ? $parms['id']    	 	 : NULL);
        	$values['web']	 	  = (array_key_exists('web',		$parms) ? $parms['web']   	 	 : NULL);
			$values['name']	 	  = (array_key_exists('name',  		$parms) ? $parms['name']  	 	 : NULL);
        	$values['cuit']	 	  = (array_key_exists('cuit',  		$parms) ? $parms['cuit']  	 	 : NULL);

        	if (array_key_exists('phones', $parms)) {
        		/*	SE MOVIÓ A UNA FUNCIÓN APARTE  */
        		// $values['phones'] = [];

        		// foreach ($parms['phones'] as $v) {
        		// 	$phone = [];
		        // 	$phone['id']	 = (array_key_exists('id', 	   $v) ? $v['id']		: NULL);	
		        // 	$phone['type']	 = (array_key_exists('type',   $v) ? $v['type'] 	: 1);
		        // 	$phone['area']	 = (array_key_exists('area',   $v) ? $v['area'] 	: 0);
        		// 	$phone['number'] = (array_key_exists('number', $v) ? $v['number'] 	: NULL);

        		// 	if ($phone['id'] || $phone['number']) array_push($values['phones'], $phone);
        		// }
        		$values['phones'] = $this->get_phone_array($parms);
        	}

        	if (array_key_exists('phones', $parms)) {
        		/*	SE MOVIÓ A UNA FUNCIÓN APARTE  */
        		// $values['phones'] = [];

        		// foreach ($parms['phones'] as $v) {
        		// 	$phone = [];
		        // 	$phone['id']	 = (array_key_exists('id', 	   $v) ? $v['id']		: NULL);	
		        // 	$phone['type']	 = (array_key_exists('type',   $v) ? $v['type'] 	: 1);
		        // 	$phone['area']	 = (array_key_exists('area',   $v) ? $v['area'] 	: 0);
        		// 	$phone['number'] = (array_key_exists('number', $v) ? $v['number'] 	: NULL);

        		// 	if ($phone['id'] || $phone['number']) array_push($values['phones'], $phone);
        		// }
        		$values['phones'] = $this->get_phone_array($parms);
        	}
        	

        	return $values;
		}


		private function insert_phones($phones){
			$id_phones = [];

			if ($phones) {
				foreach ($phones as $v) {

					if ($v['id']) 
						array_push($id_phones, $v['id']);
					
					else if ($v['number']){
						$id = $this->insert_phone($v);

						array_push($id_phones, $id);
					}

				}
			}

			return $id_phones;
		}

		private function insert_phone($parms){			
            $this->load->model('phone_model');
			return $this->phone_model->insert($parms);
		}

		public function probar($parms){
			return $this->insert_phone($parms);
		}

		private function get_phone_array($parms){
			$phones_array = [];

    		foreach ($parms['phones'] as $v) {
    			$phone = [];
	        	$phone['id']	 = (array_key_exists('id', 	   $v) ? $v['id']		: NULL);	
	        	$phone['type']	 = (array_key_exists('type',   $v) ? $v['type'] 	: 1);
	        	$phone['area']	 = (array_key_exists('area',   $v) ? $v['area'] 	: 0);
    			$phone['number'] = (array_key_exists('number', $v) ? $v['number'] 	: NULL);

    			if ($phone['id'] || $phone['number']) array_push($phones_array, $phone);
    		}

    		return $phones_array;
		}
}