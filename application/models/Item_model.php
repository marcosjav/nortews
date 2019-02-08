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
			$values = $this->get_values($parms);

			$item_id = $this->insert_item($values);
			$response = array();

			if ($item_id) {

				$values['item_id'] = $item_id;
				$response['item_id'] = $item_id;

				$response['brand_has_item'] = $this->brand_has_item($values);
				$response['company_has_item'] = $this->company_has_item($values);
				$response['stock'] = $this->stock($values);
				$response['item_has_picture'] = $this->item_has_picture($values);
				$response['item_has_subtitle'] = $this->item_has_subtitle($values);

			}

			return $response;
		}


/************************ PRIVATE FUNCIONS ********************************/
		// ITEM
		private function insert_item($values){

			$name 		 = (array_key_exists('name_item', $values) ? $values['name_item'] : NULL);
			$description = (array_key_exists('description_item', $values) ? $values['description_item'] : NULL);

			if ($name && $description)
			{
				$data = array(
				        'name_item' => $name,
				        'description_item' => $description
				);

				$this->db->insert('item', $data);
				return $this->db->insert_id();
			}

			return NULL;
		}

		// BRAND_HAS_ITEM
		private function brand_has_item($values){

			if (array_key_exists('brands', $values) && count($values['brands'],0)) {
				$brands = array();
				foreach ($values['brands'] as $v) {
					$b['item_id'] = $values['item_id'];
					$b['brand_id'] = $v;
					array_push($brands, $b);
				}

				$this->db->insert_batch('brand_has_item', $brands);

				return $this->db->insert_id() . "  size: " . count($brands,0);
			}

		}
		
		// COMPANY_HAS_ITEM
		private function company_has_item($values){

			if (array_key_exists('companies', $values) && count($values['companies'], 0)) {
				$response = array();

				foreach ($values['companies'] as $company) {

					$data = array(
						'item_id' => $values['item_id'],
						'company_id' => $company['company_id'],
						'code' => $company['code'],
						'cost_item' => $company['cost_item']
					);
					
					$this->db->insert('company_has_item', $data);

					if (array_key_exists('discounts', $company) && count($company['discounts'],0)) {
						foreach ($company['discounts'] as &$d) {
							$d['company_has_item_company_id'] = $company['company_id'];
							$d['company_has_item_item_id'] = $values['item_id'];
						}

						$this->db->insert_batch('discount', $company['discounts']);
					}
					$r = array(
						'company_id' => $company['company_id'],
						'discounts' => $this->db->insert_id() . "  size: " . count($company['discounts'],0)
					);

					array_push($response, $r);
				}

				return $response;
			}
			return "NOT company_has_item";
		}

		// STOCK - ENLAZAR SHOP CON ITEM
		private function stock($values){

			if (array_key_exists('stocks', $values) && count($values['stocks'],0)) {
				foreach ($values['stocks'] as &$d) {
					$d['item_id'] = $values['item_id'];
				}

				return $this->db->insert_batch('stock', $values['stocks']);
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

				return $this->db->insert_batch('item_has_picture', $item_has_picture);
			}
		}

		// ITEM_HAS_SUBTITLE
		private function item_has_subtitle($values){

			if (array_key_exists('subtitles', $values) && count($values['subtitles'], 0)) {
				$subtitles = array();
				foreach ($values['subtitles'] as $d) {
					$s = array();
					$s['subtitle_id'] = $d;
					$s['item_id'] = $values['item_id'];
					array_push($subtitles, $s);
				}

				return $this->db->insert_batch('item_has_subtitle', $subtitles);
			}

		}

		private function get_values($parms){
			$values = [];
			// ítem
			$values['item_id'] = 0;
        	$values['name_item']   = (array_key_exists('name_item',	$parms) ? $parms['name_item']   : 0);
        	$values['description_item']   = (array_key_exists('description_item',	$parms) ? $parms['description_item']   : 0);
        	
        	// brand_has_item
        	$values['brands']   = (array_key_exists('brands',	$parms) ? $parms['brands']   : 0);
        	
        	// item_has_subtitle
        	$values['subtitles']   = (array_key_exists('subtitles',	$parms) ? $parms['subtitles']   : 0);
        	
        	// company_has_item
        	$values['companies']   = (array_key_exists('companies',	$parms) ? $parms['companies']   : 0);
        	
        	// stock
        	$values['stocks'] = (array_key_exists('stocks',	$parms) ? $parms['stocks']   : 0);

        	// item_has_picture
     	 	$values['pictures'] = (array_key_exists('pictures',	$parms) ? $parms['pictures']   : 0);

        	return $values;
		}
}