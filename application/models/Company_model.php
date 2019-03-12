<?php
class Company_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

		public function get_list($parms)
		{
			$values = $this->get_values($parms);
			$phones = [];
			$emails = [];
			$addresses = [];

			$this->db->select('*');
			$this->db->group_by('id_company');
			$this->db->from('company');
			$this->db->join('company_has_address', 'company_has_address.company_id = company.id_company', 'left');
			$this->db->join('address', 'address.id_address = company_has_address.address_id', 'left');
			// $this->db->join('city', 'city.id_address = company_has_address.address_id', 'left');

			// $this->db->join('company_has_email', 'company_has_email.company_id = company.id_company', 'left');
			// $this->db->join('email', 'email.id_email = company_has_email.email_id', 'left');

			// $this->db->join('company_has_phone', 'company_has_phone.company_id_company = company.id_company', 'left');
			// $this->db->join('phone', 'phone.id_phone = company_has_phone.phone_id_phone', 'left');

			// $this->db->join('company_has_picture', 'company_has_picture.company_id = company.id_company', 'left');
			// $this->db->join('picture', 'picture.id_picture = company_has_picture.picture_id', 'left');

			if ($values['id_company']){
				$this->db->or_where('company.id_company', $values['id_company']);
			}
			if ($values['name_company']) $this->db->or_like('company.name_company', $values['name_company']);
			if ($values['cuit_company']) $this->db->or_like('company.cuit_company', $values['cuit_company']);

			$query = $this->db->get();

			$result = $query->result();
			

			// return $result;
			// return $this->db->get_compiled_select();
			if ($values['id_company']) {
				$this->load->model('phone_model');
				$phones = $this->phone_model->get_company_phones($values);

				$this->load->model('email_model');
				$emails = $this->email_model->get_company_emails($values);

				$this->load->model('address_model');
				$addresses = $this->address_model->get_company_addresses($values);
				
				$result['phones'] = $phones;
				$result['emails'] = $emails;
				$result['addresses'] = $addresses;
			}
			return $result; 

		}

		public function insert($parms){
			$values = $this->get_values($parms);

			if ($values['name_company'])
			{
				$data = array(
				        'name_company' => $values['name_company'],
				        'cuit_company' => $values['cuit_company'],
				        'web_company' => $values['web_company'],
				);
				$this->db->insert('company', $data);
				// return $this->db->last_query();
				// return $this->db->insert_id();
				$id_company = $this->db->insert_id();

				// return $id_company;

				if (array_key_exists('phones', $values)) {
					$this->db->flush_cache();

					// PHONES
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
				}
					// EMAILS
				if (array_key_exists('emails', $values)) {
					$this->load->model('email_model');
					$id_emails = $this->email_model->insert_col($values['emails']);
					$company_has_email = [];
					foreach ($id_emails as $id) {
						$c_h_e = array(
							'company_id' => $id_company,
							'email_id' => $id
						);
						array_push($company_has_email, $c_h_e);
					}
					$this->db->insert_batch('company_has_email', $company_has_email);
				}

					// ADDRESSES
				if (array_key_exists('addresses', $values)) {
					$this->load->model('address_model');
					$id_addresses = $this->address_model->insert_col($values['addresses']);
					$company_has_address = [];
					foreach ($id_addresses as $id) {
						$c_h_a = array(
							'company_id' => $id_company,
							'address_id' => $id
						);
						array_push($company_has_address, $c_h_a);
					}
					$this->db->insert_batch('company_has_address', $company_has_address);
				}

					$response['id_company'] = $id_company;
					return $response;
				}

			return NULL;
		}

		public function get_values($parms){
			$values = [];
        	$values['id_company'] 	 	  = (array_key_exists('id_company',    		$parms) ? $parms['id_company']    	 	 : NULL);
        	$values['web_company']	 	  = (array_key_exists('web_company',		$parms) ? $parms['web_company']   	 	 : NULL);
			$values['name_company']	 	  = (array_key_exists('name_company',  		$parms) ? $parms['name_company']  	 	 : NULL);
        	$values['cuit_company']	 	  = (array_key_exists('cuit_company',  		$parms) ? $parms['cuit_company']  	 	 : NULL);
        	$values['emails']	 	 	  = (array_key_exists('emails',  			$parms) ? $parms['emails']  	 	 	 : NULL);

        	if (array_key_exists('addresses', $parms))
        		$values['addresses'] = $this->get_address_array($parms);
        	
        	if (array_key_exists('phones', $parms))
        		$values['phones'] = $this->get_phone_array($parms);

        	return $values;
		}


		private function insert_phones($phones){
			$id_phones = [];

			if ($phones) {
				foreach ($phones as $v) {

					if ($v['id_phone']) 
						array_push($id_phones, $v['id_phone']);
					
					else if ($v['number_phone']){
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
	        	$phone['id_phone']	 = (array_key_exists('id_phone', 	   $v) ? $v['id_phone']		: NULL);	
	        	$phone['type']	 = (array_key_exists('type',   $v) ? $v['type'] 	: 1);
	        	$phone['area_phone']	 = (array_key_exists('area_phone',   $v) ? $v['area_phone'] 	: 0);
    			$phone['number_phone'] = (array_key_exists('number_phone', $v) ? $v['number_phone'] 	: NULL);

    			if ($phone['id_phone'] || $phone['number_phone']) array_push($phones_array, $phone);
    		}

    		return $phones_array;
		}

		private function get_address_array($parms){
			$address_array = [];

			foreach ($parms['addresses'] as $v) {
    			$address = [];
	        	$address['id']	 = (array_key_exists('id', 	   $v) ? $v['id']		: NULL);	
	        	$address['street']	 = (array_key_exists('street',   $v) ? $v['street'] 	: 1);
	        	$address['zip_code']	 = (array_key_exists('zip_code',   $v) ? $v['zip_code'] 	: 0);
    			$address['number'] = (array_key_exists('number', $v) ? $v['number'] 	: NULL);
    			$address['coordinates'] = (array_key_exists('coordinates', $v) ? $v['coordinates'] 	: NULL);
    			$address['city'] = (array_key_exists('city', $v) ? $v['city'] 	: NULL);

    			if ($address['id'] || ($address['number'] && $address['street'])) array_push($address_array, $address);
    		}

    		return $address_array;
		}
}