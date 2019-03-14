<?php
class Stock_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	$values = $this->get_values($parms);

			$this->db->select('*');
			$this->db->from('stock');
			$this->db->join('item', 'item.id_item = stock.item_id', 'left');
			$this->db->join('shop', 'shop.id_shop = stock.shop_id_shop', 'left');

			if ($values['item_id']) $this->db->or_where('stock.item_id', $values['item_id']);
			if ($values['shop_id_shop']) $this->db->or_like('stock.shop_id_shop', $values['shop_id_shop']);

			$query = $this->db->get();
			return $query->result();

		}

		public function insert($parms){
			$values = $this->get_values($parms);

			if ($values['shop_id_shop'] && $values['item_id'] && $values['quantity_stock'] && $values['min_stock'] && $values['unit_id'])
			{
				$data = array(
						'item_id' => $values['item_id'],
				        'quantity_stock' => $values['quantity_stock'],
				        'min_stock' => $values['min_stock'],
				        'unit_id' => $values['unit_id'],
				        'shop_id_shop' => $values['shop_id_shop']
				);

				$this->db->insert('stock', $data);
				return $this->db->insert_id();
			}

			return NULL;
		}

		private function get_values($parms){
			$values = [];

        	$values['item_id']	 	= (array_key_exists('item_id', $parms) ? $parms['item_id'] : NULL);
        	$values['quantity_stock']	 	= (array_key_exists('quantity_stock', $parms) ? $parms['quantity_stock'] : NULL);
        	$values['min_stock']	 	= (array_key_exists('min_stock', $parms) ? $parms['min_stock'] : NULL);
        	$values['unit_id']	 	= (array_key_exists('unit_id', $parms) ? $parms['unit_id'] : NULL);
        	$values['shop_id_shop']	 	= (array_key_exists('shop_id_shop', $parms) ? $parms['shop_id_shop'] : NULL);

        	return $values;
		}
}