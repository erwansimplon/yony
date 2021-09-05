<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrdersDetailsModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_details;
		public $id_order;
		public $id_product;
		public $product_reference;
		public $product_name;
		public $product_ean;
		public $product_quantity;
		public $quantity_delivered;
		public $quantity_refunded;
		public $product_price_excl;
		public $product_price_incl;
		public $product_ecotax_excl;
		public $product_ecotax_incl;
		public $unit_price_excl;
		public $unit_price_incl;
		public $product_weight;
		public $date_add;
		public $date_upd;
		
		protected $table = 'orders_details';
		protected $id = 'id_order_details';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrdersDetails', $id);
			}
		}
		
		public function getOrdersDetailsById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getOrdersDetailsByIdOrder($id){
			
			$table = array('products' => 'p',
						   'taxes'    => 't',
						   'products_image' => 'i');
			
			$liaison = array('od.id_product' => 'p.id_product',
							 'p.id_tax'      => 't.id_tax',
							 'p.id_product'  => 'i.product_id
							  AND cover = 1');
			
			$select = $this->select->select('od.*, p.quantity as stock, i.image, i.ext as image_ext, t.valeur as tva,
											 (od.product_price_excl + (od.product_ecotax_excl * od.product_quantity)) as total_product_ht')
									->from($this->table, 'od')
									->join('left', $table, $liaison)
									->where('od.id_order = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function getTotalEcotax($id){
			$select = $this->select->select('SUM(product_ecotax_excl) as total_ecotax')
									->from($this->table)
									->where('id_order = "'.$id.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->total_ecotax;
		}
		
		public function getTotalProductDelivered($id){
			$select = $this->select->select('SUM(quantity_delivered) as total_quantity_delivered')
									->from($this->table)
									->where('id_order = "'.$id.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->total_quantity_delivered;
		}
		
		public function getTotalProductRefunded($id){
			$select = $this->select->select('SUM(quantity_refunded) as total_quantity_refunded')
									->from($this->table)
									->where('id_order = "'.$id.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->total_quantity_refunded;
		}
		
		public function loadDataInArray(){
			$tabs = array('id_order'            => $this->id_order,
						  'id_product'          => $this->id_product,
						  'product_reference'   => $this->product_reference,
						  'product_name'        => $this->product_name,
						  'product_ean'         => $this->product_ean,
						  'product_quantity'    => $this->product_quantity,
						  'quantity_delivered'  => $this->quantity_delivered,
						  'quantity_refunded'   => $this->quantity_refunded,
						  'product_price_excl'  => $this->product_price_excl,
						  'product_price_incl'  => $this->product_price_incl,
						  'product_ecotax_excl' => $this->product_ecotax_excl,
						  'product_ecotax_incl' => $this->product_ecotax_incl,
						  'unit_price_excl'     => $this->unit_price_excl,
						  'unit_price_incl'     => $this->unit_price_incl,
						  'product_weight'      => $this->product_weight,
						  'date_add'            => $this->date_add,
						  'date_upd'            => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_details
		 */
		public function setIdOrderDetails($id_order_details)
		{
			$this->id_order_details = $id_order_details;
		}
		
		/**
		 * @param mixed $id_order
		 */
		public function setIdOrder($id_order)
		{
			$this->id_order = $id_order;
		}
		
		/**
		 * @param mixed $id_product
		 */
		public function setIdProduct($id_product)
		{
			$this->id_product = $id_product;
		}
		
		/**
		 * @param mixed $product_reference
		 */
		public function setProductReference($product_reference)
		{
			$this->product_reference = $product_reference;
		}
		
		/**
		 * @param mixed $product_name
		 */
		public function setProductName($product_name)
		{
			$this->product_name = $product_name;
		}
		
		/**
		 * @param mixed $product_ean
		 */
		public function setProductEan($product_ean)
		{
			$this->product_ean = $product_ean;
		}
		
		/**
		 * @param mixed $product_quantity
		 */
		public function setProductQuantity($product_quantity)
		{
			$this->product_quantity = $product_quantity;
		}
		
		/**
		 * @param mixed $quantity_delivered
		 */
		public function setQuantityDelivered($quantity_delivered)
		{
			$this->quantity_delivered = $quantity_delivered;
		}
		
		/**
		 * @param mixed $quantity_refunded
		 */
		public function setQuantityRefunded($quantity_refunded)
		{
			$this->quantity_refunded = $quantity_refunded;
		}
		
		/**
		 * @param mixed $product_price_excl
		 */
		public function setProductPriceExcl($product_price_excl)
		{
			$this->product_price_excl = $product_price_excl;
		}
		
		/**
		 * @param mixed $product_price_incl
		 */
		public function setProductPriceIncl($product_price_incl)
		{
			$this->product_price_incl = $product_price_incl;
		}
		
		/**
		 * @param mixed $product_ecotax_excl
		 */
		public function setProductEcotaxExcl($product_ecotax_excl)
		{
			$this->product_ecotax_excl = $product_ecotax_excl;
		}
		
		/**
		 * @param mixed $product_ecotax_incl
		 */
		public function setProductEcotaxIncl($product_ecotax_incl)
		{
			$this->product_ecotax_incl = $product_ecotax_incl;
		}
		
		/**
		 * @param mixed $unit_price_excl
		 */
		public function setUnitPriceExcl($unit_price_excl)
		{
			$this->unit_price_excl = $unit_price_excl;
		}
		
		/**
		 * @param mixed $unit_price_incl
		 */
		public function setUnitPriceIncl($unit_price_incl)
		{
			$this->unit_price_incl = $unit_price_incl;
		}
		
		/**
		 * @param mixed $product_weight
		 */
		public function setProductWeight($product_weight)
		{
			$this->product_weight = $product_weight;
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
									->where($this->id.' = "'.$this->id_order_details.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_details.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}