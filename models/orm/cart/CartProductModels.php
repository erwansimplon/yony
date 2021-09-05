<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CartProductModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_cart_product;
		public $id_cart;
		public $id_product;
		public $quantity;
		public $price;
		public $date_add;
		public $date_upd;
		
		protected $table = 'cart_product';
		protected $id = 'id_cart_product';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'CartProduct', $id);
			}
		}
		
		public function getCartProductById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getCartProductByIdCart($id_cart){
			
			$table = array(
				'products' => 'p',
				'products_image' => 'i'
			);
			
			$liaison = array(
				'cp.id_product' => 'p.id_product',
				'p.id_product'  => 'i.product_id
				AND i.cover = 1'
			);
			
			$select = $this->select->select('cp.*, p.quantity as stock, i.image, i.ext as image_ext,
											 p.name as product_name, p.reference as product_reference,
											 cp.quantity as product_quantity,
											 cp.price as unit_price_excl,
											 (cp.price * cp.quantity) as total_product_ht')
									->from($this->table, 'cp')
									->join('left', $table, $liaison)
									->where('cp.id_cart = "'.$id_cart.'"');
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function searchCartProduct($id_cart){
			
			$table = array('products' => 'p',
						   'products_image' => 'i');
			
			$liaison = array('cart.id_product' => 'p.id_product',
							 'p.id_product' => 'i.product_id
							  AND cover = 1');
			
			$select = $this->select->select('cart.id_cart, p.id_product, p.reference, p.name,
											 cart.price as pu_vente, p.conditionnement,
											 cart.quantity, (p.ecotax * cart.quantity) as total_ecotax,
											 ((cart.price + p.ecotax) * cart.quantity) as prix_vente,
											 i.image, i.ext as image_ext')
									->from('cart_product', 'cart')
									->join('left', $table, $liaison)
									->where('cart.id_cart = "'.(int)$id_cart.'"');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function getProductInCart($id_cart){
			
			$table = array('products' => 'p',
						   'taxes' => 't');
			
			$liaison = array('cart.id_product' => 'p.id_product',
							 'p.id_tax' => 't.id_tax');
			
			$price_ht = '(cart.price * cart.quantity)';
			$deee     = 'p.ecotax';
			$u_price  = 'cart.price';
			$tax      = '(t.valeur /100)';
			
			$select = $this->select->select('p.id_product, p.reference, cart.quantity,
											 p.name, p.ean13, cart.quantity, p.weight,
											 '.$price_ht.' as price_excl,
											 ROUND(('.$price_ht.' + ('.$price_ht.' * '.$tax.')), 2) as price_incl,
											 '.$deee.' as ecotax_excl,
											 ROUND(('.$deee.' + ('.$deee.' * '.$tax.')), 2) as ecotax_incl,
											 '.$u_price.' as unit_price_excl,
											 ROUND(('.$u_price.' + ('.$u_price.' * '.$tax.')), 2) as unit_price_incl')
									->from('cart_product', 'cart')
									->join('inner', $table, $liaison)
									->where('cart.id_cart = "'.(int)$id_cart.'"');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function taxProductCart($id_cart){
			
			$table = array('products' => 'p',
						   'taxes' => 't');
			
			$liaison = array('cart.id_product' => 'p.id_product',
							 'p.id_tax' => 't.id_tax');
			
			$select = $this->select->select('ROUND(SUM(((cart.price + p.ecotax) * cart.quantity) * (t.valeur /100)), 2) as total_tax')
									->from('cart_product', 'cart')
									->join('inner', $table, $liaison)
									->where('cart.id_cart = "'.$id_cart.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(!empty($result->total_tax)){
				return $result->total_tax;
			}
			
			return 0;
		}
		
		public function taxProductCartDetails($id_cart){
			
			$table = array('products' => 'p',
						   'taxes' => 't');
			
			$liaison = array('cart.id_product' => 'p.id_product',
							 'p.id_tax' => 't.id_tax');
			
			$select = $this->select->select('p.id_tax, t.name as tax_name, t.valeur as tax_value,
											 ROUND(SUM(((cart.price + p.ecotax) * cart.quantity) * (t.valeur /100)), 2) as amount')
									->from('cart_product', 'cart')
									->join('inner', $table, $liaison)
									->where('cart.id_cart = "'.$id_cart.'"')
									->groupby('p.id_tax');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function searchIdCartProduct($id_cart, $id_product){
			$select = $this->select->select('id_cart_product')
									->from($this->table)
									->where('id_cart = "'.$id_cart.'"',
											'id_product = "'.$id_product.'"');
								
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(isset($result->id_cart_product)){
				return $result->id_cart_product;
			}
			
			return false;
		}
		
		public function totalProductCart($id_cart){
			
			$table = array('products' => 'p');
			
			$liaison = array('cp.id_product' => 'p.id_product');
			
			$select = $this->select->select('ROUND(SUM(p.weight * cp.quantity), 2) as weight',
											'ROUND(SUM(cp.price * cp.quantity), 2) as total_ht',
											'ROUND(SUM(p.ecotax * cp.quantity), 2) as total_ecotax')
									->from($this->table, 'cp')
									->join('inner', $table, $liaison)
									->where('cp.id_cart = "'.$id_cart.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function productPrice($id_product, $product, $id_customer, $id_group){
			$product_discount = $this->getDiscountsByIdProduct($id_product, $product->prix_vente, $id_customer, $id_group);
			$tarif_group = $this->getProductPriceByGroup($id_group, $product->id_category, $product->id_manufacturer, $product->prix_vente);
			
			return min($product_discount, $tarif_group);
		}
		
		public function getDiscountsByIdProduct($id_product, $price, $id_customer, $id_group){
			
			$date = date('Y-m-d');
			
			$table = array('products' => 'p');
			
			$liaison = array('p.id_product' => 'd.id_product');
			
			$select = $this->select->select('MIN(ROUND(IF(d.remise > 0, (p.prix_vente * (1 - (d.remise /100))), d.prix_net_ht), 2)) as prix_net')
									->from('products_discounts', 'd')
									->join('inner', $table, $liaison)
									->where('d.id_product = "'.$id_product.'"',
											'(d.id_customer IN("0", "'.$id_customer.'")
											  OR d.id_group IN("0", "'.$id_group.'"))',
											'd.date_from <= "'.$date.'"',
											'd.date_to >= "'.$date.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(!empty($result->prix_net)) {
				return $result->prix_net;
			}
			
			return $price;
		}
		
		public function getProductPriceByGroup($id_group, $id_category, $id_manufacturer, $price){
			
			$select = $this->select->select('MIN(ROUND('.$price.' * (1 - (remise /100)), 2)) as prix_net')
									->from('remises')
									->where('id_group = "'.$id_group.'"',
											'(id_category = "'.$id_category.'"
											 OR id_manufacturer = "'.$id_manufacturer.'")');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(!empty($result->prix_net)) {
				return $result->prix_net;
			}
			
			return $price;
		}
		
		public function loadDataInArray(){
			$tabs = array('id_cart'    => $this->id_cart,
						  'id_product' => $this->id_product,
						  'quantity'   => $this->quantity,
						  'price'      => $this->price,
						  'date_add'   => $this->date_add,
						  'date_upd'   => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_cart_product
		 */
		public function setIdCartProduct($id_cart_product)
		{
			$this->id_cart_product = $id_cart_product;
		}
		
		/**
		 * @param mixed $id_cart
		 */
		public function setIdCart($id_cart)
		{
			$this->id_cart = $id_cart;
		}
		
		/**
		 * @param mixed $id_product
		 */
		public function setIdProduct($id_product)
		{
			$this->id_product = $id_product;
		}
		
		/**
		 * @param mixed $quantity
		 */
		public function setQuantity($quantity)
		{
			$this->quantity = $quantity;
		}
		
		/**
		 * @param mixed $price
		 */
		public function setPrice($price)
		{
			$this->price = $price;
		}
		
		/**
		 * @param mixed $date_add
		 */
		public function setDateAdd($date_add)
		{
			$this->date_add = $date_add;
		}
		
		/**
		 * @param mixed $date_upd
		 */
		public function setDateUpd($date_upd)
		{
			$this->date_upd = $date_upd;
		}
		
		public function add(){
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_cart_product.'"');
								
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_cart_product.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		public function deleteByCart($id_cart){
			$delete = $this->delete->delete($this->table.' WHERE id_cart = "'.$id_cart.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}