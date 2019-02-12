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

		public function get_full_list($parms){
        	
        	$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('shop');
			$this->db->join('address', 'address.id_address = shop.address_id_address', 'left');
			$this->db->join('city', 'address.city = city.id_city', 'left');
			$this->db->join('province', 'city.province = province.id_province', 'left');
			$this->db->join('country', 'province.country = country.id_country', 'left');

			if ($values['id_shop']) 
				$this->db->or_where('shop.id_shop', $values['id_shop']);
			if ($values['name_shop']) 
				$this->db->or_like('shop.name_shop', $values['name_shop']);
			if ($values['address_id_address']) 
				$this->db->or_like('address.id_address', $values['address_id_address']);
			if ($values['id_city']) 
				$this->db->or_like('city.id_city', $values['id_city']);
			if ($values['id_province']) 
				$this->db->or_like('province.id_province', $values['id_province']);
			if ($values['id_country']) 
				$this->db->or_like('country.id_country', $values['id_country']);

			$query = $this->db->get();

			return $query->result();

		}

		public function insert($parms){
        	$values = $this->get_values($parms);
        	// return $parms;
			if ($values['name_shop']){
				if ($values['address'])
				{
					$values['address_id_address'] = $this->insert_address($values['address']);
					
					if ($values['address_id_address']) {
						$id_shop = $this->insert_shop($values);
						$id_phones = $this->insert_phones($values['phones']);
						$id_emails = $this->insert_emails($values['emails']);

						$this->insert_shop_has_email($id_shop, $id_emails);
						$this->insert_shop_has_phone($id_shop, $id_phones);

						$response['id_shop'] = $id_shop;
						$response['id_phone'] = $id_phones;
						$response['id_email'] = $id_emails;

						return $response;
					}
					return "no se insertó el shop";
				}
				return "no tiene address";
			}

			return NULL;
		}

		private function insert_shop($values){
			$this->db->flush_cache();
			$data = array(
			        'name_shop' => $values['name_shop'],
			        'address_id_address' => $values['address_id_address']
			);

			$this->db->insert('shop', $data);
			return $this->db->insert_id();
		}

		private function get_values($parms){
			$values = [];

			// ítem
			$values['id_shop'] = (array_key_exists('id_shop',	$parms) ? $parms['id_shop']   : 0);
        	$values['name_shop']   = (array_key_exists('name_shop',	$parms) ? $parms['name_shop']   : NULL);
        	$values['address_id_address']   = (array_key_exists('address_id_address',	$parms) ? $parms['address_id_address']   : NULL);

        	$values['address']   = (array_key_exists('address',	$parms) ? $parms['address']   : NULL);
        	$values['id_city']   = (array_key_exists('id_city',	$parms) ? $parms['id_city']   : NULL);
        	$values['id_province']   = (array_key_exists('id_province',	$parms) ? $parms['id_province']   : NULL);
        	$values['id_country']   = (array_key_exists('id_country',	$parms) ? $parms['id_country']   : NULL);

        	$values['phones']   = (array_key_exists('phones',	$parms) ? $parms['phones']   : NULL);

        	$values['emails'] = (array_key_exists('emails',	$parms) ? $parms['emails']   : NULL);

        	return $values;
		}

		private function get_phone($parms){
			$phone['area_phone']   = (array_key_exists('area_phone',	$parms) ? $parms['area_phone']   : NULL);
        	$phone['number_phone']   = (array_key_exists('number_phone',	$parms) ? $parms['number_phone']   : NULL);
        	$phone['type']   = (array_key_exists('type',	$parms) ? $parms['type']   : NULL);

        	if ($phone['number_phone'] && $phone['type'])
        		return $phone;

        	return NULL;
		}

		private function insert_phone($parms){
			$phone = $this->get_phone($parms);
			
			if ($phone) {
				$this->load->model('phone_model');
				return $this->phone_model->insert($phone);
			}

			return NULL;
		}

		private function get_address($parms){
			$address['street']   = (array_key_exists('street',	$parms) ? $parms['street']   : NULL);
        	$address['number']   = (array_key_exists('number',	$parms) ? $parms['number']   : NULL);
        	$address['zip_code']   = (array_key_exists('zip_code',	$parms) ? $parms['zip_code']   : NULL);
        	$address['coordinates']   = (array_key_exists('coordinates',	$parms) ? $parms['coordinates']   : NULL);
        	$address['city']   = (array_key_exists('city',	$parms) ? $parms['city']   : NULL);

        	if ($address['street'] && $address['city'])
        		return $address;

        	return NULL;
		}

		private function insert_address($parms){
			$address = $this->get_address($parms);
			
			if ($address) {
				$this->load->model('address_model');
				return $this->address_model->insert($address);
			}

			return NULL;
		}

		private function insert_phones($phones){
			$id_phones = [];

			if ($phones) {
				$this->db->flush_cache();
				foreach ($phones as $v) {
					
					if ($v['number_phone']){
						$phone['number'] = $v['number_phone']; 
						$phone['area'] = $v['area_phone'];
						$phone['type'] = $v['type'];

						$this->load->model('phone_model');
						$id = $this->phone_model->insert($phone);

						array_push($id_phones, $id);
					}

				}
			}

			return $id_phones;
		}

		private function insert_emails($emails){
			$id_emails = [];

			if ($emails) {
				$this->db->flush_cache();
				foreach ($emails as $v) {
					
					$this->load->model('email_model');
					$email['address_email'] = $v;
					$id = $this->email_model->insert($email);

					array_push($id_emails, $id);

				}
			}

			return $id_emails;
		}

		private function insert_shop_has_email($id_shop, $emails){
			if ($id_shop && $emails) {
				$this->db->flush_cache();

				foreach ($emails as $v) {
					
					$this->db->flush_cache();
					$data = array(
					        'shop_id' => $id_shop,
					        'email_id' => $v
					);

					$this->db->insert('shop_has_email', $data);
				}
			}
		}

		private function insert_shop_has_phone($id_shop, $phones){
			if ($id_shop && $phones) {
				$this->db->flush_cache();

				foreach ($phones as $v) {
					
					$this->db->flush_cache();
					$data = array(
					        'shop_id' => $id_shop,
					        'phone_id' => $v
					);

					$this->db->insert('shop_has_phone', $data);
				}
			}
		}
}