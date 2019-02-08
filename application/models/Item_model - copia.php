<?php
class Item_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_list($parms){
        	$code 			 = (array_key_exists('code', $parms) ? $parms['code'] : NULL);
        	$title_id 		 = (array_key_exists('title_id', $parms) ? $parms['title_id'] : NULL);
        	$subtitle_id 	 = (array_key_exists('subtitle_id', $parms) ? $parms['subtitle_id'] : NULL);
        	$shop_id 		 = (array_key_exists('shop_id', $parms) ? $parms['shop_id'] : NULL);
        	$brand_id 		 = (array_key_exists('brand_id', $parms) ? $parms['brand_id'] : NULL);
        	$name 			 = (array_key_exists('name', $parms) ? $parms['name'] : NULL);
        	$description 	 = (array_key_exists('description', $parms) ? $parms['description'] : NULL);
        	$under_min_stock = (array_key_exists('under_min_stock', $parms) ? $parms['under_min_stock'] : FALSE);
        	$company_id 	 = (array_key_exists('company_id', $parms) ? $parms['company_id'] : NULL);

        	return $this->get_list_extend($code, $title_id, $subtitle_id, $shop_id, $brand_id, $name, $description, $under_min_stock, $company_id);
        }

		public function get_list_extend($code = NULL, $title_id = NULL, $subtitle_id = NULL, $shop_id = NULL, $brand_id = NULL, $name = NULL, $description = NULL, $under_min_stock = FALSE, $company_id = NULL)
		{
			$this->db->select('*');
			$this->db->from('item');

			if ($company_id || $code) $this->db->join('company_has_item', 'company_has_item.item_id = item.id_item', 'left');
			if ($company_id || $code) $this->db->join('company', 'company.id_company = company_has_item.company_id', 'left');
			if ($subtitle_id || $title_id) $this->db->join('item_has_subtitle', 'item_has_subtitle.item_id = item.id_item');
			if ($subtitle_id || $title_id) $this->db->join('subtitle', 'subtitle.id_subtitle = item_has_subtitle.subtitle_id');
			if ($title_id) $this->db->join('title', 'title.id_title = subtitle.title_id');
			if ($shop_id || $under_min_stock) $this->db->join('stock', 'stock.item_id = item.id_item');
			if ($shop_id || $under_min_stock) $this->db->join('unit', 'unit.id_unit = stock.unit_id');
			if ($shop_id || $under_min_stock) $this->db->join('shop', 'shop.id_shop = stock.shop_id');
			if ($brand_id) $this->db->join('brand_has_item', 'brand_has_item.item_id = item.id_item');
			if ($brand_id) $this->db->join('brand', 'brand.id_brand = brand_has_item.item_id');

			// $this->db->where(true);
			
			if ($code)
			{
				$this->db->or_where('item.id_item', $code);
				$this->db->or_where('company_has_item.code', $code);
			}
			
			if ($title_id || $subtitle_id) 
			{
				
				
				if ($title_id) 
				{
					
					$this->db->or_where('title.id_title', $title_id);
				}

				if ($subtitle_id) 
				{
					$this->db->or_where('subtitle.id_subtitle', $subtitle_id);
				}
			}
			
			if ($shop_id) 
			{
				

				$this->db->or_where('shop.id_shop', $shop_id);
			}

			if ($brand_id) 
			{
				$this->db->or_where('brand.id_brand', $brand_id);
			}

			if ($name) 
			{
				$this->db->or_like('item.name_item', $name);
			}

			if ($description) 
			{
				$this->db->or_like('item.description_item', $description);
			}

			if ($under_min_stock) {
				$this->db->where('stock.quantity_stock <= stock.min_stock');
			}

			if ($company_id) 
			{
				$this->db->or_where('company.id_company', $company_id);
			}

			$query = $this->db->get();
			return $query->result();

			// return $this->db->get_compiled_select();

		}

		public function insert($parms){
			$values = get_values($parms);

			$item_id = insert_item($values);

			if ($item_id && $item_id > 0) {
				$values['item_id'] = $item_id;
				brand_has_item($values);
			}
		}

		// ITEM
		private function insert_item($values){

			$name 		 = (array_key_exists('name', $values) ? $values['name'] : NULL);
			$description = (array_key_exists('description', $values) ? $values['description'] : NULL);

			if ($name && $description)
			{
				$data = array(
				        'name_item' => $name,
				        'description_item' => $description
				);

				$this->db->insert('item', $data);
				return $this->db->insert_id();
			}

			return 0;
		}

		// BRAND_HAS_ITEM
		private function brand_has_item($values){
			$brand_id = (array_key_exists('id_brand', $values) ? $values['id_brand'] : NULL);

			if ($brand_id) {
				$data = array(
				        'brand_id' => $brand_id,
				        'item_id' => $values['item_id']
				);

				$this->db->insert('brand_has_item', $data);
				return $this->db->insert_id();
			}
		}
		
		// COMPANY_HAS_ITEM
		private function company_has_item($values){
			$company_id = (array_key_exists('company_id', $values) ? $values['company_id'] : NULL);

			if ($company_id) {
				$data = array(
				        'company_id' => $company_id,
				        'item_id' => $values['item_id'],
				        'code' => $values['code'],
				        'cost_item' => $values['cost_item']
				);

				$this->db->insert('company_has_item', $data);
				return $this->db->insert_id();
			}
		}
		
		// DISCOUNT
		private function discount($values){
			$value_discount = (array_key_exists('value_discount', $values) ? $values['value_discount'] : NULL);

			if ($value_discount) {
				$data = array(
				        'value_discount' => $value_discount,
				        'description_discount' => $values['description_discount'],
				        'company_has_item_company_id' => $values['company_id'],
				        'company_has_item_item_id' => $values['item_id']
				);

				$this->db->insert('discount', $data);
				return $this->db->insert_id();
			}
		}

		// STOCK - ENLAZAR SHOP CON ITEM
		private function stock($values){

			if (array_key_exists('stocks', $values) && count($values['stocks'],0)) {
				foreach ($values['stocks'] as &$d) {
					$d['item_id'] = $values['item_id'];
				}

				$this->db->insert_batch('stock', $values['stocks']);
			}
		}

		// ITEM_HAS_PICTURE
		private function item_has_picture($values){
			$pictures = (array_key_exists('pictures', $values) ? $values['pictures'] : NULL);

			if ($pictures) {
				// get the first id of the batch and add the size of array
				$size = count($pictures, 0);

				$this->db->insert_batch('picture', $pictures);
				$first_id = $this->db->insert_id();

				$item_has_picture = [];

				for ($i=0; $i < $size; $i++) { 
					$i_h_p['picture_id'] = $first_id + $i;
					$i_h_p['item_id'] = $values['item_id']; 
					array_push($item_has_picture, $i_h_p);
				}

				$this->db->insert_batch('item_has_picture', $item_has_picture);
			}
		}

		// ITEM_HAS_SUBTITLE
		private function item_has_subtitle($values){

			if (array_key_exists('subtitles', $values) && count($values['subtitles'], 0)) {
				foreach ($values['subtitles'] as &$d) {
					$d['item_id'] = $values['item_id'];
				}

				$this->db->insert_batch('item_has_subtitle', $values['subtitles']);
			}

		}

		private function get_values($parms){
			$values = [];
			// ítem
			$values['item_id'] = 0;
        	$values['name_item']   = (array_key_exists('name_item',	$parms) ? $parms['name_item']   : 0);
        	$values['description_item']   = (array_key_exists('description_item',	$parms) ? $parms['description_item']   : 0);
        	// brand_has_item
        	$values['id_brand']   = (array_key_exists('id_brand',	$parms) ? $parms['id_brand']   : 0);
        	// item_has_subtitle
        	$values['subtitle_id']   = (array_key_exists('subtitle_id',	$parms) ? $parms['subtitle_id']   : 0);
        	// company_has_item
        	$values['company_id']   = (array_key_exists('company_id',	$parms) ? $parms['company_id']   : 0);
        	$values['code']   = (array_key_exists('code',	$parms) ? $parms['code']   : 0);
        	$values['cost_item']   = (array_key_exists('cost_item',	$parms) ? $parms['cost_item']   : 0);
        	// discount
        	$values->discount['value_discount'] = 
        	$values   = (array_key_exists('value_discount',	$parms) ? $parms['value_discount']   : 0);
        	$values['description_discount']   = (array_key_exists('description_discount',	$parms) ? $parms['description_discount']   : 0);
        	
        	return $values;
		}
}